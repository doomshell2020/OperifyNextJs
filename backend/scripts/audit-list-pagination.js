/* Read-only parity audit: never creates fixtures or changes database records. */
const assert = require('node:assert/strict');
const path = require('node:path');
require('dotenv').config({path:path.join(__dirname,'../.env'),quiet:true});
const {QueryTypes}=require('sequelize');
const jwt=require('jsonwebtoken');
const {getTenantSequelize,centralSequelize}=require('../src/config/sequelize');
const app=require('../src/app');
async function run() {
 const db=await getTenantSequelize(process.env.AUDIT_TENANT || 'tirupati_tppl');
 const originalQuery=db.query.bind(db);
 db.query=(sql,options)=>{ assert.match(typeof sql==='string'?sql:sql.query,/^\s*(SELECT|SHOW)\b/i,'Audit attempted a database mutation');return originalQuery(sql,options); };
 const server=app.listen(0);await new Promise(r=>server.once('listening',r));
 const base=`http://localhost:${server.address().port}/api`;
 const token=jwt.sign({id:1,db:process.env.AUDIT_TENANT || 'tirupati_tppl',role_id:101,permissions:[]},process.env.JWT_SECRET||'super_secret_key',{expiresIn:'1h'});
 const sql=(query,replacements={})=>db.query(query,{replacements,type:QueryTypes.SELECT});
 const get=async(route,params={})=>{const res=await fetch(base+route+'?'+new URLSearchParams(params),{headers:{Authorization:`Bearer ${token}`}});const body=await res.json();return {status:res.status,body};};
 const entries=[
  {route:'/contracts',table:'contracts',where:'1=1',vendor:'supplier_id',identity:'contract_id',column:'id',date:'contract_start_date',from:'datefrom',to:'dateto',end:'contract_end_date'},
  {route:'/designsheets',table:'designsheet',where:'1=1',identity:'contract_id',column:'contract_id',date:'datefrom',from:'datestart',to:'dateto'},
  {route:'/purchase-orders',table:'st_purchaseorder',where:"status IN ('Y','R')",vendor:'vendor_id',identity:'po_number',column:'purchaseorder_id',date:'added_time',from:'datefrom',to:'dateto'},
  {route:'/grn-inspection',table:'grn_inspection',where:'1=1',vendor:'vendor_id',identity:'po_id',column:'po_id',date:'inwarddate',from:'from_date',to:'to_date'},
  {route:'/grn',table:'st_goodsreceive',where:'1=1',vendor:'vendor_id',identity:'po_id',column:'purchaseorder_id',date:'inwarddate',from:'from_date',to:'to_date'}
 ];
 const evidence=[];
 try {
  if(process.env.AUDIT_BROWSER_ONLY==='1') {
   const evidence=JSON.parse(require('fs').readFileSync(path.join(__dirname,'../../tmp/pagination-api-results.json'),'utf8'));
   await require('./audit-list-pagination-browser')({base,token,get,evidence});
   return;
  }
  for(const e of entries){
   const expected=await sql(`SELECT id FROM ${e.table} WHERE ${e.where} ORDER BY id DESC`);
   const first=await get(e.route);assert.equal(first.status,200,JSON.stringify(first.body));
   const rows=b=>b.items||b.data;const meta=b=>b.pagination||b;
   const m=meta(first.body);assert.equal(m.total,expected.length);assert.equal(m.limit,50);assert.equal(m.page,1);assert.equal(m.totalPages,Math.ceil(expected.length/50));
   let ids=[];
   // All pages, including first/second/middle/last, prove complete coverage and no duplicates.
   for(let page=1;page<=Math.max(m.totalPages,1);page++){
    const {status,body}=page===1?first:await get(e.route,{page});assert.equal(status,200);
    const info=meta(body),actual=rows(body);
    assert.equal(info.page,page);assert.equal(info.current,actual.length);assert.equal(info.hasPrevious,page>1);assert.equal(info.hasNext,page<m.totalPages);
    assert.deepEqual(actual.map(r=>Number(r.id)),expected.slice((page-1)*50,page*50).map(r=>r.id));ids.push(...actual.map(r=>Number(r.id)));
   }
   assert.equal(new Set(ids).size,expected.length);
   // Previous/Next revisit must retain exact rows.
   for(const page of [2,1,Math.max(1,Math.ceil(m.totalPages/2)),Math.max(1,m.totalPages)]) if(page<=m.totalPages){const r=await get(e.route,{page});assert.deepEqual(rows(r.body).map(r=>Number(r.id)),expected.slice((page-1)*50,page*50).map(r=>r.id));}
   const sample=(await sql(`SELECT * FROM ${e.table} WHERE ${e.where} ORDER BY id DESC LIMIT 1`))[0];
   const date=value=>value instanceof Date?`${value.getFullYear()}-${String(value.getMonth()+1).padStart(2,'0')}-${String(value.getDate()).padStart(2,'0')}`:String(value).slice(0,10);
   const identity={[e.identity]:sample[e.column]};
   const poWhere=e.route==='/purchase-orders'?`EXISTS (SELECT 1 FROM st_purchaseorderdetails pd WHERE pd.purchaseorder_id=${e.table}.purchaseorder_id)`:'1=1';
   const checks=[['search',identity,`${e.column}=:id`,{id:sample[e.column]}],['filter',e.vendor?{vendor_id:sample[e.vendor]}:{datestart:date(sample[e.date])},e.vendor?`${e.vendor}=:vendor`:`DATE(${e.date})>=:start`,e.vendor?{vendor:sample[e.vendor]}:{start:date(sample[e.date])}],['combined',{...identity,[e.from]:date(sample[e.date]),[e.to]:date(sample[e.end||e.date])},`${e.column}=:id AND DATE(${e.date})>=:start AND DATE(${e.end||e.date})<=:end`,{id:sample[e.column],start:date(sample[e.date]),end:date(sample[e.end||e.date])}]];
   for(const [name,params,where,values] of checks){const matched=await sql(`SELECT id FROM ${e.table} WHERE ${where} AND ${poWhere} ORDER BY id DESC`,values);const res=await get(e.route,params);assert.equal(res.status,200,`${e.route} ${name}`);assert.equal(meta(res.body).total,matched.length,`${e.route} ${name} count`);assert.deepEqual(rows(res.body).map(r=>Number(r.id)),matched.slice(0,50).map(r=>r.id));if(matched.length>50){const second=await get(e.route,{...params,page:2});assert.deepEqual(rows(second.body).map(r=>Number(r.id)),matched.slice(50,100).map(r=>r.id));}}
   const empty=await get(e.route,{[e.identity]:'999999999999'});assert.equal(empty.status,200);assert.equal(meta(empty.body).total,0);assert.equal(meta(empty.body).totalPages,0);assert.equal(meta(empty.body).page,1);assert.equal(rows(empty.body).length,0);
   const reset=await get(e.route);assert.equal(meta(reset.body).total,m.total);
   const ascending=await get(e.route,{sort:'id',direction:'asc'});assert.deepEqual(rows(ascending.body).map(r=>Number(r.id)),expected.map(r=>r.id).reverse().slice(0,50));
   const capped=await get(e.route,{limit:1000});assert.equal(meta(capped.body).limit,100);
   const negative=await get(e.route,{page:-3,limit:0});assert.equal(meta(negative.body).page,1);assert.equal(meta(negative.body).limit,1);
   const beyond=await get(e.route,{page:m.totalPages+1});assert.equal(beyond.status,404);
   evidence.push({module:e.route,total:m.total,pages:m.totalPages,limit:m.limit,checks:'all pages/unique coverage, previous/next, search/filter/combined/reset/empty/count/sort/limit/404 passed'});
   console.log('PASS',e.route,m.total,'records',m.totalPages,'pages');
  }
  const indents=await get('/indents');assert.equal(indents.status,200,JSON.stringify(indents.body));const ids=await sql('SELECT indent_id FROM st_indentmaster GROUP BY indent_id ORDER BY MAX(id) DESC');
  assert.deepEqual(indents.body.data.map(r=>r.indent_id),ids.map(r=>r.indent_id));assert.equal(indents.body.defaultLimit,50);assert.equal(indents.body.paginationMode,'client');
  for(const row of indents.body.data){const total=await sql('SELECT SUM(quantity) AS total FROM st_indentmaster WHERE indent_id=:id',{id:row.indent_id});assert.equal(Number(row.total_qty),Number(total[0].total));const filtered=await get('/indents',{indent_id:row.indent_id});assert.equal(filtered.body.data.length,1);assert.equal(filtered.body.data[0].items.length,row.items.length);}
  assert.equal((await get('/indents',{indent_id:999999999})).body.total,0);
  evidence.push({module:'/indents',total:ids.length,limit:50,pages:Math.ceil(ids.length/50),checks:'unique grouping, quantities/children, exact filter, empty passed; only two live indents'});
  const excel=await fetch(base+'/grn-inspection/export/excel',{headers:{Authorization:`Bearer ${token}`}});assert.equal(excel.status,200);const workbook=new(require('exceljs')).Workbook();await workbook.xlsx.load(Buffer.from(await excel.arrayBuffer()));assert.equal(workbook.worksheets[0].rowCount,evidence.find(e=>e.module==='/grn-inspection').total+1);
  require('fs').mkdirSync(path.join(__dirname,'../../tmp'),{recursive:true});
  require('fs').writeFileSync(path.join(__dirname,'../../tmp/pagination-api-results.json'),JSON.stringify(evidence,null,2));
  if(process.env.AUDIT_BROWSER==='1')await require('./audit-list-pagination-browser')({base,token,get,evidence});
 }finally {await new Promise(r=>server.close(r));await db.close();await centralSequelize.close();}
}
run().catch(e=>{console.error(e.message,e.stack);process.exitCode=1;});
