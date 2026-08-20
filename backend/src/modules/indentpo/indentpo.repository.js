const { QueryTypes } = require('sequelize');

class IndentpoRepository {
  /**
   * Get the next available indentpo ID (MAX + 1, starting at 1001)
   */
  async getNextIndentId(dbPool) {
    const rows = await dbPool.query(
      'SELECT COALESCE(MAX(indent_id), 1000) + 1 AS next_id FROM indentpo',
      { type: QueryTypes.SELECT }
    );
    return rows[0].next_id;
  }

  /**
   * Search active contracts by workorder or title
   */
  async searchContracts(dbPool, query) {
    return await dbPool.query(
      `SELECT id, title, workorder 
       FROM contracts 
       WHERE status = 'Y' AND (title LIKE :query OR workorder LIKE :query)
       ORDER BY title ASC
       LIMIT 20`,
      { replacements: { query: `%${query}%` }, type: QueryTypes.SELECT }
    );
  }

  /**
   * Fetch finished products for a given contract
   */
  async getContractProducts(dbPool, contractId) {
    return await dbPool.query(
      `SELECT 
         bfp.id, 
         bfp.product_id, 
         bfp.price, 
         bfp.quantity,
         a.item_name,
         u.unit_name
       FROM bom_finisedproduct bfp
       JOIN st_additem a ON a.id = bfp.product_id
       LEFT JOIN st_measurementunits u ON u.id = a.uom
       WHERE bfp.contract_id = :contractId`,
      { replacements: { contractId }, type: QueryTypes.SELECT }
    );
  }

  /**
   * Search active machines
   */
  async searchMachines(dbPool, query) {
    return await dbPool.query(
      `SELECT id, machine_name 
       FROM machine_master 
       WHERE status = 'Y' AND machine_name LIKE :query
       ORDER BY machine_name ASC
       LIMIT 20`,
      { replacements: { query: `%${query}%` }, type: QueryTypes.SELECT }
    );
  }

  /**
   * Get raw materials from design sheet and calculate pending & stock
   */
  async getDesignSheetDetails(dbPool, contractId, itemId) {
    const designSheet = await dbPool.query(
      `SELECT designsheetno FROM designsheet WHERE contract_id = :contractId AND item_id = :itemId LIMIT 1`,
      { replacements: { contractId, itemId }, type: QueryTypes.SELECT }
    );

    if (designSheet.length === 0) return [];

    const sheetNo = designSheet[0].designsheetno;

    const details = await dbPool.query(
      `SELECT dsd.item_id, dsd.item_qty, dsd.is_group, a.item_name, a.category_id, u.unit_name
       FROM designsheetdetails dsd
       JOIN st_additem a ON a.id = dsd.item_id
       LEFT JOIN st_measurementunits u ON u.id = a.uom
       WHERE dsd.designsheetno = :sheetNo
       ORDER BY dsd.is_group ASC`,
      { replacements: { sheetNo }, type: QueryTypes.SELECT }
    );

    const result = [];
    for (const row of details) {
      const issuedRows = await dbPool.query(
        `SELECT ROUND(SUM(quantity), 2) as sum_qty FROM st_stock_register WHERE item_id = :item_id AND contract_id = :contractId AND finishedproduct_id = :itemId AND store_type = '2'`,
        { replacements: { item_id: row.item_id, contractId, itemId }, type: QueryTypes.SELECT }
      );
      
      const issuedQty = issuedRows[0].sum_qty || 0;
      const pendingQty = Math.max(0, row.item_qty - issuedQty);

      const inhandRows = await dbPool.query(
        `SELECT 
           ROUND(SUM(CASE WHEN store_type IN ('0','1','3') THEN quantity ELSE 0 END), 2) as grn_qty,
           ROUND(SUM(CASE WHEN store_type IN ('2','4') THEN quantity ELSE 0 END), 2) as issued_stock_qty
         FROM st_stock_register 
         WHERE item_id = :item_id`,
        { replacements: { item_id: row.item_id }, type: QueryTypes.SELECT }
      );
      
      const grn = inhandRows[0].grn_qty || 0;
      const issuedStock = inhandRows[0].issued_stock_qty || 0;
      const inhandStock = Math.max(0, grn - issuedStock);

      let groupItems = [];
      if (row.is_group == 1) {
        const gItems = await dbPool.query(
          `SELECT 
             a.id, a.item_name,
             (
               SELECT ROUND(SUM(CASE WHEN store_type IN ('0','1','3') THEN quantity ELSE 0 END), 2) - 
                      ROUND(SUM(CASE WHEN store_type IN ('2','4') THEN quantity ELSE 0 END), 2)
               FROM st_stock_register sr WHERE sr.item_id = a.id
             ) as inhand_stock
           FROM st_additem a 
           WHERE a.category_id = :category_id AND a.status = 'Y'`,
          { replacements: { category_id: row.category_id }, type: QueryTypes.SELECT }
        );
        groupItems = gItems.map(g => ({
          ...g,
          inhand_stock: g.inhand_stock ? Number(g.inhand_stock) : 0
        }));
      }

      result.push({
        item_id: row.item_id,
        item_name: row.item_name,
        is_group: row.is_group,
        category_id: row.category_id,
        unit_name: row.unit_name || 'Nos',
        design_qty: Number(row.item_qty),
        issued_qty: Number(issuedQty),
        pending_qty: Number(pendingQty),
        inhand_stock: Number(inhandStock),
        group_items: groupItems
      });
    }

    return result;
  }

