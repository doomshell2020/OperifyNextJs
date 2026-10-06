const assert=require('node:assert/strict'),puppeteer=require('puppeteer'),path=require('node:path'),fs=require('node:fs');
module.exports=async({server,token,dbName,list,receives,options})=>{
 const browser=await puppeteer.launch({headless:true});
 const results=[],errors=[];
 try{
  const page=await browser.newPage();await page.setViewport({width:1600,height:1000});
  await page.evaluateOnNewDocument(token=>{localStorage.setItem('accessToken',token);},token);
  page.on('pageerror',e=>errors.push(e.message));
  await page.setRequestInterception(true);
  page.on('request',async request=>{try{
   const url=request.url();if(url.includes('/api/')){
    if(!['GET','OPTIONS'].includes(request.method()))return request.abort();
    if(request.method()==='OPTIONS')return request.respond({status:204,headers:{'Access-Control-Allow-Origin':'*','Access-Control-Allow-Headers':'authorization,content-type','Access-Control-Allow-Methods':'GET,OPTIONS'},body:''});
    const api=url.slice(url.indexOf('/api/')+4);
    if(api==='/auth/me')return request.respond({status:200,contentType:'application/json',headers:{'Access-Control-Allow-Origin':'*','Access-Control-Allow-Headers':'authorization,content-type','Access-Control-Allow-Methods':'GET,OPTIONS'},body:JSON.stringify({success:true,data:{user:{id:1,user_name:'Read-only Audit',role_id:6,db:dbName,permissions:[]}}})});
    const response=await fetch(`http://127.0.0.1:${server.address().port}/api`+api,{headers:{Authorization:`Bearer ${token}`}});
    return request.respond({status:response.status,headers:{'Access-Control-Allow-Origin':'*','Access-Control-Allow-Headers':'authorization,content-type','Access-Control-Allow-Methods':'GET,OPTIONS','Content-Type':response.headers.get('content-type') || 'application/json'},body:Buffer.from(await response.arrayBuffer())});
   }return request.continue();
  }catch(error){errors.push(error.message);await request.abort().catch(()=>{});}});
  const navigate=async route=>{await page.goto('http://localhost:3000/dashboard/'+route,{waitUntil:'networkidle0',timeout:60000});};
  const click=async text=>{const button=await page.evaluateHandle(text=>Array.from(document.querySelectorAll('button')).find(e=>e.textContent.trim()===text),text);assert.ok(button.asElement(),text);await button.asElement().click();await button.dispose();};
  const screenshot=async name=>page.screenshot({path:path.join(__dirname,'../../tmp/jc-ui-'+dbName+'-'+name+'.png'),fullPage:true});
  await navigate('jc-challan');await page.waitForSelector('nav[aria-label="List pagination"]',{timeout:15000}).catch(async error=>{await screenshot('failure');console.log('UI failure',await page.$eval('body',e=>e.innerText),JSON.stringify(errors));throw error;});
  assert.ok((await page.$eval('nav[aria-label="List pagination"]',el=>el.textContent)).includes('of '+list.total+' records'));
  if(list.total>50){await click('Next');await page.waitForFunction(()=>document.querySelector('nav[aria-label="List pagination"]').textContent.includes('Page 2 of'));await click('Previous');await page.waitForFunction(()=>document.querySelector('nav[aria-label="List pagination"]').textContent.includes('Page 1 of'));}
  results.push('JC list, navigation and page controls');await screenshot('jc-list');
  if(list.items.length){await navigate('jc-challan/'+list.items[0].id);await page.waitForFunction(()=>document.body.textContent.includes('Receive History'));results.push('JC detail and receive history');await screenshot('jc-detail');}
  await navigate('jc-challan/create');await page.waitForSelector('input[type="radio"][value="In Progress"]');await page.click('input[type="radio"][value="In Progress"]');await page.waitForFunction(()=>document.body.textContent.includes('Semi-Finished Product'));results.push('JC create and semi-finished valuation form');await screenshot('jc-create');
  await navigate('gatepass');await page.waitForSelector('nav[aria-label="List pagination"]');results.push('Gatepass list and empty state');
  await navigate('gatepass/create');await page.waitForSelector('select[multiple]');
  await page.evaluate(()=>{const label=Array.from(document.querySelectorAll('label')).find(el=>el.textContent.includes('Miscellaneous'));label.querySelector('input').click();});
  await page.waitForFunction(()=>document.body.textContent.includes('Company Name') && document.querySelector('table select'));
  const rawOptions=await page.$eval('table select',el=>el.options.length);assert.ok(rawOptions>1);await click('+ Add Item');assert.equal(await page.$$eval('table tbody tr',rows=>rows.length),2);results.push('Gatepass miscellaneous company/raw-material form and add rows');await screenshot('gatepass-create');
  await navigate('jc-receive');await page.waitForSelector('nav[aria-label="List pagination"]');assert.ok((await page.$eval('nav[aria-label="List pagination"]',el=>el.textContent)).includes('of '+receives.total+' records'));results.push('Receive list and live balances');await screenshot('receive-list');
  if(options.eligible.length){const jc=options.eligible[0];await navigate('jc-receive/create?challan_id='+encodeURIComponent(jc.key));await page.waitForSelector('input[aria-label^="Receive quantity"]');const qty=await page.$('input[aria-label^="Receive quantity"]');await qty.type('999999999');assert.equal(await qty.evaluate(el=>el.validity.rangeOverflow),true);results.push('Incoming JC preselection, pending items and quantity range validation');await screenshot('receive-create');}
  else{await navigate('jc-receive/create');await page.waitForFunction(()=>document.body.textContent.includes('Reference JC No.'));results.push('Manual receive form');await screenshot('receive-create');}
  assert.deepEqual(errors,[]);fs.writeFileSync(path.join(__dirname,'../../tmp/jc-ui-results-'+dbName+'.json'),JSON.stringify(results,null,2));console.log('UI PASS',JSON.stringify(results));
 }finally{await browser.close();}
};
