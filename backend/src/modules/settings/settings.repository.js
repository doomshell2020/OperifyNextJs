const { QueryTypes } = require('sequelize');
const { invalid } = require('../../utils/receiptValidation');

class SettingsRepository {
  // ─── CATEGORIES ─────────────────────────────────────────────────────────────
  async getCategories(dbPool, { search } = {}) {
    let q = `SELECT id, category_name, description, status, is_print, added_time, updated_time FROM st_categorymaster WHERE 1=1`;
    const p = {};
    if (!search) q += ` AND status='Y'`;
    if (search) { q += ` AND category_name LIKE :search`; p.search = `%${search}%`; }
    q += ` ORDER BY category_name ASC`;
    return await dbPool.query(q, { replacements: p, type: QueryTypes.SELECT });
  }
  async getCategoryById(dbPool, id) {
    const rows = await dbPool.query(`SELECT * FROM st_categorymaster WHERE id = :id LIMIT 1`, { replacements: { id }, type: QueryTypes.SELECT });
    return rows[0] || null;
  }
  async checkCategoryExists(dbPool, category_name, id = 0) {
    const rows = await dbPool.query('SELECT id FROM st_categorymaster WHERE category_name=:name AND id!=:id LIMIT 1', { replacements: { name: category_name.trim(), id }, type: QueryTypes.SELECT });
    return rows.length > 0;
  }
  async createCategory(dbPool, { category_name, description }) {
    const now = new Date();
    const result = await dbPool.query(
      `INSERT INTO st_categorymaster (category_name, description, status, added_time) VALUES (:category_name, :description, 'Y', :now)`,
      { replacements: { category_name, description: description || '', now }, type: QueryTypes.INSERT }
    );
    return result[0];
  }
  async updateCategory(dbPool, id, { category_name, description }) {
    await dbPool.query(
      `UPDATE st_categorymaster SET category_name = :category_name, description = :description, updated_time = :updated_time WHERE id = :id`,
      { replacements: { category_name, description: description || '', updated_time: new Date(), id }, type: QueryTypes.UPDATE }
    );
  }
  async toggleCategoryStatus(dbPool, id, status) {
    await dbPool.query(`UPDATE st_categorymaster SET status = :status WHERE id = :id`, { replacements: { status, id }, type: QueryTypes.UPDATE });
  }
  async deleteCategory(dbPool, id) {
    await dbPool.query(`UPDATE st_categorymaster SET status='N' WHERE id = :id`, { replacements: { id }, type: QueryTypes.UPDATE });
  }

