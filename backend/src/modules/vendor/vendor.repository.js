class VendorRepository {
  async getVendorById(dbPool, id) {
    const query = `
      SELECT 
        v.id,
        v.name,
        v.address,
        v.state_id,
        v.contact_no,
        v.email,
        v.vat_no,
        v.tin_no,
        DATE(v.tin_date) as tin_date,
        v.gst_number,
        v.pancard_number,
        v.tds,
        v.description,
        v.status,
        v.contact_person,
        v.type
      FROM vendors v
      WHERE v.id = ?
    `;
    const [rows] = await dbPool.execute(query, [id]);
    return rows[0] || null;
  }

  async searchVendors(dbPool, searchKey) {
    const query = `
      SELECT id, name
      FROM vendors
      WHERE name LIKE ? AND status = 'Y'
      ORDER BY name ASC
      LIMIT 20
    `;
    const [rows] = await dbPool.execute(query, [`%${searchKey}%`]);
    return rows;
  }

  async updateVendor(dbPool, id, vendorData) {
    const {
      name, address, state_id, contact_no, email,
      vat_no, tin_no, tin_date, gst_number, pancard_number,
      tds, description, status, contact_person, type
    } = vendorData;

    const query = `
      UPDATE vendors 
      SET 
        name = ?, address = ?, state_id = ?, contact_no = ?, email = ?,
        vat_no = ?, tin_no = ?, tin_date = ?, gst_number = ?, pancard_number = ?,
        tds = ?, description = ?, status = ?, contact_person = ?, type = ?
      WHERE id = ?
    `;

    const params = [
      name, address || null, state_id || null, contact_no || null, email || null,
      vat_no || null, tin_no || null, tin_date || null, gst_number || null, pancard_number || null,
      tds || '0', description || null, status || 'Y', contact_person || null, type || 'Vendor',
      id
    ];

    const [result] = await dbPool.execute(query, params);
    return result.affectedRows > 0;
  }
}

module.exports = new VendorRepository();
