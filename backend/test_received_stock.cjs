// Read-only regression check against existing tenant records and the CakePHP joins.
require('./src/config/environment');
const assert = require('node:assert/strict');
const { QueryTypes } = require('sequelize');
const { getTenantSequelize, centralSequelize } = require('./src/config/sequelize');
const repository = require('./src/modules/stockRegister/stockRegister.repository');
const service = require('./src/modules/stockRegister/stockRegister.service');
const controller = require('./src/modules/stockRegister/stockRegister.controller');
const purchaseOrders = require('./src/modules/purchaseOrder/purchaseOrder.repository');

(async () => {
  const db = await getTenantSequelize(process.env.STOCK_TEST_DB || 'tirupati_tppl');
  try {
    const samples = await db.query(`SELECT sr.item_id, DATE(COALESCE(sr.delivery_date,sr.issue_date,sr.created)) AS date
      FROM st_stock_register sr INNER JOIN st_purchaseorder po ON po.id=sr.purchaseorder_id
      INNER JOIN st_goodsreceive gr ON gr.id=sr.goods_id
      WHERE sr.status!='N' AND sr.store_type='1'
      GROUP BY sr.item_id,date ORDER BY MAX(po.is_revised) DESC,MAX(sr.id) DESC LIMIT 20`, { type: QueryTypes.SELECT });
    assert.ok(samples.length, 'Existing receipts must be available for validation');
    let receipts = 0, revised = 0, legacyDateDifferences = 0, billDateDifferences = 0;
    for (const sample of samples) {
      const date = sample.date instanceof Date ? [sample.date.getFullYear(), String(sample.date.getMonth()+1).padStart(2,'0'), String(sample.date.getDate()).padStart(2,'0')].join('-') : sample.date;
      const filters = { product_id: String(sample.item_id), date };
      const actual = await service.getReceivedStockDetails(db, filters);
      const expected = await db.query(`SELECT sr.id,sr.po_id,po.id AS purchaseorder_id,po.added_time AS po_date,
        po.is_revised,v.name AS vendor_name,v.contact_no,v.email,gr.inwarddate,gr.bill_no,gr.bill_date,sr.quantity
        FROM st_stock_register sr LEFT JOIN st_goodsreceive gr ON gr.id=sr.goods_id
        LEFT JOIN st_purchaseorder po ON po.id=sr.purchaseorder_id LEFT JOIN vendors v ON v.id=gr.vendor_id
        WHERE sr.item_id=:product_id AND sr.status!='N' AND sr.store_type IN ('0','1','3')
        AND DATE(COALESCE(sr.delivery_date,sr.issue_date,sr.created))=:date ORDER BY sr.id DESC`,
      { replacements: filters, type: QueryTypes.SELECT });
      assert.deepEqual(actual.map(row => Object.fromEntries(Object.keys(expected[0]).map(key => [key,row[key]]))), expected);
      const summary = await repository.getStockRegister(db, { product_id: filters.product_id, date_from:date,date_to:date });
      if (summary.length) assert.equal(Number(summary[0].received_stock), Math.round(actual.reduce((sum,row)=>sum+Number(row.quantity),0)*100)/100);
      const legacy = await db.query(`SELECT sr.id FROM st_stock_register sr INNER JOIN st_goodsreceive gr ON gr.id=sr.goods_id
        INNER JOIN st_purchaseorder po ON po.id=sr.purchaseorder_id WHERE sr.item_id=:product_id AND sr.status!='N'
        AND sr.store_type='1' AND DATE(sr.created)=:date ORDER BY sr.id DESC`, { replacements:filters,type:QueryTypes.SELECT });
      if (JSON.stringify(legacy.map(row=>row.id)) !== JSON.stringify(actual.filter(row=>String(row.store_type)==='1' && row.purchaseorder_id).map(row=>row.id))) legacyDateDifferences++;
      receipts += actual.length;
      revised += actual.filter(row=>Number(row.is_revised)>0).length;
      billDateDifferences += actual.filter(row=>row.bill_date && row.inwarddate && String(row.bill_date)!==String(row.inwarddate)).length;
      assert.ok(await repository.getReceivedStockProduct(db, filters.product_id));
      // Exercise the real controller envelope consumed by the new route.
      let response;
      await controller.getReceivedStockDetails({dbPool:db,query:filters}, {
        json(value) { response=value; }
      }, error=>{throw error;});
      assert.deepEqual(response.data,actual);
      assert.equal(Number(response.product.id),Number(filters.product_id));
      for (const row of actual.filter(row=>row.purchaseorder_id)) {
        const pdfData = await purchaseOrders.getDetails(db, String(row.purchaseorder_id));
        assert.equal(Number(pdfData.po.id),Number(row.purchaseorder_id));
        assert.equal(Number(pdfData.po.amendment_no),Number(row.is_revised));
      }
    }
    for (const filters of [{date:'2026-02-30',product_id:'1'},{date:'bad',product_id:'1'},{date:'2026-10-12',product_id:'1 OR 1=1'}]) {
      await assert.rejects(service.getReceivedStockDetails(db,filters), error=>error.status===400);
    }
    assert.deepEqual(await service.getReceivedStockDetails(db,{date:'1900-01-01',product_id:String(samples[0].item_id)}),[]);
    console.log(JSON.stringify({ samples:samples.length, receipts, revised, legacyDateDifferences, billDateDifferences, result:'PASS (SELECT only)' }));
  } finally { await db.close(); await centralSequelize.close(); }
})().catch(error=>{console.error(error.message);process.exitCode=1;});
