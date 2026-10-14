// Read production fixtures; perform the full write workflow only in connection-
// local TEMPORARY tables. Closing this connection removes all test records.
require('../src/config/environment');
const assert=require('node:assert/strict');
const {Sequelize,QueryTypes}=require('sequelize');
const service=require('../src/modules/reverseIndent/reverseIndent.service');
const {migrate}=require('./migrate-reverse-schema.cjs');
const {legacyReversePermissions}=require('../src/modules/auth/legacyReverseAccess');
const {buildReverseIndentHtml,generateReverseIndentPDF}=require('../src/modules/reverseIndent/reverseIndent.pdf');

async function verify(database) {
  const real=new Sequelize(database,process.env.DB_USER || 'root',process.env.DB_PASSWORD || '',{host:process.env.DB_HOST,dialect:'mysql',logging:false,pool:{max:3}});
  const read=(sql,replacements={})=>real.query(sql,{replacements,type:QueryTypes.SELECT});
  let connection;
  const temporary=[];
  try {
    const headers=await read('SELECT * FROM reverseindent ORDER BY id');
    const stock=await read('SELECT * FROM st_stock_register ORDER BY id');
    const actual=await service.getReverseIndents(real);
    assert.deepEqual(actual.map(row=>row.id),headers.map(row=>row.id).reverse());
    let detailRows=0;
    for(const row of actual) {
      const details=await service.getReverseIndentDetails(real,row.reverse_id);
      const expected=stock.filter(item=>String(item.reverse_id)===String(row.reverse_id));
      assert.deepEqual(details.items.map(item=>item.id),expected.map(item=>item.id));
      assert.equal(details.items.reduce((sum,item)=>sum+Number(item.quantity),0),expected.reduce((sum,item)=>sum+Number(item.quantity),0));
      detailRows+=expected.length;
    }
    const contracts=await read(`SELECT DISTINCT d.contract_id FROM designsheet d JOIN contracts c ON c.id=d.contract_id WHERE c.status='Y' ORDER BY d.contract_id`);
    for(const row of contracts) {
      const products=await service.getContractProducts(real,row.contract_id);
      const expected=await read(`SELECT DISTINCT a.id,a.item_name FROM designsheet d JOIN st_additem a ON a.id=d.item_id WHERE d.contract_id=:cid AND a.itemtype='FinishedProduct' ORDER BY a.item_name`,{cid:row.contract_id});
      assert.deepEqual(products,expected);
    }
    const page1=await service.getPage(real,{page:1,limit:20}),page2=await service.getPage(real,{page:2,limit:20});
    await assert.rejects(service.getPage(real,{page:'Infinity'}),e=>e.status===400);
    assert.equal(page1.total,headers.length);assert.deepEqual(page1.data.map(r=>r.id),actual.slice(0,20).map(r=>r.id));assert.deepEqual(page2.data.map(r=>r.id),actual.slice(20,40).map(r=>r.id));
    const day=String(actual[0].issue_date instanceof Date ? actual[0].issue_date.toISOString().slice(0,10) : actual[0].issue_date).slice(0,10);
    assert.deepEqual((await service.getReverseIndents(real,{datefrom:day,date_mode:'search'})).map(r=>r.id),actual.filter(r=>new Date(r.issue_date).toISOString().slice(0,10)===day).map(r=>r.id));
    assert.equal((await service.getReverseIndents(real,{dateto:'2020-01-01',date_mode:'search'})).length,actual.length);
    for(const filter of [{contract_id:actual[0].contract_id},{item_id:actual[0].finishedproduct_id},{machines_id:actual.find(r=>r.machine_id)?.machine_id},{datefrom:'2024-01-01',dateto:'2024-12-31'}]) {
      const rows=await service.getReverseIndents(real,filter);
      const expected=actual.filter(r=>(!filter.contract_id || String(r.contract_id)===String(filter.contract_id)) && (!filter.item_id || String(r.finishedproduct_id)===String(filter.item_id)) && (!filter.machines_id || String(r.machine_id)===String(filter.machines_id)) && (!filter.datefrom || new Date(r.issue_date)>=new Date(filter.datefrom)) && (!filter.dateto || new Date(r.issue_date)<new Date('2025-01-01')));
      assert.deepEqual(rows.map(r=>r.id),expected.map(r=>r.id));
    }
    const preview=await migrate(real);
    connection=await real.connectionManager.getConnection();
    const anchor={connection};
    const local=(sql,options={})=>real.query(sql,{...options,transaction:anchor});
    // Capture each source before creating its shadow table. Test writes never
    // address an unshadowed table; tables are private to this pinned connection.
    for(const table of ['reverseindent','st_stock_register','st_additem']) {
      const fixtures=await read(`SELECT * FROM ${table} ORDER BY id`);
      await local(`CREATE TEMPORARY TABLE qa_reverse_${table} LIKE ${table}`);
      await local(`ALTER TABLE qa_reverse_${table} RENAME TO ${table}`); temporary.push(table);
      for(let start=0;start<fixtures.length;start+=100) await real.getQueryInterface().bulkInsert(table,fixtures.slice(start,start+100),{transaction:anchor});
    }
    const db={query:local,escape:value=>real.escape(value),transaction:async()=>{
      await local('START TRANSACTION');
      return {commit:()=>local('COMMIT'),rollback:()=>local('ROLLBACK')};
    }};
    // Verify the schema guard against the real tenant's missing 99 enum.
    const today=new Intl.DateTimeFormat('en-CA',{timeZone:'Asia/Kolkata',year:'numeric',month:'2-digit',day:'2-digit'}).format(new Date());
    const [semi]=await service.getSemiProducts(db);
    assert.ok(semi,'An existing active semi-finished product is required');
    const [contract]=await service.searchContracts(db);
    const [raw]=await local("SELECT id FROM st_additem WHERE itemtype='RawMaterial' AND status='Y' LIMIT 1",{type:QueryTypes.SELECT});
    const base={contract_id:String(contract.id),finishedproduct_id:String(semi.id),received_name:'Isolated Reverse QA',issue_date:today,finished_qty:'2.50',items:[{item_id:raw.id,quantity:'1.25'}]};
    const corrupt=actual.find(row=>row.status==='draft' && !row.draft_stock_clean);
    if(corrupt) {
      // Use today's date only in the isolated header to reach the posted-stock guard.
      await local('UPDATE reverseindent SET issue_date=:today WHERE id=:id',{replacements:{today,id:corrupt.id},type:QueryTypes.UPDATE});
      await assert.rejects(service.saveReverseIndent(db,{...base,contract_id:corrupt.contract_id,finishedproduct_id:corrupt.finishedproduct_id},corrupt.reverse_id),e=>e.status===409 && /posted stock/.test(e.message));
    }
    const balance=async(itemId)=>(await local(`SELECT COALESCE(ROUND(SUM(CASE WHEN store_type IN ('0','1','3') THEN quantity WHEN store_type IN ('2','4') THEN -quantity ELSE 0 END),2),0) AS qty FROM st_stock_register WHERE item_id=:itemId`,{replacements:{itemId},type:QueryTypes.SELECT}))[0].qty;
    if(!await service.schema(db).then(r=>r.draft)) await assert.rejects(service.saveReverseIndent(db,{...base,action:'draft'}),e=>e.status===503);
    await migrate(db,true);
    await local("DELETE FROM st_stock_register WHERE item_id=:id AND store_type='0'",{replacements:{id:semi.id},type:QueryTypes.DELETE});
    const initialSemi=Number(await balance(semi.id)),initialRaw=Number(await balance(raw.id));
    const draftNumber=String(await service.getNextReverseId(db));
    await service.saveReverseIndent(db,{...base,reverse_id:draftNumber,action:'draft'});
    let detail=await service.getReverseIndentDetails(db,draftNumber);
    assert.equal(detail.status,'draft');assert.deepEqual(detail.items.map(r=>r.store_type),['99','99']);
    assert.equal(detail.finished_qty,2.5);assert.equal(detail.pending_qty,null);
    assert.equal(Number(await balance(semi.id)),initialSemi);assert.equal(Number(await balance(raw.id)),initialRaw);
    const opening=()=>local("SELECT id FROM st_stock_register WHERE item_id=:id AND store_type='0'",{replacements:{id:semi.id},type:QueryTypes.SELECT});
    assert.equal((await opening()).length,0);
    await service.saveReverseIndent(db,{...base,finished_qty:3,action:'draft'},draftNumber);
    detail=await service.getReverseIndentDetails(db,draftNumber);assert.equal(detail.items.length,2);assert.equal(detail.finished_qty,3);
    await service.saveReverseIndent(db,{...base,finished_qty:4,action:'finalize'},draftNumber);
    detail=await service.getReverseIndentDetails(db,draftNumber);
    assert.deepEqual(detail.items.map(r=>[r.store_type,Number(r.quantity)]),[['0',0],['1',4],['3',1.25]]);
    assert.equal((await opening()).length,1);assert.equal(detail.status,'finalized');
    assert.equal(Number(await balance(semi.id)),initialSemi+4);assert.equal(Number(await balance(raw.id)),initialRaw+1.25);
    await assert.rejects(service.saveReverseIndent(db,{...base,action:'finalize'},draftNumber),e=>e.status===409);
    const second=String(await service.getNextReverseId(db));
    await service.saveReverseIndent(db,{...base,reverse_id:second,action:'finalize'});
    assert.equal((await opening()).length,1);
    assert.deepEqual((await service.getReverseIndentDetails(db,second)).items.map(r=>r.store_type),['1','3']);
    await assert.rejects(service.saveReverseIndent(db,{...base,reverse_id:second,action:'finalize'}),e=>e.status===409);
    for(const bad of [{finished_qty:0},{finished_qty:-1},{finished_qty:true},{finished_qty:' '},{finished_qty:'bad'},{finished_qty:'1.001'},{issue_date:'2026-02-30'},{items:[{item_id:raw.id,quantity:-1}]},{items:[{item_id:raw.id,quantity:1},{item_id:raw.id,quantity:2}]}]) await assert.rejects(service.saveReverseIndent(db,{...base,...bad}),e=>e.status===400);
    // Finished-product opening and edit pending constraints use existing design fixtures.
    const [finished]=await local(`SELECT d.item_id,d.quantity,d.contract_id FROM designsheet d JOIN st_additem a ON a.id=d.item_id
      JOIN contracts c ON c.id=d.contract_id WHERE a.itemtype='FinishedProduct' AND c.status='Y' AND CAST(d.quantity AS DECIMAL(12,2))>2 ORDER BY d.id LIMIT 1`,{type:QueryTypes.SELECT});
    assert.ok(finished,'Existing finished-product design is required');
    const fbase={...base,contract_id:finished.contract_id,finishedproduct_id:finished.item_id,finished_qty:1,items:[]};
    await local("DELETE FROM st_stock_register WHERE item_id=:id AND store_type IN ('0','1')",{replacements:{id:finished.item_id},type:QueryTypes.DELETE});
    const fNumber=String(await service.getNextReverseId(db));await service.saveReverseIndent(db,{...fbase,reverse_id:fNumber,action:'finalize'});
    assert.deepEqual((await service.getReverseIndentDetails(db,fNumber)).items.map(r=>r.store_type),['0']);
    const fDraft=String(await service.getNextReverseId(db));await service.saveReverseIndent(db,{...fbase,reverse_id:fDraft,action:'draft'});
    await assert.rejects(service.saveReverseIndent(db,{...fbase,finished_qty:Number(finished.quantity),action:'finalize'},fDraft),e=>e.status===400);
    await service.saveReverseIndent(db,{...fbase,action:'finalize'},fDraft);
    assert.deepEqual((await service.getReverseIndentDetails(db,fDraft)).items.map(r=>r.store_type),['1']);
    await assert.rejects(service.saveReverseIndent(db,{...fbase,finished_qty:Number(finished.quantity)+1,action:'finalize'}),e=>e.status===400);
    const source=await service.getDesignSheetDetails(db,finished.contract_id,finished.item_id);
    for(const group of source.items.filter(r=>Number(r.is_group)===1)) {
      const expected=await local("SELECT id FROM st_additem WHERE category_id=:category AND status='Y' AND itemtype='RawMaterial' ORDER BY item_name",{replacements:{category:group.category_id},type:QueryTypes.SELECT});
      assert.deepEqual(group.group_items.map(r=>r.id),expected.map(r=>r.id));
    }
    // Force a failure after header insertion to prove atomic rollback.
    const failureNumber=String(await service.getNextReverseId(db));
    const failing={...db,query:async(sql,opts)=>{if(/^INSERT INTO st_stock_register/.test(sql)) throw new Error('Injected stock failure');return db.query(sql,opts);}};
    await assert.rejects(service.saveReverseIndent(failing,{...base,reverse_id:failureNumber,action:'finalize'}),/Injected stock failure/);
    assert.equal(await service.getReverseIndentDetails(db,failureNumber),null);
    await service.deleteReverseIndent(db,second);assert.equal(await service.getReverseIndentDetails(db,second),null);
    assert.equal((await local('SELECT id FROM st_stock_register WHERE reverse_id=:id',{replacements:{id:second},type:QueryTypes.SELECT})).length,0);
    await local('UPDATE reverseindent SET issue_date=:date WHERE reverse_id=:id',{replacements:{date:'2020-01-01',id:draftNumber},type:QueryTypes.UPDATE});
    await assert.rejects(service.deleteReverseIndent(db,draftNumber),e=>e.status===403);
    assert.ok(buildReverseIndentHtml(detail).includes('Reverse Indent Details'));
    const pdf=await generateReverseIndentPDF(detail);assert.equal(pdf.subarray(0,4).toString(),'%PDF');
    const index='legacy:admin/reverseindent/index';
    assert.ok(legacyReversePermissions([],[index]).includes('legacy:admin/reverseindent/excel'));
    assert.ok(!legacyReversePermissions([{url:'admin/reverseindent/excel'}],[index]).includes('legacy:admin/reverseindent/excel'));
    // Independent pooled reads bypass temporary tables and prove persistence untouched.
    assert.equal(JSON.stringify(await read('SELECT * FROM reverseindent ORDER BY id')),JSON.stringify(headers));
    assert.equal(JSON.stringify(await read('SELECT * FROM st_stock_register ORDER BY id')),JSON.stringify(stock));
    console.log(JSON.stringify({database,existingHeaders:headers.length,existingStockRows:detailRows,contracts:contracts.length,workflow:'PASS (temporary tables only)',pdfBytes:pdf.length,schemaPreview:preview}));
  } finally {
    if(connection) {
      for(const table of temporary.reverse()) await real.query(`DROP TEMPORARY TABLE IF EXISTS ${table}`,{transaction:{connection}});
      await real.connectionManager.releaseConnection(connection);
    }
    await real.close();
  }
}
verify(process.env.STOCK_TEST_DB || 'tirupati_tppl').catch(error=>{console.error(error.message,error.stack);process.exitCode=1;});
