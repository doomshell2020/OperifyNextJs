const { QueryTypes } = require('sequelize');

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
    const params = {};

    if (filters.vendor_id) {
      query += ` AND p.vendor_id = :vendor_id`;
      params.vendor_id = filters.vendor_id;
    }
    if (filters.bill_no) {
      query += ` AND p.bill_no LIKE :bill_no`;
      params.bill_no = `%${filters.bill_no}%`;
    }
    if (filters.status) {
      query += ` AND p.status = :status`;
      params.status = filters.status;
    }
    if (filters.datefrom) {
      query += ` AND DATE(p.inwarddate) >= :datefrom`;
      params.datefrom = filters.datefrom;
    }
    if (filters.dateto) {
      query += ` AND DATE(p.inwarddate) <= :dateto`;
      params.dateto = filters.dateto;
    }

    query += ` ORDER BY p.id DESC`;
    return await dbPool.query(query, { replacements: params, type: QueryTypes.SELECT });
  }

  async findById(dbPool, id) {
    const rows = await dbPool.query(
      `SELECT p.*, v.name as vendor_name, v.contact_no, v.email, v.gst_number, v.address
       FROM payments p
       LEFT JOIN vendors v ON p.vendor_id = v.id
       WHERE p.id = :id LIMIT 1`,
      { replacements: { id }, type: QueryTypes.SELECT }
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
    const params = {};

    if (filters.po_no) {
      query += ` AND po_no LIKE :po_no`;
      params.po_no = `%${filters.po_no}%`;
    }
    if (filters.status) {
      query += ` AND status = :status`;
      params.status = filters.status;
    }
    if (filters.datefrom) {
      query += ` AND DATE(datefrom) >= :datefrom`;
      params.datefrom = filters.datefrom;
    }
    if (filters.dateto) {
      query += ` AND DATE(datefrom) <= :dateto`;
      params.dateto = filters.dateto;
    }

    query += ` ORDER BY id DESC`;
    return await dbPool.query(query, { replacements: params, type: QueryTypes.SELECT });
  }

  async getVendors(dbPool) {
    return await dbPool.query(
      `SELECT id, name FROM vendors WHERE status = 'Y' ORDER BY name ASC`,
      { type: QueryTypes.SELECT }
    );
  }
}

module.exports = new PaymentRepository();
