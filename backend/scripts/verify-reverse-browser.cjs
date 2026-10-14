// Existing-user HTTP/browser check. Every database query must be SELECT/SHOW;
// the proxy rejects all save requests before they reach application handlers.
require('../src/config/environment');
const assert=require('node:assert/strict');
const fs=require('node:fs');
const path=require('node:path');
const express=require('express');
const config=require('../src/config/sequelize');
const auth=require('../src/modules/auth/auth.service');
const service=require('../src/modules/reverseIndent/reverseIndent.service');

(async()=>{
  const name=process.env.STOCK_TEST_DB || 'tirupati_tppl',db=await config.getTenantSequelize(name);
  for(const pool of [db,config.centralSequelize]) {
    const original=pool.query.bind(pool);
    pool.query=(sql,options)=>{assert.match(sql.trim(),/^(SELECT|SHOW)\b/i,'Read-only check');return original(sql,options);};
  }
  let server,browser;
  try {
    const users=await config.centralSequelize.query("SELECT mobile FROM users WHERE db=:name AND is_status!='N'",{replacements:{name},type:require('sequelize').QueryTypes.SELECT});
    let session;
    for(const user of users) {
      try {
        const existing=await auth.getSession(user.mobile,name);
        if(existing.permissions.includes('legacy:admin/reverseindent/index')) {session=auth._issueTokens(existing);if(existing.permissions.includes('legacy:admin/reverseindent/add'))break;}
      }catch{}
    }
    assert.ok(session,'Existing Reverse Manager user required');
    const app=express();let mutations=0;
    app.use((req,res,next)=>{if(req.method!=='GET'){mutations++;return res.sendStatus(405);}next();});app.use(require('../src/app'));
    server=await new Promise(resolve=>{const listener=app.listen(0,'127.0.0.1',()=>resolve(listener));});
    const base=`http://127.0.0.1:${server.address().port}`,headers={Authorization:`Bearer ${session.accessToken}`};
    assert.equal((await fetch(`${base}/api/reverse-indent`)).status,401);
    const list=await fetch(`${base}/api/reverse-indent?page=1`,{headers});assert.equal(list.status,200);
    const data=await list.json();assert.ok(data.data.length);assert.equal(data.limit,50);
    const first=data.data[0];
    for(const [suffix,action] of [[`/${first.reverse_id}`,'viewreverseindent'],[`/${first.reverse_id}/edit-data`,'edit'],['/next-id','add'],['/export','excel'],[`/${first.reverse_id}/pdf`,'viewreverseindentpdf']]) {
      const response=await fetch(`${base}/api/reverse-indent${suffix}`,{headers});
      assert.equal(response.status,session.user.permissions.includes(`legacy:admin/reverseindent/${action}`)?200:403,suffix);
      if(response.status===200 && suffix==='/export') {
        const ExcelJS=require('exceljs');const book=new ExcelJS.Workbook();await book.xlsx.load(Buffer.from(await response.arrayBuffer()));
        const expected=(await service.getReverseIndents(db)).length;
        assert.ok(book.worksheets[0].rowCount>=expected);
      }
    }
    browser=await require('../src/utils/pdfBrowser').launchPdfBrowser();
    const page=await browser.newPage();await page.setViewport({width:1440,height:1000});
    await page.evaluateOnNewDocument(({accessToken,refreshToken})=>{localStorage.setItem('accessToken',accessToken);localStorage.setItem('refreshToken',refreshToken);},session);
    await page.setRequestInterception(true);const failures=[];const statuses=[];
    page.on('pageerror',e=>failures.push(e.message));
    page.on('request',async request=>{
      try {
        const url=request.url();
        if(!url.includes('/api/'))return request.continue();
        if(request.method()==='OPTIONS')return request.respond({status:204,headers:{'Access-Control-Allow-Origin':'*','Access-Control-Allow-Headers':'*'}});
        if(request.method()!=='GET'){mutations++;return request.respond({status:405,body:'Read-only test'});}
        const response=await fetch(base+url.slice(url.indexOf('/api/')),{headers});statuses.push({path:url.slice(url.indexOf('/api/')),status:response.status});
        return request.respond({status:response.status,headers:{'Access-Control-Allow-Origin':'*','Content-Type':response.headers.get('content-type') || 'application/json'},body:Buffer.from(await response.arrayBuffer())});
      }catch(e){failures.push(e.message);return request.abort().catch(()=>{});}
    });
    const folder=path.resolve(__dirname,'../../tmp/reverse-verification');fs.mkdirSync(folder,{recursive:true});
    const front=process.env.REVERSE_TEST_FRONTEND || 'http://localhost:3223';
    const go=async suffix=>{await page.goto(front+'/dashboard/reverse'+suffix,{waitUntil:'networkidle0'});};
    await go('');
    try {await page.waitForFunction(()=>document.body.innerText.includes('records · Page'));}
    catch(error){console.log(JSON.stringify({url:page.url(),body:(await page.$eval('body',el=>el.innerText)).slice(0,1800),failures,statuses}));throw error;}
    assert.equal(await page.$$eval('table tbody tr',rows=>rows.length),Math.min(50,data.total));
    assert.ok((await page.$eval('table',el=>el.innerText)).includes(first.reverse_id));
    await page.screenshot({path:path.join(folder,'list.png'),fullPage:true});
    await page.$$eval('button',buttons=>buttons.find(b=>b.textContent.trim()==='Next').click());
    await page.waitForFunction(()=>document.body.innerText.includes('Page 2 of'));
    assert.ok((await page.$eval('table tbody tr td',el=>el.innerText))==='51');
    await page.$$eval('button',buttons=>buttons.find(b=>b.textContent.trim()==='Reset').click());
    await page.waitForFunction(()=>document.body.innerText.includes('Page 1 of'));
    await go('/'+first.reverse_id);await page.waitForSelector('table tbody tr');
    const detail=await service.getReverseIndentDetails(db,first.reverse_id);
    assert.equal(await page.$$eval('table tbody tr',rows=>rows.length),detail.items.length);
    await page.screenshot({path:path.join(folder,'details.png'),fullPage:true});
    let addChecked=false;
    if(session.user.permissions.includes('legacy:admin/reverseindent/add')) {
      await go('/add');await page.waitForSelector('#reverse-contract');
      const contracts=await service.searchContracts(db);
      let selected,products;
      for(const contract of contracts) {const p=await service.getContractProducts(db,contract.id);if(p.length){selected=contract;products=p;break;}}
      assert.ok(selected);
      await page.type('#reverse-contract',selected.title);await page.waitForSelector('[role="option"]');
      await page.$$eval('[role="option"]',(options,label)=>options.find(o=>o.textContent===label)?.click(),`${selected.title} (${selected.workorder})`);
      await page.waitForFunction(()=>document.querySelector('#reverse-design-product')?.options.length>1);
      await page.select('#reverse-design-product',String(products[0].id));await page.waitForFunction(()=>document.querySelector('#reverse-semi-product')?.options.length>1);
      const semi=await service.getSemiProducts(db);assert.ok(semi.length);
      await page.select('#reverse-semi-product',String(semi[0].id));
      await page.waitForFunction(value=>document.querySelector('#reverse-semi-product')?.value===value,{},String(semi[0].id));
      assert.ok((await page.$eval('table',el=>el.innerText)).includes('N/A'));
      assert.equal(await page.$eval('form',el=>el.checkValidity()),false);
      assert.equal(await page.$$eval('button[type="submit"]',buttons=>buttons.length),2);
      await page.type('input[placeholder="Enter Name"]','Read-only browser QA');
      await page.type('input[min="0.01"]','2.50');
      assert.equal(await page.$eval('form',el=>el.checkValidity()),true,'Raw return is optional');
      const group=await page.$('form table:nth-of-type(2) select');
      if(group) {
        const value=await group.evaluate(select=>select.options[1]?.value);
        if(value) {
          await group.select(value);await page.waitForFunction(()=>!!document.querySelector('form button[type="button"]'));
          await group.select(value);await page.waitForFunction(()=>document.body.innerText.includes('This Item Already added'));
          assert.equal(await page.$$eval('form button[type="button"]',buttons=>buttons.length),1);
          await page.click('form button[type="button"]');
        }
      }
      await page.screenshot({path:path.join(folder,'add.png'),fullPage:true});addChecked=true;
    }
    if(session.user.permissions.includes('legacy:admin/reverseindent/edit')) {
      const legacy=data.data.find(row=>row.status==='draft' && !row.draft_stock_clean);
      if(legacy) {
        await go('/'+legacy.reverse_id+'/edit');await page.waitForFunction(()=>document.body.innerText.includes('Only drafts issued today'));
        assert.equal(await page.$$eval('button[type="submit"]',buttons=>buttons.every(button=>button.disabled)),true);
        await page.screenshot({path:path.join(folder,'legacy-edit-blocked.png'),fullPage:true});
      }
    }
    assert.equal(mutations,0);assert.deepEqual(failures,[]);
    assert.ok(!statuses.some(row=>row.status>=500),JSON.stringify(statuses.filter(row=>row.status>=500)));
    console.log(JSON.stringify({result:'PASS',http:'authentication, saved permissions, Excel, PDF, edit-data',browser:'50-row pagination, reset, details',addChecked,mutations,screenshots:folder}));
  } finally {if(browser)await browser.close();if(server)await new Promise(resolve=>server.close(resolve));await db.close();await config.centralSequelize.close();}
})().catch(error=>{console.error(error.message,error.stack);process.exitCode=1;});
