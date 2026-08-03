class IndentRepository {
  /**
   * Get the next available indent ID (MAX + 1, starting at 1001)
   */
  async getNextIndentId(dbPool) {
    const [rows] = await dbPool.execute(
      'SELECT COALESCE(MAX(indent_id), 1000) + 1 AS next_id FROM st_indentmaster'
    );
    return rows[0].next_id;
  }

  /**
   * Search items by name from st_additem + size info
   */
  async searchItems(dbPool, query) {
    const [rows] = await dbPool.execute(
      `SELECT 
         a.id, 
         a.item_name, 
         a.cost_price, 
         a.size_id,
         a.uom AS unit_id,
         COALESCE(s.size_name, '') AS size_name
       FROM st_additem a
       LEFT JOIN st_sizemanager s ON s.id = a.size_id
       WHERE a.item_name LIKE ? AND a.status = 'Y'
       ORDER BY a.item_name ASC
       LIMIT 20`,
      [`${query}%`]
    );
    return rows;
  }

  /**
   * Add an item to the temporary indent table
   */
  async addTempItem(dbPool, data) {
    const { indent_id, item_id, size_id, quantity, sale_price, amount, added_by } = data;
    const [result] = await dbPool.execute(
      `INSERT INTO st_indentmaster_temp 
         (indent_id, item_id, size_id, sale_price, quantity, amount, added_time, added_by)
       VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)`,
      [indent_id, item_id, size_id || null, sale_price || 0, quantity, amount || 0, added_by]
    );
    const insertedId = result.insertId;
    // Fetch back the inserted row with joins
    const [rows] = await dbPool.execute(
      `SELECT 
         t.id, t.indent_id, t.item_id, t.size_id, t.quantity, t.sale_price, t.amount,
         a.item_name, a.cost_price, a.uom AS unit_id,
         COALESCE(s.size_name, '') AS size_name,
         COALESCE(u.unit_name, '') AS unit_name
       FROM st_indentmaster_temp t
       LEFT JOIN st_additem a ON a.id = t.item_id
       LEFT JOIN st_sizemanager s ON s.id = t.size_id
       LEFT JOIN st_measurementunits u ON u.id = a.uom
       WHERE t.id = ?`,
      [insertedId]
    );
    return rows[0] || null;
  }

  /**
   * Remove an item from the temporary indent table
   */
  async removeTempItem(dbPool, id) {
    const [result] = await dbPool.execute(
      'DELETE FROM st_indentmaster_temp WHERE id = ?',
      [id]
    );
    return result.affectedRows > 0;
  }

  /**
   * Get all temp items for a given indent_id (for preview)
   */
  async getTempItems(dbPool, indent_id) {
    const [rows] = await dbPool.execute(
      `SELECT 
         t.id, t.indent_id, t.item_id, t.size_id, t.quantity, t.sale_price, t.amount,
         a.item_name, a.cost_price, a.uom AS unit_id,
         COALESCE(s.size_name, '') AS size_name,
         COALESCE(u.unit_name, '') AS unit_name,
         COALESCE(c.category_name, '') AS category_name
       FROM st_indentmaster_temp t
       LEFT JOIN st_additem a ON a.id = t.item_id
       LEFT JOIN st_sizemanager s ON s.id = t.size_id
       LEFT JOIN st_measurementunits u ON u.id = a.uom
       LEFT JOIN st_itemcategory c ON c.id = a.category_id
       WHERE t.indent_id = ?
       ORDER BY t.id ASC`,
      [indent_id]
    );
    return rows;
  }

  /**
   * Finalize an indent: move rows from indenttemp → indent, then clear temp
   */
  async finalizeIndent(dbPool, indent_id, user_id) {
    const conn = await dbPool.getConnection();
    try {
      await conn.beginTransaction();

      // Get temp items
      const [tempItems] = await conn.execute(
        'SELECT * FROM st_indentmaster_temp WHERE indent_id = ?',
        [indent_id]
      );

      if (tempItems.length === 0) {
        await conn.rollback();
        throw new Error('No items to finalize');
      }

      // Insert each temp item into the permanent indent table
      for (const item of tempItems) {
        await conn.execute(
          `INSERT INTO st_indentmaster 
             (indent_id, item_id, size_id, sale_price, quantity, amount, indent_status, added_time, added_by)
           VALUES (?, ?, ?, ?, ?, ?, 'P', NOW(), ?)`,
          [
            item.indent_id,
            item.item_id,
            item.size_id || null,
            item.sale_price || 0,
            item.quantity,
            item.amount || 0,
            user_id
          ]
        );
      }

      // Clear temp items
      await conn.execute('DELETE FROM st_indentmaster_temp WHERE indent_id = ?', [indent_id]);

      await conn.commit();
      return true;
    } catch (err) {
      await conn.rollback();
      throw err;
    } finally {
      conn.release();
    }
  }

  /**
   * List all indents with filters (indent_id, date range)
   * Returns one row per unique indent_id, with items array
   */
  async listIndents(dbPool, filters = {}) {
    const { indent_id, date_from, date_to } = filters;

    let where = '';
    const params = [];

    if (indent_id) {
      where += ' AND i.indent_id = ?';
      params.push(indent_id);
    }
    if (date_from) {
      where += ' AND DATE(i.added_time) >= ?';
      params.push(date_from);
    }
    if (date_to) {
      where += ' AND DATE(i.added_time) <= ?';
      params.push(date_to);
    }

    // Get unique indent IDs
    const [indents] = await dbPool.execute(
      `SELECT 
         i.indent_id,
         MIN(i.added_time) AS added_time,
         u.user_name AS created_by,
         SUM(i.quantity) AS total_qty,
         COUNT(i.id) AS item_count
       FROM st_indentmaster i
       LEFT JOIN users u ON u.id = i.added_by
       WHERE 1=1 ${where}
       GROUP BY i.indent_id, u.user_name
       ORDER BY i.indent_id DESC`,
      params
    );

    if (indents.length === 0) return [];

    // For each indent, get items with stock info
    const results = [];
    for (const indent of indents) {
      const [items] = await dbPool.execute(
        `SELECT 
           i.id, i.item_id, i.quantity, i.return_qty,
           a.item_name, a.unit_id,
           COALESCE(s.size_name, '') AS size_name,
           COALESCE(u2.unit_name, '') AS unit_name,
           COALESCE(
             (SELECT SUM(sr.quantity) FROM st_stockregister sr WHERE sr.item_id = i.item_id), 0
           ) AS stock_in_hand
         FROM st_indentmaster i
         LEFT JOIN st_additem a ON a.id = i.item_id
         LEFT JOIN st_sizemanager s ON s.id = i.size_id
         LEFT JOIN st_measurementunits u2 ON u2.id = a.uom
         WHERE i.indent_id = ?
         ORDER BY i.id ASC`,
        [indent.indent_id]
      );

      results.push({
        indent_id: indent.indent_id,
        added_time: indent.added_time,
        created_by: indent.created_by,
        total_qty: indent.total_qty,
        item_count: indent.item_count,
        items
      });
    }

    return results;
  }

  /**
   * Get full detail for a single indent (for print/PDF)
   */
  async getIndentDetail(dbPool, indent_id) {
    const [items] = await dbPool.execute(
      `SELECT 
         i.id, i.item_id, i.quantity, i.return_qty, i.added_time,
         a.item_name, a.uom AS unit_id,
         COALESCE(s.size_name, '') AS size_name,
         COALESCE(u2.unit_name, '') AS unit_name,
         COALESCE(c.category_name, '') AS category_name,
         u.user_name AS created_by
       FROM st_indentmaster i
       LEFT JOIN st_additem a ON a.id = i.item_id
       LEFT JOIN st_sizemanager s ON s.id = i.size_id
       LEFT JOIN st_measurementunits u2 ON u2.id = a.uom
       LEFT JOIN st_itemcategory c ON c.id = a.category_id
       LEFT JOIN users u ON u.id = i.added_by
       WHERE i.indent_id = ?
       ORDER BY i.id ASC`,
      [indent_id]
    );

    if (items.length === 0) {
      // Try indenttemp (preview before finalize)
      const [tempItems] = await dbPool.execute(
        `SELECT 
           t.id, t.item_id, t.quantity, 0 AS return_qty, t.added_time,
           a.item_name, a.uom AS unit_id,
           COALESCE(s.size_name, '') AS size_name,
           COALESCE(u2.unit_name, '') AS unit_name,
           COALESCE(c.category_name, '') AS category_name,
           NULL AS created_by
         FROM st_indentmaster_temp t
         LEFT JOIN st_additem a ON a.id = t.item_id
         LEFT JOIN st_sizemanager s ON s.id = t.size_id
         LEFT JOIN st_measurementunits u2 ON u2.id = a.uom
         LEFT JOIN st_itemcategory c ON c.id = a.category_id
         WHERE t.indent_id = ?
         ORDER BY t.id ASC`,
        [indent_id]
      );
      return { items: tempItems, is_temp: true };
    }

    return { items, is_temp: false };
  }

  /**
   * Get pending indents (indent_status = 'P' and remaining qty > 0)
   */
  async getPendingIndents(dbPool) {
    const [indents] = await dbPool.execute(
      `SELECT 
         i.indent_id,
         MIN(i.added_time) AS added_time,
         u.user_name AS created_by,
         SUM(i.quantity) AS total_qty,
         SUM(i.quantity - COALESCE(i.return_qty, 0)) AS remaining_qty
       FROM st_indentmaster i
       LEFT JOIN users u ON u.id = i.added_by
       WHERE i.indent_status = 'P'
       GROUP BY i.indent_id, u.user_name
       HAVING remaining_qty > 0
       ORDER BY i.indent_id DESC`
    );

    if (indents.length === 0) return [];

    const results = [];
    for (const indent of indents) {
      const [items] = await dbPool.execute(
        `SELECT 
           i.id, i.item_id, i.quantity, i.return_qty,
           a.item_name
         FROM st_indentmaster i
         LEFT JOIN st_additem a ON a.id = i.item_id
         WHERE i.indent_id = ? AND i.indent_status = 'P'
           AND (i.quantity - COALESCE(i.return_qty, 0)) > 0
         ORDER BY i.id ASC`,
        [indent.indent_id]
      );

      results.push({
        indent_id: indent.indent_id,
        added_time: indent.added_time,
        created_by: indent.created_by,
        total_qty: indent.total_qty,
        remaining_qty: indent.remaining_qty,
        items
      });
    }

    return results;
  }
}

module.exports = new IndentRepository();
