class GrnRepository {
  async getList(dbPool, { offset, limit, po_id, vendor_id, from_date, to_date }) {
    let query = `
      SELECT 
        grn.id,
        grn.purchaseorder_id,
        grn.inwarddate,
        grn.bill_no,
        grn.bill_date,
        grn.total_qty,
        grn.total_amt,
        v.name as vendor_name,
        v.id as vendor_id
      FROM st_goodsreceive grn
      LEFT JOIN vendors v ON grn.vendor_id = v.id
      WHERE 1=1
    `;
    const params = [];

    if (po_id) {
      query += ` AND grn.purchaseorder_id LIKE ?`;
      params.push(`%${po_id}%`);
    }
    if (vendor_id) {
      query += ` AND grn.vendor_id = ?`;
      params.push(vendor_id);
    }
    if (from_date) {
      query += ` AND grn.inwarddate >= ?`;
      params.push(from_date);
    }
    if (to_date) {
      query += ` AND grn.inwarddate <= ?`;
      params.push(to_date);
    }

    query += ` ORDER BY grn.id DESC LIMIT ? OFFSET ?`;
    
    // Convert limit and offset to numbers for MySQL
    const [rows] = await dbPool.execute(query, [...params, limit.toString(), offset.toString()]);
    
    // Count query
    let countQuery = `
      SELECT COUNT(*) as total 
      FROM st_goodsreceive grn 
      WHERE 1=1
    `;
    if (po_id) countQuery += ` AND grn.purchaseorder_id LIKE '%${po_id}%'`;
    if (vendor_id) countQuery += ` AND grn.vendor_id = '${vendor_id}'`;
    if (from_date) countQuery += ` AND grn.inwarddate >= '${from_date}'`;
    if (to_date) countQuery += ` AND grn.inwarddate <= '${to_date}'`;
    
    const [countRows] = await dbPool.execute(countQuery);
    
    return {
      data: rows,
      total: countRows[0].total
    };
  }

  async getInspectionDetails(dbPool, inspectionId) {
    const query = `
      SELECT 
        ins.*,
        po.id as po_pk_id,
        v.name as vendor_name
      FROM grn_inspection ins
      LEFT JOIN st_purchaseorder po ON ins.po_id = po.purchaseorder_id
      LEFT JOIN vendors v ON ins.vendor_id = v.id
      WHERE ins.inspection_id = ? AND ins.status = 'Y'
      ORDER BY po.id DESC
      LIMIT 1
    `;
    const [rows] = await dbPool.execute(query, [inspectionId]);
    return rows[0] || null;
  }

  async getInspectionItems(dbPool, inspectionId) {
    const query = `
      SELECT 
        gd.*,
        i.item_name,
        COALESCE(u.unit_name, 'KG') as uom,
        COALESCE(i.tax, 18) as item_tax
      FROM grn_inspection_details gd
      LEFT JOIN st_additem i ON gd.item_id = i.id
      LEFT JOIN st_measurementunits u ON i.uom = u.id
      WHERE gd.inspection_id = ?
    `;
    const [rows] = await dbPool.execute(query, [inspectionId]);
    return rows;
  }

  async getGrnDetails(dbPool, id) {
    const query = `
      SELECT 
        grn.*,
        v.name as vendor_name,
        v.gst_number as vendor_gstin
      FROM st_goodsreceive grn
      LEFT JOIN vendors v ON grn.vendor_id = v.id
      WHERE grn.id = ?
    `;
    const [rows] = await dbPool.execute(query, [id]);
    return rows[0] || null;
  }

  async getGrnItems(dbPool, goodsId) {
    const query = `
      SELECT 
        sr.*,
        i.item_name,
        COALESCE(u.unit_name, 'KG') as uom
      FROM st_stock_register sr
      LEFT JOIN st_additem i ON sr.item_id = i.id
      LEFT JOIN st_measurementunits u ON i.uom = u.id
      WHERE sr.goods_id = ? AND sr.store_type = '1'
    `;
    const [rows] = await dbPool.execute(query, [goodsId]);
    return rows;
  }
  async exportGrns(dbPool, filters) {
    const { po_id, vendor_id, from_date, to_date } = filters;
    
    let query = `
      SELECT 
        grn.id as grn_id,
        grn.inwarddate,
        grn.purchaseorder_id as po_no,
        grn.bill_no,
        grn.bill_date,
        i.item_name as product_name,
        v.name as vendor_name,
        po.total_qty as po_total_qty,
        sr.quantity as received_qty,
        dn.item_qty as scheduled_qty,
        dn.delivery_date as scheduled_date,
        grn.total_amt
      FROM st_goodsreceive grn
      LEFT JOIN vendors v ON grn.vendor_id = v.id
      LEFT JOIN (
        SELECT purchaseorder_id, total_qty 
        FROM st_purchaseorder 
        WHERE id IN (
          SELECT MAX(id) FROM st_purchaseorder WHERE status != 'N' GROUP BY purchaseorder_id
        )
      ) po ON grn.purchaseorder_id = po.purchaseorder_id
      LEFT JOIN st_stock_register sr ON sr.po_id = grn.purchaseorder_id AND sr.store_type = '1'
      LEFT JOIN st_additem i ON sr.item_id = i.id
      LEFT JOIN po_delivery_note dn ON dn.po_id = grn.purchaseorder_id AND dn.id = sr.delivery_schedule_id
      WHERE 1=1
    `;
    const params = [];

    if (po_id) {
      query += ` AND grn.purchaseorder_id LIKE ?`;
      params.push(`%${po_id}%`);
    }
    if (vendor_id) {
      query += ` AND grn.vendor_id = ?`;
      params.push(vendor_id);
    }
    if (from_date) {
      query += ` AND grn.inwarddate >= ?`;
      params.push(from_date);
    }
    if (to_date) {
      query += ` AND grn.inwarddate <= ?`;
      params.push(to_date);
    }

    query += ` ORDER BY grn.id DESC`;
    
    const [rows] = await dbPool.execute(query, params);
    return rows;
  }
}

module.exports = new GrnRepository();
