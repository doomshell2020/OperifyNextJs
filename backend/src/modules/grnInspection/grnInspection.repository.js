const { QueryTypes } = require('sequelize');
const {listOrder} = require('../../utils/listPagination');

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
    if (filters.vendor_name && !filters.vendor_id) {
      baseQuery += ' AND v.name LIKE :vendor_name';
      params.vendor_name = `%${filters.vendor_name.trim()}%`;
    }
    if (filters.bill_no) {
      baseQuery += ` AND g.bill_no LIKE :bill_no`;
      params.bill_no = `%${filters.bill_no}%`;
    }
    if (filters.po_id || filters.purchaseorder_id) {
      baseQuery += ` AND g.po_id = :po_id`;
      params.po_id = filters.po_id || filters.purchaseorder_id;
    }
    for (const [key,operator] of [['from_date','>='],['to_date','<=']]) {
      const value=filters[key] || filters[key === 'from_date' ? 'datefrom' : 'dateto'];
      if (value && value !== '1970-01-01') {baseQuery += ` AND DATE(g.inwarddate) ${operator} :${key}`;params[key]=value;}
    }

    const countQuery = `SELECT COUNT(*) as count ${baseQuery}`;
    const countRows = await dbPool.query(countQuery, { replacements: params, type: QueryTypes.SELECT });
    const total = countRows[0].count;

    const sort = listOrder(filters.sort,filters.direction,{id:'g.id',inspection_id:'g.inspection_id',po_id:'g.po_id',inwarddate:'g.inwarddate',bill_no:'g.bill_no',bill_date:'g.bill_date',total_qty:'g.total_qty',total_amt:'g.total_amt'},'g.id');
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
        g.status,
        v.name as supplier
      ${baseQuery}
      ORDER BY ${sort.column} ${sort.direction}${sort.column === 'g.id' ? '' : ', g.id DESC'}
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
      SELECT po.id, po.purchaseorder_id, po.vendor_id, po.postatus, po.delivery_date, v.name as vendor_name
      FROM st_purchaseorder po
      LEFT JOIN vendors v ON po.vendor_id = v.id
      WHERE po.purchaseorder_id = :po_id AND po.status != 'N'
      ORDER BY po.id DESC LIMIT 1
    `;
    const poRows = await dbPool.query(poQuery, { replacements: { po_id }, type: QueryTypes.SELECT });
    if (!poRows.length) return null;
    const po = poRows[0];
    if (po.postatus === 'C') return null;

    // Fetch PO items
    const itemsQuery = `
      SELECT
        pod.id,
        pod.item_id,
        CASE WHEN i.size_id = 6 THEN i.item_name
          ELSE CONCAT(i.item_name, '-', COALESCE(s.size_name, '')) END AS item_name,
        pod.item_qty as order_qty,
        pod.item_amt as rate,
        COALESCE(t.tax, 0) as tax_rate,
        pod.tax_id,
        pod.item_base_price as order_base,
        pod.item_tax_amt as order_tax,
        pod.item_total_amount as order_amount,
        COALESCE(pod.uom, '--') as uom,
        COALESCE(receipts.received_qty, 0) as previously_received_qty,
        pod.item_qty - COALESCE(receipts.received_qty, 0) as pending_qty,
        COALESCE(schedule.item_qty, 0) as received_qty,
        schedule.id as delivery_schedule_id
      FROM st_purchaseorderDetails pod
      JOIN st_additem i ON pod.item_id = i.id
      LEFT JOIN st_sizemanager s ON i.size_id = s.id
      LEFT JOIN st_taxmaster t ON pod.tax_id = t.id
      LEFT JOIN (
        SELECT item_id, ROUND(SUM(quantity), 2) as received_qty
        FROM st_stock_register
        WHERE po_id = :po_id AND status != 'N' AND store_type = '1'
        GROUP BY item_id
      ) receipts ON receipts.item_id = pod.item_id
      LEFT JOIN po_delivery_note schedule ON schedule.id = (
        SELECT dn.id FROM po_delivery_note dn
        WHERE dn.po_id = :po_id AND dn.item_id = pod.item_id AND dn.status = 'Y'
        ORDER BY dn.delivery_date ASC, dn.id ASC LIMIT 1
      )
      WHERE pod.poprimary_id = :poprimary_id AND pod.purchaseorder_id = :po_id
      ORDER BY pod.id ASC
    `;
    const items = await dbPool.query(itemsQuery, { replacements: { poprimary_id: po.id, po_id }, type: QueryTypes.SELECT });
    return { po, items };
  }

  async create(dbPool, inspection, items) {
    return require('./grnInspection.create')(dbPool, inspection, items);
  }

}

module.exports = new GrnInspectionRepository();
