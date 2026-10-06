const { getTenantModels, getTenantSequelize } = require('../../config/sequelize');
const { Op, QueryTypes } = require('sequelize');

class JobChallanService {

  // ─── HELPER: Get current stock for an item ───────────────────────────────
  async _getItemStock(models, item_id, transaction) {
    const inOpts = { where: { item_id, store_type: { [Op.in]: ['0', '1', '3'] } } };
    const outOpts = { where: { item_id, store_type: { [Op.in]: ['2', '4'] } } };
    if (transaction) { inOpts.transaction = transaction; outOpts.transaction = transaction; }

    const inward  = parseFloat(await models.st_stock_register.sum('quantity', inOpts) || 0);
    const outward = parseFloat(await models.st_stock_register.sum('quantity', outOpts) || 0);
    return Math.round((inward - outward) * 100) / 100;
  }

  // ─── LIST (with filter + pagination) ────────────────────────────────────
  async list(dbName, query) {
    const models = await getTenantModels(dbName);
    const { fromDate, toDate, vendorId, status, challanNo, page = 1, limit = 10 } = query;
    const offset = (page - 1) * limit;

    const where = {};
    if (challanNo) where.challan_no = { [Op.like]: `%${challanNo}%` };
    if (status)    where.status = status;
    if (vendorId)  where.sub_contractors_id = vendorId;
    if (fromDate && toDate) where.jc_date = { [Op.between]: [fromDate, toDate] };

    const { count, rows } = await models.job_challans.findAndCountAll({
      where,
      attributes: {exclude:['final_amount','gst_no','semi_finished_item_id','added_by']},
      order: [['jc_date', 'DESC'], ['id', 'DESC']],
      limit:  parseInt(limit),
      offset: parseInt(offset)
    });

    const ids = [...new Set(rows.map(row => row.sub_contractors_id))];
    const db = await getTenantSequelize(dbName);
    const recipients = ids.length ? await db.query('SELECT id, name FROM sub_contractors WHERE id IN (:ids)', {replacements:{ids},type:QueryTypes.SELECT}) : [];
    const byId = new Map(recipients.map(recipient => [Number(recipient.id), recipient]));
    const items = rows.map(row => ({...row.get({plain:true}), final_amount:Number(row.total_amount || 0) + Number(row.gst_amount || 0), vendor:byId.get(Number(row.sub_contractors_id)) || null}));
    return { total: count, items, page: parseInt(page), limit: parseInt(limit) };
  }

  // ─── ITEM SEARCH (by name, filtered by itemtype) ─────────────────────────
  async searchItems(dbName, search, processType) {
    const models = await getTenantModels(dbName);

    const where = {
      item_name: { [Op.like]: `%${search}%` },
      status: 'Y'
    };

    // Match CakePHP: Manufacturing → RawMaterial; Semi-Finished → Semi-Finished Product
    if (processType === 'Manufacturing') {
      where.itemtype = 'RawMaterial';
    } else if (processType === 'Semi-Finished') {
      where.itemtype = 'Semi-Finished Product';
    }

    const items = await models.st_additem.findAll({
      where,
      attributes: ['id', 'item_name', 'itemtype', 'item_isbn'],
      limit: 20
    });

    return items;
  }

  // ─── ITEM IN-HAND STOCK + HSN + TAX FETCH ────────────────────────────────
  async getItemInHandStock(dbName, item_id) {
    const models = await getTenantModels(dbName);

    const parsedId = parseInt(item_id);
    if (!parsedId || isNaN(parsedId)) throw new Error('Invalid item ID');

    const item = await models.st_additem.findByPk(parsedId);
    if (!item) throw new Error('Item not found');

    const inhand_qty = await this._getItemStock(models, parsedId, null);

    // Fetch default tax rate from st_taxmaster if linked
    let tax_rate = '';
    if (item.tax) {
      const taxRow = await models.st_taxmaster.findByPk(item.tax);
      if (taxRow) tax_rate = taxRow.tax;
    }

    return {
      inhand_qty,
      hsn_code: item.item_isbn || '',
      tax_rate,
      item_name: item.item_name,
      itemtype: item.itemtype
    };
  }

  // ─── VENDOR GST FETCH ─────────────────────────────────────────────────────
  async getVendorGst(dbName, vendor_id) {
    const db = await getTenantSequelize(dbName);
    const parsedId = parseInt(vendor_id);
    if (!parsedId || isNaN(parsedId)) throw new Error('Invalid vendor ID');

    const [vendor] = await db.query('SELECT id, name, gst_no FROM sub_contractors WHERE id=:id', {replacements:{id:parsedId},type:QueryTypes.SELECT});
    if (!vendor) throw new Error('Vendor not found');
    return { gst_no: vendor.gst_no || '' };
  }

