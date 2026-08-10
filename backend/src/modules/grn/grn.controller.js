const grnService = require('./grn.service');

class GrnController {
  async listGrns(req, res) {
    try {
      const { page = 1, limit = 10, po_id, vendor_id, from_date, to_date } = req.query;
      const result = await grnService.listGrns(req.dbPool, {
        page: parseInt(page),
        limit: parseInt(limit),
        po_id,
        vendor_id,
        from_date,
        to_date
      });
      res.status(200).json(result);
    } catch (error) {
      console.error("Error in listGrns:", error);
      res.status(500).json({ success: false, message: "Internal server error" });
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
