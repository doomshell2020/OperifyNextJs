const purchaseOrderService = require('./purchaseOrder.service');

class PurchaseOrderController {
  async getHoverDetails(req, res, next) {
    try {
      const { id } = req.params;
      const data = await purchaseOrderService.getHoverDetails(req.dbPool, id);
      return res.status(200).json({ success: true, data });
    } catch (error) {
      if (error.message === 'Purchase Order not found') {
        return res.status(404).json({ success: false, message: error.message });
      }
      next(error);
    }
  }

  async getDetails(req, res, next) {
    try {
      const { id } = req.params;
      const data = await purchaseOrderService.getDetails(req.dbPool, id);
      return res.status(200).json({ success: true, data });
    } catch (error) {
      if (error.message === 'Purchase Order not found') {
        return res.status(404).json({ success: false, message: error.message });
      }
      next(error);
    }
  }
}

module.exports = new PurchaseOrderController();
