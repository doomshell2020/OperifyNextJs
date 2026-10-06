const { QueryTypes } = require('sequelize');

async function getFirstRow(dbPool, primaryQuery, fallbackQuery) {
  try {
    const rows = await dbPool.query(primaryQuery, { type: QueryTypes.SELECT });
    return rows[0] || null;
  } catch (error) {
    if (!fallbackQuery || error?.original?.code !== 'ER_BAD_FIELD_ERROR') {
      throw error;
    }

    const rows = await dbPool.query(fallbackQuery, { type: QueryTypes.SELECT });
    return rows[0] || null;
  }
}

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
  async getDesignSheetDetails(dbPool, contractId, itemId, transaction, enforceCategoryRules=true) {
    const query=(sql,options)=>dbPool.query(sql,{...options,transaction});
    const [settings]=await query('SELECT stock_update FROM sitesettings_details WHERE sitesettings_id=1 LIMIT 1',{type:QueryTypes.SELECT});
    const designSheet = await query(
      `SELECT designsheetno FROM designsheet WHERE contract_id = :contractId AND item_id = :itemId LIMIT 1`,
      { replacements: { contractId, itemId }, type: QueryTypes.SELECT }
    );

    if (designSheet.length === 0) return [];

    const sheetNo = designSheet[0].designsheetno;

    const details = await query(
      `SELECT dsd.item_id, dsd.item_qty, dsd.is_group, a.item_name, a.category_id, u.unit_name
       FROM designsheetdetails dsd
       JOIN st_additem a ON a.id = dsd.item_id
       LEFT JOIN st_measurementunits u ON u.id = a.uom
       WHERE dsd.designsheetno = :sheetNo
       ORDER BY dsd.is_group ASC`,
      { replacements: { sheetNo }, type: QueryTypes.SELECT }
    );

    const groupedCategories=new Set();
    for(const row of details)if(Number(row.is_group)===1){
      if(enforceCategoryRules && groupedCategories.has(String(row.category_id)))throw require('../../utils/receiptValidation').invalid('You cannot indent multiple same category items. Please correct the design sheet.',409);
      groupedCategories.add(String(row.category_id));
    }
    if(enforceCategoryRules && details.some(row=>Number(row.is_group)!==1 && groupedCategories.has(String(row.category_id))))throw require('../../utils/receiptValidation').invalid('You cannot indent multiple same category items. Please correct the design sheet.',409);
    const result = [];
    for (const row of details) {
      const issuedRows = await query(
        `SELECT ROUND(SUM(quantity), 2) as sum_qty FROM st_stock_register WHERE ${Number(row.is_group)===1 ? 'item_id IN (SELECT id FROM st_additem WHERE category_id=:category_id)' : 'item_id = :item_id'} AND contract_id = :contractId AND finishedproduct_id = :itemId AND store_type = '2'`,
        { replacements: { item_id: row.item_id,category_id:row.category_id, contractId, itemId }, type: QueryTypes.SELECT }
      );
      
      const issuedQty = issuedRows[0].sum_qty || 0;
      const pendingQty = Math.max(0, row.item_qty - issuedQty);

      const inhandRows = await query(
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
        const gItems = await query(
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
        ,stock_update:settings?.stock_update==='Y'
      });
    }

    return result;
  }

  /**
   * Save IndentPO (Create Header + Details)
   */
  async saveIndentpo(dbPool, data, userId, validate) {
    const transaction = await dbPool.transaction();
    try {
      const {invalid}=require('../../utils/receiptValidation');
      await dbPool.query('SELECT id FROM designsheet WHERE contract_id=:contract_id AND item_id=:finishedproduct_id FOR UPDATE',{replacements:data,type:QueryTypes.SELECT,transaction});
      const ids=data.items.map(item=>item.item_id).sort((a,b)=>Number(a)-Number(b));
      await dbPool.query('SELECT id FROM st_additem WHERE id IN (:ids) ORDER BY id FOR UPDATE',{replacements:{ids},type:QueryTypes.SELECT,transaction});
      const existing=await dbPool.query('SELECT id FROM indentpo WHERE indent_id=:indent_id LIMIT 1 FOR UPDATE',{replacements:data,type:QueryTypes.SELECT,transaction});
      if(existing.length)throw invalid('This indent number already exists.',409);
      if(validate)await validate(transaction);
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
    if(filters.indent_id){where+=' AND i.indent_id=:indent_id';params.indent_id=filters.indent_id;}
    if(filters.search){where+=' AND (i.indent_id LIKE :search OR c.title LIKE :search OR a.item_name LIKE :search OR m.machine_name LIKE :search OR i.issued_name LIKE :search)';params.search=`%${filters.search}%`;}

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

    const from=`FROM indentpo i LEFT JOIN contracts c ON c.id=i.contract_id LEFT JOIN st_additem a ON a.id=i.finishedproduct_id LEFT JOIN machine_master m ON m.id=i.machine_id WHERE ${where}`;
    let pagination;
    if(filters.page){
      const {paginationInput,paginationResult}=require('../../utils/listPagination');
      const input=paginationInput(filters,'designsheet');Object.assign(params,{limit:input.limit,offset:input.offset});
      const [count]=await dbPool.query(`SELECT COUNT(*) AS total ${from}`,{replacements:params,type:QueryTypes.SELECT});
      pagination=paginationResult(count.total,input.page,input.limit,0);
    }
    const data=await dbPool.query(
      `SELECT 
         i.id, i.indent_id, i.issue_date, i.issued_name, i.created, i.contract_id,
         c.title as contract_name, c.workorder,
         a.item_name as product_name,
         m.machine_name
       ${from}
       ORDER BY i.id DESC ${pagination ? 'LIMIT :limit OFFSET :offset' : ''}`,
      { replacements: params, type: QueryTypes.SELECT }
    );
    return pagination ? {data,...pagination} : data;
  }

  /**
   * Get single Indentpo detail (Header + Items)
   */
  async getIndentpoDetail(dbPool, indentId) {
    const header = await dbPool.query(
      `SELECT 
         i.id, i.indent_id, i.contract_id, i.finishedproduct_id, i.machine_id,
         i.issue_date, i.issued_name, i.created, i.updated,
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
         sr.id,sr.item_id, sr.quantity,
         a.item_name as raw_material_name,
         u.unit_name
       FROM st_stock_register sr
       LEFT JOIN st_additem a ON a.id = sr.item_id
       LEFT JOIN st_measurementunits u ON u.id = a.uom
       WHERE sr.indent_id = :indentId ORDER BY sr.id ASC`,
      { replacements: { indentId }, type: QueryTypes.SELECT }
    );

    return { ...header[0], items };
  }

  async getIndentpoPdfDetail(dbPool, indentId) {
    const detail = await this.getIndentpoDetail(dbPool, indentId);
    if (!detail) return null;

    const siteDetails = await getFirstRow(
      dbPool,
      'SELECT * FROM sitesettings_details WHERE status = "Y" LIMIT 1',
      'SELECT * FROM sitesettings_details LIMIT 1'
    );

    const siteSetting = await getFirstRow(
      dbPool,
      'SELECT * FROM sitesettings WHERE status = "Y" LIMIT 1',
      'SELECT * FROM sitesettings LIMIT 1'
    );

    return {
      ...detail,
      site_details: siteDetails,
      sitesetting: siteSetting
    };
  }
}

module.exports = new IndentpoRepository();
