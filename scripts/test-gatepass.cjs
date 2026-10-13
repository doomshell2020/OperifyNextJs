const assert=require('node:assert/strict');
const fs=require('node:fs');
const vm=require('node:vm');
const path=require('node:path');
const {createRequire}=require('node:module');
const backendRequire=createRequire(path.resolve('backend/package.json'));
const actualL=backendRequire('./src/modules/jobChallan/legacyStore');
const state={gatepasses:[],gatepass_items:[]};
const calls=[];
const jcs=[{id:11,challan_no:'JC-11',sub_contractors_id:7,vehicle_no:'V-11'},{id:12,challan_no:'JC-12',sub_contractors_id:7,vehicle_no:''}];
const materials=[{item_id:3,item_name:'Raw',quantity:'10.50',jc_no:'JC-11',unit:'KG'},{item_id:3,item_name:'Raw',quantity:'8.25',jc_no:'JC-12',unit:'KG'}];
const db={async transaction(run){const before=structuredClone(state);try{return await run({transaction:true});}catch(e){Object.assign(state,before);throw e;}},async query(sql,options={}){
  calls.push(sql);const v=options.replacements || {};
  if(sql.startsWith('UPDATE gatepasses')){const g=state.gatepasses.find(g=>g.id===v.id);if(v.no)g.gatepass_no=v.no;else Object.assign(g,Object.fromEntries(Object.entries(v).filter(([k])=>k!=='id')));return [1];}
  if(sql.startsWith('DELETE FROM gatepass_items')){state.gatepass_items=state.gatepass_items.filter(i=>i.gatepass_id!==v.id);return [1];}
  if(sql.startsWith('SELECT * FROM gatepasses'))return state.gatepasses.filter(g=>g.id===v.id);
  if(sql.startsWith('SELECT * FROM gatepass_items'))return state.gatepass_items.filter(i=>i.gatepass_id===v.id);
  if(sql.includes('SELECT id,jc_id FROM gatepasses'))return state.gatepasses.filter(g=>g.id!==v.id && g.jc_id);
  if(sql.includes('FROM job_challans WHERE id IN'))return jcs.filter(j=>v.ids.includes(j.id));
  if(sql.includes('FROM job_challan_items'))return materials.filter(i=>v.ids.includes(Number(i.jc_no.slice(3))));
  if(sql.includes('FROM sub_contractors WHERE id'))return v.id===7?[{id:7}]:[];
  if(sql.includes("a.itemtype='RawMaterial'"))return v.id===3?[{id:3,unit_name:'METRE'}]:[];
  throw Error('Unexpected query: '+sql);
}};
const L={...actualL,select:(db,sql,v,t)=>db.query(sql,{replacements:v,transaction:t}),columns:async()=>[{Field:'jc_id',Type:'varchar(255)'}],insert:async(db,table,row,t)=>{
  assert(t.transaction);const id=Math.max(0,...state[table].map(i=>i.id))+1;state[table].push({id,...row,...(table==='gatepasses'?{status:'Active'}:{})});return id;
}};
const moduleContext={exports:{}};
vm.runInNewContext(fs.readFileSync('backend/src/modules/gatepass/gatepass.service.js','utf8'),{module:moduleContext,Date,Intl,Number,String,Array,Set,Math,require(name){if(name.endsWith('/config/sequelize'))return {getTenantSequelize:async()=>db};if(name.endsWith('legacyStore'))return L;return backendRequire(name);}});
const service=moduleContext.exports;
(async()=>{
  const base={date:'2026-10-13',sub_contractor_id:7,jc_id:[11,12],items:materials.map((i,index)=>({...i,description:'Description '+index,remarks:'Remark '+index}))};
  await service.save('memory',base);
  assert.equal(state.gatepasses[0].gatepass_no,'GP-0001');assert.equal(state.gatepasses[0].jc_id,'11,12');
  assert.equal(state.gatepass_items.length,2);assert.equal(state.gatepass_items[1].description,'JC No: JC-12\nDescription 1');
  let before=structuredClone(state);
  await assert.rejects(service.save('memory',base),/already been created/);assert.deepEqual(state,before);
  const original=structuredClone(state.gatepass_items);materials[0].quantity='99.00';
  await service.save('memory',{...base,items:original.map(i=>({...i,description:i.description+' edited',quantity:1000,remarks:'Edited'}))},1);
  assert.equal(state.gatepass_items[0].quantity,'10.50');assert.equal(state.gatepass_items[1].quantity,'8.25');
  assert.equal(state.gatepass_items[1].description,'JC No: JC-12\nDescription 1 edited');assert.equal(state.gatepasses[0].status,'Active');
  await service.save('memory',{gatepass_type:'Miscellaneous',date:'2026-10-13',company_name:'Memory company',items:[{item_id:3,quantity:2,unit:'METRE',remarks:'Misc'}]});
  assert.equal(state.gatepasses[1].gatepass_no,'GP-0002');assert.equal(state.gatepasses[1].jc_id,null);assert.equal(state.gatepass_items.at(-1).unit,'METRE');
  before=structuredClone(state);
  for(const payload of [
    {gatepass_type:'Miscellaneous',company_name:'',items:[{item_id:3,quantity:1}]},
    {gatepass_type:'Miscellaneous',company_name:'Memory',items:[]},
    {gatepass_type:'Miscellaneous',company_name:'Memory',items:[{item_id:3,quantity:0}]},
    {gatepass_type:'Miscellaneous',company_name:'Memory',items:[{item_id:4,quantity:1}]},
    {...base,date:'2026-02-30'}, {...base,jc_id:[]}, {...base,jc_id:[999]},
  ]){await assert.rejects(service.save('memory',payload));assert.deepEqual(state,before);}
  assert(!calls.some(sql=>/stock_register|job_challan_receives|UPDATE job_challans/.test(sql)),'Gate Pass must never change stock or JC receives');
  const html=backendRequire('./src/modules/gatepass/gatepass.pdf').build({gatepass:{gatepass_no:'GP-1',jc_id:'11',jcs:[{challan_no:'JC-11'}],items:[{item_id:3,item_name:'<Material>',description:'Calculation',quantity:1,unit:'KG',remarks:'<unsafe>'}]}});
  assert(html.includes('Calculation<br>JC No: JC-11'));assert(html.includes('&lt;Material&gt;'));assert(html.includes('size:A5 landscape'));
  console.log('Gate Pass memory transactions passed: numbering, multi-JC duplicate prevention, duplicate material descriptions, edit snapshot preservation, miscellaneous units, invalid dates/company/material/quantity, rollback, stock isolation, PDF escaping and legacy JC fallback. No real database writes.');
})().catch(e=>{console.error(e);process.exitCode=1;});