  /**
   * Save IndentPO (Create Header + Details)
   */
  async saveIndentpo(dbPool, data, userId) {
    const transaction = await dbPool.transaction();
    try {
      const headerRes = await dbPool.query(
        `INSERT INTO indentpo 
         (indent_id, contract_id, finishedproduct_id, machine_id, issued_name, issue_date, user_id, created, updated)
         VALUES (:indent_id, :contract_id, :finishedproduct_id, :machine_id, :issued_name, :issue_date, :user_id, NOW(), NOW())`,
        {
          replacements: {
            indent_id: data.indent_id,
            contract_id: data.contract_id,
            finishedproduct_id: data.finishedproduct_id,
            machine_id: data.machine_id,
            issued_name: data.issued_name,
            issue_date: data.issue_date,
            user_id: userId
          },
          type: QueryTypes.INSERT,
          transaction
        }
      );
      
      const poId = headerRes[0];

      for (const item of data.items) {
        if (item.issue_qty && Number(item.issue_qty) > 0) {
          await dbPool.query(
            `INSERT INTO st_stock_register 
             (indent_id, contract_id, finishedproduct_id, item_id, quantity, issue_date, store_type, created, added_time)
             VALUES (:indent_id, :contract_id, :finishedproduct_id, :item_id, :quantity, :issue_date, '2', NOW(), NOW())`,
            {
              replacements: {
                indent_id: data.indent_id,
                contract_id: data.contract_id,
                finishedproduct_id: data.finishedproduct_id,
                item_id: item.item_id,
                quantity: Number(item.issue_qty),
                issue_date: data.issue_date
              },
              type: QueryTypes.INSERT,
              transaction
            }
          );
        }
      }

      await transaction.commit();
      return { id: poId, indent_id: data.indent_id };
    } catch (err) {
      await transaction.rollback();
      throw err;
    }
  }

  /**
   * List Indentpo
   */
  async listIndentpo(dbPool, filters = {}) {
    let where = '1=1';
    const params = {};

    if (filters.contract_id) {
      where += ' AND i.contract_id = :contract_id';
      params.contract_id = filters.contract_id;
    }
    if (filters.machine_id) {
      where += ' AND i.machine_id = :machine_id';
      params.machine_id = filters.machine_id;
    }
    if (filters.product_id) {
      where += ' AND i.finishedproduct_id = :product_id';
      params.product_id = filters.product_id;
    }
    if (filters.date_from) {
      where += ' AND DATE(i.issue_date) >= :date_from';
      params.date_from = filters.date_from;
    }
    if (filters.date_to) {
      where += ' AND DATE(i.issue_date) <= :date_to';
      params.date_to = filters.date_to;
    }

    return await dbPool.query(
      `SELECT 
         i.id, i.indent_id, i.issue_date, i.issued_name, i.created, i.contract_id,
         c.title as contract_name, c.workorder,
         a.item_name as product_name,
         m.machine_name
       FROM indentpo i
       LEFT JOIN contracts c ON c.id = i.contract_id
       LEFT JOIN st_additem a ON a.id = i.finishedproduct_id
       LEFT JOIN machine_master m ON m.id = i.machine_id
       WHERE ${where}
       ORDER BY i.id DESC`,
      { replacements: params, type: QueryTypes.SELECT }
    );
  }

  /**
   * Get single Indentpo detail (Header + Items)
   */
  async getIndentpoDetail(dbPool, indentId) {
    const header = await dbPool.query(
      `SELECT 
         i.id, i.indent_id, i.issue_date, i.issued_name, i.created,
         c.title as contract_name, c.workorder,
         a.item_name as product_name,
         m.machine_name,
         u.user_name as created_by
       FROM indentpo i
       LEFT JOIN contracts c ON c.id = i.contract_id
       LEFT JOIN st_additem a ON a.id = i.finishedproduct_id
       LEFT JOIN machine_master m ON m.id = i.machine_id
       LEFT JOIN users u ON u.id = i.user_id
       WHERE i.indent_id = :indentId`,
      { replacements: { indentId }, type: QueryTypes.SELECT }
    );

    if (header.length === 0) return null;

    const items = await dbPool.query(
      `SELECT 
         sr.item_id, sr.quantity,
         a.item_name as raw_material_name,
         u.unit_name
       FROM st_stock_register sr
       LEFT JOIN st_additem a ON a.id = sr.item_id
       LEFT JOIN st_measurementunits u ON u.id = a.uom
       WHERE sr.indent_id = :indentId AND sr.store_type = '2'`,
      { replacements: { indentId }, type: QueryTypes.SELECT }
    );

    return { ...header[0], items };
  }
}

module.exports = new IndentpoRepository();
