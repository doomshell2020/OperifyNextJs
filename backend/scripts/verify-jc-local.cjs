// Existing-record verification only. Refuse every database statement except SELECT/SHOW.
require('../src/config/environment');
const assert=require('node:assert/strict');
const fs=require('node:fs');
const path=require('node:path');
const {getTenantSequelize,centralSequelize}=require('../src/config/sequelize');
const jc=require('../src/modules/jobChallan/jobChallan.service');
const receive=require('../src/modules/jobReceive/jobReceive.service');
const L=require('../src/modules/jobChallan/legacyStore');
async function verify(name) {
  const db=await getTenantSequelize(name),original=db.query.bind(db),queries=[];
  db.query=async(sql,options)=>{
    assert.match(sql.trim(),/^(SELECT|SHOW)\b/i,'Read-only verification must not write');
    const qualified=[...sql.matchAll(/`([a-zA-Z0-9_]+)`\.`/g)].map(match=>match[1]);
    assert(qualified.every(database=>database===name),'All queries must stay in the selected company');
    queries.push(sql);return original(sql,options);
  };
  try {
    const tables=['job_challans','job_challan_items','job_challan_receives','st_stock_register','sub_contractors'];
    const schema={};
    for(const table of tables) schema[table]=(await L.columns(db,table)).map(row=>row.Field);
    const list=await jc.list(name,{limit:50});
    const receipts=await receive.list(name,{limit:50});
    const options=await receive.options(name);
    assert(options.eligible.every(row=>!row.key.includes('|')));
    const [count]=await L.select(db,'SELECT COUNT(*) AS total FROM job_challans');
    assert.equal(list.total,Number(count.total));
    let checkedItems=0,historyRows=0;
    for(const row of list.items.slice(0,12)) {
      const detail=await jc.getDetail(name,row.id);
      const pending=await receive.details(name,{challan_id:String(row.id)});
      const expected=await L.select(db,`SELECT i.item_id,SUM(i.quantity) AS dispatched,
        COALESCE((SELECT SUM(r.received_qty) FROM job_challan_receives r WHERE r.challan_id=:id AND r.item_id=i.item_id),0) AS received
        FROM job_challan_items i WHERE i.challan_id=:id GROUP BY i.item_id`,{id:row.id});
      for(const item of expected) {
        const actual=pending.items.find(line=>Number(line.item_id)===Number(item.item_id));
        assert.equal(Number(actual.original_qty),Number(item.dispatched));
        assert.equal(Number(actual.received_qty),Number(item.received));
        assert.equal(Number(actual.pending_qty),Math.max(0,L.round(Number(item.dispatched)-Number(item.received))));
        checkedItems++;
      }
      assert.equal(detail.source_db,name);
      historyRows+=detail.history.length;
      for(const receipt of detail.history.slice(0,2)) {
        const pdf=await receive.returnPdf(name,receipt.id);
        assert.equal(Number(pdf.receipt.challan_id),Number(row.id));
      }
      const searched=await jc.list(name,{challan_no:row.challan_no,limit:100});
      assert(searched.items.some(item=>Number(item.id)===Number(row.id)));
      const calendar=row.jc_date instanceof Date ? row.jc_date.toISOString().slice(0,10) : String(row.jc_date).slice(0,10);
      const dated=await jc.list(name,{from_date:calendar,to_date:calendar,limit:100});
      assert(dated.items.some(item=>Number(item.id)===Number(row.id)),'Date filter must include the existing selected-day challan');
    }
    await assert.rejects(jc.getDetail(name,1,'other_company'),error=>error.status===403);
    await assert.rejects(receive.details(name,{challan_id:'1|other_company'}),error=>error.status===403);
    await assert.rejects(receive.returnPdf(name,1,'other_company'),error=>error.status===403);
    const [summary]=await L.select(db,"SELECT COUNT(*) AS rows_count,SUM(quantity) AS total_quantity FROM st_stock_register");
    return {company:name,challans:list.total,receiveRows:receipts.total,eligible:options.eligible.length,manualSupported:options.manual_supported,checkedItems,historyRows,queries:queries.length,stockSnapshot:summary,schema};
  } finally { db.query=original;await db.close(); }
}
(async()=>{
  const results=[];
  for(const name of (process.env.JC_TEST_DBS || 'tirupati_tppl,tirupati_kcpl').split(',')) results.push(await verify(name));
  const output=path.resolve(__dirname,'../../tmp/jc-local-readonly-results.json');
  fs.writeFileSync(output,JSON.stringify(results,null,2));
  console.log(JSON.stringify(results.map(({schema,...result})=>result),null,2));
})().catch(error=>{console.error(error);process.exitCode=1;}).finally(()=>centralSequelize.close());
