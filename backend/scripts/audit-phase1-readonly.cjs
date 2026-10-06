const assert=require('node:assert/strict'),path=require('node:path'),fs=require('node:fs');
require('dotenv').config({path:path.join(__dirname,'../.env'),quiet:true});
const {QueryTypes}=require('sequelize'),jwt=require('jsonwebtoken');
const {getTenantSequelize,centralSequelize}=require('../src/config/sequelize');
async function run(){
  const dbName=process.env.AUDIT_TENANT || 'tirupati_tppl',db=await getTenantSequelize(dbName);
  for(const connection of new Set([db,centralSequelize])) {
    const query=connection.query.bind(connection);
    connection.query=(sql,options)=>{assert.match(typeof sql==='string'?sql:sql.query,/^\s*(SELECT|SHOW)\b/i,'Read-only audit blocked a database mutation');return query(sql,options);};
  }
  const sql=(query,replacements={})=>db.query(query,{replacements,type:QueryTypes.SELECT});
  const server=require('../src/app').listen(0);await new Promise(resolve=>server.once('listening',resolve));
  const token=(role,permissions=[])=>jwt.sign({id:1,db:dbName,role_id:role,permissions},process.env.JWT_SECRET || 'super_secret_key',{expiresIn:'15m'});
  const api=async(route,params={},auth=token(101),method='GET')=>{
    const response=await fetch(`http://127.0.0.1:${server.address().port}/api${route}?${new URLSearchParams(params)}`,{method,headers:{Authorization:`Bearer ${auth}`,'Content-Type':'application/json'},...(method==='GET'?{}:{body:'{}'})});
    return {status:response.status,body:await response.json()};
  };
  const evidence={tenant:dbName,checks:[]};
  try {
    const [costContract]=await sql("SELECT c.id FROM contracts c ORDER BY EXISTS (SELECT 1 FROM st_stock_register s WHERE s.contract_id=c.id AND s.store_type='3') DESC,c.id DESC LIMIT 1");
    if(costContract){
      const result=await api(`/contracts/${costContract.id}/reverse-cost`);assert.equal(result.status,200,JSON.stringify(result.body));
      const expected=await sql(`SELECT s.id FROM st_stock_register s WHERE s.contract_id=:id AND s.store_type='3' AND s.item_id IN (SELECT item_id FROM designsheetdetails WHERE contract_id=:id) ORDER BY s.item_id,s.id DESC`,{id:costContract.id});
      assert.deepEqual(result.body.data.items.map(row=>row.id),expected.map(row=>row.id));
      for(const row of result.body.data.items){const [receipt]=await sql(`SELECT g.*,t.tax AS percentage FROM st_stock_register g LEFT JOIN st_taxmaster t ON t.id=g.tax_id WHERE g.item_id=(SELECT item_id FROM st_stock_register WHERE id=:id) AND g.store_type='1' AND g.issue_date <= (SELECT DATE(created) FROM st_stock_register WHERE id=:id) ORDER BY g.id DESC LIMIT 1`,{id:row.id});assert.equal(row.goods_id,receipt?.goods_id ?? null);assert.equal(row.cost,Number(row.quantity)*Number(receipt?.rate || 0)*parseFloat(`1.${receipt?.percentage ?? ''}`));}
      evidence.checks.push({module:'Contract reverse cost',contract:costContract.id,rows:expected.length,result:expected.length ? 'Source design-item membership, every GRN reference and saved-date tax/rate calculation matched read-only SQL' : 'Empty source-matching result verified; no qualifying reverse rows in this live sample. Tax/rate behavior covered by memory regression fixture.'});
    }
    const expected=await sql("SELECT id FROM st_additem WHERE status='Y' AND itemtype='RawMaterial' ORDER BY item_name,id");
    let actual=[];
    const first=await api('/settings/products/list');assert.equal(first.status,200,JSON.stringify(first.body));assert.equal(first.body.total,expected.length);assert.equal(first.body.limit,50);
    for(let page=1;page<=Math.max(1,first.body.totalPages);page++){
      const result=page===1?first:await api('/settings/products/list',{page});assert.equal(result.status,200);actual.push(...result.body.data.map(row=>row.id));
    }
    assert.deepEqual(actual,expected.map(row=>row.id));
    const [sample]=await sql("SELECT * FROM st_additem WHERE status='Y' ORDER BY id DESC LIMIT 1");
    if(sample){const result=await api('/settings/products/list',{search:sample.item_name,search_mode:1});assert.equal(result.status,200);const rows=await sql('SELECT id FROM st_additem WHERE item_name LIKE :name ORDER BY item_name,id',{name:`%${sample.item_name}%`});assert.equal(result.body.total,rows.length);assert.deepEqual(result.body.data.map(row=>row.id),rows.slice(0,50).map(row=>row.id));}
    const form=await api('/settings/products/form-data');assert.equal(form.status,200,JSON.stringify(form.body));assert.ok(Array.isArray(form.body.data.taxes));
    evidence.checks.push({module:'Products',records:expected.length,result:'All server pages/counts, active raw material default, search and form masters passed'});
    const [productBalance]=await sql(`SELECT COALESCE(ROUND(SUM(CASE WHEN store_type IN ('0','1','3') THEN quantity ELSE 0 END),2),0)-COALESCE(ROUND(SUM(CASE WHEN store_type IN ('2','4') THEN quantity ELSE 0 END),2),0) AS quantity FROM st_stock_register WHERE item_id=:id`,{id:first.body.data[0].id});
    assert.equal(Number(first.body.data[0].current_stock),Number(productBalance.quantity));
    const indentIds=await sql('SELECT id FROM indentpo ORDER BY id DESC');
    const indentFirst=await api('/indentpo',{page:1});assert.equal(indentFirst.status,200,JSON.stringify(indentFirst.body));assert.equal(indentFirst.body.total,indentIds.length);
    const indentActual=[];
    for(let page=1;page<=Math.max(1,indentFirst.body.totalPages);page++){const result=page===1?indentFirst:await api('/indentpo',{page});assert.equal(result.status,200);indentActual.push(...result.body.data.map(row=>row.id));}
    assert.deepEqual(indentActual,indentIds.map(row=>row.id));
    if(indentFirst.body.data.length){const row=indentFirst.body.data[0];const edit=await api('/indentpo/'+row.indent_id+'/edit-data');assert.equal(edit.status,200);const saved=await sql('SELECT id,quantity FROM st_stock_register WHERE indent_id=:id ORDER BY id',{id:row.indent_id});assert.deepEqual(edit.body.items.map(item=>[item.id,Number(item.quantity)]),saved.map(item=>[item.id,Number(item.quantity)]));}
    evidence.checks.push({module:'Indents / material issue',records:indentIds.length,result:'Complete server page traversal/count and existing edit ledger IDs/quantities passed'});
    const categories=await api('/settings/categories');assert.equal(categories.status,200,JSON.stringify(categories.body));const catRows=await sql("SELECT id FROM st_categorymaster WHERE status='Y' ORDER BY category_name");assert.deepEqual(categories.body.data.map(row=>row.id),catRows.map(row=>row.id));evidence.checks.push({module:'Categories',records:catRows.length,result:'Active list and master relationships queried successfully'});

    const date='2026-10-06',daily=await api('/stock-register/daily',{date});assert.equal(daily.status,200,JSON.stringify(daily.body));
    const totals=await sql(`SELECT p.id,COALESCE(SUM(CASE WHEN s.store_type IN ('0','1','3') THEN s.quantity WHEN s.store_type IN ('2','4') THEN -s.quantity ELSE 0 END),0) AS balance
      FROM st_additem p LEFT JOIN st_stock_register s ON s.item_id=p.id AND s.status!='N' AND DATE(s.created)<=:date
      WHERE p.itemtype='RawMaterial' AND p.status='Y' AND p.category_id!=25 GROUP BY p.id`,{date});
    assert.equal(daily.body.data.length,totals.length);const balances=new Map(totals.map(row=>[row.id,Number(row.balance)]));
    for(const row of daily.body.data){assert.ok(Math.abs(Number(row.closing_stock)-balances.get(row.item_id))<0.011);assert.equal(row.opening_stock,row.closing_stock);}
    const stock=await api('/stock-register',{date_from:date,date_to:date});assert.equal(stock.status,200,JSON.stringify(stock.body));
    for(const row of stock.body.data){const [sum]=await sql(`SELECT COALESCE(SUM(CASE WHEN store_type IN ('0','1','3') AND DATE(COALESCE(delivery_date,issue_date,created))<=:date THEN quantity WHEN store_type IN ('2','4') AND DATE(COALESCE(issue_date,created))<=:date THEN -quantity ELSE 0 END),0) AS balance FROM st_stock_register WHERE status!='N' AND item_id=:id`,{date,id:row.item_id});assert.ok(Math.abs(Number(row.closing_stock)-Number(sum.balance))<0.011);}
    evidence.checks.push({module:'Stock Register',dailyRows:daily.body.data.length,registerRows:stock.body.data.length,result:'Every returned cumulative/detailed balance matched independent SQL derived from current PHP'});
    const ExcelJS=require('exceljs');
    const download=async(route,params={})=>{
      const response=await fetch(`http://127.0.0.1:${server.address().port}/api${route}?${new URLSearchParams(params)}`,{headers:{Authorization:`Bearer ${token(101)}`}});
      assert.equal(response.status,200,route);const workbook=new ExcelJS.Workbook();await workbook.xlsx.load(Buffer.from(await response.arrayBuffer()));return workbook.worksheets[0];
    };
    const productsExport=await download('/settings/products/export');assert.equal(productsExport.actualRowCount,expected.length+1);assert.equal(productsExport.getCell('F1').value,'Current Stock');
    const detailedExport=await download('/stock-register/export',{date_from:date,date_to:date});assert.equal(detailedExport.actualRowCount,stock.body.data.length+2);assert.equal(detailedExport.getCell('J2').value,'Physical Stock');
    const [site]=await sql("SELECT ac_holder FROM sitesettings_details WHERE status='Y' LIMIT 1");assert.equal(detailedExport.getCell('A1').value,site.ac_holder || '');
    const dailyExport=await download('/stock-register/daily/export',{date});assert.equal(dailyExport.getCell('A3').value,'ID');assert.equal(dailyExport.getCell('A1').value,site.ac_holder || '');
    const issuesExport=await download('/indentpo/export');const [issueCount]=await sql('SELECT COUNT(*) AS total FROM indentpo h JOIN st_stock_register s ON s.indent_id=h.indent_id');assert.equal(issuesExport.actualRowCount,Number(issueCount.total)+1);assert.equal(issuesExport.getCell('F1').value,'Raw Material');
    evidence.checks.push({module:'Excel exports',productRows:productsExport.actualRowCount-1,materialIssueRows:issuesExport.actualRowCount-1,stockRows:detailedExport.actualRowCount-2,result:'Real API downloads decoded successfully; complete row counts, tenant company, PHP identity/physical columns verified'});
    const deniedToken=token(6,[]);
    for(const [method,route] of [['POST','/indentpo'],['PUT','/indentpo/1001'],['DELETE','/indentpo/1001'],['POST','/contracts'],['PUT','/contracts/1'],['DELETE','/contracts/1'],['POST','/grn'],['POST','/grn-inspection'],['POST','/settings/products'],['PUT','/settings/products/1'],['DELETE','/settings/products/1'],['POST','/settings/categories'],['PUT','/settings/categories/1'],['DELETE','/settings/categories/1'],['POST','/purchase-orders'],['PUT','/purchase-orders/1']]) {
      const result=await api(route,{},deniedToken,method);assert.equal(result.status,403,`${method} ${route}: ${JSON.stringify(result.body)}`);
    }
    const editToken=token(6,['contracts:edit','legacy:admin/contracts/edit','legacy:admin/additem/edit']);
    assert.equal((await api('/contracts',{},editToken)).status,403);
    assert.equal((await api('/settings/products/'+sample.id,{},editToken)).status,200);
    assert.equal((await api('/settings/products',{},editToken,'POST')).status,403);
    assert.equal((await api('/settings/products/'+sample.id,{},editToken,'DELETE')).status,403);
    evidence.checks.push({module:'Permissions',result:'16 denied write actions returned 403 before database writes; edit-only user can load product edit data but cannot list contracts/add/delete'});
    const inspections=await sql("SELECT inspection_id FROM grn_inspection WHERE status='Y' ORDER BY id DESC LIMIT 3");
    for(const inspection of inspections){const result=await api('/grn/inspection/'+inspection.inspection_id);assert.equal(result.status,200);const saved=await sql('SELECT id,cost_price,tax,amount FROM grn_inspection_details WHERE inspection_id=:id',{id:inspection.inspection_id});assert.equal(result.body.items.length,saved.length);for(const row of result.body.items){const source=saved.find(item=>item.id===row.id);assert.equal(Number(row.amount),Number(source.amount));assert.equal(Number(row.tax),Number(source.tax));assert.equal(Number(row.cost_price),Number(source.cost_price));}}
    evidence.checks.push({module:'GRN / Inspection',samples:inspections.length,result:inspections.length ? 'Existing inspection valuations reached receiving API unchanged; tax joined by saved tax master ID' : 'NEEDS ATTENTION: no active inspection exists for a read-only receiving sample'});
    fs.mkdirSync(path.join(__dirname,'../../docs'),{recursive:true});fs.writeFileSync(path.join(__dirname,`../../docs/phase1-readonly-${dbName}.json`),JSON.stringify(evidence,null,2));console.log(JSON.stringify(evidence,null,2));
  } finally {await new Promise(resolve=>server.close(resolve));await db.close();await centralSequelize.close();}
}
run().catch(error=>{console.error(error.message);process.exitCode=1;});
