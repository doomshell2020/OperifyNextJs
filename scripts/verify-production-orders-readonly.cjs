// Run against an explicit company database. All queries are SELECTs.
const assert=require('node:assert/strict');
const {getTenantSequelize,centralSequelize}=require('../backend/src/config/sequelize');
const R=require('../backend/src/modules/productionOrder/productionOrder.repository');
const S=require('../backend/src/modules/productionOrder/productionOrder.service');
const X=require('../backend/src/modules/productionOrder/productionOrder.export');
const {PassThrough}=require('node:stream');
const ExcelJS=require('../backend/node_modules/exceljs');
(async()=>{const pools=[];try{const names=process.argv.slice(2);assert(names.length,'Pass a tenant DB name');for(const name of names){assert(/^[a-zA-Z0-9_]+$/.test(name));const db=await getTenantSequelize(name);pools.push(db);const before=await R.select(db,'SELECT * FROM productionorder ORDER BY id');
  const old=[];
  for(const row of before){const [contract]=await R.select(db,'SELECT title,workorder FROM contracts WHERE id=:id',{id:row.contract_id});const [item]=await R.select(db,'SELECT item_name FROM st_additem WHERE id=:id',{id:row.item_id});const daily=await R.select(db,'SELECT * FROM production WHERE po_id=:po_id AND productprocess_id=8 ORDER BY id',{po_id:row.po_id});const started=await R.select(db,'SELECT id FROM production WHERE po_id=:po_id',{po_id:row.po_id});old.push({...row,...contract,...item,prepared_qty:daily.reduce((sum,p)=>sum+Number(p.production_shift_a || 0)+Number(p.production_shift_b || 0),0),production_started:started.length?1:0});}
  const criteria=[{}, {status:'O'}, {status:'C'}, {datefrom:'20-10-2026'}, {dateto:'20-10-2026'},...before.map(o=>({contract_id:String(o.contract_id)}))];
  for(const filters of criteria){const expected=old.filter(o=>(!filters.contract_id || String(o.contract_id)===filters.contract_id) && (!filters.status || o.status===filters.status) && (!filters.datefrom || String(o.complete_date).slice(0,10)===R.date(filters.datefrom)) && (!filters.dateto || o.complete_date && String(o.complete_date).slice(0,10)<=R.date(filters.dateto)));
    const actual=await S.list(db,filters);assert.equal(actual.total,expected.length);assert.deepEqual(actual.items.map(o=>o.id).sort(),expected.map(o=>o.id).sort());for(const row of actual.items){const source=old.find(o=>o.id===row.id);assert.equal(Number(row.prepared_qty),source.prepared_qty);assert.equal(Number(row.production_started),source.production_started);}
    const stream=new PassThrough(),chunks=[];stream.on('data',v=>chunks.push(v));stream.type=()=>stream;stream.set=()=>stream;await X.excel(db,filters,stream);const book=new ExcelJS.Workbook();await book.xlsx.load(Buffer.concat(chunks));assert.equal(book.worksheets[0].rowCount,expected.length+1);const exportRows=await R.rows(db,filters,undefined,undefined,true);exportRows.forEach((row,i)=>assert.deepEqual(book.worksheets[0].getRow(i+2).values.slice(1),X.values(row)));
  }
  for(const row of before)await S.detail(db,row.po_id);
  await S.form(db,before[0]?.contract_id);
  if(before.length){const printable=await S.detail(db,before[0].po_id);[printable.site_details]=await R.select(db,"SELECT * FROM sitesettings_details WHERE status='Y' LIMIT 1");[printable.sitesetting]=await R.select(db,'SELECT * FROM sitesettings LIMIT 1');const pdf=await require('../backend/src/modules/productionOrder/productionOrder.pdf').generate(printable);assert.equal(pdf.subarray(0,5).toString(),'%PDF-');}

  assert.deepEqual(await R.select(db,'SELECT * FROM productionorder ORDER BY id'),before);
  console.log(name+': legacy helper-query parity, '+criteria.length+' filter cases, listing/calculations, Excel column/value parity, details/form queries, and unchanged records passed ('+before.length+' orders).');
}}finally{await Promise.all([...new Set([centralSequelize,...pools])].map(db=>db.close()));}})().catch(error=>{console.error(error);process.exitCode=1;});
