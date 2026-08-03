const db = require('../../config/db');

class EmdRepository {
  async findFiltered(dbPool, filters = {}) {
    let query = `
      SELECT 
        id, bg_for, datefrom, bankguaranteeno, favour_of, po_no, amount,
        validupto, extenstionupto, lastdate, relese_date, po_or_rma,
        contect_per, board_name, currency_type, claim_upto, status, invoice_file,
        created, updated
      FROM emd_guarantees
      WHERE 1=1
    `;
    const params = [];

    if (filters.bg_for) {
      query += ` AND bg_for = ?`;
      params.push(filters.bg_for);
    }
    if (filters.bankguaranteeno) {
      query += ` AND bankguaranteeno LIKE ?`;
      params.push(`%${filters.bankguaranteeno}%`);
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
    if (filters.due_from && filters.due_to) {
      query += ` AND (
        (DATE(validupto) >= ? AND DATE(validupto) <= ?) OR
        (DATE(claim_upto) >= ? AND DATE(claim_upto) <= ?) OR
        (DATE(extenstionupto) >= ? AND DATE(extenstionupto) <= ?)
      )`;
      params.push(filters.due_from, filters.due_to, filters.due_from, filters.due_to, filters.due_from, filters.due_to);
    }

    query += ` ORDER BY id DESC`;
    const [rows] = await dbPool.execute(query, params);
    return rows;
  }

  async findById(dbPool, id) {
    const [rows] = await dbPool.execute(
      `SELECT * FROM emd_guarantees WHERE id = ? LIMIT 1`,
      [id]
    );
    return rows[0] || null;
  }

  async findAmounts(dbPool, bankGuaranteeId) {
    const [rows] = await dbPool.execute(
      `SELECT * FROM emd_amount WHERE bank_guarantee_id = ? ORDER BY id DESC`,
      [bankGuaranteeId]
    );
    return rows;
  }

  async findRemarks(dbPool, bankGuaranteeId) {
    const [rows] = await dbPool.execute(
      `SELECT * FROM emd_remarks WHERE bank_guarantee_id = ? ORDER BY id DESC`,
      [bankGuaranteeId]
    );
    return rows;
  }

  async getTotalReceived(dbPool, bankGuaranteeId) {
    const [rows] = await dbPool.execute(
      `SELECT COALESCE(SUM(recive_amount), 0) as total FROM emd_amount WHERE bank_guarantee_id = ?`,
      [bankGuaranteeId]
    );
    return parseFloat(rows[0].total) || 0;
  }
}

module.exports = new EmdRepository();
