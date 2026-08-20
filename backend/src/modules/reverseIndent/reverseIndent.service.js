const { QueryTypes } = require('sequelize');

class ReverseIndentService {
  async getNextReverseId(dbPool) {
    const rows = await dbPool.query('SELECT reverse_id FROM reverseindent ORDER BY id DESC LIMIT 1', { type: QueryTypes.SELECT });
    if (rows.length > 0 && rows[0].reverse_id) {
      const lastIdStr = String(rows[0].reverse_id).replace(/\D/g, '');
      const lastId = parseInt(lastIdStr || '1000', 10);
      return lastId + 1;
    }
    return 1001;
  }

  async getReverseIndents(dbPool, filters) {
    let query = `
      SELECT 
        r.id, r.reverse_id, r.contract_id, r.issue_date, r.received_name,
        c.title as contract_name, c.workorder,
        p.item_name as product_name,
        m.machine_name
      FROM reverseindent r
      LEFT JOIN contracts c ON r.contract_id = c.id
      LEFT JOIN st_additem p ON r.finishedproduct_id = p.id
      LEFT JOIN machine_master m ON r.machine_id = m.id
      WHERE 1=1
    `;
    const queryParams = [];

    if (filters.contract_id) {
      query += ` AND r.contract_id = ?`;
      queryParams.push(filters.contract_id);
    }
    if (filters.item_id) {
      query += ` AND r.finishedproduct_id = ?`;
      queryParams.push(filters.item_id);
    }
    if (filters.machine_id) {
      query += ` AND r.machine_id = ?`;
      queryParams.push(filters.machine_id);
    }
    if (filters.datefrom) {
      query += ` AND DATE(r.issue_date) >= ?`;
      queryParams.push(filters.datefrom);
    }
    if (filters.dateto) {
      query += ` AND DATE(r.issue_date) <= ?`;
      queryParams.push(filters.dateto);
    }

    query += ` ORDER BY r.id DESC`;

    const rows = await dbPool.query(query, { replacements: queryParams, type: QueryTypes.SELECT });
    return rows;
  }

  async saveReverseIndent(dbPool, data) {
    const transaction = await dbPool.transaction();
    try {
      const [insertRes] = await dbPool.query(`
        INSERT INTO reverseindent (
          reverse_id, contract_id, finishedproduct_id, machine_id, received_name, issue_date, created
        ) VALUES (?, ?, ?, ?, ?, ?, NOW())
      `, {
        replacements: [
          data.reverse_id,
          data.contract_id,
          data.finishedproduct_id,
          data.machine_id,
          data.received_name,
          data.issue_date
        ],
        type: QueryTypes.INSERT,
        transaction
      });
      const insertId = insertRes; // query with INSERT returns [resultId, affectedRows]

      if (data.items && Array.isArray(data.items)) {
        for (const item of data.items) {
          if (item.quantity && parseFloat(item.quantity) > 0) {
            await dbPool.query(`
              INSERT INTO st_stock_register (
                reverse_id, contract_id, finishedproduct_id, item_id, quantity, issue_date, store_type
              ) VALUES (?, ?, ?, ?, ?, ?, '3')
            `, {
              replacements: [
                data.reverse_id,
                data.contract_id,
                data.finishedproduct_id,
                item.item_id,
                item.quantity,
                data.issue_date
              ],
              type: QueryTypes.INSERT,
              transaction
            });
          }
        }
      }

      await transaction.commit();
      return insertId;
    } catch (err) {
      await transaction.rollback();
      throw err;
    }
  }

  async getReverseIndentDetails(dbPool, reverse_id) {
    const headerRows = await dbPool.query(`
      SELECT 
        r.*,
        c.title as contract_name,
        p.item_name as product_name,
        m.machine_name
      FROM reverseindent r
      LEFT JOIN contracts c ON r.contract_id = c.id
      LEFT JOIN st_additem p ON r.finishedproduct_id = p.id
      LEFT JOIN machine_master m ON r.machine_id = m.id
      WHERE r.reverse_id = ?
    `, { replacements: [reverse_id], type: QueryTypes.SELECT });

    if (headerRows.length === 0) return null;

    const items = await dbPool.query(`
      SELECT 
        s.item_id, s.quantity,
        i.item_name,
        u.unit_name as uom
      FROM st_stock_register s
      LEFT JOIN st_additem i ON s.item_id = i.id
      LEFT JOIN st_measurementunits u ON i.uom = u.id
      WHERE s.reverse_id = ? AND s.store_type = '3'
    `, { replacements: [reverse_id], type: QueryTypes.SELECT });

    return {
      ...headerRows[0],
      items
    };
  }

  async deleteReverseIndent(dbPool, reverse_id) {
    const transaction = await dbPool.transaction();
    try {
      await dbPool.query('DELETE FROM st_stock_register WHERE reverse_id = ? AND store_type = "3"', {
        replacements: [reverse_id],
        type: QueryTypes.DELETE,
        transaction
      });
      await dbPool.query('DELETE FROM reverseindent WHERE reverse_id = ?', {
        replacements: [reverse_id],
        type: QueryTypes.DELETE,
        transaction
      });
      await transaction.commit();
      return true;
    } catch (err) {
      await transaction.rollback();
      throw err;
    }
  }
}

module.exports = new ReverseIndentService();
