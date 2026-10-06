const assert=require('node:assert/strict');
const puppeteer=require('puppeteer');
const fs=require('node:fs');
module.exports=async({base,token,get,evidence})=>{
 const browser=await puppeteer.launch({headless:true});const results=[];
 try {
 const page=await browser.newPage();await page.setViewport({width:1600,height:1000});
 await page.evaluateOnNewDocument(token=>{localStorage.setItem('accessToken',token);},token);
 await page.setRequestInterception(true);
 page.on('request',async request=>{
  try {
   const url=request.url();
   if(url.includes('/api/')){
    if(!['GET','OPTIONS'].includes(request.method()))return request.abort();
    const api=url.substring(url.indexOf('/api/')+4);
    if(api==='/auth/me')return request.respond({status:200,contentType:'application/json',headers:{'Access-Control-Allow-Origin':'*'},body:JSON.stringify({success:true,data:{user:{id:1,user_name:'Audit',role_id:101,db:'tirupati_tppl',permissions:[]}}})});
    // Serve real GET results from the isolated current-code backend; never restart user's server.
    const response=await fetch(base+api,{headers:{Authorization:`Bearer ${token}`}});
    if(process.env.AUDIT_INDENT_FIXTURE==='1' && /^\/indents(?:\?|$)/.test(api) && !new URLSearchParams(api.split('?')[1]).get('indent_id') && !new URLSearchParams(api.split('?')[1]).get('date_from')) {
     const body=await response.json();const live=body.data;
     body.data=[...live,...Array.from({length:124},(_,i)=>({...live[i%live.length],indent_id:100000+i}))];body.total=126;
     return request.respond({status:200,contentType:'application/json',headers:{'Access-Control-Allow-Origin':'*'},body:JSON.stringify(body)});
    }
    return request.respond({status:response.status,headers:{'Access-Control-Allow-Origin':'*','Content-Type':response.headers.get('content-type')||'application/json'},body:Buffer.from(await response.arrayBuffer())});
   }
   await request.continue();
  }catch(e){await request.abort().catch(()=>{});}
 });
 const click=async(text)=>{const handle=await page.evaluateHandle(text=>Array.from(document.querySelectorAll('button')).find(b=>b.textContent.trim()===text),text);assert.ok(await handle.asElement(),`button ${text}`);await handle.asElement().click();await handle.dispose();};
 const waitPager=async(pageNumber)=>page.waitForFunction(p=>{const n=document.querySelector('nav[aria-label="List pagination"]');return n && n.textContent.includes(`Page ${p} of`);},{timeout:30000},pageNumber);
 const rowCount=()=>page.evaluate(()=>document.querySelector('table')?.querySelectorAll('tbody > tr').length||0);
 const setInput=async(selector,value)=>{await page.$eval(selector,(el,value)=>{const setter=Object.getOwnPropertyDescriptor(HTMLInputElement.prototype,'value').set;setter.call(el,value);el.dispatchEvent(new Event('input',{bubbles:true}));},String(value));};
 for(const [name,url,route,field] of [
  ['Contract','contracts','/contracts','input[placeholder="Enter Contract Name"]'],
  ['Design Sheet','design-sheet','/designsheets','input[placeholder="Enter Contract ID"]'],
  ['PO','purchase/orders','/purchase-orders','input[name="po_number"]'],
  ['GRN Inspection','purchase/inspections','/grn-inspection','input[name="po_id"]'],
  ['GRN','purchase/grn','/grn','input[name="po_id"]'],
  ['Indents','purchase/indents','/indents','input[placeholder="e.g. 1023"]']]){
  if(process.env.AUDIT_UI_ONLY && name!==process.env.AUDIT_UI_ONLY)continue;
  if(process.env.AUDIT_SORT_ONLY==='1') {
   if(name==='Indents')continue;
   await page.goto('http://localhost:3000/dashboard/'+url+'?sort=id&direction=asc&page=2',{waitUntil:'networkidle0',timeout:60000});await waitPager(2);
   const expected=(await get(route,{sort:'id',direction:'asc',page:2})).body;
   const records=expected.items||expected.data;assert.equal(await rowCount(),records.length);
   const text=await page.$eval('table tbody > tr',el=>el.textContent);
   const record=records[0];const label=record.title||record.designsheetno||record.inspection_id||record.po_number||record.purchaseorder_id;
   assert.ok(text.includes(String(label)),name+' sorted row differs');
   await click('Previous');await waitPager(1);
   assert.equal(new URL(page.url()).searchParams.get('direction'),'asc');
   await page.reload({waitUntil:'networkidle0'});await waitPager(1);
   assert.equal(new URL(page.url()).searchParams.get('sort'),'id');
   console.log('SORT UI PASS',name);continue;
  }
  await page.goto('http://localhost:3000/dashboard/'+url,{waitUntil:'networkidle0',timeout:60000});await waitPager(1);
  const e={...evidence.find(e=>e.module===route)};if(name==='Indents' && process.env.AUDIT_INDENT_FIXTURE==='1'){e.total=126;e.pages=3;}assert.equal(await rowCount(),Math.min(50,e.total),name+' visible rows');
  const clipping=await page.evaluate(()=>{const table=document.querySelector('table');let clipped=false;for(let el=table.parentElement;el;el=el.parentElement){const style=getComputedStyle(el);if(['hidden','auto','scroll'].includes(style.overflowY)&&el.scrollHeight>el.clientHeight+2)clipped=true;}return clipped;});assert.equal(clipping,false,name+' rows vertically clipped');
  if(e.pages>1){await click('Next');await waitPager(2);assert.equal(await rowCount(),Math.min(50,e.total-50));
   if(name!=='Indents') {
    assert.ok(new URL(page.url()).searchParams.get('page')==='2');
    await page.reload({waitUntil:'networkidle0'});await waitPager(2);assert.equal(await rowCount(),Math.min(50,e.total-50));
   }
   await click('Previous');await waitPager(1);const middle=Math.ceil(e.pages/2); // Last page and its Previous also prove boundary controls.
   while (true) {
    const current=await page.evaluate(()=>Number(document.querySelector('nav[aria-label="List pagination"]').textContent.match(/Page (\d+)/)[1]));
    if(current===middle)break;
    const next=Math.min(current+2,middle);await click(String(next));await waitPager(next);
   }
   assert.equal(await rowCount(),Math.min(50,e.total-(middle-1)*50));
   await click('Last');await waitPager(e.pages);assert.equal(await rowCount(),e.total-(e.pages-1)*50);await click('Previous');await waitPager(e.pages-1);await click('First');await waitPager(1);
  }
  if(await page.$(field)){
   await setInput(field,'__pagination_empty__');await click('Search');await page.waitForFunction(()=>{const n=document.querySelector('nav[aria-label="List pagination"]');return n?.textContent.includes('of 0 records');},{timeout:30000});
   const reset=await page.$('button[title="Reset Filters"]');if(reset)await reset.click();else await click('Reset');await page.waitForFunction(total=>document.querySelector('nav[aria-label="List pagination"]')?.textContent.includes(`of ${total} records`),{timeout:30000},e.total);
  }
  // Apply a real search and a date filter together, starting from a later page.
  const first=(await get(route)).body; const rows=first.items||first.data; const sample=rows[0];
  if(sample && await page.$(field)) {
   if(e.pages>1){await click('Next');await waitPager(2);}
   const value=route==='/contracts'?sample.title:route==='/designsheets'?sample.contract_id:route==='/purchase-orders'?sample.po_number:route==='/indents'?sample.indent_id:sample.po_id||sample.purchaseorder_id;
   await setInput(field,value);
   if(e.pages>1)await waitPager(2); // Draft edits must not replace the active page.
   const dateInput=await page.$('input[type="date"]');
   if(dateInput){await setInput('input[type="date"]','1930-01-01');const dates=await page.$$('input[type="date"]');if(dates.length>1)await page.evaluate(el=>{Object.getOwnPropertyDescriptor(HTMLInputElement.prototype,'value').set.call(el,'2100-01-01');el.dispatchEvent(new Event('input',{bubbles:true}));},dates[1]);}
   await click('Search');await waitPager(1);
   await page.waitForFunction(()=>{const nav=document.querySelector('nav[aria-label="List pagination"]');return nav && /of [1-9]\d* records/.test(nav.textContent);},{timeout:30000});
   if(dateInput)assert.equal(await page.$eval('input[type="date"]',el=>el.value),'1930-01-01');
   if(name!=='Indents') {
    await page.reload({waitUntil:'networkidle0'});await waitPager(1);assert.equal(await page.$eval(field,el=>el.value),String(value));
    if(dateInput)assert.equal(await page.$eval('input[type="date"]',el=>el.value),'1930-01-01');
   }
   const reset=await page.$('button[title="Reset Filters"]');if(reset)await reset.click();else await click('Reset');
   await page.waitForFunction(total=>document.querySelector('nav[aria-label="List pagination"]')?.textContent.includes(`of ${total} records`),{timeout:30000},e.total);
   if(name!=='Indents') {
    await page.goBack({waitUntil:'networkidle0'});await waitPager(1);
    assert.equal(await page.$eval(field,el=>el.value),String(value));
    const resetAgain=await page.$('button[title="Reset Filters"]');if(resetAgain)await resetAgain.click();else await click('Reset');
    await page.waitForFunction(total=>document.querySelector('nav[aria-label="List pagination"]')?.textContent.includes(`of ${total} records`),{timeout:30000},e.total);
   }
  }
  if(name==='Indents') {
   await setInput('input[aria-label="Search indent table"]','__empty_table__');
   await page.waitForFunction(()=>document.querySelector('nav[aria-label="List pagination"]')?.textContent.includes('of 0 records'));
   await setInput('input[aria-label="Search indent table"]','');
   await page.waitForFunction(total=>document.querySelector('nav[aria-label="List pagination"]')?.textContent.includes(`of ${total} records`),{},e.total);
  }
  if(name==='PO') {
   const candidates=(await get('/indents/items/search',{q:'st'})).body.data || [];
   if(candidates.length) {
    const product=candidates[0];const prefix=product.item_name.slice(0,2);
    await setInput('input[name="item_name"]',prefix);
    await page.waitForFunction(()=>document.querySelectorAll('#po-product-options option').length>0);
    const label=product.item_name+(product.size_name?` (${product.size_name})`:'');
    await setInput('input[name="item_name"]',label);await click('Search');
    const expected=(await get('/purchase-orders',{search:'1',item_id:product.id})).body.total;
    await page.waitForFunction(total=>document.querySelector('nav[aria-label="List pagination"]')?.textContent.includes(`of ${total} records`),{},expected);
    assert.equal(new URL(page.url()).searchParams.get('item_id'),String(product.id));
    await page.reload({waitUntil:'networkidle0'});await waitPager(1);
    assert.equal(await page.$eval('input[name="item_name"]',el=>el.value),label);
    await (await page.$('button[title="Reset Filters"]')).click();
    await page.waitForFunction(total=>document.querySelector('nav[aria-label="List pagination"]')?.textContent.includes(`of ${total} records`),{},e.total);
   }
   const vendorRows=(await get('/vendors/search',{q:sample.vendor_name})).body.data || [];
   const vendor=vendorRows.find(v=>v.name===sample.vendor_name);
   if(vendor) {
    await setInput('input[name="vendor_name"]',vendor.name);
    await page.waitForFunction(()=>document.querySelectorAll('#po-vendor-options option').length>0);
    await click('Search');
    const expected=(await get('/purchase-orders',{search:'1',vendor_id:vendor.id})).body.total;
    await page.waitForFunction(total=>document.querySelector('nav[aria-label="List pagination"]')?.textContent.includes(`of ${total} records`),{},expected);
    assert.equal(new URL(page.url()).searchParams.get('vendor_id'),String(vendor.id));
    await (await page.$('button[title="Reset Filters"]')).click();
    await page.waitForFunction(total=>document.querySelector('nav[aria-label="List pagination"]')?.textContent.includes(`of ${total} records`),{},e.total);
   }
   const actions=await page.$$('button');const lastAction=await page.evaluateHandle(()=>Array.from(document.querySelectorAll('button')).filter(b=>b.textContent.trim().startsWith('Action')).at(-1));
   await lastAction.asElement().click();
   await page.waitForSelector('body > div.fixed.w-56');
   const visible=await page.$eval('body > div.fixed.w-56',el=>{const r=el.getBoundingClientRect();return r.top>=0 && r.bottom<=innerHeight && r.left>=0 && r.right<=innerWidth;});assert.equal(visible,true,'PO action menu clipped');
  }
  await page.screenshot({path:`tmp/pagination-${name.replaceAll(' ','-')}.png`,fullPage:true});results.push({module:name,visibleRows:Math.min(50,e.total),clipped:false,controls:'first/second/middle/previous/next/last/search+date/reset/empty verified where live pages exist'});console.log('UI PASS',name);
 }
 const output=process.env.AUDIT_INDENT_FIXTURE==='1'?'tmp/pagination-indent-fixture-results.json':'tmp/pagination-ui-results.json';
 const previous=fs.existsSync(output)?JSON.parse(fs.readFileSync(output,'utf8')):[];
 fs.writeFileSync(output,JSON.stringify([...previous.filter(p=>!results.some(r=>r.module===p.module)),...results],null,2));
 }finally {await browser.close();}
};
