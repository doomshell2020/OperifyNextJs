const { QueryTypes, Transaction } = require('sequelize');
const { paginationInput, paginationResult } = require('../../utils/listPagination');
const TITLE = 'PO-Wise Raw Material Pending Order List';
const HEADERS = ['PO NO', 'DATE', 'NAME OF PARTY', 'ITEMS', 'ORDER QTY', 'RECEIVED QTY', 'RATE IN Rs', 'BALANCE QTY'];
const invalid = message => Object.assign(new Error(message), { status: 400 });

function validate(input = {}) {
  const result = {};
  for (const key of ['item_id', 'vendor_id']) {
    const value = input[key];
    if (value !== undefined && value !== null && value !== '') {
      let ids = [];
      if (Array.isArray(value)) {
        if (value.length === 0) throw invalid(`Invalid ${key}`);
        for (const v of value) {
          const s = String(v).trim();
          if (!/^[1-9]\d{0,9}$/.test(s)) throw invalid(`Invalid ${key}`);
          ids.push(s);
        }
      } else if (typeof value === 'string' || typeof value === 'number') {
        const raw = String(value).trim();
        if (raw !== '') {
          const parts = raw.split(',');
          for (const p of parts) {
            const s = p.trim();
            if (!/^[1-9]\d{0,9}$/.test(s)) throw invalid(`Invalid ${key}`);
            ids.push(s);
          }
        }
      } else {
        throw invalid(`Invalid ${key}`);
      }
      ids = [...new Set(ids)];
      if (ids.length > 0) {
        result[key] = ids.length === 1 ? ids[0] : ids;
      }
    }
  }
  for (const key of ['datefrom', 'dateto']) {
    const value = input[key];
    if (value !== undefined && value !== null && value !== '') {
      if (typeof value !== 'string') throw invalid(`Invalid ${key}`);
      const trimmed = value.trim();
      let iso = trimmed;
      if (/^\d{2}-\d{2}-\d{4}$/.test(trimmed)) {
        const [d, m, y] = trimmed.split('-');
        iso = `${y}-${m}-${d}`;
      }
      if (!/^\d{4}-\d{2}-\d{2}$/.test(iso) || iso < '1000-01-01' || iso > '9998-12-31') throw invalid(`Invalid ${key}`);
      const date = new Date(`${iso}T00:00:00Z`);
      if (!Number.isFinite(date.getTime()) || date.toISOString().slice(0, 10) !== iso) throw invalid(`Invalid ${key}`);
      result[key] = iso;
    }
  }
  if (result.datefrom && result.dateto && result.datefrom > result.dateto) throw invalid('Date From cannot be later than Date To.');

  const postatusInput = input.postatus !== undefined ? input.postatus : input.status;
  if (postatusInput !== undefined && postatusInput !== null && postatusInput !== '') {
    if (typeof postatusInput !== 'string') throw invalid('Invalid postatus');
    const upper = postatusInput.trim().toUpperCase();
    let val = '';
    if (upper === 'O' || upper === 'OPEN') val = 'O';
    else if (upper === 'C' || upper === 'CLOSE' || upper === 'CLOSED') val = 'C';
    else if (upper === 'ALL' || upper === '') val = '';
    else throw invalid('Invalid postatus');
    if (val) result.postatus = val;
  }

  for (const key of ['page', 'limit']) {
    if (input[key] !== undefined && (typeof input[key] === 'object' || !/^[1-9]\d*$/.test(String(input[key])) || !Number.isSafeInteger(Number(input[key])))) throw invalid(`Invalid ${key}`);
  }
  return result;
}