  // ─── PRODUCTS (st_additem) ───────────────────────────────────────────────────
  async getProducts(dbPool, { search, category_id, status, itemtype } = {}) {
    let q = `
      SELECT i.id, i.item_name, i.category_id, i.uom, i.tax, i.itemtype,
             i.cost_price, i.sale_price, i.min_order_qty, i.status,
             i.added_time, i.updated_time,
             c.category_name, u.unit_name as uom_name
      FROM st_additem i
      LEFT JOIN st_categorymaster c ON i.category_id = c.id
      LEFT JOIN st_measurementunits u ON i.uom = u.id
      WHERE 1=1`;
    const p = {};
    if (search) { q += ` AND i.item_name LIKE :search`; p.search = `%${search}%`; }
    if (category_id) { q += ` AND i.category_id = :category_id`; p.category_id = category_id; }
    if (status) { q += ` AND i.status = :status`; p.status = status; }
    if (itemtype) { q += ` AND i.itemtype = :itemtype`; p.itemtype = itemtype; }
    q += ` ORDER BY i.item_name ASC`;
    return await dbPool.query(q, { replacements: p, type: QueryTypes.SELECT });
  }
  async getProductById(dbPool, id) {
    const rows = await dbPool.query(
      `SELECT i.*, c.category_name, u.unit_name as uom_name,
        (SELECT COUNT(*) FROM designsheetdetails d WHERE d.item_id=i.id) AS designsheet_count
       FROM st_additem i
       LEFT JOIN st_categorymaster c ON i.category_id = c.id
       LEFT JOIN st_measurementunits u ON i.uom = u.id
       WHERE i.id = :id LIMIT 1`,
      { replacements: { id }, type: QueryTypes.SELECT }
    );
    return rows[0] || null;
  }
  async checkItemExists(dbPool, item_name) {
    const rows = await dbPool.query(
      `SELECT id FROM st_additem WHERE item_name = :item_name LIMIT 1`,
      { replacements: { item_name: item_name.trim().toUpperCase() }, type: QueryTypes.SELECT }
    );
    return rows.length > 0;
  }
  async createProduct(dbPool, item) {
    const result = await dbPool.query(
      `INSERT INTO st_additem (
         item_name, category_id, uom, item_isbn, sale_price, discount, weight, volume, min_order_qty,
         itemtype, finishedprocess_id, productprocess_id, cname, tax, size_id, location_name, status, added_time
       ) VALUES (
         :item_name, :category_id, :uom, :item_isbn, :sale_price, :discount, :weight, :volume, :min_order_qty,
         :itemtype, :finishedprocess_id, :productprocess_id, :cname, :tax, :size_id, :location_name, 'Y', :added_time
       )`,
      {
        replacements: {
          item_name: item.item_name.trim().toUpperCase(),
          category_id: item.category_id || null,
          uom: item.uom || null,
          item_isbn: item.item_isbn || null,
          sale_price: item.sale_price || 0,
          discount: item.discount || 0,
          weight: item.weight || null,
          volume: item.volume || null,
          min_order_qty: item.min_order_qty || null,
          itemtype: item.itemtype || 'RawMaterial',
          finishedprocess_id: item.finishedprocess_id || null,
          productprocess_id: item.productprocess_id || null,
          cname: item.cname || null,
          tax: item.tax || null,
          size_id: item.size_id || item.size || null,
          location_name: item.location_name || null,
          added_time: new Date()
        },
        type: QueryTypes.INSERT
      }
    );
    return result[0];
  }
  async checkItemExistsForEdit(dbPool, item_name, id) {
    const rows = await dbPool.query(
      `SELECT id FROM st_additem WHERE item_name = :item_name AND id != :id LIMIT 1`,
      { replacements: { item_name: item_name.trim().toUpperCase(), id }, type: QueryTypes.SELECT }
    );
    return rows.length > 0;
  }
  async updateProduct(dbPool, id, item) {
    if (Object.hasOwn(item,'category_id')) {
      const existing=await this.getProductById(dbPool,id);
      if (!existing) throw invalid('Product not found.',404);
      if (Number(existing.designsheet_count)>0 && String(item.category_id || '')!==String(existing.category_id || '')) {
        throw invalid('Category cannot be changed because this item is used in a design sheet.',409);
      }
    }
    const fields = ['item_name','category_id','uom','item_isbn','sale_price','discount','size_id','location_name','cname','tax','itemtype','finishedprocess_id','productprocess_id'];
    const changes = {};
    for (const field of fields) if (Object.hasOwn(item, field)) changes[field] = item[field] === '' ? null : item[field];
    changes.item_name = item.item_name.trim().toUpperCase();
    if (Array.isArray(changes.productprocess_id)) changes.productprocess_id = changes.productprocess_id.join(',');
    const assignments = Object.keys(changes).map(field => `${field}=:${field}`).join(',');
    await dbPool.query(`UPDATE st_additem SET ${assignments},updated_time=:updated_time WHERE id=:id`, {
      replacements: { ...changes, id, updated_time: new Date() }, type: QueryTypes.UPDATE
    });
  }

  async deleteProduct(dbPool, id) {
    return dbPool.transaction(async transaction => {
      const products = await dbPool.query('SELECT id FROM st_additem WHERE id=:id FOR UPDATE', { replacements: { id }, type: QueryTypes.SELECT, transaction });
      if (!products.length) throw invalid('Product not found.', 404);
      const [used] = await dbPool.query(`SELECT
        (SELECT COUNT(*) FROM st_purchaseorderdetails WHERE item_id=:id) +
        (SELECT COUNT(*) FROM bom_finisedproduct WHERE product_id=:id) +
        (SELECT COUNT(*) FROM designsheetdetails WHERE item_id=:id) AS total`, { replacements: { id }, type: QueryTypes.SELECT, transaction });
      if (Number(used.total)>0) throw invalid('This item cannot be deleted because it is already used in the system.',409);
      await dbPool.query('DELETE FROM st_additem WHERE id=:id', { replacements:{id},type:QueryTypes.DELETE,transaction });
    });
  }

  async toggleProductStatus(dbPool, id, status) {
    await dbPool.query(`UPDATE st_additem SET status = :status WHERE id = :id`, { replacements: { status, id }, type: QueryTypes.UPDATE });
  }

  // ─── TAXES ──────────────────────────────────────────────────────────────────
  async getTaxes(dbPool) {
    return await dbPool.query(`SELECT id, tax FROM st_taxmaster WHERE status = 'Y' AND parent = '0' ORDER BY id ASC`, { type: QueryTypes.SELECT });
  }

