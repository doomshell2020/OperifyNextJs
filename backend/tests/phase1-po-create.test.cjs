const test=require('node:test'),assert=require('node:assert/strict');
const calculate=require('../src/modules/purchaseOrder/purchaseOrder.create-validation');
test('PO creation calculates included/excluded tax from the saved master, ignoring submitted totals',async()=>{
  const tx={};const db={query:async(sql,args)=>{assert.equal(args.transaction,tx);return sql.includes('st_taxmaster')?[{tax:18}]:[{id:1}];}};
  const result=await calculate(db,{vendor_id:1,total_amt:999,postatus:'C'},[{item_id:1,item_qty:2,item_amt:118,tax_id:1,tax_cal:'0',tax_percentage:28,item_total_amount:999},{item_id:1,item_qty:1,item_amt:100,tax_id:1,tax_cal:'1'}],tx);
  assert.equal(result.items[0].item_base_price,236);assert.equal(result.items[0].item_tax_amt,36);assert.equal(result.items[0].item_total_amount,236);
  assert.equal(result.items[1].item_tax_amt,18);assert.equal(result.po.total_amt,354);assert.equal(result.po.total_tax,54);assert.equal(result.po.total_qty,3);assert.equal(result.po.postatus,'O');
});
test('replayed PO number is rejected under lock before any insertion',async()=>{
  let rollback=0;const tx={rollback:async()=>rollback++,commit:async()=>{throw Error('Commit reached');}};
  const db={transaction:async()=>tx,query:async(sql,args)=>{assert.equal(args.transaction,tx);assert.match(sql,/FOR UPDATE/);assert.match(sql,/^SELECT/);return [{id:1}];}};
  await assert.rejects(require('../src/modules/purchaseOrder/purchaseOrder.service').createPurchaseOrder(db,{purchaseorder_id:'2627-1'},[]),/already exists/);assert.equal(rollback,1);
});
