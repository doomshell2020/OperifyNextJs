const assert=require('node:assert/strict');
const {PassThrough}=require('node:stream');
const R=require('../backend/src/modules/productionOrder/productionOrder.repository');
const S=require('../backend/src/modules/productionOrder/productionOrder.service');
const X=require('../backend/src/modules/productionOrder/productionOrder.export');
const ExcelJS=require('../backend/node_modules/exceljs');
const P=require('../backend/src/modules/productionOrder/productionOrder.pdf');
const state={orders:[],production:[],required:'1,8'};
const calls=[];
const db={transaction:async(options,run)=>{if(typeof options==='function')run=options;const before=structuredClone(state);try{return await run({test:true});}catch(e){Object.assign(state,before);throw e;}},query:async(sql,options={})=>{
  calls.push({sql,...options});const v=options.replacements || {};
  if(sql.startsWith('SELECT id FROM sitesettings'))return [{id:1}];
  if(sql.startsWith('SELECT id FROM contracts'))return v.id===7?[{id:7}]:[];
  if(sql.startsWith('SELECT po_id FROM productionorder'))return state.orders.length?[{po_id:state.orders.at(-1).po_id}]:[];
  if(sql.includes('FROM bom_finisedproduct b'))return [{id:10,product_id:9,quantity:'20',pendingqty:20-state.orders.reduce((n,o)=>n+Number(o.plannedqty),0),unit_name:'KM'}];
  if(sql.startsWith('INSERT INTO productionorder')){assert(options.transaction);const id=state.orders.length+1;state.orders.push({id,...v,status:'O'});return [id];}
  if(sql.startsWith('SELECT * FROM productionorder'))return state.orders.filter(o=>o.id===v.id);
  if(sql.startsWith('SELECT productprocess_id FROM st_additem'))return [{productprocess_id:state.required}];
  if(sql.startsWith('SELECT DISTINCT productprocess_id FROM production'))return state.production.filter(p=>p.po_id===v.po_id && p.item_id===v.item_id && v.required.includes(p.productprocess_id));
  if(sql.startsWith('SELECT id,process_name FROM finishedproduct_process'))return [{id:1,process_name:'Drawing'},{id:8,process_name:'Sheathing'}];
  if(sql.startsWith('UPDATE productionorder')){assert(options.transaction);const row=state.orders.find(o=>o.id===v.id);row.status=v.status;row.complete_date=sql.includes('CURRENT_DATE()')?'2026-10-20':null;return [1];}
  if(sql.startsWith('SELECT id FROM production'))return state.production.filter(p=>p.po_id===v.po_id).slice(0,1);
  if(sql.startsWith('DELETE FROM productionorder')){assert(options.transaction);state.orders=state.orders.filter(o=>o.id!==v.id);return [1];}
  throw new Error('Unexpected test SQL: '+sql);
}};
(async()=>{
  assert.equal(R.date('20-10-2026'),'2026-10-20');assert.throws(()=>R.date('2026-02-30'));
  assert.throws(()=>R.conditions({contract_id:'1 OR 1=1'}));assert.throws(()=>R.conditions({status:'X'}));
  const where=R.conditions({contract_id:'7',status:'C',datefrom:'20-10-2026',dateto:'21-10-2026'});
  assert(where.sql.includes('DATE(o.complete_date)=:datefrom'));assert(!where.sql.includes('>='));assert(where.sql.includes('DATE(o.complete_date)<=:dateto'));
  assert.deepEqual(where.values,{contract_id:7,status:'C',datefrom:'2026-10-20',dateto:'2026-10-21'});
  assert.equal(R.order({},true),'o.id DESC, o.id DESC');assert.equal(R.order({}),'o.po_id DESC, o.id DESC');assert(!R.order({sort:'plannedqty;DROP TABLE productionorder'}).includes('DROP'));
  assert.equal(S.paging({}).limit,50);assert.equal(S.paging({limit:500}).limit,100);assert.throws(()=>S.paging({page:Number.MAX_SAFE_INTEGER}));
  const payload={po_id:'1001',issuedate:'2026-10-20',contract_id:7,items:[{bom_id:10,item_id:9,plannedqty:'12.50',startdate:'2026-10-20',enddate:'2026-10-22'}]};
  await S.create(db,payload);assert.equal(state.orders[0].totaldays,'3');assert.equal(state.orders[0].uom,'KM');
  let before=structuredClone(state);await assert.rejects(S.create(db,payload),/number has changed/);assert.deepEqual(state,before);
  await assert.rejects(S.create(db,{...payload,po_id:'1002',items:[{...payload.items[0],plannedqty:'8.00'}]}),/greater/);assert.deepEqual(state,before);
  await assert.rejects(S.create(db,{...payload,po_id:'1002',items:[{...payload.items[0],plannedqty:'1.00',enddate:'2026-10-19'}]}),/negative/);assert.deepEqual(state,before);
  await assert.rejects(S.changeStatus(db,1,'C'),/Drawing, Sheathing/);assert.equal(state.orders[0].status,'O');
  state.production=[{po_id:'1001',item_id:9,productprocess_id:1,production_shift_a:0},{po_id:'1001',item_id:9,productprocess_id:8,production_shift_a:1}];
  await S.changeStatus(db,1,'C');assert.equal(state.orders[0].status,'C');assert.equal(state.orders[0].complete_date,'2026-10-20');
  await assert.rejects(S.remove(db,1),/Only open/);
  await S.changeStatus(db,1,'O');assert.equal(state.orders[0].complete_date,null);
  await assert.rejects(S.remove(db,1),/no production entries/);
  state.production=[];state.required='';await assert.rejects(S.changeStatus(db,1,'C'),/No process/);
  await S.remove(db,1);assert.equal(state.orders.length,0);
  const stream=new PassThrough(),chunks=[];stream.on('data',chunk=>chunks.push(chunk));stream.type=()=>stream;stream.set=()=>stream;
  const base={po_id:'1001',title:'Contract <A>',workorder:'WO-1',item_name:'Cable',issuedate:'2026-10-20',startdate:'2026-10-21',enddate:'2026-10-22',plannedqty:'10.25',prepared_qty:12.50,status:'O'};
  const pages=[];const exportDb={transaction:async(options,run)=>run({snapshot:true}),query:async(sql,options)=>{const {offset,limit}=options.replacements;assert(options.transaction.snapshot);assert.equal(options.replacements.contract_id,7);pages.push(offset);return Array.from({length:Math.max(0,Math.min(limit,2505-offset))},(_,i)=>({...base,id:offset+i,po_id:String(1001+offset+i)}));}};
  await X.excel(exportDb,{contract_id:'7'},stream);
  const workbook=new ExcelJS.Workbook();await workbook.xlsx.load(Buffer.concat(chunks));const sheet=workbook.worksheets[0];
  assert.equal(sheet.rowCount,2506);assert.deepEqual(pages,[0,1000,2000]);assert.deepEqual(sheet.getRow(1).values.slice(1),X.headers);assert.equal(sheet.getCell('B2').value,'20-10-2026');assert.equal(sheet.getCell('H2').value,12.5);assert.equal(sheet.getCell('I2').value,-2.25);assert.equal(sheet.getCell('I2').numFmt,'0.00');assert.equal(sheet.getCell('J2').value,'Open');
  assert.deepEqual(X.values({...base,plannedqty:'1.235',prepared_qty:0.111}).slice(6,9),[1.24,0.11,1.12]);
  assert.equal(X.values({...base,prepared_qty:1.005})[7],1.01);
  const html=P.html({order:base,quantity:20,pending_qty:-2.25,processes:[],materials:[]});assert(html.includes('Contract &lt;A&gt;'));assert.equal((html.match(/-2.25 KM/g)||[]).length,2);
  console.log('Production Orders tests passed: date/filter validation, default/export sort, pagination, inclusive days, stale numbering, pending limits, transactional rollback, required processes, close/reopen, protected deletion, 2,505-row streaming XLSX with filtered batches, negative pending quantities, date/number formatting, and PDF escaping. No real database writes.');
})().catch(error=>{console.error(error);process.exitCode=1;});
