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

  async getItemHistory(dbPool, itemId) {
    return purchaseOrderRepository.getItemHistory(dbPool, itemId);
  }

  async listPurchaseOrders(dbPool, filters, page = 1, limit = 10) {
    const offset = (page - 1) * limit;
    const items = await purchaseOrderRepository.listPurchaseOrders(dbPool, filters, offset, limit);
    const total = await purchaseOrderRepository.countPurchaseOrders(dbPool, filters);
    
    return {
      items,
      total,
      page,
      limit,
      totalPages: Math.ceil(total / limit)
    };
  }

  async revisePurchaseOrder(dbPool, id, poData) {
    let connection;
    try {
      connection = await dbPool.getConnection();
      await connection.beginTransaction();

      await purchaseOrderRepository.updatePurchaseOrder(connection, id, poData.po);
      if (poData.items && poData.items.length > 0) {
        await purchaseOrderRepository.updatePurchaseOrderItems(connection, id, poData.po.po_number, poData.items);
      }

      await connection.commit();
      return { success: true };
    } catch (error) {
      if (connection) await connection.rollback();
      throw error;
    } finally {
      if (connection) connection.release();
    }
  }

  async addDeliveryNote(dbPool, id, data) {
    let connection;
    try {
      connection = await dbPool.getConnection();
      await connection.beginTransaction();

      const { po_number, vendor_id, items, remarks } = data;
      await purchaseOrderRepository.addDeliveryNote(connection, id, po_number, vendor_id, items, remarks);

      await connection.commit();
      return { success: true };
    } catch (error) {
      if (connection) await connection.rollback();
      throw error;
    } finally {
      if (connection) connection.release();
    }
  }

  async deletePurchaseOrder(dbPool, id) {
    let connection;
    try {
      connection = await dbPool.getConnection();
      await connection.beginTransaction();

      await purchaseOrderRepository.deletePurchaseOrder(connection, id);

      await connection.commit();
      return { success: true };
    } catch (error) {
      if (connection) await connection.rollback();
      throw error;
    } finally {
      if (connection) connection.release();
    }
  }

  async getNextPoNumber(dbPool) {
    return await purchaseOrderRepository.getNextPoNumber(dbPool);
  }

  async createPurchaseOrder(dbPool, poData, items) {
    let connection;
    try {
      connection = await dbPool.getConnection();
      await connection.beginTransaction();

      // Ensure a purchaseorder_id exists, otherwise generate one
      if (!poData.purchaseorder_id) {
        poData.purchaseorder_id = await purchaseOrderRepository.getNextPoNumber(dbPool);
      }

      const result = await purchaseOrderRepository.createPurchaseOrder(connection, poData, items);

      await connection.commit();
      return { success: true, data: result };
    } catch (error) {
      if (connection) await connection.rollback();
      throw error;
    } finally {
      if (connection) connection.release();
    }
  }
}

module.exports = new PurchaseOrderService();
