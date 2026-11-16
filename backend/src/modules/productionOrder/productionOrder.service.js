const R = require('./productionOrder.repository');
const { paginationResult } = require('../../utils/listPagination');

function paging(query = {}) {
  const integer = (value, fallback) => /^\d+$/.test(String(value ?? '')) && Number.isSafeInteger(Number(value)) ? Number(value) : fallback;
  const page = Math.max(1,integer(query.page,1)), limit = Math.max(1,Math.min(100,integer(query.limit,50))), offset = (page-1)*limit;
  if (!Number.isSafeInteger(offset)) R.fail('Invalid page');
  return {page,limit,offset};
}
async function list(db, query) {
  const {page,limit,offset} = paging(query), where = R.conditions(query);
  const [{total}] = await R.select(db, `SELECT COUNT(*) AS total FROM productionorder o ${where.sql}`,where.values);
  const items = await R.rows(db,query,{limit,offset});
  return {items,...paginationResult(total,page,limit,items.length)};
}
async function contracts(db, query) {
  const search = String(query.fetch || '').slice(0,255);
  if (!search) return [];
  return R.select(db,"SELECT id,title,workorder FROM contracts WHERE status='Y' AND (title LIKE :search OR workorder LIKE :search) ORDER BY id",{search:`%${search}%`});
}
async function nextNumber(db, transaction) {
  // po_id is a legacy VARCHAR: retain Cake's descending ordering before incrementing.
  const [last] = await R.select(db,'SELECT po_id FROM productionorder ORDER BY po_id DESC LIMIT 1',{},transaction);
  const value = last?.po_id ? Number(last.po_id)+1 : 1001;
  if (!Number.isSafeInteger(value)) R.fail('The last production order number is invalid.',409);
  return String(value);
}
async function form(db, contractId) {
  const po_id = await nextNumber(db);
  return {po_id,products:contractId ? await R.products(db,contractId) : [],orders:contractId ? await R.rows(db,{contract_id:contractId,sort:'id'}) : []};
}
async function create(db, payload = {}) {
  const contract_id = R.id(payload.contract_id), issuedate = R.date(payload.issuedate,'issue date');
  if (!Array.isArray(payload.items) || payload.items.some(row=>!row || typeof row !== 'object' || Array.isArray(row))) R.fail('Invalid production order items.');
  const lines = payload.items.filter(row=>row.plannedqty !== '' && row.plannedqty != null);
  if (!lines.length) R.fail('Please enter a planned quantity for at least one product.');
  return db.transaction(async transaction => {
    // Serialize creation per company without schema changes or trusting a stale form number.
    await R.select(db,'SELECT id FROM sitesettings ORDER BY id LIMIT 1 FOR UPDATE',{},transaction);
    const [contract] = await R.select(db,"SELECT id FROM contracts WHERE id=:id AND status='Y' FOR UPDATE",{id:contract_id},transaction);
    if (!contract) R.fail('Your entered Contract does not exist.');
    const po_id = await nextNumber(db,transaction);
    if (String(payload.po_id) !== po_id) R.fail('Production order number has changed. Refresh the form and try again.',409);
    const products = await R.products(db,contract_id,transaction), ids = [];
    for (const line of lines) {
      const item_id = R.id(line.item_id), product = products.find(p=>Number(p.product_id)===item_id && (!line.bom_id || Number(p.id)===R.id(line.bom_id)));
      if (!product) R.fail('Product does not belong to this contract.');
      if (!/^\d+(\.\d{1,2})?$/.test(String(line.plannedqty)) || !Number.isFinite(Number(line.plannedqty))) R.fail('Planned quantity must be a non-negative number with at most two decimal places.');
      const plannedqty = Number(line.plannedqty);
      if (plannedqty > Number(product.pendingqty)) R.fail(`Quantity cannot be greater than ${product.pendingqty}`);
      const startdate = R.date(line.startdate,'start date'), enddate = R.date(line.enddate,'end date');
      const totaldays = (Date.parse(enddate)-Date.parse(startdate))/86400000+1;
      if (totaldays <= 0) R.fail('Total days cannot be negative.');
      const [insertId] = await db.query(`INSERT INTO productionorder (po_id,issuedate,contract_id,item_id,plannedqty,uom,startdate,enddate,totaldays) VALUES (:po_id,:issuedate,:contract_id,:item_id,:plannedqty,:uom,:startdate,:enddate,:totaldays)`,{replacements:{po_id,issuedate,contract_id,item_id,plannedqty:String(line.plannedqty),uom:product.unit_name || '',startdate,enddate,totaldays:String(totaldays)},transaction});
      ids.push(insertId);
      // The pending quantity is calculated over all orders, including completed orders.
      for (const p of products) if (Number(p.product_id)===item_id) p.pendingqty=Number(p.pendingqty)-plannedqty;
    }
    return {po_id,ids};
  });
}
async function changeStatus(db, rawId, status) {
  const id = R.id(rawId);
  if (!['O','C'].includes(status)) R.fail('Invalid status');
  return db.transaction(async transaction => {
    const [order] = await R.select(db,'SELECT * FROM productionorder WHERE id=:id FOR UPDATE',{id},transaction);
    if (!order) R.fail('Production order not found',404);
    if (status === 'C') {
      const [item] = await R.select(db,'SELECT productprocess_id FROM st_additem WHERE id=:id',{id:order.item_id},transaction);
      if (!item?.productprocess_id || item.productprocess_id === '0') R.fail('No process defined for this item.');
      const required = String(item.productprocess_id).split(',').map(value=>parseInt(value.trim(),10) || 0);
      const completed = await R.select(db,'SELECT DISTINCT productprocess_id FROM production WHERE po_id=:po_id AND item_id=:item_id AND productprocess_id IN (:required)',{po_id:order.po_id,item_id:order.item_id,required},transaction);
      const done = new Set(completed.map(p=>Number(p.productprocess_id))), missing = required.filter(p=>!done.has(p));
      if (missing.length) {
        const names = await R.select(db,'SELECT id,process_name FROM finishedproduct_process ORDER BY id',{},transaction);
        R.fail('Cannot close. Pending processes: '+missing.map(p=>names.find(n=>Number(n.id)===p)?.process_name || `Unknown(${p})`).join(', '),409);
      }
    }
    await db.query(`UPDATE productionorder SET status=:status,complete_date=${status === 'C' ? 'CURRENT_DATE()' : 'NULL'} WHERE id=:id`,{replacements:{id,status},transaction});
    return {message:`Production Order ${status === 'C' ? 'closed' : 'opened'} successfully.`};
  });
}
async function remove(db, rawId) {
  const id = R.id(rawId);
  return db.transaction(async transaction => {
    const [order] = await R.select(db,'SELECT * FROM productionorder WHERE id=:id FOR UPDATE',{id},transaction);
    if (!order) R.fail('Production order not found',404);
    const started = await R.select(db,'SELECT id FROM production WHERE po_id=:po_id LIMIT 1 FOR UPDATE',{po_id:order.po_id},transaction);
    if (order.status === 'C' || started.length) R.fail('Only open production orders with no production entries can be deleted.',409);
    await db.query('DELETE FROM productionorder WHERE id=:id',{replacements:{id},transaction});
    return {message:'Production Order deleted successfully'};
  });
}
async function detail(db, poId) {
  const po_id = String(R.id(poId));
  // Legacy modal selects the last row for this PO, even for a multi-product PO.
  const [order] = await R.select(db,`SELECT o.*,c.title,c.workorder,a.item_name FROM productionorder o LEFT JOIN contracts c ON c.id=o.contract_id LEFT JOIN st_additem a ON a.id=o.item_id WHERE o.po_id=:po_id ORDER BY o.id DESC LIMIT 1`,{po_id});
  if (!order) R.fail('Production order not found',404);
  const entries = await R.select(db,'SELECT * FROM production WHERE po_id=:po_id ORDER BY id ASC',{po_id});
  const masters = await R.select(db,'SELECT id,process_name FROM finishedproduct_process ORDER BY id');
  const processes = masters.flatMap(master=>{
    const rows=entries.filter(row=>Number(row.productprocess_id)===Number(master.id));
    return rows.length ? [{...master,startdate:rows[0].production_date,enddate:rows.at(-1).production_date,prepared_qty:rows.reduce((sum,row)=>sum+Number(row.production_shift_a || 0)+Number(row.production_shift_b || 0),0)}] : [];
  });
  const prepared_qty = entries.filter(row=>Number(row.productprocess_id)===8).reduce((sum,row)=>sum+Number(row.production_shift_a || 0)+Number(row.production_shift_b || 0),0);
  const [design] = await R.select(db,'SELECT * FROM designsheet WHERE contract_id=:contract_id AND item_id=:item_id LIMIT 1',order);
  const materials = design ? await R.select(db,`SELECT d.id,d.item_id,a.item_name,
    (SELECT SUM(item_qty) FROM designsheetdetails WHERE designsheetno=d.designsheetno AND item_id=d.item_id) / NULLIF(:quantity,0) * :plannedqty AS required_qty,
    COALESCE((SELECT ROUND(SUM(quantity),2) FROM st_stock_register WHERE item_id=d.item_id AND created<=(SELECT issuedate FROM productionorder WHERE id=:order_id) AND store_type IN ('0','1','3')),0)-COALESCE((SELECT ROUND(SUM(quantity),2) FROM st_stock_register WHERE item_id=d.item_id AND created<=(SELECT issuedate FROM productionorder WHERE id=:order_id) AND store_type IN ('2','4')),0) AS available_qty
    FROM designsheetdetails d LEFT JOIN st_additem a ON a.id=d.item_id WHERE d.designsheetno=:designsheetno ORDER BY d.id`,{...order,order_id:order.id,quantity:design.quantity,designsheetno:design.designsheetno}) : [];
  return {order,quantity:design?.quantity || 0,prepared_qty,pending_qty:Number(order.plannedqty || 0)-prepared_qty,processes,materials};
}
module.exports = {paging,list,contracts,form,create,changeStatus,remove,detail};
