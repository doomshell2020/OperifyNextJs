const { QueryTypes } = require('sequelize');

class IndentRepository {
  /**
   * Get the next available indent ID (MAX + 1, starting at 1001)
   */
  async getNextIndentId(dbPool) {
    const rows = await dbPool.query(
      'SELECT COALESCE(MAX(indent_id), 1000) + 1 AS next_id FROM st_indentmaster',
      { type: QueryTypes.SELECT }
    );
    return rows[0].next_id;
  }

  /**
   * Search items by name from st_additem + size info
   */
  async searchItems(dbPool, query) {
    return await dbPool.query(
      `SELECT 
         a.id, 
         a.item_name, 
         a.cost_price, 
         a.size_id,
         a.uom AS unit_id,
         COALESCE(s.size_name, '') AS size_name
       FROM st_additem a
       LEFT JOIN st_sizemanager s ON s.id = a.size_id
       WHERE a.item_name LIKE :query AND a.status = 'Y'
       ORDER BY a.item_name ASC
       LIMIT 20`,
      { replacements: { query: `${query}%` }, type: QueryTypes.SELECT }
    );
  }

  /**
   * Add an item to the temporary indent table
   */
  async addTempItem(dbPool, data) {
    const { indent_id, item_id, size_id, quantity, sale_price, amount, added_by } = data;
    const insertedId = await dbPool.query(
      `INSERT INTO st_indentmaster_temp 
         (indent_id, item_id, size_id, sale_price, quantity, amount, added_time, added_by)
       VALUES (:indent_id, :item_id, :size_id, :sale_price, :quantity, :amount, NOW(), :added_by)`,
      {
        replacements: {
          indent_id,
          item_id,
          size_id: size_id || null,
          sale_price: sale_price || 0,
          quantity,
          amount: amount || 0,
          added_by
        },
        type: QueryTypes.INSERT
      }
    );
    
    const rows = await dbPool.query(
      `SELECT 
         t.id, t.indent_id, t.item_id, t.size_id, t.quantity, t.sale_price, t.amount,
         a.item_name, a.cost_price, a.uom AS unit_id,
         COALESCE(s.size_name, '') AS size_name,
         COALESCE(u.unit_name, '') AS unit_name
       FROM st_indentmaster_temp t
       LEFT JOIN st_additem a ON a.id = t.item_id
       LEFT JOIN st_sizemanager s ON s.id = t.size_id
       LEFT JOIN st_measurementunits u ON u.id = a.uom
       WHERE t.id = :insertedId`,
      { replacements: { insertedId: insertedId[0] }, type: QueryTypes.SELECT }
    );
    return rows[0] || null;
  }

  /**
   * Remove an item from the temporary indent table
   */
  async removeTempItem(dbPool, id) {
    const result = await dbPool.query(
      'DELETE FROM st_indentmaster_temp WHERE id = :id',
      { replacements: { id }, type: QueryTypes.DELETE }
    );
    // Sequelize DELETE doesn't always return affectedRows exactly, but typically nothing throws on empty.
    return true;
  }

