const repo = require('./grn.repository');
const { formatQty, formatAmt } = require('../../utils/formatters');

class GrnService {
  async listGrns(dbPool, params) {
    const { page, limit } = params;
    const offset = (page - 1) * limit;
    const result = await repo.getList(dbPool, { ...params, offset });
    
    return {
      data: result.data,
      pagination: {
        total: result.total,
        page,
        limit,
        totalPages: Math.ceil(result.total / limit)
      }
    };
  }

  async getInspectionForGrn(dbPool, inspectionId) {
    const inspection = await repo.getInspectionDetails(dbPool, inspectionId);
    if (!inspection) return null;
    
    const items = await repo.getInspectionItems(dbPool, inspectionId);
    return { inspection, items };
  }

  async getGrnDetails(dbPool, id) {
    const grn = await repo.getGrnDetails(dbPool, id);
    if (!grn) return null;
    const items = await repo.getGrnItems(dbPool, id);
    return { grn, items };
  }

  async createGrn(dbPool, payload) {
    const conn = await dbPool.getConnection();
    try {
      await conn.beginTransaction();

      const {
        purchaseorder_id,
        inspection_id,
        vendor_id,
        inwarddate,
        bill_date,
        bill_no,
        remark,
        items
      } = payload;

      let totalQty = 0;
      let totalTax = 0;
      let totalAmt = 0;

      // Filter items that have received quantity > 0
      const receivedItems = items.filter(item => Number(item.received_qty) > 0);
      
      if (receivedItems.length === 0) {
        throw new Error("No items received in this GRN.");
      }

      for (const item of receivedItems) {
        const qty = Number(item.received_qty);
        const rate = Number(item.rate);
        const taxRate = Number(item.tax_rate) || 0;
        
        const baseAmount = qty * rate;
        const taxAmount = baseAmount * (taxRate / 100);
        const itemTotal = baseAmount + taxAmount;
        
        totalQty += qty;
        totalAmt += itemTotal;
        totalTax += taxAmount;
      }

      // 1. Insert GRN header (st_goodsreceive)
      const grnQuery = `
        INSERT INTO st_goodsreceive 
        (purchaseorder_id, vendor_id, inwarddate, bill_date, bill_no, remark, total_qty, total_tax, total_amt, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'O')
      `;
      const [grnRes] = await conn.execute(grnQuery, [
        purchaseorder_id,
        vendor_id,
        inwarddate,
        bill_date,
        bill_no,
        remark || '',
        totalQty,
        totalTax,
        totalAmt
      ]);
      const goodsId = grnRes.insertId;

      // 2. Fetch PO ID (internal PK)
      const [poRows] = await conn.execute(`SELECT id, delivery_date, total_qty FROM st_purchaseorder WHERE purchaseorder_id = ? ORDER BY id DESC LIMIT 1`, [purchaseorder_id]);
      if (poRows.length === 0) throw new Error("PO not found");
      const poInternalId = poRows[0].id;
      const poTotalQty = Number(poRows[0].total_qty);

      // 3. Insert items into st_stock_register and update st_stock_available
      for (const item of receivedItems) {
        const qty = Number(item.received_qty);
        const rate = Number(item.rate);
        const taxRate = Number(item.tax_rate) || 0;
        const baseAmount = qty * rate;
        const taxAmount = baseAmount * (taxRate / 100);
        const itemTotal = baseAmount + taxAmount;

        // Insert into st_stock_register
        const srQuery = `
          INSERT INTO st_stock_register 
          (purchaseorder_id, po_id, goods_id, vendor_id, item_id, quantity, rate, amount, cost_price, tax_id, tax, issue_date, delivery_date, store_type, status)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '1', 'Y')
        `;
        // We might not have tax_id mapped directly, assuming taxRate is the percentage. Using 0 for tax_id if not present.
        await conn.execute(srQuery, [
          poInternalId,
          purchaseorder_id,
          goodsId,
          vendor_id,
          item.item_id,
          qty,
          rate,
          itemTotal,
          baseAmount, // cost_price
          item.tax_id || 0,
          taxAmount,
          inwarddate,
          inwarddate
        ]);

        // Update st_stock_available
        const [saRows] = await conn.execute(`SELECT id, stock_available FROM st_stock_available WHERE item_id = ?`, [item.item_id]);
        if (saRows.length > 0) {
          const saId = saRows[0].id;
          const currentStock = Number(saRows[0].stock_available);
          const newStock = currentStock + qty;
          await conn.execute(`UPDATE st_stock_available SET stock_available = ? WHERE id = ?`, [newStock, saId]);
        } else {
          // If no row exists, we could insert, but CakePHP code assumed it exists. Let's insert to be safe.
          await conn.execute(`INSERT INTO st_stock_available (item_id, stock_available) VALUES (?, ?)`, [item.item_id, qty]);
        }
      }

      // 4. Update PO Status
      const [sumRows] = await conn.execute(`
        SELECT SUM(quantity) as received_qty 
        FROM st_stock_register 
        WHERE po_id = ? AND status != 'N' AND store_type = '1'
      `, [poInternalId]);
      
      const totalReceivedQty = sumRows[0].received_qty ? Number(sumRows[0].received_qty) : 0;
      let poStatus = 'O'; // Open
      if (totalReceivedQty >= poTotalQty) {
        poStatus = 'C'; // Complete
      }

      await conn.execute(`UPDATE st_purchaseorder SET postatus = ? WHERE purchaseorder_id = ?`, [poStatus, purchaseorder_id]);
      await conn.execute(`UPDATE st_goodsreceive SET status = ? WHERE id = ?`, [poStatus, goodsId]);

      // 5. Update Inspection Status
      if (inspection_id) {
        await conn.execute(`UPDATE grn_inspection SET status = 'N' WHERE inspection_id = ?`, [inspection_id]);
      }

      await conn.commit();
      return { goods_id: goodsId, poStatus };
    } catch (error) {
      await conn.rollback();
      throw error;
    } finally {
      conn.release();
    }
  }

