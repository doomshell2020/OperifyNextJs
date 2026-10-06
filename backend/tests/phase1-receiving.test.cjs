const test = require('node:test');
const assert = require('node:assert/strict');
const receive = require('../src/modules/grn/grn.receipt');
const inspect = require('../src/modules/grnInspection/grnInspection.create');
// Writes and transactions in this suite exist only in memory.
function fixture(options = {}) {
  let entries = [], status = options.status || 'Y';
  const po = {id:81,purchaseorder_id:'2627-5',vendor_id:7,total_qty:10,status:'Y',postatus:'O'};
  const item = {item_id:9,quantity:2,rate:118,cost_price:200,tax_id:8,tax:36,amount:236};
  const inspection = {id:3,inspection_id:'1001',po_id:po.purchaseorder_id,vendor_id:7,inwarddate:'2026-10-06',bill_date:'2026-10-05',bill_no:'B-1'};
  const db = {
    getDatabaseName:()=> 'memory',
    transaction: async fn => { const backup=entries.slice(); try{return await fn({});}catch(error){entries=backup;throw error;} },
    query: async (sql, args) => {
      assert.ok(args.transaction, 'Every receipt query uses its transaction');
      if (/^INSERT|^UPDATE/.test(sql)) {
        if (options.failStock && sql.includes('INSERT INTO st_stock_register')) throw Error('stock failure');
        entries.push({sql,values:args.replacements});
        if (sql.includes("UPDATE grn_inspection SET status='N'")) status='N';
        return [91,1];
      }
      if (sql.includes('GET_LOCK')) return [{acquired:1}];
      if (sql.includes('RELEASE_LOCK')) return [{released:1}];
      if (sql.startsWith('SELECT po_id FROM grn_inspection')) return [{po_id:po.purchaseorder_id}];
      if (sql.startsWith('SELECT * FROM st_purchaseorder ')) return [po];
      if (sql.startsWith('SELECT * FROM grn_inspection ')) return [{...inspection,status}];
      if (sql.startsWith('SELECT * FROM grn_inspection_details')) return options.duplicateItems ? [item,item] : [item];
      if (sql.startsWith('SELECT item_id,SUM(item_qty)')) return [{item_id:9,quantity:10}];
      if (sql.startsWith('SELECT item_id,SUM(quantity) AS quantity FROM st_stock_register')) return [{item_id:9,quantity:options.received ?? 0}];
      if (sql.startsWith('SELECT id FROM st_stock_available')) return [{id:11}];
      if (sql.startsWith('SELECT inspection_id FROM grn_inspection')) return [{inspection_id:'1000'}];
      if (sql.startsWith('SELECT id FROM grn_inspection')) return [];
      if (sql.startsWith('SELECT * FROM st_purchaseorderdetails')) return [{item_id:9,item_qty:10,item_amt:118,item_base_price:1000,item_tax_amt:180,item_total_amount:1180,tax_id:8}];
      if (sql.startsWith('SELECT item_id,SUM(quantity) AS quantity FROM grn_inspection_details')) return [{item_id:9,quantity:options.inspected ?? 0}];
      throw Error('Unexpected SQL: '+sql);
    },
    entries:()=>entries
  };
  return db;
}
const payload={inspection_id:'1001',purchaseorder_id:'2627-5',vendor_id:7,remark:'Received',items:[{item_id:9,received_qty:999,rate:999,tax_rate:99}]};
test('GRN uses saved included-tax valuation, not tampered client rows, and preserves PO identifiers',async()=>{
  const db=fixture();await receive(db,payload);
  const header=db.entries().find(row=>row.sql.includes('INSERT INTO st_goodsreceive'));
  assert.equal(header.values.totalAmt,236);assert.equal(header.values.totalTax,36);
  const stock=db.entries().find(row=>row.sql.includes('INSERT INTO st_stock_register'));
  assert.equal(stock.values.quantity,2);assert.equal(stock.values.base,200);assert.equal(stock.values.poId,81);assert.equal(stock.values.poNumber,'2627-5');
  assert.equal(db.entries().filter(row=>row.sql.includes('INSERT INTO payments')).length,1);
});
test('repeated GRN submission consumes inspection once',async()=>{const db=fixture();await receive(db,payload);const count=db.entries().length;await assert.rejects(receive(db,payload),/already been received/);assert.equal(db.entries().length,count);});
test('receipt failure rolls back header, payment and stock',async()=>{const db=fixture({failStock:true});await assert.rejects(receive(db,payload),/stock failure/);assert.equal(db.entries().length,0);});
test('all repeated inspection rows share one remaining quantity budget',async()=>{const db=fixture({received:7,duplicateItems:true});await assert.rejects(receive(db,payload),/remaining PO quantity/);assert.equal(db.entries().length,0);});
test('receipt closes revision family by displayed PO number',async()=>{const db=fixture({received:8});const result=await receive(db,payload);assert.equal(result.poStatus,'C');assert.ok(db.entries().some(row=>row.sql.includes('UPDATE st_goodsreceive')&&row.values.poNumber==='2627-5'));});
test('inspection derives valuation and vendor from PO, generates number and retains tax/base/date fields',async()=>{
  const db=fixture();const result=await inspect(db,{po_id:'2627-5',inspection_id:'garbage',vendor_id:7,inwarddate:'2026-10-06',bill_date:'2026-10-05',bill_no:'B',remark:'checked',total_amt:999},[{item_id:9,quantity:2,amount:999,tax:999}]);
  assert.equal(result.data.inspection_id,'1001');
  const detail=db.entries().find(row=>row.sql.includes('INSERT INTO grn_inspection_details'));
  assert.equal(detail.values.cost_price,200);assert.equal(detail.values.tax,36);assert.equal(detail.values.amount,236);assert.equal(detail.values.tax_id,8);assert.equal(detail.values.vendor,7);
});
test('inspection blocks repeated product rows and over-inspection before writing',async()=>{
  const header={po_id:'2627-5',inwarddate:'2026-10-06',bill_date:'2026-10-05',bill_no:'B',remark:'checked'};
  for(const [options,rows,pattern] of [[{},[{item_id:9,quantity:2},{item_id:9,quantity:2}],/already been added/],[{inspected:9},[{item_id:9,quantity:2}],/pending PO quantity/]]){
    const db=fixture(options);await assert.rejects(inspect(db,header,rows),pattern);assert.equal(db.entries().length,0);
  }
});
test('receipt dates accept Sequelize Date values without changing stored calendar date',()=>{
 const validation=require('../src/utils/receiptValidation');assert.equal(validation.storedDate(new Date('2026-10-06T00:00:00Z'),'Inward date'),'2026-10-06');assert.equal(validation.storedDate('2026-10-06 12:00:00','Bill date'),'2026-10-06');assert.throws(()=>validation.storedDate('2026-02-30','Bill date'),/valid date/);
});
