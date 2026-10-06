const { QueryTypes } = require('sequelize');
const { getTenantSequelize } = require('../../config/sequelize');
const schemas = new WeakMap();
const fail = (message, status = 400) => { const error = new Error(message); error.status = status; throw error; };
const identifier = value => {
  if (!/^[a-zA-Z0-9_]+$/.test(String(value))) fail('Invalid database identifier', 403);
  return '`' + value + '`';
};
const table = (name, database) => (database ? identifier(database) + '.' : '') + identifier(name);
const select = (db, sql, replacements = {}, transaction) => db.query(sql, { replacements, transaction, type: QueryTypes.SELECT });
async function columns(db, name, database) {
  if (!schemas.has(db)) schemas.set(db, new Map());
  const cache = schemas.get(db), key = `${database || ''}.${name}`;
  if (!cache.has(key)) cache.set(key, await select(db, `SHOW COLUMNS FROM ${table(name, database)}`));
  return cache.get(key);
}
// The deployed CakePHP schema differs from the generated Sequelize models.
// Only persist explicitly supplied fields that actually exist; never sync/alter tables.
async function insert(db, name, values, transaction, database) {
  const names = new Set((await columns(db, name, database)).map(c => c.Field));
  const fields = Object.keys(values).filter(key => names.has(key) && values[key] !== undefined);
  const replacements = Object.fromEntries(fields.map((key, i) => ['v' + i, values[key]]));
  const [id] = await db.query(`INSERT INTO ${table(name, database)} (${fields.map(identifier).join(',')}) VALUES (${fields.map((_, i) => ':v' + i).join(',')})`, { replacements, transaction, type: QueryTypes.INSERT });
  return id;
}
function positiveId(value) {
  if (!/^\d+$/.test(String(value)) || !Number.isSafeInteger(Number(value)) || Number(value) <= 0) fail('Invalid record ID');
  return Number(value);
}
function number(value, label, positive = false) {
  const n = Number(value || 0);
  if (!Number.isFinite(n) || n < 0 || (positive && n <= 0)) fail(`${label} must be ${positive ? 'greater than zero' : 'a non-negative number'}`);
  return n;
}
function date(value, label = 'Date') {
  const str = String(value || '');
  if (!/^\d{4}-\d{2}-\d{2}$/.test(str) || Number.isNaN(Date.parse(str)) || new Date(str).toISOString().slice(0, 10) !== str) fail(`${label} is required and must be a valid date`);
  return str;
}
const today = () => new Intl.DateTimeFormat('en-CA', { timeZone: 'Asia/Kolkata', year: 'numeric', month: '2-digit', day: '2-digit' }).format(new Date());
const round = value => Math.round((Number(value) + Number.EPSILON) * 100) / 100;
function paginate(items, query = {}, defaultLimit = 50) {
  const page = Math.max(1, parseInt(query.page) || 1), limit = Math.min(100, Math.max(1, parseInt(query.limit) || defaultLimit));
  const total = items.length, totalPages = Math.ceil(total / limit);
  if (page > Math.max(1, totalPages)) fail('Page not found', 404);
  return { items: items.slice((page - 1) * limit, page * limit), total, page, limit, totalPages };
}
function filterRows(rows, query, dateField) {
  const search = String(query.search || '').toLowerCase();
  const challanNo = String(query.challan_no || query.challanNo || '').toLowerCase();
  const from = query.from_date || query.fromDate, to = query.to_date || query.toDate;
  const vendor = query.sub_contractor_id || query.vendor_id || query.vendorId;
  return rows.filter(row => (!challanNo || String(row.challan_no || '').toLowerCase().includes(challanNo)) && (!search || [row.challan_no, row.gatepass_no, row.reference_jc_no, row.item_name, row.sender_name, row.company_name, row.vendor_name].some(v => String(v || '').toLowerCase().includes(search))) &&
    (!vendor || String(row.sub_contractor_id || row.sub_contractors_id) === String(vendor)) &&
    (!query.status || row.status === query.status) &&
    (!from || String(row[dateField] || '').slice(0, 10) >= from) && (!to || String(row[dateField] || '').slice(0, 10) <= to));
}
async function linked(dbName) {
  const db = await getTenantSequelize(dbName);
  return select(db, "SELECT DISTINCT name, database_name FROM sub_contractors WHERE database_name IS NOT NULL AND database_name != '' AND database_name != :dbName", { dbName });
}
async function source(dbName, senderDb, challanId, transaction, connection) {
  const db = connection || await getTenantSequelize(dbName);
  const id = positiveId(challanId);
  if (senderDb === dbName) {
    const [jc] = await select(db, `SELECT * FROM job_challans WHERE id=:id${transaction ? ' FOR UPDATE' : ''}`, { id }, transaction);
    if (!jc) fail('Job Challan not found', 404);
    return jc;
  }
  if (!(await linked(dbName)).some(row => row.database_name === senderDb)) fail('Sender company is not linked to this tenant', 403);
  const [jc] = await select(db, `SELECT j.* FROM ${table('job_challans', senderDb)} j JOIN ${table('sub_contractors', senderDb)} s ON s.id=j.sub_contractors_id WHERE j.id=:id AND s.database_name=:dbName${transaction ? ' FOR UPDATE' : ''}`, { id, dbName }, transaction);
  if (!jc) fail('Unauthorized or invalid Job Challan', 403);
  return jc;
}
async function stock(db, itemId, transaction, database) {
  const [row] = await select(db, `SELECT ROUND(COALESCE(SUM(CASE WHEN store_type IN ('0','1','3') THEN quantity ELSE 0 END),0),2)-ROUND(COALESCE(SUM(CASE WHEN store_type IN ('2','4') THEN quantity ELSE 0 END),0),2) AS balance FROM ${table('st_stock_register', database)} WHERE item_id=:itemId`, { itemId }, transaction);
  return Number(row.balance);
}
async function movement(db, { challan_id, item_id, quantity, issue_date, sub_contractors_id, store_type }, transaction) {
  return insert(db, 'st_stock_register', { indent_id: String(challan_id || 0), contract_id: '0', finishedproduct_id: '0', item_id, quantity, issue_date, sub_contractors_id, store_type, added_time: new Date(), status: 'Y' }, transaction);
}
module.exports = { fail, identifier, table, select, columns, insert, positiveId, number, date, today, round, paginate, filterRows, linked, source, stock, movement };
