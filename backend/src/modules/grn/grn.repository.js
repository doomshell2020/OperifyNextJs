const { QueryTypes } = require('sequelize');
const {listOrder} = require('../../utils/listPagination');

class GrnRepository {
  async getList(dbPool, { offset, limit, po_id, vendor_id, from_date, to_date, sort:sortName, direction }) {
    let query = `
      SELECT 
        grn.id,
        grn.purchaseorder_id,
        grn.inwarddate,
        grn.bill_no,
        grn.bill_date,
        grn.total_qty,
        grn.total_amt,
        grn.status,
        v.name as vendor_name,
        v.id as vendor_id
      FROM st_goodsreceive grn
      LEFT JOIN vendors v ON grn.vendor_id = v.id
      WHERE 1=1
    `;
    const params = {};

    if (po_id) {
      query += ` AND grn.purchaseorder_id = :po_id`;
      params.po_id = po_id;
    }
    if (vendor_id) {
      query += ` AND grn.vendor_id = :vendor_id`;
      params.vendor_id = vendor_id;
    }
    if (from_date && from_date !== '1970-01-01') {
      query += ` AND DATE(grn.inwarddate) >= :from_date`;
      params.from_date = from_date;
    }
    if (to_date && to_date !== '1970-01-01') {
      query += ` AND DATE(grn.inwarddate) <= :to_date`;
      params.to_date = to_date;
    }

    let countQuery = query.replace(/SELECT[\s\S]*?FROM/, 'SELECT COUNT(*) as total FROM');
    const countRows = await dbPool.query(countQuery, { replacements: params, type: QueryTypes.SELECT });

    const sort = listOrder(sortName,direction,{id:'grn.id',purchaseorder_id:'grn.purchaseorder_id',inwarddate:'grn.inwarddate',bill_no:'grn.bill_no',bill_date:'grn.bill_date',total_qty:'grn.total_qty',total_amt:'grn.total_amt'},'grn.id');
    query += ` ORDER BY ${sort.column} ${sort.direction}${sort.column === 'grn.id' ? '' : ', grn.id DESC'} LIMIT :limit OFFSET :offset`;
    params.limit = parseInt(limit);
    params.offset = parseInt(offset);
    
    const rows = await dbPool.query(query, { replacements: params, type: QueryTypes.SELECT });
    
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
      WHERE ins.inspection_id = :inspectionId AND ins.status = 'Y'
      ORDER BY po.id DESC
      LIMIT 1
    `;
    const rows = await dbPool.query(query, { replacements: { inspectionId }, type: QueryTypes.SELECT });
    return rows[0] || null;
  }

  async getInspectionItems(dbPool, inspectionId) {
    const query = `
      SELECT 
        gd.*,
        i.item_name,
        COALESCE(u.unit_name, 'KG') as uom,
        COALESCE(t.tax, 0) as item_tax
      FROM grn_inspection_details gd
      LEFT JOIN st_taxmaster t ON gd.tax_id=t.id
      LEFT JOIN st_additem i ON gd.item_id = i.id
      LEFT JOIN st_measurementunits u ON i.uom = u.id
      WHERE gd.inspection_id = :inspectionId
    `;
    return await dbPool.query(query, { replacements: { inspectionId }, type: QueryTypes.SELECT });
  }

  async getGrnDetails(dbPool, id) {
    const query = `
      SELECT 
        grn.*,
        v.name as vendor_name,
        v.gst_number as vendor_gstin
      FROM st_goodsreceive grn
      LEFT JOIN vendors v ON grn.vendor_id = v.id
      WHERE grn.id = :id
    `;
    const rows = await dbPool.query(query, { replacements: { id }, type: QueryTypes.SELECT });
    return rows[0] || null;
  }

  async getGrnItems(dbPool, goodsId) {
    const query = `
      SELECT 
        sr.*,
        i.item_name,
        u.unit_name as uom,
        (SELECT pod.item_qty FROM st_purchaseorderdetails pod
         WHERE pod.purchaseorder_id = sr.po_id AND pod.item_id = sr.item_id
         ORDER BY pod.id DESC LIMIT 1) as order_qty
      FROM st_stock_register sr
      LEFT JOIN st_additem i ON sr.item_id = i.id
      LEFT JOIN st_measurementunits u ON i.uom = u.id
      WHERE sr.goods_id = :goodsId AND sr.store_type = '1' AND sr.status != 'N'
      ORDER BY sr.id ASC
    `;
    return await dbPool.query(query, { replacements: { goodsId }, type: QueryTypes.SELECT });
  }

  async getPdfSettings(dbPool) {
    const site = await dbPool.query("SELECT * FROM sitesettings_details WHERE status = 'Y' LIMIT 1", {type:QueryTypes.SELECT});
    const setting = await dbPool.query('SELECT * FROM sitesettings LIMIT 1', {type:QueryTypes.SELECT});
    return {site_details:site[0] || {}, sitesetting:setting[0] || {}};
  }

  async getPdfTaxes(dbPool, taxId) {
    // Match gettaxnameparent()/gettaxname2() in CommanHelper, including a zero tax.
    return dbPool.query('SELECT tax FROM st_taxmaster WHERE id = :taxId ORDER BY id DESC', {replacements:{taxId:taxId ?? 0}, type:QueryTypes.SELECT});
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
    const params = {};

    if (po_id) {
      query += ` AND grn.purchaseorder_id = :po_id`;
      params.po_id = po_id;
    }
    if (vendor_id) {
      query += ` AND grn.vendor_id = :vendor_id`;
      params.vendor_id = vendor_id;
    }
    if (from_date && from_date !== '1970-01-01') {
      query += ` AND DATE(grn.inwarddate) >= :from_date`;
      params.from_date = from_date;
    }
    if (to_date && to_date !== '1970-01-01') {
      query += ` AND DATE(grn.inwarddate) <= :to_date`;
      params.to_date = to_date;
    }

    query += ` ORDER BY grn.inwarddate DESC, grn.id DESC`;
    
    return await dbPool.query(query, { replacements: params, type: QueryTypes.SELECT });
  }
}

module.exports = new GrnRepository();