  // ─── SUPPLIERS (vendors) ────────────────────────────────────────────────────
  async getSuppliers(dbPool, { search, status, type } = {}) {
    let q = `SELECT id, name, address, contact_no, email, gst_number, pancard_number,
                    tin_no, tds, contact_person, type, status, created_date
             FROM vendors WHERE 1=1`;
    const p = {};
    if (search) { q += ` AND (name LIKE :search OR contact_person LIKE :search OR gst_number LIKE :search)`; p.search = `%${search}%`; }
    if (status) { q += ` AND status = :status`; p.status = status; }
    if (type) { q += ` AND type = :type`; p.type = type; }
    q += ` ORDER BY id ASC`;
    return await dbPool.query(q, { replacements: p, type: QueryTypes.SELECT });
  }
  async getSupplierById(dbPool, id) {
    const rows = await dbPool.query(`SELECT * FROM vendors WHERE id = :id LIMIT 1`, { replacements: { id }, type: QueryTypes.SELECT });
    return rows[0] || null;
  }
  async createSupplier(dbPool, data) {
    const { name, address, contact_no, email, gst_number, pancard_number, tin_no, tds, contact_person, type, description } = data;
    const result = await dbPool.query(
      `INSERT INTO vendors (name, address, contact_no, email, gst_number, pancard_number, tin_no, tds, contact_person, type, description, status, created_date)
       VALUES (:name, :address, :contact_no, :email, :gst_number, :pancard_number, :tin_no, :tds, :contact_person, :type, :description, 'Y', :now)`,
      {
        replacements: {
          name, address: address || '', contact_no: contact_no || '', email: email || '', gst_number: gst_number || '',
          pancard_number: pancard_number || '', tin_no: tin_no || '', tds: tds || 0, contact_person: contact_person || '',
          type: type || 'Vendor', description: description || '', now: new Date()
        },
        type: QueryTypes.INSERT
      }
    );
    return result[0];
  }
  async updateSupplier(dbPool, id, data) {
    const { name, address, contact_no, email, gst_number, pancard_number, tin_no, tds, contact_person, type, description } = data;
    await dbPool.query(
      `UPDATE vendors SET name=:name, address=:address, contact_no=:contact_no, email=:email, gst_number=:gst_number, pancard_number=:pancard_number, tin_no=:tin_no, tds=:tds, contact_person=:contact_person, type=:type, description=:description WHERE id=:id`,
      {
        replacements: {
          name, address: address || '', contact_no: contact_no || '', email: email || '', gst_number: gst_number || '',
          pancard_number: pancard_number || '', tin_no: tin_no || '', tds: tds || 0, contact_person: contact_person || '',
          type: type || 'Vendor', description: description || '', id
        },
        type: QueryTypes.UPDATE
      }
    );
  }
  async toggleSupplierStatus(dbPool, id, status) {
    await dbPool.query(`UPDATE vendors SET status = :status WHERE id = :id`, { replacements: { status, id }, type: QueryTypes.UPDATE });
  }

  // ─── USERS ──────────────────────────────────────────────────────────────────
  async getUsers(dbPool, { search, is_status } = {}) {
    let q = `SELECT id, user_name, email, mobile, role_id, db, is_admin, is_status, created, last_login FROM users WHERE 1=1`;
    const p = {};
    if (search) { q += ` AND (user_name LIKE :search OR email LIKE :search OR mobile LIKE :search)`; p.search = `%${search}%`; }
    if (is_status) { q += ` AND is_status = :is_status`; p.is_status = is_status; }
    q += ` ORDER BY id ASC`;
    return await dbPool.query(q, { replacements: p, type: QueryTypes.SELECT });
  }
  async getUserById(dbPool, id) {
    const rows = await dbPool.query(
      `SELECT id, user_name, email, mobile, role_id, db, is_admin, is_status, created, last_login FROM users WHERE id = :id LIMIT 1`,
      { replacements: { id }, type: QueryTypes.SELECT }
    );
    return rows[0] || null;
  }
  async toggleUserStatus(dbPool, id, status) {
    await dbPool.query(`UPDATE users SET is_status = :status WHERE id = :id`, { replacements: { status, id }, type: QueryTypes.UPDATE });
  }
  async toggleUserStatusCentral(centralPool, mobile, status) {
    await centralPool.query(`UPDATE users SET is_status = :status WHERE mobile = :mobile`, { replacements: { status, mobile }, type: QueryTypes.UPDATE });
  }
  async getUserByMobile(dbPool, mobile) {
    const rows = await dbPool.query(`SELECT id FROM users WHERE mobile = :mobile LIMIT 1`, { replacements: { mobile }, type: QueryTypes.SELECT });
    return rows[0] || null;
  }
  async createUser(dbPool, data) {
    const { user_name, email, mobile, password, role_id, c_id, academic_year, db, board, is_admin, confirm_pass } = data;
    const now = new Date();
    const replacements = {
      user_name, c_id: c_id || 1, academic_year: academic_year || '', email: email || '', 
      password: password || '', confirm_pass: confirm_pass || '', now, 
      role_id: role_id || null, db: db || '', board: board || '', mobile: mobile || '', is_admin: is_admin || 'N'
    };
    const result = await dbPool.query(
      `INSERT INTO users (user_name, c_id, academic_year, email, password, confirm_pass, created, role_id, db, board, mobile, is_admin, is_status)
       VALUES (:user_name, :c_id, :academic_year, :email, :password, :confirm_pass, :now, :role_id, :db, :board, :mobile, :is_admin, 'Y')`,
      { replacements, type: QueryTypes.INSERT }
    );
    return result[0];
  }
  
