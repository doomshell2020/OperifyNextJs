const test=require('node:test'),assert=require('node:assert/strict');
const repo=require('../src/modules/settings/settings.repository');
const controller=require('../src/modules/settings/settings.controller');
const stock=require('../src/modules/stockRegister/stockRegister.repository');
test('category deletion preserves category and product relationships by deactivating it',async()=>{
  let call;await repo.deleteCategory({query:async(sql,args)=>{call={sql,args};}},7);
  assert.match(call.sql,/UPDATE st_categorymaster SET status='N'/);assert.equal(call.args.type,'UPDATE');assert.equal(call.args.replacements.id,7);
});
test('duplicate category name cannot reach INSERT',async()=>{
  const db={query:async sql=>{assert.match(sql,/^SELECT/);return [{id:7}];}};
  let status;await controller.createCategory({body:{category_name:' Metal '},dbPool:db},{status:code=>{status=code;return {json:()=>{}};}},error=>{throw error;});assert.equal(status,409);
});
test('product edit persists PHP fields, does not apply add-time discount twice and retains omitted fields',async()=>{
  let call;await repo.updateProduct({query:async(sql,args)=>{call={sql,args};}},9,{item_name:' cable ',sale_price:'100',discount:'5',tax:'8',item_isbn:'8544',productprocess_id:['1','8'],size_id:'6'});
  assert.equal(call.args.replacements.item_name,'CABLE');assert.equal(call.args.replacements.sale_price,'100');assert.equal(call.args.replacements.tax,'8');assert.equal(call.args.replacements.item_isbn,'8544');assert.equal(call.args.replacements.productprocess_id,'1,8');assert.ok(!call.sql.includes('location_name='));
});
test('used product cannot be deleted',async()=>{
  const db={transaction:async fn=>fn({}),query:async(sql,args)=>{assert.ok(args.transaction);if(sql.startsWith('SELECT id'))return [{id:1}];if(sql.startsWith('SELECT'))return [{total:1}];throw Error('Deletion reached');}};
  await assert.rejects(repo.deleteProduct(db,1),/already used/);
});
test('product category cannot change after the item is used in a design sheet',async()=>{
  const db={query:async sql=>{assert.match(sql,/^SELECT/);return [{id:9,category_id:2,designsheet_count:1}];}};
  await assert.rejects(repo.updateProduct(db,9,{item_name:'Cable',category_id:'3'}),/Category cannot be changed/);
});
test('daily stock uses cumulative active creation-date quantities and never doubles reverse/return quantities',async()=>{
  const db={query:async(sql,args)=>{
    if(sql.includes('FROM st_additem'))return [{item_id:1,item_name:'Copper'}];
    assert.match(sql,/status!='N' AND DATE\(created\)<=:date/);assert.equal(args.replacements.date,'2026-10-06');
    return [{item_id:1,store_type:'0',quantity:5},{item_id:1,store_type:'1',quantity:10},{item_id:1,store_type:'3',quantity:3},{item_id:1,store_type:'2',quantity:4},{item_id:1,store_type:'4',quantity:2}];
  }};
  const [row]=await stock.getDailyStockAsOfDate(db,{date:'2026-10-06'});assert.equal(row.received_stock,'18.00');assert.equal(row.issued_stock,'6.00');assert.equal(row.closing_stock,'12.00');assert.equal(row.opening_stock,row.closing_stock);
});
test('detailed stock dispatch uses issue date and skips zero rows like current PHP',async()=>{
  const db={query:async sql=>{
    if(sql.includes('FROM st_additem'))return [{item_id:1,item_name:'Copper'},{item_id:2,item_name:'Empty'}];
    assert.match(sql,/DATE\(COALESCE\(issue_date, created\)\) as indent_date/);
    return [{item_id:1,store_type:'1',grn_date:'2026-10-01',indent_date:'2026-10-01',total_qty:10},{item_id:1,store_type:'2',grn_date:'2026-10-06',indent_date:'2026-10-06',total_qty:4}];
  }};
  const result=await stock.getStockRegister(db,{date_from:'2026-10-06',date_to:'2026-10-06'});assert.equal(result.length,1);assert.equal(result[0].opening_stock,'10.00');assert.equal(result[0].dispatched_stock,'4.00');assert.equal(result[0].closing_stock,'6.00');
});
