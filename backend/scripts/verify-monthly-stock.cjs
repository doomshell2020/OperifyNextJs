// Read-only MySQL verification. No tables or stock records are created/changed.
require('../src/config/environment');
const assert = require('node:assert/strict');
const { Writable } = require('node:stream');
const fs = require('node:fs');
const path = require('node:path');
const { execFileSync } = require('node:child_process');
const express = require('express');
const ExcelJS = require('exceljs');
const { QueryTypes } = require('sequelize');
const { getTenantSequelize, centralSequelize } = require('../src/config/sequelize');
const monthly = require('../src/modules/stockRegister/monthlyStock.service');
const monthlyRepository = require('../src/modules/stockRegister/monthlyStock.repository');
const stock = require('../src/modules/stockRegister/stockRegister.service');
const { monthLabel } = require('../src/modules/stockRegister/stockRegister.export');

async function exportRows(db, filters, expected) {
  const chunks=[];
  const response=new Writable({write(chunk,encoding,callback){chunks.push(chunk);callback();}});
  const headers={}; response.setHeader=(key,value)=>{headers[key]=value;};
  await monthly.exportExcel(db,{...filters,page:2,page_size:1},response);
  const bytes=Buffer.concat(chunks);
  const workbook=new ExcelJS.Workbook(); await workbook.xlsx.load(bytes);
  const sheet=workbook.getWorksheet('Stock Register');
  assert.equal(sheet.rowCount-2,expected.length);
  assert.deepEqual(sheet.getRow(2).values.slice(1),['#','Item ID','Month','Product Name','Category','Opening Stock','Received Stock','Dispatched Stock','Closing Stock','Physical Stock']);
  assert.ok(sheet.model.merges.includes('A1:J1'));
  assert.equal(sheet.getRow(1).height,50);
  assert.equal(sheet.getCell('A1').font.size,14);
  assert.equal(sheet.getCell('A2').font.bold,true);
  assert.equal(sheet.autoFilter,`A2:J${Math.max(2,sheet.rowCount)}`);
  for(const [index,row] of expected.entries()) {
    const actual=sheet.getRow(index+3);
    assert.equal(actual.getCell(1).value,index+1);
    assert.equal(actual.getCell(2).value,row.item_id);
    assert.equal(actual.getCell(3).value,monthLabel(row.month));
    assert.equal(actual.getCell(4).value,row.item_name);
    for(const [offset,key] of ['opening_stock','received_stock','dispatched_stock','closing_stock'].entries()) {
      assert.equal(actual.getCell(offset+6).value,Number(row[key]));
      assert.equal(actual.getCell(offset+6).numFmt,'0.000');
    }
    assert.ok(actual.getCell(10).value == null || actual.getCell(10).value==='');
  }
  assert.match(headers['Content-Disposition'],/Month_wise_Stock_Register/);
  return { bytes,sheet };
}

async function allRows(db, filters) {
  const data=await monthlyRepository.getMonthlyStock(db,monthly.normalizeFilters(filters),true);
  assert.equal(new Set(data.map(row=>`${row.month}:${row.item_id}`)).size,data.length);
  return data;
}

