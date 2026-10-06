const test = require('node:test');
const assert = require('node:assert/strict');
const repository = require('../src/modules/contract/contract.repository');
const {date,logoSrc} = require('../src/utils/legacyPdf');
const {words,buildGrnHtml} = require('../src/modules/grn/grn.pdf');
const {buildSubcontractorChallanHtml} = require('../src/modules/jobChallan/jobChallan.subcontractor.pdf');

test('sister-company challans preserve saved totals and split tax by GST state', () => {
  const data={site_details:{gst_no:'08AACCK1942L1ZE'},sitesetting:{first_name:'Issuer'},challan:{vendor:{name:'Receiver',gst_no:'08AAAAA0000A1Z0'},job_challan_items:[{quantity:2,rate:100,tax_amount:36,tax_rate:18,amount:200,unit_name:'Mtr',item_name:'First item'},{amount:999,item_name:'Second item'}]}};
  const local=buildSubcontractorChallanHtml(data);
  assert.equal((local.match(/Rs\. 18\.00/g) || []).length,2);
  assert.match(local,/Rs\. -/);
  assert.match(local,/<b>200\.00<\/b>/); // PHP uses saved amount even when it excludes GST.
  assert.doesNotMatch(local,/Second item/);
  data.challan.vendor.gst_no='27AAAAA0000A1Z0';
  data.challan.job_challan_items[0].amount=0;
  const interstate=buildSubcontractorChallanHtml(data);
  assert.match(interstate,/Rs\. 36\.00/);
  assert.match(interstate,/<b>236\.00<\/b>/);
});

test('contract processes use both shifts, ID-ordered endpoints, and unique PO numbers', async () => {
  const rows = [
    {productprocess_id:'8',process_name:'Outer Sheathing',production_date:'2026-10-03',po_id:14,production_shift_a:2,production_shift_b:3,manpower_day:'4',manpower_night:'2',nextday8am:'140',reading8am:'100'},
    {productprocess_id:'8',process_name:'Outer Sheathing',production_date:'2026-10-01',po_id:14,production_shift_a:4,production_shift_b:null,manpower_day:'1',manpower_night:null,nextday8am:'160',reading8am:'140'},
    {productprocess_id:'8',process_name:'Outer Sheathing',production_date:'2026-10-02',po_id:15,production_shift_a:1,production_shift_b:2,manpower_day:'2',manpower_night:'1',nextday8am:'180',reading8am:'160'},
  ];
  const result=await repository.getPdfProduction({query:async()=>rows},42,9);
  assert.equal(result.has_production,true);
  assert.equal(result.labour,10);
  assert.equal(result.operation,80);
  assert.deepEqual(result.processes,[{process_id:8,process_name:'Outer Sheathing',start_date:'2026-10-03',end_date:'2026-10-02',po_numbers:'14,15',quantity:12}]);
});

test('contract category totals retain returns and negative pending quantities', async () => {
  let calls=0;
  const pool={models:{designsheet:{findOne:async()=>({designsheetno:100})}},query:async()=>{
    calls++;
    if(calls===1)return [{item_id:1,is_group:1,category_id:4,category_name:'Copper',item_name:'Representative item',as_per_design:10},{item_id:3,is_group:0,item_name:'Tape',as_per_design:5}];
    if(calls===2)return [{item_id:1,item_name:'Rod',issued_qty:12},{item_id:2,item_name:'Wire',issued_qty:-1},{item_id:4,item_name:'Returned in full',issued_qty:0}];
    return [{item_id:3,item_name:'Tape',issued_qty:2}];
  }};
  const rows=await repository.findDesignSheetDetails(pool,42,9);
  assert.equal(rows[0].item_name,'Copper');
  assert.equal(rows[0].total_issued,11);
  assert.equal(rows[0].pending_qty,-1);
  assert.deepEqual(rows[0].issued_items.map(i=>i.issued_qty),[12,-1]);
  assert.deepEqual(rows[1].issued_items,[]);
});

test('PDF dates preserve stored MySQL calendar dates independently of host timezone',()=>{
  assert.equal(date('2026-09-14 23:59:00'),'14-09-2026');
  assert.equal(date(new Date('2026-09-14T23:59:00Z'),true),'14-Sep-2026');
  assert.equal(date(null),'');
  assert.equal(logoSrc({small_logo:'missing-tenant-logo.png'}),'');
});

test('GRN preserves saved amounts, zero tax, missing order quantity, and last-row tax status',()=>{
  const html=buildGrnHtml({grn:{id:1,freight:5,remark:''},items:[
    {item_name:'A & B',quantity:10,rate:20,cost_price:200,amount:200,tax:0,tax_rates:['0'],order_qty:25,uom:'Nos'},
    {item_name:'C',quantity:3,rate:10,cost_price:30,amount:33,tax:3,tax_rates:['10'],order_qty:null,uom:'M'},
  ]});
  assert.match(html,/238\.00/);
  assert.match(html,/Tax Excluded/);
  assert.match(html,/25 Nos/);
  assert.match(html,/A &amp; B/);
  assert.doesNotMatch(html,/18%/);
  assert.match(html,/>0<\/td>/);
});

test('GRN amount words match the PHP Indian numbering without an extra hundred conjunction',()=>{
  assert.equal(words(5125920),'Fifty One Lakh Twenty Five Thousand Nine Hundred Twenty Rupees Only');
  assert.equal(words(120.05),'One Hundred Twenty Rupees and Five Paisa Only');
});
