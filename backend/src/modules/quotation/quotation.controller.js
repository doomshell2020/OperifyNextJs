const quotationService = require('./quotation.service');

class QuotationController {
  async getQuotationsList(req, res, next) {
    try {
      const { quotation_id, vendor_id, status, is_award, datefrom, dateto } = req.query;
      const filters = {
        quotation_id: quotation_id || undefined,
        vendor_id: vendor_id ? parseInt(vendor_id, 10) : undefined,
        status: status || undefined,
        is_award: is_award !== undefined ? parseInt(is_award, 10) : undefined,
        datefrom: datefrom || undefined,
        dateto: dateto || undefined,
      };
      const data = await quotationService.getQuotationsList(req.dbPool, filters);
      res.json({ success: true, data });
    } catch (err) {
      next(err);
    }
  }

  async getQuotationDetail(req, res, next) {
    try {
      const data = await quotationService.getQuotationDetail(req.dbPool, parseInt(req.params.id, 10));
      if (!data) return res.status(404).json({ success: false, message: 'Quotation not found' });
      res.json({ success: true, data });
    } catch (err) {
      next(err);
    }
  }

  async getVendors(req, res, next) {
    try {
      const data = await quotationService.getVendors(req.dbPool);
      res.json({ success: true, data });
    } catch (err) {
      next(err);
    }
  }
}

module.exports = new QuotationController();
