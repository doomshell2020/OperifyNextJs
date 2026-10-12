// Read-only HTTP verification with an existing user's saved role grants.
require('../src/config/environment');
const assert=require('node:assert/strict');
const express=require('express');
const config=require('../src/config/sequelize');
const auth=require('../src/modules/auth/auth.service');
const L=require('../src/modules/jobChallan/legacyStore');
function protect(db){const query=db.query.bind(db);db.query=(sql,options)=>{
  assert.match(sql.trim(),/^(SELECT|SHOW)\b/i,'Read-only HTTP tests must not write database records');return query(sql,options);
};}
(async()=>{
  const name=process.env.JC_TEST_DB || 'tirupati_tppl',db=await config.getTenantSequelize(name);
  protect(db);protect(config.centralSequelize);
  let server;
  try {
    const users=await L.select(config.centralSequelize,"SELECT mobile FROM users WHERE db=:name AND is_status!='N'",{name});
    let session,firstSession;
    const sessionErrors=[];
    for(const user of users) {try {const existing=await auth.getSession(user.mobile,name);firstSession ||= auth._issueTokens(existing);if(existing.permissions.includes('legacy:admin/jobchallan/index')){session=auth._issueTokens(existing);break;}}catch(error){sessionErrors.push(error.message);}}
    if(!session && !firstSession){console.log(JSON.stringify({result:'BLOCKED',reason:'No usable existing local session',sessionErrors:[...new Set(sessionErrors)]}));return;}
    const hasListReader=!!session;
    session ||= firstSession;
    const readonly=express();readonly.use((req,res,next)=>req.method==='GET'?next():res.sendStatus(405));readonly.use(require('../src/app'));
    server=await new Promise(resolve=>{const listener=readonly.listen(0,'127.0.0.1',()=>resolve(listener));});
    const base=`http://127.0.0.1:${server.address().port}/api`,headers={Authorization:`Bearer ${session.accessToken}`};
    assert.equal((await fetch(base+'/job-challan')).status,401);
    if(!hasListReader){assert.equal((await fetch(base+'/job-challan',{headers})).status,403);console.log('Existing-user HTTP denial check passed. Authorized HTTP checks blocked: no existing local user has jobchallan/index. No role grants were changed.');return;}
    const list=await fetch(base+'/job-challan',{headers});assert.equal(list.status,200);
    const body=await list.json();assert.equal(body.success,true);assert(body.data.items.length);
    const id=body.data.items[0].id;
    const tracking=await fetch(base+`/job-challan/${id}/tracking`,{headers});assert.equal(tracking.status,200);
    assert.equal((await tracking.json()).data.source_db,name);
    const routes=[['/jc-receive','receiveindex'],['/jc-receive/options','receiveadd'],[`/jc-receive/details?challan_id=${id}`,'receiveadd'],[`/job-challan/${id}`,'view']];
    for(const [route,grant] of routes) {
      const allowed=session.user.permissions.includes(`legacy:admin/jobchallan/${grant}`) || (route.includes('/details?') && session.user.permissions.includes('legacy:admin/jobchallan/itemreceived'));
      assert.equal((await fetch(base+route,{headers})).status,allowed?200:403,route);
    }
    console.log('Existing-user HTTP checks passed: authentication, local list/tracking, receive options/details and saved action grants. GET only.');
  }finally{if(server)await new Promise(resolve=>server.close(resolve));await db.close();await config.centralSequelize.close();}
})().catch(error=>{console.error(error);process.exitCode=1;});
