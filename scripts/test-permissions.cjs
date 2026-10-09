// Read-only regression checks. No database connections, credentials or writes.
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const { createRequire } = require('node:module');
const root = path.resolve(__dirname, '..');
const backendRequire = createRequire(path.join(root, 'backend/package.json'));
const frontendRequire = createRequire(path.join(root, 'frontend/package.json'));
const permission = require('../backend/src/modules/jobChallan/legacyPermission');
const { requirePermission, requireAnyPermission } = require('../backend/src/middleware/permission');
const { isLegacyToday, requireCurrentIndent } = require('../backend/src/middleware/legacyActionDate');
const today = new Intl.DateTimeFormat('en-CA', { timeZone:'Asia/Kolkata',year:'numeric',month:'2-digit',day:'2-digit' }).format(new Date());
const key = (controller, action) => `legacy:admin/${controller}/${action}`;
const combinations = [[], ['index'], ['index','edit'], ['index','add'], ['index','delete'], ['index','add','edit','delete','view','export','approve','revised','receive','dispatch','status']];

function evaluate(fn, req) {
  return new Promise((resolve, reject) => {
    const res = { status(code) { this.code = code; return this; }, json(body) { resolve({ code:this.code || 200, body }); } };
    Promise.resolve(fn(req, res, error => error ? reject(error) : resolve({ code:200 }))).catch(reject);
  });
}

// Load actual route declarations, replacing business handlers and tenant/auth
// with inert stubs; authorization middleware itself is real.
function routerFor(module) {
  const filename = path.join(root, `backend/src/modules/${module}/${module}.routes.js`);
  const handler = () => stub;
  const stub = new Proxy(handler, { get: () => stub });
  const exports = { exports:{} };
  vm.runInNewContext(fs.readFileSync(filename,'utf8'), { module:exports, exports:exports.exports,
    require(name) {
      if (name==='express') return backendRequire(name);
      if (name.endsWith('/legacyPermission') || name==='./legacyPermission') return permission;
      if (name.endsWith('/middleware/permission')) return { requirePermission, requireAnyPermission };
      if (name.endsWith('/middleware/legacyActionDate')) return { requireCurrentIndent };
      if (name==='multer') return Object.assign(() => ({single:()=>handler,fields:()=>handler}), {diskStorage:()=>({})});
      if (name==='path') return path;
      if (name==='fs') return fs;
      return stub;
    }, __dirname:path.dirname(filename), console, Buffer, process });
  return exports.exports;
}

