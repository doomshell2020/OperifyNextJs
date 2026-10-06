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
      const result = await purchaseOrderRepository.createRevision(
        dbPool,
        id,
        poData.po || {},
        poData.items || [],
        transaction
      );

      await transaction.commit();
      return { success: true, data: result };
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
      const V=require('../../utils/receiptValidation');
      if(!poData) throw V.invalid('Purchase Order is required.');
      // Serialize previews/new numbering and reject replay before inserting any rows.
      await V.select(dbPool,'SELECT id FROM st_purchaseorder ORDER BY id DESC LIMIT 1 FOR UPDATE',{},transaction);
      if (!poData.purchaseorder_id) {
        poData.purchaseorder_id = await purchaseOrderRepository.getNextPoNumber(dbPool,transaction);
      }
      const existing=await V.select(dbPool,'SELECT id FROM st_purchaseorder WHERE purchaseorder_id=:number LIMIT 1 FOR UPDATE',{number:poData.purchaseorder_id},transaction);
      if(existing.length)throw V.invalid('Purchase Order number already exists. Refresh the number before creating another PO.',409);
      const validated=await require('./purchaseOrder.create-validation')(dbPool,poData,items,transaction);

      const result = await purchaseOrderRepository.createPurchaseOrder(dbPool, validated.po, validated.items, transaction);

      await transaction.commit();
      return { success: true, data: result };
    } catch (error) {
      await transaction.rollback();
      throw error;
    }
  }
}

module.exports = new PurchaseOrderService();
