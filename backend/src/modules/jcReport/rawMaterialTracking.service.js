const { QueryTypes, Transaction } = require('sequelize');
const ExcelJS = require('exceljs');

const TITLE = 'Category-wise Raw Material Tracking Report';
const QUANTITIES = {
  sent: 'Total Raw Material Sent Qty',
  received: 'Total Processed Material Received Qty',
  loss: 'Total Wastage / Conversion Loss Qty',
  pending: 'Pending Raw Material Qty'
};
const NOTE = 'Date filters use the JC date. Quantities include all receipts recorded against the selected JCs. The JC source has no recorded loss or conversion factor, so the loss column shows zero recorded loss. Unallocated receipts are excluded from the balances.';

const SUMMARY_COLUMNS = {
  category: 'Raw Material Category',
  company: 'Subcontractor / Company Name',
  unit: 'Unit',
  sent: QUANTITIES.sent,
  received: QUANTITIES.received,
  loss: QUANTITIES.loss,
  pending: QUANTITIES.pending,
  count: 'Total Number of JCs',
  numbers: 'JC Numbers (Reference)'
};

const invalid = message => Object.assign(new Error(message), { status: 400 });

function validateFilters(query = {}) {
  const filters = {
    challan_no: '',
    vendor_id: '',
    category_id: '',
    from_date: '',
    to_date: ''
  };

  for (const key of ['from_date', 'to_date']) {
    let value = query[key];
    if (value !== undefined && value !== null) {
      if (typeof value === 'object') throw invalid('Invalid search value.');
      value = String(value).trim();
      const dmYMatch = /^(\d{2})-(\d{2})-(\d{4})$/.exec(value);
      if (dmYMatch) {
        value = `${dmYMatch[3]}-${dmYMatch[2]}-${dmYMatch[1]}`;
      }
      if (value !== '') {
        if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) {
          throw invalid('Please enter valid dates.');
        }
        const d = new Date(`${value}T00:00:00Z`);
        if (!Number.isFinite(d.getTime()) || d.toISOString().slice(0, 10) !== value) {
          throw invalid('Please enter valid dates.');
        }
        filters[key] = value;
      }
    }
  }

  if (filters.from_date && filters.to_date && filters.from_date > filters.to_date) {
    throw invalid('From Date cannot be after To Date.');
  }

  if (query.challan_no !== undefined && query.challan_no !== null) {
    if (typeof query.challan_no === 'object') throw invalid('Invalid search value.');
    filters.challan_no = String(query.challan_no).trim();
  }

  if (query.vendor_id !== undefined && query.vendor_id !== null) {
    const v = String(query.vendor_id).trim();
    if (v !== '' && !/^[1-9][0-9]*$/.test(v)) {
      throw invalid('Please select a valid supplier.');
    }
    filters.vendor_id = v;
  }

  if (query.category_id !== undefined && query.category_id !== null) {
    const c = String(query.category_id).trim();
    if (c !== '' && !/^[1-9][0-9]*$/.test(c)) {
      throw invalid('Please select a valid raw material category.');
    }
    filters.category_id = c;
  }

  return filters;
}

function referenceNumbers(reference, knownNumbers) {
  reference = String(reference || '').trim().toLowerCase();
  if (knownNumbers[reference]) {
    return [reference];
  }

  const separator = /^\d+(?:\s*[-,;\/]\s*\d+)+$/.test(reference)
    ? /\s*[-,;\/]\s*/
    : /\s*[,;\/]\s*/;

  const parts = reference.split(separator);
  const result = [];
  const seen = {};
  for (const part of parts) {
    const trimmed = part.trim();
    if (trimmed && !seen[trimmed]) {
      seen[trimmed] = true;
      result.push(trimmed);
    }
  }
  return result;
}

function eligibleIssues(candidates, receipt) {
  // Sort candidate indices numerically (oldest JC first, matching PHP ksort)
  const indices = Object.keys(candidates).map(Number).sort((a, b) => a - b);
  
  const exact = {};
  for (const idx of indices) {
    if (String(candidates[idx].item_id) === String(receipt.item_id)) {
      exact[idx] = candidates[idx];
    }
  }
  if (Object.keys(exact).length > 0) {
    return exact;
  }

  if (!receipt.unit_id) {
    return {};
  }

  const compatible = {};
  for (const idx of indices) {
    const issue = candidates[idx];
    if (String(issue.unit_id) === String(receipt.unit_id) && issue.unit !== 'Unspecified') {
      compatible[idx] = issue;
    }
  }

  const sameCategory = {};
  for (const idx of Object.keys(compatible)) {
    const issue = compatible[idx];
    if (issue.category_id && String(issue.category_id) === String(receipt.category_id)) {
      sameCategory[idx] = issue;
    }
  }
  if (Object.keys(sameCategory).length > 0) {
    return sameCategory;
  }

  const categories = [];
  for (const idx of indices) {
    const issue = candidates[idx];
    categories.push(issue.category_id ? 'category:' + issue.category_id : 'item:' + issue.item_id);
  }
  const uniqueCategories = new Set(categories);

  if (uniqueCategories.size === 1 && Object.keys(compatible).length === indices.length) {
    return compatible;
  }

  return {};
}