  /**
   * Get all temp items for a given indent_id (for preview)
   */
  async getTempItems(dbPool, indent_id) {
    return await dbPool.query(
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
       LEFT JOIN st_categorymaster c ON c.id = a.category_id
       WHERE t.indent_id = :indent_id
       ORDER BY t.id ASC`,
      { replacements: { indent_id }, type: QueryTypes.SELECT }
    );
  }

  /**
   * Finalize an indent: move rows from indenttemp → indent, then clear temp
   */
  async finalizeIndent(dbPool, indent_id, user_id) {
    const transaction = await dbPool.transaction();
    try {
      // Get temp items
      const tempItems = await dbPool.query(
        'SELECT * FROM st_indentmaster_temp WHERE indent_id = :indent_id FOR UPDATE',
        { replacements: { indent_id }, type: QueryTypes.SELECT, transaction }
      );

      if (tempItems.length === 0) {
        throw new Error('No items to finalize');
      }
      const existing=await dbPool.query('SELECT id FROM st_indentmaster WHERE indent_id=:indent_id LIMIT 1 FOR UPDATE',{replacements:{indent_id},type:QueryTypes.SELECT,transaction});
      if(existing.length)throw require('../../utils/receiptValidation').invalid('This indent has already been finalized.',409);

      // Insert each temp item into the permanent indent table
      for (const item of tempItems) {
        await dbPool.query(
          `INSERT INTO st_indentmaster 
             (indent_id, item_id, size_id, sale_price, quantity, amount, indent_status, added_time, added_by)
           VALUES (:indent_id, :item_id, :size_id, :sale_price, :quantity, :amount, 'P', NOW(), :user_id)`,
          {
            replacements: {
              indent_id: item.indent_id,
              item_id: item.item_id,
              size_id: item.size_id || null,
              sale_price: item.sale_price || 0,
              quantity: item.quantity,
              amount: item.amount || 0,
              user_id
            },
            type: QueryTypes.INSERT,
            transaction
          }
        );
      }

      // Clear temp items
      await dbPool.query('DELETE FROM st_indentmaster_temp WHERE indent_id = :indent_id', {
        replacements: { indent_id },
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

  /**
   * List all indents with filters (indent_id, date range)
   * Returns one row per unique indent_id, with items array
   */
  async listIndents(dbPool, filters = {}) {
    const clauses=[],params={};
    if (filters.indent_id) {clauses.push('i.indent_id=:indent_id');params.indent_id=filters.indent_id;}
    if (filters.date_from && filters.date_from !== '1970-01-01') {clauses.push('DATE(i.added_time)>=:date_from');params.date_from=filters.date_from;}
    if (filters.date_to && filters.date_to !== '1970-01-01') {clauses.push('DATE(i.added_time)<=:date_to');params.date_to=filters.date_to;}
    const indents=await dbPool.query(`SELECT i.indent_id, MIN(i.added_time) as added_time,
      MAX(u.user_name) as created_by, SUM(i.quantity) as total_qty, COUNT(i.id) as item_count,
      SUM(GREATEST(i.quantity-COALESCE(i.return_qty,0),0)) as remaining_qty,
      CASE WHEN SUM(GREATEST(i.quantity-COALESCE(i.return_qty,0),0))>0 THEN 'Pending' ELSE 'Completed' END as status
      FROM st_indentmaster i LEFT JOIN users u ON u.id=(SELECT j.added_by FROM st_indentmaster j WHERE j.indent_id=i.indent_id ORDER BY j.id ASC LIMIT 1)
      ${clauses.length ? 'WHERE i.indent_id IN (SELECT f.indent_id FROM st_indentmaster f WHERE '+clauses.join(' AND ').replaceAll('i.','f.')+')' : ''}
      GROUP BY i.indent_id ORDER BY MAX(i.id) DESC`,{replacements:params,type:QueryTypes.SELECT});
    if (!indents.length) return [];
    // The legacy DataTable loads all matching indents; fetch their children in one query.
    const items=await dbPool.query(`SELECT i.id,i.indent_id,i.item_id,i.quantity,i.return_qty,
      a.item_name,a.uom as unit_id,COALESCE(s.size_name,'') as size_name,COALESCE(u.unit_name,'') as unit_name,
      (SELECT COALESCE(SUM(sr.quantity),0) FROM st_stock_register sr WHERE sr.item_id=i.item_id AND sr.store_type='4' AND sr.status!='N') as stock_in_hand
      FROM st_indentmaster i LEFT JOIN st_additem a ON a.id=i.item_id
      LEFT JOIN st_sizemanager s ON s.id=i.size_id LEFT JOIN st_measurementunits u ON u.id=a.uom
      WHERE i.indent_id IN (:ids) ORDER BY i.id ASC`,{replacements:{ids:indents.map(i=>i.indent_id)},type:QueryTypes.SELECT});
    const byIndent=new Map();
    for (const item of items) {const id=String(item.indent_id);if (!byIndent.has(id)) byIndent.set(id,[]);byIndent.get(id).push(item);}
    return indents.map(indent=>({...indent,items:byIndent.get(String(indent.indent_id)) || []}));
  }

  /**
   * Get full detail for a single indent (for print/PDF)
   */
  async getIndentDetail(dbPool, indent_id) {
    const siteRows = await dbPool.query("SELECT * FROM sitesettings_details WHERE status = 'Y' LIMIT 1", {type:QueryTypes.SELECT});
    const items = await dbPool.query(
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
       LEFT JOIN st_categorymaster c ON c.id = a.category_id
       LEFT JOIN users u ON u.id = i.added_by
       WHERE i.indent_id = :indent_id
       ORDER BY i.id ASC`,
      { replacements: { indent_id }, type: QueryTypes.SELECT }
    );

    if (items.length === 0) {
      // Try indenttemp (preview before finalize)
      const tempItems = await dbPool.query(
        `SELECT 
           t.id, t.item_id, t.quantity, 0 AS return_qty, t.added_time,
           a.item_name, a.uom AS unit_id,
           COALESCE(s.size_name, '') AS size_name,
           COALESCE(u2.unit_name, '') AS unit_name,
           COALESCE(c.category_name, '') AS category_name,
           u.user_name AS created_by
         FROM st_indentmaster_temp t
         LEFT JOIN st_additem a ON a.id = t.item_id
         LEFT JOIN st_sizemanager s ON s.id = t.size_id
         LEFT JOIN st_measurementunits u2 ON u2.id = a.uom
         LEFT JOIN st_categorymaster c ON c.id = a.category_id
         LEFT JOIN users u ON u.id = t.added_by
         WHERE t.indent_id = :indent_id
         ORDER BY t.id ASC`,
        { replacements: { indent_id }, type: QueryTypes.SELECT }
      );
      return { items: tempItems, is_temp: true, site_details:siteRows[0] || {} };
    }

    return { items, is_temp: false, site_details:siteRows[0] || {} };
  }

  /**
   * Get pending indents (indent_status = 'P' and remaining qty > 0)
   */
  async getPendingIndents(dbPool) {
    const indents = await dbPool.query(
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
       ORDER BY i.indent_id DESC`,
      { type: QueryTypes.SELECT }
    );

    if (indents.length === 0) return [];

    const results = [];
    for (const indent of indents) {
      const items = await dbPool.query(
        `SELECT 
           i.id, i.item_id, i.quantity, i.return_qty,
           a.item_name
         FROM st_indentmaster i
         LEFT JOIN st_additem a ON a.id = i.item_id
         WHERE i.indent_id = :indent_id AND i.indent_status = 'P'
           AND (i.quantity - COALESCE(i.return_qty, 0)) > 0
         ORDER BY i.id ASC`,
        { replacements: { indent_id: indent.indent_id }, type: QueryTypes.SELECT }
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
