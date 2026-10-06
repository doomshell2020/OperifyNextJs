const { getTenantSequelize } = require('../../config/sequelize');
const L = require('./legacyStore');
class JobChallanService {
  async list(dbName, query = {}) {
    const db = await getTenantSequelize(dbName);
    const rows = await L.select(db, `SELECT j.*, s.name AS vendor_name,
      COALESCE(j.total_amount,0)+COALESCE(j.gst_amount,0) AS final_amount
      FROM job_challans j LEFT JOIN sub_contractors s ON s.id=j.sub_contractors_id ORDER BY j.id DESC`);
    return L.paginate(L.filterRows(rows, query, 'jc_date').map(row => ({ ...row, final_amount: Number(row.final_amount), vendor: { id: row.sub_contractors_id, name: row.vendor_name } })), query);
  }
  async searchItems(dbName, search = '', processType = '') {
    const db = await getTenantSequelize(dbName);
    const type = processType === 'Manufacturing' ? 'RawMaterial' : processType === 'Semi-Finished' ? 'Semi-Finished Product' : '';
    return L.select(db, `SELECT a.id,a.item_name,a.itemtype,a.item_isbn,u.unit_name FROM st_additem a LEFT JOIN st_measurementunits u ON u.id=a.uom WHERE a.status='Y' AND a.item_name LIKE :search ${type ? 'AND a.itemtype=:type' : ''} ORDER BY a.item_name`, { search: `%${search}%`, type });
  }
  async getItemInHandStock(dbName, item_id) {
    const db = await getTenantSequelize(dbName), id = L.positiveId(item_id);
    const [item] = await L.select(db, 'SELECT a.*,t.tax AS tax_rate FROM st_additem a LEFT JOIN st_taxmaster t ON t.id=a.tax WHERE a.id=:id', { id });
    if (!item) L.fail('Item not found', 404);
    return { inhand_qty: await L.stock(db, id), hsn_code: item.item_isbn || '', tax_rate: item.tax_rate || '', item_name: item.item_name, itemtype: item.itemtype };
  }
  async getVendorGst(dbName, vendor_id) {
    const db = await getTenantSequelize(dbName);
    const [vendor] = await L.select(db, 'SELECT gst_no FROM sub_contractors WHERE id=:id', { id: L.positiveId(vendor_id) });
    if (!vendor) L.fail('Sub Contractor not found', 404);
    return vendor;
  }
  async listVendors(dbName) { return L.select(await getTenantSequelize(dbName), 'SELECT id,name,gst_no,status FROM sub_contractors ORDER BY name'); }
  async listTaxMaster(dbName) { return L.select(await getTenantSequelize(dbName), "SELECT id,tax FROM st_taxmaster WHERE status='Y' ORDER BY id"); }
  async addVendor(dbName, payload) {
    const db = await getTenantSequelize(dbName);
    if (!String(payload.name || '').trim()) L.fail('Company/Party Name is required');
    const data = Object.fromEntries(['name','contact_person','mobile','email','pan_no','gst_no','address'].map(key => [key, String(payload[key] || '').trim()]));
    data.database_name = dbName;
    data.status = 'Active';
    return db.transaction(async t => ({ id: await L.insert(db, 'sub_contractors', { ...data, created: new Date(), modified: new Date() }, t), name: data.name }));
  }
  async create(dbName, payload) {
    const db = await getTenantSequelize(dbName);
    const challan_no = String(payload.challan_no || '').trim();
    if (!/^\d+$/.test(challan_no)) L.fail('JC No. is required and must contain only numeric values.');
    const jc_date = L.date(payload.jc_date);
    const sub_contractors_id = L.positiveId(payload.sub_contractors_id);
    const processing_type = payload.processing_type || 'Manufacturing';
    if (!['Manufacturing','In Progress'].includes(processing_type)) L.fail('Invalid Process Type');
    // A named lock protects numbering on the legacy database, which has no unique
    // challan_no index. The pinned transaction connection owns and releases it.
    return db.transaction(async t => {
      const lockName = `jc-number:${dbName}`.slice(0,64);
      const [lock] = await L.select(db, 'SELECT GET_LOCK(:name,10) AS acquired', { name: lockName }, t);
      if (Number(lock.acquired) !== 1) L.fail('Another JC is being saved. Please retry.', 409);
      try {
        const existing = await L.select(db, 'SELECT id FROM job_challans WHERE challan_no=:challan_no LIMIT 1 FOR UPDATE', { challan_no }, t);
        if (existing.length) L.fail('JC No. already exists. Please use a different JC No.', 409);
        const [vendor] = await L.select(db, 'SELECT id FROM sub_contractors WHERE id=:id', { id: sub_contractors_id }, t);
        if (!vendor) L.fail('Invalid Sub Contractor');
        const raw = (Array.isArray(payload.items) ? payload.items : []).filter(item => item.item_id && Number(item.quantity) > 0);
        if (!raw.length && processing_type === 'Manufacturing') L.fail('Please add at least one item with a valid quantity.');
        const lines = raw.map(item => ({ ...item, requiredType: 'RawMaterial' }));
        if (processing_type === 'In Progress') lines.push({ item_id: payload.semi_finished_item_id, quantity: payload.semi_finished_quantity, rate: payload.semi_finished_rate, tax_rate: payload.semi_finished_tax_rate, hsn_code: payload.semi_finished_hsn_code, requiredType: 'Semi-Finished Product' });
        // Lock item masters in deterministic order. Repeated rows share a running
        // stock budget, so they cannot each spend the same available quantity.
        const ids = [...new Set(lines.map(item => L.positiveId(item.item_id)))].sort((a,b) => a-b);
        const masters = await L.select(db, "SELECT * FROM st_additem WHERE id IN (:ids) ORDER BY id FOR UPDATE", { ids }, t);
        const budgets = new Map();
        const processed = [];
        let total_amount = 0, gst_amount = 0;
        for (const line of lines) {
          const item_id = L.positiveId(line.item_id), master = masters.find(item => Number(item.id) === item_id);
          if (!master || master.status !== 'Y' || master.itemtype !== line.requiredType) L.fail(`Select an active ${line.requiredType} item`);
          const quantity = L.number(line.quantity, 'Quantity', true);
          if (!budgets.has(item_id)) budgets.set(item_id, await L.stock(db, item_id, t));
          if (quantity > budgets.get(item_id)) L.fail(`Insufficient stock for ${master.item_name}. Available: ${budgets.get(item_id)}`);
          budgets.set(item_id, L.round(budgets.get(item_id)-quantity));
          const rate = L.number(line.rate, 'Rate'), tax_rate = L.number(line.tax_rate, 'Tax rate');
          const amount = L.round(quantity*rate), tax_amount = L.round(amount*tax_rate/100), total = L.round(amount+tax_amount);
          total_amount += amount; gst_amount += tax_amount;
          processed.push({ item_id, item_name: master.item_name, quantity, hsn_code: line.hsn_code || master.item_isbn || '', rate, tax_rate, amount, tax_amount, total, total_amount: total, cgst: tax_amount/2, sgst: tax_amount/2, return_type: line.requiredType });
        }
        const id = await L.insert(db, 'job_challans', { challan_no, jc_date, sub_contractors_id, processing_type, total_amount: L.round(total_amount), gst_amount: L.round(gst_amount), final_amount: L.round(total_amount+gst_amount), estimated_values: L.number(payload.estimated_values, 'Estimated Value'), expected_days: L.number(payload.expected_days, 'Expected Days'), work_description: String(payload.work_description || ''), modified: new Date() }, t);
        for (const item of processed) {
          await L.insert(db, 'job_challan_items', { ...item, challan_id: id }, t);
          await L.movement(db, { challan_id: id, item_id: item.item_id, quantity: item.quantity, issue_date: jc_date, store_type: '2', sub_contractors_id }, t);
        }
        return { id, challan_no };
      } finally { await L.select(db, 'SELECT RELEASE_LOCK(:name)', { name: lockName }, t); }
    });
  }
  async getDetail(dbName, id, senderDb) {
    const sourceDb = senderDb || dbName, db = await getTenantSequelize(dbName);
    const jc = await L.source(dbName, sourceDb, id);
    const [vendor] = await L.select(db, `SELECT * FROM ${L.table('sub_contractors',sourceDb)} WHERE id=:id`, { id: jc.sub_contractors_id });
    const items = await L.select(db, `SELECT i.*,a.item_name AS master_item_name,a.itemtype,a.item_isbn,u.unit_name,
      COALESCE((SELECT SUM(r.received_qty) FROM ${L.table('job_challan_receives',sourceDb)} r WHERE r.challan_id=i.challan_id AND r.item_id=i.item_id),0) AS received_qty
      FROM ${L.table('job_challan_items',sourceDb)} i LEFT JOIN ${L.table('st_additem',sourceDb)} a ON a.id=i.item_id LEFT JOIN ${L.table('st_measurementunits',sourceDb)} u ON u.id=a.uom WHERE i.challan_id=:id ORDER BY i.id`, { id: jc.id });
    const history = await L.select(db, `SELECT r.*,a.item_name FROM ${L.table('job_challan_receives',sourceDb)} r LEFT JOIN ${L.table('st_additem',sourceDb)} a ON a.id=r.item_id WHERE r.challan_id=:id ORDER BY r.receive_date,r.id`, { id: jc.id });
    const dispatch = new Map();
    for (const row of items) dispatch.set(Number(row.item_id), (dispatch.get(Number(row.item_id)) || 0)+Number(row.quantity));
    jc.vendor = vendor || {}; jc.final_amount = Number(jc.total_amount || 0)+Number(jc.gst_amount || 0);
    jc.job_challan_items = items.map(row => ({ ...row, total: row.total ?? row.total_amount ?? Number(row.amount || 0)+Number(row.tax_amount || 0), return_type: row.return_type || row.itemtype, pending_qty: Math.max(0, L.round(dispatch.get(Number(row.item_id))-Number(row.received_qty))), item: { id: row.item_id, item_name: row.item_name || row.master_item_name, unit_name: row.unit_name } }));
    const tracking = {};
    if (vendor?.database_name && vendor.database_name !== sourceDb) {
      for (const row of items) {
        const [local] = await L.select(db, `SELECT id FROM ${L.table('st_additem',vendor.database_name)} WHERE item_name=:name LIMIT 1`, { name: row.item_name || row.master_item_name });
        const [used] = local ? await L.select(db, `SELECT COALESCE(ROUND(SUM(quantity),2),0) AS consumed FROM ${L.table('st_stock_register',vendor.database_name)} WHERE item_id=:id AND store_type IN ('2','4')`, { id: local.id }) : [{}];
        tracking[row.item_id] = { consumed: Number(used.consumed || 0), balance: local ? await L.stock(db, local.id, null, vendor.database_name) : 0 };
      }
    }
    const [site_details] = await L.select(db, `SELECT * FROM ${L.table('sitesettings_details',sourceDb)} WHERE status='Y' LIMIT 1`);
    const [sitesetting] = await L.select(db, `SELECT * FROM ${L.table('sitesettings',sourceDb)} LIMIT 1`);
    return { challan: jc, source_db: sourceDb, site_details, sitesetting, history, tracking };
  }
  async getPdfDetail(dbName, id, senderDb) {
    if (senderDb) return this.getDetail(dbName,id,senderDb);
    try { return await this.getDetail(dbName,id); }
    catch (error) {
      if (error.status !== 404) throw error;
      for (const sender of await L.linked(dbName)) {
        try { return await this.getDetail(dbName,id,sender.database_name); }
        catch (lookupError) { if (![403,404].includes(lookupError.status)) throw lookupError; }
      }
      throw error;
    }
  }
  async remove(dbName, id) {
    const db = await getTenantSequelize(dbName); id = L.positiveId(id);
    return db.transaction(async t => {
      const jc = await L.source(dbName,dbName,id,t,db);
      const [count] = await L.select(db,'SELECT COUNT(*) AS total FROM job_challan_receives WHERE challan_id=:id',{id},t);
      if (Number(count.total)) L.fail('This JC cannot be deleted because it has already been used in JC Receive.',409);
      // indent_id is shared with ordinary indents. Scope reversal to this JC's
      // subcontractor and its zero-contract movements, preserving other modules.
      await db.query("DELETE FROM st_stock_register WHERE indent_id=:id AND sub_contractors_id=:sub AND COALESCE(contract_id,'0')='0' AND COALESCE(finishedproduct_id,'0')='0' AND store_type IN ('2','1')",{replacements:{id:String(id),sub:jc.sub_contractors_id},transaction:t});
      await db.query('DELETE FROM job_challan_items WHERE challan_id=:id',{replacements:{id},transaction:t});
      await db.query('DELETE FROM job_challans WHERE id=:id',{replacements:{id},transaction:t});
    });
  }
}
module.exports = new JobChallanService();
