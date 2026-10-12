// Exercise the actual browser PDF services without database writes.
const assert=require('node:assert/strict');
const fs=require('node:fs');
const path=require('node:path');
const vm=require('node:vm');
const ts=require('../frontend/node_modules/typescript');
function service(file){
 const calls=[],closed=[],revoked=[],downloads=[];
 let failure=false,blocked=false,mime='application/pdf';
 const viewer={closed:false,document:{body:{}},location:{replace:url=>calls.push(['preview',url])},close(){closed.push(true);}};
 const module={exports:{}};
 vm.runInNewContext(ts.transpileModule(fs.readFileSync(path.join(__dirname,'../frontend/services',file),'utf8'),{compilerOptions:{module:ts.ModuleKind.CommonJS,esModuleInterop:true}}).outputText,{
  module,exports:module.exports,console,
  require(){return {get:async(url,options)=>{calls.push([url,options]);if(failure)throw new Error('HTTP 403');return {data:{type:mime}};}};},
  URL:{createObjectURL:()=> 'blob:test',revokeObjectURL:url=>revoked.push(url)},
  window:{open:()=>{calls.push(['open']);return blocked?null:viewer;},URL:{createObjectURL:()=> 'blob:test',revokeObjectURL:url=>revoked.push(url)},setInterval:()=>1,clearInterval(){},setTimeout(){calls.push(['deferred cleanup']);}},
  document:{body:{appendChild(){}},createElement:()=>({setAttribute(name,value){this[name]=value;},click(){downloads.push(this.download);},parentNode:{removeChild(){}}})},
 });
 return {api:module.exports,calls,closed,downloads,revoked,setFailure:value=>failure=value,setBlocked:value=>blocked=value,setMime:value=>mime=value};
}
(async()=>{
 const grn=service('grnPdf.service.ts');
 await grn.api.grnPdfService.openGrnPdf(17);
 assert.equal(grn.calls[0][0],'open','Open synchronously before fetching to avoid popup blockers');
 assert(grn.calls.some(([url])=>url==='/grn/17/pdf'));
 assert(grn.calls.some(([url])=>url==='preview'));
 await grn.api.grnPdfService.downloadGrnPdf(17);
 assert.deepEqual(grn.downloads,['GRN_Details_17.pdf']);
 assert.equal(grn.revoked.length,0,'Keep the download URL alive until the browser has consumed it');
 grn.setMime('application/json');await assert.rejects(grn.api.grnPdfService.openGrnPdf(17),/did not return/);assert.equal(grn.closed.length,1);
 grn.setFailure(true);await assert.rejects(grn.api.grnPdfService.openGrnPdf(17),/403/);assert.equal(grn.closed.length,2);
 grn.setBlocked(true);await assert.rejects(grn.api.grnPdfService.openGrnPdf(17),/Allow popups/);
 const po=service('purchaseOrderPdf.service.ts');await po.api.openPurchaseOrderPdf(42,'details');
 assert(po.calls.some(([url])=>url==='/purchase-orders/42/details/pdf'));
 await po.api.openPurchaseOrderPdf(42,'current');assert(po.calls.some(([url,options])=>url==='/purchase-orders/42/pdf' && options.params.mode==='current'));
 console.log('GRN print/download, PDF type validation, rejected requests, blocked popups, URL lifetime and dedicated PO details PDF routing passed.');
})().catch(error=>{console.error(error);process.exitCode=1;});
