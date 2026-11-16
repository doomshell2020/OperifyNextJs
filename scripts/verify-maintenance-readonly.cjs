// SELECT/SHOW queries only. Reproduce PHP's LEFT JOIN and per-user name lookups.
const assert=require('node:assert/strict');
const {centralSequelize,getTenantSequelize}=require('../backend/src/config/sequelize');
const R=require('../backend/src/modules/maintenance/maintenance.repository');
const S=require('../backend/src/modules/maintenance/maintenance.service');
const P=require('../backend/src/modules/maintenance/maintenance.pdf');
async function main(){const connections=[centralSequelize];try{
  const names=process.argv.slice(2);assert(names.length,'Pass tenant database names.');
  for(const name of names){assert(/^[a-zA-Z0-9_]+$/.test(name));const db=await getTenantSequelize(name);connections.push(db);
    const before=await R.select(db,'SELECT * FROM maintenance ORDER BY id');
    const users=await R.select(db,'SELECT id,user_name,role_id FROM users');const machines=await R.select(db,'SELECT id,machine_name,status FROM machine_master');
    const active=before.filter(r=>r.status==='Y').sort((a,b)=>String(b.datefrom).localeCompare(String(a.datefrom)) || b.id-a.id);
    const cases=[{}, {datefrom:R.today()}, {dateto:R.today()}, {m_status:'pending'}, {m_status:'assigned'}, {m_status:'completed'}, {m_status:'complete'},...active.slice(0,12).flatMap(r=>[{machines_id:r.machine_id},{assigned_to:String(r.assigned_to || '')},{datefrom:String(r.datefrom).slice(0,10),dateto:String(r.datefrom).slice(0,10)}])];
    for(const q of cases){const expected=active.filter(r=>(!q.machines_id || String(r.machine_id)===String(q.machines_id)) && (!q.assigned_to || String(r.assigned_to).toLowerCase().includes(q.assigned_to.toLowerCase())) && (!q.m_status || r.maintenance_status===q.m_status) && (!q.datefrom || String(r.datefrom).slice(0,10)>=q.datefrom) && (!q.dateto || String(r.datefrom).slice(0,10)<=q.dateto));
      const result=await S.list(db,q);assert.equal(result.total,expected.length,JSON.stringify(q));assert.deepEqual(result.items.map(r=>r.id),expected.slice(0,50).map(r=>r.id));
      for(const r of result.items){assert.equal(r.machine_name,machines.find(m=>m.id===r.machine_id)?.machine_name ?? null);for(const [key,alias] of [['assigned_to','assigned_name'],['shift_incharge','shift_name'],['maintenance_incharge','incharge_name'],['production_head','production_name']])assert.equal(r[alias],users.find(u=>Number(u.id)===Number(r[key]))?.user_name ?? null);}
      const pdfRows=await R.rows(db,q);assert.deepEqual(pdfRows.map(r=>r.id),expected.map(r=>r.id));assert.equal(P.values(pdfRows[0] || {},0).length,11);P.html(pdfRows,q);
    }
    const page2=await S.list(db,{page:2});assert.deepEqual(page2.items.map(r=>r.id),active.slice(50,100).map(r=>r.id));
    const meta=await R.metadata(db,users[0]?.id || 1);
    for(const [group,roles] of [['assigned',[104,23]],['shifts',[111]],['incharges',[104,112]],['heads',[102]]])assert.deepEqual(meta[group].map(r=>r.id),users.filter(u=>roles.includes(Number(u.role_id))).sort((a,b)=>a.id-b.id).map(r=>r.id));
    await R.machines(db,'a');for(const r of active.slice(0,5))await S.detail(db,r.id);
    assert.deepEqual(await R.select(db,'SELECT * FROM maintenance ORDER BY id'),before);
    console.log(`${name}: ${cases.length} list/PDF parity cases, role dropdowns, page 2, lookups and unchanged ${before.length} records passed. Assignment supported: ${meta.schema.can_assign}; completion supported: ${meta.schema.can_complete}.`);
  }
}finally{await Promise.all([...new Set(connections)].map(db=>db.close()));}}
main().catch(e=>{console.error(e);process.exitCode=1;});