  // ─── VENDORS LIST for dropdown ────────────────────────────────────────────
  async listVendors(dbName) {
    const db = await getTenantSequelize(dbName);
    return db.query('SELECT id, name, gst_no FROM sub_contractors ORDER BY name ASC', {type:QueryTypes.SELECT});
  }

  // ─── TAX MASTER LIST for dropdown ────────────────────────────────────────
  async listTaxMaster(dbName) {
    const models = await getTenantModels(dbName);
    return await models.st_taxmaster.findAll({
      where: { status: 'Y' },
      attributes: ['id', 'tax'],
      order: [['id', 'ASC']]
    });
  }

  // ─── CREATE (full business logic + transaction) ───────────────────────────
  async create(dbName, payload, user) {
    const models    = await getTenantModels(dbName);
    const sequelize = await getTenantSequelize(dbName);

    return await sequelize.transaction(async (t) => {
      // 1. Validate challan_no
      const rawChallanNo = String(payload.challan_no || '').trim();
      if (!rawChallanNo) throw new Error('JC No. is required.');
      if (!/^\d+$/.test(rawChallanNo)) throw new Error('JC No. must contain only numeric values.');

      const existing = await models.job_challans.findOne({
        where: { challan_no: rawChallanNo }, transaction: t
      });
      if (existing) throw new Error('JC No. already exists. Please use a different JC No.');

      // 2. Validate date
      const jc_date = payload.jc_date;
      if (!jc_date) throw new Error('Date is required.');

      // 3. Validate sub-contractor
      const sub_contractors_id = parseInt(payload.sub_contractors_id);
      if (!sub_contractors_id || isNaN(sub_contractors_id)) throw new Error('Sub Contractor is required.');
      const [vendor] = await sequelize.query('SELECT id FROM sub_contractors WHERE id=:id', {replacements:{id:sub_contractors_id},type:QueryTypes.SELECT,transaction:t});
      if (!vendor) throw new Error('Invalid Sub Contractor.');

      // 4. Validate process type
      const validProcessTypes = ['Manufacturing', 'In Progress'];
      const processType = payload.processing_type;
      if (!validProcessTypes.includes(processType)) throw new Error('Invalid Process Type.');

      // 5. Validate items (raw materials)
      const rawItems = Array.isArray(payload.items) ? payload.items : [];
      const validItems = rawItems.filter(i => i.item_id && parseFloat(i.quantity) > 0);

      if (validItems.length === 0 && processType === 'Manufacturing') {
        throw new Error('Please add at least one item with a valid quantity.');
      }

      let totalAmount = 0;
      let totalTax    = 0;
      const processedItems = [];

      for (const item of validItems) {
        const item_id = parseInt(item.item_id);
        if (!item_id || isNaN(item_id)) throw new Error('Invalid item ID in items list.');

        // Validate item exists and is a RawMaterial
        const itemData = await models.st_additem.findOne({
          where: { id: item_id, status: 'Y' }, transaction: t
        });
        if (!itemData) throw new Error(`Item ID ${item_id} not found or inactive.`);
        if (itemData.itemtype !== 'RawMaterial') {
          throw new Error(`Item "${itemData.item_name}" is not a Raw Material.`);
        }

        const qty = parseFloat(item.quantity);
        if (isNaN(qty) || qty <= 0 || !isFinite(qty)) {
          throw new Error(`Invalid quantity for item "${itemData.item_name}".`);
        }

        // Server-side stock check
        const currentStock = await this._getItemStock(models, item_id, t);
        if (qty > currentStock) {
          throw new Error(
            `Insufficient stock for "${itemData.item_name}". Available: ${currentStock}, Requested: ${qty}`
          );
        }

        // Server-side recalculation — NEVER trust frontend amounts
        const rate     = parseFloat(item.rate) || 0;
        const tax_rate = parseFloat(item.tax_rate) || 0;
        const amount   = Math.round(qty * rate * 100) / 100;
        const tax_amt  = Math.round((amount * tax_rate) / 100 * 100) / 100;
        const total    = Math.round((amount + tax_amt) * 100) / 100;

        totalAmount += amount;
        totalTax    += tax_amt;

        processedItems.push({
          item_id,
          item_name: itemData.item_name,
          quantity:  qty,
          hsn_code:  String(item.hsn_code || itemData.item_isbn || '').substring(0, 50),
          rate,
          tax_rate,
          tax_amount: tax_amt,
          amount,
          total,
          return_type: 'RawMaterial'
        });
      }

      // 6. Semi-Finished Product (only for "In Progress")
      let sfpItem = null;
      if (processType === 'In Progress') {
        const sf_item_id = parseInt(payload.semi_finished_item_id);
        const sf_qty     = parseFloat(payload.semi_finished_quantity);

        if (!sf_item_id || isNaN(sf_item_id)) throw new Error('Semi-Finished Product is required for "In Progress" type.');
        if (isNaN(sf_qty) || sf_qty <= 0) throw new Error('Semi-Finished Product quantity must be greater than 0.');

        const sfItemData = await models.st_additem.findOne({
          where: { id: sf_item_id, status: 'Y' }, transaction: t
        });
        if (!sfItemData) throw new Error('Semi-Finished Product not found or inactive.');
        if (sfItemData.itemtype !== 'Semi-Finished Product') {
          throw new Error(`Item "${sfItemData.item_name}" is not a Semi-Finished Product.`);
        }

        const sfStock = await this._getItemStock(models, sf_item_id, t);
        if (sf_qty > sfStock) {
          throw new Error(
            `Insufficient stock for Semi-Finished Product "${sfItemData.item_name}". Available: ${sfStock}, Requested: ${sf_qty}`
          );
        }

        sfpItem = {
          item_id: sf_item_id,
          item_name: sfItemData.item_name,
          quantity: sf_qty,
          return_type: 'Semi-Finished Product',
          rate: 0, tax_rate: 0, tax_amount: 0, amount: 0, total: 0
        };
      }

      // 7. Extra header fields
      const estimatedValues = parseFloat(payload.estimated_values) || 0;
      const expectedDays    = parseInt(payload.expected_days) || 0;
      const workDescription = String(payload.work_description || '').trim().substring(0, 500);

      // 8. Compute master totals (server-side only)
      const grandTotal = Math.round((totalAmount + totalTax) * 100) / 100;

      // 9. Insert Job Challan master (whitelist only safe fields)
      const challan = await models.job_challans.create({
        challan_no:        rawChallanNo,
        jc_date,
        sub_contractors_id,
        processing_type:   processType,
        total_amount:      Math.round(totalAmount * 100) / 100,
        gst_amount:        Math.round(totalTax * 100) / 100,
        final_amount:      grandTotal,
        estimated_values:  estimatedValues,
        expected_days:     expectedDays,
        work_description:  workDescription,
        status:            'Pending',
        added_by:          user.id
      }, { transaction: t });

      // 10. Insert raw material items + stock dispatch entries
      for (const item of processedItems) {
        await models.job_challan_items.create({
          challan_id:  challan.id,
          item_id:     item.item_id,
          quantity:    item.quantity,
          hsn_code:    item.hsn_code,
          rate:        item.rate,
          tax_rate:    item.tax_rate,
          tax_amount:  item.tax_amount,
          amount:      item.amount,
          total:       item.total,
          return_type: 'RawMaterial'
        }, { transaction: t });

        // Stock outward entry (store_type 2 = dispatch) — matching CakePHP logic
        await models.st_stock_register.create({
          indent_id:           String(challan.id),
          item_id:             item.item_id,
          quantity:            item.quantity,
          issue_date:          jc_date,
          store_type:          '2',
          sub_contractors_id:  sub_contractors_id,
          contract_id:         '0',
          finishedproduct_id:  '0',
          added_time:          new Date()
        }, { transaction: t });
      }

      // 11. Insert Semi-Finished Product item + stock dispatch entry (if In Progress)
      if (sfpItem) {
        await models.job_challan_items.create({
          challan_id:  challan.id,
          item_id:     sfpItem.item_id,
          quantity:    sfpItem.quantity,
          hsn_code:    '',
          rate:        0,
          tax_rate:    0,
          tax_amount:  0,
          amount:      0,
          total:       0,
          return_type: 'Semi-Finished Product'
        }, { transaction: t });

        await models.st_stock_register.create({
          indent_id:           String(challan.id),
          item_id:             sfpItem.item_id,
          quantity:            sfpItem.quantity,
          issue_date:          jc_date,
          store_type:          '2',
          sub_contractors_id:  sub_contractors_id,
          contract_id:         '0',
          finishedproduct_id:  '0',
          added_time:          new Date()
        }, { transaction: t });
      }

      return challan;
    });
  }

