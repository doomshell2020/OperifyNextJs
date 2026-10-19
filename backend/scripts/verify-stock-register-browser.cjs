// Real-session HTTP/browser verification. No login, saves, or database mutations.
require('../src/config/environment');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const express = require('express');
const { QueryTypes } = require('sequelize');
const config = require('../src/config/sequelize');
const auth = require('../src/modules/auth/auth.service');
const service = require('../src/modules/stockRegister/stockRegister.service');

(async () => {
  const name = process.env.STOCK_TEST_DB || 'tirupati_tppl';
  const db = await config.getTenantSequelize(name);
  for (const pool of [db, config.centralSequelize]) {
    const query = pool.query.bind(pool);
    pool.query = (sql, options) => {
      assert.match(sql.trim(), /^(SELECT|SHOW)\b/i, 'Read-only verification');
      return query(sql, options);
    };
  }
  let server, browser;
  try {
    const users = await config.centralSequelize.query("SELECT mobile FROM users WHERE db=:name AND is_status!='N'", { replacements: { name }, type: QueryTypes.SELECT });
    let session;
    for (const user of users) {
      try {
        const existing = await auth.getSession(user.mobile, name);
        if (existing.permissions.includes('legacy:admin/stockregister/index')) { session = auth._issueTokens(existing); break; }
      } catch { /* Try another existing active account. */ }
    }
    assert.ok(session, 'An existing Stock Register reader is required');
    let mutations = 0;
    const app = express();
    app.use((req, res, next) => { if (req.method !== 'GET') { mutations++; return res.sendStatus(405); } next(); });
    app.use(require('../src/app'));
    server = await new Promise(resolve => { const listener = app.listen(0, '127.0.0.1', () => resolve(listener)); });
    const base = `http://127.0.0.1:${server.address().port}`;
    const headers = { Authorization: `Bearer ${session.accessToken}` };
    assert.equal((await fetch(`${base}/api/stock-register/options`)).status, 401);
    assert.equal((await fetch(`${base}/api/stock-register/options`, { headers })).status, 200);
    const options = await service.getRegisterOptions(db);
    const [latest] = await db.query("SELECT MAX(DATE(COALESCE(delivery_date,issue_date,created))) AS date FROM st_stock_register WHERE status!='N'", { type: QueryTypes.SELECT });
    const filters = { date_from: latest.date, date_to: latest.date, category_ids: options.categories.map(c => String(c.id)) };
    const rows = await service.getStockRegister(db, filters);
    assert.ok(rows.length);
    const invalid = await fetch(`${base}/api/stock-register?date_from=${latest.date}&date_to=${latest.date}`, { headers });
    assert.equal(invalid.status, 400);
    browser = await require('../src/utils/pdfBrowser').launchPdfBrowser();
    const page = await browser.newPage();
    await page.setViewport({ width: 1440, height: 1000 });
    await page.evaluateOnNewDocument(({ accessToken, refreshToken }) => {
      localStorage.setItem('accessToken', accessToken); localStorage.setItem('refreshToken', refreshToken);
    }, session);
    const failures = [], exports = [];
    page.on('pageerror', error => failures.push(error.message));
    await page.setRequestInterception(true);
    page.on('request', async request => {
      try {
        const url = request.url();
        if (!url.includes('/api/')) return request.continue();
        if (request.method() === 'OPTIONS') return request.respond({ status: 204, headers: { 'Access-Control-Allow-Origin': '*', 'Access-Control-Allow-Headers': '*' } });
        if (request.method() !== 'GET') { mutations++; return request.respond({ status: 405 }); }
        if (url.includes('/stock-register/export')) exports.push(new URL(url));
        const response = await fetch(base + url.slice(url.indexOf('/api/')), { headers });
        if (response.status >= 500) failures.push(`${response.status}: ${new URL(url).pathname}`);
        return request.respond({ status: response.status, headers: {
          'Access-Control-Allow-Origin': '*', 'Access-Control-Expose-Headers': 'Content-Disposition',
          'Content-Type': response.headers.get('content-type') || 'application/json',
          ...(response.headers.get('content-disposition') ? { 'Content-Disposition': response.headers.get('content-disposition') } : {})
        }, body: Buffer.from(await response.arrayBuffer()) });
      } catch (error) { failures.push(error.message); await request.abort().catch(() => {}); }
    });
    await page.goto((process.env.STOCK_TEST_FRONTEND || 'http://127.0.0.1:3224') + '/dashboard/inventory/stock', { waitUntil: 'networkidle0', timeout: 120000 });
    await page.waitForSelector('.stock-register-page');
    assert.equal(await page.$$eval('table tbody tr', elements => elements.length), 1);
    assert.ok((await page.$eval('table tbody', element => element.innerText)).includes('No Record Found'));
    assert.equal(await page.$('[aria-label="Export Summary Excel"]'), null);
    await page.$eval('input[data-date-native][aria-label="Date From"]', (input, value) => {
      Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'value').set.call(input, value);
      input.dispatchEvent(new Event('input', { bubbles: true })); input.dispatchEvent(new Event('change', { bubbles: true }));
    }, latest.date);
    await page.waitForFunction(value => document.querySelector('input[data-date-native][aria-label="Date To"]').value === value, {}, latest.date);
    await page.click('.stock-category-dropdown summary');
    await page.click('.stock-category-all input');
    const search = async () => {
      const response = page.waitForResponse(r => r.request().method() === 'GET' && /\/api\/stock-register\?/.test(r.url()));
      await page.$$eval('.stock-register-actions button', buttons => buttons.find(b => b.textContent.trim() === 'Search').click());
      assert.equal((await response).status(), 200);
    };
    await search();
    await page.waitForFunction(count => document.querySelectorAll('table tbody tr').length === count, {}, rows.length);
    assert.equal(await page.$$eval('table thead th', elements => elements.length), 8);
    const first = await page.$$eval('table tbody tr:first-child td', cells => cells.map(cell => cell.innerText));
    const visibleText = value => value.trim().replace(/\s+/g, ' ');
    assert.equal(first[2], visibleText(rows[0].item_name));
    assert.equal(first[3], visibleText(rows[0].category_name));
    assert.match(first[1], /^\d{2}-\d{2}-\d{2}$/);
    // Editing unsubmitted controls must not change the displayed report's export.
    await page.click('.stock-category-list fieldset input');
    const exported = page.waitForResponse(r => r.request().method() === 'GET' && r.url().includes('/stock-register/export'));
    await page.click('[aria-label="Export Summary Excel"]');
    assert.equal((await exported).status(), 200);
    assert.equal(exports[0].searchParams.get('category_ids'), filters.category_ids.join(','));
    const folder = path.resolve(__dirname, '../../tmp/stock-register-verification');
    fs.mkdirSync(folder, { recursive: true });
    await page.screenshot({ path: path.join(folder, 'consolidated.png'), fullPage: true });
    await page.setViewport({ width: 390, height: 844 });
    assert.ok(await page.$eval('.stock-register-results', el => el.clientWidth <= window.innerWidth));
    assert.ok(await page.$eval('.stock-register-results', el => el.scrollWidth > el.clientWidth));
    await page.screenshot({ path: path.join(folder, 'mobile.png') });
    await page.$$eval('.stock-register-actions button', buttons => buttons.find(b => b.textContent.trim() === 'Reset').click());
    await page.waitForFunction(() => document.querySelector('table tbody').innerText.includes('No Record Found'));
    assert.equal(await page.$$eval('table thead th', elements => elements.length), 6);
    await page.setViewport({ width: 1440, height: 1000 });
    const product = options.products.find(p => p.id === rows[0].item_id);
    await page.type('input[placeholder="Enter Item Name (Optional)"]', product.item_name);
    await page.$$eval('.stock-register-filters > div:first-child .overflow-y-auto > div', (elements, label) => {
      elements.find(el => el.textContent.trim() === label.trim()).click();
    }, product.item_name);
    const to = new Date(Date.parse(latest.date) + 31 * 86400000).toISOString().slice(0, 10);
    for (const [label, value] of [['Date From', latest.date], ['Date To', to]]) {
      await page.$eval(`input[data-date-native][aria-label="${label}"]`, (input, date) => {
        Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'value').set.call(input, date);
        input.dispatchEvent(new Event('input', { bubbles: true })); input.dispatchEvent(new Event('change', { bubbles: true }));
      }, value);
    }
    const productRows = await service.getStockRegister(db, { date_from: latest.date, date_to: to, product_id: String(product.id) });
    await search();
    await page.waitForFunction(count => document.querySelectorAll('table tbody tr').length === count, {}, productRows.length);
    assert.equal(await page.$$eval('table thead th', elements => elements.length), 6);
    assert.equal(await page.$eval('input[data-date-native][aria-label="Date To"]', input => input.max), to);
    await page.screenshot({ path: path.join(folder, 'product.png'), fullPage: true });
    assert.equal(mutations, 0);
    assert.deepEqual(failures, []);
    console.log(JSON.stringify({ result: 'PASS (SELECT/GET only)', reportRows: rows.length,
      checks: 'real session, authentication, options, HTTP validation, initial empty state, date lock, All Category, consolidated/product columns, 31-day product range, DD-MM-YY, export applied filters, reset, mobile overflow', screenshots: folder }));
  } finally {
    if (browser) await browser.close();
    if (server) await new Promise(resolve => server.close(resolve));
    await db.close(); await config.centralSequelize.close();
  }
})().catch(error => { console.error(error); process.exitCode = 1; });
