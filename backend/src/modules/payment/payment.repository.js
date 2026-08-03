class PaymentRepository {
  async findFiltered(dbPool, filters = {}) {
    let query = `
      SELECT 
        p.id, p.vendor_id, p.store_type, p.inwarddate, p.bill_no, p.receipt_no,
        p.bill_date, p.total_amt, p.remark, p.created_date, p.pay_date, p.goods_id, p.status,
        v.name as vendor_name, v.contact_no, v.gst_number
      FROM payments p
      LEFT JOIN vendors v ON p.vendor_id = v.id
      WHERE 1=1
    `;
    const params = [];

    if (filters.vendor_id) {
      query += ` AND p.vendor_id = ?`;
      params.push(filters.vendor_id);
    }
    if (filters.bill_no) {
      query += ` AND p.bill_no LIKE ?`;
      params.push(`%${filters.bill_no}%`);
    }
    if (filters.status) {
      query += ` AND p.status = ?`;
      params.push(filters.status);
    }
    if (filters.datefrom) {
      query += ` AND DATE(p.inwarddate) >= ?`;
      params.push(filters.datefrom);
    }
    if (filters.dateto) {
      query += ` AND DATE(p.inwarddate) <= ?`;
      params.push(filters.dateto);
    }

    query += ` ORDER BY p.id DESC`;
    const [rows] = await dbPool.execute(query, params);
    return rows;
  }

  async findById(dbPool, id) {
    const [rows] = await dbPool.execute(
      `SELECT p.*, v.name as vendor_name, v.contact_no, v.email, v.gst_number, v.address
       FROM payments p
       LEFT JOIN vendors v ON p.vendor_id = v.id
       WHERE p.id = ? LIMIT 1`,
      [id]
    );
    return rows[0] || null;
  }

  async findParticularPayments(dbPool, filters = {}) {
    let query = `
      SELECT id, particular, consignee, po_no, invoice, due_period,
             datefrom, bill_dis_date, amount, created, modified, status
      FROM particular_payments
      WHERE 1=1
    `;
    const params = [];

    if (filters.po_no) {
      query += ` AND po_no LIKE ?`;
      params.push(`%${filters.po_no}%`);
    }
    if (filters.status) {
      query += ` AND status = ?`;
      params.push(filters.status);
    }
    if (filters.datefrom) {
      query += ` AND DATE(datefrom) >= ?`;
      params.push(filters.datefrom);
    }
    if (filters.dateto) {
      query += ` AND DATE(datefrom) <= ?`;
      params.push(filters.dateto);
    }

    query += ` ORDER BY id DESC`;
    const [rows] = await dbPool.execute(query, params);
    return rows;
  }

  async getVendors(dbPool) {
    const [rows] = await dbPool.execute(
      `SELECT id, name FROM vendors WHERE status = 'Y' ORDER BY name ASC`
    );
    return rows;
  }
}

module.exports = new PaymentRepository();
