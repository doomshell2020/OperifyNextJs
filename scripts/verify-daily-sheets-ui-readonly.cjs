// Local browser QA backed by SELECT-only service calls. All mutation requests
// are rejected before reaching any server. Start the built frontend on 3197.
const assert=require('node:assert/strict'),fs=require('node:fs/promises');
const {getTenantSequelize,centralSequelize}=require('../backend/src/config/sequelize');
const {launchPdfBrowser}=require('../backend/src/utils/pdfBrowser');
const R=require('../backend/src/modules/dailySheet/dailySheet.repository');
const S=require('../backend/src/modules/dailySheet/dailySheet.service');
const {legacyDailySheetPermissions}=require('../backend/src/modules/auth/legacyDailySheetAccess');
(async()=>{
  const name=process.argv[2] || 'tirupati_tppl',db=await getTenantSequelize(name);let browser;
  try{
    const before=await R.select(db,'SELECT * FROM production ORDER BY id');
    const meta=await R.metadata(db),requests=[],errors=[];
    const permissions=legacyDailySheetPermissions([],['index','add','edit','deletedailysheet','viewcontractdetail'].map(a=>'legacy:admin/production/'+a));
    browser=await launchPdfBrowser();const page=await browser.newPage();await page.setViewport({width:1440,height:1000});
    page.on('pageerror',e=>errors.push(e.message));
    await page.evaluateOnNewDocument(()=>{localStorage.setItem('accessToken','readonly-ui-session');localStorage.setItem('refreshToken','readonly-ui-session');});
    await page.setRequestInterception(true);
    page.on('request',request=>{void(async()=>{
      const url=new URL(request.url());if(!url.pathname.startsWith('/api/') && !url.pathname.startsWith('/public/'))return request.continue();
      const headers={'access-control-allow-origin':'*','access-control-allow-headers':'*','access-control-allow-methods':'GET,OPTIONS'};
      if(request.method()==='OPTIONS')return request.respond({status:204,headers});
      requests.push({path:url.pathname,query:Object.fromEntries(url.searchParams),method:request.method()});
      if(request.method()!=='GET')return request.respond({status:409,headers,body:'Database writes disabled in browser QA.'});
      const query=Object.fromEntries(url.searchParams);let data;
      if(url.pathname==='/api/auth/me')data={user:{id:1,user_name:'Read-only UI Verification',db:name,role_id:105,permissions,companies:[]}};
      else if(url.pathname==='/api/settings/logo')return request.respond({status:200,headers,contentType:'application/json',body:JSON.stringify({success:true})});
      else if(url.pathname==='/api/daily-sheets/metadata')data=meta;
      else if(url.pathname==='/api/daily-sheets')data=await S.list(db,query);
      else if(url.pathname==='/api/daily-sheets/lookup')data=await R.lookup(db,query);
      else if(url.pathname==='/api/daily-sheets/dependencies')data=await R.dependencies(db,query);
      else if(url.pathname.startsWith('/api/daily-sheets/datewise/'))data=await S.datewise(db,url.pathname.split('/').at(-1));
      else if(url.pathname.startsWith('/public/'))return request.respond({status:200,headers,contentType:'image/png',body:Buffer.from('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+j2gAAAABJRU5ErkJggg==','base64')});
      else return request.respond({status:404,headers,body:'Read-only QA does not handle '+url.pathname});
      return request.respond({status:200,headers,contentType:'application/json',body:JSON.stringify({success:true,data})});
    })().catch(error=>{errors.push(error.message);void request.abort();});});
    const base=process.env.DAILY_SHEET_UI_URL || 'http://127.0.0.1:3197';
    await page.goto(base+'/dashboard/production/sheet',{waitUntil:'networkidle0',timeout:90000});await page.waitForSelector('.daily-table');
    assert.equal(await page.$$eval('.daily-table thead tr',r=>r.length),2);
    assert.equal(await page.$eval('.daily-table tbody',e=>e.textContent.includes('No Record Found')),before.length===0);
    assert(await page.$('a[href="/dashboard/production/sheet/datewise/add"]'),'Date-wise grant derived from Add');
    await fs.mkdir('tmp/daily-sheet-verification',{recursive:true});
    await page.screenshot({path:'tmp/daily-sheet-verification/list-desktop.png',fullPage:true});
    await page.type('input[placeholder="PO No."]','1001');await page.click('form button.legacy-button');await page.waitForNetworkIdle();
    assert(requests.some(r=>r.path==='/api/daily-sheets' && r.query.po_id==='1001'));
    await page.reload({waitUntil:'networkidle0'});assert.equal(await page.$eval('input[placeholder="PO No."]',e=>e.value),'1001');
    await page.evaluate(()=>Array.from(document.querySelectorAll('button')).find(b=>b.textContent==='Reset').click());await page.waitForNetworkIdle();
    assert.equal(await page.$eval('input[placeholder="PO No."]',e=>e.value),'');
    await page.setViewport({width:390,height:844});await page.screenshot({path:'tmp/daily-sheet-verification/list-mobile.png',fullPage:true});
    assert(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth),'Mobile list overflow');
    await page.setViewport({width:1440,height:1000});await page.click('a[href="/dashboard/production/sheet/add"]');await page.waitForSelector('.daily-entry');await page.waitForNetworkIdle();
    const contracts=await R.lookup(db,{kind:'contracts',fetch:''});
    if(contracts.length){await page.type('input[aria-label="Contract Name"]',contracts[0].name.slice(0,5));await page.waitForNetworkIdle();await page.focus('input[aria-label="Contract Name"]');await page.waitForSelector('[role="option"]');await page.click('[role="option"]');await page.waitForNetworkIdle();assert(requests.some(r=>r.path==='/api/daily-sheets/dependencies' && r.query.contract_id));}
    assert.equal(await page.$$eval('input[type="radio"][name="completed"]',r=>r.length),meta.has_completed?2:0);
    await page.screenshot({path:'tmp/daily-sheet-verification/single-add.png',fullPage:true});
    await page.goto(base+'/dashboard/production/sheet/datewise/add',{waitUntil:'networkidle0'});await page.waitForSelector('.daily-machine-row');
    assert.equal(await page.$$eval('.daily-machine-row',r=>r.length),meta.machines.length);
    await page.evaluate(()=>Array.from(document.querySelectorAll('button')).find(b=>b.textContent==='+ Add More').click());
    assert.equal(await page.$$eval('.daily-machine-row',r=>r.length),meta.machines.length+1);
    await page.setViewport({width:390,height:844});assert(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth),'Mobile date-wise form overflow');
    await page.setViewport({width:1440,height:1000});await page.screenshot({path:'tmp/daily-sheet-verification/datewise-add.png',fullPage:true});
    await page.goto(base+'/dashboard/production/sheet/datewise/'+meta.today,{waitUntil:'networkidle0'});assert(await page.$eval('main',e=>e.textContent.includes('No Record Found')));
    assert.deepEqual(errors,[]);assert(!requests.some(r=>r.method!=='GET'));
    assert.deepEqual(await R.select(db,'SELECT * FROM production ORDER BY id'),before);
    console.log('Daily Sheet browser QA passed: listing, search/reset, URL/reload persistence, single add, real contract autocomplete and dependent dropdown request, 54-machine date-wise form, Add More, date-wise edit empty state, desktop/mobile layout, no page errors and no writes.');
  }finally{await browser?.close();await db.close();await centralSequelize.close();}
})().catch(e=>{console.error(e);process.exitCode=1;});
