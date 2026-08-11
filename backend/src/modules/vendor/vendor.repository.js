const { QueryTypes } = require('sequelize');

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
      WHERE v.id = :id
    `;
    const rows = await dbPool.query(query, { replacements: { id }, type: QueryTypes.SELECT });
    return rows[0] || null;
  }

  async searchVendors(dbPool, searchKey) {
    const query = `
      SELECT id, name
      FROM vendors
      WHERE name LIKE :searchKey AND status = 'Y'
      ORDER BY name ASC
      LIMIT 20
    `;
    return await dbPool.query(query, { replacements: { searchKey: `%${searchKey}%` }, type: QueryTypes.SELECT });
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
        name = :name, address = :address, state_id = :state_id, contact_no = :contact_no, email = :email,
        vat_no = :vat_no, tin_no = :tin_no, tin_date = :tin_date, gst_number = :gst_number, pancard_number = :pancard_number,
        tds = :tds, description = :description, status = :status, contact_person = :contact_person, type = :type
      WHERE id = :id
    `;

    const params = {
      name, address: address || null, state_id: state_id || null, contact_no: contact_no || null, email: email || null,
      vat_no: vat_no || null, tin_no: tin_no || null, tin_date: tin_date || null, gst_number: gst_number || null, pancard_number: pancard_number || null,
      tds: tds || '0', description: description || null, status: status || 'Y', contact_person: contact_person || null, type: type || 'Vendor',
      id
    };

    await dbPool.query(query, { replacements: params, type: QueryTypes.UPDATE });
    return true; // QueryTypes.UPDATE doesn't always easily expose affectedRows across all dialects, but throws on failure.
  }
}

module.exports = new VendorRepository();