  async updateGrn(dbPool, id, payload) {
    throw new Error("Update GRN not implemented");
  }

  async deleteGrn(dbPool, id) {
    throw new Error("Delete GRN not implemented");
  }
  async exportGrnsToExcel(dbPool, filters) {
    const rows = await repo.exportGrns(dbPool, filters);

    const exceljs = require('exceljs');
    const workbook = new exceljs.Workbook();
    workbook.creator = 'Maarten Balliauw';
    workbook.lastModifiedBy = 'Maarten Balliauw';
    
    const worksheet = workbook.addWorksheet('Sheet1');
    
    worksheet.columns = [
      { header: 'S.No.', key: 'sno', width: 10 },
      { header: 'GRN No.', key: 'grn_no', width: 15 },
      { header: 'PO No.', key: 'po_no', width: 15 },
      { header: 'GRN Inward Date', key: 'inward_date', width: 20 },
      { header: 'Bill No.', key: 'bill_no', width: 15 },
      { header: 'Bill Date', key: 'bill_date', width: 20 },
      { header: 'Product Name', key: 'product_name', width: 30 },
      { header: 'Vendor Name', key: 'vendor_name', width: 30 },
      { header: 'Total Qty', key: 'total_qty', width: 15 },
      { header: 'Total Recived Qty', key: 'received_qty', width: 20 },
      { header: 'Scheduled Qty', key: 'scheduled_qty', width: 15 },
      { header: 'Scheduled Date', key: 'scheduled_date', width: 20 },
      { header: 'GRN Total Amount', key: 'total_amt', width: 20 },
    ];

    let sno = 1;
    for (const row of rows) {
      const inwardDateObj = row.inwarddate ? new Date(row.inwarddate) : null;
      const billDateObj = row.bill_date ? new Date(row.bill_date) : null;
      const scheduledDateObj = row.scheduled_date ? new Date(row.scheduled_date) : null;
      
      const formatDate = (dateObj) => {
        if (!dateObj) return 'N/A';
        const d = String(dateObj.getDate()).padStart(2, '0');
        const m = String(dateObj.getMonth() + 1).padStart(2, '0');
        const y = String(dateObj.getFullYear()).slice(-2);
        return `${d}-${m}-${y}`;
      };

      const excelRow = worksheet.addRow({
        sno: sno++,
        grn_no: row.grn_id,
        po_no: row.po_no,
        inward_date: row.inwarddate ? formatDate(inwardDateObj) : 'N/A',
        bill_no: row.bill_no,
        bill_date: row.bill_date ? formatDate(billDateObj) : 'N/A',
        product_name: row.product_name,
        vendor_name: row.vendor_name,
        total_qty: row.po_total_qty ? formatQty(row.po_total_qty) : 'N/A',
        received_qty: row.received_qty ? formatQty(row.received_qty) : 'N/A',
        scheduled_qty: row.scheduled_qty ? formatQty(row.scheduled_qty) : 'N/A',
        scheduled_date: row.scheduled_date ? formatDate(scheduledDateObj) : 'N/A',
        total_amt: row.total_amt ? formatAmt(row.total_amt) : 'N/A'
      });

      if (inwardDateObj && scheduledDateObj && inwardDateObj > scheduledDateObj) {
        excelRow.getCell(12).fill = {
          type: 'pattern',
          pattern: 'solid',
          fgColor: { argb: 'FFFF0000' }
        };
        excelRow.getCell(12).font = {
          color: { argb: 'FFFFFFFF' }
        };
      }
    }

    const buffer = await workbook.xlsx.writeBuffer();
    return buffer;
  }
}

module.exports = new GrnService();
