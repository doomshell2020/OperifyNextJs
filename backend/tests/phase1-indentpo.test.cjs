const test=require('node:test'),assert=require('node:assert/strict');
const repo=require('../src/modules/indentpo/indentpo.repository'),service=require('../src/modules/indentpo/indentpo.service'),mutations=require('../src/modules/indentpo/indentpo.mutations');
test('group pending quantity includes all category members and retains tenant stock enforcement setting',async()=>{
 const db={query:async(sql,args)=>{
  if(sql.includes('sitesettings_details'))return [{stock_update:'N'}];
  if(sql.includes('SELECT designsheetno'))return [{designsheetno:'1001'}];
  if(sql.includes('FROM designsheetdetails'))return [{item_id:9,item_qty:10,is_group:1,category_id:7,item_name:'Copper'}];
  if(sql.includes('as sum_qty')){assert.match(sql,/item_id IN \(SELECT id FROM st_additem WHERE category_id=:category_id\)/);return [{sum_qty:4}];}
  if(sql.includes('as grn_qty'))return [{grn_qty:0,issued_stock_qty:0}];
  return [{id:9,item_name:'Copper',inhand_stock:0},{id:10,item_name:'Wire',inhand_stock:0}];
 }};const [row]=await repo.getDesignSheetDetails(db,1,2);assert.equal(row.pending_qty,6);assert.equal(row.stock_update,false);
});
test('group siblings consume one pending budget and zero-stock issues remain allowed when PHP disables stock enforcement',async()=>{
 const originalDetails=repo.getDesignSheetDetails,originalSave=repo.saveIndentpo;
 repo.getDesignSheetDetails=async()=>[{item_id:9,is_group:1,pending_qty:6,inhand_stock:0,stock_update:false,group_items:[{id:9,inhand_stock:0},{id:10,inhand_stock:0}]}];
 repo.saveIndentpo=async(db,data,user,validate)=>{await validate({});return {id:1};};
 try{
  const header={indent_id:'1001',contract_id:1,finishedproduct_id:2,machine_id:1};
  await assert.rejects(service.saveIndentpo({}, {...header,items:[{item_id:9,issue_qty:4},{item_id:10,issue_qty:3}]},1),/pending quantity/);
  assert.equal((await service.saveIndentpo({}, {...header,items:[{item_id:9,issue_qty:4}]},1)).id,1);
 }finally{repo.getDesignSheetDetails=originalDetails;repo.saveIndentpo=originalSave;}
});
test('material issue list counts and limits the full filtered dataset on the server',async()=>{
 const db={query:async(sql,args)=>{assert.match(sql,/i.issue_date/);if(sql.includes('COUNT(*)'))return [{total:101}];assert.match(sql,/LIMIT :limit OFFSET :offset/);assert.equal(args.replacements.offset,50);return [{id:3,indent_id:'1003'}];}};
 const result=await repo.listIndentpo(db,{page:2,date_from:'2026-10-01'});assert.equal(result.total,101);assert.equal(result.page,2);assert.equal(result.limit,50);assert.equal(result.data.length,1);
});
test('material issue edit updates existing ledger rows without inserting another deduction',async()=>{
 const writes=[];const db={transaction:async fn=>fn({}),query:async(sql,args)=>{assert.ok(args.transaction);if(sql.startsWith('SELECT *'))return [{id:1}];if(sql.startsWith('SELECT'))return [{id:7,item_id:9}];assert.match(sql,/^UPDATE/);writes.push({sql,args});return [null,1];}};
 await mutations.update(db,'1001',{machine_id:2,issued_name:'Admin',issue_date:'2026-10-06',items:[{id:7,item_id:9,quantity:2}]},1);assert.equal(writes.length,2);assert.equal(writes[1].args.replacements.quantity,2);
});
