const repo = require('./grn.repository');
const { formatQty, formatAmt } = require('../../utils/formatters');
const {paginationInput,paginationResult} = require('../../utils/listPagination');

class GrnService {
  async listGrns(dbPool, params) {
    const {page,limit} = paginationInput(params,'grn');
    const offset = (page - 1) * limit;
    const result = await repo.getList(dbPool, { ...params, limit, offset });
    
    return {
      data: result.data,
      pagination: paginationResult(result.total,page,limit,result.data.length)
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
    for (const item of items) item.tax_rates = (await repo.getPdfTaxes(dbPool,item.tax_id)).map(t=>t.tax);
    return { grn, items, ...await repo.getPdfSettings(dbPool) };
  }

  async createGrn(dbPool, payload) {
    return require('./grn.receipt')(dbPool, payload);
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
        const y = String(dateObj.getFullYear());
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
        total_qty: Number(row.po_total_qty || 0).toFixed(2),
        received_qty: Number(row.received_qty || 0).toFixed(2),
        scheduled_qty: row.scheduled_qty ? formatQty(row.scheduled_qty) : 'N/A',
        scheduled_date: row.scheduled_date ? formatDate(scheduledDateObj) : 'N/A',
        total_amt: Number(row.total_amt || 0).toFixed(2)
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
