// Pure and mocked service tests. Never connects to a database or sends messages.
const assert=require('node:assert/strict');
const R=require('../backend/src/modules/maintenance/maintenance.repository');
const S=require('../backend/src/modules/maintenance/maintenance.service');
const P=require('../backend/src/modules/maintenance/maintenance.pdf');
const {legacyMaintenancePermissions:grants}=require('../backend/src/modules/auth/legacyMaintenanceAccess');
const key=a=>'legacy:admin/maintenance/'+a;
const body={datefrom:'20-10-2026',machines_id:'7',breakdown_type:'Bearing',total_time:'3',assigned_to:'1',shift_incharge:'2',maintenance_incharge:'3',production_head:'4',remark:'Broken 😀 motor'};
const fields=['id','machine_id','breakdown_type','assigned_to','total_time','shift_incharge','maintenance_incharge','production_head','remark','datefrom','updated','status','assigned_date','completion_date','completion_time','remarks'].map(Field=>({Field,Type:'text'})).concat({Field:'maintenance_status',Type:"enum('pending','assigned','complete','completed')"});
function database({current='pending',schema=fields,exists=true,person=true}={}) {
  const calls=[];
  return {calls,transaction:fn=>fn({mock:true}),query:async(sql,options={})=>{
    calls.push({sql,options});
    if(sql.startsWith('SHOW'))return schema;
    if(sql.includes('FROM permission_module'))return [{edit:'1',delete:'1'}];
    if(sql.includes('FROM maintenance m'))return exists?[{id:10,machine_id:7,maintenance_status:current,status:'Y'}]:[];
    if(sql.includes('FROM machine_master'))return exists?[{id:7}]:[];
    if(sql.includes('FROM users'))return person?[{id:1}]:[];
    if(sql.startsWith('INSERT'))return [99,1];
    if(sql.startsWith('UPDATE'))return [undefined,1];
    throw Error('Unexpected query '+sql);
  }};
}
async function main(){
  const where=R.conditions({machines_id:'7',assigned_to:"1' OR 1=1 --",m_status:'assigned',datefrom:'20-10-2026',dateto:'21-10-2026'});
  assert(!where.sql.includes('OR 1=1'));assert.equal(where.values.assigned,"%1' OR 1=1 --%");assert.equal(where.values.datefrom,'2026-10-20');assert.match(where.sql,/DATE\(m.datefrom\)>=/);assert.match(where.sql,/DATE\(m.datefrom\)<=/);
  for(const q of [{machines_id:'-1'},{datefrom:'31-02-2026'},{m_status:'unknown'}])assert.throws(()=>R.conditions(q));
  assert.deepEqual(grants([],[]),[]);
  assert(grants([],[key('index')]).includes(key('edit')));
  assert(!grants([{url:'admin/maintenance/edit'}],[key('index')]).includes(key('edit')));
  assert(grants([{url:'admin/maintenance/edit'}],[key('index'),key('edit')]).includes(key('edit')));
  assert.equal(S.payload(body).remark,'Broken  motor');
  assert.equal(R.timestamp(new Date('2026-10-19T20:00:00Z')),'2026-10-20 01:30:00');
  for(const change of [{total_time:'a'},{total_time:'1.5'},{remark:''},{datefrom:'2026-02-30'},{breakdown_type:[]},{assigned_to:''}])assert.throws(()=>S.payload({...body,...change}));
  for(const next of ['pending','assigned'])assert.throws(()=>S.transition('completed',next),/Cannot change/);
  // The source controller checks the literal 'completed', not the older 'complete' value.
  S.transition('complete','pending');
  assert.throws(()=>S.transition('assigned','pending'),/Cannot change/);
  for(const [current,next] of [['pending','assigned'],['pending','completed'],['assigned','completed'],['completed','completed']])S.transition(current,next);
  let db=database();const result=await S.save(db,body);assert.equal(result.id,99);assert.equal(db.calls.filter(c=>c.sql.startsWith('INSERT')).length,1);assert.match(db.calls.at(-1).sql,/machine_id/);
  db=database();await S.save(db,{...body,machines_id:'8'},10);assert(!db.calls.at(-1).sql.includes('machine_id='));assert.equal(db.calls.at(-1).options.replacements.id,10);
  assert.match(db.calls.at(-1).options.replacements.updated,/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/);
  db=database({schema:fields.map(f=>f.Field==='updated'?{...f,Type:'date'}:f)});await S.save(db,body,10);assert.equal(db.calls.at(-1).options.replacements.updated,R.today());
  db=database({person:false});await assert.rejects(S.save(db,body),/selection/);assert(!db.calls.some(c=>/^(INSERT|UPDATE)/.test(c.sql)));
  db=database({exists:false});await assert.rejects(S.remove(db,10),/not found/);
  db=database();await S.remove(db,10);assert.match(db.calls.at(-1).sql,/SET status='N'/);assert(!db.calls.some(c=>/^DELETE/.test(c.sql)));
  db=database();await S.changeStatus(db,10,{status:'assigned'});assert.match(db.calls.at(-1).sql,/assigned_date=:assigned_date/);
  db=database({current:'assigned'});await S.changeStatus(db,10,{completion_date:'2026-10-20',completion_time:'2 hrs',remarks:'Repaired'},true);assert.equal(db.calls.at(-1).options.replacements.status,'completed');assert.match(db.calls.at(-1).sql,/completion_time/);
  db=database({current:'assigned'});await assert.rejects(S.changeStatus(db,10,{status:'pending'}),/Cannot change/);assert(!db.calls.some(c=>/^UPDATE/.test(c.sql)));
  const oldSchema=fields.filter(f=>!['assigned_date','completion_date','completion_time','remarks'].includes(f.Field)).map(f=>f.Field==='maintenance_status'?{...f,Type:"enum('pending','assigned','complete')"}:f);
  for(const complete of [false,true]){db=database({schema:oldSchema});await assert.rejects(S.changeStatus(db,10,complete?{completion_date:'2026-10-20',completion_time:'1',remarks:'Done'}:{status:'assigned'},complete),e=>e.code==='MAINTENANCE_SCHEMA_MISMATCH');assert(!db.calls.some(c=>/^UPDATE/.test(c.sql)));}
  const html=P.html([{...body,datefrom:'2026-10-20',machine_name:'<script>alert(1)</script>',maintenance_status:'pending'}],{datefrom:'2026-10-20'});assert(!html.includes('<script>'));assert(html.includes('&lt;script&gt;'));assert(html.includes('20-10-2026'));assert.equal(P.headers.length,11);
  // Exercise notification payloads with in-memory providers and an ephemeral key.
  const envNames=['MAINTENANCE_WHATSAPP_URL','MAINTENANCE_WHATSAPP_KEY','MAINTENANCE_FIREBASE_CLIENT_EMAIL','MAINTENANCE_FIREBASE_PRIVATE_KEY','MAINTENANCE_FIREBASE_PROJECT_ID','MAINTENANCE_FIREBASE_CREDENTIALS','GOOGLE_APPLICATION_CREDENTIALS'];
  const saved=envNames.map(k=>process.env[k]),originalFetch=global.fetch,captured=[];
  try{
    envNames.forEach(k=>delete process.env[k]);const N=require('../backend/src/modules/maintenance/maintenance.notifications');
    assert.equal((await N.send({query:()=>assert.fail('Unconfigured providers must not query recipients')},1)).length,2);
    process.env.MAINTENANCE_WHATSAPP_URL='https://example.invalid/whatsapp';process.env.MAINTENANCE_WHATSAPP_KEY='test-key';
    process.env.MAINTENANCE_FIREBASE_CLIENT_EMAIL='test@example.invalid';process.env.MAINTENANCE_FIREBASE_PROJECT_ID='test-project';
    process.env.MAINTENANCE_FIREBASE_PRIVATE_KEY=require('node:crypto').generateKeyPairSync('rsa',{modulusLength:2048}).privateKey.export({type:'pkcs8',format:'pem'});
    global.fetch=async(url,options)=>{captured.push({url:String(url),options});return {ok:true,json:async()=>({access_token:'mock-token'})};};
    const notificationsDb={query:async sql=>sql.includes('users_device')?[{token:'mock-device'}]:sql.includes('sitesettings_details')?[{company_name:'Test Company'}]:[{datefrom:'2026-10-20',machine_name:'M1',user_name:'Test Assignee',mobile:'0000000000',total_time:'3',remark:'Repair',maintenance_incharge:'3'}]};
    assert.deepEqual(await N.send(notificationsDb,1),[]);assert.equal(captured.length,3);
    const whatsapp=JSON.parse(captured.find(r=>r.url.includes('example.invalid')).options.body);assert.equal(whatsapp.campaignName,'operify_machine_maintenance_request');assert.deepEqual(whatsapp.templateParams,['Test Company','M1','Test Assignee','20-10-2026','3','Repair']);
    const push=JSON.parse(captured.find(r=>r.url.includes('fcm.googleapis.com')).options.body);assert.equal(push.message.token,'mock-device');assert.match(push.message.notification.body,/added by:3 for Machine:M1/);
    assert.deepEqual(push.message.data,{key1:'value1',key2:'value2'});
    const fs=require('node:fs'),path=require('node:path'),os=require('node:os');
    const credentialFile=path.join(os.tmpdir(),'maintenance-test-'+require('node:crypto').randomUUID()+'.json');
    try{
      fs.writeFileSync(credentialFile,JSON.stringify({project_id:process.env.MAINTENANCE_FIREBASE_PROJECT_ID,client_email:process.env.MAINTENANCE_FIREBASE_CLIENT_EMAIL,private_key:process.env.MAINTENANCE_FIREBASE_PRIVATE_KEY}));
      delete process.env.MAINTENANCE_FIREBASE_PROJECT_ID;delete process.env.MAINTENANCE_FIREBASE_CLIENT_EMAIL;delete process.env.MAINTENANCE_FIREBASE_PRIVATE_KEY;process.env.MAINTENANCE_FIREBASE_CREDENTIALS=credentialFile;
      captured.length=0;assert.deepEqual(await N.send(notificationsDb,1),[]);assert(captured.some(r=>r.url.includes('/projects/test-project/messages:send')));
      global.fetch=async()=>({ok:false});assert.equal((await N.send(notificationsDb,1)).length,2,'Provider failures must return warnings rather than fail the saved record');
    }finally{fs.unlinkSync(credentialFile);}
  }finally{global.fetch=originalFetch;envNames.forEach((k,i)=>{if(saved[i]===undefined)delete process.env[k];else process.env[k]=saved[i];});}
  // Exercise real HTTP route guards while replacing auth/tenant with in-memory middleware.
  const authPath=require.resolve('../backend/src/middleware/auth'),tenantPath=require.resolve('../backend/src/middleware/tenant');
  require.cache[authPath]={id:authPath,filename:authPath,loaded:true,exports:(req,res,next)=>{req.user={id:1,permissions:JSON.parse(req.headers['x-test-permissions'] || '[]')};next();}};
  let httpDb;
  require.cache[tenantPath]={id:tenantPath,filename:tenantPath,loaded:true,exports:(req,res,next)=>{const mode=req.headers['x-test-fixture'];req.dbPool=mode?(httpDb=database({schema:mode==='old'?oldSchema:fields,current:['assigned','completed'].includes(mode)?mode:'pending'})):{query:async()=>[{edit:'0',delete:'0'}]};next();}};
  const app=require('../backend/node_modules/express')();app.use(require('../backend/node_modules/express').json());app.use(require('../backend/src/modules/maintenance/maintenance.routes'));
  app.use((error,req,res,next)=>res.status(error.status || 500).json({success:false,error:{code:error.code,message:error.message}}));
  const server=app.listen(0,'127.0.0.1');await new Promise(resolve=>server.once('listening',resolve));
  try{const base='http://127.0.0.1:'+server.address().port;for(const [method,url] of [['GET','/'],['POST','/'],['GET','/pdf'],['GET','/metadata'],['GET','/machines'],['GET','/10'],['PUT','/10'],['DELETE','/10'],['PATCH','/10/status'],['POST','/10/complete']])assert.equal((await fetch(base+url,{method})).status,403,method+' '+url);
    for(const [method,action] of [['GET','edit'],['PUT','edit'],['DELETE','status']])assert.equal((await fetch(base+'/10',{method,headers:{'x-test-permissions':JSON.stringify([key(action)])}})).status,403,'Per-user flag required');
    const send=async(method,url,action,payload,fixture='full')=>{
      const response=await fetch(base+url,{method,headers:{'Content-Type':'application/json','x-test-permissions':JSON.stringify([key(action)]),'x-test-fixture':fixture},...(payload?{body:JSON.stringify(payload)}:{})});
      return {status:response.status,body:await response.json()};
    };
    const notifications=require('../backend/src/modules/maintenance/maintenance.notifications'),originalSend=notifications.send;notifications.send=async()=>[];
    try{
      assert.equal((await send('POST','/','add',body)).status,200);assert(httpDb.calls.some(c=>c.sql.startsWith('INSERT')));
      assert.equal((await send('PUT','/10','edit',body)).status,200);assert(httpDb.calls.some(c=>c.sql.startsWith('UPDATE')));
      assert.equal((await send('DELETE','/10','status')).status,200);assert.match(httpDb.calls.at(-1).sql,/status='N'/);
      assert.equal((await send('PATCH','/10/status','maintenancestatus',{status:'assigned'})).status,200);assert.equal(httpDb.calls.at(-1).options.replacements.assigned_date,R.today());
      assert.equal((await send('PATCH','/10/status','maintenancestatus',{status:'pending'},'assigned')).status,409);assert(!httpDb.calls.some(c=>c.sql.startsWith('UPDATE')));
      assert.equal((await send('PATCH','/10/status','maintenancestatus',{status:'assigned'},'completed')).status,409);
      const completion={completion_date:'2026-10-20',completion_time:'2 hrs',remarks:'Repaired'};
      assert.equal((await send('POST','/10/complete','completemaintenance',{...completion,remarks:''})).status,400);assert(!httpDb.calls.some(c=>c.sql.startsWith('UPDATE')));
      const missing=await send('POST','/10/complete','completemaintenance',completion,'old');assert.equal(missing.status,409);assert.equal(missing.body.error.code,'MAINTENANCE_SCHEMA_MISMATCH');assert(!httpDb.calls.some(c=>c.sql.startsWith('UPDATE')));
      // After manual provisioning, the same route succeeds without a frontend/schema cache gate.
      assert.equal((await send('POST','/10/complete','completemaintenance',completion)).status,200);assert.deepEqual(httpDb.calls.at(-1).options.replacements,{id:10,status:'completed',...completion});
    }finally{notifications.send=originalSend;}
  }finally{await new Promise(resolve=>server.close(resolve));}
  console.log('Maintenance: query validation, legacy filters, status rules, mocked CRUD, schema guards, PDF escaping and HTTP permission checks passed. No database access.');
}
main().catch(e=>{console.error(e);process.exitCode=1;});
