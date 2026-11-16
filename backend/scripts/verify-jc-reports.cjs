// Read-only MySQL verification for JC Material and Raw Material Tracking reports.
// No tables or records are created or modified.
require('../src/config/environment');
const assert = require('node:assert/strict');
const { Writable } = require('node:stream');
const ExcelJS = require('exceljs');
const { getTenantSequelize } = require('../src/config/sequelize');
const jcMaterial = require('../src/modules/jcReport/jcMaterial.service');
const rawTracking = require('../src/modules/jcReport/rawMaterialTracking.service');

async function testJcMaterial(db) {
  console.log('Verifying JC Material Report...');

  // 1. Options
  const options = await jcMaterial.getOptions(db);
  assert.ok(Array.isArray(options.suppliers) && options.suppliers.length > 0, 'Suppliers options present');
  assert.ok(Array.isArray(options.items) && options.items.length > 0, 'Items options present');

  // 2. Default results
  const result = await jcMaterial.jcResults(db, {}, 1, 50);
  assert.equal(result.pagination.total, 104, 'Total JC count matches expected');
  assert.equal(result.pagination.pages, 3, 'Total pages match expected');
  assert.equal(result.data.length, 50, 'Page 1 size is 50');
  assert.equal(result.summary.jc_count, 104, 'Summary jc_count');
  assert.equal(result.summary.pending_count, 86, 'Summary pending_count');
  assert.ok(result.summary.units.kg, 'KG unit summary present');
  assert.equal(Number(result.summary.units.kg.created.toFixed(2)), 266661.09, 'KG created qty');
  assert.equal(Number(result.summary.units.kg.received.toFixed(2)), 118455.48, 'KG received qty');

  // 3. Filter validations
  for (const invalid of [
    { from_date: '2026-13-01' },
    { from_date: '2026-08-31', to_date: '2026-08-01' },
    { vendor_id: 'abc' },
    { item_id: '0' },
    { balance: 'UnknownStatus' }
  ]) {
    assert.throws(() => jcMaterial.validateFilters(invalid));
  }

  // 4. Balance filter parity
  const completed = await jcMaterial.jcResults(db, { balance: 'Completed' }, 1, 50);
  assert.equal(completed.pagination.total, 9, 'Completed balance count');
  for (const row of completed.data) {
    assert.equal(row.balance_status, 'Completed');
  }

  const pending = await jcMaterial.jcResults(db, { balance: 'Pending' }, 1, 50);
  assert.equal(pending.pagination.total, 73, 'Pending balance count');
  for (const row of pending.data) {
    assert.equal(row.balance_status, 'Pending');
  }

  // 5. Excel export
  const chunks = [];
  const res = new Writable({ write(c, e, cb) { chunks.push(c); cb(); } });
  res.setHeader = () => {};
  await jcMaterial.exportExcel(db, {}, res);
  const wb = new ExcelJS.Workbook();
  await wb.xlsx.load(Buffer.concat(chunks));
  const sheet = wb.getWorksheet('JC Report');
  assert.ok(sheet, 'Worksheet exists');
  assert.equal(sheet.rowCount, 108, 'Workbook total rows (4 header + 104 data)');
  assert.equal(sheet.getCell('A1').value, 'JC Material Report | JC Report');

  console.log('PASS: JC Material Report verified successfully.');
}

async function testRawMaterialTracking(db) {
  console.log('Verifying Category-wise Raw Material Tracking Report...');

  // 1. Options
  const options = await rawTracking.getOptions(db);
  assert.ok(Array.isArray(options.categories) && options.categories.length > 0, 'Categories options present');
  assert.ok(Array.isArray(options.suppliers) && options.suppliers.length > 0, 'Suppliers options present');

  // 2. Default results
  const result = await rawTracking.getTrackingReport(db, {}, 1, 50);
  assert.equal(result.pagination.total, 13, 'Total groups match expected');
  assert.equal(result.unmatched.length, 7, 'Unmatched count matches expected');
  assert.ok(result.summary['4'], 'Unit 4 (KG) summary present');
  assert.equal(Number(result.summary['4'].sent.toFixed(2)), 266661.09, 'Total sent qty');
  assert.equal(Number(result.summary['4'].received.toFixed(2)), 118455.48, 'Total received qty');
  assert.equal(Number(result.summary['4'].loss.toFixed(2)), 0.0, 'Total loss qty is 0');
  assert.equal(Number(result.summary['4'].pending.toFixed(2)), 150398.16, 'Total pending qty');

  // 3. Filter validations
  for (const invalid of [
    { from_date: '2026-13-01' },
    { from_date: '2026-08-31', to_date: '2026-08-01' },
    { vendor_id: 'abc' },
    { category_id: '0' }
  ]) {
    assert.throws(() => rawTracking.validateFilters(invalid));
  }

  // 4. Category filter
  const catResult = await rawTracking.getTrackingReport(db, { category_id: '3' }, 1, 50);
  assert.equal(catResult.pagination.total, 1, 'Category 3 count');
  assert.equal(catResult.data[0].category, 'ALUMINIUM ROD');

  // 5. Excel export
  const chunks = [];
  const res = new Writable({ write(c, e, cb) { chunks.push(c); cb(); } });
  res.setHeader = () => {};
  await rawTracking.exportExcel(db, {}, res);
  const wb = new ExcelJS.Workbook();
  await wb.xlsx.load(Buffer.concat(chunks));
  const sheet = wb.getWorksheet('Category Summary');
  assert.ok(sheet, 'Worksheet exists');
  assert.equal(sheet.rowCount, 16, 'Workbook total rows (3 header + 13 data)');
  assert.equal(sheet.getCell('A1').value, 'Category-wise Raw Material Tracking Report');

  console.log('PASS: Category-wise Raw Material Tracking Report verified successfully.');
}

(async () => {
  const db = await getTenantSequelize(process.env.STOCK_TEST_DB || 'tirupati_tppl');
  const original = db.query.bind(db);
  db.query = (sql, options) => {
    assert.match(sql.trim(), /^(SELECT|SHOW|SET|START TRANSACTION|COMMIT|ROLLBACK)\b/i, 'Only read queries allowed');
    return original(sql, options);
  };

  try {
    await testJcMaterial(db);
    await testRawMaterialTracking(db);
    console.log(JSON.stringify({ result: 'PASS (read-only)', jcRows: 104, rawGroups: 13 }));
  } finally {
    await db.close();
  }
})().catch(err => {
  console.error(err);
  process.exit(1);
});
