const assert=require('node:assert/strict'),path=require('node:path');require('dotenv').config({path:path.join(__dirname,'../.env'),quiet:true});
const {getTenantSequelize,centralSequelize}=require('../src/config/sequelize');const {QueryTypes}=require('sequelize');
(async()=>{const db=await getTenantSequelize('tirupati_tppl');const query=db.query.bind(db);db.query=(sql,options)=>{assert.match(sql,/^\s*(SELECT|SHOW)\b/i);return query(sql,options)};const select=(sql,replacements={})=>db.query(sql,{replacements,type:QueryTypes.SELECT});
try {
 const poService=require('../src/modules/purchaseOrder/purchaseOrder.service');
 const item=(await select('SELECT item_id FROM st_purchaseorderdetails ORDER BY id DESC LIMIT 1'))[0].item_id;
 for(const filters of [{search:'1'},{item_id:item},{status:'C'},{type:'deli',datefrom:'2020-01-01',dateto:'2100-01-01'},{datefrom:'2100-01-01'}]) {
  const clauses=['EXISTS (SELECT 1 FROM st_purchaseorderdetails pd WHERE pd.purchaseorder_id=po.purchaseorder_id'+(filters.item_id?' AND pd.item_id=:item_id':'')+')'];
  if(filters.status)clauses.push('po.postatus=:status');if(filters.datefrom && filters.dateto)clauses.push('DATE(po.delivery_date) BETWEEN :datefrom AND :dateto');
  const expected=await select('SELECT po.id FROM st_purchaseorder po WHERE '+clauses.join(' AND ')+' ORDER BY po.id DESC',filters);
  const result=await poService.listPurchaseOrders(db,filters,1,50);assert.equal(result.total,expected.length);assert.deepEqual(result.items.map(r=>r.id),expected.slice(0,50).map(r=>r.id));
 }
 const page=await poService.listPurchaseOrders(db,{},1,50);
 for(const row of page.items){const qty=(await select("SELECT COALESCE(SUM(quantity),0) AS qty FROM st_stock_register WHERE po_id=:number AND purchaseorder_id=:id AND store_type='1'",{number:row.po_number,id:row.id}))[0].qty;assert.equal(Number(row.received_qty),Number(qty));}
 const contract=require('../src/modules/contract/contract.repository');
 const expected=await select("SELECT id FROM contracts WHERE DATE(contract_start_date)>='2020-01-01' AND DATE(contract_end_date)<='2100-01-01' ORDER BY id DESC");
 const result=await contract.findFiltered(db,{datefrom:'2020-01-01',dateto:'2100-01-01',limit:50,offset:0});assert.equal(result.count,expected.length);assert.deepEqual(result.rows.map(r=>r.id),expected.slice(0,50).map(r=>r.id));
 const grn=require('../src/modules/grn/grn.repository');
 const sample=(await select('SELECT purchaseorder_id, vendor_id FROM st_goodsreceive ORDER BY id DESC LIMIT 1'))[0];
 const exported=await grn.exportGrns(db,{po_id:sample.purchaseorder_id,vendor_id:sample.vendor_id,from_date:'1930-01-01',to_date:'2100-01-01'});assert.ok(exported.length);assert.ok(exported.every(r=>r.po_no===sample.purchaseorder_id));
 console.log('PASS: PO detail/item/status/delivery-date/partial-date rules, receipt quantities, Contract DATE bounds, GRN export applied filters. No database writes.');
}finally{await db.close();await centralSequelize.close();}})().catch(e=>{console.error(e.message);process.exitCode=1});