  // ─── GET DETAIL ───────────────────────────────────────────────────────────
  async getDetail(dbName, id) {
    const sequelize = await getTenantSequelize(dbName);
    const parsedId = parseInt(id);
    if (!parsedId || isNaN(parsedId)) throw new Error('Invalid Job Challan ID');

    const challanRows = await sequelize.query(`
      SELECT 
        jc.*,
        v.name AS vendor_name,
        v.address AS vendor_address,
        v.gst_no AS vendor_gst_no
      FROM job_challans jc
      LEFT JOIN sub_contractors v ON v.id = jc.sub_contractors_id
      WHERE jc.id = :id
      LIMIT 1
    `, {
      replacements: { id: parsedId },
      type: QueryTypes.SELECT
    });

    const challan = challanRows[0];
    if (!challan) throw new Error('Job Challan not found');

    const items = await sequelize.query(`
      SELECT 
        jci.*,
        COALESCE(jci.total_amount, jci.amount + COALESCE(jci.tax_amount, 0)) AS total,
        a.item_name AS master_item_name,
        mu.unit_name
      FROM job_challan_items jci
      LEFT JOIN st_additem a ON a.id = jci.item_id
      LEFT JOIN st_measurementunits mu ON mu.id = a.uom
      WHERE jci.challan_id = :id
      ORDER BY jci.id ASC
    `, {
      replacements: { id: parsedId },
      type: QueryTypes.SELECT
    });

    const siteRows = await sequelize.query("SELECT * FROM sitesettings_details WHERE status = 'Y' LIMIT 1", { type: QueryTypes.SELECT });
    const settingRows = await sequelize.query('SELECT * FROM sitesettings LIMIT 1', { type: QueryTypes.SELECT });

    challan.vendor = {
      id: challan.sub_contractors_id,
      name: challan.vendor_name,
      address: challan.vendor_address,
      gst_no: challan.vendor_gst_no
    };
    challan.job_challan_items = items.map(item => ({
      ...item,
      return_type: item.return_type || 'RawMaterial',
      total: item.total,
      item: {
        id: item.item_id,
        item_name: item.item_name || item.master_item_name,
        unit_name: item.unit_name
      }
    }));

    return {
      challan,
      source_db: dbName,
      site_details: siteRows[0] || null,
      sitesetting: settingRows[0] || null
    };
  }

