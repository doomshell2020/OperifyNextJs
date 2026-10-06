const { QueryTypes } = require('sequelize');

function invalid(message, status = 400) {
  const error = new Error(message);
  error.status = status;
  return error;
}
function number(value, field, positive = false) {
  const result = Number(value);
  if (value === '' || value == null || !Number.isFinite(result) || result < 0 || (positive && result === 0)) {
    throw invalid(`${field} must be a valid ${positive ? 'positive' : 'non-negative'} number.`);
  }
  return result;
}
function date(value, field) {
  if (!/^\d{4}-\d{2}-\d{2}$/.test(String(value)) || Number.isNaN(Date.parse(value)) || new Date(value).toISOString().slice(0, 10) !== value) {
    throw invalid(`${field} must be a valid date.`);
  }
  return value;
}
function storedDate(value, field) {
  if(value instanceof Date) {
    if(Number.isNaN(value.getTime()))throw invalid(`${field} must be a valid date.`);
    return date(value.toISOString().slice(0,10),field);
  }
  return date(String(value || '').slice(0,10),field);
}
const round = value => Math.round((Number(value) + Number.EPSILON) * 100) / 100;
const select = (db, sql, replacements, transaction) => db.query(sql, { replacements, transaction, type: QueryTypes.SELECT });
const write = (db, sql, replacements, transaction) => db.query(sql, { replacements, transaction, type: QueryTypes.UPDATE });
const insert = async (db, sql, replacements, transaction) => (await db.query(sql, { replacements, transaction, type: QueryTypes.INSERT }))[0];

// Lock the entire revision family. Inspection and receipt writers use the same
// lock order, so concurrent requests cannot consume the same remaining quantity.
async function purchaseOrder(db, poNumber, transaction) {
  const rows = await select(db, 'SELECT * FROM st_purchaseorder WHERE purchaseorder_id=:poNumber ORDER BY id DESC FOR UPDATE', { poNumber }, transaction);
  const po = rows.find(row => row.status !== 'N');
  if (!po) throw invalid('Purchase Order not found.', 404);
  if (po.postatus === 'C') throw invalid('Purchase Order is already complete.', 409);
  return po;
}

module.exports = { invalid, number, date, storedDate, round, select, write, insert, purchaseOrder };
