// Read-only integration check. Production INSERT/UPDATE/DELETE are never executed.
require('../src/config/environment');
const assert = require('node:assert/strict');
const { Sequelize, QueryTypes } = require('sequelize');
const repository = require('../src/modules/grnInspection/grnInspection.repository');
const create = require('../src/modules/grnInspection/grnInspection.create');

async function verify(database) {
  const db = new Sequelize(database, process.env.DB_USER || 'root', process.env.DB_PASSWORD || '', {
    host: process.env.DB_HOST, dialect: 'mysql', logging: false
  });
  const select = (sql, replacements = {}) => db.query(sql, { replacements, type: QueryTypes.SELECT });
  try {
    const latest = await select(`SELECT po.id,po.purchaseorder_id,po.postatus FROM st_purchaseorder po
      WHERE po.status!='N' AND po.id=(SELECT MAX(p.id) FROM st_purchaseorder p
        WHERE p.purchaseorder_id=po.purchaseorder_id AND p.status!='N')
        AND po.postatus!='C' ORDER BY po.id DESC LIMIT 8`);
    const numbers = [...new Set(['2627-1', ...latest.map(po => po.purchaseorder_id)])];
    let simulated = 0;
    for (const number of numbers) {
      const [legacyPo] = await select("SELECT * FROM st_purchaseorder WHERE purchaseorder_id=:number AND status!='N' ORDER BY id DESC LIMIT 1", {number});
      const result = await repository.getPoDetails(db, number);
      if (!legacyPo || legacyPo.postatus === 'C') {
        assert.equal(result, null);
        console.log(JSON.stringify({database,number,result:legacyPo ? 'closed; correctly blocked' : 'missing'}));
        continue;
      }
      assert.equal(result.po.id, legacyPo.id);
      const legacyItems = await select(`SELECT d.*,i.item_name,i.size_id,s.size_name,t.tax
        FROM st_purchaseorderDetails d INNER JOIN st_additem i ON i.id=d.item_id
        LEFT JOIN st_sizemanager s ON s.id=i.size_id LEFT JOIN st_taxmaster t ON t.id=d.tax_id
        WHERE d.purchaseorder_id=:number AND d.poprimary_id=:id ORDER BY d.id ASC`, {number,id:legacyPo.id});
      assert.equal(result.items.length, legacyItems.length);
      for (let index = 0; index < legacyItems.length; index++) {
        const expected = legacyItems[index], actual = result.items[index];
        assert.equal(actual.id, expected.id);
        assert.equal(actual.item_name, Number(expected.size_id) === 6 ? expected.item_name : `${expected.item_name}-${expected.size_name || ''}`);
        for (const [api, stored] of [['order_qty','item_qty'],['rate','item_amt'],['order_base','item_base_price'],['order_tax','item_tax_amt'],['order_amount','item_total_amount']]) {
          assert.equal(Number(actual[api]), Number(expected[stored]));
        }
        assert.equal(actual.uom, expected.uom ?? '--');
        assert.equal(Number(actual.tax_rate), Number(expected.tax || 0));
        const [receipt] = await select("SELECT ROUND(SUM(quantity),2) AS qty FROM st_stock_register WHERE po_id=:number AND item_id=:item AND store_type='1' AND status!='N'", {number,item:expected.item_id});
        assert.equal(Number(actual.pending_qty), Number(expected.item_qty) - Number(receipt.qty || 0));
        const [schedule] = await select("SELECT id,item_qty FROM po_delivery_note WHERE po_id=:number AND item_id=:item AND status='Y' ORDER BY delivery_date ASC,id ASC LIMIT 1", {number,item:expected.item_id});
        assert.equal(Number(actual.received_qty), Number(schedule?.item_qty || 0));
        assert.equal(actual.delivery_schedule_id, schedule?.id ?? null);
      }
      // Exercise the actual submission function with SELECTs against existing data,
      // but intercept every mutation in memory before it reaches Sequelize.
      const writes = [];
      const simulationDb = {
        getDatabaseName: () => database,
        transaction: async fn => fn({}),
        query: async (sql, options) => {
          if (/^INSERT\b/.test(sql)) { writes.push({sql,values:options.replacements}); return [999999, 1]; }
          if (sql.includes('GET_LOCK')) return [{acquired:1}];
          if (sql.includes('RELEASE_LOCK')) return [{released:1}];
          assert.match(sql, /^SELECT\b/, 'Only SELECT can reach the database');
          return select(sql.replace(/ FOR UPDATE/g, ''), options.replacements);
        }
      };
      let submission = 'no available quantity';
      for (const item of result.items) {
        const [prior] = await select("SELECT SUM(quantity) AS qty FROM grn_inspection_details WHERE purchaseorder_id=:number AND item_id=:item AND status!='N'", {number,item:item.item_id});
        const available = Math.min(Number(item.pending_qty), Number(item.order_qty) - Number(prior.qty || 0));
        if (available <= 0) continue;
        await create(simulationDb, {po_id:number,vendor_id:result.po.vendor_id,inwarddate:'2026-10-09',bill_date:'2026-10-09',bill_no:'READ-ONLY-CHECK',remark:'Read-only verification'}, [{item_id:item.item_id,quantity:Math.min(1,available),delivery_schedule_id:item.delivery_schedule_id}]);
        assert.equal(writes.length, 2);
        assert.ok(writes[1].values.quantity > 0);
        simulated++;
        submission = 'passed with writes intercepted in memory';
        break;
      }
      console.log(JSON.stringify({database,number,revisionId:result.po.id,items:result.items.map(item=>({name:item.item_name,quantity:item.order_qty,pending:item.pending_qty,received:item.received_qty,uom:item.uom,rate:item.rate,tax:item.tax_rate})),submission}));
    }
    console.log(JSON.stringify({database,ordersChecked:numbers.length,simulatedSubmissions:simulated}));
  } finally { await db.close(); }
}
(async () => {
  for (const database of process.argv.slice(2)) await verify(database);
})().catch(error => { console.error(error); process.exitCode = 1; });
