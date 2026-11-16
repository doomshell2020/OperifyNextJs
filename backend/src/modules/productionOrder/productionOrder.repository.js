const { QueryTypes } = require('sequelize');
const select = (db, sql, replacements = {}, transaction) => db.query(sql, { replacements, type: QueryTypes.SELECT, transaction });
const joins = `FROM productionorder o
  LEFT JOIN contracts c ON c.id=o.contract_id
  LEFT JOIN st_additem a ON a.id=o.item_id
  LEFT JOIN (SELECT po_id, SUM(CASE WHEN productprocess_id=8 THEN COALESCE(production_shift_a,0)+COALESCE(production_shift_b,0) ELSE 0 END) AS prepared_qty,COUNT(*) AS production_count FROM production GROUP BY po_id) p ON p.po_id=o.po_id`;
const columns = `o.*, c.title, c.workorder, a.item_name,
  COALESCE(p.prepared_qty,0) AS prepared_qty,
  (COALESCE(p.production_count,0)>0) AS production_started`;

function fail(message, status = 400) { const error = new Error(message); error.status = status; throw error; }
function id(value) {
  if (!/^\d+$/.test(String(value)) || !Number.isSafeInteger(Number(value)) || Number(value) < 1) fail('Invalid ID');
  return Number(value);
}
function date(value, label = 'Date') {
  let text = String(value || '');
  if (/^\d{2}-\d{2}-\d{4}$/.test(text)) text = text.split('-').reverse().join('-');
  const parsed = new Date(text + 'T00:00:00Z');
  if (!/^\d{4}-\d{2}-\d{2}$/.test(text) || !Number.isFinite(parsed.getTime()) || parsed.toISOString().slice(0,10) !== text) fail(`Invalid ${label}`);
  return text;
}
function conditions(query = {}) {
  const parts = [], values = {};
  if (query.contract_id) { values.contract_id = id(query.contract_id); parts.push('o.contract_id=:contract_id'); }
  if (query.status) {
    if (!['O','C'].includes(query.status)) fail('Invalid status');
    values.status = query.status; parts.push('o.status=:status');
  }
  // Cake's searchpodetail uses equality for From, not a range lower bound.
  if (query.datefrom && query.datefrom !== '1970-01-01') { values.datefrom = date(query.datefrom, 'completion date from'); parts.push('DATE(o.complete_date)=:datefrom'); }
  if (query.dateto && query.dateto !== '1970-01-01') { values.dateto = date(query.dateto, 'completion date to'); parts.push('DATE(o.complete_date)<=:dateto'); }
  return { sql: parts.length ? 'WHERE ' + parts.join(' AND ') : '', values };
}
function order(query = {}, exporting = false) {
  const allowed = {id:'o.id',po_id:'o.po_id',issuedate:'o.issuedate',title:'c.title',item_name:'a.item_name',startdate:'o.startdate',enddate:'o.enddate',plannedqty:'CAST(o.plannedqty AS DECIMAL(30,8))',prepared_qty:'prepared_qty'};
  const sort = String(query.sort || '').split('.').at(-1);
  const column = Object.hasOwn(allowed,sort) ? allowed[sort] : exporting ? 'o.id' : 'o.po_id';
  return `${column} ${String(query.direction || 'desc').toLowerCase() === 'asc' ? 'ASC' : 'DESC'}, o.id DESC`;
}
async function rows(db, query = {}, paging, transaction, exporting = false) {
  const where = conditions(query);
  return select(db, `SELECT ${columns} ${joins} ${where.sql} ORDER BY ${order(query,exporting)}${paging ? ' LIMIT :limit OFFSET :offset' : ''}`, {...where.values,...paging},transaction);
}
async function products(db, contractId, transaction) {
  return select(db, `SELECT b.*,a.item_name,u.unit_name,
    b.quantity-COALESCE((SELECT SUM(o.plannedqty) FROM productionorder o WHERE o.contract_id=b.contract_id AND o.item_id=b.product_id),0) AS pendingqty
    FROM bom_finisedproduct b LEFT JOIN st_additem a ON a.id=b.product_id LEFT JOIN st_measurementunits u ON u.id=a.uom
    WHERE b.contract_id=:contract_id ORDER BY b.id`, {contract_id:id(contractId)},transaction);
}
module.exports = {select,fail,id,date,conditions,order,rows,products};