// All aggregates are formed before their joins. Ledger IDs are never joined to
// individual PO lines: this schema has no receipt-to-detail foreign key.
function query(filters) {
  const where = ["po.status IN ('Y','R')"];
  if (filters.item_id) {
    where.push(Array.isArray(filters.item_id) ? 'd.item_id IN (:item_id)' : 'd.item_id=:item_id');
  }
  if (filters.vendor_id) {
    where.push(Array.isArray(filters.vendor_id) ? 'po.vendor_id IN (:vendor_id)' : 'po.vendor_id=:vendor_id');
  }
  if (filters.datefrom) where.push('po.added_time>=:datefrom');
  if (filters.dateto) where.push('po.added_time<DATE_ADD(:dateto, INTERVAL 1 DAY)');
  if (filters.postatus) where.push('po.postatus=:postatus');
  return `WITH latest AS (
    SELECT purchaseorder_id,MAX(id) id FROM st_purchaseorder GROUP BY purchaseorder_id
  ), po_lines AS (
    SELECT po.id,po.purchaseorder_id,po.is_revised,DATE_FORMAT(po.added_time,'%d-%m-%Y') po_date,
      po.vendor_id,v.name vendor_name,d.item_id,i.item_name,
      UPPER(TRIM(COALESCE(NULLIF(TRIM(d.uom),''),u.unit_name,''))) unit,
      CAST(d.item_qty AS DECIMAL(30,3)) qty,CAST(d.item_amt AS DECIMAL(30,2)) rate
    FROM latest l JOIN st_purchaseorder po ON po.id=l.id
    JOIN st_purchaseorderDetails d ON d.poprimary_id=po.id
    JOIN st_additem i ON i.id=d.item_id AND i.itemtype IN ('RawMaterial', 'Raw Material')
    LEFT JOIN st_measurementunits u ON u.id=i.uom LEFT JOIN vendors v ON v.id=po.vendor_id
    WHERE ${where.join(' AND ')}
  ), unit_lines AS (
    SELECT id,purchaseorder_id,is_revised,po_date,vendor_id,vendor_name,item_id,item_name,
      unit,SUM(qty) qty,GROUP_CONCAT(DISTINCT rate ORDER BY rate SEPARATOR ' / ') rates
    FROM po_lines GROUP BY id,purchaseorder_id,is_revised,po_date,vendor_id,vendor_name,item_id,item_name,unit
  ), orders AS (
    SELECT id,purchaseorder_id,is_revised,po_date,vendor_id,vendor_name,item_id,item_name,
      SUM(qty) ordered,GROUP_CONCAT(rates ORDER BY unit SEPARATOR ' / ') rates,COUNT(*) unit_count,MAX(unit) unit,
      GROUP_CONCAT(CONCAT(CAST(qty AS CHAR),IF(unit='','',CONCAT(' ',unit))) ORDER BY unit SEPARATOR ' / ') ordered_parts
    FROM unit_lines GROUP BY id,purchaseorder_id,is_revised,po_date,vendor_id,vendor_name,item_id,item_name
  ), movements AS (
    SELECT s.po_id,s.item_id,
      SUM(CASE WHEN s.store_type='1' THEN CAST(s.quantity AS DECIMAL(30,3)) ELSE -CAST(s.quantity AS DECIMAL(30,3)) END) received
    FROM st_stock_register s
    WHERE s.status IN ('Y','R') AND (
      (s.store_type='1' AND EXISTS (SELECT 1 FROM st_goodsreceive g
        WHERE g.id=s.goods_id AND CONVERT(g.id USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(s.goods_id USING utf8mb4) COLLATE utf8mb4_unicode_ci AND CONVERT(g.purchaseorder_id USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(s.po_id USING utf8mb4) COLLATE utf8mb4_unicode_ci AND g.status IN ('O','C')))
      OR (s.store_type='4' AND NULLIF(s.po_id,'') IS NOT NULL))
    GROUP BY s.po_id,s.item_id
  ), historical_units AS (
    SELECT p.purchaseorder_id,d.item_id,COUNT(DISTINCT UPPER(TRIM(COALESCE(NULLIF(TRIM(d.uom),''),u.unit_name,'')))) unit_count
    FROM st_purchaseorder p JOIN st_purchaseorderDetails d ON d.poprimary_id=p.id
    JOIN st_additem i ON i.id=d.item_id LEFT JOIN st_measurementunits u ON u.id=i.uom
    WHERE p.status IN ('Y','R') GROUP BY p.purchaseorder_id,d.item_id
  ), incomplete_returns AS (
    SELECT r.purchaseorder_id,COUNT(*) n FROM st_purchasereturn r
    WHERE NOT EXISTS (SELECT 1 FROM st_stock_register s WHERE s.store_type='4' AND CONVERT(s.return_id USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(r.id USING utf8mb4) COLLATE utf8mb4_unicode_ci)
    GROUP BY r.purchaseorder_id
  ), report AS (
    SELECT o.*,COALESCE(m.received,0) received,
      o.ordered-COALESCE(m.received,0) balance,
      (o.unit_count>1 OR (m.po_id IS NOT NULL AND (h.unit_count>1 OR o.unit=''))) unit_conflict,
      COALESCE(r.n,0) incomplete_returns
    FROM orders o
    LEFT JOIN movements m ON CONVERT(m.po_id USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(o.purchaseorder_id USING utf8mb4) COLLATE utf8mb4_unicode_ci AND m.item_id=o.item_id
    LEFT JOIN historical_units h ON CONVERT(h.purchaseorder_id USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(o.purchaseorder_id USING utf8mb4) COLLATE utf8mb4_unicode_ci AND h.item_id=o.item_id
    LEFT JOIN incomplete_returns r ON CONVERT(r.purchaseorder_id USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(o.purchaseorder_id USING utf8mb4) COLLATE utf8mb4_unicode_ci
  ) `;
}

