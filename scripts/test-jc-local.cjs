// Transaction regression tests using an in-memory database adapter. No real database writes.
const assert=require('node:assert/strict');
const fs=require('node:fs');
const path=require('node:path');
const vm=require('node:vm');
const {createRequire}=require('node:module');
const backendRequire=createRequire(path.resolve(__dirname,'../backend/package.json'));
const actualL=require('../backend/src/modules/jobChallan/legacyStore');
const company='selected_company';
const fields={
  job_challans:['id','challan_no','jc_date','sub_contractors_id','processing_type','total_amount','gst_amount','estimated_values','expected_days','work_description','modified'],
  job_challan_items:['id','challan_id','item_id','item_name','quantity','hsn_code','rate','tax_rate','amount','tax_amount','total_amount'],
  job_challan_receives:['id','challan_id','item_id','dispatch_qty','received_qty','receive_date','vehicle_no','sub_contractors_id','remarks','rate','tax_rate','tax_amount','amount','created','is_manual','manual_challan_no','reference_jc_no','challan_date','jc_type','other_company_name','receive_no'],
  st_stock_register:['id','indent_id','contract_id','finishedproduct_id','item_id','quantity','issue_date','sub_contractors_id','store_type','added_time','status']
};
const db={state:{job_challans:[],job_challan_items:[],job_challan_receives:[],st_stock_register:[{item_id:1,quantity:100,store_type:'0'},{item_id:2,quantity:10,store_type:'0'}]},calls:[],
  async transaction(run){const snapshot=structuredClone(this.state);try{return await run({local:true});}catch(error){this.state=snapshot;throw error;}},
  async query(sql,options={}) {
    this.calls.push(sql);
    for(const match of sql.matchAll(/`([a-zA-Z0-9_]+)`\.`/g)) assert.equal(match[1],company,'Never query another company');
    const v=options.replacements || {};
    if(sql.startsWith('SHOW COLUMNS')) {const name=sql.match(/`([^`]+)`$/)[1];return fields[name].map(Field=>({Field}));}
    if(sql.includes('GET_LOCK'))return [{acquired:1}];
    if(sql.includes('RELEASE_LOCK'))return [{released:1}];
    if(sql.startsWith('INSERT INTO')) {
      assert(options.transaction?.local,'All inserts must be in one local transaction');
      const match=sql.match(/INSERT INTO (?:`[^`]+`\.)?`([^`]+)` \((.*?)\)/),names=[...match[2].matchAll(/`([^`]+)`/g)].map(row=>row[1]);
      const rows=this.state[match[1]],row={id:rows.length+1,...Object.fromEntries(names.map((key,index)=>[key,v['v'+index]]))};
      if(match[1]==='job_challans')row.status='Pending';
      rows.push(row);return [row.id];
    }
    if(sql.startsWith('UPDATE')) {assert(options.transaction?.local);this.state.job_challans.find(row=>row.id===v.id).status=v.status;return [1];}
    if(sql.includes('SELECT id FROM job_challans WHERE challan_no'))return this.state.job_challans.filter(row=>row.challan_no===v.challan_no);
    if(sql.startsWith('SELECT * FROM job_challans'))return this.state.job_challans.filter(row=>row.id===v.id);
    if(sql.includes('FROM sub_contractors WHERE id'))return Number(v.id)===7?[{id:7,name:'Local party'}]:[];
    if(sql.includes('SELECT * FROM st_additem WHERE id IN'))return [{id:1,item_name:'Raw material',itemtype:'RawMaterial',status:'Y'},{id:2,item_name:'Semi finished',itemtype:'Semi-Finished Product',status:'Y'}].filter(row=>v.ids.includes(row.id));
    if(sql.includes('FROM st_additem WHERE id=:id'))return [{id:Number(v.id)}];
    if(sql.includes('AS balance')) {const entries=this.state.st_stock_register.filter(row=>Number(row.item_id)===Number(v.itemId));return [{balance:entries.reduce((sum,row)=>sum+(['0','1','3'].includes(row.store_type)?Number(row.quantity):['2','4'].includes(row.store_type)?-Number(row.quantity):0),0)}];}
    if(sql.includes('AS original_qty')) {
      const groups=new Map();
      for(const row of this.state.job_challan_items.filter(row=>row.challan_id===v.id)) groups.set(row.item_id,(groups.get(row.item_id)||0)+row.quantity);
      return [...groups].map(([item_id,original_qty])=>({item_id,item_name:item_id===1?'Raw material':'Semi finished',original_qty,received_qty:this.state.job_challan_receives.filter(row=>row.challan_id===v.id && row.item_id===item_id).reduce((sum,row)=>sum+row.received_qty,0)}));
    }
    if(sql.includes('AS dispatched'))return [{dispatched:this.state.job_challan_items.filter(row=>row.challan_id===v.id).reduce((sum,row)=>sum+row.quantity,0),received:this.state.job_challan_receives.filter(row=>row.challan_id===v.id).reduce((sum,row)=>sum+row.received_qty,0)}];
    throw new Error('Unimplemented memory query: '+sql);
  }
};
const L={...actualL,source:async(dbName,sender,id,t)=>{
  if(sender && sender!==dbName)actualL.fail('JC records must belong to the selected company.',403);
  return actualL.source(dbName,sender,id,t,db);
}};
function load(file) {
  const module={exports:{}};
  vm.runInNewContext(fs.readFileSync(path.resolve(__dirname,'../backend/src/modules',file),'utf8'),{module,exports:module.exports,console,Date,Intl,Set,Map,Number,String,Array,Math,require(name){
    if(name.endsWith('/config/sequelize'))return {getTenantSequelize:async name=>{assert.equal(name,company);return db;}};
    if(name.endsWith('legacyStore'))return L;
    return backendRequire(name);
  }});
  return module.exports;
}
const jc=load('jobChallan/jobChallan.service.js'),receive=load('jobReceive/jobReceive.service.js');
const base={jc_date:'2026-10-12',sub_contractors_id:7,processing_type:'Manufacturing'};
(async()=>{
  const created=await jc.create(company,{...base,challan_no:'100',items:[{item_id:1,quantity:20,rate:10,tax_rate:18},{item_id:1,quantity:30,rate:10,tax_rate:18}]});
  assert.equal(created.id,1);
  assert.equal(db.state.st_stock_register.filter(row=>row.store_type==='2').length,2);
  assert.equal(db.state.job_challans[0].total_amount,500);assert.equal(db.state.job_challans[0].gst_amount,90);
  const initial=structuredClone(db.state);
  await assert.rejects(jc.create(company,{...base,challan_no:'100',items:[{item_id:1,quantity:1}]}),/already exists/);
  await assert.rejects(jc.create(company,{...base,challan_no:'101',items:[{item_id:1,quantity:30},{item_id:1,quantity:30}]}),/Insufficient stock/);
  assert.deepEqual(db.state,initial,'Rejected dispatches must not write any records');
  await receive.create(company,{challan_id:'1',items:[{item_id:1,receive_qty:10},{item_id:1,receive_qty:15}]});
  assert.equal(db.state.job_challan_receives.length,2);
  assert.equal(db.state.st_stock_register.filter(row=>row.store_type==='1').length,2,'One IN movement per saved receipt');
  assert.equal(db.state.job_challans[0].status,'Partially Returned');
  const partial=structuredClone(db.state);
  await assert.rejects(receive.create(company,{challan_id:'1',items:[{item_id:1,receive_qty:5},{item_id:1,receive_qty:30}]}),/Pending Qty/);
  assert.deepEqual(db.state,partial,'A later invalid line must roll back the earlier receipt and stock movement');
  await assert.rejects(receive.create(company,{challan_id:'1|other_company',items:[{item_id:1,receive_qty:1}]}),error=>error.status===403);
  assert.deepEqual(db.state,partial);
  await receive.create(company,{items:[{item_id:1,receive_qty:25,receive_date:'2026-10-11',vehicle_no:'V-1',rate:2,tax_rate:18}]},'1');
  assert.equal(db.state.job_challans[0].status,'Completed');
  const last=db.state.job_challan_receives.at(-1);
  assert.equal(last.receive_date,'2026-10-11');assert.equal(last.vehicle_no,'V-1');assert.equal(last.tax_amount,9);assert.equal(last.amount,59);
  const complete=structuredClone(db.state);
  await assert.rejects(receive.create(company,{challan_id:'1',items:[{item_id:1,receive_qty:1}]}),/cannot be received/);
  assert.deepEqual(db.state,complete,'Replaying a completed receipt cannot add stock twice');
  await jc.create(company,{...base,challan_no:'102',processing_type:'In Progress',items:[],semi_finished_item_id:2,semi_finished_quantity:4,semi_finished_rate:3,semi_finished_tax_rate:5});
  assert.equal(db.state.job_challan_items.at(-1).item_id,2);assert.equal(db.state.st_stock_register.at(-1).store_type,'2');
  await receive.create(company,{jc_type:'Others',manual_jc_type:'Others',other_company_name:'Manual party',challan_no:'M-1',challan_date:'2026-10-12',manual_items:[{item_id:1,receive_qty:3}]});
  assert.equal(db.state.job_challan_receives.at(-1).is_manual,1);assert.equal(db.state.st_stock_register.at(-1).indent_id,'0');
  assert(db.calls.some(sql=>sql.includes('FOR UPDATE')),'Receipt workflow must serialize through the locked local JC');
  assert.equal(actualL.filterRows([{jc_date:new Date('2026-10-12T00:00:00Z')}],{from_date:'2026-10-12',to_date:'2026-10-12'},'jc_date').length,1);
  assert.throws(()=>actualL.filterRows([],{from_date:'2026-10-13',to_date:'2026-10-12'},'jc_date'),/earlier/);
  console.log('JC local transactions: dispatch, duplicate rows, stock budgets, partial/full/manual receives, rollback, replay, cross-company rejection and dates passed. No real database writes.');
})().catch(error=>{console.error(error);process.exitCode=1;});
