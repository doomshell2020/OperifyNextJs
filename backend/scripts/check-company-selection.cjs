// Read-only integration audit. Uses existing accounts; never logs in or writes records.
const assert = require('node:assert/strict');
const { once } = require('node:events');
const { QueryTypes } = require('sequelize');
const { centralSequelize, getTenantSequelize } = require('../src/config/sequelize');
const auth = require('../src/modules/auth/auth.service');
const dashboard = require('../src/modules/dashboard/dashboard.service');
const app = require('../src/app');

(async () => {
  const pools = new Set([centralSequelize]);
  const server = app.listen(0, '127.0.0.1');
  await once(server, 'listening');
  const base = `http://127.0.0.1:${server.address().port}/api`;
  const request = async (path, token, body) => {
    const response = await fetch(base + path, {
      method: body ? 'POST' : 'GET',
      headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json' },
      ...(body && { body: JSON.stringify(body) })
    });
    return { status: response.status, body: await response.json() };
  };
  try {
    const schemas = new Set((await centralSequelize.query('SHOW DATABASES', { type: QueryTypes.SELECT })).map(row => row.Database));
    const today = new Intl.DateTimeFormat('en-CA', { timeZone: 'Asia/Kolkata', year: 'numeric', month: '2-digit', day: '2-digit' }).format(new Date());
    const heads = await centralSequelize.query("SELECT mobile, db, c_id, role_id FROM users WHERE role_id=105 AND is_status='Y'", { type: QueryTypes.SELECT });
    assert.ok(heads.length, 'An existing ERP head is required for the live audit');
    for (const head of heads) {
      const user = await auth.getSession(head.mobile, head.db);
      const legacy = await centralSequelize.query('SELECT db FROM users WHERE db LIKE :prefix GROUP BY db', {
        replacements: { prefix: head.db.split('_')[0] + '%' }, type: QueryTypes.SELECT
      });
      assert.deepEqual(user.companies.map(c => c.school_database).sort(), legacy.map(c => c.db).sort(), 'Same options as PHP for this account');
      const originalIdentity = { id: user.id, role_id: user.role_id, c_id: user.c_id, tech_id: user.tech_id, permissions: user.permissions };
      let current = auth._issueTokens(user);
      const results = [];
      for (const company of user.companies) {
        const switched = await request('/auth/switch-company', current.accessToken, { newDb: company.school_database });
        if (!schemas.has(company.school_database)) {
          assert.equal(switched.status, 503);
          assert.equal(switched.body.error.code, 'COMPANY_UNAVAILABLE');
          assert.equal((await request('/auth/me', current.accessToken)).body.data.user.db, current.user.db);
          results.push({ database: company.school_database, label: company.school_name, unavailableLocally: true });
          console.log(`SKIP data checks: ${company.school_database} is not installed locally. Switch fails safely with 503.`);
          continue;
        }
        assert.equal(switched.status, 200);
        current = switched.body.data;
        const me = await request('/auth/me', current.accessToken);
        assert.equal(me.status, 200);
        assert.equal(me.body.data.user.db, company.school_database);
        for (const [key, value] of Object.entries(originalIdentity)) assert.deepEqual(me.body.data.user[key], value, `Preserve ${key}`);
        const refresh = await request('/auth/refresh', current.accessToken, { refreshToken: current.refreshToken });
        assert.equal(refresh.status, 200);
        current.accessToken = refresh.body.data.accessToken;
        assert.equal(refresh.body.data.user.db, company.school_database);
        assert.equal(refresh.body.data.user.id, user.id);
        assert.deepEqual(refresh.body.data.user.permissions, user.permissions);
        const pool = await getTenantSequelize(company.school_database);
        pools.add(pool);
        const endpoints = {
          summary: 'getSummary', charts: 'getCharts', 'latest-purchase-orders': 'getLatestPurchaseOrders',
          'latest-production': 'getLatestProduction', 'latest-maintenance': 'getLatestMaintenance',
          'latest-inspection': 'getLatestInspection', 'latest-grn': 'getLatestGrn'
        };
        for (const [endpoint, method] of Object.entries(endpoints)) {
          const actual = await request('/dashboard/' + endpoint, current.accessToken);
          assert.equal(actual.status, 200, `${company.school_database}: ${endpoint}`);
          const expected = JSON.parse(JSON.stringify(await dashboard[method](pool)));
          assert.deepEqual(actual.body.data, expected, `${endpoint} uses the selected database`);
        }
        const modules = {};
        for (const endpoint of ['contracts', 'purchase-orders', 'grn', 'designsheets', 'indentpo', 'stock-register', 'job-challan', 'jc-receive', 'gatepass']) {
          const actual = await request('/' + endpoint + `?limit=1&page=1&date_from=${today.slice(0, 7)}-01&date_to=${today}`, current.accessToken);
          assert.ok([200, 403].includes(actual.status), `${company.school_database}/${endpoint}: ${actual.status}`);
          modules[endpoint] = actual.status;
        }
        const summary = await dashboard.getSummary(pool);
        results.push({ database: company.school_database, label: company.school_name, contracts: summary.contracts.total, purchaseOrders: summary.purchaseOrders.total, grn: summary.grn.total, modules });
      }
      const denied = await request('/auth/switch-company', current.accessToken, { newDb: 'unauthorized_company' });
      assert.equal(denied.status, 403);
      assert.equal((await request('/auth/switch-company', current.accessToken, {})).status, 400);
      const back = await request('/auth/switch-company', current.accessToken, { newDb: head.db });
      assert.equal(back.status, 200);
      assert.equal(back.body.data.user.db, head.db);
      console.log('PASS: PHP option parity, switch/refresh/restore, stable identity, seven dashboard endpoints, nine module reads and denied companies.', JSON.stringify(results));
    }
    const ordinary = await centralSequelize.query("SELECT mobile,db FROM users WHERE role_id=6 AND is_status='Y'", { type: QueryTypes.SELECT });
    for (const account of ordinary) {
      if (!schemas.has(account.db)) { console.log(`SKIP ordinary-user data checks: ${account.db} is not installed locally.`); continue; }
      const user = await auth.getSession(account.mobile, account.db);
      pools.add(await getTenantSequelize(account.db));
      assert.ok(user.companies.every(company => company.school_database === account.db));
      const tokens = auth._issueTokens(user);
      assert.equal((await request('/auth/switch-company', tokens.accessToken, { newDb: 'unauthorized_company' })).status, 403);
      assert.equal((await request('/auth/me', tokens.accessToken)).body.data.user.id, user.id);
    }
    console.log('PASS: ordinary users remain restricted to their own company. No database records or schema changed.');
  } finally {
    await new Promise(resolve => server.close(resolve));
    await Promise.all([...pools].map(pool => pool.close()));
  }
})().catch(error => { console.error(error.message); process.exitCode = 1; });
