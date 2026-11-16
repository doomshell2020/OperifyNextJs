const { QueryTypes } = require('sequelize');
const { id, date, fail } = require('../productionOrder/productionOrder.repository');
const select = async (db, sql, replacements = {}, transaction) => {
  try { return await db.query(sql, { replacements, type: QueryTypes.SELECT, transaction }); }
  catch (error) {
    if ((error.original?.code || error.parent?.code)==='ER_NO_SUCH_TABLE') {
      const unavailable=new Error('Maintenance data is not configured for this company. Select a configured company or contact your administrator.');
      unavailable.status=409; unavailable.code='MAINTENANCE_TABLES_UNAVAILABLE'; throw unavailable;
    }
    throw error;
  }
};
const today = () => new Intl.DateTimeFormat('en-CA', { timeZone:'Asia/Kolkata', year:'numeric', month:'2-digit', day:'2-digit' }).format(new Date());
function timestamp(now=new Date()) {
  const values=Object.fromEntries(new Intl.DateTimeFormat('en-CA',{timeZone:'Asia/Kolkata',year:'numeric',month:'2-digit',day:'2-digit',hour:'2-digit',minute:'2-digit',second:'2-digit',hourCycle:'h23'}).formatToParts(now).map(p=>[p.type,p.value]));
  return `${values.year}-${values.month}-${values.day} ${values.hour}:${values.minute}:${values.second}`;
}
async function updatedValue(db,transaction) {
  const fields=await select(db,"SHOW COLUMNS FROM maintenance LIKE 'updated'",{},transaction);
  return fields.find(f=>f.Field==='updated')?.Type.toLowerCase()==='date'?today():timestamp();
}
const joins = `FROM maintenance m LEFT JOIN machine_master mm ON mm.id=m.machine_id
 LEFT JOIN users a ON a.id=m.assigned_to LEFT JOIN users s ON s.id=m.shift_incharge
 LEFT JOIN users i ON i.id=m.maintenance_incharge LEFT JOIN users p ON p.id=m.production_head`;
const columns = `m.*,mm.machine_name,a.user_name AS assigned_name,s.user_name AS shift_name,i.user_name AS incharge_name,p.user_name AS production_name`;
function conditions(query = {}) {
  const parts = ["m.status='Y'"], values = {};
  if (query.machines_id) { values.machine = id(String(query.machines_id).trim()); parts.push('m.machine_id=:machine'); }
  // PHP searches the stored assigned_to value (usually a user ID), not the joined name.
  if (query.assigned_to) { values.assigned = '%'+String(query.assigned_to).slice(0,255)+'%'; parts.push('m.assigned_to LIKE :assigned'); }
  if (query.m_status) {
    if (!['pending','assigned','completed','complete'].includes(query.m_status)) fail('Invalid maintenance status');
    values.status = query.m_status;
    parts.push('m.maintenance_status=:status');
  }
  for (const [key,op] of [['datefrom','>='],['dateto','<=']]) {
    if (query[key] && query[key] !== '1970-01-01') { values[key] = date(query[key]); parts.push(`DATE(m.datefrom)${op}:${key}`); }
  }
  return { sql:'WHERE '+parts.join(' AND '), values };
}
async function rows(db, query = {}, paging, transaction) {
  const where = conditions(query);
  // Neither PHP table header contains sort links; retain date DESC ordering.
  return select(db, `SELECT ${columns} ${joins} ${where.sql} ORDER BY m.datefrom DESC,m.id DESC${paging?' LIMIT :limit OFFSET :offset':''}`, {...where.values,...paging}, transaction);
}
async function capabilities(db) {
  const fields = await select(db, 'SHOW COLUMNS FROM maintenance');
  const names = new Set(fields.map(f=>f.Field));
  const missingAssigned = ['assigned_date'].filter(f=>!names.has(f));
  const missingCompletion = ['completion_date','completion_time','remarks'].filter(f=>!names.has(f));
  const statusType=fields.find(f=>f.Field==='maintenance_status')?.Type || '';
  if (!statusType || (statusType.startsWith('enum(') && !statusType.includes("'completed'"))) missingCompletion.push("maintenance_status value 'completed'");
  return { can_assign:!missingAssigned.length, can_complete:!missingCompletion.length, missing_assignment:missingAssigned, missing_completion:missingCompletion };
}
async function userActions(db, userId) {
  // CommanHelper::finduserpermisson uses the first per-user row for index.
  const [row] = await select(db, `SELECT \`edit\`,\`delete\` FROM permission_module WHERE user_id=:user AND controller='Maintenance' AND action='index' ORDER BY id ASC LIMIT 1`, {user:id(userId)});
  return { edit:Number(row?.edit)===1, delete:Number(row?.delete)===1 };
}
async function metadata(db, userId) {
  const [users, schema, actions] = await Promise.all([
    select(db, 'SELECT id,user_name,role_id FROM users WHERE role_id IN (104,23,111,112,102) ORDER BY id'), capabilities(db), userActions(db,userId)
  ]);
  const roles = ids => users.filter(u=>ids.includes(Number(u.role_id)));
  return { today:today(), assigned:roles([104,23]), shifts:roles([111]), incharges:roles([104,112]), heads:roles([102]), schema, actions };
}
async function machines(db, fetch) {
  return select(db, "SELECT id,machine_name AS name FROM machine_master WHERE status='Y' AND machine_name LIKE :fetch ORDER BY machine_name ASC", {fetch:'%'+String(fetch || '').slice(0,255)+'%'});
}
module.exports = { select,id,date,fail,today,timestamp,updatedValue,joins,conditions,rows,capabilities,userActions,metadata,machines };