function reconcile(issues, receipts, filters) {
  const byJc = {};
  const byNumber = {};
  const options = { item_id: {}, category_id: {}, vendor_id: {} };
  const groups = {};
  const summary = {};
  const unmatched = [];

  for (let index = 0; index < issues.length; index++) {
    const issue = issues[index];
    issue.product = String(issue.product || '').trim();
    issue.category = issue.category || 'Uncategorized';
    issue.company = issue.company || 'Unspecified';
    issue.unit = issue.unit || 'Unspecified';
    issue.sent = Number(issue.sent || 0);
    issue.received = 0.0;
    issue.loss = 0.0;
    issue.receipts = [];

    if (!byJc[issue.jc_id]) byJc[issue.jc_id] = [];
    byJc[issue.jc_id].push(index);

    const number = String(issue.challan_no || '').trim().toLowerCase();
    if (issue.vendor_id && number !== '') {
      if (!byNumber[issue.vendor_id]) byNumber[issue.vendor_id] = {};
      if (!byNumber[issue.vendor_id][number]) byNumber[issue.vendor_id][number] = {};
      byNumber[issue.vendor_id][number][issue.jc_id] = true;
    }

    if (issue.item_id) options.item_id[issue.item_id] = issue.product;
    if (issue.category_id) options.category_id[issue.category_id] = issue.category;
    if (issue.vendor_id) options.vendor_id[issue.vendor_id] = issue.company;
  }

  const events = [[], [], []];
  for (const receipt of receipts) {
    const reference = String(receipt.reference_jc_no || '').trim();
    const numbers = byNumber[receipt.sub_contractors_id] || {};
    let ids = [];

    if (receipt.linked_id) {
      ids.push(receipt.linked_id);
    } else if (!receipt.challan_id && reference !== '') {
      const parts = referenceNumbers(reference, numbers);
      for (const number of parts) {
        const jcMap = numbers[number] || {};
        const count = Object.keys(jcMap).length;
        if (count !== 1) {
          ids = [];
          break;
        }
        ids.push(Number(Object.keys(jcMap)[0]));
      }
    }

    const priority = receipt.linked_id ? 0 : (ids.length === 1 ? 1 : 2);
    events[priority].push([receipt, ids]);
  }

  const allEvents = [...events[0], ...events[1], ...events[2]];

  for (const [receipt, ids] of allEvents) {
    const candidates = {};
    for (const id of ids) {
      for (const index of byJc[id] || []) {
        candidates[index] = issues[index];
      }
    }

    const eligible = eligibleIssues(candidates, receipt);
    const eligibleIndices = Object.keys(eligible).map(Number);
    let remaining = Number(receipt.received_qty || 0);

    for (const index of eligibleIndices) {
      const issue = issues[index];
      const capacity = Math.max(0, Math.round((issue.sent - issue.received - issue.loss) * 1e6) / 1e6);
      const assigned = eligibleIndices.length === 1 ? remaining : Math.min(capacity, Math.max(0, remaining));
      issues[index].received = Math.round((issue.received + assigned) * 1e6) / 1e6;

      if (assigned !== 0) {
        let date = receipt.receive_date ? String(receipt.receive_date).slice(0, 10) : '';
        if (!date || date === '0000-00-00') {
          date = receipt.challan_date ? String(receipt.challan_date).slice(0, 10) : '';
        }

        issues[index].receipts.push({
          id: String(receipt.id),
          date: date || '',
          receipt_no: receipt.manual_challan_no || receipt.receive_no || '',
          reference: receipt.reference_jc_no || null,
          product: receipt.item_name || 'Unknown item',
          receipt_qty: Number(receipt.received_qty),
          quantity: assigned,
          unit: receipt.unit || issue.unit,
          jc_id: String(issue.jc_id),
          jc_number: issue.challan_no
        });
      }

      remaining = Math.round((remaining - assigned) * 1e6) / 1e6;
      if (remaining === 0) break;
    }

    if (remaining !== 0) {
      unmatched.push({
        id: String(receipt.id),
        challan: String(receipt.manual_challan_no || ''),
        reference: String(receipt.reference_jc_no || receipt.challan_id || ''),
        company: String(receipt.company || receipt.other_company_name || ''),
        product: String(receipt.item_name || ''),
        quantity: remaining,
        unit: receipt.unit || 'Unspecified'
      });
    }
  }

  const search = String(filters.challan_no || '').toLowerCase();
  const knownNumbers = {};
  for (const numbers of Object.values(byNumber)) {
    Object.assign(knownNumbers, numbers);
  }
  const requested = referenceNumbers(search, knownNumbers);

  const reversedIssues = [...issues].reverse();

  for (const issue of reversedIssues) {
    const date = issue.jc_date ? String(issue.jc_date).slice(0, 10) : '';
    const jcNoLower = String(issue.challan_no || '').trim().toLowerCase();

    if (
      (search !== '' && !requested.includes(jcNoLower)) ||
      ((filters.from_date !== '' || filters.to_date !== '') && (!date || date === '0000-00-00')) ||
      (filters.from_date !== '' && date < filters.from_date) ||
      (filters.to_date !== '' && date > filters.to_date)
    ) {
      continue;
    }

    if (filters.vendor_id !== '' && String(issue.vendor_id) !== filters.vendor_id) continue;
    if (filters.category_id !== '' && String(issue.category_id) !== filters.category_id) continue;
    if (filters.item_id && String(issue.item_id) !== filters.item_id) continue;

    const key = JSON.stringify([
      issue.vendor_id,
      issue.category_id,
      issue.unit_id,
      issue.category_id ? null : issue.item_id
    ]);

    issue.pending = Math.max(0, Math.round((issue.sent - issue.received - issue.loss) * 1e6) / 1e6);
    issue.count = 1;
    issue.numbers = issue.challan_no;

    if (!groups[key]) {
      groups[key] = {
        category: issue.category,
        company: issue.company,
        unit: issue.unit,
        sent: 0.0,
        received: 0.0,
        loss: 0.0,
        pending: 0.0,
        jcs: new Map(),
        details: [],
        receipts: {}
      };
    }

    const group = groups[key];
    group.jcs.set(issue.jc_id, issue.challan_no);
    group.details.push(issue);

    for (const receipt of issue.receipts) {
      const receiptKey = `${receipt.id}:${receipt.jc_id}`;
      if (!group.receipts[receiptKey]) {
        group.receipts[receiptKey] = { ...receipt, quantity: 0.0 };
      }
      group.receipts[receiptKey].quantity = Math.round((group.receipts[receiptKey].quantity + receipt.quantity) * 1e6) / 1e6;
    }

    const unitKey = issue.unit_id || 0;
    if (!summary[unitKey]) {
      summary[unitKey] = {
        unit: issue.unit,
        sent: 0.0,
        received: 0.0,
        loss: 0.0,
        pending: 0.0
      };
    }

    for (const field of ['sent', 'received', 'loss', 'pending']) {
      group[field] += issue[field];
      summary[unitKey][field] += issue[field];
    }
  }

  const groupList = Object.values(groups);
  for (const group of groupList) {
    group.count = group.jcs.size;
    group.numbers = Array.from(group.jcs.values()).join(', ');
    group.jcs = Object.fromEntries(group.jcs);
    group.receipts = Object.values(group.receipts);
  }

  return {
    groups: groupList,
    summary,
    options,
    unmatched
  };
}

