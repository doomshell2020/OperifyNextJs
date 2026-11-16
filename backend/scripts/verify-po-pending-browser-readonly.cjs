// Existing users, GET requests and SELECTs only. Does not log in or save records.
require('../src/config/environment');
const assert = require('node:assert/strict');
const express = require('express');
const { QueryTypes } = require('sequelize');
const ExcelJS = require('exceljs');
const config = require('../src/config/sequelize');
const auth = require('../src/modules/auth/auth.service');
const report = require('../src/modules/purchaseOrder/pendingReport');
let browser, server;
const pools = [];
(async () => {
  try {
    const sessions = {};
    for (const name of ['tirupati_tppl', 'tirupati_kcpl']) {
      const db = await config.getTenantSequelize(name); pools.push(db);
      for (const pool of [config.centralSequelize, db]) {
        if (pool.pendingGuard) continue;
        const query = pool.query.bind(pool);
        pool.query = (sql, options) => {
          assert.match(typeof sql === 'string' ? sql.trim() : sql.query.trim(), /^(SELECT|SHOW|WITH|SET TRANSACTION|START TRANSACTION|COMMIT|ROLLBACK)\b/i, 'No database mutations');
          return query(sql, options);
        };
        pool.pendingGuard = true;
      }
      const users = await config.centralSequelize.query("SELECT mobile FROM users WHERE db=:name AND is_status!='N'", { replacements: { name }, type: QueryTypes.SELECT });
      for (const user of users) {
        try {
          const existing = await auth.getSession(user.mobile, name);
          if (existing.permissions.includes('legacy:admin/purchaseorder/index')) { sessions[name] = auth._issueTokens(existing); break; }
        } catch { /* Read another existing user. */ }
      }
      assert.ok(sessions[name], `Existing PO reader required: ${name}`);
    }
    let mutations = 0;
    const app = express();
    app.use((req,res,next) => { if (req.method !== 'GET') { mutations++; return res.sendStatus(405); } next(); });
    app.use(require('../src/app'));
    server = await new Promise(resolve => { const s = app.listen(0, '127.0.0.1', () => resolve(s)); });
    const base = `http://127.0.0.1:${server.address().port}`;
    const endpoint = '/api/reports/po-raw-material-pending';
    for (const suffix of ['', '/options', '/export']) assert.equal((await fetch(base + endpoint + suffix)).status, 401);
    const session = sessions.tirupati_tppl;
    const headers = { Authorization: `Bearer ${session.accessToken}` };
    for (const query of ['datefrom=2026-02-30', 'datefrom=2026-10-29&dateto=2026-10-28', 'item_id=bad', 'page=0']) assert.equal((await fetch(`${base}${endpoint}?${query}`, { headers })).status, 400);
    for (const [name, entry] of Object.entries(sessions)) {
      const response = await fetch(`${base}${endpoint}?db=ignored&company=ignored`, { headers: { Authorization: `Bearer ${entry.accessToken}` } });
      assert.equal(response.status, 200);
      const actual = await response.json();
      const expected = await report.list(await config.getTenantSequelize(name), {});
      assert.deepEqual(actual.data, expected.data); assert.equal(actual.pagination.total, expected.pagination.total);
    }
    const sample = (await report.rows(pools[0], {}, 1, 0))[0];
    const filter = `item_id=${sample.item_id}&vendor_id=${sample.vendor_id}`;
    const expected = await report.rows(pools[0], report.validate({ item_id: String(sample.item_id), vendor_id: String(sample.vendor_id) }), 100000, 0);
    const exported = await fetch(`${base}${endpoint}/export?${filter}&limit=1&page=2`, { headers });
    assert.equal(exported.status, 200);
    const workbook = new ExcelJS.Workbook(); await workbook.xlsx.load(Buffer.from(await exported.arrayBuffer()));
    assert.equal(workbook.worksheets[0].rowCount, expected.length + 2);
    assert.deepEqual(workbook.worksheets[0].getRow(3).values.slice(1), report.cells(expected[0]));

    browser = await require('../src/utils/pdfBrowser').launchPdfBrowser();
    const page = await browser.newPage(); await page.setViewport({ width: 1440, height: 1000 });
    await page.evaluateOnNewDocument(tokens => { localStorage.setItem('accessToken', tokens.accessToken); localStorage.setItem('refreshToken', tokens.refreshToken); }, session);
    const errors = [], requests = [];
    let failNext = false, holdNext = false, releaseHeld, notifyHeld;
    page.on('pageerror', error => errors.push(error.message));
    await page.setRequestInterception(true);
    page.on('request', async request => {
      const url = request.url();
      try {
        if (!url.includes('/api/')) return request.continue();
        if (request.method() === 'OPTIONS') return request.respond({ status: 204, headers: { 'Access-Control-Allow-Origin': '*', 'Access-Control-Allow-Headers': '*' } });
        assert.equal(request.method(), 'GET'); requests.push(new URL(url));
        if (new URL(url).pathname === endpoint && failNext) {
          failNext = false;
          return request.respond({ status: 503, headers: { 'Access-Control-Allow-Origin': '*', 'Content-Type': 'application/json' }, body: JSON.stringify({ error: { message: 'Simulated network failure' } }) });
        }
        if (new URL(url).pathname === endpoint && holdNext) {
          holdNext = false;
          const held = new Promise(resolve => { releaseHeld = resolve; }); notifyHeld(); await held;
        }
        const response = await fetch(base + url.slice(url.indexOf('/api/')), { headers });
        await request.respond({ status: response.status, headers: { 'Access-Control-Allow-Origin': '*', 'Content-Type': response.headers.get('content-type') || 'application/json' }, body: Buffer.from(await response.arrayBuffer()) });
      } catch (error) { errors.push(error.message); await request.abort().catch(() => {}); }
    });
    await page.goto('http://127.0.0.1:3224/dashboard/reports/po-raw-material-pending', { waitUntil: 'networkidle0', timeout: 120000 });
    await page.waitForSelector('#pending-item');
    const reportCalls = () => requests.filter(url => url.pathname === endpoint);
    assert.equal(reportCalls().length, 0);
    assert.match(await page.$eval('table tbody', node => node.innerText), /Click Search to view the report/);
    assert.deepEqual(await page.$$eval('thead th', nodes => nodes.map(node => node.innerText)), report.HEADERS);
    let navigations = 0; page.on('framenavigated', frame => { if (frame === page.mainFrame()) navigations++; });
    const clickText = text => page.$$eval('button', (buttons, target) => buttons.find(button => button.textContent.trim() === target).click(), text);
    const waitReport = () => page.waitForResponse(response => response.request().method() === 'GET' && new URL(response.url()).pathname === endpoint);
    let response = waitReport(); await clickText('Search'); assert.equal((await response).status(), 200);
    await page.waitForFunction(() => document.querySelectorAll('tbody tr').length === 50);
    response = waitReport(); await clickText('Next >'); assert.equal((await response).status(), 200);
    await page.waitForFunction(() => document.querySelector('.legacy-pagination').innerText.includes('Page 2 of'));
    assert.equal(reportCalls().at(-1).searchParams.get('page'), '2');
    await clickText('Reset');
    await page.waitForFunction(() => document.querySelector('tbody').innerText.includes('Click Search'));
    const resetCount = reportCalls().length;
    // Searchable raw-material selection with the keyboard.
    await page.type('#pending-item', sample.item_name.trim()); await page.keyboard.press('ArrowDown'); await page.keyboard.press('Enter');
    response = waitReport(); await clickText('Search'); assert.equal((await response).status(), 200);
    assert.equal(reportCalls().at(-1).searchParams.get('item_id'), String(sample.item_id));
    assert.equal(reportCalls().at(-1).searchParams.get('page'), '1');
    assert.equal(reportCalls().length, resetCount + 1);
    await page.type('#pending-supplier', sample.vendor_name.trim()); await page.keyboard.press('ArrowDown'); await page.keyboard.press('Enter');
    const iso = sample.po_date.split('-').reverse().join('-');
    for (const label of ['Date From', 'Date To']) {
      await page.$eval(`input[data-date-native][aria-label="${label}"]`, (input, value) => {
        Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'value').set.call(input, value);
        input.dispatchEvent(new Event('input', { bubbles: true })); input.dispatchEvent(new Event('change', { bubbles: true }));
      }, iso);
    }
    response = waitReport(); await clickText('Search'); assert.equal((await response).status(), 200);
    assert.equal(reportCalls().at(-1).searchParams.get('vendor_id'), String(sample.vendor_id));
    assert.equal(reportCalls().at(-1).searchParams.get('datefrom'), iso); assert.equal(reportCalls().at(-1).searchParams.get('dateto'), iso);
    await page.waitForFunction(() => !document.querySelector('[aria-busy="true"]'));
    const download = page.waitForResponse(r => r.request().method() === 'GET' && new URL(r.url()).pathname === endpoint + '/export');
    await page.click('[aria-label="Export Excel"]'); assert.equal((await download).status(), 200);
    const downloadUrl = requests.findLast(url => url.pathname === endpoint + '/export');
    assert.equal(downloadUrl.searchParams.get('item_id'), String(sample.item_id)); assert.equal(downloadUrl.searchParams.get('dateto'), iso);
    await page.waitForFunction(() => ![...document.querySelectorAll('[role="status"]')].some(node => node.innerText.includes('Exporting')));
    failNext = true; response = waitReport(); await clickText('Search'); assert.equal((await response).status(), 503);
    await page.waitForFunction(() => document.querySelector('[role="alert"]')?.innerText.includes('Simulated network failure'));
    await clickText('Reset');
    // A cancelled slow request must not restore rows after Reset.
    holdNext = true; const heldStarted = new Promise(resolve => { notifyHeld = resolve; });
    await clickText('Search'); await heldStarted; await clickText('Reset'); releaseHeld();
    await page.waitForFunction(() => document.querySelector('tbody').innerText.includes('Click Search'));
    // Return to the all-record search for pagination and narrow viewport QA.
    await clickText('Reset'); response = waitReport(); await clickText('Search'); await response;
    await page.waitForFunction(() => document.querySelectorAll('tbody tr').length === 50);
    await page.screenshot({ path: require('node:path').resolve(__dirname, '../../tmp/po-pending-desktop.png'), fullPage: true });
    await page.setViewport({ width: 390, height: 844 });
    assert.ok(await page.$eval('table', table => table.parentElement.clientWidth < table.scrollWidth));
    await page.screenshot({ path: require('node:path').resolve(__dirname, '../../tmp/po-pending-mobile.png'), fullPage: true });
    assert.equal(navigations, 0); assert.equal(mutations, 0); assert.deepEqual(errors, []);
    console.log('PASS: real-session auth, invalid inputs, two-company isolation, filtered full Excel, blank initial page, headers, AJAX Search/Reset/pagination, combined supplier/item/date filters, Excel icon, network failures, cancelled stale request, page-1 reset, responsive table, no JS errors. GET/SELECT only.');
  } finally {
    if (browser) await browser.close();
    if (server) await new Promise(resolve => server.close(resolve));
    await Promise.all(pools.map(db => db.close())); await config.centralSequelize.close();
  }
})().catch(error => { console.error(error); process.exitCode = 1; });
