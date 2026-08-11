const { QueryTypes } = require('sequelize');

class GrnInspectionRepository {
  async list(dbPool, filters, limit, offset) {
    let baseQuery = `
      FROM grn_inspection g
      LEFT JOIN vendors v ON g.vendor_id = v.id
      WHERE 1=1
    `;
    const params = {};

    if (filters.vendor_id) {
      baseQuery += ` AND g.vendor_id = :vendor_id`;
      params.vendor_id = filters.vendor_id;
    }
    if (filters.bill_no) {
      baseQuery += ` AND g.bill_no LIKE :bill_no`;
      params.bill_no = `%${filters.bill_no}%`;
    }
    if (filters.po_id) {
      baseQuery += ` AND g.po_id LIKE :po_id`;
      params.po_id = `%${filters.po_id}%`;
    }

    const countQuery = `SELECT COUNT(*) as count ${baseQuery}`;
    const countRows = await dbPool.query(countQuery, { replacements: params, type: QueryTypes.SELECT });
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
      LIMIT :limit OFFSET :offset
    `;
    params.limit = parseInt(limit);
    params.offset = parseInt(offset);

    const rows = await dbPool.query(query, { replacements: params, type: QueryTypes.SELECT });
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
      WHERE g.id = :id
    `;
    const rows = await dbPool.query(query, { replacements: { id }, type: QueryTypes.SELECT });
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
      WHERE d.inspection_id = :inspectionId
    `;
    return await dbPool.query(query, { replacements: { inspectionId }, type: QueryTypes.SELECT });
  }

  async getNextId(dbPool) {
    const query = `SELECT MAX(CAST(inspection_id AS UNSIGNED)) as max_id FROM grn_inspection`;
    const rows = await dbPool.query(query, { type: QueryTypes.SELECT });
    const maxId = rows[0].max_id || 1000;
    return (parseInt(maxId) + 1).toString();
  }

  async getPoDetails(dbPool, po_id) {
    // Fetch PO main details
    const poQuery = `
      SELECT po.id, po.purchaseorder_id, po.vendor_id, v.name as vendor_name
      FROM st_purchaseorder po
      LEFT JOIN vendors v ON po.vendor_id = v.id
      WHERE po.purchaseorder_id = :po_id AND po.postatus != 'C'
      ORDER BY po.id DESC LIMIT 1
    `;
    const poRows = await dbPool.query(poQuery, { replacements: { po_id }, type: QueryTypes.SELECT });
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
      WHERE pod.poprimary_id = :poprimary_id
    `;
    const items = await dbPool.query(itemsQuery, { replacements: { poprimary_id: po.id }, type: QueryTypes.SELECT });
    return { po, items };
  }

  async create(dbPool, inspection, items) {
    const transaction = await dbPool.transaction();
    try {
      const insResult = await dbPool.models.grn_inspection.create({
        po_id: inspection.po_id ?? null,
        inspection_id: inspection.inspection_id ?? null,
        vendor_id: inspection.vendor_id ?? null,
        inwarddate: inspection.inwarddate ?? null,
        bill_no: inspection.bill_no ?? null,
        bill_date: inspection.bill_date ?? null,
        total_qty: inspection.total_qty ?? 0,
        total_tax: inspection.total_tax ?? 0,
        total_amt: inspection.total_amt ?? 0,
        remark: inspection.remark ?? ''
      }, { transaction });

      const detailsToInsert = items.map(item => ({
        purchaseorder_id: inspection.po_id ?? null,
        inspection_id: inspection.inspection_id ?? null,
        item_id: item.item_id ?? null,
        quantity: item.quantity ?? 0,
        rate: item.rate ?? 0,
        amount: item.amount ?? 0,
        tax: item.tax ?? 0
      }));

      if (detailsToInsert.length > 0) {
        await dbPool.models.grn_inspection_details.bulkCreate(detailsToInsert, { transaction });
      }

      await transaction.commit();
      return { data: { id: insResult.id, inspection_id: inspection.inspection_id } };
    } catch (error) {
      await transaction.rollback();
      throw error;
    }
  }
}

module.exports = new GrnInspectionRepository();