// Independent raw-ledger aggregation: prove whole-month quantities, rather
// than accepting a first-day snapshot or sums of opening/closing balances.
async function verifyWholeMonth(db, year, month) {
  const start=`${year}-${String(month).padStart(2,'0')}-01`;
  const end=month===12 ? `${year+1}-01-01` : `${year}-${String(month+1).padStart(2,'0')}-01`;
  const receipt="DATE(COALESCE(sr.delivery_date,sr.issue_date,sr.created))";
  const dispatch="DATE(COALESCE(sr.issue_date,sr.created))";
  const inMonth=`((sr.store_type IN ('0','1','3') AND ${receipt} >= :start AND ${receipt} < :end) OR (sr.store_type IN ('2','4') AND ${dispatch} >= :start AND ${dispatch} < :end))`;
  const expected=await db.query(`SELECT p.id AS item_id,p.item_name,
    ROUND(SUM(CASE WHEN sr.store_type IN ('0','1','3') THEN sr.quantity ELSE 0 END),2) AS received,
    ROUND(SUM(CASE WHEN sr.store_type IN ('2','4') THEN sr.quantity ELSE 0 END),2) AS dispatched,
    ROUND(SUM(CASE WHEN sr.store_type IN ('0','1','3') AND ${receipt}=:start THEN sr.quantity ELSE 0 END),2) AS first_day_received,
    ROUND(SUM(CASE WHEN sr.store_type IN ('2','4') AND ${dispatch}=:start THEN sr.quantity ELSE 0 END),2) AS first_day_dispatched,
    COUNT(DISTINCT CASE WHEN sr.store_type IN ('0','1','3') THEN ${receipt} ELSE ${dispatch} END) AS movement_days
    FROM st_stock_register sr JOIN st_additem p ON p.id=sr.item_id
    WHERE sr.status!='N' AND p.status='Y' AND p.itemtype='RawMaterial' AND p.category_id!=25 AND ${inMonth}
    GROUP BY p.id,p.item_name ORDER BY movement_days DESC,p.id`,{replacements:{start,end},type:QueryTypes.SELECT});
  const rows=await allRows(db,{year,month});
  const actual=new Map(rows.map(row=>[row.item_id,row]));
  for(const product of expected) {
    const row=actual.get(product.item_id);
    assert.equal(Number(row?.received_stock || 0),Number(product.received));
    assert.equal(Number(row?.dispatched_stock || 0),Number(product.dispatched));
  }
  const expectedById=new Map(expected.map(row=>[row.item_id,row]));
  for(const row of rows) {
    const product=expectedById.get(row.item_id);
    assert.equal(Number(row.received_stock),Number(product?.received || 0));
    assert.equal(Number(row.dispatched_stock),Number(product?.dispatched || 0));
  }
  const multiDay=expected.find(row=>Number(row.movement_days)>1 && (Number(row.received)!==Number(row.first_day_received) || Number(row.dispatched)!==Number(row.first_day_dispatched)));
  return {month,productsChecked:rows.length,sample:multiDay || null};
}

async function verifyBoundaries(db, original) {
  const table = (columns,records) => '(' + records.map(record => 'SELECT '+columns.map((column,index)=>`${db.escape(record[index])} AS ${column}`).join(',')).join(' UNION ALL ') + ')';
  const products=table(['id','item_name','itemtype','status','category_id'],[
    [1,'Product A','RawMaterial','Y',1],[2,'Product B','RawMaterial','Y',2],
    [3,'Excluded category','RawMaterial','Y',25],[4,'Inactive','RawMaterial','N',1],[5,'Finished','FinishedProduct','Y',1]
  ]);
  const categories=table(['id','category_name'],[[1,'Category A'],[2,'Category B'],[25,'Excluded']]);
  const movements=table(['item_id','store_type','status','quantity','delivery_date','issue_date','created'],[
    [1,'0','Y',100,null,null,'2023-12-31 23:59:59'],
    [1,'1','Y',20,'2024-01-31 23:59:59','2024-02-01','2024-02-01'],
    [1,'2','Y',5,'2024-01-15','2024-02-01 00:00:00','2024-01-15'],
    [1,'3','R',2,null,'2024-02-29 23:59:59','2024-03-01'],
    [1,'4','Y',3,null,null,'2024-02-29 23:59:59'],
    [1,'1','N',999,'2024-02-01',null,'2024-02-01'],
    [1,'5','Y',999,null,null,'2024-02-01'],[1,'6','Y',999,null,null,'2024-02-01'],
    [1,'1','Y',7,'2024-03-01 00:00:00',null,'2024-02-01'],
    [2,'2','Y',4,null,'2024-02-29','2024-01-01'],
    [3,'0','Y',100,null,null,'2023-12-31'],[4,'0','Y',100,null,null,'2023-12-31'],[5,'0','Y',100,null,null,'2023-12-31']
  ]);
  const fixture={transaction:db.transaction.bind(db),query:(sql,options)=>original(sql.replace(/\bst_additem\b/g,products).replace(/\bst_categorymaster\b/g,categories).replace(/\bst_stock_register\b/g,movements),options)};
  const rows=await allRows(fixture,{year:2024});
  assert.ok(rows.every(row=>row.item_id<=2));
  const a=rows.filter(row=>row.item_id===1);
  assert.deepEqual(a.slice(0,3).map(row=>[row.month,Number(row.opening_stock),Number(row.received_stock),Number(row.dispatched_stock),Number(row.closing_stock)]),[
    ['2024-01',100,20,0,120],['2024-02',120,2,8,114],['2024-03',114,7,0,121]
  ]);
  assert.equal(Number(rows.find(row=>row.item_id===2).closing_stock),-4);
  assert.deepEqual(await allRows(fixture,{year:2024,month:2,category_ids:'1',product_id:2}),[]);
  return rows.length;
}

