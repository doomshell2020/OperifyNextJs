class PurchaseOrderRepository {
  async getHoverDetails(dbPool, idOrNumber) {
    const query = `
      SELECT 
        po.id,
        po.purchaseorder_id as po_number,
        DATE(po.added_time) as po_date,
        v.name as vendor_name,
        CONCAT('VEN-', LPAD(v.id, 4, '0')) as vendor_code,
        COALESCE(v.contact_person, 'N/A') as contact_person,
        COALESCE(v.contact_no, 'N/A') as mobile,
        COALESCE(v.email, 'N/A') as email,
        po.total_qty as quantity,
        po.total_amt as amount,
        DATE(po.delivery_date) as delivery_date,
        CASE 
          WHEN po.postatus = 'O' THEN 'Open'
          WHEN po.postatus = 'C' THEN 'Closed'
          ELSE 'Active'
        END as status,
        COALESCE(u.user_name, po.added_by_type, 'Admin') as created_by
      FROM st_purchaseorder po
      LEFT JOIN vendors v ON po.vendor_id = v.id
      LEFT JOIN users u ON po.added_by = u.id
      WHERE po.id = ? OR po.purchaseorder_id = ?
    `;
    const [rows] = await dbPool.execute(query, [idOrNumber, idOrNumber]);
    return rows[0] || null;
  }

  async getDetails(dbPool, idOrNumber) {
    const poQuery = `
      SELECT 
        po.id,
        po.purchaseorder_id as po_number,
        DATE(po.added_time) as po_date,
        po.is_revised as amendment_no,
        DATE(po.revised_date) as amendment_date,
        DATE(po.delivery_date) as delivery_date,
        CASE 
          WHEN po.postatus = 'O' THEN 'Open'
          WHEN po.postatus = 'C' THEN 'Closed'
          ELSE 'Active'
        END as status,
        v.name as vendor_name,
        COALESCE(v.gst_number, 'N/A') as gst_number,
        po.total_amt as total_amount
      FROM st_purchaseorder po
      LEFT JOIN vendors v ON po.vendor_id = v.id
      WHERE po.id = ? OR po.purchaseorder_id = ?
    `;
    
    const [poRows] = await dbPool.execute(poQuery, [idOrNumber, idOrNumber]);
    const po = poRows[0] || null;
    
    if (!po) return null;
    
    const itemsQuery = `
      SELECT 
        pod.id,
        i.item_name,
        pod.item_qty as order_qty,
        pod.item_qty as pending_qty,
        pod.item_base_price as rate,
        pod.item_amt as price,
        pod.tax_percentage,
        pod.item_tax_amt as tax_amt,
        pod.item_total_amount as amount,
        COALESCE(pod.uom, 'KG') as uom
      FROM st_purchaseorderDetails pod
      LEFT JOIN st_additem i ON pod.item_id = i.id
      WHERE pod.poprimary_id = ? OR pod.purchaseorder_id = ?
    `;
    
    const [itemRows] = await dbPool.execute(itemsQuery, [po.id, po.po_number]);
    return { po, items: itemRows };
  }
}

module.exports = new PurchaseOrderRepository();