// Expected URLs are the PHP operations, independent of route implementation.
const routes = [
  ['contract','get','/','contracts','index'],['contract','post','/','contracts','add'],
  ['contract','put','/:id','contracts','edit'],['contract','delete','/:id','contracts','delete'],
  ['contract','get','/:id/pdf','production','viewcontractdetailspdf'],['contract','get','/:id/edit-data','contracts','edit'],
  ['designsheet','get','/','designsheet','index'],['designsheet','post','/','designsheet','add'],
  ['designsheet','put','/:id','designsheet','edit'],['designsheet','delete','/:id','designsheet','delete'],
  ['designsheet','delete','/details/:id','designsheet','deletedata'],['designsheet','get','/view/:designsheetno/pdf','designsheet','viewdesignsheet'],
  ['purchaseOrder','post','/','purchaseorder','add'],['purchaseOrder','put','/:id','purchaseorder','revised'],
  ['purchaseOrder','delete','/:id','purchaseorder','delete'],['purchaseOrder','post','/:id/delivery-note','purchaseorder','deliverynote'],
  ['purchaseOrder','get','/:id/pdf','purchaseorder','view'],
  ['indentpo','get','/','indentpo','index'],['indentpo','post','/','indentpo','add'],
  ['indentpo','put','/:indent_id','indentpo','edit'],['indentpo','delete','/:indent_id','indentpo','delete'],
  ['indentpo','get','/:indent_id/pdf','indentpo','viewindentpopdf'],['indentpo','get','/export','indentpo','indentpoexcel'],
  ['indent','post','/finalize','indent','add'],['indent','post','/temp','indent','add'],
  ['indent','delete','/temp/:id','indent','add'],['indent','get','/:indent_id/pdf','indent','view'],
  ['grn','post','/','goodsreceived','add'],['grn','get','/:id/pdf','goodsreceived','view'],['grn','get','/export','goodsreceived','grnexcel'],
  ['grnInspection','get','/','goodsreceived','grninspection'],['grnInspection','get','/export/excel','goodsreceived','grninspectionexcel'],
  ['jobChallan','post','/','jobchallan','add'],['jobChallan','delete','/:id','jobchallan','delete'],
  ['jobChallan','post','/:id/receive','jobchallan','itemreceived'],['jobChallan','get','/:id/pdf','jobchallan','viewpdf'],
  ['jobReceive','post','/','jobchallan','receiveadd'],['jobReceive','get','/','jobchallan','receiveindex'],['jobReceive','get','/:id/pdf','jobchallan','viewreturnpdf'],
  ['gatepass','post','/','gatepasses','add'],['gatepass','put','/:id','gatepasses','edit'],['gatepass','get','/:id/pdf','gatepasses','gatepasspdf'],
  ['gatepass','get','/:id/edit-data','gatepasses','edit'],
  ['reverseIndent','post','/','reverseindent','add'],['reverseIndent','delete','/:id','reverseindent','delete'],['reverseIndent','get','/:id/pdf','reverseindent','viewreverseindentpdf'],
  ['stockRegister','get','/export','stockregister','summaryexcel'],['stockRegister','get','/daily/export','stockregister','dailystockexcel'],
  ['stockRegister','get','/details/received','stockregister','receivedstock'],['stockRegister','get','/details/dispatched','stockregister','dispatchedstock'],
  ['settings','post','/products','additem','add'],['settings','put','/products/:id','additem','edit'],['settings','delete','/products/:id','additem','delete'],
  ['settings','patch','/products/:id/status','additem','status'],['settings','get','/products/export','additem','viewitemexcel'],
  ['settings','post','/categories','itemcategory','add'],['settings','put','/categories/:id','itemcategory','edit'],['settings','delete','/categories/:id','itemcategory','delete'],
  ['settings','patch','/categories/:id/status','itemcategory','status'],['settings','patch','/categories/:id/print-status','itemcategory','printstatus'],
  ['settings','post','/users','roles','add'],['settings','put','/users/:id','roles','add'],['settings','delete','/users/:id','roles','delete'],
  ['settings','patch','/users/:id/status','roles','status'],['vendor','put','/:id','vendors','add'],
  ['emd','get','/','emd','index'],['quotation','get','/:id/details','quotation','viewquotationdetail'],['payment','get','/:id/details','paymentmanager','viewamount'],
  ['emd','get','/:id/details','emd','viewamount'],['jobChallan','post','/vendors','jobchallan','ajaxaddsubcontractor'],
];