async function verifyHttp(db,year) {
  const auth=require('../src/modules/auth/auth.service');
  const users=await centralSequelize.query("SELECT mobile FROM users WHERE db=:db AND is_status!='N'",{replacements:{db:db.getDatabaseName()},type:QueryTypes.SELECT});
  let allowed,denied;
  for(const user of users) {
    try {
      const session=await auth.getSession(user.mobile,db.getDatabaseName());
      if(session.permissions.includes('legacy:admin/stockregister/index')) allowed ||= auth._issueTokens(session);
      else denied ||= auth._issueTokens(session);
      if(allowed && denied) break;
    } catch(error) { if(![401,403].includes(error.status)) throw error; }
  }
  const app=express();
  app.use('/stock-register',require('../src/modules/stockRegister/stockRegister.routes'));
  app.use((error,req,res,next)=>res.status(error.status || 500).json({success:false,error:{message:error.message}}));
  const server=await new Promise(resolve=>{const listener=app.listen(0,'127.0.0.1',()=>resolve(listener));});
  const base=`http://127.0.0.1:${server.address().port}/stock-register`;
  try {
    for(const route of ['/monthly','/monthly/options','/monthly/export']) assert.equal((await fetch(base+route)).status,401);
    if(denied) {
      for(const route of ['/monthly','/monthly/options','/monthly/export']) assert.equal((await fetch(base+route,{headers:{Authorization:`Bearer ${denied.accessToken}`}})).status,403);
    }
    if(allowed) {
      const headers={Authorization:`Bearer ${allowed.accessToken}`};
      const options=await fetch(base+'/monthly/options',{headers}); assert.equal(options.status,200);
      assert.ok((await options.json()).data.products.length);
      const result=await fetch(base+`/monthly?year=${year}&page_size=1`,{headers}); assert.equal(result.status,200);
      const body=await result.json(); assert.equal(body.data.length,1); assert.ok(body.pagination.total>1);
      assert.equal((await fetch(base+'/monthly?year=bad',{headers})).status,400);
      const exported=await fetch(base+`/monthly/export?year=${year}&product_id=${body.data[0].item_id}`,{headers});
      assert.equal(exported.status,200); assert.match(exported.headers.get('content-type'),/spreadsheetml/);
      const workbook=new ExcelJS.Workbook(); await workbook.xlsx.load(Buffer.from(await exported.arrayBuffer()));
      assert.ok(workbook.getWorksheet('Stock Register').rowCount>2);
    }
    return {unauthenticated:true,authorized:!!allowed,denied:!!denied};
  } finally {await new Promise(resolve=>server.close(resolve));}
}

// Execute the actual available PHP private method using read-only fixture adapters.
function cakeDaily(products,movements,dateFrom,dateTo) {
  const source=fs.readFileSync(path.resolve(__dirname,'../../operify-cake-php-old-project/src/Controller/Admin/StockregisterController.php'),'utf8');
  const method=source.slice(source.indexOf('    private function _getConsolidatedStock('),source.lastIndexOf('\n}'));
  const php=`namespace Cake\\Datasource { class ConnectionManager { static function get($name) { return new \\Connection; } } }
namespace { $input=json_decode(stream_get_contents(STDIN),true);
class Connection { function execute($sql){return $this;} function fetchAll($type){global $input;return $input['movements'];} }
class Items { function find($type){return $this;} function contain($value){return $this;} function where($value){return $this;} function order($value){return $this;} function toArray(){global $input;return json_decode(json_encode($input['products']));} }
class Report { public $Additem; function __construct(){$this->Additem=new Items;} function loadModel($value){} ${method}
function run($from,$to){return $this->_getConsolidatedStock($from,$to);} }
echo json_encode((new Report)->run($input['from'],$input['to'])); }`;
  return JSON.parse(execFileSync('php',['-r',php],{input:JSON.stringify({products,movements,from:dateFrom,to:dateTo}),maxBuffer:10*1024*1024}).toString());
}

