class GrnInspectionRepository {
  async list(dbPool, filters, limit, offset) {
    let baseQuery = `
      FROM grn_inspection g
      LEFT JOIN vendors v ON g.vendor_id = v.id
      WHERE 1=1
    `;
    const params = [];

    if (filters.vendor_id) {
      baseQuery += ` AND g.vendor_id = ?`;
      params.push(filters.vendor_id);
    }
    if (filters.bill_no) {
      baseQuery += ` AND g.bill_no LIKE ?`;
      params.push(`%${filters.bill_no}%`);
    }
    if (filters.po_id) {
      baseQuery += ` AND g.po_id LIKE ?`;
      params.push(`%${filters.po_id}%`);
    }

    const countQuery = `SELECT COUNT(*) as count ${baseQuery}`;
    const [countRows] = await dbPool.execute(countQuery, params);
    const total = countRows[0].count;

    const query = `
      SELECT 
        g.id,
        g.inspection_id,
        g.po_id,
        g.inwarddate as inward_date,
        g.bill_no,
        g.bill_date,
        g.total_qty,
        g.total_amt,
        v.name as supplier
      ${baseQuery}
      ORDER BY g.id DESC
      LIMIT ? OFFSET ?
    `;
    params.push(limit.toString(), offset.toString());

    const [rows] = await dbPool.execute(query, params);
    return { data: rows, total };
  }

  async findById(dbPool, id) {
    const query = `
      SELECT 
        g.*,
        v.name as vendor_name,
        v.address as vendor_address,
        v.gst_number
      FROM grn_inspection g
      LEFT JOIN vendors v ON g.vendor_id = v.id
      WHERE g.id = ?
    `;
    const [rows] = await dbPool.execute(query, [id]);
    return rows[0] || null;
  }

  async getItemsByInspectionId(dbPool, inspectionId) {
    const query = `
      SELECT 
        d.*,
        i.item_name,
        COALESCE(u.unit_name, 'KG') as unit_name
      FROM grn_inspection_details d
      LEFT JOIN st_additem i ON d.item_id = i.id
      LEFT JOIN st_measurementunits u ON i.uom = u.id
      WHERE d.inspection_id = ?
    `;
    const [rows] = await dbPool.execute(query, [inspectionId]);
    return rows;
  }

  async getNextId(dbPool) {
    const query = `SELECT MAX(CAST(inspection_id AS UNSIGNED)) as max_id FROM grn_inspection`;
    const [rows] = await dbPool.execute(query);
    const maxId = rows[0].max_id || 1000;
    return (parseInt(maxId) + 1).toString();
  }

  async getPoDetails(dbPool, po_id) {
    // Fetch PO main details
    const poQuery = `
      SELECT po.id, po.purchaseorder_id, po.vendor_id, v.name as vendor_name
      FROM st_purchaseorder po
      LEFT JOIN vendors v ON po.vendor_id = v.id
      WHERE po.purchaseorder_id = ? AND po.postatus != 'C'
      ORDER BY po.id DESC LIMIT 1
    `;
    const [poRows] = await dbPool.execute(poQuery, [po_id]);
    if (!poRows.length) return null;
    const po = poRows[0];

    // Fetch PO items
    const itemsQuery = `
      SELECT 
        pod.item_id, 
        i.item_name,
        pod.item_qty as order_qty,
        pod.item_amt as rate,
        pod.tax_percentage as tax_rate
      FROM st_purchaseorderDetails pod
      JOIN st_additem i ON pod.item_id = i.id
      WHERE pod.poprimary_id = ?
    `;
    const [items] = await dbPool.execute(itemsQuery, [po.id]);
    return { po, items };
  }

  async create(dbPool, inspection, items) {
    const connection = await dbPool.getConnection();
    try {
      await connection.beginTransaction();

      const insertInspectionQuery = `
        INSERT INTO grn_inspection 
        (po_id, inspection_id, vendor_id, inwarddate, bill_no, bill_date, total_qty, total_tax, total_amt, remark)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
      `;
      const [insResult] = await connection.execute(insertInspectionQuery, [
        inspection.po_id ?? null,
        inspection.inspection_id ?? null,
        inspection.vendor_id ?? null,
        inspection.inwarddate ?? null,
        inspection.bill_no ?? null,
        inspection.bill_date ?? null,
        inspection.total_qty ?? 0,
        inspection.total_tax ?? 0,
        inspection.total_amt ?? 0,
        inspection.remark ?? ''
      ]);

      const insertDetailsQuery = `
        INSERT INTO grn_inspection_details
        (purchaseorder_id, inspection_id, item_id, quantity, rate, amount, tax)
        VALUES (?, ?, ?, ?, ?, ?, ?)
      `;

      for (const item of items) {
        await connection.execute(insertDetailsQuery, [
          inspection.po_id ?? null,
          inspection.inspection_id ?? null,
          item.item_id ?? null,
          item.quantity ?? 0,
          item.rate ?? 0,
          item.amount ?? 0,
          item.tax ?? 0
        ]);
      }

      await connection.commit();
      return { data: { id: insResult.insertId, inspection_id: inspection.inspection_id } };
    } catch (error) {
      await connection.rollback();
      throw error;
    } finally {
      connection.release();
    }
  }
}

module.exports = new GrnInspectionRepository();