async function data(db, filters) {
  const issues = await db.query(`SELECT j.id AS jc_id, j.challan_no, j.jc_date,
    j.sub_contractors_id AS vendor_id, s.name AS company,
    a.id AS item_id, a.item_name AS product, c.id AS category_id, c.category_name AS category,
    a.uom AS unit_id, u.unit_name AS unit, SUM(i.quantity) AS sent
    FROM job_challan_items i
    INNER JOIN job_challans j ON j.id = i.challan_id
    INNER JOIN st_additem a ON a.id = i.item_id AND a.itemtype = 'RawMaterial'
    LEFT JOIN st_categorymaster c ON c.id = a.category_id
    LEFT JOIN st_measurementunits u ON u.id = a.uom
    LEFT JOIN sub_contractors s ON s.id = j.sub_contractors_id
    GROUP BY j.id, j.challan_no, j.jc_date, j.sub_contractors_id, s.name,
        a.id, a.item_name, c.id, c.category_name, a.uom, u.unit_name
    ORDER BY j.jc_date, j.id, a.id`, { type: QueryTypes.SELECT });

  const receipts = await db.query(`SELECT r.id, r.challan_id, r.reference_jc_no,
    r.manual_challan_no, r.receive_no, r.challan_date, r.sub_contractors_id, r.item_id, r.received_qty,
    r.receive_date, s.name AS company, r.other_company_name, a.item_name,
    a.category_id, a.uom AS unit_id, u.unit_name AS unit, j.id AS linked_id
    FROM job_challan_receives r
    LEFT JOIN job_challans j ON j.id = r.challan_id
    LEFT JOIN st_additem a ON a.id = r.item_id
    LEFT JOIN st_measurementunits u ON u.id = a.uom
    LEFT JOIN sub_contractors s ON s.id = r.sub_contractors_id
    WHERE r.received_qty <> 0 AND (r.challan_id > 0 OR r.is_manual = 1)
    ORDER BY COALESCE(NULLIF(r.receive_date, '0000-00-00'), r.challan_date), r.id`, { type: QueryTypes.SELECT });

  return reconcile(issues, receipts, filters);
}

