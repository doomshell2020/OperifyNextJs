const grnService = require('./grn.service');

class GrnController {
  async downloadPdf(req, res, next) {
    try {
      const data = await grnService.getGrnDetails(req.dbPool, req.params.id);
      if (!data) return res.status(404).json({success:false, message:'GRN not found'});
      const { generateGrnPDF } = require('./grn.pdf');
      res.setHeader('Content-Type', 'application/pdf');
      res.setHeader('Content-Disposition', `inline; filename="GRN_Details_${Number(req.params.id)}.pdf"`);
      res.send(await generateGrnPDF(data));
    } catch (error) { next(error); }
  }

  async listGrns(req, res, next) {
    try {
      const result = await grnService.listGrns(req.dbPool, {...req.query,po_id:req.query.po_id || req.query.purchaseorder_id,from_date:req.query.from_date || req.query.datefrom,to_date:req.query.to_date || req.query.dateto});
      res.status(200).json(result);
    } catch (error) {
      next(error);
    }
  }

  async exportGrns(req, res) {
    try {
      const { po_id, vendor_id, from_date, to_date } = req.query;
      const buffer = await grnService.exportGrnsToExcel(req.dbPool, {
        po_id,
        vendor_id,
        from_date,
        to_date
      });

      const dateStr = new Date().toLocaleDateString('en-GB').replace(/\//g, '-');
      const filename = `GRN_Summary-${dateStr}.xlsx`;

      res.setHeader('Content-Disposition', `attachment;filename=${filename}`);
      res.setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      res.setHeader('Cache-Control', 'max-age=0');
      
      res.status(200).send(buffer);
    } catch (error) {
      console.error("Error in exportGrns:", error);
      res.status(500).json({ success: false, message: "Internal server error" });
    }
  }

  async getInspectionForGrn(req, res) {
    try {
      const { inspectionId } = req.params;
      const result = await grnService.getInspectionForGrn(req.dbPool, inspectionId);
      if (!result) {
        return res.status(404).json({ success: false, message: "Inspection not found or already processed" });
      }
      res.status(200).json({ success: true, ...result });
    } catch (error) {
      console.error("Error in getInspectionForGrn:", error);
      res.status(500).json({ success: false, message: "Internal server error" });
    }
  }

  async createGrn(req, res) {
    try {
      const payload = req.body;
      const result = await grnService.createGrn(req.dbPool, payload);
      res.status(201).json({ success: true, data: result });
    } catch (error) {
      console.error("Error in createGrn:", error);
      res.status(500).json({ success: false, message: error.message });
    }
  }

  async getGrnDetails(req, res) {
    try {
      const { id } = req.params;
      const result = await grnService.getGrnDetails(req.dbPool, id);
      if (!result) return res.status(404).json({ success: false, message: 'GRN not found' });
      res.status(200).json({ success: true, data: result });
    } catch (error) {
      console.error("Error in getGrnDetails:", error);
      res.status(500).json({ success: false, message: "Internal server error" });
    }
  }

  async updateGrn(req, res) {
    try {
      const { id } = req.params;
      const payload = req.body;
      const result = await grnService.updateGrn(req.dbPool, id, payload);
      res.status(200).json({ success: true, data: result });
    } catch (error) {
      console.error("Error in updateGrn:", error);
      res.status(500).json({ success: false, message: error.message });
    }
  }

  async deleteGrn(req, res) {
    try {
      const { id } = req.params;
      await grnService.deleteGrn(req.dbPool, id);
      res.status(200).json({ success: true, message: 'GRN deleted successfully' });
    } catch (error) {
      console.error("Error in deleteGrn:", error);
      res.status(500).json({ success: false, message: error.message });
    }
  }
}

module.exports = new GrnController();
