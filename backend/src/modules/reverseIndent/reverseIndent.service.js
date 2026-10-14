const { QueryTypes } = require('sequelize');
const v = require('../../utils/receiptValidation');
const { isLegacyToday } = require('../../middleware/legacyActionDate');
const headerSql = `SELECT r.*, c.title AS contract_name, c.workorder,
  p.item_name AS product_name, p.itemtype, u.unit_name AS product_uom, m.machine_name,
  NOT EXISTS(SELECT 1 FROM st_stock_register posted WHERE posted.reverse_id=r.reverse_id AND posted.store_type!='99') AS draft_stock_clean
  FROM reverseindent r LEFT JOIN contracts c ON c.id=r.contract_id
  LEFT JOIN st_additem p ON p.id=r.finishedproduct_id
  LEFT JOIN st_measurementunits u ON u.id=p.uom
  LEFT JOIN machine_master m ON m.id=r.machine_id`;
const id = (value, field) => {
  if (!/^[1-9]\d*$/.test(String(value))) throw v.invalid(`${field} must be a valid ID.`);
  return String(value);
};
function quantity(value, field, positive = false) {
  if (!['string','number'].includes(typeof value) || (typeof value==='string' && !value.trim())) throw v.invalid(`${field} must be a valid number.`);
  const n = v.number(value, field, positive);
  if (Math.abs(n * 100 - Math.round(n * 100)) > 0.00001) throw v.invalid(`${field} allows at most two decimal places.`);
  return n;
}
function where(filters = {}) {
  const conditions = [], replacements = {};
  for (const [param, column] of [['contract_id','contract_id'], ['item_id','finishedproduct_id'], ['machine_id','machine_id']]) {
    const value = filters[param] || (param === 'machine_id' ? filters.machines_id : null);
    if (value) { replacements[param] = id(value, param); conditions.push(`r.${column}=:${param}`); }
  }
  for (const [param, operator] of [['datefrom','>='], ['dateto','<=']]) {
    if (filters[param]) {
      replacements[param] = v.date(filters[param], param);
      // PHP searchitem uses equality for a lone start date and ignores a
      // lone end date; index uses independent inclusive range boundaries.
      if(filters.date_mode==='search' && param==='dateto' && !filters.datefrom) continue;
      const op=filters.date_mode==='search' && param==='datefrom' && !filters.dateto ? '=' : operator;
      conditions.push(`DATE(r.issue_date) ${op} :${param}`);
    }
  }
  if (replacements.datefrom && replacements.dateto && replacements.datefrom > replacements.dateto) throw v.invalid('Start Date must be before End Date.');
  return { sql: conditions.length ? ` WHERE ${conditions.join(' AND ')}` : '', replacements };
}
class ReverseIndentService {
  async getNextReverseId(db, transaction) {
    const [row] = await v.select(db, 'SELECT reverse_id FROM reverseindent ORDER BY id DESC LIMIT 1',{},transaction);
    return row?.reverse_id ? Number(String(row.reverse_id).replace(/\D/g, '') || 1000) + 1 : 1001;
  }
  async getReverseIndents(db, filters = {}) {
    const f = where(filters);
    return v.select(db, `${headerSql}${f.sql} ORDER BY r.id DESC`, f.replacements);
  }
  async getPage(db, filters = {}) {
    const f = where(filters);
    const page=Number(filters.page || 1),requestedLimit=Number(filters.limit || 50);
    if(!Number.isSafeInteger(page) || page<1 || !Number.isSafeInteger(requestedLimit) || requestedLimit<1) throw v.invalid('Page and limit must be positive integers.');
    const limit=Math.min(100,requestedLimit);
    const [count] = await v.select(db, `SELECT COUNT(*) AS total FROM reverseindent r${f.sql}`, f.replacements);
    const data = await v.select(db, `${headerSql}${f.sql} ORDER BY r.id DESC LIMIT :limit OFFSET :offset`, {...f.replacements, limit, offset:(page-1)*limit});
    return { data, total:Number(count.total), page, limit };
  }
  async getExportRows(db, filters = {}) {
    const f=where(filters);
    return v.select(db,`SELECT r.issue_date,r.reverse_id,c.title AS contract_name,c.workorder,
      p.item_name AS product_name,m.machine_name,s.quantity,raw.item_name AS item_name,r.received_name
      FROM reverseindent r JOIN st_stock_register s ON s.reverse_id=r.reverse_id
      LEFT JOIN contracts c ON c.id=r.contract_id LEFT JOIN st_additem p ON p.id=r.finishedproduct_id
      LEFT JOIN machine_master m ON m.id=r.machine_id LEFT JOIN st_additem raw ON raw.id=s.item_id
      ${f.sql} ORDER BY r.issue_date DESC,r.id DESC,s.id`,f.replacements);
  }
  async searchContracts(db, q = '') {
    return v.select(db, `SELECT id,title,workorder FROM contracts WHERE status='Y'
      AND (title LIKE :q OR workorder LIKE :q) ORDER BY title`, {q:`%${q}%`});
  }
  async searchMachines(db, q = '') {
    return v.select(db, `SELECT id,machine_name FROM machine_master WHERE status='Y' AND machine_name LIKE :q ORDER BY machine_name`, {q:`%${q}%`});
  }
  async searchProducts(db, q = '') {
    return v.select(db, `SELECT id,item_name FROM st_additem WHERE itemtype IN ('FinishedProduct','Semi-Finished Product')
      AND item_name LIKE :q ORDER BY item_name`, {q:`%${q}%`});
  }
  async getContractProducts(db, contractId) {
    return v.select(db, `SELECT DISTINCT a.id,a.item_name FROM designsheet d JOIN st_additem a ON a.id=d.item_id
      WHERE d.contract_id=:contractId AND a.itemtype='FinishedProduct' ORDER BY a.item_name`, {contractId:id(contractId,'Contract')});
  }
  async getSemiProducts(db, transaction) {
    return v.select(db, `SELECT a.id,a.item_name,u.unit_name AS uom FROM st_additem a LEFT JOIN st_measurementunits u ON u.id=a.uom
      WHERE a.itemtype='Semi-Finished Product' AND a.status='Y' ORDER BY a.item_name`,{},transaction);
  }
  async getDesignSheetDetails(db, contractId, productId, transaction) {
    const [sheet] = await v.select(db, 'SELECT * FROM designsheet WHERE contract_id=:contractId AND item_id=:productId ORDER BY id LIMIT 1',
      {contractId:id(contractId,'Contract'),productId:id(productId,'Product')},transaction);
    if (!sheet) throw v.invalid('Design sheet not found for this product and contract.',404);
    const items = await v.select(db, `SELECT d.item_id,d.is_group,d.uom,a.item_name,a.category_id,c.category_name
      FROM designsheetdetails d JOIN st_additem a ON a.id=d.item_id LEFT JOIN st_categorymaster c ON c.id=a.category_id
      WHERE d.designsheetno=:sheetNo ORDER BY d.is_group,d.id`, {sheetNo:sheet.designsheetno},transaction);
    for (const item of items) {
      item.group_items = Number(item.is_group) === 1 ? await v.select(db, `SELECT a.id,a.item_name,u.unit_name AS uom,
        COALESCE((SELECT ROUND(SUM(CASE WHEN s.store_type IN ('0','1','3') THEN s.quantity WHEN s.store_type IN ('2','4') THEN -s.quantity ELSE 0 END),2)
          FROM st_stock_register s WHERE s.item_id=a.id),0) AS current_stock
        FROM st_additem a LEFT JOIN st_measurementunits u ON u.id=a.uom WHERE a.category_id=:categoryId AND a.status='Y' AND a.itemtype='RawMaterial' ORDER BY a.item_name`, {categoryId:item.category_id},transaction) : [];
    }
    return { design_qty:Number(sheet.quantity), pending_qty:null, items, semi_products:await this.getSemiProducts(db,transaction) };
  }
  async getReverseIndentDetails(db, reverseId) {
    const [header] = await v.select(db, `${headerSql} WHERE r.reverse_id=:reverseId ORDER BY r.id DESC LIMIT 1`, {reverseId});
    if (!header) return null;
    const items = await v.select(db, `SELECT s.id,s.item_id,s.quantity,s.store_type,i.item_name,u.unit_name AS uom
      FROM st_stock_register s LEFT JOIN st_additem i ON i.id=s.item_id LEFT JOIN st_measurementunits u ON u.id=i.uom
      WHERE s.reverse_id=:reverseId ORDER BY s.id`, {reverseId});
    const [site_details] = await v.select(db, `SELECT * FROM sitesettings_details WHERE status='Y' LIMIT 1`);
    const [sitesetting] = await v.select(db, 'SELECT * FROM sitesettings LIMIT 1');
    const finished = items.filter(row => String(row.item_id) === String(header.finishedproduct_id) && ['0','1','99'].includes(String(row.store_type)));
    const [sheet] = await v.select(db, 'SELECT quantity FROM designsheet WHERE contract_id=:contractId AND item_id=:productId ORDER BY id LIMIT 1',
      {contractId:header.contract_id,productId:header.finishedproduct_id});
    const [received] = await v.select(db, `SELECT COALESCE(SUM(quantity),0) AS qty FROM st_stock_register
      WHERE contract_id=:contractId AND item_id=:productId AND store_type IN ('0','1') AND COALESCE(reverse_id,'')!=:reverseId`,
      {contractId:header.contract_id,productId:header.finishedproduct_id,reverseId});
    const semi = header.itemtype === 'Semi-Finished Product';
    return { ...header, status:header.status || (items.some(row=>String(row.store_type)==='99') ? 'draft' : 'finalized'),
      items, raw_items:items.filter(row=>String(row.item_id)!==String(header.finishedproduct_id)),
      finished_qty:finished.reduce((sum,row)=>sum+Number(row.quantity),0),
      pending_qty:semi ? null : Math.max(0, Number(sheet?.quantity || 0)-Number(received.qty)),
      can_change:isLegacyToday(header.issue_date), site_details, sitesetting };
  }
  async schema(db) {
    const headers = await db.query('SHOW COLUMNS FROM reverseindent', {type:QueryTypes.SELECT});
    const stock = await db.query("SHOW COLUMNS FROM st_stock_register LIKE 'store_type'", {type:QueryTypes.SELECT});
    return { status:headers.some(c=>c.Field==='status'), draft:stock[0]?.Type.includes("'99'") };
  }
  async saveReverseIndent(db, data, reverseId = null) {
    const draft = data.action === 'draft';
    if (data.action && !['draft','finalize'].includes(data.action)) throw v.invalid('Invalid save action.');
    const qty = quantity(data.finished_qty,'Received Qty',true);
    const contractId = id(data.contract_id,'Contract');
    const productId = id(data.finishedproduct_id || data.finisheditem_id,'Product');
    const issueDate = v.date(data.issue_date,'Issued Date');
    const today = new Intl.DateTimeFormat('en-CA',{timeZone:'Asia/Kolkata',year:'numeric',month:'2-digit',day:'2-digit'}).format(new Date());
    if (issueDate > today) throw v.invalid('Issued Date cannot be in the future.');
    const receivedName = String(data.received_name || '').trim();
    if (!receivedName || receivedName.length>255) throw v.invalid('Received By is required (maximum 255 characters).');
    const machineId = data.machine_id ? id(data.machine_id,'Machine') : null;
    const schema = await this.schema(db);
    if (!schema.status || (draft && !schema.draft)) throw v.invalid('Reverse draft schema is missing. Run the Reverse Manager schema migration for this tenant.',503);
    const rows = [], seen = new Set();
    if (data.items != null && !Array.isArray(data.items)) throw v.invalid('Items must be a list.');
    for (const item of data.items || []) {
      if (item.quantity === '' || item.quantity == null) continue;
      const itemId = id(item.item_id,'Raw Material');
      const amount = quantity(item.quantity,'Reverse quantity');
      if (seen.has(itemId) || itemId===productId) throw v.invalid('This Item Already added');
      seen.add(itemId); rows.push({item_id:itemId,quantity:amount});
    }
    const transaction = await db.transaction();
    try {
      // Serialize reverse number allocation without altering contracts or design sheets.
      await v.select(db,'SELECT id FROM reverseindent ORDER BY id DESC LIMIT 1 FOR UPDATE',{},transaction);
      const [existing] = reverseId ? await v.select(db,'SELECT * FROM reverseindent WHERE reverse_id=:reverseId FOR UPDATE',{reverseId},transaction) : [];
      if (reverseId && !existing) throw v.invalid('Invalid reverse indent.',404);
      if (existing) {
        if (!isLegacyToday(existing.issue_date)) throw v.invalid('Reverse indents can only be edited on their issue day.',403);
        if (existing.status!=='draft') throw v.invalid('Finalized reverse indent cannot be edited.',409);
        const active = await v.select(db,"SELECT id FROM st_stock_register WHERE reverse_id=:reverseId AND store_type!='99' FOR UPDATE",{reverseId},transaction);
        if (active.length) throw v.invalid('This draft contains posted stock. Review it before editing.',409);
        if (String(existing.contract_id)!==contractId || String(existing.finishedproduct_id)!==productId) throw v.invalid('Contract and Product cannot be changed when editing.');
      }
      const [contract] = await v.select(db,'SELECT id,status FROM contracts WHERE id=:contractId',{contractId},transaction);
      if (!contract || (!existing && contract.status!=='Y')) throw v.invalid('Select an active Contract.');
      const [product] = await v.select(db,'SELECT id,itemtype,status FROM st_additem WHERE id=:productId FOR UPDATE',{productId},transaction);
      if (!product) throw v.invalid('Product not found.');
      const semi = product.itemtype === 'Semi-Finished Product';
      if (!semi && product.itemtype!=='FinishedProduct') throw v.invalid('Select a Finished or Semi-Finished Product.');
      if (!existing && semi && product.status!=='Y') throw v.invalid('Select an active Semi-Finished Product.');
      if (machineId) {
        const [machine] = await v.select(db,'SELECT id FROM machine_master WHERE id=:machineId',{machineId},transaction);
        if (!machine) throw v.invalid('Machine not found.');
      }
      if (!draft && !semi) {
        const [sheet] = await v.select(db,'SELECT quantity FROM designsheet WHERE contract_id=:contractId AND item_id=:productId ORDER BY id LIMIT 1 FOR UPDATE',{contractId,productId},transaction);
        if (!sheet) throw v.invalid('Design sheet not found for this product and contract.');
        let pending = Number(sheet.quantity);
        // CakePHP edit.ctp subtracts other receipts; add uses the unchanged sheet quantity.
        if (existing) {
          const [received] = await v.select(db,`SELECT COALESCE(SUM(quantity),0) AS qty FROM st_stock_register WHERE contract_id=:contractId
            AND item_id=:productId AND store_type IN ('0','1') AND COALESCE(reverse_id,'')!=:reverseId`,{contractId,productId,reverseId},transaction);
          pending -= Number(received.qty);
        }
        if (!Number.isFinite(pending) || qty>pending) throw v.invalid('Received Qty cannot exceed Design Sheet Qty.');
      }
      // The source design product is a UI selection, not the stock product persisted by PHP.
      if (!existing && data.design_product_id) {
        const design = await this.getDesignSheetDetails(db,contractId,id(data.design_product_id,'Design Product'),transaction);
        const allowed = new Set(design.items.flatMap(row=>Number(row.is_group)===1?row.group_items.map(child=>String(child.id)):[String(row.item_id)]));
        if (rows.some(row=>!allowed.has(row.item_id))) throw v.invalid('Raw Material is not applicable to the selected design sheet.');
      }
      if (rows.length) {
        const found = await v.select(db,'SELECT id FROM st_additem WHERE id IN (:ids)',{ids:rows.map(row=>row.item_id)},transaction);
        if (found.length!==rows.length) throw v.invalid('Raw Material not found.');
      }
      const number = String(existing?.reverse_id || data.reverse_id || await this.getNextReverseId(db,transaction));
      if (!/^\d+$/.test(number) || number.length>55) throw v.invalid('Invalid Reverse ID.');
      if (!existing) {
        const duplicate = await v.select(db,'SELECT id FROM reverseindent WHERE reverse_id=:number FOR UPDATE',{number},transaction);
        if (duplicate.length) throw v.invalid('Reverse ID already exists. Refresh the form.',409);
      }
      const header = {number,contractId,productId,machineId,receivedName,issueDate,status:draft?'draft':'finalized'};
      let resultId = existing?.id;
      if (existing) {
        await v.write(db,`UPDATE reverseindent SET machine_id=:machineId,received_name=:receivedName,issue_date=:issueDate,status=:status,updated=NOW() WHERE id=:resultId`,{...header,resultId},transaction);
        await db.query("DELETE FROM st_stock_register WHERE reverse_id=:number AND store_type='99'",{replacements:{number},type:QueryTypes.DELETE,transaction});
      } else {
        resultId = await v.insert(db,`INSERT INTO reverseindent (reverse_id,contract_id,finishedproduct_id,machine_id,received_name,issue_date,status,created)
          VALUES (:number,:contractId,:productId,:machineId,:receivedName,:issueDate,:status,NOW())`,header,transaction);
      }
      const stock = async (itemId, amount, type) => v.insert(db,`INSERT INTO st_stock_register
        (reverse_id,contract_id,finishedproduct_id,item_id,quantity,issue_date,store_type)
        VALUES (:number,:contractId,:productId,:itemId,:amount,:issueDate,:type)`,{...header,itemId,amount,type},transaction);
      const opening = await v.select(db,"SELECT id FROM st_stock_register WHERE item_id=:productId AND store_type='0' LIMIT 1 FOR UPDATE",{productId},transaction);
      if (!draft && semi && !opening.length) await stock(productId,0,'0');
      await stock(productId,qty,draft?'99':semi||opening.length?'1':'0');
      for (const row of rows) await stock(row.item_id,row.quantity,draft?'99':'3');
      await transaction.commit();
      return resultId;
    } catch (error) { await transaction.rollback(); throw error; }
  }
  async deleteReverseIndent(db, reverseId) {
    const transaction = await db.transaction();
    try {
      const [header] = await v.select(db,'SELECT * FROM reverseindent WHERE reverse_id=:reverseId FOR UPDATE',{reverseId},transaction);
      if (!header) throw v.invalid('Reverse Indent not found.',404);
      if (!isLegacyToday(header.issue_date)) throw v.invalid('Reverse indents can only be deleted on their issue day.',403);
      await v.select(db,'SELECT id FROM st_additem WHERE id=:productId FOR UPDATE',{productId:header.finishedproduct_id},transaction);
      // PHP deletes every stock row for the reverse, including its opening/product receipt.
      await db.query('DELETE FROM st_stock_register WHERE reverse_id=:reverseId',{replacements:{reverseId},type:QueryTypes.DELETE,transaction});
      await db.query('DELETE FROM reverseindent WHERE id=:headerId',{replacements:{headerId:header.id},type:QueryTypes.DELETE,transaction});
      await transaction.commit(); return true;
    } catch (error) { await transaction.rollback(); throw error; }
  }
}
module.exports = new ReverseIndentService();
