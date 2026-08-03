const paymentService = require('./payment.service');

class PaymentController {
  async getPaymentsList(req, res, next) {
    try {
      const { vendor_id, bill_no, status, datefrom, dateto } = req.query;
      const filters = {
        vendor_id: vendor_id ? parseInt(vendor_id, 10) : undefined,
        bill_no: bill_no || undefined,
        status: status || undefined,
        datefrom: datefrom || undefined,
        dateto: dateto || undefined,
      };
      const data = await paymentService.getPaymentsList(req.dbPool, filters);
      res.json({ success: true, data });
    } catch (err) {
      next(err);
    }
  }

  async getPaymentDetail(req, res, next) {
    try {
      const data = await paymentService.getPaymentDetail(req.dbPool, parseInt(req.params.id, 10));
      if (!data) return res.status(404).json({ success: false, message: 'Payment not found' });
      res.json({ success: true, data });
    } catch (err) {
      next(err);
    }
  }

  async getParticularPayments(req, res, next) {
    try {
      const { po_no, status, datefrom, dateto } = req.query;
      const filters = {
        po_no: po_no || undefined,
        status: status || undefined,
        datefrom: datefrom || undefined,
        dateto: dateto || undefined,
      };
      const data = await paymentService.getParticularPayments(req.dbPool, filters);
      res.json({ success: true, data });
    } catch (err) {
      next(err);
    }
  }

  async getVendors(req, res, next) {
    try {
      const data = await paymentService.getVendors(req.dbPool);
      res.json({ success: true, data });
    } catch (err) {
      next(err);
    }
  }
}

module.exports = new PaymentController();
