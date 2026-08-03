class QuotationRepository {
  async findFiltered(dbPool, filters = {}) {
    let query = `
      SELECT
        q.id, q.quotation_id, q.vendor_id, q.is_award, q.delivery_date,
        q.acceptance_date, q.freight, q.payment_terms, q.remark,
        q.total_qty, q.total_tax, q.total_amt, q.is_revised, q.status,
        q.added_time, q.postatus,
        v.name as vendor_name, v.contact_no, v.email, v.gst_number
      FROM st_quotations q
      LEFT JOIN vendors v ON q.vendor_id = v.id
      WHERE 1=1
    `;
    const params = [];

    if (filters.quotation_id) {
      query += ` AND q.quotation_id LIKE ?`;
      params.push(`%${filters.quotation_id}%`);
    }
    if (filters.vendor_id) {
      query += ` AND q.vendor_id = ?`;
      params.push(filters.vendor_id);
    }
    if (filters.status) {
      query += ` AND q.status = ?`;
      params.push(filters.status);
    }
    if (filters.is_award !== undefined) {
      query += ` AND q.is_award = ?`;
      params.push(filters.is_award);
    }
    if (filters.datefrom) {
      query += ` AND DATE(q.added_time) >= ?`;
      params.push(filters.datefrom);
    }
    if (filters.dateto) {
      query += ` AND DATE(q.added_time) <= ?`;
      params.push(filters.dateto);
    }

    query += ` ORDER BY q.id DESC`;
    const [rows] = await dbPool.execute(query, params);
    return rows;
  }

  async findById(dbPool, id) {
    const [rows] = await dbPool.execute(
      `SELECT q.*, v.name as vendor_name, v.contact_no, v.email, v.gst_number, v.address, v.contact_person
       FROM st_quotations q
       LEFT JOIN vendors v ON q.vendor_id = v.id
       WHERE q.id = ? LIMIT 1`,
      [id]
    );
    return rows[0] || null;
  }

  async findDetails(dbPool, quotationDbId) {
    const [rows] = await dbPool.execute(
      `SELECT qd.*, i.item_name, i.item_description, u.unit_name as uom_name
       FROM st_quotations_details qd
       LEFT JOIN st_additem i ON qd.item_id = i.id
       LEFT JOIN st_measurementunits u ON qd.uom = u.id
       WHERE qd.quotation_id = ?
       ORDER BY qd.id ASC`,
      [quotationDbId]
    );
    return rows;
  }

  async getVendors(dbPool) {
    const [rows] = await dbPool.execute(
      `SELECT id, name FROM vendors WHERE status = 'Y' ORDER BY name ASC`
    );
    return rows;
  }
}

module.exports = new QuotationRepository();
