const db = require('../../config/db');

class ContractRepository {
  async findFiltered(dbPool, filters = {}) {
    let query = `
      SELECT 
        c.id,
        c.title,
        c.workorder,
        c.cost,
        c.contract_start_date,
        c.contract_end_date,
        c.issuedate,
        c.description,
        c.status,
        c.added_time,
        v.name as vendor_name
      FROM contracts c
      LEFT JOIN vendors v ON c.supplier_id = v.id
      WHERE 1=1
    `;
    const params = [];

    if (filters.contract_name) {
      query += ` AND (c.title LIKE ? OR c.workorder LIKE ?)`;
      params.push(`%${filters.contract_name}%`, `%${filters.contract_name}%`);
    }
    if (filters.vendor_name) {
      query += ` AND v.name LIKE ?`;
      params.push(`%${filters.vendor_name}%`);
    }
    if (filters.cost) {
      query += ` AND c.cost LIKE ?`;
      params.push(`%${filters.cost}%`);
    }
    if (filters.datefrom && filters.datefrom !== '1970-01-01') {
      query += ` AND DATE(c.contract_start_date) >= ?`;
      params.push(filters.datefrom);
    }
    if (filters.dateto && filters.dateto !== '1970-01-01') {
      query += ` AND DATE(c.contract_end_date) <= ?`;
      params.push(filters.dateto);
    }

    query += ` ORDER BY c.id DESC`;

    const [rows] = await dbPool.execute(query, params);
    return rows;
  }

  async findById(dbPool, id) {
    const query = `
      SELECT 
        c.id,
        c.title,
        c.workorder,
        c.cost,
        c.operation_cost,
        c.labour_cost,
        c.description,
        c.status,
        c.contract_start_date,
        c.contract_end_date,
        c.issuedate,
        v.name as vendor_name,
        COALESCE(v.gst_number, 'N/A') as gst_number
      FROM contracts c
      LEFT JOIN vendors v ON c.supplier_id = v.id
      WHERE c.id = ?
    `;
    const [rows] = await dbPool.execute(query, [id]);
    return rows[0] || null;
  }

  async findItemsByContractId(dbPool, contractId) {
    const query = `
      SELECT 
        bfp.id,
        bfp.product_id,
        bfp.price,
        bfp.quantity,
        i.item_name,
        COALESCE(u.unit_name, 'KG') as uom,
        (
          SELECT COALESCE(SUM(plannedqty), 0)
          FROM productionorder po
          WHERE po.contract_id = bfp.contract_id AND po.item_id = bfp.product_id
        ) as planned_qty,
        0 as prepared_qty
      FROM bom_finisedproduct bfp
      LEFT JOIN st_additem i ON bfp.product_id = i.id
      LEFT JOIN st_measurementunits u ON i.uom = u.id
      WHERE bfp.contract_id = ?
    `;
    const [rows] = await dbPool.execute(query, [contractId]);
    return rows;
  }