// Format DECIMAL strings without conversion through binary floating point.
function decimal(value, places = 3) {
  const match = /^(-?)(\d+)(?:\.(\d+))?$/.exec(String(value ?? '0'));
  if (!match) throw new Error('Invalid stored quantity');
  return match[1] + match[2].replace(/\B(?=(\d{3})+(?!\d))/g, ',') + (places ? '.' + (match[3] || '').padEnd(places, '0').slice(0, places) : '');
}
function cells(row) {
  const suffix = row.unit ? ` ${row.unit}` : '';
  const warning = Number(row.unit_conflict) ? ' [UNIT CONFLICT: receipt allocation unavailable]' : '';
  const incomplete = Number(row.incomplete_returns) ? ' [Incomplete return: quantity unavailable]' : '';
  const balance = /^-?0(?:\.0+)?$/.test(String(row.balance)) ? '-' : String(row.balance).startsWith('-') ? `(${decimal(String(row.balance).slice(1))})${suffix}` : decimal(row.balance) + suffix;
  return [String(row.purchaseorder_id) + (Number(row.is_revised) > 0 ? ` R-${row.is_revised}` : ''), row.po_date || '', row.vendor_name || '',
    (row.item_name || '') + warning + incomplete,
    Number(row.unit_conflict) ? row.ordered_parts.split(' / ').map(part => part.replace(/^-?\d+(?:\.\d+)?/, number => decimal(number))).join(' / ') : decimal(row.ordered) + suffix,
    Number(row.unit_conflict) ? 'Unit conflict' : decimal(row.received) + suffix,
    row.rates ? [...new Set(row.rates.split(' / '))].map(rate => decimal(rate, 2)).join(' / ') : '',
    Number(row.unit_conflict) ? 'Unit conflict' : balance];
}
const select = (db, sql, replacements, transaction) => db.query(sql, { replacements, type: QueryTypes.SELECT, transaction });
async function rows(db, filters, limit, offset, transaction) {
  return select(db, query(filters) + 'SELECT * FROM report ORDER BY id DESC,item_id ASC LIMIT :limit OFFSET :offset', { ...filters, limit, offset }, transaction);
}
async function list(db, input) {
  const filters = validate(input), { page, limit, offset } = paginationInput(input, 'purchaseOrder');
  return db.transaction({ isolationLevel: Transaction.ISOLATION_LEVELS.REPEATABLE_READ, readOnly: true }, async transaction => {
    const [count] = await select(db, query(filters) + 'SELECT COUNT(*) total FROM report', filters, transaction);
    const data = await rows(db, filters, limit, offset, transaction);
    return { data: data.map(row => ({ po_id: row.id, item_id: row.item_id, cells: cells(row) })), pagination: paginationResult(count.total, page, limit, data.length) };
  });
}
async function exportExcel(db, input, output) {
  const filters = validate(input);
  const ExcelJS = require('exceljs');
  return db.transaction({ isolationLevel: Transaction.ISOLATION_LEVELS.REPEATABLE_READ, readOnly: true }, async transaction => {
    // Run before writing headers so schema errors still produce a JSON error.
    let batch = await rows(db, filters, 1000, 0, transaction);
    output.setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    output.setHeader('Content-Disposition', 'attachment; filename=PO_Raw_Material_Pending.xlsx');
    const book = new ExcelJS.stream.xlsx.WorkbookWriter({ stream: output, useStyles: true });
    const sheet = book.addWorksheet('PO Pending', { views: [{ state: 'frozen', ySplit: 2 }] });
    sheet.columns = [24,16,36,60,26,26,26,26].map(width => ({ width }));
    sheet.mergeCells('A1:H1'); sheet.getCell('A1').value = TITLE; sheet.getRow(1).font = { bold: true, size: 14 }; sheet.getRow(1).commit();
    const header = sheet.addRow(HEADERS); header.font = { bold: true }; header.commit();
    let offset = 0, previous;
    while (batch.length) {
      for (const entry of batch) {
        if (output.destroyed) throw new Error('Export connection closed');
        const values = cells(entry);
        if (previous === entry.id) values.splice(0, 3, '', '', '');
        previous = entry.id;
        // Explicit strings plus prefixing dangerous initial characters prevent
        // formula interpretation even in spreadsheet import/conversion tools.
        const row = sheet.addRow(values.map(value => /^[\s\u0000-\u001f]*[=+@-]/.test(value) && value !== '-' ? "'" + value : value));
        row.alignment = { vertical: 'top', wrapText: true }; row.commit();
      }
      offset += batch.length;
      if (batch.length < 1000) break;
      batch = await rows(db, filters, 1000, offset, transaction);
    }
    await book.commit();
  });
}
module.exports = { TITLE, HEADERS, validate, query, decimal, cells, list, rows, exportExcel };