  async getPdfDetail(dbName, id, senderDb) {
    const db = await getTenantSequelize(dbName);
    const linked = await db.query("SELECT DISTINCT database_name FROM sub_contractors WHERE database_name IS NOT NULL AND database_name != '' AND database_name != :dbName", {
      replacements:{dbName}, type:QueryTypes.SELECT
    });
    const sources = linked.map(row=>row.database_name).filter(name=>/^[a-zA-Z0-9_]+$/.test(name));
    if (senderDb && senderDb !== dbName) {
      if (!sources.includes(senderDb)) { const error=new Error('Sender company is not linked to this tenant'); error.statusCode=403; throw error; }
      return this.getDetail(senderDb,id);
    }
    try { return await this.getDetail(dbName,id); }
    catch (error) {
      if (error.message !== 'Job Challan not found') throw error;
      for (const source of sources) {
        try { return await this.getDetail(source,id); }
        catch (lookupError) { if (lookupError.message !== 'Job Challan not found') throw lookupError; }
      }
      throw error;
    }
  }

  // ─── DELETE (with JC Receive guard) ─────────────────────────────────────
  async remove(dbName, id) {
    const models    = await getTenantModels(dbName);
    const sequelize = await getTenantSequelize(dbName);
    const parsedId  = parseInt(id);
    if (!parsedId || isNaN(parsedId)) throw new Error('Invalid Job Challan ID');

    return await sequelize.transaction(async (t) => {
      const challan = await models.job_challans.findByPk(parsedId, { transaction: t });
      if (!challan) throw new Error('Job Challan not found.');

      const receiveCount = await models.job_challan_receives.count({
        where: { challan_id: parsedId }, transaction: t
      });
      if (receiveCount > 0) {
        throw new Error('This JC cannot be deleted because it has already been used in JC Receive.');
      }

      // Delete stock entries (store_type 2 = dispatch, matching CakePHP delete logic)
      await models.st_stock_register.destroy({
        where: {
          indent_id:  String(parsedId),
          store_type: { [Op.in]: ['2', '1'] }
        },
        transaction: t
      });

      await models.job_challan_items.destroy({ where: { challan_id: parsedId }, transaction: t });
      await models.job_challans.destroy({ where: { id: parsedId }, transaction: t });
    });
  }
}

module.exports = new JobChallanService();
