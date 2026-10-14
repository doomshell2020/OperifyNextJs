const reverseIndentService = require('./reverseIndent.service');

class ReverseIndentController {
  async getNextReverseId(req, res, next) {
    try {
      const id = await reverseIndentService.getNextReverseId(req.dbPool);
      res.json({ next_id: `${id}` });
    } catch (err) {
      next(err);
    }
  }

  async getReverseIndents(req, res, next) {
    try {
      const filters = req.query;
      const data = filters.page ? await reverseIndentService.getPage(req.dbPool, filters) : await reverseIndentService.getReverseIndents(req.dbPool, filters);
      res.json(data);
    } catch (err) {
      next(err);
    }
  }

  async saveReverseIndent(req, res, next) {
    try {
      const data = req.body;
      const result = await reverseIndentService.saveReverseIndent(req.dbPool, data);
      res.status(201).json({ message: 'Reverse Indent saved successfully', id: result });
    } catch (err) {
      next(err);
    }
  }

  async getReverseIndentDetails(req, res, next) {
    try {
      const { id } = req.params;
      const details = await reverseIndentService.getReverseIndentDetails(req.dbPool, id);
      if (!details) {
        return res.status(404).json({ message: 'Reverse Indent not found' });
      }
      res.json(details);
    } catch (err) {
      next(err);
    }
  }

  async updateReverseIndent(req, res, next) {
    try {
      const id = await reverseIndentService.saveReverseIndent(req.dbPool, req.body, req.params.id);
      res.json({ message: 'Reverse Indent updated successfully', id });
    } catch (error) { next(error); }
  }

  async lookup(req, res, next) {
    try {
      const methods = {contracts:'searchContracts', machines:'searchMachines', products:'searchProducts', 'semi-products':'getSemiProducts'};
      const args=req.params.kind==='semi-products' ? [req.dbPool] : [req.dbPool,String(req.query.q || '')];
      res.json(await reverseIndentService[methods[req.params.kind]](...args));
    } catch (error) { next(error); }
  }
  async contractProducts(req, res, next) {
    try { res.json(await reverseIndentService.getContractProducts(req.dbPool,req.params.contractId)); }
    catch (error) { next(error); }
  }
  async designSheet(req, res, next) {
    try { res.json(await reverseIndentService.getDesignSheetDetails(req.dbPool,req.params.contractId,req.params.productId)); }
    catch (error) { next(error); }
  }
  async exportExcel(req, res, next) {
    try {
      const ExcelJS = require('exceljs');
      const rows = await reverseIndentService.getExportRows(req.dbPool,req.query);
      const book = new ExcelJS.Workbook();
      const sheet = book.addWorksheet('Reverse Indent Summary');
      sheet.columns = ['Issued Date','Received Id.','Contract','Finished Product','Machine Name','Raw Material','Received Qty','Received By'].map((header,i)=>({header,width:[12,14,30,30,30,30,15,20][i]}));
      const { date, ucfirst } = require('../../utils/legacyPdf');
      for (const row of rows) {
        sheet.addRow([date(row.issue_date),String(row.reverse_id),`${row.contract_name || ''}(${row.workorder || ''})`,row.product_name,row.machine_name,row.item_name,Number(row.quantity),ucfirst(row.received_name)]);
      }
      require('../../utils/quantityExcel').formatQuantityColumns(sheet,[7]);
      sheet.getRow(1).font={bold:true};
      res.setHeader('Content-Type','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      res.setHeader('Content-Disposition','attachment; filename="Reverse_Indent_Summary.xlsx"');
      res.end(Buffer.from(await book.xlsx.writeBuffer()));
    } catch (error) { next(error); }
  }

  async deleteReverseIndent(req, res, next) {
    try {
      const { id } = req.params;
      await reverseIndentService.deleteReverseIndent(req.dbPool, id);
      res.json({ message: 'Reverse Indent deleted successfully' });
    } catch (err) {
      next(err);
    }
  }
  async exportPDF(req, res, next) {
    try {
      const { id } = req.params;
      const reverseIndentService = require('./reverseIndent.service');
      const details = await reverseIndentService.getReverseIndentDetails(req.dbPool, id);
      
      if (!details) {
        return res.status(404).json({ success: false, message: 'Reverse Indent not found' });
      }

      const { generateReverseIndentPDF } = require('./reverseIndent.pdf');
      const tenantDb = req.user?.db || 'default';
      const pdfBuffer = await generateReverseIndentPDF(details, tenantDb);
      
      res.setHeader('Content-Type', 'application/pdf');
      res.setHeader('Content-Disposition', `inline; filename="reverse-indent-${id}.pdf"`);
      res.end(pdfBuffer, 'binary');
    } catch (err) {
      next(err);
    }
  }
}

module.exports = new ReverseIndentController();
