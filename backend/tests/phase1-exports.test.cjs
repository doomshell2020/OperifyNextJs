const test=require('node:test'),assert=require('node:assert/strict'),{Writable}=require('node:stream'),ExcelJS=require('exceljs');
const stockExport=require('../src/modules/stockRegister/stockRegister.export');
function response(){const parts=[];const headers={};const res=new Writable({write(chunk,encoding,done){parts.push(chunk);done();}});res.setHeader=(key,value)=>headers[key]=value;return {res,headers,load:async()=>{const book=new ExcelJS.Workbook();await book.xlsx.load(Buffer.concat(parts));return book.worksheets[0];}};}
const row={item_id:9,item_name:'Copper',category_name:'Metal',date_range:'2026-10-06',opening_stock:'10.00',received_stock:'5.00',dispatched_stock:'2.00',issued_stock:'2.00',closing_stock:'13.00'};
test('consolidated export keeps tenant company, item identity and blank physical stock',async()=>{
 const output=response();await stockExport.detailed({query:async sql=>{assert.match(sql,/^SELECT/);return [{ac_holder:'KCPL'}];}},{},[row],output.res);
 const sheet=await output.load();assert.equal(sheet.getCell('A1').value,'KCPL');assert.equal(sheet.getCell('B3').value,9);assert.equal(sheet.getCell('C3').value,'06-10-2026');assert.equal(sheet.getCell('D3').value,'Copper');assert.equal(sheet.getCell('E3').value,'Metal');assert.equal(sheet.getCell('I3').value,13);assert.equal(sheet.getCell('J3').value,'');assert.equal(sheet.columnCount,10);
});
test('single-product export uses the seven PHP columns',async()=>{
 const output=response();await stockExport.detailed({query:async()=>[{ac_holder:'TPPL'}]},{product_id:9},[row],output.res);const sheet=await output.load();assert.equal(sheet.columnCount,7);assert.equal(sheet.getCell('G3').value,13);
});
test('daily export preserves PHP midnight cutoff for reverse/return without adding them twice',async()=>{
 const output=response();await stockExport.daily({query:async sql=>{
  if(sql.includes('sitesettings_details'))return [{ac_holder:'KCPL'}];assert.match(sql,/created < :date/);return [{item_id:9,reverse_qty:2,return_qty:1}];
 }},{date:'2026-10-06'},[row],output.res);const sheet=await output.load();assert.equal(sheet.getCell('B2').value,'Date:06-10-2026');assert.equal(sheet.getCell('A4').value,9);assert.equal(sheet.getCell('D4').value,3);assert.equal(sheet.getCell('G4').value,2);assert.equal(sheet.getCell('H4').value,1);assert.equal(sheet.getCell('I4').value,3);
});
test('product export includes the entire filtered dataset and PHP ledger balance',async()=>{
 const output=response();const db={query:async(sql,args)=>{
  if(sql.includes('COUNT(*)'))return [{total:1}];
  if(sql.includes('FROM st_additem')){assert.ok(!sql.includes('LIMIT :limit'));return [{id:9,item_name:'Copper',category_name:'metal',itemtype:'RawMaterial',uom_name:'KG'}];}
  assert.ok(!sql.includes("status!='N'"));return [{item_id:9,quantity:12.5}];
 }};await require('../src/modules/settings/products.export')(db,{},output.res);const sheet=await output.load();assert.equal(sheet.getCell('C2').value,'Metal');assert.equal(sheet.getCell('F2').value,12.5);assert.equal(sheet.columnCount,6);
});
