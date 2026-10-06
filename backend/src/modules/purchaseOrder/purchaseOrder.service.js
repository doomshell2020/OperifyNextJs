const purchaseOrderRepository = require('./purchaseOrder.repository');
const {paginationInput,paginationResult} = require('../../utils/listPagination');

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

  async getItemHistory(dbPool, itemId) {
    return purchaseOrderRepository.getItemHistory(dbPool, itemId);
  }

  async listPurchaseOrders(dbPool, filters, page, limit) {
    ({page,limit} = paginationInput({page,limit},'purchaseOrder'));
    const offset = (page - 1) * limit;
    const items = await purchaseOrderRepository.listPurchaseOrders(dbPool, filters, offset, limit);
    const total = await purchaseOrderRepository.countPurchaseOrders(dbPool, filters);
    
    return {
      items,
      ...paginationResult(total,page,limit,items.length)
    };
  }

  async revisePurchaseOrder(dbPool, id, poData) {
    const transaction = await dbPool.transaction();
    try {
      await purchaseOrderRepository.updatePurchaseOrder(dbPool, id, poData.po, transaction);
      if (poData.items && poData.items.length > 0) {
        await purchaseOrderRepository.updatePurchaseOrderItems(dbPool, id, poData.po.po_number, poData.items, transaction);
      }

      await transaction.commit();
      return { success: true };
    } catch (error) {
      await transaction.rollback();
      throw error;
    }
  }

  async addDeliveryNote(dbPool, id, data) {
    const transaction = await dbPool.transaction();
    try {
      const { po_number, vendor_id, schedules, remark } = data;
      await purchaseOrderRepository.addDeliveryNote(dbPool, id, po_number, vendor_id, schedules, remark, transaction);

      await transaction.commit();
      return { success: true };
    } catch (error) {
      await transaction.rollback();
      throw error;
    }
  }

  async deletePurchaseOrder(dbPool, id) {
    const transaction = await dbPool.transaction();
    try {
      await purchaseOrderRepository.deletePurchaseOrder(dbPool, id, transaction);

      await transaction.commit();
      return { success: true };
    } catch (error) {
      await transaction.rollback();
      throw error;
    }
  }

  async getNextPoNumber(dbPool) {
    return await purchaseOrderRepository.getNextPoNumber(dbPool);
  }

  async createPurchaseOrder(dbPool, poData, items) {
    const transaction = await dbPool.transaction();
    try {
      if (!poData.purchaseorder_id) {
        poData.purchaseorder_id = await purchaseOrderRepository.getNextPoNumber(dbPool);
      }

      const result = await purchaseOrderRepository.createPurchaseOrder(dbPool, poData, items, transaction);

      await transaction.commit();
      return { success: true, data: result };
    } catch (error) {
      await transaction.rollback();
      throw error;
    }
  }
}

module.exports = new PurchaseOrderService();
