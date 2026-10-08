const {QueryTypes}=require('sequelize');
const ExcelJS=require('exceljs');
const repo=require('./purchaseOrder.repository');
const select=(db,sql,replacements={})=>db.query(sql,{replacements,type:QueryTypes.SELECT});
const date=value=>{if(!value)return '';const iso=value instanceof Date?value.toISOString().slice(0,10):String(value).slice(0,10);return /^\d{4}-\d{2}-\d{2}$/.test(iso)?iso.split('-').reverse().join('-'):'';};
const number=po=>po.status==='R'?`${po.purchaseorder_id} R-${po.is_revised}`:po.purchaseorder_id;
async function latest(db,filters){
 const {whereString,queryParams}=repo.buildListFilters(filters);
 return select(db,`SELECT po.*,v.name AS vendor_name,v.contact_no FROM st_purchaseorder po LEFT JOIN vendors v ON v.id=po.vendor_id ${whereString} AND po.id=(SELECT MAX(p.id) FROM st_purchaseorder p WHERE p.purchaseorder_id=po.purchaseorder_id) ORDER BY po.id DESC`,queryParams);
}
async function delivery(db,filters){
 const params={},dates=[];
 if(filters.datefrom){dates.push('DATE(n.delivery_date)>=:datefrom');params.datefrom=filters.datefrom;}
 if(filters.dateto){dates.push('DATE(n.delivery_date)<=:dateto');params.dateto=filters.dateto;}
 const notes=await select(db,`SELECT n.delivery_date,po.id,po.purchaseorder_id,po.status,po.is_revised,po.added_time,po.postatus,v.name AS vendor_name,v.contact_no FROM po_delivery_note n JOIN st_purchaseorder po ON po.id=n.poprimary_id LEFT JOIN vendors v ON v.id=po.vendor_id WHERE n.status='Y' AND po.postatus='O' AND po.id=(SELECT MAX(p.id) FROM st_purchaseorder p WHERE p.purchaseorder_id=po.purchaseorder_id) ${dates.length?'AND '+dates.join(' AND '):''} ORDER BY n.delivery_date DESC,n.poprimary_id DESC`,params);
 const seen=new Set(),scheduled=new Set(),rows=[];
 for(const po of notes){const key=po.purchaseorder_id+'|'+date(po.delivery_date);if(seen.has(key))continue;seen.add(key);scheduled.add(po.purchaseorder_id);rows.push(po);}
 // PHP's delivery report filters dates, then suppresses header schedules when
 // that PO already has a matching delivery note. It does not use other filters.
 const fallback=await latest(db,{type:'deli',datefrom:filters.datefrom,dateto:filters.dateto,status:'O'});
 for(const po of fallback){if(!scheduled.has(po.purchaseorder_id))rows.push(po);}
 rows.sort((a,b)=>new Date(b.delivery_date)-new Date(a.delivery_date));
 return rows.map((po,i)=>[i+1,date(po.delivery_date),number(po),po.vendor_name,po.contact_no,date(po.added_time)]);
}
async function summary(db,filters){
 const rows=[];
 for(const po of await latest(db,filters)){
  const items=await select(db,'SELECT pd.*,i.item_name,t.tax FROM st_purchaseorderDetails pd LEFT JOIN st_additem i ON i.id=pd.item_id LEFT JOIN st_taxmaster t ON t.id=pd.tax_id WHERE pd.purchaseorder_id=:po AND pd.poprimary_id=:id',{po:po.purchaseorder_id,id:po.id});
  for(const item of items){
   const receipts=await select(db,"SELECT sr.goods_id,sr.quantity,g.inwarddate FROM st_stock_register sr LEFT JOIN st_goodsreceive g ON g.id=sr.goods_id WHERE sr.po_id=:po AND sr.store_type='1' AND sr.item_id=:item ORDER BY sr.id ASC",{po:po.purchaseorder_id,item:item.item_id});
   const received=receipts.reduce((sum,r)=>sum+Number(r.quantity || 0),0),balance=Number(item.item_qty || 0)-received;
   if(po.postatus==='O' && balance===0)continue;
   rows.push([number(po),date(po.added_time),date(po.delivery_date),po.vendor_name,po.contact_no,receipts.map(r=>r.goods_id).join(','),date(receipts.at(-1)?.inwarddate),item.item_name,Number(item.item_amt || 0),Number(item.item_qty || 0),received,balance,item.tax ?? '']);
  }
 }
 return rows;
}
async function comparison(db,filters){
 const rows=[];
 for(const po of await latest(db,filters)){
  const items=await select(db,'SELECT pd.*,i.item_name FROM st_purchaseorderDetails pd LEFT JOIN st_additem i ON i.id=pd.item_id WHERE pd.purchaseorder_id=:po AND pd.poprimary_id=:id',{po:po.purchaseorder_id,id:po.id});
  for(const item of items){
   const history=await select(db,'SELECT pd.* FROM st_purchaseorderDetails pd WHERE pd.item_id=:item AND DATE(pd.inward_date)<=DATE(:date) ORDER BY pd.inward_date DESC,pd.id DESC',{item:item.item_id,date:po.added_time});
   const unique=[],seen=new Set();for(const entry of history){if(seen.has(entry.purchaseorder_id))continue;seen.add(entry.purchaseorder_id);unique.push(entry);if(unique.length===5)break;}
   let lowest=-1;unique.forEach((entry,i)=>{if(Number(entry.item_amt)!==0 && (lowest<0 || Number(entry.item_amt)<Number(unique[lowest].item_amt)))lowest=i;});
   const best=lowest<0?null:(await select(db,'SELECT po.*,v.name AS vendor_name FROM st_purchaseorder po LEFT JOIN vendors v ON v.id=po.vendor_id WHERE po.purchaseorder_id=:po ORDER BY po.id DESC LIMIT 1',{po:unique[lowest].purchaseorder_id}))[0];
   rows.push({values:[rows.length+1,date(po.added_time),number(po),po.vendor_name,item.item_name,...Array.from({length:5},(_,i)=>unique[i]?Number(unique[i].item_amt):''),best?.vendor_name || '',best?number(best):'',date(best?.added_time)],lowest});
  }
 }
 return rows;
}
async function workbook(db,filters){
 const type=filters.type || 'po';if(!['po','deli','comp'].includes(type))throw Object.assign(new Error('Invalid purchase order report type'),{status:400});
 const headers=type==='deli'?['S.No.','Delivery Date','PO No','Supplier Name','Mobile No.','PO Date']:type==='comp'?['S.No.','PO Date','PO No','PO Vendor Name','Item Name','Price1','Price2','Price3','Price4','Price5','Vendor Name','PO No.','PO Date.']:['PO No','PO Date','Delivery Date','Supplier Name','Mobile No.','GRN No.','GRN Date','Producat Name','Producat Price','PO Qty.','Received Qty.','Balance','Tax'];
 const rows=type==='deli'?await delivery(db,filters):type==='comp'?await comparison(db,filters):await summary(db,filters);
 const book=new ExcelJS.Workbook(),sheet=book.addWorksheet('Sheet1');sheet.columns=headers.map(header=>({header,width:/Name/.test(header)?35:15}));sheet.getRow(1).font={bold:true};sheet.getRow(1).fill={type:'pattern',pattern:'solid',fgColor:{argb:'FF808080'}};sheet.autoFilter={from:{row:1,column:1},to:{row:1,column:headers.length}};
 if(type==='po')sheet.views=[{state:'frozen',ySplit:1}];
 for(const entry of rows){const row=sheet.addRow(Array.isArray(entry)?entry:entry.values);if(type==='comp' && entry.lowest>=0)row.getCell(6+entry.lowest).font={bold:true,color:{argb:'FFFF0000'}};}
 return book;
}
module.exports={workbook,summary,comparison,delivery};
