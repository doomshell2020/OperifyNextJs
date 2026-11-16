// Isolated business-rule and route tests: no database connection or writes.
const assert=require('node:assert/strict');
const fs=require('node:fs'),vm=require('node:vm'),path=require('node:path');
const {createRequire}=require('node:module');
const {PassThrough}=require('node:stream');
const R=require('../backend/src/modules/dailySheet/dailySheet.repository');
const S=require('../backend/src/modules/dailySheet/dailySheet.service');
const X=require('../backend/src/modules/dailySheet/dailySheet.export');
const {legacyDailySheetPermissions:grants}=require('../backend/src/modules/auth/legacyDailySheetAccess');
const backendRequire=createRequire(path.resolve('backend/package.json'));
const ExcelJS=backendRequire('exceljs');
const body={production_date:'2026-10-20',contract_id:1,po_id:1001,item_id:2,machine_id:3,productprocess_id:8,plan_qty:'10',production_shift_a:'4.125',production_shift_b:'5.875',reading8am:'10',reading8pm:'11',nextday8am:'12',manpower_day:'2',manpower_night:'3',scrap:'0',remark:'Check ☀ 😀',completed:'Y'};
function mock(options={}) {
  const writes=[];
  const db={writes,transaction:async(a,b)=>(b || a)({mock:true}),query:async(sql,opts={})=>{
    if(/^(INSERT|UPDATE|DELETE)/.test(sql)){writes.push({sql,values:opts.replacements});return [];}
    if(sql==='SHOW COLUMNS FROM production')return options.noCompleted?[]:[{Field:'is_completed'}];
    if(sql.includes('SELECT id,process_name FROM'))return [];
    if(sql.includes('SELECT id,machine_name,status'))return [];
    if(sql.includes('SELECT p.*,m.machine_name'))return [{...body,id:7}];
    if(sql.includes('SELECT * FROM productionorder'))return [{plannedqty:options.plannedqty ?? 10}];
    if(sql.includes('SELECT id FROM machine_master'))return [{id:3}];
    if(sql.includes('SELECT productprocess_id FROM st_additem'))return [{productprocess_id:options.noProcesses?'':'8,9'}];
    if(sql.includes('SELECT id FROM finishedproduct_process'))return [{id:8}];
    if(sql.includes('SELECT DISTINCT productprocess_id'))return options.missing?[{productprocess_id:8}]:[{productprocess_id:8},{productprocess_id:9}];
    if(sql.includes('SELECT process_name FROM'))return [{process_name:'Pending process'}];
    if(sql.includes('SELECT production_shift_a'))return options.totals || [];
    if(sql.includes('SELECT plannedqty'))return [{plannedqty:10}];
    if(sql.includes('production_date=:today'))return options.duplicate?[{id:1}]:[];
    if(sql.includes('SELECT id FROM production WHERE production_date'))return options.noDateRow?[]:[{id:7}];
    throw new Error('Unmocked SQL: '+sql);
  }};return db;
}
async function routeTests(){
  const filename=path.resolve('backend/src/modules/dailySheet/dailySheet.routes.js');
  const stub=()=>{},proxy=new Proxy(stub,{get:()=>proxy});const mod={exports:{}};
  vm.runInNewContext(fs.readFileSync(filename,'utf8'),{module:mod,require(name){
    if(name==='express')return backendRequire(name);
    if(name.endsWith('/legacyPermission'))return require('../backend/src/modules/jobChallan/legacyPermission');
    if(name.endsWith('/middleware/permission'))return require('../backend/src/middleware/permission');
    return proxy;
  }});
  let checks=0;
  for(const layer of mod.exports.stack.filter(l=>l.route)){
    const route=layer.route,method=Object.keys(route.methods)[0];
    const action=route.path==='/dependencies'?'add':route.path.startsWith('/datewise')?method==='post'?'add_datewise_dailysheet':'edit_datewise_dailysheet':route.path==='/:id'?method==='delete'?'deletedailysheet':'edit':method==='post'?'add':'index';
    const publicHelpers=['/metadata','/lookup','/dependencies'].includes(route.path);
    for(const role_id of [101,105])for(const allowed of [false,true]){
      const req={user:{role_id,permissions:allowed?['legacy:admin/production/'+action]:[]}};
      let status=200;
      for(const layer of route.stack.slice(0,-1)){
        let next=false;const res={status(code){status=code;return this;},json(){}};
        layer.handle(req,res,()=>{next=true;});if(!next)break;
      }
      const restricted=route.path==='/:id';
      // Helpers have any-of scopes, tested with index where applicable.
      const expected=allowed && (!restricted || role_id===105)?200:403;
      assert.equal(status,expected,method+' '+route.path+' role '+role_id+' grant '+allowed+' helper '+publicHelpers);checks++;
    }
  }return checks;
}
(async()=>{
  const template=fs.readFileSync(path.resolve('operify-cake-php-old-project/src/Template/Admin/Production/dailysheetexcel.ctp'),'utf8');
  assert.deepEqual(X.headers,[...template.matchAll(/setCellValue\('[A-N]1', '([^']+)'\)/g)].map(m=>m[1]));
  assert.equal(R.today(new Date('2026-10-20T19:00:00Z')),'2026-10-21');
  assert.match(R.conditions({datefrom:'01-10-2026'}).sql,/production_date\)=/);
  assert.match(R.conditions({datefrom:'01-10-2026',dateto:'20-10-2026'}).sql,/production_date\)>=/);
  assert.equal(R.conditions({machines_id:3,po_id:1001}).values.machines_id,3);
  for(const q of [{po_id:'1 OR 1=1'},{datefrom:'31-02-2026'},{datefrom:'2026-10-21',dateto:'2026-10-20'}])assert.throws(()=>R.conditions(q));
  assert.equal(R.order({sort:'id; DROP TABLE production',direction:'asc;'}),'p.id DESC,p.id DESC');
  for(const patch of [{reading8pm:'9'},{nextday8am:'9'},{production_shift_a:'-1'},{manpower_day:'NaN'}])assert.throws(()=>S.normalize({...body,...patch},body.production_date));
  let db=mock();await S.save(db,body);assert.equal(db.writes.length,1);assert.equal(db.writes[0].values.status,'C');assert.equal(db.writes[0].values.is_completed,'Y');assert.equal(db.writes[0].values.remark,'Check  ');
  db=mock({plannedqty:20,noCompleted:true});await S.save(db,body);assert.equal(db.writes[0].values.status,'O');assert(!('is_completed' in db.writes[0].values));
  for(const opts of [{missing:true},{noProcesses:true}]){db=mock(opts);await assert.rejects(S.save(db,body),/process/i);assert.equal(db.writes.length,0);}
  db=mock({duplicate:true});await assert.rejects(S.save(db,body,7),/already exists/);assert.equal(db.writes.length,0);
  db=mock();await S.save(db,body,7);assert.equal(db.writes[0].values.status,'C');assert(!('is_completed' in db.writes[0].values));
  db=mock();await S.saveDatewise(db,{items:[{...body,plan_qty:''},body]});assert.equal(db.writes.length,1);assert.equal(db.writes[0].values.production_shift_a,null);assert(!('status' in db.writes[0].values));assert.equal(db.writes[0].values.is_completed,null);
  db=mock();await S.saveDatewise(db,{items:[body]},'2026-10-19');assert.equal(db.writes[0].values.production_shift_a,'4.125');assert.equal(db.writes[0].values.id,7);assert(!('status' in db.writes[0].values));
  db=mock({noDateRow:true});await assert.rejects(S.saveDatewise(db,{items:[body]},'2026-10-19'),/No Daily Sheet/);assert.equal(db.writes.length,0);
  db=mock();await S.remove(db,7);assert.match(db.writes[0].sql,/productiondetails/);assert.match(db.writes[1].sql,/DELETE FROM production WHERE/);
  const key=a=>'legacy:admin/production/'+a;
  assert.deepEqual(grants([],[]),[]);assert(grants([],[key('add')]).includes(key('add_datewise_dailysheet')));assert(grants([],[key('index')]).includes(key('edit_datewise_dailysheet')));
  assert(!grants([{url:'admin/production/edit_datewise_dailysheet'}],[key('index')]).includes(key('edit_datewise_dailysheet')));
  const fixture={...body,machine_name:'Machine',title:'Contract',workorder:'WO',item_name:'Product',process_name:'Process'};
  const data=Array.from({length:1003},(_,i)=>({...fixture,id:1003-i,po_id:1001+i})),batches=[];
  const exportDb={transaction:async(a,fn)=>fn({}),query:async(sql,opts)=>{batches.push(opts.replacements.offset);return data.slice(opts.replacements.offset,opts.replacements.offset+opts.replacements.limit);}};
  const stream=new PassThrough(),chunks=[];stream.on('data',c=>chunks.push(c));stream.type=()=>stream;stream.set=()=>stream;
  await X.excel(exportDb,{},stream);assert.deepEqual(batches,[0,1000]);const book=new ExcelJS.Workbook();await book.xlsx.load(Buffer.concat(chunks));const sheet=book.worksheets[0];assert.equal(sheet.rowCount,1004);assert.deepEqual(sheet.getRow(1).values.slice(1),X.headers);assert.deepEqual(sheet.getRow(2).values.slice(1),X.values(data[0]));assert.equal(sheet.getRow(2).getCell(7).numFmt,'0.00');assert.equal(sheet.getRow(2).getCell(1).value,'20-10-2026');assert.equal(sheet.getRow(2).getCell(10).value,10);assert.equal(sheet.getRow(2).getCell(14).value,5);
  console.log(`Daily Sheet isolated tests passed: filtering, injection rejection, readings, legacy process gate, status, missing Completed column, single/date-wise saves, delete relationship, Excel round-trip across 1003 rows, scoped permission aliases and ${await routeTests()} route checks. No database writes.`);
})().catch(e=>{console.error(e);process.exitCode=1;});
