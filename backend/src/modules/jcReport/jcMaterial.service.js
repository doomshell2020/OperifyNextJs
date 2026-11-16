const { QueryTypes, Transaction } = require('sequelize');
const ExcelJS = require('exceljs');

const invalid = message => Object.assign(new Error(message), { status: 400 });

function validateFilters(query = {}) {
  const filters = {
    challan_no: '',
    vendor_id: '',
    item_id: '',
    from_date: '',
    to_date: '',
    balance: ''
  };

  for (const key of Object.keys(filters)) {
    if (query[key] !== undefined && query[key] !== null) {
      if (typeof query[key] === 'object') {
        throw invalid('Invalid search value.');
      }
      filters[key] = String(query[key]).trim();
    }
  }

  for (const key of ['vendor_id', 'item_id']) {
    if (filters[key] !== '' && !/^[1-9][0-9]*$/.test(filters[key])) {
      throw invalid('Please select a valid supplier and material.');
    }
  }

  for (const key of ['from_date', 'to_date']) {
    const value = filters[key];
    if (value !== '') {
      if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) {
        throw invalid('Please enter valid dates.');
      }
      const d = new Date(`${value}T00:00:00Z`);
      if (!Number.isFinite(d.getTime()) || d.toISOString().slice(0, 10) !== value) {
        throw invalid('Please enter valid dates.');
      }
    }
  }

  if (filters.from_date && filters.to_date && filters.from_date > filters.to_date) {
    throw invalid('From Date cannot be after To Date.');
  }

  const validBalances = ['', 'Pending', 'Partial', 'Completed', 'Excess', 'Receipt only', 'No material', 'Reference receipts'];
  if (!validBalances.includes(filters.balance)) {
    throw invalid('Please select a valid balance status.');
  }

  return filters;
}

function searchJcNumbers(input, jcs) {
  input = String(input || '').trim();
  if (input === '') return {};

  for (const id of Object.keys(jcs)) {
    if (String(jcs[id].challan_no).trim().toLowerCase() === input.toLowerCase()) {
      return { [input.toLowerCase()]: true };
    }
  }

  const parts = /^\d+(?:\s*[-,;\/]\s*\d+)+$/.test(input)
    ? input.split(/\s*[-,;\/]\s*/)
    : input.split(/\s*[,;\/]\s*/);

  const numbers = {};
  for (const part of parts) {
    const trimmed = part.trim().toLowerCase();
    if (trimmed !== '') numbers[trimmed] = true;
  }
  return numbers;
}

