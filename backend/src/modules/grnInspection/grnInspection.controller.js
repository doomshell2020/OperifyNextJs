const grnInspectionService = require('./grnInspection.service');

class GrnInspectionController {
  async exportInspections(req, res, next) {
    try {
      // Legacy grninspectionexcel exports the complete list, independent of page/filter.
      const { QueryTypes } = require('sequelize');
      const ExcelJS = require('exceljs');
      const rows = await req.dbPool.query(`SELECT g.*, v.name AS supplier FROM grn_inspection g LEFT JOIN vendors v ON v.id=g.vendor_id ORDER BY g.id DESC`, { type: QueryTypes.SELECT });
      const workbook = new ExcelJS.Workbook();
      const sheet = workbook.addWorksheet('GRN Inspection');
      sheet.addRow(['S.No.', 'Inspection No.', 'PO No.', 'Inspection Inward', 'Bill No.', 'Bill Date', 'Supplier', 'Total Qty', 'Total Recived Qty']);
      const date = value => {
        if (!value) return '';
        const text = value instanceof Date ? `${value.getUTCFullYear()}-${String(value.getUTCMonth()+1).padStart(2,'0')}-${String(value.getUTCDate()).padStart(2,'0')}` : String(value).slice(0,10);
        return text.split('-').reverse().join('-');
      };
      rows.forEach((row, index) => sheet.addRow([index+1, row.inspection_id, row.po_id, date(row.inwarddate), row.bill_no, date(row.bill_date), row.supplier, Number(row.total_qty), Number(row.total_amt)]));
      sheet.columns.forEach(column => { column.width = 18; });
      sheet.getRow(1).font = { bold: true };
      res.setHeader('Content-Type','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      res.setHeader('Content-Disposition','attachment; filename="GRN_Inspection.xlsx"');
      await workbook.xlsx.write(res);
      res.end();
    } catch (error) { next(error); }
  }
  async listInspections(req, res, next) {
    try {
      const { page, limit, ...filters } = req.query;
      const data = await grnInspectionService.listInspections(req.dbPool, filters, page, limit);
      res.status(200).json({ success: true, ...data });
    } catch (error) {
      next(error);
    }
  }

  async getDetails(req, res, next) {
    try {
      const { id } = req.params;
      const data = await grnInspectionService.getDetails(req.dbPool, id);
      return res.status(200).json({ success: true, data });
    } catch (error) {
      if (error.message === 'Inspection not found') {
        return res.status(404).json({ success: false, message: error.message });
      }
      next(error);
    }
  }

  async createInspection(req, res, next) {
    try {
      const { inspection, items } = req.body;
      const result = await grnInspectionService.createInspection(req.dbPool, inspection, items);
      res.status(201).json({ success: true, message: 'GRN Inspection created successfully', data: result.data });
    } catch (error) {
      next(error);
    }
  }

  async getNextInspectionNumber(req, res, next) {
    try {
      const nextId = await grnInspectionService.getNextInspectionNumber(req.dbPool);
      res.status(200).json({ success: true, nextId });
    } catch (error) {
      next(error);
    }
  }
  
  async getPoDetails(req, res, next) {
    try {
      const { po_id } = req.params;
      const data = await grnInspectionService.getPoDetails(req.dbPool, po_id);
      res.status(200).json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }
}

module.exports = new GrnInspectionController();
