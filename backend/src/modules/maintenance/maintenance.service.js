const R = require('./maintenance.repository');
function text(value, label, max=255, required=true) {
  if (typeof value !== 'string' && typeof value !== 'number') R.fail(`Invalid ${label}`);
  const s=String(value);
  if ((required && !s.trim()) || s.length>max) R.fail(`Invalid ${label}`);
  return s;
}
const stripEmoji = value => value.replace(/[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}]+/gu,'');
function payload(body) {
  const data={ datefrom:R.date(body.datefrom), breakdown_type:text(body.breakdown_type,'Type Of Breakdown'), total_time:text(body.total_time,'Total Time'), remark:stripEmoji(text(body.remark,'Remark',65535)) };
  // PHP's number input uses the browser default integer step; no duration calculation.
  if (!Number.isFinite(Number(data.total_time)) || !Number.isInteger(Number(data.total_time))) R.fail('Total Time must be a whole number.');
  for (const key of ['assigned_to','shift_incharge','maintenance_incharge','production_head']) data[key]=text(body[key],key);
  return data;
}
async function list(db, query={}) {
  const page = query.page===undefined?1:R.id(query.page), limit=50, offset=(page-1)*limit;
  if (!Number.isSafeInteger(offset)) R.fail('Invalid page');
  const where=R.conditions(query);
  const [{total}] = await R.select(db,`SELECT COUNT(*) AS total FROM maintenance m ${where.sql}`,where.values);
  return {items:await R.rows(db,query,{limit,offset}),total:Number(total),page,limit};
}
async function detail(db, value, transaction, lock=false) {
  const [row]=await R.select(db,`SELECT m.*,mm.machine_name FROM maintenance m LEFT JOIN machine_master mm ON mm.id=m.machine_id WHERE m.id=:id AND m.status='Y'${lock?' FOR UPDATE':''}`,{id:R.id(value)},transaction);
  if (!row) R.fail('Maintenance record not found.',404);
  return row;
}
async function save(db, body, value) {
  const data=payload(body), machine=R.id(String(body.machines_id || '').trim());
  return db.transaction(async transaction=>{
    if (value) await detail(db,value,transaction,true);
    const [exists]=await R.select(db,'SELECT id FROM machine_master WHERE id=:id',{id:machine},transaction);
    if (!exists) R.fail('Your entered machine does not exists.');
    if (!value) {
      const groups={assigned_to:[104,23],shift_incharge:[111],maintenance_incharge:[104,112],production_head:[102]};
      for (const [key,roles] of Object.entries(groups)) {
        const [user]=await R.select(db,'SELECT id FROM users WHERE id=:id AND role_id IN (:roles)',{id:R.id(data[key]),roles},transaction);
        if (!user) R.fail(`Invalid ${key} selection.`);
      }
    }
    const opts={replacements:{...data,machine,id:value?R.id(value):undefined},transaction};
    if (value) {
      // Legacy edit deliberately does not update machine_id; personnel fields are text inputs.
      await db.query(`UPDATE maintenance SET ${Object.keys(data).map(k=>k+'=:'+k).join(',')},updated=:updated WHERE id=:id`,{...opts,replacements:{...opts.replacements,updated:await R.updatedValue(db,transaction)}});
      return {id:R.id(value),message:'Maintenance details successfully updated.'};
    }
    const [insertId]=await db.query(`INSERT INTO maintenance (machine_id,${Object.keys(data).join(',')}) VALUES (:machine,${Object.keys(data).map(k=>':'+k).join(',')})`,opts);
    return {id:insertId,message:'Maintenance details added successfully.'};
  });
}
function transition(current,next) {
  if (!['pending','assigned','completed'].includes(next)) R.fail('Invalid maintenance status.');
  if (current==='completed' && ['pending','assigned'].includes(next)) R.fail('Cannot change status from completed to assigned or pending.',409);
  if (current==='assigned' && next==='pending') R.fail('Cannot change status from assigned to pending.',409);
}
function requireSchema(schema, completing) {
  const missing = completing?schema.missing_completion:schema.missing_assignment;
  if (missing.length) { const error=new Error('This database does not support '+(completing?'maintenance completion':'assignment dates')+'. Missing: '+missing.join(', ')+'. No changes were saved.'); error.status=409;error.code='MAINTENANCE_SCHEMA_MISMATCH';throw error; }
}
async function changeStatus(db,value,body,completing=false) {
  const next=completing?'completed':body.status;
  if (!completing && next==='completed') R.fail('Use the Complete Maintenance form.');
  const schema=await R.capabilities(db);
  return db.transaction(async transaction=>{
    const row=await detail(db,value,transaction,true); transition(row.maintenance_status,next);
    if (next==='assigned' || completing) requireSchema(schema,completing);
    const replacements={id:R.id(value),status:next};
    let extra='';
    if (completing) {
      replacements.completion_date=R.date(body.completion_date);
      replacements.completion_time=text(body.completion_time ?? '', 'Completion Time',255,false);
      replacements.remarks=text(body.remarks,'Remarks',65535);
      extra=',completion_date=:completion_date,completion_time=:completion_time,remarks=:remarks';
    } else if (next==='assigned') {replacements.assigned_date=R.today();extra=',assigned_date=:assigned_date';}
    await db.query('UPDATE maintenance SET maintenance_status=:status'+extra+' WHERE id=:id',{replacements,transaction});
    return {message:completing?'Maintenance marked as completed.':'Maintenance status updated successfully..'};
  });
}
async function remove(db,value) {
  return db.transaction(async transaction=>{
    await detail(db,value,transaction,true);
    await db.query("UPDATE maintenance SET status='N' WHERE id=:id",{replacements:{id:R.id(value)},transaction});
    return {message:'Maintenance details deleted successfully.'};
  });
}
module.exports={list,detail,save,changeStatus,remove,payload,transition,requireSchema,stripEmoji};
