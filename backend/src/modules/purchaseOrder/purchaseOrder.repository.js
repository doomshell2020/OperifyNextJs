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
      ORDER BY po.id DESC LIMIT 1
    `;
    const [rows] = await dbPool.execute(query, [idOrNumber, idOrNumber]);
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
      WHERE po.id = ? OR po.purchaseorder_id = ?
      ORDER BY po.id DESC LIMIT 1
    `;
    
    const [poRows] = await dbPool.execute(poQuery, [idOrNumber, idOrNumber]);
    const po = poRows[0] || null;
    
    if (!po) return null;

    const siteSettingsQuery = `SELECT * FROM sitesettings_details WHERE status = 'Y' LIMIT 1`;
    const [siteSettingsRows] = await dbPool.execute(siteSettingsQuery);
    const site_details = siteSettingsRows[0] || null;
    
    if (!po) return null;
    
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
      WHERE pod.poprimary_id = ?
    `;
    
    const [itemRows] = await dbPool.execute(itemsQuery, [po.id]);

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
      WHERE g.po_id = ?
    `;
    const [grnRows] = await dbPool.execute(grnQuery, [po.po_number]);
    
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
        WHERE gd.inspection_id = ?
      `;
      const [grnItemRows] = await dbPool.execute(grnItemsQuery, [grn.grn_number]);
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

  async getItemHistory(connection, itemId) {
    const q = `
      SELECT po.purchaseorder_id as po_number, pod.inward_date as generated_date, 
             v.name as supplier, pod.item_qty as quantity, 
             pod.item_amt as price
      FROM st_purchaseorderDetails pod
      JOIN st_purchaseorder po ON pod.poprimary_id = po.id
      LEFT JOIN vendors v ON po.vendor_id = v.id
      WHERE pod.item_id = ?
      ORDER BY pod.inward_date DESC, pod.id DESC
      LIMIT 25
    `;
    const [rows] = await connection.execute(q, [itemId]);
    
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
    let queryParams = [];

    if (filters.po_number) {
      whereClauses.push('po.purchaseorder_id LIKE ?');
      queryParams.push(`%${filters.po_number}%`);
    }
    if (filters.vendor_name) {
      whereClauses.push('v.name LIKE ?');
      queryParams.push(`%${filters.vendor_name}%`);
    }
    if (filters.datefrom && filters.dateto) {
      whereClauses.push('DATE(po.added_time) BETWEEN ? AND ?');
      queryParams.push(filters.datefrom, filters.dateto);
    }
    if (filters.status) {
      whereClauses.push('po.postatus = ?');
      queryParams.push(filters.status); // O, C, etc.
    }

    const whereString = whereClauses.length ? 'WHERE ' + whereClauses.join(' AND ') : '';
    
    // For 'Received Qty', we sum the delivery note qty
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
      LIMIT ${Number(limit) || 10} OFFSET ${Number(offset) || 0}
    `;

    
    const [rows] = await dbPool.execute(query, queryParams);
    return rows;
  }

  async countPurchaseOrders(dbPool, filters) {
    let whereClauses = [];
    let queryParams = [];

    if (filters.po_number) {
      whereClauses.push('po.purchaseorder_id LIKE ?');
      queryParams.push(`%${filters.po_number}%`);
    }
    if (filters.vendor_name) {
      whereClauses.push('v.name LIKE ?');
      queryParams.push(`%${filters.vendor_name}%`);
    }
    if (filters.datefrom && filters.dateto) {
      whereClauses.push('DATE(po.added_time) BETWEEN ? AND ?');
      queryParams.push(filters.datefrom, filters.dateto);
    }
    if (filters.status) {
      whereClauses.push('po.postatus = ?');
      queryParams.push(filters.status);
    }

    const whereString = whereClauses.length ? 'WHERE ' + whereClauses.join(' AND ') : '';
    const query = `SELECT COUNT(*) as total FROM st_purchaseorder po LEFT JOIN vendors v ON po.vendor_id = v.id ${whereString}`;
    
    const [rows] = await dbPool.execute(query, queryParams);
    return rows[0].total;
  }

  async updatePurchaseOrder(connection, id, poData) {
    const query = `
      UPDATE st_purchaseorder
      SET 
        vendor_id = ?,
        delivery_date = ?,
        remarks = ?,
        total_qty = ?,
        total_amt = ?,
        is_revised = COALESCE(is_revised, 0) + 1,
        revised_date = CURRENT_TIMESTAMP
      WHERE id = ?
    `;
    const params = [
      poData.vendor_id,
      poData.delivery_date || null,
      poData.remarks || null,
      poData.total_qty || 0,
      poData.total_amt || 0,
      id
    ];
    await connection.execute(query, params);
  }

  async updatePurchaseOrderItems(connection, poprimary_id, po_number, items) {
    // Delete existing items
    await connection.execute(`DELETE FROM st_purchaseorderDetails WHERE poprimary_id = ?`, [poprimary_id]);

    // Insert new ones
    for (const item of items) {
      const query = `
        INSERT INTO st_purchaseorderDetails 
        (purchaseorder_id, poprimary_id, item_id, tax_id, item_amt, item_qty, item_base_price, tax_percentage, item_tax_amt, item_total_amount)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
      `;
      await connection.execute(query, [
        po_number,
        poprimary_id,
        item.item_id,
        item.tax_id || null,
        item.item_amt || 0,
        item.item_qty || 0,
        item.item_base_price || 0,
        item.tax_percentage || 0,
        item.item_tax_amt || 0,
        item.item_total_amount || 0
      ]);
    }
  }

  async addDeliveryNote(connection, poprimary_id, po_number, vendor_id, items, remarks) {
    for (const item of items) {
      // If received_qty > 0
      if (item.received_qty > 0) {
        // Construct the note capturing accepted/rejected and remarks
        const finalNote = `Accepted: ${item.accepted_qty || 0}, Rejected: ${item.rejected_qty || 0}. Remarks: ${remarks || ''}`;

        const query = `
          INSERT INTO po_delivery_note 
          (po_id, poprimary_id, vendor_id, item_id, item_qty, delivery_date, delivery_note)
          VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP, ?)
        `;
        await connection.execute(query, [
          po_number,
          poprimary_id,
          vendor_id,
          item.item_id,
          item.received_qty,
          finalNote
        ]);
      }
    }
  }

  async deletePurchaseOrder(connection, id) {
    await connection.execute('DELETE FROM st_purchaseorderDetails WHERE poprimary_id = ?', [id]);
    await connection.execute('DELETE FROM po_delivery_note WHERE poprimary_id = ?', [id]);
    await connection.execute('DELETE FROM st_purchaseorder WHERE id = ?', [id]);
  }

  async getNextPoNumber(dbPool) {
    const currentYear = new Date().getFullYear();
    const currentMonth = new Date().getMonth() + 1;
    let financialYearStart = currentMonth >= 4 ? `${currentYear}-04-01` : `${currentYear - 1}-04-01`;

    const query = `
      SELECT purchaseorder_id 
      FROM st_purchaseorder 
      WHERE DATE(added_time) >= ? AND is_revised = '0' 
      ORDER BY id DESC LIMIT 1
    `;
    const [rows] = await dbPool.execute(query, [financialYearStart]);
    
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

  async createPurchaseOrder(connection, poData, items) {
    // 1. Insert into st_purchaseorder
    const poQuery = `
      INSERT INTO st_purchaseorder (
        purchaseorder_id, vendor_id, vendorshipaddress, delivery_date, freight, 
        payment_terms, transit_insurance, remark, payment_term, total_qty, total_tax, total_amt, 
        added_time, revised_date, issue_vendor, postatus, is_revised
      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    `;
    const poParams = [
      poData.purchaseorder_id,
      poData.vendor_id,
      poData.vendorshipaddress || '',
      poData.delivery_date || new Date(),
      poData.freight || '',
      poData.payment_terms || '',
      poData.transit_insurance || '',
      poData.remark || '',
      poData.payment_term || '',
      poData.total_qty || 0,
      poData.total_tax || 0,
      poData.total_amt || 0,
      poData.added_time || new Date(),
      poData.added_time || new Date(), // revised_date initialized same as added
      poData.issue_vendor || 'N',
      poData.postatus || 'O',
      0 // is_revised
    ];
    
    const [poResult] = await connection.execute(poQuery, poParams);
    const poprimary_id = poResult.insertId;

    // 2. Insert into st_purchaseorderDetails
    for (const item of items) {
      const itemQuery = `
        INSERT INTO st_purchaseorderDetails (
          purchaseorder_id, poprimary_id, item_id, tax_id, item_amt, item_qty, item_base_price, 
          tax_percentage, item_tax_amt, item_total_amount, uom, weight, volume, inward_date, revised_date
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
      `;
      const itemParams = [
        poData.purchaseorder_id,
        poprimary_id,
        item.item_id,
        item.tax_id || null,
        item.item_amt || 0,
        item.item_qty || 0,
        item.item_base_price || 0,
        item.tax_percentage || 0,
        item.item_tax_amt || 0,
        item.item_total_amount || 0,
        item.uom || '',
        item.weight || '',
        item.volume || '',
        poData.added_time || new Date(),
        poData.added_time || new Date()
      ];
      await connection.execute(itemQuery, itemParams);
    }
    
    return { poprimary_id, purchaseorder_id: poData.purchaseorder_id };
  }
}

module.exports = new PurchaseOrderRepository();
