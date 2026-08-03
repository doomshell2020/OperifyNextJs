const contractRepository = require('./contract.repository');

class ContractService {
  async getContractsList(dbPool, filters) {
    return await contractRepository.findFiltered(dbPool, filters);
  }

  async getContractDetails(dbPool, id) {
    const contract = await contractRepository.findById(dbPool, id);
    if (!contract) return null;
    
    const items = await contractRepository.findItemsByContractId(dbPool, id);
    
    // Fetch raw materials (design sheet) for each item
    for (const item of items) {
      item.raw_materials = await contractRepository.findDesignSheetDetails(dbPool, id, item.product_id);
    }

    const productionOrders = await contractRepository.findProductionOrdersByContractId(dbPool, id);
    const inspectionReports = await contractRepository.findInspectionReportsByContractId(dbPool, id);

    return { 
      contract, 
      items, 
      productionOrders, 
      inspectionReports 
    };
  }

  async getFormData(dbPool) {
    return await contractRepository.getFormData(dbPool);
  }

  async createContract(dbPool, data) {
    const connection = await dbPool.getConnection();
    try {
      await connection.beginTransaction();
      
      const contractId = await contractRepository.createContract(connection, data);
      
      if (data.finished_products && Array.isArray(data.finished_products)) {
        for (const product of data.finished_products) {
          if (product.product_id) {
            await contractRepository.addFinishedProduct(connection, contractId, product);
          }
        }
      }
      
      await connection.commit();
      return contractId;
    } catch (error) {
      await connection.rollback();
      throw error;
    } finally {
      connection.release();
    }
  }
}

module.exports = new ContractService();
