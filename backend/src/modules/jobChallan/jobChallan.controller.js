const JobChallanService = require('./jobChallan.service');
const { generateJobChallanPDF } = require('./jobChallan.pdf');

class JobChallanController {

  async list(req, res, next) {
    try {
      const data = await JobChallanService.list(req.dbName, req.query);
      res.json({ success: true, data });
    } catch (err) { next(err); }
  }

  async create(req, res, next) {
    try {
      const data = await JobChallanService.create(req.dbName, req.body, req.user);
      res.json({ success: true, data, message: 'Job Challan created successfully' });
    } catch (err) { next(err); }
  }

  async getDetail(req, res, next) {
    try {
      const data = await JobChallanService.getDetail(req.dbName, req.params.id);
      res.json({ success: true, data });
    } catch (err) { next(err); }
  }

  async downloadPdf(req, res, next) {
    try {
      const data = await JobChallanService.getPdfDetail(req.dbName, req.params.id, req.query.sender_db);
      const pdfBuffer = await generateJobChallanPDF(data);

      res.setHeader('Content-Type', 'application/pdf');
      res.setHeader('Content-Disposition', `inline; filename="JC-${req.params.id}.pdf"`);
      res.end(pdfBuffer, 'binary');
    } catch (err) {
      if (err.statusCode === 403) return res.status(403).json({success:false,message:err.message});
      if (err.message === 'Job Challan not found') return res.status(404).json({success:false,message:err.message});
      next(err);
    }
  }

  async remove(req, res, next) {
    try {
      await JobChallanService.remove(req.dbName, req.params.id);
      res.json({ success: true, message: 'Job Challan deleted successfully' });
    } catch (err) { next(err); }
  }

  async searchItems(req, res, next) {
    try {
      const { search = '', process_type = '' } = req.query;
      const items = await JobChallanService.searchItems(req.dbName, search, process_type);
      res.json({ success: true, data: items });
    } catch (err) { next(err); }
  }

  async getItemStock(req, res, next) {
    try {
      const data = await JobChallanService.getItemInHandStock(req.dbName, req.query.item_id);
      res.json({ success: true, data });
    } catch (err) { next(err); }
  }

  async getVendorGst(req, res, next) {
    try {
      const data = await JobChallanService.getVendorGst(req.dbName, req.query.vendor_id);
      res.json({ success: true, data });
    } catch (err) { next(err); }
  }

  async listVendors(req, res, next) {
    try {
      const data = await JobChallanService.listVendors(req.dbName);
      res.json({ success: true, data });
    } catch (err) { next(err); }
  }

  async listTaxMaster(req, res, next) {
    try {
      const data = await JobChallanService.listTaxMaster(req.dbName);
      res.json({ success: true, data });
    } catch (err) { next(err); }
  }
}

module.exports = new JobChallanController();