function formatQty(value) {
  if (value === null || value === undefined) return '0';
  const num = Number(value);
  const formatted = num.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 6 });
  return formatted;
}

async function getTrackingReport(db, inputFilters, page = 1, limit = 50) {
  const filters = validateFilters(inputFilters);
  const result = await data(db, filters);

  const count = result.groups.length;
  const pages = Math.max(1, Math.ceil(count / limit));
  const currentPage = Math.max(1, Math.min(Number(page) || 1, pages));
  const pagedGroups = result.groups.slice((currentPage - 1) * limit, currentPage * limit);

  return {
    data: pagedGroups,
    summary: result.summary,
    options: result.options,
    unmatched: result.unmatched,
    pagination: {
      page: currentPage,
      limit,
      total: count,
      pages
    }
  };
}

async function getOptions(db) {
  const [categories, suppliers] = await Promise.all([
    db.query(`SELECT DISTINCT c.id, c.category_name AS name
      FROM st_categorymaster c
      INNER JOIN st_additem a ON a.category_id = c.id AND a.itemtype = 'RawMaterial'
      ORDER BY c.category_name`, { type: QueryTypes.SELECT }),
    db.query('SELECT id, name FROM sub_contractors ORDER BY name', { type: QueryTypes.SELECT })
  ]);
  return { categories, suppliers };
}

async function exportExcel(db, inputFilters, res) {
  const filters = validateFilters(inputFilters);
  const result = await data(db, filters);
  const rows = result.groups;
  const unmatchedCount = result.unmatched.length;

  const workbook = new ExcelJS.Workbook();
  workbook.title = TITLE;

  const sheet = workbook.addWorksheet('Category Summary', {
    views: [{ state: 'frozen', xSplit: 3, ySplit: 3 }]
  });

  const columns = Object.keys(SUMMARY_COLUMNS);
  const lastColLetter = String.fromCharCode(65 + columns.length - 1);

  sheet.mergeCells(`A1:${lastColLetter}1`);
  sheet.getCell('A1').value = TITLE;
  sheet.getRow(1).height = 28;
  sheet.getCell('A1').font = { bold: true, size: 14 };
  sheet.getCell('A1').alignment = { vertical: 'middle' };

  sheet.mergeCells(`A2:${lastColLetter}2`);
  const note = `One row per raw material category, subcontractor and unit. ${unmatchedCount} unallocated receipts excluded from the company balances.`;
  sheet.getCell('A2').value = note;
  sheet.getRow(2).height = 32;
  sheet.getCell('A2').alignment = { wrapText: true, vertical: 'middle' };

  const headerRow = sheet.getRow(3);
  headerRow.values = Object.values(SUMMARY_COLUMNS);
  headerRow.height = 32;
  headerRow.font = { bold: true, color: { argb: 'FFFFFFFF' } };
  headerRow.alignment = { wrapText: true, vertical: 'middle' };
  headerRow.eachCell(cell => {
    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF305B84' } };
  });

  for (let i = 0; i < rows.length; i++) {
    const row = rows[i];
    const line = i + 4;
    const r = sheet.getRow(line);
    const rowValues = columns.map(key => {
      const val = row[key];
      return (key === 'sent' || key === 'received' || key === 'loss' || key === 'pending' || key === 'count')
        ? Number(val) : val;
    });
    r.values = rowValues;
    r.alignment = { wrapText: true, vertical: 'top' };

    for (const colLetter of ['D', 'E', 'F', 'G']) {
      const cell = sheet.getCell(`${colLetter}${line}`);
      if (typeof cell.value === 'number') {
        cell.numFmt = '#,##0.000';
      }
    }

    const pendingCell = sheet.getCell(`G${line}`);
    pendingCell.font = { bold: true };
    pendingCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFFF2CC' } };
  }

  sheet.columns = columns.map(key => ({
    width: ['category', 'company', 'numbers'].includes(key) ? 34 : 22
  }));

  res.setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  res.setHeader('Content-Disposition', 'attachment; filename=category-wise-raw-material-tracking.xlsx');
  await workbook.xlsx.write(res);
}

module.exports = {
  TITLE,
  QUANTITIES,
  NOTE,
  SUMMARY_COLUMNS,
  validateFilters,
  referenceNumbers,
  eligibleIssues,
  reconcile,
  data,
  formatQty,
  getTrackingReport,
  getOptions,
  exportExcel
};
