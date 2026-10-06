const test=require('node:test'),assert=require('node:assert/strict');
const design=require('../src/modules/designsheet/designsheet.controller');
const po=require('../src/modules/purchaseOrder/purchaseOrder.repository');
function fixture(failDetail=false){
 let records=[];let error,status;
 const transaction={};
 const db={transaction:async fn=>{const before=records.slice();try{return await fn(transaction);}catch(e){records=before;throw e;}}};
 const models={contracts:{findOne:async()=>({id:1})},bom_finisedproduct:{findOne:async()=>({quantity:10})},designsheet:{findOne:async()=>null,create:async(data,options)=>{assert.equal(options.transaction,transaction);records.push(data);return{id:7};}},designsheetdetails:{bulkCreate:async(data,options)=>{assert.equal(options.transaction,transaction);if(failDetail)throw Error('detail failure');records.push(...data);}}};
 const req={dbPool:db,models,body:{contract_id:1,item_id:2,designsheetno:'1001',quantity:10,datefrom:'2026-10-06',pitemname:[9],pitemquantity:[10]}};
 const res={status:code=>{status=code;return res;},json:()=>{}};
 return {req,res,next:e=>{error=e;},records:()=>records,error:()=>error,status:()=>status};
}
test('design sheet header rolls back when detail insertion fails',async()=>{const f=fixture(true);await design.create(f.req,f.res,f.next);assert.equal(f.records().length,0);assert.equal(f.error().message,'detail failure');});
test('duplicate design items are rejected before header insertion',async()=>{const f=fixture();f.req.body.pitemname=[9,9];await design.create(f.req,f.res,f.next);assert.equal(f.status(),400);assert.equal(f.records().length,0);});
test('valid design header and details share one transaction',async()=>{const f=fixture();await design.create(f.req,f.res,f.next);assert.equal(f.error(),undefined);assert.equal(f.status(),201);assert.equal(f.records().length,2);assert.equal(f.records()[1].designsheet_id,7);});
test('PO with any receipt cannot reach deletion',async()=>{
 const db={query:async(sql,args)=>{assert.ok(args.transaction);if(sql.includes('SELECT purchaseorder_id'))return [{purchaseorder_id:'2627-1',status:'Y',is_revised:0}];if(sql.includes('st_goodsreceive'))return [{id:2}];throw Error('Unexpected write');}};
 await assert.rejects(po.deletePurchaseOrder(db,1,{}),/GRN exists/);
});
test('old PO revision cannot reach deletion',async()=>{
 const db={query:async sql=>{if(sql.includes('SELECT purchaseorder_id'))return [{purchaseorder_id:'2627-1',status:'Y',is_revised:0}];if(sql.includes('st_goodsreceive'))return [];if(sql.includes('MAX(is_revised)'))return [{revision:1}];throw Error('Unexpected write');}};
 await assert.rejects(po.deletePurchaseOrder(db,1,{}),/latest active PO revision/);
});
test('stale PO revision locks its family and rejects another revision before any write',async()=>{
 let familyLocked=false;
 const db={query:async(sql,args)=>{assert.ok(args.transaction);if(sql.includes('SELECT *')){assert.match(sql,/FOR UPDATE/);return [{id:1,purchaseorder_id:'2627-1',is_revised:0}];}if(sql.includes('SELECT id')){assert.match(sql,/FOR UPDATE/);familyLocked=true;return [{id:2},{id:1}];}if(sql.includes('MAX(is_revised)'))return [{latest_revision:1}];throw Error('Unexpected write');}};
 await assert.rejects(po.createRevision(db,1,{},[],{}),/latest Purchase Order revision/);assert.ok(familyLocked);
});
test('contract edits preserve existing finished-product IDs and add only new rows',async()=>{
 const repository=require('../src/modules/contract/contract.repository');let inserted=[],deleted=[];
 const db={models:{bom_finisedproduct:{findAll:async()=>[{id:17,product_id:2,quantity:10,price:50}],destroy:async options=>deleted.push(options.where),create:async data=>inserted.push(data)}}};
 await repository.syncFinishedProducts(db,1,[{product_id:2,quantity:10,price:50},{product_id:3,quantity:2,price:100}],{});assert.equal(deleted.length,0);assert.deepEqual(inserted.map(row=>row.product_id),[3]);
 await assert.rejects(repository.syncFinishedProducts(db,1,[{product_id:2,quantity:11,price:50}],{}),/read-only/);assert.equal(deleted.length,0);
});
test('indent finalization locks its cart and rejects a duplicate permanent indent before writes',async()=>{
 const repository=require('../src/modules/indent/indent.repository');let rollbacks=0;const transaction={rollback:async()=>rollbacks++,commit:async()=>{throw Error('Commit reached');}};
 const db={transaction:async()=>transaction,query:async(sql,args)=>{assert.equal(args.transaction,transaction);assert.match(sql,/FOR UPDATE/);return sql.includes('st_indentmaster_temp')?[{item_id:9}]:[{id:1}];}};
 await assert.rejects(repository.finalizeIndent(db,'1001',1),/already been finalized/);assert.equal(rollbacks,1);
});
