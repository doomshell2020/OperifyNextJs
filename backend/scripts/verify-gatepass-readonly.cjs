// SELECT/SHOW only: existing data, saved grants and real HTTP routes. Never call save.
require('../src/config/environment');
const assert=require('node:assert/strict');
const fs=require('node:fs');
const path=require('node:path');
const crypto=require('node:crypto');
const express=require('express');
const config=require('../src/config/sequelize');
const auth=require('../src/modules/auth/auth.service');
const L=require('../src/modules/jobChallan/legacyStore');
const service=require('../src/modules/gatepass/gatepass.service');
const connections=new Set();
function protect(db){if(connections.has(db))return;connections.add(db);const original=db.query.bind(db);db.query=(sql,options)=>{assert.match(sql.trim(),/^(SELECT|SHOW)\b/i,'Verification cannot write database records');return original(sql,options);};}
const digest=rows=>crypto.createHash('sha256').update(JSON.stringify(rows)).digest('hex');
async function snapshot(db){const result={};for(const table of ['gatepasses','gatepass_items','job_challans','job_challan_items','job_challan_receives','st_stock_register'])result[table]=digest(await L.select(db,'SELECT * FROM '+L.identifier(table)+' ORDER BY id'));return result;}
(async()=>{
  protect(config.centralSequelize);
  const folder=path.resolve(__dirname,'../../tmp/gatepass-verification');fs.mkdirSync(folder,{recursive:true});
  const app=express();app.use((req,res,next)=>req.method==='GET'?next():res.sendStatus(405));app.use(require('../src/app'));
  const server=await new Promise(resolve=>{const listener=app.listen(0,'127.0.0.1',()=>resolve(listener));});
  const base=`http://127.0.0.1:${server.address().port}/api/gatepass`;
  const results=[];
  try{
    assert.equal((await fetch(base)).status,401);
    for(const name of ['tirupati_tppl','tirupati_kcpl']){
      const db=await config.getTenantSequelize(name);protect(db);const before=await snapshot(db);
      const list=await service.list(name);assert.equal(list.limit,20);
      const [count]=await L.select(db,'SELECT COUNT(*) AS total FROM gatepasses');assert.equal(list.total,Number(count.total));
      for(const limit of [20,50,100,500])assert.equal((await service.list(name,{limit})).limit,limit);
      await assert.rejects(service.list(name,{page:999999}),e=>e.status===404);
      const vendors=await service.vendors(name);assert(vendors.length<=200);
      const options=await service.options(name);assert(options.multiple_jcs);
      const used=new Set((await L.select(db,'SELECT jc_id FROM gatepasses')).flatMap(row=>String(row.jc_id || '').split(',').filter(Boolean)));
      assert(options.jcs.every(jc=>!used.has(String(jc.id))));
      let materialRows=0;
      for(const row of list.items){
        const detail=await service.detail(name,row.id);assert.equal(detail.gatepass.id,row.id);
        const saved=await L.select(db,'SELECT * FROM gatepass_items WHERE gatepass_id=:id ORDER BY id',{id:row.id});assert.equal(detail.gatepass.items.length,saved.length);materialRows+=saved.length;
        for(let i=0;i<saved.length;i++)for(const key of ['item_id','quantity','unit','description','remarks'])assert.equal(detail.gatepass.items[i][key],saved[i][key]);
        assert((await service.list(name,{search:row.gatepass_no,status:row.status})).items.some(g=>g.id===row.id));
        const filtered=await service.list(name,{sub_contractor_id:row.sub_contractor_id,from_date:row.date,to_date:row.date});assert(filtered.items.some(g=>g.id===row.id));
        assert.equal((await service.list(name,{from_date:'2099-01-01'})).total,list.total,'PHP ignores one-sided date filters');
        const edit=await service.options(name,row.id);assert(String(row.jc_id).split(',').every(id=>edit.jcs.some(j=>String(j.id)===id)));
        const jcData=await service.jcData(name,row.jc_id);const source=await L.select(db,'SELECT item_id,quantity FROM job_challan_items WHERE challan_id IN (:ids) ORDER BY challan_id,id',{ids:String(row.jc_id).split(',')});assert.equal(jcData.items.length,source.length);
        for(let i=0;i<source.length;i++){assert.equal(jcData.items[i].item_id,source[i].item_id);assert.equal(jcData.items[i].quantity,source[i].quantity);}
        if(name==='tirupati_tppl'){
          fs.writeFileSync(path.join(folder,'existing-gatepass.pdf'),await require('../src/modules/gatepass/gatepass.pdf').generate(detail));
          fs.writeFileSync(path.join(folder,'existing-gatepass.html'),require('../src/modules/gatepass/gatepass.pdf').build(detail));
        }
      }
      const users=await L.select(config.centralSequelize,"SELECT mobile FROM users WHERE db=:name AND is_status!='N'",{name});
      let sessions=0,allowed=0,denied=0;const errors=new Set();
      for(const user of users){
        let existing;try{existing=await auth.getSession(user.mobile,name);}catch(e){errors.add(e.message);continue;}
        sessions++;const session=auth._issueTokens(existing),headers={Authorization:`Bearer ${session.accessToken}`};
        const id=list.items[0]?.id;
        const routes=[['','index'],['/vendors','index'],['/options','add'],...(id?[[`/options?edit_id=${id}`,'edit'],[`/${id}`,'view'],[`/${id}/edit-data`,'edit'],[`/${id}/pdf`,'gatepasspdf']]:[])];
        for(const [route,action] of routes){const granted=existing.permissions.includes('legacy:admin/gatepasses/'+action);const response=await fetch(base+route,{headers});assert.equal(response.status,granted?200:403,route);if(granted){allowed++;if(action!=='gatepasspdf')assert.equal((await response.json()).success,true);}else denied++;}
      }
      assert.deepEqual(await snapshot(db),before,'All six module and stock tables must remain identical');
      results.push({company:name,gatepasses:list.total,materialRows,eligibleJcs:options.jcs.length,rawMaterials:options.products.length,multipleJcs:options.multiple_jcs,existingSessions:sessions,httpAllowed:allowed,httpDenied:denied,sessionErrors:[...errors],unchangedTables:Object.keys(before)});
    }
    fs.writeFileSync(path.join(folder,'results.json'),JSON.stringify(results,null,2));console.log(JSON.stringify(results,null,2));
  }finally{await new Promise(resolve=>server.close(resolve));for(const db of connections)await db.close();}
})().catch(e=>{console.error(e);process.exitCode=1;});
