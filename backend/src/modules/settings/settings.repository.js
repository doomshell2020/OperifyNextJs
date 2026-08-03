class SettingsRepository {
  // ─── CATEGORIES ─────────────────────────────────────────────────────────────
  async getCategories(dbPool, { search } = {}) {
    let q = `SELECT id, category_name, description, status, added_time, updated_time FROM st_categorymaster WHERE 1=1`;
    const p = [];
    if (search) { q += ` AND category_name LIKE ?`; p.push(`%${search}%`); }
    q += ` ORDER BY category_name ASC`;
    const [rows] = await dbPool.execute(q, p);
    return rows;
  }
  async getCategoryById(dbPool, id) {
    const [rows] = await dbPool.execute(`SELECT * FROM st_categorymaster WHERE id = ? LIMIT 1`, [id]);
    return rows[0] || null;
  }
  async createCategory(dbPool, { category_name, description }) {
    const now = new Date();
    const [result] = await dbPool.execute(
      `INSERT INTO st_categorymaster (category_name, description, status, added_time) VALUES (?, ?, 'Y', ?)`,
      [category_name, description || '', now]
    );
    return result.insertId;
  }
  async updateCategory(dbPool, id, { category_name, description }) {
    await dbPool.execute(
      `UPDATE st_categorymaster SET category_name = ?, description = ?, updated_time = ? WHERE id = ?`,
      [category_name, description || '', new Date(), id]
    );
  }
  async toggleCategoryStatus(dbPool, id, status) {
    await dbPool.execute(`UPDATE st_categorymaster SET status = ? WHERE id = ?`, [status, id]);
  }
  async deleteCategory(dbPool, id) {
    await dbPool.execute(`DELETE FROM st_categorymaster WHERE id = ?`, [id]);
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
    const p = [];
    if (search) { q += ` AND i.item_name LIKE ?`; p.push(`%${search}%`); }
    if (category_id) { q += ` AND i.category_id = ?`; p.push(category_id); }
    if (status) { q += ` AND i.status = ?`; p.push(status); }
    if (itemtype) { q += ` AND i.itemtype = ?`; p.push(itemtype); }
    q += ` ORDER BY i.item_name ASC`;
    const [rows] = await dbPool.execute(q, p);
    return rows;
  }
  async getProductById(dbPool, id) {
    const [rows] = await dbPool.execute(
      `SELECT i.*, c.category_name, u.unit_name as uom_name
       FROM st_additem i
       LEFT JOIN st_categorymaster c ON i.category_id = c.id
       LEFT JOIN st_measurementunits u ON i.uom = u.id
       WHERE i.id = ? LIMIT 1`,
      [id]
    );
    return rows[0] || null;
  }
  async toggleProductStatus(dbPool, id, status) {
    await dbPool.execute(`UPDATE st_additem SET status = ? WHERE id = ?`, [status, id]);
  }

  // ─── SUPPLIERS (vendors) ────────────────────────────────────────────────────
  async getSuppliers(dbPool, { search, status, type } = {}) {
    let q = `SELECT id, name, address, contact_no, email, gst_number, pancard_number,
                    tin_no, tds, contact_person, type, status, created_date
             FROM vendors WHERE 1=1`;
    const p = [];
    if (search) { q += ` AND (name LIKE ? OR contact_person LIKE ? OR gst_number LIKE ?)`; p.push(`%${search}%`, `%${search}%`, `%${search}%`); }
    if (status) { q += ` AND status = ?`; p.push(status); }
    if (type) { q += ` AND type = ?`; p.push(type); }
    q += ` ORDER BY id ASC`;
    const [rows] = await dbPool.execute(q, p);
    return rows;
  }
  async getSupplierById(dbPool, id) {
    const [rows] = await dbPool.execute(`SELECT * FROM vendors WHERE id = ? LIMIT 1`, [id]);
    return rows[0] || null;
  }
  async createSupplier(dbPool, data) {
    const { name, address, contact_no, email, gst_number, pancard_number, tin_no, tds, contact_person, type, description } = data;
    const [result] = await dbPool.execute(
      `INSERT INTO vendors (name, address, contact_no, email, gst_number, pancard_number, tin_no, tds, contact_person, type, description, status, created_date)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Y', ?)`,
      [name, address || '', contact_no || '', email || '', gst_number || '', pancard_number || '', tin_no || '', tds || 0, contact_person || '', type || 'Vendor', description || '', new Date()]
    );
    return result.insertId;
  }
  async updateSupplier(dbPool, id, data) {
    const { name, address, contact_no, email, gst_number, pancard_number, tin_no, tds, contact_person, type, description } = data;
    await dbPool.execute(
      `UPDATE vendors SET name=?, address=?, contact_no=?, email=?, gst_number=?, pancard_number=?, tin_no=?, tds=?, contact_person=?, type=?, description=? WHERE id=?`,
      [name, address || '', contact_no || '', email || '', gst_number || '', pancard_number || '', tin_no || '', tds || 0, contact_person || '', type || 'Vendor', description || '', id]
    );
  }
  async toggleSupplierStatus(dbPool, id, status) {
    await dbPool.execute(`UPDATE vendors SET status = ? WHERE id = ?`, [status, id]);
  }

  // ─── USERS ──────────────────────────────────────────────────────────────────
  async getUsers(dbPool, { search, is_status } = {}) {
    let q = `SELECT id, user_name, email, mobile, role_id, db, is_admin, is_status, created, last_login FROM users WHERE 1=1`;
    const p = [];
    if (search) { q += ` AND (user_name LIKE ? OR email LIKE ? OR mobile LIKE ?)`; p.push(`%${search}%`, `%${search}%`, `%${search}%`); }
    if (is_status) { q += ` AND is_status = ?`; p.push(is_status); }
    q += ` ORDER BY id ASC`;
    const [rows] = await dbPool.execute(q, p);
    return rows;
  }
  async getUserById(dbPool, id) {
    const [rows] = await dbPool.execute(
      `SELECT id, user_name, email, mobile, role_id, db, is_admin, is_status, created, last_login FROM users WHERE id = ? LIMIT 1`,
      [id]
    );
    return rows[0] || null;
  }
  async toggleUserStatus(dbPool, id, status) {
    await dbPool.execute(`UPDATE users SET is_status = ? WHERE id = ?`, [status, id]);
  }

  // ─── HELPERS ────────────────────────────────────────────────────────────────
  async getCategoryList(dbPool) {
    const [rows] = await dbPool.execute(`SELECT id, category_name FROM st_categorymaster WHERE status='Y' ORDER BY category_name ASC`);
    return rows;
  }
  async getUomList(dbPool) {
    const [rows] = await dbPool.execute(`SELECT id, unit_name FROM st_measurementunits ORDER BY unit_name ASC`);
    return rows;
  }
}

module.exports = new SettingsRepository();
