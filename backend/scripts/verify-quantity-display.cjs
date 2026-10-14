// Presentation checks only: every database query is SELECT/SHOW. No saves.
require('../src/config/environment');
const assert=require('node:assert/strict');
const fs=require('node:fs');
const path=require('node:path');
const vm=require('node:vm');
const Module=require('node:module');
const {Writable}=require('node:stream');
const {Sequelize,QueryTypes}=require('sequelize');
const ExcelJS=require('exceljs');
const {formatQty,formatAmt}=require('../src/utils/formatters');
const {formatQuantityColumns}=require('../src/utils/quantityExcel');
const root=path.resolve(__dirname,'../..'),front=path.join(root,'frontend');
const frontendRequire=Module.createRequire(path.join(front,'package.json'));
const ts=frontendRequire('typescript');
const cache=new Map();
function loadTs(file) {
  const filename=/\.tsx?$/.test(file)?file:fs.existsSync(file+'.tsx')?file+'.tsx':file+'.ts';
  if(cache.has(filename))return cache.get(filename).exports;
  const mod=new Module(filename,module);mod.filename=filename;mod.paths=Module._nodeModulePaths(path.dirname(filename));cache.set(filename,mod);
  mod.require=name=>name.startsWith('@/')?loadTs(path.join(front,name.slice(2))):name.startsWith('.')?loadTs(path.resolve(path.dirname(filename),name)):frontendRequire(name);
  const code=ts.transpileModule(fs.readFileSync(filename,'utf8'),{compilerOptions:{module:ts.ModuleKind.CommonJS,jsx:ts.JsxEmit.ReactJSX,esModuleInterop:true}}).outputText;
  mod._compile(code,filename);return mod.exports;
}
const Q=3135.6700439453125,day='2026-10-14';
const item={id:1,item_id:1,item_name:'Quantity audit item',uom:'KG',unit_name:'KG',unit:'KG',quantity:Q,order_qty:Q,pending_qty:Q,item_qty:Q,
  km_item_qty:Q,planned_qty:Q,prepared_qty:Q,as_per_design:Q,total_issued:Q,issued_qty:Q,rate:12.34,price:100.12,cost_price:100.12,amount:123.45,tax:18.12,tax_amt:18.12,tax_amount:18.12,tax_rate:18,tax_percentage:18,hsn_code:'1234'};
