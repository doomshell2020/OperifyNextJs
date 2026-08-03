class IndentpoRepository {
  /**
   * Get the next available indentpo ID (MAX + 1, starting at 1001)
   */
  async getNextIndentId(dbPool) {
    const [rows] = await dbPool.execute(
      'SELECT COALESCE(MAX(indent_id), 1000) + 1 AS next_id FROM indentpo'
    );
    return rows[0].next_id;
  }

  /**
   * Search active contracts by workorder or title
   */
  async searchContracts(dbPool, query) {
    const [rows] = await dbPool.execute(
      `SELECT id, title, workorder 
       FROM contracts 
       WHERE status = 'Y' AND (title LIKE ? OR workorder LIKE ?)
       ORDER BY title ASC
       LIMIT 20`,
      [`%${query}%`, `%${query}%`]
    );
    return rows;
  }

  /**
   * Fetch finished products for a given contract
   */
  async getContractProducts(dbPool, contractId) {
    const [rows] = await dbPool.execute(
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
       WHERE bfp.contract_id = ?`,
      [contractId]
    );
    return rows;
  }

  /**
   * Search active machines
   */
  async searchMachines(dbPool, query) {
    const [rows] = await dbPool.execute(
      `SELECT id, machine_name 
       FROM machine_master 
       WHERE status = 'Y' AND machine_name LIKE ?
       ORDER BY machine_name ASC
       LIMIT 20`,
      [`%${query}%`]
    );
    return rows;
  }

  /**
   * Get raw materials from design sheet and calculate pending & stock
   */
  async getDesignSheetDetails(dbPool, contractId, itemId) {
    const [designSheet] = await dbPool.execute(
      `SELECT designsheetno FROM designsheet WHERE contract_id = ? AND item_id = ? LIMIT 1`,
      [contractId, itemId]
    );

    if (designSheet.length === 0) return [];

    const sheetNo = designSheet[0].designsheetno;

    const [details] = await dbPool.execute(
      `SELECT dsd.item_id, dsd.item_qty, dsd.is_group, a.item_name, a.category_id, u.unit_name
       FROM designsheetdetails dsd
       JOIN st_additem a ON a.id = dsd.item_id
       LEFT JOIN st_measurementunits u ON u.id = a.uom
       WHERE dsd.designsheetno = ?
       ORDER BY dsd.is_group ASC`,
      [sheetNo]
    );

    const result = [];
    for (const row of details) {
      // Pending quantity = design qty - already issued qty (store_type = 2 or 4) + returned (if applicable)
      // Mirroring the old legacy logic. In CakePHP: rawitempendingqty
      
      const [issuedRows] = await dbPool.execute(
        `SELECT ROUND(SUM(quantity), 2) as sum_qty FROM st_stock_register WHERE item_id = ? AND contract_id = ? AND finishedproduct_id = ? AND store_type = '2'`,
        [row.item_id, contractId, itemId]
      );
      
      const issuedQty = issuedRows[0].sum_qty || 0;
      const pendingQty = Math.max(0, row.item_qty - issuedQty);

      // Inhand stock = GRN (0,1,3) - Indent (2,4)
      const [inhandRows] = await dbPool.execute(
        `SELECT 
           ROUND(SUM(CASE WHEN store_type IN ('0','1','3') THEN quantity ELSE 0 END), 2) as grn_qty,
           ROUND(SUM(CASE WHEN store_type IN ('2','4') THEN quantity ELSE 0 END), 2) as issued_stock_qty
         FROM st_stock_register 
         WHERE item_id = ?`,
        [row.item_id]
      );
      
      const grn = inhandRows[0].grn_qty || 0;
      const issuedStock = inhandRows[0].issued_stock_qty || 0;
      const inhandStock = Math.max(0, grn - issuedStock);

      let groupItems = [];
      if (row.is_group == 1) {
        const [gItems] = await dbPool.execute(
          `SELECT 
             a.id, a.item_name,
             (
               SELECT ROUND(SUM(CASE WHEN store_type IN ('0','1','3') THEN quantity ELSE 0 END), 2) - 
                      ROUND(SUM(CASE WHEN store_type IN ('2','4') THEN quantity ELSE 0 END), 2)
               FROM st_stock_register sr WHERE sr.item_id = a.id
             ) as inhand_stock
           FROM st_additem a 
           WHERE a.category_id = ? AND a.status = 'Y'`,
          [row.category_id]
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
    const conn = await dbPool.getConnection();
    try {
      await conn.beginTransaction();

      // Insert Header
      const [headerRes] = await conn.execute(
        `INSERT INTO indentpo 
         (indent_id, contract_id, finishedproduct_id, machine_id, issued_name, issue_date, created, updated)
         VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())`,
        [
          data.indent_id,
          data.contract_id,
          data.finishedproduct_id,
          data.machine_id,
          data.issued_name,
          data.issue_date
        ]
      );
      
      const poId = headerRes.insertId;

      // Insert Details into st_stock_register (store_type = 2 for issue)
      for (const item of data.items) {
        if (item.issue_qty && Number(item.issue_qty) > 0) {
          await conn.execute(
            `INSERT INTO st_stock_register 
             (indent_id, contract_id, finishedproduct_id, item_id, quantity, issue_date, store_type, created, added_time)
             VALUES (?, ?, ?, ?, ?, ?, '2', NOW(), NOW())`,
            [
              data.indent_id,
              data.contract_id,
              data.finishedproduct_id,
              item.item_id,
              Number(item.issue_qty),
              data.issue_date
            ]
          );
        }
      }

      await conn.commit();
      return { id: poId, indent_id: data.indent_id };
    } catch (err) {
      await conn.rollback();
      throw err;
    } finally {
      conn.release();
    }
  }

  /**
   * List Indentpo
   */
  async listIndentpo(dbPool, filters = {}) {
    let where = '1=1';
    const params = [];

    if (filters.contract_id) {
      where += ' AND i.contract_id = ?';
      params.push(filters.contract_id);
    }
    if (filters.machine_id) {
      where += ' AND i.machine_id = ?';
      params.push(filters.machine_id);
    }
    if (filters.product_id) {
      where += ' AND i.finishedproduct_id = ?';
      params.push(filters.product_id);
    }
    if (filters.date_from) {
      where += ' AND DATE(i.issue_date) >= ?';
      params.push(filters.date_from);
    }
    if (filters.date_to) {
      where += ' AND DATE(i.issue_date) <= ?';
      params.push(filters.date_to);
    }

    const [rows] = await dbPool.execute(
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
      params
    );

    return rows;
  }

  /**
   * Get single Indentpo detail (Header + Items)
   */
  async getIndentpoDetail(dbPool, indentId) {
    const [header] = await dbPool.execute(
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
       WHERE i.indent_id = ?`,
      [indentId]
    );

    if (header.length === 0) return null;

    const [items] = await dbPool.execute(
      `SELECT 
         sr.item_id, sr.quantity,
         a.item_name as raw_material_name,
         u.unit_name
       FROM st_stock_register sr
       LEFT JOIN st_additem a ON a.id = sr.item_id
       LEFT JOIN st_measurementunits u ON u.id = a.uom
       WHERE sr.indent_id = ? AND sr.store_type = '2'`,
      [indentId]
    );

    return { ...header[0], items };
  }
}

module.exports = new IndentpoRepository();
