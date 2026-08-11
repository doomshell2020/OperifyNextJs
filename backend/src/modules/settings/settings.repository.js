const { QueryTypes } = require('sequelize');

class SettingsRepository {
  // ─── CATEGORIES ─────────────────────────────────────────────────────────────
  async getCategories(dbPool, { search } = {}) {
    let q = `SELECT id, category_name, description, status, added_time, updated_time FROM st_categorymaster WHERE 1=1`;
    const p = {};
    if (search) { q += ` AND category_name LIKE :search`; p.search = `%${search}%`; }
    q += ` ORDER BY category_name ASC`;
    return await dbPool.query(q, { replacements: p, type: QueryTypes.SELECT });
  }
  async getCategoryById(dbPool, id) {
    const rows = await dbPool.query(`SELECT * FROM st_categorymaster WHERE id = :id LIMIT 1`, { replacements: { id }, type: QueryTypes.SELECT });
    return rows[0] || null;
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
    await dbPool.query(`DELETE FROM st_categorymaster WHERE id = :id`, { replacements: { id }, type: QueryTypes.DELETE });
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
      `SELECT i.*, c.category_name, u.unit_name as uom_name
       FROM st_additem i
       LEFT JOIN st_categorymaster c ON i.category_id = c.id
       LEFT JOIN st_measurementunits u ON i.uom = u.id
       WHERE i.id = :id LIMIT 1`,
      { replacements: { id }, type: QueryTypes.SELECT }
    );
    return rows[0] || null;
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

  // ─── HELPERS ────────────────────────────────────────────────────────────────
  async getCategoryList(dbPool) {
    return await dbPool.query(`SELECT id, category_name FROM st_categorymaster WHERE status='Y' ORDER BY category_name ASC`, { type: QueryTypes.SELECT });
  }
  async getUomList(dbPool) {
    return await dbPool.query(`SELECT id, unit_name FROM st_measurementunits ORDER BY unit_name ASC`, { type: QueryTypes.SELECT });
  }
}

module.exports = new SettingsRepository();