  async findDesignSheetDetails(dbPool, contractId, productId) {
    // 1. Find designsheetno
    const [designSheet] = await dbPool.execute(
      `SELECT designsheetno FROM designsheet WHERE contract_id = ? AND item_id = ? LIMIT 1`,
      [contractId, productId]
    );

    if (designSheet.length === 0) return [];
    const sheetNo = designSheet[0].designsheetno;

    // 2. Fetch design sheet details
    const [details] = await dbPool.execute(
      `SELECT dsd.item_id, dsd.item_qty as as_per_design, dsd.is_group, a.item_name, a.category_id
       FROM designsheetdetails dsd
       JOIN st_additem a ON a.id = dsd.item_id
       WHERE dsd.designsheetno = ?
       ORDER BY dsd.is_group ASC`,
      [sheetNo]
    );

    const result = [];
    for (const row of details) {
      let issuedItems = [];
      let totalIssued = 0;

      if (row.is_group == 1 && row.category_id) {
        // Fetch all actual issued stock items belonging to this category
        const [issuedRows] = await dbPool.execute(
          `SELECT s.item_id, a.item_name, ROUND(SUM(s.quantity), 2) as issued_qty
           FROM st_stock_register s
           JOIN st_additem a ON a.id = s.item_id
           WHERE s.contract_id = ? AND s.finishedproduct_id = ? AND s.store_type = '2' 
             AND a.category_id = ?
           GROUP BY s.item_id`,
          [contractId, productId, row.category_id]
        );
        issuedItems = issuedRows;
      } else {
        // Fetch issued stock for this specific item
        const [issuedRows] = await dbPool.execute(
          `SELECT s.item_id, a.item_name, ROUND(SUM(s.quantity), 2) as issued_qty
           FROM st_stock_register s
           JOIN st_additem a ON a.id = s.item_id
           WHERE s.contract_id = ? AND s.finishedproduct_id = ? AND s.store_type = '2' 
             AND s.item_id = ?
           GROUP BY s.item_id`,
          [contractId, productId, row.item_id]
        );
        issuedItems = issuedRows;
      }

      totalIssued = issuedItems.reduce((sum, item) => sum + (Number(item.issued_qty) || 0), 0);

      result.push({
        id: row.item_id,
        item_name: row.item_name,
        as_per_design: row.as_per_design,
        total_issued: totalIssued,
        pending_qty: Math.max(0, row.as_per_design - totalIssued),
        issued_items: issuedItems
      });
    }

    return result;
  }

  async findProductionOrdersByContractId(dbPool, contractId) {
    const query = `
      SELECT 
        po.po_id,
        po.issuedate,
        po.plannedqty,
        po.startdate,
        po.enddate,
        po.status,
        i.item_name as product_name,
        0 as prepared_qty
      FROM productionorder po
      LEFT JOIN st_additem i ON po.item_id = i.id
      WHERE po.contract_id = ?
    `;
    const [rows] = await dbPool.execute(query, [contractId]);
    return rows;
  }

  async findInspectionReportsByContractId(dbPool, contractId) {
    const query = `
      SELECT 
        id as s_no,
        name as inspector_name,
        inspection_date
      FROM st_inspection_report
      WHERE work_order_no = ?
    `;
    const [rows] = await dbPool.execute(query, [contractId]);
    return rows;
  }

  async getFormData(dbPool) {
    const [vendors] = await dbPool.execute('SELECT id, name FROM vendors ORDER BY name ASC');
    const [items] = await dbPool.execute(`
      SELECT id, item_name as name 
      FROM st_additem 
      WHERE itemtype = 'FinishedProduct' 
         OR category_id IN (SELECT id FROM st_categorymaster WHERE category_name LIKE '%FINISH%')
      ORDER BY item_name ASC
    `);
    return { vendors, items };
  }

  async createContract(dbConnection, data) {
    const query = `
      INSERT INTO contracts (
        supplier_id, title, workorder, cost, operation_cost, labour_cost,
        issuedate, contract_start_date, contract_end_date, description, status
      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Y')
    `;
    const params = [
      data.supplier_id || null,
      data.title || null,
      data.workorder || null,
      data.cost || null,
      data.operation_cost || null,
      data.labour_cost || null,
      data.issuedate || null,
      data.contract_start_date || null,
      data.contract_end_date || null,
      data.description || null
    ];
    const [result] = await dbConnection.execute(query, params);
    return result.insertId;
  }

  async addFinishedProduct(dbConnection, contractId, product) {
    const query = `
      INSERT INTO bom_finisedproduct (
        contract_id, product_id, price, quantity
      ) VALUES (?, ?, ?, ?)
    `;
    const params = [
      contractId,
      product.product_id,
      product.price || '0',
      product.quantity || '0'
    ];
    await dbConnection.execute(query, params);
  }
}

module.exports = new ContractRepository();
