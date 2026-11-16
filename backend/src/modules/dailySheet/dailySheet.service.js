const R=require('./dailySheet.repository');
const numericFields=['plan_qty','production_shift_a','production_shift_b','reading8am','reading8pm','nextday8am','manpower_day','manpower_night'];
const sum=row=>Number(row.production_shift_a || 0)+Number(row.production_shift_b || 0);
const present=value=>value!=='' && value!==null && value!==undefined;
const decorate=row=>({...row,total_production:sum(row),total_manpower:Number(row.manpower_day || 0)+Number(row.manpower_night || 0)});
async function list(db,query={}) {
  const page=query.page?Number(query.page):1,limit=50,where=R.conditions(query);
  if(!Number.isSafeInteger(page) || page<1 || !Number.isSafeInteger((page-1)*limit)) R.fail('Invalid page.');
  return db.transaction({readOnly:true,isolationLevel:'REPEATABLE READ'},async t=>{
    const [count]=await R.select(db,`SELECT COUNT(*) AS total ${R.joins} ${where.sql}`,where.values,t);
    return {items:(await R.rows(db,query,{limit,offset:(page-1)*limit},t)).map(decorate),total:Number(count.total),page,limit};
  });
}
async function detail(db,id,transaction) {
  const [row]=await R.select(db,`SELECT ${R.columns} ${R.joins} WHERE p.id=:id`,{id:R.id(id)},transaction);
  if(!row) R.fail('Daily Sheet not found.',404);
  return decorate(row);
}
async function datewise(db,value) {
  const date=R.date(value);
  // Template displays the first entry per machine; submit updates the latest one.
  const rows=await R.select(db,`SELECT ${R.columns},
    (SELECT r.reading8am FROM production r WHERE r.machine_id=p.machine_id AND r.nextday8am IS NOT NULL ORDER BY r.id DESC LIMIT 1) AS carried_reading,
    (SELECT r.id FROM production r WHERE r.machine_id=p.machine_id AND r.nextday8am IS NOT NULL ORDER BY r.id DESC LIMIT 1) AS reading_source_id
    ${R.joins} WHERE p.production_date=:date AND p.id=(SELECT MIN(p2.id) FROM production p2 WHERE p2.machine_id=p.machine_id AND p2.production_date=:date) ORDER BY m.machine_name`,{date});
  return {date,items:rows.map(row=>({...row,reading8am:row.reading_source_id?row.carried_reading:row.reading8am,reading_locked:!!row.reading_source_id}))};
}
function normalize(body,production_date) {
  const data={production_date,contract_id:R.id(body.contract_id),po_id:R.id(body.po_id),item_id:R.id(body.item_id),machine_id:R.id(body.machine_id),productprocess_id:present(body.productprocess_id)?R.id(body.productprocess_id):null};
  for(const key of numericFields) {
    const value=body[key];
    if(present(value) && (!/^\d*(\.\d+)?$/.test(String(value)) || !Number.isFinite(Number(value)) || Number(value)<0)) R.fail('Invalid '+key);
    data[key]=present(value)?String(value):null;
  }
  for(const key of ['plan_qty','reading8am','reading8pm','nextday8am','manpower_day','manpower_night']) if(data[key]?.length> (key==='plan_qty'?45:255)) R.fail(key+' is too long.');
  for(const [a,b,label] of [['reading8am','reading8pm',"Reading at 8.00 PM can't be less than reading at 8.00 AM"],['reading8am','nextday8am',"Reading Next Day can't be less than reading at 8.00 AM"],['reading8pm','nextday8am',"Reading Next Day can't be less than reading at 8.00 PM"]]) {
    if(present(data[a]) && present(data[b]) && Number(data[a])>Number(data[b])) R.fail(label);
  }
  data.scrap=String(body.scrap || '');if(data.scrap.length>255) R.fail('Scrap is too long.');
  data.remark=String(body.remark || '');if(data.remark.length>60000) R.fail('Remark is too long.');
  return data;
}
async function relationships(db,data,t,{single=false}={}) {
  const [order]=await R.select(db,'SELECT * FROM productionorder WHERE po_id=:po_id AND item_id=:item_id AND contract_id=:contract_id ORDER BY id LIMIT 1',{...data},t);
  if(!order) R.fail('Your entered PO ID, contract and product do not match an existing Production Order.');
  const [machine]=await R.select(db,'SELECT id FROM machine_master WHERE id=:id',{id:data.machine_id},t);
  if(!machine) R.fail('Your entered Machine does not exist.');
  const [item]=await R.select(db,'SELECT productprocess_id FROM st_additem WHERE id=:id',{id:data.item_id},t);
  if(!item) R.fail('Product does not exist.');
  if(data.productprocess_id) {
    const [process]=await R.select(db,'SELECT id FROM finishedproduct_process WHERE id=:id',{id:data.productprocess_id},t);
    if(!process) R.fail('Process does not exist.');
  }
  if(single && !String(item.productprocess_id || '').trim()) R.fail('No process defined for this item.');
  return {order,item};
}
async function write(db,data,id,t) {
  const keys=Object.keys(data);
  if(id) await db.query(`UPDATE production SET ${keys.map(k=>'`'+k+'`=:'+k).join(',')} WHERE id=:id`,{replacements:{...data,id},transaction:t});
  else await db.query(`INSERT INTO production (${keys.map(k=>'`'+k+'`').join(',')}) VALUES (${keys.map(k=>':'+k).join(',')})`,{replacements:data,transaction:t});
}
async function save(db,body,id) {
  const meta=await R.metadata(db);
  const date=R.date(body.production_date),data=normalize(body,date);
  for(const key of ['reading8am','reading8pm','nextday8am','production_shift_a','production_shift_b','manpower_day','manpower_night']) if(!present(data[key])) R.fail(key+' is required.');
  return db.transaction(async t=>{
    const previous=id?await detail(db,id,t):null;
    const {order,item}=await relationships(db,data,t,{single:!id});
    if(!id) {
      const required=String(item.productprocess_id).split(',').map(v=>v.trim()).filter(Boolean);
      const completed=await R.select(db,'SELECT DISTINCT productprocess_id FROM production WHERE po_id=:po AND item_id=:item',{po:data.po_id,item:data.item_id},t);
      const missing=required.filter(v=>!completed.some(p=>String(p.productprocess_id)===v));
      if(missing.length) {
        const names=await R.select(db,'SELECT process_name FROM finishedproduct_process WHERE id IN (:ids)',{ids:missing},t);
        R.fail('Processes not completed: '+names.map(v=>v.process_name).join(', '));
      }
    } else {
      const duplicate=await R.select(db,'SELECT id FROM production WHERE machine_id=:machine AND production_date=:today AND id<>:id LIMIT 1',{machine:data.machine_id,today:meta.today,id:R.id(id)},t);
      if(duplicate.length) R.fail('Entry for this machine on '+meta.today+' already exists.');
    }
    // Cake edit totals use the original PO/item/process, excluding the edited row.
    const group=previous || data;
    const totals=await R.select(db,`SELECT production_shift_a,production_shift_b FROM production WHERE po_id=:po AND item_id=:item AND productprocess_id <=> :process${id?' AND id<>:id':''}`,{po:group.po_id,item:group.item_id,process:group.productprocess_id,...(id?{id:R.id(id)}:{})},t);
    const planned=previous?(await R.select(db,'SELECT plannedqty FROM productionorder WHERE po_id=:po AND item_id=:item ORDER BY id LIMIT 1',{po:previous.po_id,item:previous.item_id},t))[0]?.plannedqty:order.plannedqty;
    data.status=totals.reduce((v,p)=>v+sum(p),0)+sum(data)>=Number(planned || 0)?'C':'O';
    if(id) data.updated=new Date();
    data.remark=data.remark.replace(/[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}]+/gu,'');
    if(!id && meta.has_completed) {if(!['Y','N'].includes(body.completed)) R.fail('Invalid Completed value.');data.is_completed=body.completed;}
    await write(db,data,id?R.id(id):null,t);
    return {message:id?'Data Update has been updated Successfully.':'Daily Sheet Successfully Added.'};
  });
}
async function saveDatewise(db,body,value) {
  const meta=await R.metadata(db),date=value?R.date(value):meta.today;
  if(!Array.isArray(body.items) || body.items.length>500) R.fail('Invalid date-wise entries (maximum 500).');
  const items=body.items.filter(row=>present(row.plan_qty));
  if(!items.length) R.fail('Enter at least one Planned Quantity.');
  return db.transaction(async t=>{
    for(const row of items) {
      const data=normalize(value?row:{...row,production_shift_a:null,production_shift_b:null,reading8am:null,reading8pm:null,nextday8am:null,manpower_day:null,manpower_night:null,scrap:'',remark:''},date);
      await relationships(db,data,t);
      let id=null;
      if(value) {
        const [old]=await R.select(db,'SELECT id FROM production WHERE production_date=:date AND machine_id=:machine ORDER BY id DESC LIMIT 1',{date,machine:data.machine_id},t);
        if(!old) R.fail('No Daily Sheet entry for this machine on '+date,404);
        id=old.id;
      }
      if(meta.has_completed) data.is_completed=null;
      // Date-wise paths intentionally do not recalculate status in CakePHP.
      await write(db,data,id,t);
    }
    return {message:'Daily Sheet Successfully Added.'};
  });
}
async function remove(db,value) {
  const id=R.id(value);
  return db.transaction(async t=>{
    await detail(db,id,t);
    await db.query('DELETE FROM productiondetails WHERE production_id=:id',{replacements:{id},transaction:t});
    await db.query('DELETE FROM production WHERE id=:id',{replacements:{id},transaction:t});
    return {message:'Daily Sheet deleted successfully'};
  });
}
module.exports={list,detail,datewise,save,saveDatewise,remove,normalize,decorate};
