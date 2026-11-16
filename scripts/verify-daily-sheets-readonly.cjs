// SELECT/SHOW only. Never creates test rows or invokes mutation services.
const assert=require('node:assert/strict');
const {PassThrough}=require('node:stream');
const {getTenantSequelize,centralSequelize}=require('../backend/src/config/sequelize');
const R=require('../backend/src/modules/dailySheet/dailySheet.repository');
const S=require('../backend/src/modules/dailySheet/dailySheet.service');
const X=require('../backend/src/modules/dailySheet/dailySheet.export');
const ExcelJS=require('../backend/node_modules/exceljs');
async function run(){
  const connections=[];
  try{
    const names=process.argv.slice(2);assert(names.length,'Pass tenant database names');
    const labels=await R.select(centralSequelize,"SELECT url FROM permission_label WHERE url LIKE 'admin/production/%'");
    console.log('Production permission catalogue:',labels.map(r=>r.url).join(', '));
    for(const name of names){
      assert(/^[a-zA-Z0-9_]+$/.test(name));const db=await getTenantSequelize(name);connections.push(db);
      const before=await R.select(db,'SELECT * FROM production ORDER BY id');
      const children=await R.select(db,'SELECT * FROM productiondetails ORDER BY id');
      const orders=await R.select(db,'SELECT * FROM productionorder ORDER BY id');
      // Reproduce the old contain(Machinemaster) and per-row helper lookups.
      const old=[];
      for(const row of before){
        const [machine]=await R.select(db,'SELECT * FROM machine_master WHERE id=:id',{id:row.machine_id});if(!machine)continue;
        const [contract]=await R.select(db,'SELECT * FROM contracts WHERE id=:id',{id:row.contract_id});
        const [product]=await R.select(db,'SELECT * FROM st_additem WHERE id=:id',{id:row.item_id});
        const [process]=await R.select(db,'SELECT * FROM finishedproduct_process WHERE id=:id',{id:row.productprocess_id});
        old.push({...row,machine_name:machine.machine_name,title:contract?.title,workorder:contract?.workorder,item_name:product?.item_name,process_name:process?.process_name});
      }
      const meta=await R.metadata(db);assert.match(meta.today,/^\d{4}-\d{2}-\d{2}$/);
      const cases=[{}, {datefrom:meta.today},{datefrom:meta.today,dateto:meta.today},{dateto:meta.today},...old.slice(0,20).flatMap(r=>[{po_id:r.po_id},{contract_id:r.contract_id},{item_id:r.item_id},{machines_id:r.machine_id},{productprocess_id:r.productprocess_id}])];
      for(const query of cases){
        const where=R.conditions(query);
        const expected=old.filter(row=>Object.entries({po_id:'po_id',contract_id:'contract_id',item_id:'item_id',machines_id:'machine_id',productprocess_id:'productprocess_id'}).every(([key,col])=>!query[key] || String(row[col])===String(query[key])) && (!query.datefrom || (query.dateto?X.values(row)[0].split('-').reverse().join('-')>=R.date(query.datefrom):X.values(row)[0].split('-').reverse().join('-')===R.date(query.datefrom))) && (!query.dateto || X.values(row)[0].split('-').reverse().join('-')<=R.date(query.dateto))).sort((a,b)=>b.id-a.id);
        const result=await S.list(db,query);assert.equal(result.total,expected.length);
        assert.deepEqual(result.items.map(r=>r.id),expected.slice(0,50).map(r=>r.id));
        for(const row of result.items){const source=expected.find(r=>r.id===row.id);assert.equal(row.total_production,Number(source.production_shift_a || 0)+Number(source.production_shift_b || 0));assert.equal(row.total_manpower,Number(source.manpower_day || 0)+Number(source.manpower_night || 0));}
        const stream=new PassThrough(),chunks=[];stream.on('data',c=>chunks.push(c));stream.type=()=>stream;stream.set=()=>stream;
        await X.excel(db,query,stream);const book=new ExcelJS.Workbook();await book.xlsx.load(Buffer.concat(chunks));const sheet=book.worksheets[0];assert.deepEqual(sheet.getRow(1).values.slice(1),X.headers);assert.equal(sheet.rowCount,expected.length+1);
        expected.forEach((row,i)=>assert.deepEqual(sheet.getRow(i+2).values.slice(1).map(v=>v ?? ''),X.values(row).map(v=>v ?? '')));
      }
      for(const kind of ['contracts','products','machines']) await R.lookup(db,{kind,fetch:'a'});
      await R.dependencies(db,{});await S.datewise(db,meta.today);
      for(const order of orders.slice(0,10))await R.dependencies(db,{contract_id:order.contract_id,item_id:order.item_id,po_id:order.po_id});
      assert.deepEqual(await R.select(db,'SELECT * FROM production ORDER BY id'),before);
      assert.deepEqual(await R.select(db,'SELECT * FROM productiondetails ORDER BY id'),children);
      assert.deepEqual(await R.select(db,'SELECT * FROM productionorder ORDER BY id'),orders);
      console.log(`${name}: ${cases.length} filter/export parity cases, lookups, dependent dropdown queries, date-wise query and unchanged records passed. ${before.length} production rows; ${meta.machines.length} machines; Completed column: ${meta.has_completed}.`);
    }
  }finally{await Promise.all([...new Set([...connections,centralSequelize])].map(db=>db.close()));}
}
run().catch(e=>{console.error(e);process.exitCode=1;});