async function backendTests() {
  let checks=0;
  for (const controller of ['contracts','designsheet','purchaseorder','goodsreceived','indentpo','indent','jobchallan','gatepasses','stockregister','additem','itemcategory','roles','reverseindent','emd','quotation','paymentmanager']) {
    for (const grants of combinations) for (const action of combinations.at(-1)) {
      const result = await evaluate(permission(controller,action), { user:{ role_id:101, permissions:grants.map(a=>key(controller,a)) } });
      assert.equal(result.code, grants.includes(action)?200:403, `${controller}/${action}`); checks++;
    }
  }
  for (const [module,method,url,controller,action] of routes) {
    const route=routerFor(module).stack.find(layer=>layer.route?.path===url && layer.route.methods[method]).route;
    const guard=route.stack[0].handle;
    for (const permissions of [[],[key(controller,'unrelated')],[key(controller,action)]]) {
      const result=await evaluate(guard,{user:{role_id:101,permissions},query:{},params:{}});
      assert.equal(result.code,permissions.includes(key(controller,action))?200:403,`${module} ${method} ${url}`); checks++;
    }
  }
  for (const mode of ['revised','delivery']) {
    const route=routerFor('purchaseOrder').stack.find(l=>l.route?.path==='/:id/pdf').route;
    assert.equal((await evaluate(route.stack[0].handle,{user:{permissions:[key('purchaseorder','view')]},query:{mode}})).code,403);
  }
  const req={user:{permissions:[]},params:{indent_id:'1'},dbPool:{query:async()=>[{issue_date:'2000-01-01'}]}};
  assert.equal((await evaluate(requireCurrentIndent,req)).code,403);
  req.dbPool.query=async()=>[{issue_date:today}]; assert.equal((await evaluate(requireCurrentIndent,req)).code,200);
  assert.equal(isLegacyToday('2026-10-09',new Date('2026-10-08T18:31:00Z')),true);
  assert.equal(isLegacyToday('2026-10-08',new Date('2026-10-08T18:31:00Z')),false);
  console.log(`Backend: ${checks} grant/route checks passed; print modes and persisted-date restrictions passed.`);
}

async function permissionLoadingTests() {
  const module = {exports:{}};
  const db = { permission_access: {findAll:async options=>{
    assert.deepEqual(JSON.parse(JSON.stringify(options.where)),{role_id:'fixture-role',is_permission:'1'});
    return [{p_lable_id:1},{p_lable_id:2}];
  }}, permission_label: {findAll:async options=>{
    assert.deepEqual(Array.from(options.where.id),[1,2]);
    return [{url:'admin/contracts/edit_other'},{url:'admin/designsheet/edit'}];
  }}};
  vm.runInNewContext(fs.readFileSync(path.join(root,'backend/src/modules/auth/auth.repository.js'),'utf8'),
    {module,exports:module.exports,require:()=>({centralModels:db}),console});
  const result=await module.exports.getUserPermissions('fixture-role');
  assert(!result.includes('contracts:edit'),'A similarly named URL must not grant Edit');
  assert(result.includes('designsheet:edit'));
  assert(result.includes('legacy:admin/contracts/edit_other'));
  console.log('Permission loading: central role/allowed-label join and exact alias matching passed.');
}

