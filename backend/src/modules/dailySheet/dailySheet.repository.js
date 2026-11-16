const {QueryTypes} = require('sequelize');
const {id,date,fail} = require('../productionOrder/productionOrder.repository');
const select = (db,sql,replacements={},transaction)=>db.query(sql,{replacements,type:QueryTypes.SELECT,transaction});
const today=(now=new Date())=>new Intl.DateTimeFormat('en-CA',{timeZone:'Asia/Kolkata',year:'numeric',month:'2-digit',day:'2-digit'}).format(now);
const joins = `FROM production p INNER JOIN machine_master m ON m.id=p.machine_id
 LEFT JOIN contracts c ON c.id=p.contract_id LEFT JOIN st_additem a ON a.id=p.item_id
 LEFT JOIN finishedproduct_process f ON f.id=p.productprocess_id`;
const columns = `p.*,m.machine_name,c.title,c.workorder,a.item_name,f.process_name`;
function conditions(query={}) {
  const parts=[],values={};
  for(const [key,column] of Object.entries({po_id:'po_id',contract_id:'contract_id',item_id:'item_id',machines_id:'machine_id',productprocess_id:'productprocess_id'})) {
    if(query[key]) {values[key]=id(String(query[key]).trim());parts.push(`p.${column}=:${key}`);}
  }
  // Match searchitem (the submitted search form), including single-date equality.
  if(query.datefrom) {values.datefrom=date(query.datefrom);parts.push(`DATE(p.production_date)${query.dateto?'>=':'='}:datefrom`);}
  if(query.dateto) {values.dateto=date(query.dateto);parts.push('DATE(p.production_date)<=:dateto');}
  if(values.datefrom && values.dateto && values.datefrom>values.dateto) fail('End Date must not precede Start Date.');
  return {sql:parts.length?'WHERE '+parts.join(' AND '):'',values};
}
function order(query={}) {
  const allowed={id:'p.id',production_date:'p.production_date',po_id:'p.po_id',title:'c.title',machine_name:'m.machine_name',item_name:'a.item_name',process_name:'f.process_name',plan_qty:'CAST(p.plan_qty AS DECIMAL(30,8))'};
  const key=String(query.sort || '').split('.').at(-1);
  return `${Object.hasOwn(allowed,key)?allowed[key]:'p.id'} ${query.direction==='asc'?'ASC':'DESC'},p.id DESC`;
}
async function rows(db,query={},paging,transaction) {
  const where=conditions(query);
  return select(db,`SELECT ${columns} ${joins} ${where.sql} ORDER BY ${order(query)}${paging?' LIMIT :limit OFFSET :offset':''}`,{...where.values,...paging},transaction);
}
async function metadata(db) {
  const [processes,machines,fields]=await Promise.all([
    select(db,'SELECT id,process_name FROM finishedproduct_process ORDER BY id'),
    select(db,'SELECT id,machine_name,status FROM machine_master ORDER BY machine_name'),
    select(db,'SHOW COLUMNS FROM production')
  ]);
  return {processes,machines,has_completed:fields.some(f=>f.Field==='is_completed'),today:today()};
}
async function lookup(db,query={}) {
  const values={fetch:'%'+String(query.fetch || '').slice(0,255)+'%'};
  switch(query.kind) {
    case 'contracts': return select(db,"SELECT id,CONCAT(COALESCE(title,''),'(',COALESCE(workorder,''),')') AS name FROM contracts WHERE status='Y' AND (title LIKE :fetch OR workorder LIKE :fetch) ORDER BY id",values);
    case 'products': return select(db,"SELECT id,item_name AS name FROM st_additem WHERE status='Y' AND itemtype='FinishedProduct' AND item_name LIKE :fetch ORDER BY id",values);
    case 'machines': return select(db,"SELECT id,machine_name AS name FROM machine_master WHERE status='Y' AND machine_name LIKE :fetch ORDER BY machine_name",values);
    default: fail('Invalid lookup');
  }
}
async function dependencies(db,query={}) {
  const products=query.contract_id?await select(db,`SELECT a.id,a.item_name FROM bom_finisedproduct b INNER JOIN st_additem a ON a.id=b.product_id WHERE b.contract_id=:id ORDER BY b.id`,{id:id(query.contract_id)}):[];
  const orders=query.contract_id && query.item_id?await select(db,`SELECT DISTINCT po_id FROM productionorder WHERE contract_id=:contract AND item_id=:item AND status='O' ORDER BY po_id`,{contract:id(query.contract_id),item:id(query.item_id)}):[];
  const processes=query.item_id?await select(db,`SELECT f.id,f.process_name FROM st_additem a INNER JOIN finishedproduct_process f ON FIND_IN_SET(f.id,REPLACE(a.productprocess_id,' ','')) WHERE a.id=:id ORDER BY FIND_IN_SET(f.id,REPLACE(a.productprocess_id,' ',''))`,{id:id(query.item_id)}):[];
  const [reading]=query.machine_id?await select(db,'SELECT nextday8am FROM production WHERE machine_id=:id ORDER BY id DESC LIMIT 1',{id:id(query.machine_id)}):[];
  const progress=query.po_id && query.item_id?await select(db,`SELECT productprocess_id,SUM(COALESCE(production_shift_a,0)+COALESCE(production_shift_b,0)) AS quantity FROM production WHERE po_id=:po AND item_id=:item GROUP BY productprocess_id`,{po:id(query.po_id),item:id(query.item_id)}):[];
  const [orderRow]=query.po_id && query.item_id?await select(db,'SELECT plannedqty FROM productionorder WHERE po_id=:po AND item_id=:item ORDER BY id LIMIT 1',{po:id(query.po_id),item:id(query.item_id)}):[];
  return {products,orders,processes,reading:reading?.nextday8am ?? null,progress,plannedqty:orderRow?.plannedqty ?? null};
}
module.exports={select,id,date,fail,today,joins,columns,conditions,order,rows,metadata,lookup,dependencies};
