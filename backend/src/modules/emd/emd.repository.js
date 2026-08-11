const { QueryTypes } = require('sequelize');

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
    const params = {};

    if (filters.bg_for) {
      query += ` AND bg_for = :bg_for`;
      params.bg_for = filters.bg_for;
    }
    if (filters.bankguaranteeno) {
      query += ` AND bankguaranteeno LIKE :bankguaranteeno`;
      params.bankguaranteeno = `%${filters.bankguaranteeno}%`;
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
    if (filters.due_from && filters.due_to) {
      query += ` AND (
        (DATE(validupto) >= :due_from AND DATE(validupto) <= :due_to) OR
        (DATE(claim_upto) >= :due_from AND DATE(claim_upto) <= :due_to) OR
        (DATE(extenstionupto) >= :due_from AND DATE(extenstionupto) <= :due_to)
      )`;
      params.due_from = filters.due_from;
      params.due_to = filters.due_to;
    }

    query += ` ORDER BY id DESC`;
    return await dbPool.query(query, { replacements: params, type: QueryTypes.SELECT });
  }

  async findById(dbPool, id) {
    const record = await dbPool.models.emd_guarantees.findOne({
      where: { id },
      raw: true
    });
    return record || null;
  }

  async findAmounts(dbPool, bankGuaranteeId) {
    return await dbPool.models.emd_amount.findAll({
      where: { bank_guarantee_id: bankGuaranteeId },
      order: [['id', 'DESC']],
      raw: true
    });
  }

  async findRemarks(dbPool, bankGuaranteeId) {
    return await dbPool.models.emd_remarks.findAll({
      where: { bank_guarantee_id: bankGuaranteeId },
      order: [['id', 'DESC']],
      raw: true
    });
  }

  async getTotalReceived(dbPool, bankGuaranteeId) {
    const total = await dbPool.models.emd_amount.sum('recive_amount', {
      where: { bank_guarantee_id: bankGuaranteeId }
    });
    return parseFloat(total) || 0;
  }
}

module.exports = new EmdRepository();
