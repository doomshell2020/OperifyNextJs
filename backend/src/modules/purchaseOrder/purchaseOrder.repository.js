const { QueryTypes } = require('sequelize');
const {listOrder} = require('../../utils/listPagination');

class PurchaseOrderRepository {
  getDisplayNumber(poNumber, amendmentNo) {
    const revision = Number(amendmentNo) || 0;
    return revision > 0 ? `${poNumber} R-${revision}` : poNumber;
  }

  async getHoverDetails(dbPool, idOrNumber) {
    const query = `
      SELECT 
        po.id,
        po.purchaseorder_id as po_number,
        po.is_revised as amendment_no,
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
    const row = rows[0] || null;
    if (!row) return null;
    return {
      ...row,
      display_po_number: this.getDisplayNumber(row.po_number, row.amendment_no)
    };
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
        po.payment_terms,
        po.transit_insurance,
        po.issue_vendor,
        po.amendment_remarks,
        po.total_amt as total_amount
      FROM st_purchaseorder po
      LEFT JOIN vendors v ON po.vendor_id = v.id
      WHERE po.id = :idOrNumber OR po.purchaseorder_id = :idOrNumber
      ORDER BY po.id DESC LIMIT 1
    `;
    
    const poRows = await dbPool.query(poQuery, { replacements: { idOrNumber }, type: QueryTypes.SELECT });
    const po = poRows[0] || null;
    
    if (!po) return null;

    po.display_po_number = this.getDisplayNumber(po.po_number, po.amendment_no);

    const siteSettingsQuery = `SELECT * FROM sitesettings_details WHERE status = 'Y' LIMIT 1`;
    const siteSettingsRows = await dbPool.query(siteSettingsQuery, { type: QueryTypes.SELECT });
    const site_details = siteSettingsRows[0] || null;

    const paymentTermsQuery = `
      SELECT id, description
      FROM st_paymentterms
      WHERE status = 'Y'
      ORDER BY id ASC
    `;
    const payment_terms = await dbPool.query(paymentTermsQuery, { type: QueryTypes.SELECT });

    const officerQuery = `
      SELECT name, mobile, designation
      FROM officers_name
      WHERE designation = 'Purchase Officer' AND status = 'Y'
      ORDER BY id DESC
      LIMIT 1
    `;
    const officerRows = await dbPool.query(officerQuery, { type: QueryTypes.SELECT });
    const officer = officerRows[0] || null;
    
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
        pod.tax_id,
        COALESCE(pod.uom, 'KG') as uom,
        pod.weight,
        pod.volume
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

    // Fetch Delivery Schedules
    const scheduleQuery = `
      SELECT 
        id,
        item_id,
        item_qty,
        DATE(delivery_date) as delivery_date,
        delivery_note as remark
      FROM po_delivery_note
      WHERE poprimary_id = :poId
      ORDER BY delivery_date ASC
    `;
    const schedules = await dbPool.query(scheduleQuery, { replacements: { poId: po.id }, type: QueryTypes.SELECT });

    return { po, items: updatedItemRows, site_details, payment_terms, officer, grns, schedules };
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

  buildListFilters(filters = {}) {
    const clauses = [], params = {};
    const poNumber = filters.po_number || filters.purchaseorder_id;
    if (poNumber) {clauses.push('po.purchaseorder_id = :po_number');params.po_number=poNumber;}
    if (filters.vendor_id) {clauses.push('po.vendor_id = :vendor_id');params.vendor_id=filters.vendor_id;}
    if (filters.vendor_name) {clauses.push('v.name LIKE :vendor_name');params.vendor_name=`%${filters.vendor_name}%`;}
    if (filters.datefrom && filters.dateto && filters.datefrom !== '1970-01-01' && filters.dateto !== '1970-01-01') {
      const dateColumn=filters.type === 'deli' ? 'delivery_date' : 'added_time';
      clauses.push(`DATE(po.${dateColumn}) BETWEEN :datefrom AND :dateto`);
      params.datefrom=filters.datefrom;params.dateto=filters.dateto;
    }
    if (filters.status) {clauses.push('po.postatus = :status');params.status=filters.status;}
    // Cake's AJAX search includes PO numbers from its detail query (not a join).
    const searching=filters.search === '1' || Boolean(poNumber || filters.vendor_id || filters.vendor_name || filters.datefrom || filters.dateto || filters.status || filters.item_id);
    if (searching) {
      clauses.push(`EXISTS (SELECT 1 FROM st_purchaseorderDetails pd WHERE pd.purchaseorder_id=po.purchaseorder_id${filters.item_id ? ' AND pd.item_id=:item_id' : ''})`);
      if (filters.item_id) params.item_id=filters.item_id;
    } else clauses.push("po.status IN ('Y', 'R')");
    return {whereString:'WHERE '+clauses.join(' AND '),queryParams:params};
  }

  async listPurchaseOrders(dbPool, filters, offset, limit) {
    const {whereString,queryParams}=this.buildListFilters(filters);
    const sort=listOrder(filters.sort,filters.direction,{id:'po.id',purchaseorder_id:'po.purchaseorder_id',added_time:'po.added_time',delivery_date:'po.delivery_date',total_qty:'po.total_qty',total_amt:'po.total_amt',is_revised:'po.is_revised'},'po.id');
    const query = `
      SELECT 
        po.id,
        po.purchaseorder_id as po_number,
        po.is_revised as amendment_no,
        CASE WHEN po.is_revised > 0 THEN CONCAT(po.purchaseorder_id, ' R-', po.is_revised) ELSE po.purchaseorder_id END as display_po_number,
        CASE WHEN po.is_revised = latest.latest_revision THEN 1 ELSE 0 END as is_latest_revision,
        DATE(po.added_time) as po_date,
        v.id as vendor_id,
        v.name as vendor_name,
        COALESCE(v.contact_no, 'N/A') as mobile,
        po.total_qty as quantity,
        (SELECT COALESCE(SUM(sr.quantity), 0) FROM st_stock_register sr WHERE sr.po_id=po.purchaseorder_id AND sr.purchaseorder_id=po.id AND sr.store_type='1') as received_qty,
        (SELECT COUNT(*) FROM po_delivery_note WHERE poprimary_id = po.id AND COALESCE(status, 'Y') != 'N') as delivery_notes_count,
        po.total_amt as amount,
        DATE(po.delivery_date) as delivery_date,
        CASE 
          WHEN po.postatus = 'O' THEN 'Open'
          WHEN po.postatus = 'C' THEN 'Closed'
          ELSE 'Active'
        END as status,
        po.postatus,
        po.status as record_status
      FROM st_purchaseorder po
      LEFT JOIN vendors v ON po.vendor_id = v.id
      LEFT JOIN (
        SELECT purchaseorder_id, MAX(is_revised) AS latest_revision
        FROM st_purchaseorder
        WHERE status IN ('Y', 'R')
        GROUP BY purchaseorder_id
      ) latest ON latest.purchaseorder_id = po.purchaseorder_id
      ${whereString}
      ORDER BY ${sort.column} ${sort.direction}${sort.column === 'po.id' ? '' : ', po.id DESC'}
      LIMIT :limit OFFSET :offset
    `;

    queryParams.limit = limit;
    queryParams.offset = Number(offset) || 0;
    
    const rows = await dbPool.query(query, { replacements: queryParams, type: QueryTypes.SELECT });
    return rows;
  }

  async countPurchaseOrders(dbPool, filters) {
    const {whereString,queryParams}=this.buildListFilters(filters);
    const rows=await dbPool.query(`SELECT COUNT(*) as total FROM st_purchaseorder po LEFT JOIN vendors v ON po.vendor_id=v.id ${whereString}`, {replacements:queryParams,type:QueryTypes.SELECT});
    return Number(rows[0].total);
  }

  async updatePurchaseOrder(dbPool, id, poData, transaction) {
    const query = `
      UPDATE st_purchaseorder
      SET 
        vendor_id = :vendor_id,
        delivery_date = :delivery_date,
        remark = :remark,
        total_qty = :total_qty,
        total_amt = :total_amt,
        is_revised = COALESCE(is_revised, 0) + 1,
        revised_date = CURRENT_TIMESTAMP
      WHERE id = :id
    `;
    const params = {
      vendor_id: poData.vendor_id,
      delivery_date: poData.delivery_date || null,
      remark: poData.remark || null,
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
          item_amt: item.rate !== undefined ? item.rate : (item.item_amt || 0),
          item_qty: item.order_qty !== undefined ? item.order_qty : (item.item_qty || 0),
          item_base_price: item.price !== undefined ? item.price : (item.item_base_price || 0),
          tax_percentage: item.tax_percentage || 0,
          item_tax_amt: item.tax_amt !== undefined ? item.tax_amt : (item.item_tax_amt || 0),
          item_total_amount: item.amount !== undefined ? item.amount : (item.item_total_amount || 0)
        },
        type: QueryTypes.INSERT,
        transaction
      });
    }
  }

  async createRevision(dbPool, sourceId, poData, items, transaction) {
    const sourceRows = await dbPool.query(
      `SELECT * FROM st_purchaseorder WHERE id = :id LIMIT 1 FOR UPDATE`,
      { replacements: { id: sourceId }, type: QueryTypes.SELECT, transaction }
    );
    const source = sourceRows[0];
    if (!source) {
      throw new Error('Purchase Order not found');
    }

    await dbPool.query('SELECT id FROM st_purchaseorder WHERE purchaseorder_id=:poNumber ORDER BY id DESC FOR UPDATE', {replacements:{poNumber:source.purchaseorder_id},type:QueryTypes.SELECT,transaction});
    const revisionRows = await dbPool.query(
      `SELECT COALESCE(MAX(is_revised), 0) as latest_revision
       FROM st_purchaseorder
       WHERE purchaseorder_id = :poNumber AND status IN ('Y', 'R')`,
      { replacements: { poNumber: source.purchaseorder_id }, type: QueryTypes.SELECT, transaction }
    );
    const latestRevision = Number(revisionRows[0]?.latest_revision) || 0;
    if ((Number(source.is_revised) || 0) !== latestRevision) {
      throw new Error('Only the latest Purchase Order revision can be revised');
    }
    const revisionNumber = latestRevision + 1;

    const normalizedItems = (items && items.length > 0) ? items : await this.getRevisionSourceItems(dbPool, source.id, transaction);
    const totals = normalizedItems.reduce((acc, item) => {
      const qty = Number(item.order_qty ?? item.item_qty ?? 0) || 0;
      const tax = Number(item.tax_amt ?? item.item_tax_amt ?? 0) || 0;
      const amount = Number(item.amount ?? item.item_total_amount ?? 0) || 0;
      acc.total_qty += qty;
      acc.total_tax += tax;
      acc.total_amt += amount;
      return acc;
    }, { total_qty: 0, total_tax: 0, total_amt: 0 });

    const revisedDate = poData.revised_date || poData.inwarddate || new Date();
    const poQuery = `
      INSERT INTO st_purchaseorder (
        purchaseorder_id, vendor_id, quotation_id, vendorshipaddress, delivery_date, freight,
        payment_terms, transit_insurance, remark, email_vendor, total_qty, total_tax, total_amt,
        is_revised, status, added_by, added_by_type, added_time, updated_by, updated_by_type,
        updated_time, token, amendment_remarks, issue_vendor, payment_term, postatus, revised_date
      ) VALUES (
        :purchaseorder_id, :vendor_id, :quotation_id, :vendorshipaddress, :delivery_date, :freight,
        :payment_terms, :transit_insurance, :remark, :email_vendor, :total_qty, :total_tax, :total_amt,
        :is_revised, :status, :added_by, :added_by_type, :added_time, :updated_by, :updated_by_type,
        :updated_time, :token, :amendment_remarks, :issue_vendor, :payment_term, :postatus, :revised_date
      )
    `;
    const poParams = {
      purchaseorder_id: source.purchaseorder_id,
      vendor_id: poData.vendor_id ?? source.vendor_id,
      quotation_id: source.quotation_id || null,
      vendorshipaddress: poData.vendorshipaddress ?? source.vendorshipaddress ?? '',
      delivery_date: poData.delivery_date || source.delivery_date,
      freight: poData.freight ?? source.freight ?? '',
      payment_terms: poData.payment_terms ?? source.payment_terms ?? '',
      transit_insurance: poData.transit_insurance ?? source.transit_insurance ?? '',
      remark: poData.remark ?? source.remark ?? '',
      email_vendor: source.email_vendor || 'N',
      total_qty: totals.total_qty,
      total_tax: totals.total_tax,
      total_amt: totals.total_amt,
      is_revised: revisionNumber,
      status: 'R',
      added_by: source.added_by || null,
      added_by_type: source.added_by_type || null,
      added_time: source.added_time,
      updated_by: source.updated_by || null,
      updated_by_type: source.updated_by_type || null,
      updated_time: new Date(),
      token: source.token || null,
      amendment_remarks: poData.amendment_remarks ?? source.amendment_remarks ?? '',
      issue_vendor: poData.issue_vendor || source.issue_vendor || 'N',
      payment_term: poData.payment_term ?? source.payment_term ?? '',
      postatus: source.postatus || 'O',
      revised_date: revisedDate
    };

    const result = await dbPool.query(poQuery, { replacements: poParams, type: QueryTypes.INSERT, transaction });
    const newPOId = result[0];

    for (const item of normalizedItems) {
      const qty = Number(item.order_qty ?? item.item_qty ?? 0) || 0;
      const rate = Number(item.rate ?? item.item_amt ?? 0) || 0;
      const price = Number(item.price ?? item.item_base_price ?? (qty * rate)) || 0;
      const taxPercentage = Number(item.tax_percentage ?? 0) || 0;
      const taxAmt = Number(item.tax_amt ?? item.item_tax_amt ?? ((price * taxPercentage) / 100)) || 0;
      const amount = Number(item.amount ?? item.item_total_amount ?? (price + taxAmt)) || 0;

      await dbPool.query(
        `INSERT INTO st_purchaseorderDetails (
          purchaseorder_id, poprimary_id, item_id, tax_id, item_amt, item_qty, item_base_price,
          tax_percentage, item_tax_amt, item_total_amount, uom, weight, volume, vendor_id,
          inward_date, revised_date
        ) VALUES (
          :purchaseorder_id, :poprimary_id, :item_id, :tax_id, :item_amt, :item_qty, :item_base_price,
          :tax_percentage, :item_tax_amt, :item_total_amount, :uom, :weight, :volume, :vendor_id,
          :inward_date, :revised_date
        )`,
        {
          replacements: {
            purchaseorder_id: source.purchaseorder_id,
            poprimary_id: newPOId,
            item_id: item.item_id,
            tax_id: item.tax_id || null,
            item_amt: rate,
            item_qty: qty,
            item_base_price: price,
            tax_percentage: taxPercentage,
            item_tax_amt: taxAmt,
            item_total_amount: amount,
            uom: item.uom || '',
            weight: item.weight || '',
            volume: item.volume || '',
            vendor_id: poParams.vendor_id,
            inward_date: source.added_time,
            revised_date: revisedDate
          },
          type: QueryTypes.INSERT,
          transaction
        }
      );
    }

    await dbPool.query(
      `UPDATE po_delivery_note SET poprimary_id = :newPOId WHERE poprimary_id = :oldPOId`,
      { replacements: { newPOId, oldPOId: source.id }, type: QueryTypes.UPDATE, transaction }
    );

    await dbPool.query(
      `UPDATE st_stock_register SET purchaseorder_id = :newPOId WHERE purchaseorder_id = :oldPOId`,
      { replacements: { newPOId, oldPOId: source.id }, type: QueryTypes.UPDATE, transaction }
    );

    return {
      id: newPOId,
      po_number: source.purchaseorder_id,
      amendment_no: revisionNumber,
      display_po_number: this.getDisplayNumber(source.purchaseorder_id, revisionNumber)
    };
  }

  async getRevisionSourceItems(dbPool, poId, transaction) {
    return await dbPool.query(
      `SELECT
        item_id,
        tax_id,
        item_amt,
        item_qty,
        item_base_price,
        tax_percentage,
        item_tax_amt,
        item_total_amount,
        uom,
        weight,
        volume
      FROM st_purchaseorderDetails
      WHERE poprimary_id = :poId`,
      { replacements: { poId }, type: QueryTypes.SELECT, transaction }
    );
  }

  async addDeliveryNote(dbPool, poprimary_id, po_number, vendor_id, schedules, remark, transaction) {
    // Delete existing schedules for this PO
    await dbPool.query(`DELETE FROM po_delivery_note WHERE poprimary_id = :poprimary_id`, {
      replacements: { poprimary_id }, type: QueryTypes.DELETE, transaction
    });

    for (const schedule of schedules) {
      if (schedule.inwarddate && schedule.inwarddate.trim() !== '') {
        for (const item of schedule.items) {
          if (item.qty > 0) {
            const query = `
              INSERT INTO po_delivery_note 
              (po_id, poprimary_id, vendor_id, item_id, item_qty, delivery_date, delivery_note)
              VALUES (:po_number, :poprimary_id, :vendor_id, :item_id, :item_qty, :delivery_date, :delivery_note)
            `;
            await dbPool.query(query, {
              replacements: {
                po_number,
                poprimary_id,
                vendor_id,
                item_id: item.item_id,
                item_qty: item.qty,
                delivery_date: schedule.inwarddate,
                delivery_note: remark || ''
              },
              type: QueryTypes.INSERT,
              transaction
            });
          }
        }
      }
    }
  }

  async deletePurchaseOrder(dbPool, id, transaction) {
    const [source] = await dbPool.query('SELECT purchaseorder_id,status,is_revised FROM st_purchaseorder WHERE id=:id FOR UPDATE', {replacements:{id},type:QueryTypes.SELECT,transaction});
    const {invalid}=require('../../utils/receiptValidation');
    if (!source) throw invalid('Purchase Order not found.',404);
    const receipts = await dbPool.query('SELECT id FROM st_goodsreceive WHERE purchaseorder_id=:poNumber LIMIT 1', {replacements:{poNumber:source.purchaseorder_id},type:QueryTypes.SELECT,transaction});
    if (receipts.length) throw invalid('Purchase Order cannot be deleted because a GRN exists.',409);
    const [latest] = await dbPool.query(`SELECT MAX(is_revised) AS revision FROM st_purchaseorder WHERE purchaseorder_id=:poNumber AND status IN ('Y','R')`, {replacements:{poNumber:source.purchaseorder_id},type:QueryTypes.SELECT,transaction});
    if (source.status==='N' || Number(source.is_revised)!==Number(latest.revision)) throw invalid('Only the latest active PO revision can be deleted.',409);
    await dbPool.query('DELETE FROM st_purchaseorderDetails WHERE poprimary_id = :id', { replacements: { id }, type: QueryTypes.DELETE, transaction });
    await dbPool.query('DELETE FROM po_delivery_note WHERE poprimary_id = :id', { replacements: { id }, type: QueryTypes.DELETE, transaction });
    await dbPool.query('DELETE FROM st_purchaseorder WHERE id = :id', { replacements: { id }, type: QueryTypes.DELETE, transaction });
  }

  async getNextPoNumber(dbPool, transaction) {
    const currentYear = new Date().getFullYear();
    const currentMonth = new Date().getMonth() + 1;
    let financialYearStart = currentMonth >= 4 ? `${currentYear}-04-01` : `${currentYear - 1}-04-01`;

    const query = `
      SELECT purchaseorder_id 
      FROM st_purchaseorder 
      WHERE DATE(added_time) >= :financialYearStart AND is_revised = '0' 
      ORDER BY id DESC LIMIT 1
    `;
    const rows = await dbPool.query(query, { replacements: { financialYearStart }, type: QueryTypes.SELECT, transaction });
    
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

