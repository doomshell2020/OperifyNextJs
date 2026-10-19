// Read-only integration verification using real tenant data and the actual PHP method.
require('../src/config/environment');
const assert = require('node:assert/strict');
const { spawnSync } = require('node:child_process');
const path = require('node:path');
const { Writable } = require('node:stream');
const ExcelJS = require('exceljs');
const { QueryTypes } = require('sequelize');
const { getTenantSequelize, centralSequelize } = require('../src/config/sequelize');
const service = require('../src/modules/stockRegister/stockRegister.service');
const controller = require('../src/modules/stockRegister/stockRegister.controller');

(async () => {
  const db = await getTenantSequelize(process.env.STOCK_TEST_DB || 'tirupati_tppl');
  const originalQuery = db.query.bind(db);
  let queries = 0;
  db.query = (sql, options) => {
    assert.match(sql.trim(), /^(SELECT|SHOW)\b/i, 'Database access must be read-only');
    queries++;
    return originalQuery(sql, options);
  };
  const select = (sql, replacements = {}) => db.query(sql, { replacements, type: QueryTypes.SELECT });
  const addDays = (value, days) => new Date(Date.parse(value) + days * 86400000).toISOString().slice(0, 10);
  try {
    const options = await service.getRegisterOptions(db);
    assert.deepEqual(options.categories, await select('SELECT id,category_name FROM st_categorymaster ORDER BY id'));
    const active = await select("SELECT id FROM st_additem WHERE status='Y' ORDER BY id");
    assert.deepEqual(options.products.map(p => p.id), active.map(p => p.id));
    const samples = await select(`SELECT sr.store_type,MAX(CASE WHEN sr.store_type IN ('0','1','3')
      THEN DATE(COALESCE(sr.delivery_date,sr.issue_date,sr.created)) ELSE DATE(COALESCE(sr.issue_date,sr.created)) END) AS date
      FROM st_stock_register sr JOIN st_additem p ON p.id=sr.item_id
      WHERE sr.status!='N' AND p.itemtype='RawMaterial' AND p.status='Y' AND p.category_id!=25
      AND sr.store_type IN ('0','1','2','3','4') GROUP BY sr.store_type`);
    assert.ok(samples.length, 'Existing stock transactions are required');
    const categories = options.categories.map(c => String(c.id));
    const cases = [];
    for (const sample of samples) {
      if (!sample.date) continue;
      cases.push({ date_from: sample.date, date_to: sample.date, category_ids: categories });
    }
    const date = cases[0].date_from;
    const candidates = await service.getStockRegister(db, cases[0]);
    assert.ok(candidates.length);
    const first = candidates[0];
    const [category] = await select('SELECT category_id FROM st_additem WHERE id=:id', { id: first.item_id });
    cases.push({ date_from: date, date_to: date, category_ids: [String(category.category_id)] });
    cases.push({ date_from: date, date_to: date, category_ids: ['25'] });
    cases.push({ date_from: date, date_to: addDays(date, 31), product_id: String(first.item_id), category_ids: ['25'] });
    cases.push({ date_from: '1900-01-01', date_to: '1900-01-01', category_ids: categories });
    let comparedRows = 0;
    let largest = 0;
    const started = Date.now();
    for (const filters of cases) {
      const params = {};
      let where = "p.itemtype='RawMaterial' AND p.status='Y' AND p.category_id NOT IN (25)";
      if (filters.product_id) { where += ' AND p.id=:id'; params.id = filters.product_id; }
      else { where += ' AND p.category_id IN (:categories)'; params.categories = filters.category_ids; }
      const products = await select(`SELECT p.id,p.item_name,p.item_isbn,c.category_name,u.unit_name AS unit
        FROM st_additem p LEFT JOIN st_categorymaster c ON c.id=p.category_id
        LEFT JOIN st_measurementunits u ON u.id=p.uom WHERE ${where} ORDER BY p.item_name ASC`, params);
      const movements = products.length ? await select(`SELECT item_id,store_type,
        DATE(COALESCE(delivery_date,issue_date,created)) AS grn_date,
        DATE(COALESCE(issue_date,created)) AS indent_date,SUM(quantity) AS total_qty
        FROM st_stock_register WHERE status!='N' AND item_id IN (:ids)
        GROUP BY item_id,store_type,grn_date,indent_date`, { ids: products.map(p => p.id) }) : [];
      const php = spawnSync(process.env.PHP_BINARY || 'php', [path.join(__dirname, 'stock-register-legacy-reference.php')], {
        input: JSON.stringify({ products, movements, from: filters.date_from, to: filters.date_to }),
        encoding: 'utf8', maxBuffer: 64 * 1024 * 1024
      });
      if (php.error) throw php.error;
      assert.equal(php.status, 0, php.stderr);
      const reference = JSON.parse(php.stdout).map(row => ({
        item_id: row.item_id, product_code: row.product_code, item_name: row.product_name,
        category_name: row.category, unit: row.unit, date_range: row.date,
        opening_stock: row.opening.toFixed(2), received_stock: row.received.toFixed(2),
        dispatched_stock: row.dispatched.toFixed(2), closing_stock: row.closing.toFixed(2)
      }));
      const rows = await service.getStockRegister(db, filters);
      assert.deepEqual(rows, reference, `PHP parity: ${JSON.stringify(filters)}`);
      comparedRows += rows.length;
      largest = Math.max(largest, rows.length);
      let response;
      await controller.getStockRegister({ dbPool: db, query: filters }, { json: data => { response = data; } }, e => { throw e; });
      assert.deepEqual(response.data, rows);
      const chunks = [];
      const headers = {};
      const res = new Writable({ write(chunk, encoding, callback) { chunks.push(chunk); callback(); } });
      res.setHeader = (name, value) => { headers[name] = value; };
      await controller.exportExcel({ dbPool: db, query: filters }, res, e => { throw e; });
      const workbook = new ExcelJS.Workbook();
      await workbook.xlsx.load(Buffer.concat(chunks));
      const sheet = workbook.getWorksheet('Stock Register');
      const productMode = !!filters.product_id;
      const columns = ['#','Item ID',productMode ? 'DATE' : 'Date',
        ...(!productMode ? ['Product Name','Category'] : []),
        'Opening Stock','Received Stock','Dispatched Stock','Closing Stock',...(!productMode ? ['Physical Stock'] : [])];
      assert.deepEqual(sheet.getRow(2).values.slice(1), columns);
      assert.equal(sheet.rowCount - 2, rows.length);
      assert.match(headers['Content-Disposition'], /\.xlsx"$/);
      rows.forEach((row, index) => {
        const cells = sheet.getRow(index + 3);
        const [year, month, day] = row.date_range.split('-');
        assert.equal(cells.getCell(1).value, index + 1);
        assert.equal(cells.getCell(2).value, row.item_id);
        assert.equal(cells.getCell(3).value, `${day}-${month}-${year.slice(-2)}`);
        if (!productMode) {
          assert.equal(cells.getCell(4).value, row.item_name);
          assert.equal(cells.getCell(5).value, row.category_name);
          assert.ok(!cells.getCell(10).value);
        }
        ['opening_stock','received_stock','dispatched_stock','closing_stock'].forEach((key, offset) => {
          const cell = cells.getCell((productMode ? 4 : 6) + offset);
          assert.equal(cell.value, Number(row[key]));
          assert.equal(cell.numFmt, '0.00');
        });
      });
    }
    const invalidFilters = [
      {}, { date_from: '2026-02-30', date_to: date }, { date_from: date, date_to: addDays(date, -1) },
      { date_from: date, date_to: date },
      { date_from: date, date_to: addDays(date, 1), category_ids: categories },
      { date_from: date, date_to: addDays(date, 32), product_id: String(first.item_id) },
      { date_from: date, date_to: date, product_id: '1 OR 1=1' },
      { date_from: date, date_to: date, category_ids: ['bad'] }
    ];
    for (const filters of invalidFilters) {
      const before = queries;
      await assert.rejects(service.getStockRegister(db, filters), e => e.status === 400);
      await assert.rejects(service.exportExcel(db, filters, {}), e => e.status === 400);
      assert.equal(queries, before, 'Invalid filters must not query the database');
    }
    console.log(JSON.stringify({ result: 'PASS (SELECT only)', php: 'Original _getConsolidatedStock method',
      cases: cases.length, transactionTypes: samples.map(s => s.store_type), comparedRows,
      largestReport: largest, invalidFilters: invalidFilters.length, elapsedMs: Date.now() - started }));
  } finally { await db.close(); await centralSequelize.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