  async createUserCentral(centralPool, data) {
    const { user_name, email, mobile, password, role_id, c_id, academic_year, db, board, is_admin, confirm_pass } = data;
    const now = new Date();
    const replacements = {
      user_name, c_id: c_id || 1, academic_year: academic_year || '', email: email || '', 
      password: password || '', confirm_pass: confirm_pass || '', now, 
      role_id: role_id || null, db: db || '', board: board || '', mobile: mobile || '', is_admin: is_admin || 'N', tech_id: 0
    };
    await centralPool.query(
      `INSERT INTO users (user_name, c_id, academic_year, email, tech_id, password, confirm_pass, created, role_id, db, board, mobile)
       VALUES (:user_name, :c_id, :academic_year, :email, :tech_id, :password, :confirm_pass, :now, :role_id, :db, :board, :mobile)`,
      { replacements, type: QueryTypes.INSERT }
    );
  }
  
  async updateUser(dbPool, id, data) {
    let q = `UPDATE users SET user_name = :user_name, email = :email, mobile = :mobile, role_id = :role_id`;
    if (data.password) {
      q += `, password = :password, confirm_pass = :confirm_pass`;
    }
    q += ` WHERE id = :id`;
    await dbPool.query(q, {
      replacements: { ...data, id },
      type: QueryTypes.UPDATE
    });
  }

  async updateUserCentral(centralPool, oldMobile, data) {
    let q = `UPDATE users SET user_name = :user_name, email = :email, mobile = :new_mobile, role_id = :role_id`;
    if (data.password) {
      q += `, password = :password, confirm_pass = :confirm_pass`;
    }
    q += ` WHERE mobile = :old_mobile`;
    await centralPool.query(q, {
      replacements: { ...data, old_mobile: oldMobile, new_mobile: data.mobile },
      type: QueryTypes.UPDATE
    });
  }

  async deleteUser(dbPool, id) {
    await dbPool.query(`DELETE FROM users WHERE id = :id`, { replacements: { id }, type: QueryTypes.DELETE });
  }

  async deleteUserCentral(centralPool, mobile) {
    // We delete central DB user by mobile to keep sync
    await centralPool.query(`DELETE FROM users WHERE mobile = :mobile`, { replacements: { mobile }, type: QueryTypes.DELETE });
  }

  // ─── ROLES ──────────────────────────────────────────────────────────────────
  async getRoles(dbPool) {
    // Return all roles so the frontend can display correct names in the table
    // The frontend dropdown will exclude 1, 6, 101, 105 as per the old CakePHP project logic
    return await dbPool.query(
      `SELECT id, name, status, created FROM roles ORDER BY name ASC`,
      { type: QueryTypes.SELECT }
    );
  }

  // ─── HELPERS ────────────────────────────────────────────────────────────────
  async getCategoryList(dbPool) {
    return await dbPool.query(`SELECT id, category_name FROM st_categorymaster WHERE status='Y' ORDER BY category_name ASC`, { type: QueryTypes.SELECT });
  }
  async getFinishedProcessList(dbPool) {
    return await dbPool.query(`SELECT id, process_name FROM finishedproduct_process ORDER BY id ASC`, { type: QueryTypes.SELECT });
  }
  async getUomList(dbPool) {
    return await dbPool.query(`SELECT id, unit_name FROM st_measurementunits ORDER BY unit_name ASC`, { type: QueryTypes.SELECT });
  }
}

module.exports = new SettingsRepository();
