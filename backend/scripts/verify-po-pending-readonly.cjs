// Only SELECT/WITH queries and read-only transaction controls; no fixture tables.
require('../src/config/environment');
const assert = require('node:assert/strict');
const { QueryTypes } = require('sequelize');
const config = require('../src/config/sequelize');
const report = require('../src/modules/purchaseOrder/pendingReport');
const { Writable } = require('node:stream');
const ExcelJS = require('exceljs');

function fixtureSql() {
  const fixtures = {
    st_purchaseorder: [
      ['id','purchaseorder_id','is_revised','added_time','vendor_id','status'],
      [1,'P1',0,'2026-10-01 00:00:00',1,'Y'], [2,'P1',1,'2026-10-01 00:00:00',1,'R'],
      [3,'P2',0,'2026-10-02 23:59:59',2,'Y'], [4,'P3',0,'2026-10-03',1,'N'],
      [5,'P4',0,'2026-10-04',1,'Y'], [6,'P4',1,'2026-10-04',1,'N']],
    st_purchaseorderDetails: [
      ['id','poprimary_id','item_id','uom','item_qty','item_amt'],
      [1,1,1,'KG',999,1], [2,2,1,'kg',10.1,5], [3,2,1,'KG',4.2,6],
      [4,2,2,'NOS',3,1], [5,2,3,'MTR',5,1], [6,2,4,'KG',5,1], [7,2,5,'KG',5,1],
      [8,2,5,'NOS',7,2], [9,3,1,'KG',10,4], [10,4,1,'KG',100,1], [11,5,1,'KG',100,1]],
    st_additem: [['id','item_name','itemtype','uom'], [1,'Raw 1','RawMaterial',1], [2,'Raw 2','RawMaterial',2], [3,'Raw 3','RawMaterial',3], [4,'Finished','FinishedProduct',1], [5,'Conflict','RawMaterial',1]],
    st_measurementunits: [['id','unit_name'], [1,'KG'],[2,'NOS'],[3,'MTR']],
    vendors: [['id','name'], [1,'Supplier A'],[2,'Supplier B']],
    st_goodsreceive: [['id','purchaseorder_id','status'], [1,'P1','O'],[2,'P1','C'],[3,'P2','O'],[4,'WRONG','O']],
    st_stock_register: [['id','po_id','goods_id','item_id','quantity','store_type','status','return_id'],
      [1,'P1','1',1,5.1,'1','Y',null], [2,'P1','2',1,4.2,'1','Y',null], [3,'P1','1',1,99,'1','N',null],
      [4,'P1','1',1,99,'3','Y',null], [5,'P1','4',1,99,'1','Y',null], [6,'P1','1',1,1,'4','Y','1'],
      [7,'P1','1',1,99,'4','N','2'], [8,'P1','1',2,3,'1','Y',null], [9,'P2','3',1,12,'1','Y',null],
      [10,'P1','2',1,-0.5,'1','Y',null], [11,'P1','2',1,-0.2,'4','Y','4']],
    st_purchasereturn: [['id','purchaseorder_id'],[1,'P1'],[3,'P2']],
  };
  const quote = value => value === null ? 'NULL' : typeof value === 'number' ? String(value) : "'" + value.replaceAll("'", "''") + "'";
  return Object.entries(fixtures).map(([table, [columns, ...values]]) => `f_${table} AS (${values.map((row,index) => 'SELECT ' + row.map((v,i) => quote(v) + (index ? '' : ` AS \`${columns[i]}\``)).join(',')).join(' UNION ALL ')})`).join(',');
}
async function verifyFixtures(db) {
  let sql = report.query({});
  for (const table of ['st_purchaseorderDetails','st_purchaseorder','st_stock_register','st_goodsreceive','st_measurementunits','st_additem','st_purchasereturn','vendors']) sql = sql.replace(new RegExp(`\\b${table}\\b`, 'g'), `f_${table}`);
  sql = sql.replace(/^WITH /, 'WITH ' + fixtureSql() + ',');
  const rows = await db.query(sql + 'SELECT * FROM report ORDER BY id,item_id', { type: QueryTypes.SELECT });
  assert.equal(rows.length, 5); // no FinishedProduct, cancelled PO or old revision
  const first = rows.find(r => r.id === 2 && r.item_id === 1);
  assert.equal(first.ordered, '14.300'); assert.equal(first.received, '8.000'); assert.equal(first.balance, '6.300'); assert.equal(first.rates, '5.00 / 6.00');
  assert.equal(rows.find(r => r.item_id === 2).balance, '0.000');
  assert.equal(rows.find(r => r.item_id === 3).received, '0.000');
  assert.equal(Number(rows.find(r => r.item_id === 5).unit_conflict), 1);
  const excess = rows.find(r => r.id === 3); assert.equal(excess.balance, '-2.000'); assert.equal(Number(excess.incomplete_returns), 1);
  console.log('PASS: isolated SQL fixtures: revisions, cancellation, repeated lines/rates, partial/full/zero/excess, multi-GRN, inactive receipts/returns, non-receipt movement, wrong GRN, unit conflicts and incomplete returns.');
}
async function verifyCompany(name) {
  const db = await config.getTenantSequelize(name);
  const start = Date.now();
  const all = await report.rows(db, {}, 100000, 0);
  assert.ok(all.length); assert.equal(new Set(all.map(r => `${r.id}:${r.item_id}`)).size, all.length);
  const summary = { total: all.length, full: 0, partial: 0, unreceived: 0, excess: 0, conflict: 0 };
  for (const row of all) {
    if (Number(row.unit_conflict)) { summary.conflict++; continue; }
    if (Number(row.balance) === 0) summary.full++;
    else if (Number(row.balance) < 0) summary.excess++;
    else if (Number(row.received) === 0) summary.unreceived++;
    else summary.partial++;
  }
  // Independent one-row ledger comparisons, sampled across receipt categories.
  const sampled = new Map();
  for (const row of all) { const key = Number(row.received) === 0 ? 'zero' : Number(row.balance) === 0 ? 'full' : Number(row.balance) < 0 ? 'excess' : 'partial'; if (!sampled.has(key) && !Number(row.unit_conflict)) sampled.set(key, row); }
  for (const row of sampled.values()) {
    const [raw] = await db.query(`SELECT CAST(COALESCE(SUM(CASE WHEN s.store_type='1' THEN CAST(s.quantity AS DECIMAL(30,3)) ELSE -CAST(s.quantity AS DECIMAL(30,3)) END),0) AS DECIMAL(40,3)) qty
      FROM st_stock_register s WHERE s.po_id=:po AND s.item_id=:item AND s.status IN ('Y','R')
      AND (s.store_type='4' OR (s.store_type='1' AND s.goods_id IN (SELECT CAST(g.id AS CHAR) FROM st_goodsreceive g WHERE g.purchaseorder_id=:po AND g.status IN ('O','C'))))`, { replacements: { po: row.purchaseorder_id, item: row.item_id }, type: QueryTypes.SELECT });
    assert.equal(row.received, raw.qty);
  }
  const sample = all[0], iso = sample.po_date.split('-').reverse().join('-');
  for (const filter of [{ item_id: String(sample.item_id) }, { vendor_id: String(sample.vendor_id) }, { datefrom: iso }, { dateto: iso }, { datefrom: iso, dateto: iso, item_id: String(sample.item_id), vendor_id: String(sample.vendor_id) }]) {
    const result = await report.rows(db, report.validate(filter), 100000, 0);
    const expected = all.filter(r => (!filter.item_id || String(r.item_id) === filter.item_id) && (!filter.vendor_id || String(r.vendor_id) === filter.vendor_id) && (!filter.datefrom || r.po_date.split('-').reverse().join('-') >= filter.datefrom) && (!filter.dateto || r.po_date.split('-').reverse().join('-') <= filter.dateto));
    assert.deepEqual(result.map(r => `${r.id}:${r.item_id}`), expected.map(r => `${r.id}:${r.item_id}`));
  }
  const first = await report.list(db, {}), second = await report.list(db, { page: 2 });
  assert.equal(first.pagination.total, all.length); assert.equal(first.data.length, 50); assert.equal(second.data.length, 50);
  assert.deepEqual(second.data.map(r => `${r.po_id}:${r.item_id}`), all.slice(50,100).map(r => `${r.id}:${r.item_id}`));
  const frequency = new Map();
  for (const row of all) frequency.set(row.item_id, (frequency.get(row.item_id) || 0) + 1);
  const mostUsed = [...frequency].sort((a,b) => b[1] - a[1])[0][0];
  const expectedExport = all.filter(row => row.item_id === mostUsed);
  const chunks = [], output = new Writable({ write(chunk,_,done) { chunks.push(chunk); done(); } }); output.setHeader = () => {};
  await report.exportExcel(db, { item_id: String(mostUsed), limit: 1, page: 2 }, output);
  const workbook = new ExcelJS.Workbook(); await workbook.xlsx.load(Buffer.concat(chunks));
  const sheet = workbook.worksheets[0]; assert.equal(sheet.rowCount, expectedExport.length + 2);
  for (const [index,row] of expectedExport.entries()) {
    const expected = report.cells(row);
    if (index > 0 && expectedExport[index - 1].id === row.id) expected.splice(0,3,'','','');
    assert.deepEqual(Array.from({ length: 8 },(_,column) => sheet.getRow(index+3).getCell(column+1).value || ''), expected);
  }
  console.log(`PASS: filtered Excel ${expectedExport.length} rows, independent of requested page/limit; all eight formatted cells compared.`);
  console.log(name, JSON.stringify(summary), `checks ${Date.now() - start}ms`);
  return db;
}
(async () => {
  try {
    const db = await verifyCompany('tirupati_tppl'); await verifyFixtures(db); await verifyCompany('tirupati_kcpl');
  } finally { await Promise.all(['tirupati_tppl','tirupati_kcpl'].map(async name => (await config.getTenantSequelize(name)).close())); await config.centralSequelize.close(); }
})().catch(error => { console.error(error.message); process.exitCode = 1; });
