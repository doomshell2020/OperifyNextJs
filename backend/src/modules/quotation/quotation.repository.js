const { QueryTypes } = require('sequelize');

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
    const params = {};

    if (filters.quotation_id) {
      query += ` AND q.quotation_id LIKE :quotation_id`;
      params.quotation_id = `%${filters.quotation_id}%`;
    }
    if (filters.vendor_id) {
      query += ` AND q.vendor_id = :vendor_id`;
      params.vendor_id = filters.vendor_id;
    }
    if (filters.status) {
      query += ` AND q.status = :status`;
      params.status = filters.status;
    }
    if (filters.is_award !== undefined) {
      query += ` AND q.is_award = :is_award`;
      params.is_award = filters.is_award;
    }
    if (filters.datefrom) {
      query += ` AND DATE(q.added_time) >= :datefrom`;
      params.datefrom = filters.datefrom;
    }
    if (filters.dateto) {
      query += ` AND DATE(q.added_time) <= :dateto`;
      params.dateto = filters.dateto;
    }

    query += ` ORDER BY q.id DESC`;
    return await dbPool.query(query, { replacements: params, type: QueryTypes.SELECT });
  }

  async findById(dbPool, id) {
    const rows = await dbPool.query(
      `SELECT q.*, v.name as vendor_name, v.contact_no, v.email, v.gst_number, v.address, v.contact_person
       FROM st_quotations q
       LEFT JOIN vendors v ON q.vendor_id = v.id
       WHERE q.id = :id LIMIT 1`,
      { replacements: { id }, type: QueryTypes.SELECT }
    );
    return rows[0] || null;
  }

  async findDetails(dbPool, quotationDbId) {
    return await dbPool.query(
      `SELECT qd.*, i.item_name, i.item_description, u.unit_name as uom_name
       FROM st_quotations_details qd
       LEFT JOIN st_additem i ON qd.item_id = i.id
       LEFT JOIN st_measurementunits u ON qd.uom = u.id
       WHERE qd.quotation_id = :quotationDbId
       ORDER BY qd.id ASC`,
      { replacements: { quotationDbId }, type: QueryTypes.SELECT }
    );
  }

  async getVendors(dbPool) {
    return await dbPool.query(
      `SELECT id, name FROM vendors WHERE status = 'Y' ORDER BY name ASC`,
      { type: QueryTypes.SELECT }
    );
  }
}

module.exports = new QuotationRepository();
