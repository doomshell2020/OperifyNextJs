const purchaseOrderRepository = require('./purchaseOrder.repository');

class PurchaseOrderService {
  async getHoverDetails(dbPool, idOrNumber) {
    const data = await purchaseOrderRepository.getHoverDetails(dbPool, idOrNumber);
    if (!data) {
      throw new Error('Purchase Order not found');
    }
    return data;
  }

  async getDetails(dbPool, idOrNumber) {
    const data = await purchaseOrderRepository.getDetails(dbPool, idOrNumber);
    if (!data) {
      throw new Error('Purchase Order not found');
    }
    return data;
  }
}

module.exports = new PurchaseOrderService();
