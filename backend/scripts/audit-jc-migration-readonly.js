// Every live SQL call is guarded against writes, including linked company DBs.
const assert=require('node:assert/strict'),path=require('node:path'),fs=require('node:fs');
require('dotenv').config({path:path.join(__dirname,'../.env'),quiet:true});
const {getTenantSequelize,centralSequelize}=require('../src/config/sequelize');
const L=require('../src/modules/jobChallan/legacyStore');
async function run(){
 const connections=new Set();
 const guard=db=>{if(connections.has(db))return;const query=db.query.bind(db);db.query=(sql,options)=>{assert.match(typeof sql==='string'?sql:sql.query,/^\s*(SELECT|SHOW)\b/i,'Audit attempted a write');return query(sql,options);};connections.add(db);};
 guard(centralSequelize);const dbName=process.env.AUDIT_TENANT || 'tirupati_tppl',db=await getTenantSequelize(dbName);guard(db);
 for(const row of await L.linked(dbName))guard(await getTenantSequelize(row.database_name));
 const server=require('../src/app').listen(0);await new Promise(r=>server.once('listening',r));
 const token=require('jsonwebtoken').sign({id:1,db:dbName,role_id:101,permissions:[]},process.env.JWT_SECRET || 'super_secret_key',{expiresIn:'15m'});
 const api=async(route,params={},status=200)=>{const response=await fetch(`http://127.0.0.1:${server.address().port}/api`+route+'?'+new URLSearchParams(params),{headers:{Authorization:`Bearer ${token}`}});const body=await response.json();assert.equal(response.status,status,JSON.stringify(body));return body.data;};
 const evidence={tenant:dbName,checks:[],schema:[]};
 try{
  for(const table of ['job_challans','job_challan_items','job_challan_receives','gatepasses','gatepass_items','st_stock_register'])evidence.schema.push({table,fields:(await L.columns(db,table)).map(c=>({name:c.Field,type:c.Type}))});
  const list=await api('/job-challan'),[total]=await L.select(db,'SELECT COUNT(*) AS total FROM job_challans');assert.equal(list.total,Number(total.total));assert.equal(list.limit,50);
  if(list.items.length){const row=list.items[0],detail=await api('/job-challan/'+row.id);assert.equal(detail.challan.id,row.id);assert.equal(detail.challan.final_amount,Number(row.total_amount || 0)+Number(row.gst_amount || 0));assert.ok(detail.history);assert.ok((await api('/job-challan',{challanNo:row.challan_no})).items.every(i=>i.challan_no.includes(row.challan_no)));assert.ok((await api('/job-challan',{vendorId:row.sub_contractors_id})).items.every(i=>String(i.sub_contractors_id)===String(row.sub_contractors_id)));assert.ok((await api('/job-challan',{status:row.status})).items.every(i=>i.status===row.status));if(process.env.AUDIT_PDF==='1')fs.writeFileSync(path.join(__dirname,'../../tmp/pdfs/jc-existing.pdf'),await require('../src/modules/jobChallan/jobChallan.pdf').generateJobChallanPDF(detail));}
  if(list.total>50){const next=await api('/job-challan',{page:2});assert.ok(!next.items.some(row=>list.items.some(first=>first.id===row.id)));}
  await api('/job-challan',{page:999999},404);evidence.checks.push('JC count, pagination, details, totals, history, number/company/status filters');
  const gateList=await api('/gatepass'),[gateCount]=await L.select(db,'SELECT COUNT(*) AS total FROM gatepasses');assert.equal(gateList.total,Number(gateCount.total));assert.equal(gateList.limit,20);
  const opts=await api('/gatepass/options');assert.ok(opts.products);
  if(gateList.items.length){const row=gateList.items[0],detail=await api('/gatepass/'+row.id);assert.equal(detail.gatepass.id,row.id);assert.ok((await api('/gatepass',{search:row.gatepass_no})).items.every(i=>i.gatepass_no.includes(row.gatepass_no)));await api('/gatepass/options',{edit_id:row.id});if(process.env.AUDIT_PDF==='1')fs.writeFileSync(path.join(__dirname,'../../tmp/pdfs/gatepass-existing.pdf'),await require('../src/modules/gatepass/gatepass.pdf').generate(detail));}evidence.checks.push('Getpass count, default page size, details, items, search, edit options');
  const receives=await api('/jc-receive');assert.ok(receives.items.every(row=>Number(row.pending_qty)===Math.max(0,L.round(Number(row.dispatch_qty)-Number(row.total_received)))));const options=await api('/jc-receive/options');
  for(const jc of options.eligible.slice(0,3)){const detail=await api('/jc-receive/details',{challan_id:jc.key});assert.ok(detail.items.some(row=>Number(row.pending_qty)>0));}
  evidence.checks.push('Receive list, manual receipts, balances, products/units, eligible incoming JCs');
  await api('/job-challan/1',{sender_db:'unlinked_database'},403);await api('/jc-receive/details',{challan_id:'1|unlinked_database'},403);evidence.checks.push('Unlinked company access rejected');
  const [receipt]=await L.select(db,'SELECT id FROM job_challan_receives WHERE challan_id IS NOT NULL ORDER BY id DESC LIMIT 1');
  if(receipt){const detail=await require('../src/modules/jobReceive/jobReceive.service').returnPdf(dbName,receipt.id);if(process.env.AUDIT_PDF==='1')fs.writeFileSync(path.join(__dirname,'../../tmp/pdfs/return-existing.pdf'),await require('../src/modules/jobReceive/jobReceive.pdf').generate(detail));evidence.checks.push('Return challan existing data');}
  assert.equal((await fetch(`http://127.0.0.1:${server.address().port}/api/gatepass`)).status,401);evidence.checks.push('Unauthenticated API rejected');
  if(process.env.AUDIT_BROWSER==='1')await require('./audit-jc-migration-browser')({server,token,dbName,list,receives,options});
  fs.writeFileSync(path.join(__dirname,'../../tmp/jc-migration-readonly-'+dbName+'.json'),JSON.stringify(evidence,null,2));console.log(JSON.stringify({...evidence,schema:undefined,jcs:list.total,gatepasses:gateList.total,receive_rows:receives.total,multiple_jcs:opts.multiple_jcs},null,2));
 }finally{await new Promise(resolve=>server.close(resolve));for(const conn of connections)await conn.close();}
}
run().catch(error=>{console.error(error.stack);process.exitCode=1;});
