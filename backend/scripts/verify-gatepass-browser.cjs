// Real Next.js pages and existing user/data. API proxy refuses every mutation.
require('../src/config/environment');
const assert=require('node:assert/strict');
const fs=require('node:fs');
const path=require('node:path');
const express=require('express');
const config=require('../src/config/sequelize');
const L=require('../src/modules/jobChallan/legacyStore');
const auth=require('../src/modules/auth/auth.service');
(async()=>{
  const name='tirupati_tppl',db=await config.getTenantSequelize(name);
  for(const connection of [db,config.centralSequelize]){const query=connection.query.bind(connection);connection.query=(sql,options)=>{assert.match(sql.trim(),/^(SELECT|SHOW)\b/i);return query(sql,options);};}
  let server,browser;
  try{
    const users=await L.select(config.centralSequelize,"SELECT mobile FROM users WHERE db=:name AND is_status!='N'",{name});let session;
    for(const u of users){try{session=auth._issueTokens(await auth.getSession(u.mobile,name));break;}catch{}}
    assert(session,'Existing session required');
    const list=await require('../src/modules/gatepass/gatepass.service').list(name),g=list.items[0];assert(g);
    const detail=await require('../src/modules/gatepass/gatepass.service').detail(name,g.id);
    const options=await require('../src/modules/gatepass/gatepass.service').options(name);assert(options.jcs.length && options.products.length);
    const app=express();app.use((req,res,next)=>req.method==='GET'?next():res.sendStatus(405));app.use(require('../src/app'));
    server=await new Promise(resolve=>{const s=app.listen(0,'127.0.0.1',()=>resolve(s));});
    const base=`http://127.0.0.1:${server.address().port}`,front='http://127.0.0.1:3217';
    browser=await require('../src/utils/pdfBrowser').launchPdfBrowser();const page=await browser.newPage();await page.setViewport({width:1440,height:1000});
    await page.evaluateOnNewDocument(({accessToken,refreshToken})=>{localStorage.setItem('accessToken',accessToken);localStorage.setItem('refreshToken',refreshToken);},session);
    await page.setRequestInterception(true);let mutations=0;const failures=[];
    page.on('pageerror',error=>failures.push(error.message));
    page.on('request',async request=>{try{
      const url=request.url();if(url.includes('/api/')){
        if(request.method()==='OPTIONS')return request.respond({status:204,headers:{'Access-Control-Allow-Origin':'*','Access-Control-Allow-Headers':'*','Access-Control-Allow-Methods':'GET'}});
        if(request.method()!=='GET'){mutations++;return request.respond({status:405,body:'Read-only browser verification'});}
        const apiPath=url.slice(url.indexOf('/api/'));
        const response=await fetch(base+apiPath,{headers:{Authorization:`Bearer ${session.accessToken}`}});
        return request.respond({status:response.status,headers:{'Access-Control-Allow-Origin':'*','Content-Type':response.headers.get('content-type') || 'application/json'},body:Buffer.from(await response.arrayBuffer())});
      }
      return request.continue();
    }catch(error){failures.push(error.message);return request.abort().catch(()=>{});}});
    const folder=path.resolve(__dirname,'../../tmp/gatepass-verification');
    const go=async suffix=>{await page.goto(front+'/dashboard/gatepass'+suffix,{waitUntil:'networkidle0'});};
    await go('');await page.waitForSelector('nav[aria-label="List pagination"]');
    assert((await page.$eval('table',el=>el.textContent)).includes(g.gatepass_no));
    await page.select('form.legacy-filter-row select','500');await page.$$eval('button',buttons=>buttons.find(b=>b.textContent.trim()==='Search').click());
    await page.waitForFunction(()=>location.search.includes('limit=500'));
    await page.screenshot({path:path.join(folder,'list.png'),fullPage:true});
    await go('/'+g.id);assert.equal(await page.$$eval('table:nth-of-type(2) tbody tr',rows=>rows.length),detail.gatepass.items.length);
    assert((await page.$eval('main.legacy-gatepass',el=>el.textContent)).includes('06-10-2026'));
    await page.screenshot({path:path.join(folder,'details.png'),fullPage:true});
    await go('/'+g.id+'/edit');await page.waitForSelector('#gatepass-jcs');
    assert.equal(await page.$$eval('.gatepass-select__multi-value',values=>values.length),String(g.jc_id).split(',').length);
    const quantities=await page.$$eval('input[type="number"]',inputs=>inputs.map(input=>input.value));
    assert.deepEqual(quantities,detail.gatepass.items.map(i=>i.quantity));
    assert.deepEqual(await page.$$eval('table textarea',inputs=>inputs.map(input=>input.value)),detail.gatepass.items.map(i=>i.description || ''));
    assert.equal(await page.$$eval('input[type="number"]',inputs=>inputs.every(i=>i.readOnly)),true);
    await page.screenshot({path:path.join(folder,'edit.png'),fullPage:true});
    await go('/create');await page.waitForSelector('#gatepass-jcs');
    assert.equal(await page.$eval('form.legacy-form',form=>form.checkValidity()),false);
    await page.type('#gatepass-jcs',options.jcs[0].challan_no);await page.keyboard.press('Enter');
    await page.waitForSelector('input[type="number"]');
    assert(await page.$eval('input[type="number"]',input=>input.readOnly));
    await page.type('#gatepass-jcs',options.jcs[1].challan_no);await page.keyboard.press('Enter');await page.waitForNetworkIdle();
    const combined=await require('../src/modules/gatepass/gatepass.service').jcData(name,[options.jcs[0].id,options.jcs[1].id]);
    assert.deepEqual(await page.$$eval('input[type="number"]',inputs=>inputs.map(i=>i.value)),combined.items.map(i=>i.quantity));
    await page.$$eval('input[name="gatepass_type"]',inputs=>inputs[1].click());
    await page.waitForSelector('#gatepass-material-0');await page.type('#gatepass-material-0',options.products[0].item_name);await page.waitForSelector('[role="listbox"]');assert(await page.$eval('[role="listbox"]',el=>el.getBoundingClientRect().height>0));await page.keyboard.press('Enter');
    await page.waitForFunction(()=>document.querySelector('table input[readonly]')?.value);
    assert.equal(await page.$eval('table input[readonly]',input=>input.value),options.products[0].unit_name || 'KG');
    assert.equal(await page.$eval('input[type="number"]',input=>input.readOnly),false);
    assert.equal(await page.$eval('form.legacy-form',form=>form.checkValidity()),false);
    await page.$$eval('button',buttons=>buttons.find(b=>b.textContent.trim()==='+ Add Item').click());
    assert.equal(await page.$$eval('table tbody tr',rows=>rows.length),2);
    await page.click('button[aria-label="Remove item 2"]');assert.equal(await page.$$eval('table tbody tr',rows=>rows.length),1);
    await page.screenshot({path:path.join(folder,'miscellaneous.png'),fullPage:true});
    await go('/'+g.id+'/pdf');await page.waitForSelector('iframe[title="PDF preview"]');
    assert.equal(mutations,0);assert.deepEqual(failures,[]);
    console.log('Existing-user browser checks passed: list and 500 limit, details/date/materials, 14-JC edit selection and 15 saved descriptions/quantities, searchable JC auto-fill, miscellaneous raw material/unit, add/remove rows, required validation, PDF preview. Zero save requests.');
  }finally{if(browser)await browser.close();if(server)await new Promise(r=>server.close(r));await db.close();await config.centralSequelize.close();}
})().catch(e=>{console.error(e);process.exitCode=1;});