// Render real manager components with read-only fixtures and the actual action
// hook. Query/service infrastructure is replaced; permission gates are not.
const React=frontendRequire('react');
const {renderToStaticMarkup}=frontendRequire('react-dom/server');
const ts=frontendRequire('typescript');
let grants=[], fixture={}, seededRows=[], currentPath='/dashboard';
const noop=()=>{};
const dummy=()=>null;
const canPermission=p=>grants.includes(p);
function loadTS(filename) {
  const m={exports:{}};
  const source=ts.transpileModule(fs.readFileSync(filename,'utf8'),{compilerOptions:{jsx:ts.JsxEmit.ReactJSX,module:ts.ModuleKind.CommonJS,esModuleInterop:true}}).outputText;
  vm.runInNewContext(source,{module:m,exports:m.exports,Date,Intl,console,require(name){
    if(name==='react')return {...React,useEffect:noop,useState:value=>[Array.isArray(value)&&value.length===0?seededRows:typeof value==='function'?value():value===true?false:value,noop]};
    if(name==='react/jsx-runtime')return frontendRequire(name);
    if(name==='react-dom')return {createPortal:dummy};
    if(name.includes('PermissionContext'))return {usePermission:()=>({hasPermission:canPermission,permissions:grants})};
    if(name.includes('useLegacyActionAccess'))return loadTS(path.join(root,'frontend/components/ui/useLegacyActionAccess.ts'));
    if(name.includes('useJcAccess'))return loadTS(path.join(root,'frontend/components/jobChallan/useJcAccess.ts'));
    if(name.includes('AuthContext'))return {useAuth:()=>({user:{id:1,db:'fixture',permissions:grants},loading:false})};
    if(name.includes('legacyActionDate'))return {isLegacyToday};
    if(name.includes('useListLocation'))return {useListLocation:()=>true};
    if(name==='@tanstack/react-query')return {useQuery:options=>({data:fixture[options.queryKey[0]],isLoading:false,isError:false,refetch:noop}),useQueryClient:()=>({invalidateQueries:noop}),useMutation:()=>({mutate:noop,isPending:false})};
    if(name==='next/navigation')return {useRouter:()=>({push:noop}),usePathname:()=>currentPath};
    if(name==='next/link')return {__esModule:true,default:({children,href,...props})=>React.createElement('a',{href,...props},children)};
    if(name==='lucide-react')return new Proxy({},{get:(_,icon)=>()=>React.createElement('svg',{'data-icon':icon})});
    if(name.endsWith('.css'))return new Proxy({},{get:(_,prop)=>String(prop)});
    if(name.includes('dateFormatter'))return {formatContractDate:v=>v || '',formatDate:v=>v || ''};
    if(name.includes('ListPagination'))return {ListPagination:dummy,LEGACY_LIST_LIMIT:50};
    return new Proxy({__esModule:true,default:dummy},{get:(obj,prop)=>prop in obj?obj[prop]:dummy});
  }});
  return m.exports;
}
function render(file,permissions,data,rows=[],props={}) {
  grants=permissions;fixture=data;seededRows=rows;
  return renderToStaticMarkup(React.createElement(loadTS(path.join(root,'frontend',file)).default,props));
}
function actionCell(markup) { return [...markup.matchAll(/<tr[^>]*>(.*?)<\/tr>/gs)].at(-1)[1].match(/<td[^>]*>(.*?)<\/td>/gs).at(-1); }
function frontendTests() {
  const row={id:1,indent_id:1,issue_date:today,contract_name:'Contract',product_name:'Product',machine_name:'Machine',indent_id:1};
  for (const actions of combinations) {
    const html=render('app/dashboard/purchase/indentpo/page.tsx',actions.map(a=>key('indentpo',a)),{},[row]);
    const cell=actionCell(html);
    assert.equal(cell.includes('>Edit</a>'),actions.includes('edit'));
    assert.equal(cell.includes('>Delete</button>'),actions.includes('delete'));
    assert(!cell.includes('Printer'),'PHP Indentpo rows have no Print action');
    if(!actions.includes('edit')&&!actions.includes('delete')) assert(!/<button|<a /.test(cell),'Action cell must be blank');
  }
  const old=render('app/dashboard/purchase/indentpo/page.tsx',[key('indentpo','edit'),key('indentpo','delete')],{},[{...row,issue_date:'2000-01-01'}]);
  assert(!/<button|<a /.test(actionCell(old)),'Historical indent row must be blank');
  for(const [file,controller,queryKey,data] of [
    ['admin/products','additem','products',{data:[{id:1,item_name:'Item',status:'Y',current_stock:1}]}],
    ['admin/categories','itemcategory','categories',[{id:1,category_name:'Category',status:'Y',is_print:'Y'}]],
    ['contracts','contracts','contracts',{data:[{id:1,title:'Contract',cost:1,designsheet_count:0}],total:1}],
    ['design-sheet','designsheet','designsheets',{data:[{id:1,designsheetno:'DS-1',quantity:1,indentpo_count:0}],total:1}],
  ]) {
    for(const actions of combinations) {
      const html=render(`app/dashboard/${file}/page.tsx`,actions.map(a=>key(controller,a)),{[queryKey]:data});
      const cell=actionCell(html);
      assert.equal(/title="Edit"/.test(cell),actions.includes('edit'),file+' Edit');
      assert.equal(/title="Delete"/.test(cell),actions.includes('delete'),file+' Delete');
      if(!actions.includes('edit')&&!actions.includes('delete')&&!actions.includes('status'))assert(!/<button|<a /.test(cell),file+' blank action');
    }
  }
  const po={id:1,amount:1,po_number:'1',is_latest_revision:1,amendment_no:0,delivery_notes_count:0};
  let html=render('app/dashboard/purchase/orders/page.tsx',[key('purchaseorder','index')],{'purchase-orders':{items:[po],total:1,page:1}});
  assert(html.includes('>1</td>') || html.includes('>1</button>'),'PO fixture row must render');
  assert(!/<button/.test(actionCell(html)),'No empty PO Action menu');
  html=render('app/dashboard/purchase/orders/page.tsx',[key('purchaseorder','delete')],{'purchase-orders':{items:[po],total:1,page:1}});
  assert(/<button/.test(actionCell(html)),'Permitted PO Action menu');
  for(const [file,controller,queryKey,operation] of [
    ['emd','emd','emd-list','viewamount'],['payments','paymentmanager','payments-list','viewamount'],
    ['quotations','quotation','quotations-list','viewquotationdetail'],['purchase/grn','goodsreceived','grn','viewgrndetail'],
  ]) {
    const row={id:1,total_amt:1,amount:1,status:'Y'};
    const data=queryKey==='grn'?{data:[row],pagination:{total:1,page:1}}:[row];
    for(const allowed of [false,true]) {
      const html=render(`app/dashboard/${file}/page.tsx`,[key(controller,'index'),...(allowed?[key(controller,operation)]:[])],{[queryKey]:data});
      assert.equal(/<button|<a /.test(actionCell(html)),allowed,file+' view action');
    }
  }
  const jc={id:1,challan_id:1,challan_no:'JC-1',gatepass_no:'GP-1',status:'Pending',total_received:1,pending_qty:0,sender_db:'fixture',is_manual:0};
  for(const module of ['gatepass','receive']) {
    const controller=module==='gatepass'?'gatepasses':'jobchallan';
    const listAction=module==='gatepass'?'index':'receiveindex';
    const query=module==='gatepass'?'gatepass':'jc-receive';
    html=render('components/jobChallan/JcModuleList.tsx',[key(controller,listAction)],{[query]:{items:[jc],total:1}},[],{module});
    assert(!/<button|<a /.test(actionCell(html)),query+' blank action');
    html=render('components/jobChallan/JcModuleList.tsx',[key(controller,listAction),key(controller,module==='gatepass'?'edit':'viewpdf')],{[query]:{items:[jc],total:1}},[],{module});
    assert(/<a /.test(actionCell(html)),query+' allowed action');
  }
  for(const [pathname,controller,action] of [
    ['/dashboard/contracts/add','contracts','add'],['/dashboard/contracts/edit/1','contracts','edit'],
    ['/dashboard/purchase/indentpo/new','indentpo','add'],['/dashboard/admin/products/edit/1','additem','edit'],
    ['/dashboard/purchase/inspections/print-po/1','purchaseorder','view'],
  ]) {
    currentPath=pathname;
    const Guard=loadTS(path.join(root,'frontend/components/ui/LegacyRouteAccess.tsx')).LegacyRouteAccess;
    grants=[key(controller,'index')];
    assert(renderToStaticMarkup(React.createElement(Guard,null,'secret form')).includes('You do not have permission'));
    grants=[key(controller,action)];
    assert.equal(renderToStaticMarkup(React.createElement(Guard,null,'allowed form')),'allowed form');
  }
  console.log('Frontend: 12 managers passed row visibility/blank-cell checks; direct-form guards passed.');
}
backendTests().then(permissionLoadingTests).then(frontendTests).catch(error=>{console.error(error);process.exitCode=1;});