const common={reverse_id:'Q-1',contract_name:'Quantity audit',workorder:'Q1',product_name:item.item_name,machine_name:'Audit',received_name:'Audit',issue_date:day,items:[item],site_details:{},sitesetting:{}};
function pdfDocuments() {
  return [
    ['contract',require('../src/modules/contract/contract.pdf').buildContractHtml({...common,contract:{workorder:'Q1',title:'Audit',cost:123.45},items:[{...item,has_production:true,processes:[{quantity:Q}],raw_materials:[{...item,issued_items:[item]}]}],productionOrders:[{plannedqty:Q,prepared_qty:Q}]})],
    ['design-sheet',require('../src/modules/designsheet/designsheet.pdf').buildDesignSheetHtml({...common,designsheet:{designsheetno:'Q1',quantity:Q,datefrom:day},designsheetdetails:[item]})],
    ['indent',require('../src/modules/indent/indent.pdf').buildIndentHtml(common,'Q1','Audit')],
    ['indentpo',require('../src/modules/indentpo/indentpo.pdf').buildIndentpoHtml(common)],
    ['reverse',require('../src/modules/reverseIndent/reverseIndent.pdf').buildReverseIndentHtml(common)],
    ['grn',require('../src/modules/grn/grn.pdf').buildGrnHtml({...common,grn:{grn_id:'Q1',freight:0}})],
    ['po-details',require('../src/modules/purchaseOrder/purchaseOrder.details.pdf').buildDetailsHtml({...common,po:{po_number:'Q1'},grns:[{id:'Q1',items:[item]}],schedules:[{item_id:1,item_qty:Q,delivery_date:day}]})],
    ['gatepass',require('../src/modules/gatepass/gatepass.pdf').build({...common,gatepass:{gatepass_no:'Q1',date:day,items:[item]}})],
    ['subcontractor-jc',require('../src/modules/jobChallan/jobChallan.subcontractor.pdf').buildSubcontractorChallanHtml({...common,challan:{challan_no:'Q1',jc_date:day,job_challan_items:[item]}})],
    ['jc-return',require('../src/modules/jobReceive/jobReceive.pdf').build({...common,receipt:{...item,id:1,received_qty:Q,receive_date:day},challan:{challan_no:'Q1'},sender_site:{}})],
  ];
}
async function mainJcHtml() {
  // Exercise the real generator while capturing its HTML instead of opening a
  // second renderer; render and inspect that exact HTML below.
  const filename=path.join(root,'backend/src/modules/jobChallan/jobChallan.pdf.js'),exports={exports:{}};
  const localRequire=Module.createRequire(filename);
  vm.runInNewContext(fs.readFileSync(filename,'utf8'),{module:exports,exports:exports.exports,require:name=>name==='../../utils/legacyPdf'?{...localRequire(name),render:async html=>html}:localRequire(name),console});
  return exports.exports.generateJobChallanPDF({...common,challan:{challan_no:'Q1',jc_date:day,vendor:{},job_challan_items:[item]}});
}
async function capture(fn) {
  const chunks=[];
  const res=new Writable({write(chunk,encoding,next){chunks.push(chunk);next();}});
  res.setHeader=()=>{};res.send=buffer=>res.end(buffer);
  await fn(res);
  if(!res.writableEnded)res.end();
  if(!res.writableFinished)await new Promise(resolve=>res.once('finish',resolve));
  const book=new ExcelJS.Workbook();await book.xlsx.load(Buffer.concat(chunks));return book;
}
function checkExcel(name,book,columns,start=2) {
  const sheet=book.worksheets[0];let checked=0;
  for(let row=start;row<=sheet.rowCount;row++)for(const column of columns) {
    const cell=sheet.getRow(row).getCell(column);
    if(typeof cell.value==='number'){assert.equal(cell.numFmt,'0.000',`${name} ${cell.address}`);checked++;}
  }
  assert.ok(checked,`${name}: numeric quantities must be present`);return checked;
}
async function run() {
  const db=new Sequelize(process.env.STOCK_TEST_DB || 'tirupati_tppl',process.env.DB_USER || 'root',process.env.DB_PASSWORD || '',{host:process.env.DB_HOST,dialect:'mysql',logging:false});
  const original=db.query.bind(db);db.query=(sql,options)=>{assert.match(sql.trim(),/^(SELECT|SHOW)\b/i);return original(sql,options);};
  let browser;
  try {
    const frontendFormat=loadTs(path.join(front,'utils/formatters')).formatQty;
    for(const [value,expected] of [[Q,'3135.670'],[0,'0.000'],[3100,'3100.000'],['1.2346','1.235'],[-2.5,'-2.500'],[null,'0.000'],[NaN,'0.000'],[Infinity,'0.000']]) {
      assert.equal(formatQty(value),expected);assert.equal(frontendFormat(value),expected);
    }
    assert.equal(formatAmt(123.4567),'123.46');
    const sample=new ExcelJS.Workbook(),sampleSheet=sample.addWorksheet('Numeric');sampleSheet.columns=[{key:'qty',header:'Qty'},{key:'amount',header:'Amount'}];
    formatQuantityColumns(sampleSheet,['qty']);sampleSheet.addRow({qty:Q,amount:123.45});
    const roundtrip=new ExcelJS.Workbook();await roundtrip.xlsx.load(await sample.xlsx.writeBuffer());
    assert.equal(roundtrip.worksheets[0].getCell('A2').value,Q);assert.equal(roundtrip.worksheets[0].getCell('A2').numFmt,'0.000');assert.notEqual(roundtrip.worksheets[0].getCell('B2').numFmt,'0.000');
    const documents=pdfDocuments();documents.push(['main-jc',await mainJcHtml()]);
    process.env.NEXT_PUBLIC_API_URL ||= 'http://localhost:4000/api';
    const React=frontendRequire('react'),{renderToStaticMarkup}=frontendRequire('react-dom/server');
    const {QueryClient,QueryClientProvider}=frontendRequire('@tanstack/react-query');
    const poData={po:{id:1,po_number:'Q1',total_amount:123.45},items:[item],site_details:{},schedules:[{id:1,item_id:1,item_qty:Q,delivery_date:day}],grns:[{id:1,items:[item]}]};
    const Print=loadTs(path.join(front,'components/dashboard/PrintPurchaseOrder')).PrintPurchaseOrder;
    for(const mode of ['current','revised','delivery']) {
      const queryClient=new QueryClient();
      const html=renderToStaticMarkup(React.createElement(QueryClientProvider,{client:queryClient},React.createElement(Print,{poId:1,documentData:poData,printMode:mode,onClose:()=>{}})));
      documents.push(['po-'+mode,html]);queryClient.clear();
    }
    const Details=loadTs(path.join(front,'components/PurchaseOrderDetailsPrint')).PurchaseOrderDetailsPrint;
    documents.push(['po-details-react',renderToStaticMarkup(React.createElement(Details,{data:poData,standalone:true}))]);
    const Receiving=loadTs(path.join(front,'components/ReceivingDetailsTable')).ReceivingDetailsTable;
    documents.push(['receiving-table',renderToStaticMarkup(React.createElement(Receiving,{items:[item]}))]);
    browser=await require('../src/utils/pdfBrowser').launchPdfBrowser();const page=await browser.newPage();
    await page.setRequestInterception(true);page.on('request',request=>/^https?:/.test(request.url())?request.abort():request.continue());
    const folder=path.join(root,'tmp/quantity-verification');fs.mkdirSync(folder,{recursive:true});
    for(const [name,html] of documents) {
      assert.ok(html.includes('3135.670'),name);assert.ok(!html.includes(String(Q)),`${name}: unformatted quantity`);
      await page.setContent(html,{waitUntil:'domcontentloaded'});
      const text=await page.$eval('body',body=>body.innerText);assert.ok(text.includes('3135.670'),`${name}: visible quantity`);
      const pdf=Buffer.from(await page.pdf({format:'A4',printBackground:true}));assert.equal(pdf.subarray(0,5).toString(),'%PDF-');fs.writeFileSync(path.join(folder,name+'.pdf'),pdf);
      if(['po-current','reverse','grn'].includes(name))await page.screenshot({path:path.join(folder,name+'.png'),fullPage:true});
    }
    const excelChecks={};
    const po=await require('../src/modules/purchaseOrder/purchaseOrder.export').workbook(db,{type:'po',po_number:'2627-409'});
    excelChecks.po=checkExcel('PO',po,[10,11,12]);
    const grnBuffer=await require('../src/modules/grn/grn.service').exportGrnsToExcel(db,{}),grn=new ExcelJS.Workbook();await grn.xlsx.load(grnBuffer);excelChecks.grn=checkExcel('GRN',grn,[9,10,11]);
    const inspection=await capture(res=>require('../src/modules/grnInspection/grnInspection.controller').exportInspections({dbPool:db},res,error=>{throw error;}));excelChecks.inspection=checkExcel('Inspection',inspection,[8]);
    const indentpo=await capture(res=>require('../src/modules/indentpo/indentpo.export')(db,{},res));excelChecks.indentpo=checkExcel('IndentPO',indentpo,[7]);
    const reverse=await capture(res=>require('../src/modules/reverseIndent/reverseIndent.controller').exportExcel({dbPool:db,query:{}},res,error=>{throw error;}));excelChecks.reverse=checkExcel('Reverse',reverse,[7]);
    const products=await capture(res=>require('../src/modules/settings/products.export')(db,{search:'COPPER'},res));excelChecks.products=checkExcel('Products',products,[6]);
    const stockExport=require('../src/modules/stockRegister/stockRegister.export');
    const stockRows=[{item_id:1,item_name:'Quantity audit',date_range:day,month:'2026-10',opening_stock:Q,received_stock:Q,dispatched_stock:Q,issued_stock:Q,closing_stock:Q}];
    const detailed=await capture(res=>stockExport.detailed(db,{},stockRows,res));excelChecks.stock=checkExcel('Stock',detailed,[6,7,8,9],3);
    const daily=await capture(res=>stockExport.daily(db,{date:day,category_ids:['1']},stockRows,res));excelChecks.daily=checkExcel('Daily',daily,[4,5,6,7,8,9],4);
    const monthly=await capture(res=>stockExport.monthly(db,{year:2026},stockRows,res));excelChecks.monthly=checkExcel('Monthly',monthly,[6,7,8,9],3);
    for(const [name,book] of [['po',po],['grn',grn],['inspection',inspection],['indentpo',indentpo],['reverse',reverse],['products',products],['stock',detailed],['daily',daily],['monthly',monthly]])await book.xlsx.writeFile(path.join(folder,name+'.xlsx'));
    console.log(JSON.stringify({result:'PASS',pdfTemplates:documents.length,excelChecks,formulaPrecision:'unchanged numeric values',database:'SELECT/SHOW only',artifacts:folder}));
  } finally {if(browser)await browser.close();await db.close();}
}
run().catch(error=>{console.error(error.stack);process.exitCode=1;});
