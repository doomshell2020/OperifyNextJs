// Pure regression tests: simulated query results; no database connections/writes.
const assert = require('node:assert/strict');
const repo = require('../backend/src/modules/purchaseOrder/purchaseOrder.repository');
const transaction = {};
async function designSheetCache() {
  const fs=require('node:fs'),path=require('node:path'),vm=require('node:vm');
  const {createRequire}=require('node:module');
  const frontendRequire=createRequire(path.resolve(__dirname,'../frontend/package.json'));
  const React=frontendRequire('react'),ts=frontendRequire('typescript');
  const {QueryClient}=frontendRequire('@tanstack/react-query');
  const client=new QueryClient();
  const listKeys=[['designsheets',{page:1}],['designsheets',{page:2,contract_id:'7'}]];
  for(const key of listKeys)client.setQueryData(key,{data:[]});
  client.setQueryData(['designsheet-details','12'],{});
  const events=[];
  const module={exports:{}};
  const code=ts.transpileModule(fs.readFileSync(path.resolve(__dirname,'../frontend/app/dashboard/design-sheet/add/page.tsx'),'utf8'),{compilerOptions:{jsx:ts.JsxEmit.ReactJSX,module:ts.ModuleKind.CommonJS,esModuleInterop:true}}).outputText;
  const dummy=()=>null;
  vm.runInNewContext(code,{module,exports:module.exports,console,Date,FormData,Set,require(name){
    if(name==='react')return {...React,useEffect:()=>{},useRef:()=>({current:null}),useState:initial=>[initial && !Array.isArray(initial) && Object.hasOwn(initial,'contract_id')?{...initial,contract_id:'7',item_id:'8',quantity:'1'}:initial,()=>{}]};
    if(name==='react/jsx-runtime')return frontendRequire(name);
    if(name==='@tanstack/react-query')return {useQueryClient:()=>client};
    if(name==='next/navigation')return {useRouter:()=>({push:url=>{for(const key of listKeys)assert(client.getQueryState(key).isInvalidated);events.push(url);}})};
    if(name.includes('designsheet.service'))return {designsheetService:{createDesignSheet:async()=>events.push('saved')}};
    if(name==='react-hot-toast')return {toast:{success:()=>{},error:message=>{throw new Error(message);}}};
    return new Proxy({__esModule:true,default:dummy},{get:(object,key)=>key in object?object[key]:dummy});
  }});
  const tree=module.exports.default();
  const flatten=element=>!element || typeof element!=='object'?[]:[element,...React.Children.toArray(element.props?.children).flatMap(flatten)];
  const form=flatten(tree).find(element=>element.type==='form');assert(form);
  await form.props.onSubmit({preventDefault(){}});
  assert.deepEqual(events,['saved','/dashboard/design-sheet']);
  assert(client.getQueryState(['designsheet-details','12']).isInvalidated);
  assert.equal(client.getQueryData(listKeys[1]).data.length,0,'Filtered lists are invalidated without dropping their keys/data');
  client.clear();
}
function revisionDb(revision=0, latest=revision) {
  const writes=[];
  const source={id:12,purchaseorder_id:'2627-415',is_revised:revision,status:'R',vendor_id:7,delivery_date:'2026-10-20',added_time:'2026-09-01',postatus:'O'};
  return {writes,source,async query(sql,options) {
    const values=options.replacements;
    if(sql.startsWith('SELECT * FROM st_purchaseorder'))return [{...source}];
    if(sql.includes('latest_revision'))return [{latest_revision:latest}];
    if(sql.startsWith('SELECT id FROM st_purchaseorder'))return [{id:12}];
    if(sql.includes('SELECT id FROM st_additem'))return values.id===8 ? [{id:8}] : [];
    if(sql.includes('SELECT tax FROM st_taxmaster'))return [{tax:18}];
    assert.equal(options.transaction,transaction);
    writes.push({sql,values});
    return sql.includes('INSERT INTO st_purchaseorder (') ? [99] : [1];
  }};
}
async function revisions() {
  for(const revision of [0,3]) {
    const db=revisionDb(revision);
    const original={...db.source};
    const result=await repo.createRevision(db,12,{vendor_id:999,revised_date:'2026-10-09'},[{item_id:8,order_qty:2,rate:10,tax_id:6,amount:999,tax_amt:999}],transaction);
    assert.equal(result.amendment_no,revision+1);
    assert.equal(result.display_po_number,`2627-415 R-${revision+1}`);
    const header=db.writes[0].values;
    assert.equal(header.vendor_id,7);assert.equal(header.added_time,original.added_time);
    assert.equal(header.total_qty,2);assert.equal(header.total_tax,3.6);assert.equal(header.total_amt,23.6);
    assert.equal(db.writes[1].values.poprimary_id,99);
    assert.deepEqual(db.source,original);
    assert(!db.writes.some(write=>/^UPDATE st_purchaseorder/.test(write.sql.trim())));
  }
  await assert.rejects(repo.createRevision(revisionDb(1,2),12,{},[],transaction),/latest/);
  await assert.rejects(repo.createRevision(revisionDb(),12,{},[],transaction),/items are required/);
  await assert.rejects(repo.createRevision(revisionDb(),12,{},[{item_id:999,order_qty:1,rate:1}],transaction),/item does not exist/);
  await assert.rejects(repo.createRevision(revisionDb(),12,{},[{item_id:8,order_qty:-1,rate:1}],transaction),/non-negative/);
}
function deliveryDb(completed=false) {
  const writes=[];
  const po={id:12,purchaseorder_id:'2627-415',vendor_id:7,added_time:'2026-10-01',delivery_date:'2026-10-20',postatus:'O',status:'Y'};
  return {writes,async query(sql,options) {
    if(sql.includes('SELECT * FROM st_purchaseorder'))return [{...po}];
    if(sql.includes('SUM(item_qty)'))return [{item_id:8,qty:2}];
    if(sql.includes('SELECT item_id,item_qty'))return completed?[{item_id:8,item_qty:2,delivery_date:'2026-10-10',status:'N'}]:[];
    writes.push({sql,values:options.replacements});return [1];
  }};
}
const schedule=(date='2026-10-10',qty=2)=>[{inwarddate:date,items:[{item_id:8,qty}]}];
async function delivery() {
  const db=deliveryDb();await repo.addDeliveryNote(db,12,'wrong',999,schedule(),'note',transaction);
  assert.equal(db.writes[1].values.po_number,'2627-415');assert.equal(db.writes[1].values.vendor_id,7);
  for(const [rows,message] of [[schedule('2026-09-30'),/between/],[schedule('2026-10-21'),/between/],[schedule('',2),/cannot be blank/],[schedule('2026-10-10',1),/must equal/],[[...schedule(),...schedule()],/unique/]]) {
    const db=deliveryDb();await assert.rejects(repo.addDeliveryNote(db,12,'',0,rows,'',transaction),message);assert.equal(db.writes.length,0);
  }
  const complete=deliveryDb(true);await repo.addDeliveryNote(complete,12,'',0,schedule(),'note',transaction);
  assert.equal(complete.writes.length,1);assert(complete.writes[0].sql.includes("status != 'N'"));
  await assert.rejects(repo.addDeliveryNote(deliveryDb(true),12,'',0,schedule('2026-10-11'),'note',transaction),/Completed/);
}
revisions().then(delivery).then(designSheetCache).then(()=>console.log('Revision identity, numbering, immutable history, tax totals, delivery bounds, quantities, completed schedules and Design Sheet cache invalidation passed.')).catch(error=>{console.error(error);process.exitCode=1;});
