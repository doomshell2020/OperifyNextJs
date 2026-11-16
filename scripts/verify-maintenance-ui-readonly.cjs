// Browser QA: all API traffic is intercepted; mutation requests never reach a server.
const assert=require('node:assert/strict'),fs=require('node:fs/promises');
const {getTenantSequelize,centralSequelize}=require('../backend/src/config/sequelize');
const {launchPdfBrowser}=require('../backend/src/utils/pdfBrowser');
const R=require('../backend/src/modules/maintenance/maintenance.repository');
const S=require('../backend/src/modules/maintenance/maintenance.service');
const PDF=require('../backend/src/modules/maintenance/maintenance.pdf');
const {legacyMaintenancePermissions}=require('../backend/src/modules/auth/legacyMaintenanceAccess');
(async()=>{const name=process.argv[2] || 'tirupati_tppl',db=await getTenantSequelize(name);let browser;
try{
  const before=await R.select(db,'SELECT * FROM maintenance ORDER BY id'),meta=await R.metadata(db,24003);
  // Test-only dropdown fallback when this old local tenant lacks a required role.
  for(const [index,group] of ['assigned','shifts','incharges','heads'].entries())if(!meta[group].length)meta[group]=[{id:90001+index,user_name:'QA '+group}];
  const permissions=legacyMaintenancePermissions([],['legacy:admin/maintenance/index']);
  const requests=[],errors=[],overrides=new Map(),deleted=new Set();let denied=false,simulateWrites=false;
  browser=await launchPdfBrowser();const page=await browser.newPage();await page.setViewport({width:1440,height:1000});
  page.on('pageerror',e=>errors.push(e.message));
  await page.evaluateOnNewDocument(()=>{localStorage.setItem('accessToken','read-only-maintenance-test');localStorage.setItem('refreshToken','read-only-maintenance-test');});
  await page.setRequestInterception(true);
  page.on('request',request=>{void(async()=>{
    const url=new URL(request.url());if(!url.pathname.startsWith('/api/') && !url.pathname.startsWith('/public/'))return request.continue();
    const headers={'access-control-allow-origin':'*','access-control-allow-headers':'*','access-control-allow-methods':'GET,OPTIONS,PATCH,POST,PUT,DELETE'};
    const respond=(status,data)=>request.respond({status,headers,contentType:'application/json',body:JSON.stringify(data)});
    if(request.method()==='OPTIONS')return request.respond({status:204,headers});
    const q=Object.fromEntries(url.searchParams),body=request.postData()?JSON.parse(request.postData()):undefined;requests.push({method:request.method(),path:url.pathname,query:q,body});
    if(request.method()!=='GET'){
      if(!simulateWrites)return respond(409,{success:false,error:{message:'Database writes disabled in browser QA.'}});
      const id=Number(url.pathname.split('/')[3]);let message;
      if(request.method()==='PATCH' && url.pathname.endsWith('/status')){
        const row={...await S.detail(db,id),...overrides.get(id)};
        try{S.transition(row.maintenance_status,body.status);}catch(e){return respond(e.status || 400,{success:false,error:{message:e.message}});}
        overrides.set(id,{...overrides.get(id),maintenance_status:body.status,assigned_date:R.today()});message='Maintenance status updated successfully..';
      }else if(request.method()==='POST' && url.pathname.endsWith('/complete')){
        overrides.set(id,{...overrides.get(id),...body,maintenance_status:'completed'});message='Maintenance marked as completed.';
      }else if(request.method()==='POST' && url.pathname==='/api/maintenance')message='Maintenance details added successfully.';
      else if(request.method()==='PUT' && /^\/api\/maintenance\/\d+$/.test(url.pathname)){overrides.set(id,{...overrides.get(id),...body});message='Maintenance details successfully updated.';}
      else if(request.method()==='DELETE' && /^\/api\/maintenance\/\d+$/.test(url.pathname)){deleted.add(id);message='Maintenance details deleted successfully.';}
      else return respond(409,{success:false,error:{message:'Unexpected mutation blocked in browser QA.'}});
      return respond(200,{success:true,data:{id:id || 900001,message,warnings:[]}});
    }
    let data;
    if(url.pathname==='/api/auth/me')data={user:{id:24003,user_name:'Read-only QA',db:name,role_id:105,companies:[],permissions:denied?[]:permissions}};
    else if(url.pathname==='/api/settings/logo')return respond(200,{success:true});
    else if(url.pathname==='/api/maintenance/metadata')data=meta;
    else if(url.pathname==='/api/maintenance'){data=await S.list(db,q);data.items=data.items.filter(r=>!deleted.has(r.id)).map(r=>({...r,...overrides.get(r.id)}));data.total-=deleted.size;}
    else if(url.pathname==='/api/maintenance/machines')data=await R.machines(db,q.fetch);
    else if(/^\/api\/maintenance\/\d+$/.test(url.pathname)){data=await S.detail(db,url.pathname.split('/').at(-1));data={...data,...overrides.get(data.id)};}
    else if(url.pathname.startsWith('/public/'))return request.respond({status:200,headers,contentType:'image/png',body:Buffer.from('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+j2gAAAABJRU5ErkJggg==','base64')});
    else return respond(404,{success:false,error:{message:'Read-only QA route not handled'}});
    return respond(200,{success:true,data});
  })().catch(e=>{errors.push(e.message);void request.abort();});});
  const base=process.env.MAINTENANCE_UI_URL || 'http://127.0.0.1:3198',url=base+'/dashboard/maintenance/breakdowns';
  await fs.mkdir('tmp/maintenance-verification',{recursive:true});
  await page.goto(url,{waitUntil:'networkidle0',timeout:90000});await page.waitForSelector('.maintenance-table');
  assert.equal(await page.$$eval('.maintenance-table th',els=>els.length),11);
  const active=before.filter(r=>r.status==='Y');assert.equal(await page.$$eval('.maintenance-table tbody tr',els=>els.length),Math.min(50,active.length) || 1);
  await page.screenshot({path:'tmp/maintenance-verification/list-desktop.png',fullPage:true});
  await page.type('input[placeholder="Assigned To"]','Mukesh');await page.click('.maintenance-filters button.legacy-button');await page.waitForNetworkIdle();
  assert(requests.some(r=>r.path==='/api/maintenance' && r.query.assigned_to==='Mukesh'));
  await page.reload({waitUntil:'networkidle0'});assert.equal(await page.$eval('input[placeholder="Assigned To"]',e=>e.value),'Mukesh');
  await page.evaluate(()=>Array.from(document.querySelectorAll('button')).find(b=>b.textContent==='Reset').click());await page.waitForNetworkIdle();
  assert.equal(await page.$eval('input[placeholder="Assigned To"]',e=>e.value),'');
  await page.select('.maintenance-table tbody select','completed');await page.waitForSelector('dialog[open]');
  assert(!(await page.$eval('dialog button[type="submit"]',e=>e.disabled)),'Submit must not depend on cached schema metadata');
  const postsBefore=requests.filter(r=>r.method==='POST').length;await page.click('dialog button[type="submit"]');assert.equal(requests.filter(r=>r.method==='POST').length,postsBefore,'Remarks validation must prevent an empty submission');
  await page.type('dialog textarea[name="remarks"]','Read-only completion error test');await page.click('dialog button[type="submit"]');await page.waitForNetworkIdle();assert(await page.$('dialog[open]'),'Failed completion must keep the form open');assert(await page.$eval('dialog',e=>e.textContent.includes('Database writes disabled in browser QA.')));
  await page.screenshot({path:'tmp/maintenance-verification/completion-modal.png',fullPage:true});
  await page.click('dialog button[aria-label="Close"]');await page.waitForSelector('dialog:not([open])');
  await page.select('.maintenance-table tbody select','assigned');await page.waitForNetworkIdle();assert(await page.$eval('main',e=>e.textContent.includes('Database writes disabled in browser QA.')));
  // Simulate manually provisioned schema/provider responses without writing to MySQL.
  simulateWrites=true;await page.select('.maintenance-table tbody select','assigned');await page.waitForNetworkIdle();assert(await page.$eval('main',e=>e.textContent.includes('Maintenance status updated successfully')));
  await page.select('.maintenance-table tbody select','completed');await page.waitForSelector('dialog[open]');await page.type('dialog input[name="completion_time"]','2 hrs');await page.type('dialog textarea[name="remarks"]','Repair verified');await page.click('dialog button[type="submit"]');await page.waitForSelector('dialog:not([open])');await page.waitForNetworkIdle();
  assert.equal(await page.$eval('.maintenance-table tbody select',e=>e.value),'completed');assert(requests.some(r=>r.path.endsWith('/complete') && r.body?.completion_time==='2 hrs' && r.body?.remarks==='Repair verified'));
  await page.select('.maintenance-table tbody select','pending');await page.waitForNetworkIdle();assert.equal(await page.$eval('.maintenance-table tbody select',e=>e.value),'completed');assert(await page.$eval('main',e=>e.textContent.includes('Cannot change status from completed')));
  await page.setViewport({width:390,height:844});assert(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth),'Mobile page overflow');await page.screenshot({path:'tmp/maintenance-verification/list-mobile.png',fullPage:true});
  await page.setViewport({width:1440,height:1000});await page.click('a[href="/dashboard/maintenance/breakdowns/add"]');await page.waitForSelector('.maintenance-form');await page.waitForNetworkIdle();
  assert.equal(await page.$$eval('.maintenance-form select',els=>els.length),4);assert.equal(await page.$eval('.maintenance-form input[type="date"]',e=>e.value),meta.today);
  const machines=await R.machines(db,'');if(machines.length){await page.type('input[aria-label="Machine Name"]',machines[0].name.slice(0,3));await page.waitForNetworkIdle();await page.focus('input[aria-label="Machine Name"]');await page.waitForSelector('[role="option"]');await page.click('[role="option"]');assert.equal(await page.$eval('input[aria-label="Machine Name"]',e=>e.value),machines[0].name);}
  await page.screenshot({path:'tmp/maintenance-verification/add-desktop.png',fullPage:true});await page.setViewport({width:390,height:844});assert(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth),'Mobile form overflow');
  await page.type('input[placeholder="Enter Type Of Breakdown"]','QA bearing');await page.type('.maintenance-form input[type="number"]','3');
  const selects=await page.$$('.maintenance-form select');for(const [index,group] of ['assigned','shifts','incharges','heads'].entries())await selects[index].select(String(meta[group][0].id));await page.type('textarea[placeholder="Enter Remark"]','QA simulated create');await page.click('form button.legacy-button');await page.waitForSelector('.maintenance-table');await page.waitForNetworkIdle();assert(requests.some(r=>r.method==='POST' && r.path==='/api/maintenance' && r.body.remark==='QA simulated create'));
  if(meta.actions.edit && active.length){await page.goto(url+'/edit/'+active[0].id,{waitUntil:'networkidle0'});await page.waitForSelector('.maintenance-form');assert.equal(await page.$$eval('.maintenance-form select',els=>els.length),0);await page.screenshot({path:'tmp/maintenance-verification/edit-mobile.png',fullPage:true});await page.focus('textarea[placeholder="Enter Remark"]');await page.keyboard.down('Control');await page.keyboard.press('A');await page.keyboard.up('Control');await page.keyboard.type('QA simulated edit');await page.click('form button.legacy-button');await page.waitForSelector('.maintenance-table');await page.waitForNetworkIdle();assert(requests.some(r=>r.method==='PUT' && r.path==='/api/maintenance/'+active[0].id && r.body.remark==='QA simulated edit'));}
  if(meta.actions.delete){const count=await page.$$eval('.maintenance-table tbody tr',r=>r.length);page.once('dialog',dialog=>dialog.accept());await page.click('button[aria-label^="Delete maintenance"]');await page.waitForNetworkIdle();assert.equal(await page.$$eval('.maintenance-table tbody tr',r=>r.length),count-1);}
  denied=true;await page.goto(url,{waitUntil:'networkidle0'});assert(await page.$eval('body',e=>e.textContent.includes('You do not have permission')));
  const report=await browser.newPage();const rows=await R.rows(db,{});await report.setContent(PDF.html(rows),{waitUntil:'networkidle0'});await report.setViewport({width:1123,height:794});await report.screenshot({path:'tmp/maintenance-verification/pdf-layout.png',fullPage:true});
  const buffer=await report.pdf({format:'A4',landscape:true,preferCSSPageSize:true,printBackground:true});assert(Buffer.from(buffer).subarray(0,4).equals(Buffer.from('%PDF')));await fs.writeFile('tmp/maintenance-verification/report-test.pdf',buffer);
  assert.deepEqual(errors,[]);assert.deepEqual(await R.select(db,'SELECT * FROM maintenance ORDER BY id'),before);
  console.log('Maintenance browser QA passed: listing, filters/reset, reload persistence, autocomplete, simulated create/edit/delete/assignment/completion, required remarks, backward-status rejection, failed-submit recovery, denied access, desktop/mobile layout, PDF rendering, no page errors, unchanged database. All mutation responses were in-memory fixtures.');
}finally{await browser?.close();await db.close();await centralSequelize.close();}})().catch(e=>{console.error(e);process.exitCode=1;});