function escapeRegex(string) {
  return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

async function referenceGroups(db, includeUnreferenced = false) {
  const referenceWhere = includeUnreferenced
    ? 'AND (r.challan_id IS NULL OR r.challan_id = 0)'
    : "AND TRIM(COALESCE(r.reference_jc_no, '')) != ''";

  const receipts = await db.query(`SELECT r.id, r.reference_jc_no, r.manual_challan_no,
    r.challan_date, r.receive_date, r.sub_contractors_id, r.other_company_name,
    r.item_id, r.received_qty, a.item_name, u.unit_name,
    COALESCE(s.name, r.other_company_name, 'Unspecified') AS supplier
    FROM job_challan_receives r
    LEFT JOIN sub_contractors s ON s.id = r.sub_contractors_id
    LEFT JOIN st_additem a ON a.id = r.item_id
    LEFT JOIN st_measurementunits u ON u.id = a.uom
    WHERE r.is_manual = 1 ${referenceWhere}
    ORDER BY r.id ASC`, { type: QueryTypes.SELECT });

  if (!receipts.length) {
    return [];
  }

  const issues = await db.query(`SELECT j.id AS challan_id, j.challan_no, j.jc_date,
    j.sub_contractors_id, i.item_id, COALESCE(a.item_name, i.item_name) AS item_name,
    u.unit_name, i.quantity FROM job_challans j
    INNER JOIN job_challan_items i ON i.challan_id = j.id
    LEFT JOIN st_additem a ON a.id = i.item_id
    LEFT JOIN st_measurementunits u ON u.id = a.uom`, { type: QueryTypes.SELECT });

  const groups = {};
  const seenReceiptIds = {};

  for (const receipt of receipts) {
    if (seenReceiptIds[receipt.id]) continue;
    seenReceiptIds[receipt.id] = true;

    const reference = String(receipt.reference_jc_no || '').trim();
    const key = JSON.stringify([receipt.sub_contractors_id, receipt.other_company_name, reference.toUpperCase()]);

    if (!groups[key]) {
      groups[key] = {
        reference,
        supplier: receipt.supplier,
        vendor_id: receipt.sub_contractors_id,
        receipts: [],
        issued: [],
        jc_ids: {},
        jc_numbers: {},
        dates: [],
        receipt_numbers: {},
        last_date: null
      };

      for (const issue of issues) {
        const number = String(issue.challan_no || '').trim();
        if (
          !receipt.sub_contractors_id ||
          String(issue.sub_contractors_id) !== String(receipt.sub_contractors_id) ||
          number === ''
        ) {
          continue;
        }

        const pattern = new RegExp(`(?<![A-Za-z0-9])${escapeRegex(number)}(?![A-Za-z0-9])`, 'i');
        if (!pattern.test(reference)) {
          continue;
        }

        groups[key].issued.push(issue);
        groups[key].jc_ids[issue.challan_id] = true;
        groups[key].jc_numbers[number] = number;
        if (issue.jc_date) {
          groups[key].dates.push(String(issue.jc_date).slice(0, 10));
        }
      }
    }

    groups[key].receipts.push(receipt);
    if (receipt.manual_challan_no) {
      groups[key].receipt_numbers[receipt.manual_challan_no] = receipt.manual_challan_no;
    }

    let date = receipt.receive_date ? String(receipt.receive_date).slice(0, 10) : '';
    if (!date || date === '0000-00-00') {
      date = receipt.challan_date ? String(receipt.challan_date).slice(0, 10) : '';
    }
    if (date && date === '0000-00-00') {
      date = null;
    }
    if (date && (!groups[key].last_date || date > groups[key].last_date)) {
      groups[key].last_date = date;
    }
  }

  return Object.values(groups);
}

function allocateManualJcs(jcs, groups) {
  const allocationAudit = { records: [], units: {} };
  const events = [];
  const seen = {};

  for (const group of groups) {
    const jcIds = Object.keys(group.jc_ids);
    const jcNumbers = Object.values(group.jc_numbers);
    const single = jcIds.length === 1 && String(group.reference).trim().toLowerCase() === String(jcNumbers[0] || '').trim().toLowerCase();

    for (const receipt of group.receipts) {
      if (seen[receipt.id]) continue;
      seen[receipt.id] = true;

      let date = receipt.receive_date ? String(receipt.receive_date).slice(0, 10) : '';
      if (!date || date === '0000-00-00') {
        date = receipt.challan_date ? String(receipt.challan_date).slice(0, 10) : '';
      }
      events.push({
        single,
        ids: jcIds,
        reference: group.reference,
        receipt,
        date: date || ''
      });
    }
  }

  events.sort((a, b) => {
    return (Number(b.single) - Number(a.single)) ||
      String(a.date).localeCompare(String(b.date)) ||
      (Number(a.receipt.id) - Number(b.receipt.id));
  });

  for (const event of events) {
    const receipt = event.receipt;
    let remaining = Math.round(Number(receipt.received_qty) * 1e6) / 1e6;
    const ids = [...event.ids];

    ids.sort((a, b) => {
      const dateA = String(jcs[a]?.jc_date || '');
      const dateB = String(jcs[b]?.jc_date || '');
      return dateA.localeCompare(dateB) || (Number(a) - Number(b));
    });

    for (const id of ids) {
      if (!jcs[id] || Object.keys(jcs[id].units).length !== 1 || !receipt.unit_name) continue;
      const unit = Object.values(jcs[id].units)[0];
      if (String(unit).trim().toLowerCase() !== String(receipt.unit_name).trim().toLowerCase() || unit === 'Unspecified') {
        continue;
      }

      const capacity = Math.max(0, Math.round((jcs[id].issued_qty - jcs[id].received_qty) * 1e6) / 1e6);
      const assigned = event.single ? remaining : Math.min(capacity, Math.max(0, remaining));
      if (Math.abs(assigned) < 0.000001) continue;

      jcs[id].received_qty = Math.round((jcs[id].received_qty + assigned) * 1e6) / 1e6;
      remaining = Math.round((remaining - assigned) * 1e6) / 1e6;

      jcs[id].reference_note.push(
        `Receive ID ${receipt.id}, challan ${receipt.manual_challan_no}, reference ${event.reference}: ${assigned} ${unit}` +
        (event.single ? ' (single JC).' : ' (approved FIFO).')
      );

      if (event.date && (!jcs[id].last_received_date || event.date > jcs[id].last_received_date)) {
        jcs[id].last_received_date = event.date;
      }

      if (Math.abs(remaining) < 0.000001) break;
    }

    if (Math.abs(remaining) > 0.000001) {
      const unit = receipt.unit_name || 'Unspecified';
      if (!allocationAudit.units[unit]) allocationAudit.units[unit] = 0;
      allocationAudit.units[unit] += remaining;
      allocationAudit.records.push({
        receive_id: receipt.id,
        receipt_no: receipt.manual_challan_no,
        reference: event.reference,
        quantity: remaining,
        unit
      });
    }
  }

  return allocationAudit;
}

async function jcRows(db, filters) {
  const sourceSql = `SELECT j.id AS challan_id, j.challan_no, j.jc_date,
    j.status AS jc_status, j.processing_type, s.name AS supplier, j.sub_contractors_id AS vendor_id,
    q.item_id, COALESCE(a.item_name, q.item_name, 'Unknown material') AS item_name,
    COALESCE(q.issue_count, 0) AS issue_count,
    COALESCE(u.unit_name, 'Unspecified') AS unit_name,
    COALESCE(q.issued_qty, 0) AS issued_qty,
    COALESCE(q.received_qty, 0) AS received_qty,
    GREATEST(ROUND(COALESCE(q.issued_qty, 0) - COALESCE(q.received_qty, 0), 6), 0) AS pending_qty,
    GREATEST(ROUND(COALESCE(q.received_qty, 0) - COALESCE(q.issued_qty, 0), 6), 0) AS excess_qty,
    q.last_received_date, COALESCE(q.receipt_count, 0) AS receipt_count,
    CASE WHEN q.challan_id IS NULL THEN 'No material'
         WHEN q.issue_count = 0 THEN 'Receipt only'
         WHEN ROUND(q.received_qty - q.issued_qty, 6) > 0 THEN 'Excess'
         WHEN ROUND(q.issued_qty - q.received_qty, 6) <= 0 THEN 'Completed'
         WHEN q.received_qty > 0 THEN 'Partial'
         ELSE 'Pending' END AS balance_status
    FROM job_challans j
    LEFT JOIN sub_contractors s ON s.id = j.sub_contractors_id
    LEFT JOIN (
        SELECT t.challan_id, t.item_id, MAX(t.item_name) AS item_name,
            SUM(t.issued_qty) AS issued_qty, SUM(t.received_qty) AS received_qty,
            SUM(t.issue_count) AS issue_count, SUM(t.receipt_count) AS receipt_count,
            MAX(t.last_received_date) AS last_received_date
        FROM (
            SELECT challan_id, item_id, item_name, quantity AS issued_qty,
                0 AS received_qty, 1 AS issue_count, 0 AS receipt_count,
                NULL AS last_received_date FROM job_challan_items
            UNION ALL
            SELECT challan_id, item_id, NULL AS item_name, 0 AS issued_qty,
                received_qty, 0 AS issue_count, 1 AS receipt_count,
                receive_date AS last_received_date FROM (
                    SELECT DISTINCT id, challan_id, item_id, received_qty, receive_date, is_manual
                    FROM job_challan_receives
                ) receipt_source
            WHERE challan_id IS NOT NULL
        ) t GROUP BY t.challan_id, t.item_id
    ) q ON q.challan_id = j.id
    LEFT JOIN st_additem a ON a.id = q.item_id
    LEFT JOIN st_measurementunits u ON u.id = a.uom
    WHERE 1 = 1`;

  const items = await db.query(sourceSql, { type: QueryTypes.SELECT });

  const jcs = {};
  for (const item of items) {
    if (item.challan_id === null || item.challan_id === undefined) continue;
    const id = item.challan_id;
    if (!jcs[id]) {
      jcs[id] = {
        challan_id: id,
        challan_no: item.challan_no,
        jc_date: item.jc_date ? String(item.jc_date).slice(0, 10) : '',
        vendor_id: item.vendor_id,
        supplier: item.supplier,
        issued_qty: 0.0,
        received_qty: 0.0,
        pending_qty: null,
        units: {},
        item_ids: [],
        item_names: {},
        unresolved: false,
        reference_note: [],
        received_percent: null,
        pending_percent: null,
        last_received_date: null
      };
    }

    const jc = jcs[id];
    jc.issued_qty += Number(item.issued_qty || 0);
    jc.received_qty += Number(item.received_qty || 0);

    if (Number(item.issued_qty) !== 0 || Number(item.received_qty) !== 0) {
      const unitKey = String(item.unit_name || '').trim().toLowerCase();
      jc.units[unitKey] = item.unit_name;
    }

    jc.item_ids.push(String(item.item_id));
    if (Number(item.issue_count) > 0 && item.item_name) {
      jc.item_names[item.item_name] = item.item_name;
    }

    const recDate = item.last_received_date ? String(item.last_received_date).slice(0, 10) : null;
    if (recDate && (!jc.last_received_date || recDate > jc.last_received_date)) {
      jc.last_received_date = recDate;
    }
  }

  const groups = await referenceGroups(db, true);
  const allocationAudit = allocateManualJcs(jcs, groups);

  const companyQuantities = {};
  for (const jc of Object.values(jcs)) {
    const unitValues = Object.values(jc.units);
    if (unitValues.length !== 1 || jc.units['unspecified']) continue;
    const unit = String(unitValues[0]).trim().toLowerCase();
    if (!companyQuantities[unit]) companyQuantities[unit] = { created: 0, received: 0 };
    companyQuantities[unit].created += jc.issued_qty;
    companyQuantities[unit].received += jc.received_qty;
  }

  const requestedNumbers = searchJcNumbers(filters.challan_no, jcs);
  const rows = [];

  for (const jc of Object.values(jcs)) {
    if (
      (filters.challan_no !== '' && !requestedNumbers[String(jc.challan_no).trim().toLowerCase()]) ||
      (filters.vendor_id !== '' && String(jc.vendor_id) !== filters.vendor_id) ||
      (filters.item_id !== '' && !jc.item_ids.includes(filters.item_id)) ||
      (filters.from_date !== '' && jc.jc_date < filters.from_date) ||
      (filters.to_date !== '' && jc.jc_date > filters.to_date)
    ) {
      continue;
    }

    const unitValues = Object.values(jc.units);
    jc.unit_name = unitValues.length ? unitValues[0] : 'Unspecified';

    if (unitValues.length > 1 || jc.units['unspecified']) {
      jc.issued_qty = null;
      jc.received_qty = null;
      jc.unresolved = true;
      jc.unit_name = 'Mixed / unknown';
      jc.reference_note.push('Different or unknown units cannot be added into a single quantity.');
    }

    if (!jc.unresolved) {
      jc.issued_qty = Math.round(jc.issued_qty * 1e6) / 1e6;
      jc.received_qty = Math.round(jc.received_qty * 1e6) / 1e6;
      jc.pending_qty = Math.round((jc.issued_qty - jc.received_qty) * 1e6) / 1e6;
      jc.received_percent = jc.issued_qty > 0 ? Math.round((jc.received_qty / jc.issued_qty * 100) * 1e4) / 1e4 : (jc.received_qty === 0 ? 0.0 : null);
      jc.pending_percent = jc.issued_qty > 0 ? Math.round((jc.pending_qty / jc.issued_qty * 100) * 1e4) / 1e4 : (jc.received_qty === 0 ? 0.0 : null);
    }

    jc.balance_status = jc.unresolved
      ? 'Reference receipts'
      : (jc.pending_qty < 0 ? 'Excess' : (jc.pending_qty === 0 ? 'Completed' : (jc.received_qty > 0 ? 'Partial' : 'Pending')));

    if (filters.balance !== '' && jc.balance_status !== filters.balance) continue;

    jc.reference_note = jc.reference_note.join(' ');
    const sortedNames = Object.values(jc.item_names).sort((a, b) => a.localeCompare(b, undefined, { numeric: true, sensitivity: 'base' }));
    jc.jc_item_name = sortedNames.length ? sortedNames.join(', ') : '-';
    rows.push(jc);
  }

  rows.sort((a, b) => {
    return String(b.jc_date).localeCompare(String(a.jc_date)) || (Number(b.challan_id) - Number(a.challan_id));
  });

  return { rows, allocationAudit, companyQuantities };
}

function formatDisplayQuantity(value) {
  if (value === null || value === undefined) return 'Needs review';
  const num = Number(value);
  const formatted = num.toLocaleString('en-US', { minimumFractionDigits: 3, maximumFractionDigits: 3 });
  const sign = formatted.startsWith('-') ? '-' : '';
  const clean = formatted.replace(/^-/, '');
  return sign + clean.padStart(7, '0');
}

async function jcResults(db, inputFilters, page = 1, limit = 50) {
  const filters = validateFilters(inputFilters);
  const { rows: matching, allocationAudit } = await jcRows(db, filters);
  const count = matching.length;

  const summary = {
    jc_count: count,
    pending_count: 0,
    review_count: 0,
    units: {}
  };

  for (const row of matching) {
    if (row.pending_qty > 0) summary.pending_count++;
    if (row.unresolved) summary.review_count++;
    if (row.issued_qty === null) continue;

    const unit = String(row.unit_name).toLowerCase();
    if (!summary.units[unit]) {
      summary.units[unit] = {
        unit_name: row.unit_name,
        created: 0,
        received: 0,
        pending: 0,
        complete: true
      };
    }
    summary.units[unit].created += row.issued_qty;
    summary.units[unit].received += row.received_qty;
    if (row.pending_qty === null) {
      summary.units[unit].complete = false;
    } else {
      summary.units[unit].pending += row.pending_qty;
    }
  }

  const pages = Math.max(1, Math.ceil(count / limit));
  const currentPage = Math.max(1, Math.min(Number(page) || 1, pages));
  const pagedRows = matching.slice((currentPage - 1) * limit, currentPage * limit);

  return {
    data: pagedRows.map(row => ({
      ...row,
      formatted_issued_qty: formatDisplayQuantity(row.issued_qty),
      formatted_received_qty: formatDisplayQuantity(row.received_qty),
      formatted_pending_qty: formatDisplayQuantity(row.pending_qty)
    })),
    summary,
    pagination: {
      page: currentPage,
      limit,
      total: count,
      pages
    },
    allocationAudit
  };
}

async function getOptions(db) {
  const [suppliers, items] = await Promise.all([
    db.query('SELECT id, name FROM sub_contractors ORDER BY name', { type: QueryTypes.SELECT }),
    db.query(`SELECT a.id, a.item_name FROM st_additem a
      WHERE a.itemtype = 'RawMaterial' AND (
          EXISTS (SELECT 1 FROM job_challan_items i
              INNER JOIN job_challans j ON j.id = i.challan_id WHERE i.item_id = a.id)
          OR EXISTS (SELECT 1 FROM job_challan_receives r
              INNER JOIN job_challans j ON j.id = r.challan_id
              WHERE r.item_id = a.id AND COALESCE(r.is_manual, 0) = 0)
      ) ORDER BY a.item_name`, { type: QueryTypes.SELECT })
  ]);
  return { suppliers, items };
}

async function exportExcel(db, inputFilters, res) {
  const filters = validateFilters(inputFilters);
  const { rows, allocationAudit } = await jcRows(db, filters);

  const workbook = new ExcelJS.Workbook();
  workbook.title = 'JC Material Report';
  workbook.subject = 'Issued materials and received returns';

  const sheet = workbook.addWorksheet('JC Report', {
    views: [{ state: 'frozen', xSplit: 2, ySplit: 4 }]
  });

  const headers = ['JC Number', 'Created Qty', 'Received Qty', 'Pending Qty', 'JC Item Name', 'Unit', 'Supplier / Company', 'JC ID'];
  const lastCol = 'H';

  sheet.mergeCells(`A1:${lastCol}1`);
  sheet.getCell('A1').value = 'JC Material Report | JC Report';
  sheet.getRow(1).height = 28;
  sheet.getCell('A1').font = { bold: true, size: 16, color: { argb: 'FFFFFFFF' } };
  sheet.getCell('A1').fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF17365D' } };
  sheet.getCell('A1').alignment = { vertical: 'middle' };

  sheet.mergeCells(`A2:${lastCol}2`);
  const now = new Date();
  const dateStr = now.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) + ' ' +
    now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
  sheet.getCell('A2').value = `Generated: ${dateStr} | Matching records: ${rows.length}`;
  sheet.getRow(2).height = 22;
  sheet.getCell('A2').alignment = { vertical: 'middle' };

  const unallocatedNotes = [];
  if (allocationAudit && allocationAudit.units) {
    for (const [unit, qty] of Object.entries(allocationAudit.units)) {
      unallocatedNotes.push(`${qty.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ${unit}`);
    }
  }
  let description = 'One row per JC. Pending = created minus received. Shared references use approved FIFO.';
  if (unallocatedNotes.length) {
    description += ` Company unallocated receipts (excluded from JC totals): ${unallocatedNotes.join('; ')}.`;
  }

  sheet.mergeCells(`A3:${lastCol}3`);
  sheet.getCell('A3').value = rows.length ? description : 'No matching records for the selected filters.';
  sheet.getRow(3).height = 36;
  sheet.getCell('A3').alignment = { wrapText: true, vertical: 'middle' };

  const headerRow = sheet.getRow(4);
  headerRow.values = headers;
  headerRow.height = 25;
  headerRow.font = { bold: true, color: { argb: 'FFFFFFFF' } };
  headerRow.alignment = { vertical: 'middle' };
  headerRow.eachCell(cell => {
    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF305B84' } };
  });

  for (let i = 0; i < rows.length; i++) {
    const row = rows[i];
    const line = i + 5;
    const r = sheet.getRow(line);
    r.values = [
      row.challan_no,
      row.issued_qty === null ? 'Mixed units' : Number(row.issued_qty),
      row.received_qty === null ? 'Needs review' : Number(row.received_qty),
      row.pending_qty === null ? 'Needs review' : Number(row.pending_qty),
      row.jc_item_name,
      row.unit_name,
      row.supplier,
      Number(row.challan_id)
    ];

    r.alignment = { vertical: 'top', wrapText: true };

    if (i % 2 === 0) {
      r.eachCell(cell => {
        cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFEDF3F8' } };
      });
    }

    // Number format for quantities
    for (const colLetter of ['B', 'C', 'D']) {
      const cell = sheet.getCell(`${colLetter}${line}`);
      if (typeof cell.value === 'number') {
        cell.numFmt = '#,##0.000';
      }
    }

    // Highlight pending qty
    const pendingCell = sheet.getCell(`D${line}`);
    pendingCell.font = { bold: true };
    pendingCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFFF2CC' } };
  }

  sheet.columns = [
    { width: 16 }, // JC Number
    { width: 16 }, // Created Qty
    { width: 16 }, // Received Qty
    { width: 16 }, // Pending Qty
    { width: 34 }, // JC Item Name
    { width: 14 }, // Unit
    { width: 28 }, // Supplier / Company
    { width: 12 }  // JC ID
  ];

  res.setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  res.setHeader('Content-Disposition', 'attachment; filename=jc-report.xlsx');
  await workbook.xlsx.write(res);
}

module.exports = {
  validateFilters,
  searchJcNumbers,
  referenceGroups,
  allocateManualJcs,
  jcRows,
  jcResults,
  getOptions,
  exportExcel,
  formatDisplayQuantity
};
