const test=require('node:test'),assert=require('node:assert/strict');
// All fixtures and writes are in memory; this suite never opens a database.
const config=require.resolve('../src/config/sequelize');
let state;
const db={transaction:async fn=>{const backup=structuredClone(state);try{return await fn({id:'transaction'});}catch(error){state=backup;throw error;}},query:async(sql,opts)=>{
 assert.ok(opts.transaction,'A write must use the transaction');
 if(sql.startsWith('UPDATE'))state.updates.push({sql,values:opts.replacements});
 else if(sql.startsWith('DELETE'))state.deletes.push(sql);
 else throw Error('Unexpected write '+sql);
}};
require.cache[config]={id:config,filename:config,loaded:true,exports:{getTenantSequelize:async()=>db,centralSequelize:db}};
const L=require('../src/modules/jobChallan/legacyStore'),original={...L};
const challan=require('../src/modules/jobChallan/jobChallan.service'),receive=require('../src/modules/jobReceive/jobReceive.service'),gate=require('../src/modules/gatepass/gatepass.service');
let overrides={};
function setup(extra={}){
 state={inserts:[],movements:[],updates:[],deletes:[]};
 Object.assign(L,original);overrides=extra;
 L.select=async(db,sql,params,t)=>{
  if(extra.select){const result=extra.select(sql,params,t);if(result!==undefined)return result;}
  if(sql.includes('GET_LOCK'))return [{acquired:1}];
  if(sql.includes('RELEASE_LOCK'))return [{}];
  if(sql.startsWith('SELECT id FROM job_challans WHERE challan_no'))return extra.duplicate?[{id:1}]:[];
  if(sql.includes('SELECT * FROM st_additem WHERE id IN'))return [{id:1,item_name:'Copper',itemtype:'RawMaterial',status:'Y'},{id:2,item_name:'Cable',itemtype:'Semi-Finished Product',status:'Y'}];
  if(sql.includes('SELECT id FROM sub_contractors'))return [{id:1}];
  if(sql.includes('SELECT id FROM st_additem'))return extra.missingItem?[]:[{id:99}];
  if(sql.includes(' AS dispatched,'))return [{dispatched:10,received:extra.received ?? 4}];
  if(sql.includes('SELECT COUNT(*)'))return [{total:extra.receiveCount || 0}];
  if(sql.includes('SELECT id,jc_id FROM gatepasses'))return extra.passes || [];
  if(sql.includes('SELECT a.id,u.unit_name'))return [{id:1,unit_name:'KG'}];
  if(sql.startsWith('SELECT * FROM gatepasses'))return [{id:7,gatepass_type:'Miscellaneous'}];
  throw Error('Unhandled read '+sql);
 };
 L.insert=async(db,table,values,t,database)=>{assert.ok(t);state.inserts.push({table,values,database});return state.inserts.length+100;};
 L.movement=async(db,values,t)=>{assert.ok(t);state.movements.push(values);};
 L.stock=async()=>extra.stock ?? 10;
 L.columns=async()=>extra.columns || ['rate','tax_rate','tax_amount','amount','is_manual','manual_challan_no','reference_jc_no','challan_date','vehicle_no','jc_type','other_company_name','receive_no'].map(Field=>({Field}));
 L.source=async(dbName,sender,id,t)=>{assert.ok(t);return {id:3,status:extra.status || 'Created',sub_contractors_id:2};};
 receive.pending=async()=>[{item_id:1,item_name:'Copper',original_qty:10,received_qty:2,pending_qty:8}];
 gate.jcData=async()=>({sub_contractor_id:2,items:[{item_id:1,item_name:'Copper',quantity:10,jc_no:'123',unit:'KG'}]});
}
const dispatch={challan_no:'123',jc_date:'2026-10-06',sub_contractors_id:1,processing_type:'Manufacturing',items:[{item_id:1,quantity:4,rate:5,tax_rate:18}]};
test('dispatch calculates totals and creates one outward movement per item',async()=>{setup();await challan.create('tenant',dispatch);assert.equal(state.inserts[0].values.total_amount,20);assert.equal(state.inserts[0].values.gst_amount,3.6);assert.equal(state.inserts[1].values.total_amount,23.6);assert.equal(state.movements.length,1);assert.equal(state.movements[0].store_type,'2');});
test('duplicate JC number does not create stock',async()=>{setup({duplicate:true});await assert.rejects(challan.create('tenant',dispatch),/already exists/);assert.equal(state.inserts.length,0);assert.equal(state.movements.length,0);});
test('repeated dispatch rows cannot overspend the same stock budget',async()=>{setup({stock:6});await assert.rejects(challan.create('tenant',{...dispatch,items:[...dispatch.items,...dispatch.items]}),/Insufficient stock/);assert.equal(state.inserts.length,0);});
test('semi-finished product retains rate, HSN, tax and contributes to header totals',async()=>{setup();await challan.create('tenant',{...dispatch,processing_type:'In Progress',semi_finished_item_id:2,semi_finished_quantity:2,semi_finished_rate:10,semi_finished_tax_rate:5,semi_finished_hsn_code:'8544'});assert.equal(state.inserts[0].values.total_amount,40);assert.equal(state.inserts[0].values.gst_amount,4.6);assert.equal(state.inserts[2].values.hsn_code,'8544');assert.equal(state.inserts[2].values.rate,10);assert.equal(state.movements.length,2);});
test('manual receive stores local receipt and exactly one inward movement',async()=>{setup();await receive.create('tenant',{jc_type:'Others',manual_jc_type:'Others',other_company_name:'Supplier',challan_no:'M-1',challan_date:'2026-10-06',manual_items:[{item_id:1,receive_qty:3}]});assert.equal(state.inserts[0].values.is_manual,1);assert.equal(state.inserts[0].values.challan_id,null);assert.equal(state.movements[0].challan_id,0);assert.equal(state.movements[0].store_type,'1');});
test('incoming receipt uses sender ID and maps local item by name',async()=>{setup();await receive.create('tenant',{challan_id:'3|sender',items:[{item_id:1,receive_qty:2}]});assert.equal(state.inserts[0].database,'sender');assert.equal(state.inserts[0].values.item_id,1);assert.equal(state.movements[0].item_id,99);assert.equal(state.updates[0].values.status,'Partially Returned');});
test('missing local item rolls back the entire incoming receipt',async()=>{setup({missingItem:true});await assert.rejects(receive.create('tenant',{challan_id:'3|sender',items:[{item_id:1,receive_qty:2}]}),/does not exist/);assert.equal(state.inserts.length,0);assert.equal(state.movements.length,0);});
test('duplicate rows in one receive cannot exceed pending quantity',async()=>{setup();await assert.rejects(receive.create('tenant',{challan_id:'3|sender',items:[{item_id:1,receive_qty:5},{item_id:1,receive_qty:5}]}),/greater than Pending/);assert.equal(state.inserts.length,0);assert.equal(state.movements.length,0);});
test('fully received JC becomes Completed',async()=>{setup({received:10});await receive.create('tenant',{challan_id:'3|sender',items:[{item_id:1,receive_qty:8}]});assert.equal(state.updates[0].values.status,'Completed');});
test('completed or cancelled JC cannot receive more stock',async()=>{for(const status of ['Completed','Cancelled','Deleted']){setup({status});await assert.rejects(receive.create('tenant',{challan_id:'3|sender',items:[{item_id:1,receive_qty:1}]}),/cannot be received/);assert.equal(state.movements.length,0);}});
test('local return charge fields cannot be silently discarded on old schemas',async()=>{setup({columns:[]});await assert.rejects(receive.create('tenant',{receive_date:'2026-10-06',items:[{item_id:1,receive_qty:1,rate:10}]},3),/valuation columns/);assert.equal(state.movements.length,0);});
test('JC deletion is blocked once any receipt exists',async()=>{setup({receiveCount:1});await assert.rejects(challan.remove('tenant',3),/cannot be deleted/);assert.equal(state.deletes.length,0);});
test('miscellaneous Gatepass generates GP number and never moves stock',async()=>{setup();await gate.save('tenant',{gatepass_type:'Miscellaneous',company_name:'Supplier',date:'2026-10-06',items:[{item_id:1,quantity:4}]});assert.equal(state.inserts[0].table,'gatepasses');assert.match(state.updates[0].values.no,/^GP-\d{4}$/);assert.equal(state.movements.length,0);});
test('JC Gatepass quantity comes from source JC, ignoring tampered client quantity',async()=>{setup();await gate.save('tenant',{gatepass_type:'JC Based',jc_id:[3],date:'2026-10-06',items:[{item_id:1,quantity:999,description:'notes'}]});assert.equal(state.inserts[1].values.quantity,10);assert.equal(state.inserts[1].values.description,'JC No: 123\nnotes');assert.equal(state.movements.length,0);});
test('duplicate JC mapping within a comma separated Gatepass is rejected',async()=>{setup({passes:[{id:9,jc_id:'2,3'}]});await assert.rejects(gate.save('tenant',{jc_id:[3],date:'2026-10-06'}),/already been created/);assert.equal(state.inserts.length,0);});
test('multi-JC saves fail before writes when schema still uses integer',async()=>{setup({columns:[{Field:'jc_id',Type:'int(11)'}]});await assert.rejects(gate.save('tenant',{jc_id:[3,4],date:'2026-10-06'}),/migration/);assert.equal(state.inserts.length,0);});
test('Getpass validation failure during edit retains original items',async()=>{setup();await assert.rejects(gate.save('tenant',{gatepass_type:'Miscellaneous',company_name:'',items:[]},7),/Company Name/);assert.equal(state.deletes.length,0);});
test('pagination and calendar validation reject invalid boundaries',()=>{assert.throws(()=>L.date('2026-02-30'),/valid date/);assert.equal(L.paginate(Array.from({length:101}),{limit:500}).limit,100);assert.throws(()=>L.paginate([],{page:2}),/Page not found/);});
test('successful Gatepass edit replaces items inside its transaction without touching stock',async()=>{setup();await gate.save('tenant',{gatepass_type:'Miscellaneous',company_name:'Supplier',date:'2026-10-06',items:[{item_id:1,quantity:2}]},7);assert.equal(state.updates[0].values.id,7);assert.deepEqual(state.deletes,['DELETE FROM gatepass_items WHERE gatepass_id=:id']);assert.equal(state.inserts[0].values.gatepass_id,7);assert.equal(state.movements.length,0);});
test('manual receipt is blocked without its metadata columns',async()=>{setup({columns:[]});await assert.rejects(receive.create('tenant',{jc_type:'Others',manual_jc_type:'Others',other_company_name:'Supplier',challan_no:'M-1',challan_date:'2026-10-06',manual_items:[{item_id:1,receive_qty:3}]}),/Manual receive columns/);assert.equal(state.inserts.length,0);});
test('migrated schema retains local return rate and tax with one inward movement',async()=>{setup();await receive.create('tenant',{receive_date:'2026-10-06',items:[{item_id:1,receive_qty:2,rate:10,tax_rate:18}]},3);const receipt=state.inserts[0].values;assert.equal(receipt.rate,10);assert.equal(receipt.tax_rate,18);assert.equal(receipt.tax_amount,3.6);assert.equal(receipt.amount,23.6);assert.equal(state.movements.length,1);assert.equal(state.movements[0].item_id,1);assert.equal(state.movements[0].store_type,'1');});
test('migrated Gatepass schema retains multiple JC mappings without moving stock',async()=>{setup({columns:[{Field:'jc_id',Type:'varchar(255)'}]});gate.jcData=async()=>({sub_contractor_id:2,items:[{item_id:1,item_name:'Copper',quantity:10,jc_no:'123',unit:'KG'},{item_id:1,item_name:'Copper',quantity:5,jc_no:'124',unit:'KG'}]});await gate.save('tenant',{gatepass_type:'JC Based',jc_id:[3,4],date:'2026-10-06',items:[]});assert.equal(state.inserts[0].values.jc_id,'3,4');assert.equal(state.inserts[1].values.quantity,10);assert.equal(state.inserts[2].values.quantity,5);assert.equal(state.movements.length,0);});
test('configured action permissions deny an unassigned role at the backend',async()=>{setup({select:sql=>sql.includes('permission_label')?[{url:'admin/jobchallan/add'}]:undefined});const file=require.resolve('../src/modules/jobChallan/legacyPermission');delete require.cache[file];const middleware=require(file)('jobchallan','add');let status=0,nextCalled=false;await middleware({user:{role_id:6,permissions:[]}},{status:value=>{status=value;return {json:()=>{}};}},()=>{nextCalled=true;});assert.equal(status,403);assert.equal(nextCalled,false);});
test('configured action permissions accept its grant; unconfigured PHP actions remain available',async()=>{setup({select:sql=>sql.includes('permission_label')?[{url:'admin/jobchallan/add'}]:undefined});let file=require.resolve('../src/modules/jobChallan/legacyPermission');delete require.cache[file];let nextCalled=false;await require(file)('jobchallan','add')({user:{role_id:6,permissions:['legacy:admin/jobchallan/add']}},{},()=>{nextCalled=true;});assert.equal(nextCalled,true);setup({select:sql=>sql.includes('permission_label')?[]:undefined});delete require.cache[file];nextCalled=false;await require(file)('jobchallan','add')({user:{role_id:6,permissions:[]}},{},()=>{nextCalled=true;});assert.equal(nextCalled,true);});
