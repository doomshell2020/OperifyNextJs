// Read-only regression against existing records. Never creates or updates data.
require('../src/config/environment');
const assert = require('node:assert/strict');
const { Writable } = require('node:stream');
const ExcelJS = require('exceljs');
const { QueryTypes } = require('sequelize');
const { getTenantSequelize, centralSequelize } = require('../src/config/sequelize');
const service = require('../src/modules/stockRegister/stockRegister.service');
const controller = require('../src/modules/stockRegister/stockRegister.controller');

(async () => {
  const db = await getTenantSequelize(process.env.STOCK_TEST_DB || 'tirupati_tppl');
  const originalQuery = db.query.bind(db);
  db.query = (sql, options) => {
    assert.match(sql.trim(), /^(SELECT|SHOW)\b/i, 'Only read-only queries are allowed');
    return originalQuery(sql, options);
  };
  try {
    const categories = await db.query(`SELECT DISTINCT p.category_id FROM st_additem p
      JOIN st_stock_register sr ON sr.item_id=p.id AND sr.status!='N'
      WHERE p.itemtype='RawMaterial' AND p.status='Y' AND p.category_id!=25
      ORDER BY p.category_id LIMIT 2`, { type: QueryTypes.SELECT });
    assert.equal(categories.length, 2, 'Two existing categories with stock are required');
    const ids = categories.map(row => String(row.category_id));
    const [latest] = await db.query(`SELECT MAX(DATE(COALESCE(delivery_date,issue_date,created))) AS date
      FROM st_stock_register WHERE status!='N'`, { type: QueryTypes.SELECT });
    const filters = { date_from: latest.date, date_to: latest.date };
    const multi = await service.getStockRegister(db, { ...filters, category_ids: ids.join(',') });
    const singles = (await Promise.all(ids.map(category_id => service.getStockRegister(db, { ...filters, category_id })))).flat();
    const sort = rows => [...rows].sort((a,b) => a.item_id-b.item_id);
    assert.ok(multi.length);
    assert.deepEqual(sort(multi), sort(singles));
    assert.deepEqual(await service.getStockRegister(db, { ...filters, category_ids: [...ids, ids[0]] }), multi);
    assert.deepEqual(await service.getStockRegister(db, { ...filters, category_ids: [] }), await service.getStockRegister(db, filters));
    const product_id = String(multi[0].item_id);
    const product = await service.getStockRegister(db, { ...filters, product_id, category_ids: ids });
    assert.deepEqual(product, multi.filter(row => String(row.item_id) === product_id));
    await assert.rejects(service.getStockRegister(db, { ...filters, category_ids: '1 OR 1=1' }), error => error.status === 400);
    let response;
    await controller.getStockRegister({ dbPool: db, query: { ...filters, category_ids: ids.join(',') } },
      { json: value => { response = value; } }, error => { throw error; });
    assert.deepEqual(response.data, multi);
    const chunks = [];
    const res = new Writable({ write(chunk, encoding, callback) { chunks.push(chunk); callback(); } });
    res.setHeader = () => {};
    await controller.exportExcel({ dbPool: db, query: { ...filters, category_ids: ids.join(',') } }, res, error => { throw error; });
    const workbook = new ExcelJS.Workbook();
    await workbook.xlsx.load(Buffer.concat(chunks));
    const sheet = workbook.getWorksheet('Stock Register');
    assert.equal(sheet.rowCount - 2, multi.length);
    multi.forEach((row, index) => {
      const cells = sheet.getRow(index + 3);
      assert.equal(cells.getCell(2).value, row.item_id);
      assert.equal(cells.getCell(5).value, row.category_name);
      assert.equal(cells.getCell(9).value, Number(row.closing_stock));
    });
    console.log(JSON.stringify({ result: 'PASS (SELECT only)', date: latest.date, categories: ids, rows: multi.length, excelRows: sheet.rowCount - 2 }));
  } finally {
    await db.close();
    await centralSequelize.close();
  }
})().catch(error => { console.error(error); process.exitCode = 1; });
