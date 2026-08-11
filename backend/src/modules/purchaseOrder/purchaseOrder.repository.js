const { QueryTypes } = require('sequelize');

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
      WHERE po.id = :idOrNumber OR po.purchaseorder_id = :idOrNumber
      ORDER BY po.id DESC LIMIT 1
    `;
    const rows = await dbPool.query(query, { replacements: { idOrNumber }, type: QueryTypes.SELECT });
    return rows[0] || null;
  }

  async getDetails(dbPool, idOrNumber) {
    const poQuery = `
      SELECT 
        po.id,
        po.purchaseorder_id as po_number,
        po.vendor_id,
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
        COALESCE(v.pancard_number, 'N/A') as pancard_number,
        COALESCE(v.address, 'N/A') as vendor_address,
        COALESCE(v.contact_no, 'N/A') as vendor_phone,
        COALESCE(v.email, 'N/A') as vendor_email,
        po.remark,
        po.payment_term,
        po.freight,
        po.total_amt as total_amount
      FROM st_purchaseorder po
      LEFT JOIN vendors v ON po.vendor_id = v.id
      WHERE po.id = :idOrNumber OR po.purchaseorder_id = :idOrNumber
      ORDER BY po.id DESC LIMIT 1
    `;
    
    const poRows = await dbPool.query(poQuery, { replacements: { idOrNumber }, type: QueryTypes.SELECT });
    const po = poRows[0] || null;
    
    if (!po) return null;

    const siteSettingsQuery = `SELECT * FROM sitesettings_details WHERE status = 'Y' LIMIT 1`;
    const siteSettingsRows = await dbPool.query(siteSettingsQuery, { type: QueryTypes.SELECT });
    const site_details = siteSettingsRows[0] || null;
    
    const itemsQuery = `
      SELECT 
        pod.id,
        pod.item_id,
        i.item_name,
        pod.item_qty as order_qty,
        pod.item_amt as rate,
        pod.item_base_price as price,
        pod.tax_percentage,
        pod.item_tax_amt as tax_amt,
        pod.item_total_amount as amount,
        COALESCE(pod.uom, 'KG') as uom
      FROM st_purchaseorderDetails pod
      LEFT JOIN st_additem i ON pod.item_id = i.id
      WHERE pod.poprimary_id = :poId
    `;
    
    const itemRows = await dbPool.query(itemsQuery, { replacements: { poId: po.id }, type: QueryTypes.SELECT });

    // Fetch Goods Received Data (GRNs)
    const grnQuery = `
      SELECT 
        g.id,
        g.inspection_id as grn_number,
        g.bill_no,
        DATE(g.bill_date) as bill_date,
        DATE(g.inwarddate) as inward_date,
        g.total_qty,
        g.total_amt
      FROM grn_inspection g
      WHERE g.po_id = :poNumber
    `;
    const grnRows = await dbPool.query(grnQuery, { replacements: { poNumber: po.po_number }, type: QueryTypes.SELECT });
    
    // For each GRN, fetch its items
    const grns = await Promise.all(grnRows.map(async (grn) => {
      const grnItemsQuery = `
        SELECT 
          gd.id,
          gd.item_id,
          i.item_name,
          gd.quantity as item_qty,
          gd.cost_price as rate,
          gd.rate as price,
          COALESCE(t.tax, 18) as tax_percentage,
          gd.tax as tax_amt,
          gd.amount as amount,
          COALESCE(u.unit_name, 'KG') as uom
        FROM grn_inspection_details gd
        LEFT JOIN st_additem i ON gd.item_id = i.id
        LEFT JOIN st_taxmaster t ON gd.tax_id = t.id
        LEFT JOIN st_measurementunits u ON i.uom = u.id
        WHERE gd.inspection_id = :grnNumber
      `;
      const grnItemRows = await dbPool.query(grnItemsQuery, { replacements: { grnNumber: grn.grn_number }, type: QueryTypes.SELECT });
      return { ...grn, items: grnItemRows };
    }));

    // Calculate pending quantity for each PO item based on received GRN items
    const updatedItemRows = itemRows.map(item => {
      let receivedQty = 0;
      grns.forEach(grn => {
        grn.items.forEach(grnItem => {
          if (grnItem.item_id === item.item_id) {
            receivedQty += Number(grnItem.item_qty) || 0;
          }
        });
      });
      return {
        ...item,
        pending_qty: Number(item.order_qty) - receivedQty
      };
    });

    return { po, items: updatedItemRows, site_details, grns };
  }

  async getItemHistory(dbPool, itemId) {
    const q = `
      SELECT po.purchaseorder_id as po_number, pod.inward_date as generated_date, 
             v.name as supplier, pod.item_qty as quantity, 
             pod.item_amt as price
      FROM st_purchaseorderDetails pod
      JOIN st_purchaseorder po ON pod.poprimary_id = po.id
      LEFT JOIN vendors v ON po.vendor_id = v.id
      WHERE pod.item_id = :itemId
      ORDER BY pod.inward_date DESC, pod.id DESC
      LIMIT 25
    `;
    const rows = await dbPool.query(q, { replacements: { itemId }, type: QueryTypes.SELECT });
    
    // Remove revised POs (duplicate po_number)
    const uniquePOs = [];
    const poNumbers = new Set();
    for (const row of rows) {
      if (!poNumbers.has(row.po_number)) {
        poNumbers.add(row.po_number);
        uniquePOs.push(row);
      }
    }
    
    return uniquePOs.slice(0, 5);
  }

  async listPurchaseOrders(dbPool, filters, offset, limit) {
    let whereClauses = [];
    let queryParams = {};

    if (filters.po_number) {
      whereClauses.push('po.purchaseorder_id LIKE :po_number');
      queryParams.po_number = `%${filters.po_number}%`;
    }
    if (filters.vendor_name) {
      whereClauses.push('v.name LIKE :vendor_name');
      queryParams.vendor_name = `%${filters.vendor_name}%`;
    }
    if (filters.datefrom && filters.dateto) {
      whereClauses.push('DATE(po.added_time) BETWEEN :datefrom AND :dateto');
      queryParams.datefrom = filters.datefrom;
      queryParams.dateto = filters.dateto;
    }
    if (filters.status) {
      whereClauses.push('po.postatus = :status');
      queryParams.status = filters.status;
    }

    const whereString = whereClauses.length ? 'WHERE ' + whereClauses.join(' AND ') : '';
    
    const query = `
      SELECT 
        po.id,
        po.purchaseorder_id as po_number,
        DATE(po.added_time) as po_date,
        v.id as vendor_id,
        v.name as vendor_name,
        COALESCE(v.contact_no, 'N/A') as mobile,
        po.total_qty as quantity,
        (SELECT COALESCE(SUM(item_qty), 0) FROM po_delivery_note WHERE poprimary_id = po.id) as received_qty,
        po.total_amt as amount,
        DATE(po.delivery_date) as delivery_date,
        CASE 
          WHEN po.postatus = 'O' THEN 'Open'
          WHEN po.postatus = 'C' THEN 'Closed'
          ELSE 'Active'
        END as status
      FROM st_purchaseorder po
      LEFT JOIN vendors v ON po.vendor_id = v.id
      ${whereString}
      ORDER BY po.id DESC
      LIMIT :limit OFFSET :offset
    `;

    queryParams.limit = Number(limit) || 10;
    queryParams.offset = Number(offset) || 0;
    
    const rows = await dbPool.query(query, { replacements: queryParams, type: QueryTypes.SELECT });
    return rows;
  }

  async countPurchaseOrders(dbPool, filters) {
    let whereClauses = [];
    let queryParams = {};

    if (filters.po_number) {
      whereClauses.push('po.purchaseorder_id LIKE :po_number');
      queryParams.po_number = `%${filters.po_number}%`;
    }
    if (filters.vendor_name) {
      whereClauses.push('v.name LIKE :vendor_name');
      queryParams.vendor_name = `%${filters.vendor_name}%`;
    }
    if (filters.datefrom && filters.dateto) {
      whereClauses.push('DATE(po.added_time) BETWEEN :datefrom AND :dateto');
      queryParams.datefrom = filters.datefrom;
      queryParams.dateto = filters.dateto;
    }
    if (filters.status) {
      whereClauses.push('po.postatus = :status');
      queryParams.status = filters.status;
    }

    const whereString = whereClauses.length ? 'WHERE ' + whereClauses.join(' AND ') : '';
    const query = `SELECT COUNT(*) as total FROM st_purchaseorder po LEFT JOIN vendors v ON po.vendor_id = v.id ${whereString}`;
    
    const rows = await dbPool.query(query, { replacements: queryParams, type: QueryTypes.SELECT });
    return rows[0].total;
  }

  async updatePurchaseOrder(dbPool, id, poData, transaction) {
    const query = `
      UPDATE st_purchaseorder
      SET 
        vendor_id = :vendor_id,
        delivery_date = :delivery_date,
        remarks = :remarks,
        total_qty = :total_qty,
        total_amt = :total_amt,
        is_revised = COALESCE(is_revised, 0) + 1,
        revised_date = CURRENT_TIMESTAMP
      WHERE id = :id
    `;
    const params = {
      vendor_id: poData.vendor_id,
      delivery_date: poData.delivery_date || null,
      remarks: poData.remarks || null,
      total_qty: poData.total_qty || 0,
      total_amt: poData.total_amt || 0,
      id
    };
    await dbPool.query(query, { replacements: params, type: QueryTypes.UPDATE, transaction });
  }

  async updatePurchaseOrderItems(dbPool, poprimary_id, po_number, items, transaction) {
    // Delete existing items
    await dbPool.query(`DELETE FROM st_purchaseorderDetails WHERE poprimary_id = :poprimary_id`, {
      replacements: { poprimary_id }, type: QueryTypes.DELETE, transaction
    });

    // Insert new ones
    for (const item of items) {
      const query = `
        INSERT INTO st_purchaseorderDetails 
        (purchaseorder_id, poprimary_id, item_id, tax_id, item_amt, item_qty, item_base_price, tax_percentage, item_tax_amt, item_total_amount)
        VALUES (:po_number, :poprimary_id, :item_id, :tax_id, :item_amt, :item_qty, :item_base_price, :tax_percentage, :item_tax_amt, :item_total_amount)
      `;
      await dbPool.query(query, {
        replacements: {
          po_number,
          poprimary_id,
          item_id: item.item_id,
          tax_id: item.tax_id || null,
          item_amt: item.item_amt || 0,
          item_qty: item.item_qty || 0,
          item_base_price: item.item_base_price || 0,
          tax_percentage: item.tax_percentage || 0,
          item_tax_amt: item.item_tax_amt || 0,
          item_total_amount: item.item_total_amount || 0
        },
        type: QueryTypes.INSERT,
        transaction
      });
    }
  }

  async addDeliveryNote(dbPool, poprimary_id, po_number, vendor_id, items, remarks, transaction) {
    for (const item of items) {
      // If received_qty > 0
      if (item.received_qty > 0) {
        // Construct the note capturing accepted/rejected and remarks
        const finalNote = `Accepted: ${item.accepted_qty || 0}, Rejected: ${item.rejected_qty || 0}. Remarks: ${remarks || ''}`;

        const query = `
          INSERT INTO po_delivery_note 
          (po_id, poprimary_id, vendor_id, item_id, item_qty, delivery_date, delivery_note)
          VALUES (:po_number, :poprimary_id, :vendor_id, :item_id, :item_qty, CURRENT_TIMESTAMP, :delivery_note)
        `;
        await dbPool.query(query, {
          replacements: {
            po_number,
            poprimary_id,
            vendor_id,
            item_id: item.item_id,
            item_qty: item.received_qty,
            delivery_note: finalNote
          },
          type: QueryTypes.INSERT,
          transaction
        });
      }
    }
  }

  async deletePurchaseOrder(dbPool, id, transaction) {
    await dbPool.query('DELETE FROM st_purchaseorderDetails WHERE poprimary_id = :id', { replacements: { id }, type: QueryTypes.DELETE, transaction });
    await dbPool.query('DELETE FROM po_delivery_note WHERE poprimary_id = :id', { replacements: { id }, type: QueryTypes.DELETE, transaction });
    await dbPool.query('DELETE FROM st_purchaseorder WHERE id = :id', { replacements: { id }, type: QueryTypes.DELETE, transaction });
  }

  async getNextPoNumber(dbPool) {
    const currentYear = new Date().getFullYear();
    const currentMonth = new Date().getMonth() + 1;
    let financialYearStart = currentMonth >= 4 ? `${currentYear}-04-01` : `${currentYear - 1}-04-01`;

    const query = `
      SELECT purchaseorder_id 
      FROM st_purchaseorder 
      WHERE DATE(added_time) >= :financialYearStart AND is_revised = '0' 
      ORDER BY id DESC LIMIT 1
    `;
    const rows = await dbPool.query(query, { replacements: { financialYearStart }, type: QueryTypes.SELECT });
    
    if (rows.length > 0 && rows[0].purchaseorder_id) {
      const po_id = rows[0].purchaseorder_id.split('-');
      if (po_id.length > 1) {
        return `${po_id[0]}-${parseInt(po_id[1]) + 1}`;
      }
    }
    
    // Default fallback format: YY(YY+1)-1 (e.g. 2627-1)
    const yrStart = parseInt(financialYearStart.substring(2, 4));
    const yrEnd = yrStart + 1;
    return `${yrStart}${yrEnd}-1`;
  }

  async createPurchaseOrder(dbPool, poData, items, transaction) {
    // 1. Insert into st_purchaseorder
    const poQuery = `
      INSERT INTO st_purchaseorder (
        purchaseorder_id, vendor_id, vendorshipaddress, delivery_date, freight, 
        payment_terms, transit_insurance, remark, payment_term, total_qty, total_tax, total_amt, 
        added_time, revised_date, issue_vendor, postatus, is_revised
      ) VALUES (:purchaseorder_id, :vendor_id, :vendorshipaddress, :delivery_date, :freight, 
        :payment_terms, :transit_insurance, :remark, :payment_term, :total_qty, :total_tax, :total_amt, 
        :added_time, :revised_date, :issue_vendor, :postatus, :is_revised)
    `;
    const poParams = {
      purchaseorder_id: poData.purchaseorder_id,
      vendor_id: poData.vendor_id,
      vendorshipaddress: poData.vendorshipaddress || '',
      delivery_date: poData.delivery_date || new Date(),
      freight: poData.freight || '',
      payment_terms: poData.payment_terms || '',
      transit_insurance: poData.transit_insurance || '',
      remark: poData.remark || '',
      payment_term: poData.payment_term || '',
      total_qty: poData.total_qty || 0,
      total_tax: poData.total_tax || 0,
      total_amt: poData.total_amt || 0,
      added_time: poData.added_time || new Date(),
      revised_date: poData.added_time || new Date(), // revised_date initialized same as added
      issue_vendor: poData.issue_vendor || 'N',
      postatus: poData.postatus || 'O',
      is_revised: 0
    };
    
    const poResult = await dbPool.query(poQuery, { replacements: poParams, type: QueryTypes.INSERT, transaction });
    const poprimary_id = poResult[0];

    // 2. Insert into st_purchaseorderDetails
    for (const item of items) {
      const itemQuery = `
        INSERT INTO st_purchaseorderDetails (
          purchaseorder_id, poprimary_id, item_id, tax_id, item_amt, item_qty, item_base_price, 
          tax_percentage, item_tax_amt, item_total_amount, uom, weight, volume, inward_date, revised_date
        ) VALUES (:purchaseorder_id, :poprimary_id, :item_id, :tax_id, :item_amt, :item_qty, :item_base_price, 
          :tax_percentage, :item_tax_amt, :item_total_amount, :uom, :weight, :volume, :inward_date, :revised_date)
      `;
      const itemParams = {
        purchaseorder_id: poData.purchaseorder_id,
        poprimary_id,
        item_id: item.item_id,
        tax_id: item.tax_id || null,
        item_amt: item.item_amt || 0,
        item_qty: item.item_qty || 0,
        item_base_price: item.item_base_price || 0,
        tax_percentage: item.tax_percentage || 0,
        item_tax_amt: item.item_tax_amt || 0,
        item_total_amount: item.item_total_amount || 0,
        uom: item.uom || '',
        weight: item.weight || '',
        volume: item.volume || '',
        inward_date: poData.added_time || new Date(),
        revised_date: poData.added_time || new Date()
      };
      await dbPool.query(itemQuery, { replacements: itemParams, type: QueryTypes.INSERT, transaction });
    }
    
    return { poprimary_id, purchaseorder_id: poData.purchaseorder_id };
  }
}

module.exports = new PurchaseOrderRepository();