(async()=>{
  const db=await getTenantSequelize(process.env.STOCK_TEST_DB || 'tirupati_tppl');
  const original=db.query.bind(db);
  db.query=(sql,options)=>{assert.match(sql.trim(),/^(SELECT|SHOW|SET|START TRANSACTION|COMMIT|ROLLBACK)\b/i);return original(sql,options);};
  const centralQuery=centralSequelize.query.bind(centralSequelize);
  centralSequelize.query=(sql,options)=>{assert.match(sql.trim(),/^(SELECT|SHOW)\b/i);return centralQuery(sql,options);};
  try {
    const boundaryRows=await verifyBoundaries(db,original);
    for(const invalid of [{},{year:2026,month:13},{year:'2026 OR 1=1'},{year:2026,category_ids:'1 OR 1=1'}, {year:2026,product_id:-1},{year:2026,page:0},{year:2026,page_size:101}]) {
      await assert.rejects(monthly.getMonthlyStock(db,invalid),error=>error.status===400);
    }
    assert.equal(monthLabel('2024-02'),'Feb(1-29)'); assert.equal(monthLabel('2026-02'),'Feb(1-28)');
    const [latest]=await original(`SELECT MAX(DATE(COALESCE(delivery_date,issue_date,created))) AS date FROM st_stock_register WHERE status!='N'`,{type:QueryTypes.SELECT});
    const year=Number(String(latest.date).slice(0,4));
    const wholeMonths=[];
    for(const month of [2,9,12]) wholeMonths.push(await verifyWholeMonth(db,year,month));
    assert.ok(wholeMonths.find(result=>result.month===9).sample,'September must include a product with movements beyond the first day');
    const http=await verifyHttp(db,year);
    const filters={year};
    const annual=await allRows(db,filters); assert.ok(annual.length);
    let reconciled=0;
    const byKey=new Map(annual.map(row=>[`${row.month}:${row.item_id}`,row]));
    for(const row of annual) {
      assert.equal(Number((Number(row.opening_stock)+Number(row.received_stock)-Number(row.dispatched_stock)).toFixed(2)) || 0,Number(row.closing_stock) || 0);
      const [y,m]=row.month.split('-').map(Number);
      const next=`${m===12?y+1:y}-${String(m===12?1:m+1).padStart(2,'0')}:${row.item_id}`;
      if(byKey.has(next)){assert.equal(Number(row.closing_stock),Number(byKey.get(next).opening_stock));reconciled++;}
    }
    const options=await monthly.getOptions(db);
    const ids=[...new Set(annual.map(row=>String(options.products.find(p=>p.id===row.item_id)?.category_id)))].slice(0,2);
    const multi=await allRows(db,{year,category_ids:ids.join(',')});
    const singles=(await Promise.all(ids.map(category_id=>allRows(db,{year,category_id})))).flat();
    const sort=rows=>[...rows].sort((a,b)=>`${a.month}:${a.item_id}`.localeCompare(`${b.month}:${b.item_id}`));
    assert.deepEqual(sort(multi),sort(singles));
    assert.deepEqual(await allRows(db,{year,category_ids:[...ids,ids[0]]}),multi);
    const sampleProducts=[...new Set([
      ...annual.filter(row=>Number(row.received_stock)>0 || Number(row.dispatched_stock)>0).map(row=>row.item_id).slice(0,3),annual[0].item_id
    ])];
    let phpComparisons=0;
    for(const product_id of sampleProducts) {
      const productRows=await allRows(db,{year,product_id});
      assert.deepEqual(productRows,annual.filter(row=>row.item_id===product_id));
      const [product]=await original(`SELECT p.id,p.item_name,p.item_isbn,c.category_name FROM st_additem p LEFT JOIN st_categorymaster c ON c.id=p.category_id WHERE p.id=:product_id`,{replacements:{product_id},type:QueryTypes.SELECT});
      product.itemcategory={category_name:product.category_name};
      const movements=await original(`SELECT item_id,store_type,DATE(COALESCE(delivery_date,issue_date,created)) AS grn_date,DATE(COALESCE(issue_date,created)) AS indent_date,SUM(quantity) AS total_qty FROM st_stock_register WHERE status!='N' AND item_id=:product_id GROUP BY item_id,store_type,grn_date,indent_date`,{replacements:{product_id},type:QueryTypes.SELECT});
      const sampleMonths=[...new Map([productRows[0],productRows.find(row=>Number(row.received_stock)>0),productRows.find(row=>Number(row.dispatched_stock)>0),productRows.at(-1)]
        .filter(Boolean).map(row=>[row.month,row])).values()];
      for(const row of sampleMonths) {
        const [y,m]=row.month.split('-').map(Number);
        const date_from=`${row.month}-01`,date_to=`${row.month}-${new Date(Date.UTC(y,m,0)).getUTCDate()}`;
        const php=cakeDaily([product],movements,date_from,date_to);
        assert.ok(php.length);
        assert.equal(Number(row.opening_stock),php[0].opening);
        assert.equal(Number(row.closing_stock),php.at(-1).closing);
        assert.equal(Number(row.received_stock),Number(php.reduce((sum,r)=>sum+r.received,0).toFixed(2)));
        assert.equal(Number(row.dispatched_stock),Number(php.reduce((sum,r)=>sum+r.dispatched,0).toFixed(2)));
        const existing=await stock.getStockRegister(db,{product_id,date_from,date_to});
        assert.equal(Number(existing[0].opening_stock),Number(row.opening_stock));
        assert.equal(Number(existing.at(-1).closing_stock),Number(row.closing_stock));
        phpComparisons++;
      }
      const december=await allRows(db,{year,month:12,product_id});
      const january=await allRows(db,{year:year+1,month:1,product_id});
      if(december.length && january.length) assert.equal(Number(december[0].closing_stock),Number(january[0].opening_stock));
    }
    const selectedMonth=String(annual.at(-1).month).slice(5);
    const monthRows=await allRows(db,{year,month:selectedMonth});
    assert.deepEqual(monthRows,annual.filter(row=>row.month===`${year}-${selectedMonth}`));
    const paged=[];
    const firstPage=await monthly.getMonthlyStock(db,{year,month:selectedMonth,page_size:100});
    paged.push(...firstPage.data);
    for(let page=2;page<=firstPage.pagination.total_pages;page++) paged.push(...(await monthly.getMonthlyStock(db,{year,month:selectedMonth,page_size:100,page})).data);
    assert.deepEqual(paged,monthRows);
    const small=await monthly.getMonthlyStock(db,{year,page:2,page_size:1});
    assert.deepEqual(small.data,annual.slice(1,2));
    assert.equal(small.pagination.total,annual.length);
    assert.deepEqual((await monthly.getMonthlyStock(db,{year,page:1000000})).data,[]);
    const fullExport=await exportRows(db,filters,annual);
    await exportRows(db,{year,month:selectedMonth},monthRows);
    const productCategory=String(options.products.find(p=>p.id===sampleProducts[0]).category_id);
    await exportRows(db,{year,product_id:sampleProducts[0],category_ids:[productCategory]},annual.filter(row=>row.item_id===sampleProducts[0]));
    const empty=await allRows(db,{year,product_id:Number.MAX_SAFE_INTEGER}); assert.deepEqual(empty,[]);
    await exportRows(db,{year,product_id:Number.MAX_SAFE_INTEGER},empty);
    if(process.env.STOCK_TEST_OUTPUT) {fs.mkdirSync(path.dirname(process.env.STOCK_TEST_OUTPUT),{recursive:true});fs.writeFileSync(process.env.STOCK_TEST_OUTPUT,fullExport.bytes);}
    console.log(JSON.stringify({result:'PASS (read-only)',year,annualRows:annual.length,products:sampleProducts.length,phpComparisons,reconciled,categories:ids,monthRows:monthRows.length,exports:4,boundaryRows,http,wholeMonths}));
  } finally {await db.close();await centralSequelize.close();}
})().catch(error=>{console.error(error);process.exitCode=1;});
