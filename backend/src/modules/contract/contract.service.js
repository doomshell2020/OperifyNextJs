const contractRepository = require('./contract.repository');
const { QueryTypes } = require('sequelize');

function removeEmojis(value) {
  return String(value ?? '')
    .replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]/g, '')
    .replace(/[\u2600-\u27BF]/g, '');
}

function normalizeContractPayload(data = {}) {
  return {
    ...data,
    supplier_id: data.supplier_id || data.vendor_id || null,
    title: String(data.title || '').trim(),
    workorder: String(data.workorder || '').trim(),
    cost: data.cost ?? null,
    operation_cost: data.operation_cost ?? null,
    labour_cost: data.labour_cost ?? null,
    description: removeEmojis(data.description || ''),
    issuedate: data.issuedate || null,
    contract_start_date: data.contract_start_date || null,
    contract_end_date: data.contract_end_date || null,
    finished_products: Array.isArray(data.finished_products) ? data.finished_products : []
  };
}

class ContractService {
  async getContractsList(dbPool, filters) {
    return await contractRepository.findFiltered(dbPool, filters);
  }

  async getContractDetails(dbPool, id) {
    const contract = await contractRepository.findById(dbPool, id);
    if (!contract) return null;
    
    const items = await contractRepository.findItemsByContractId(dbPool, id);
    contract.production_labour = 0;
    contract.production_operation = 0;
    
    // Fetch raw materials (design sheet) for each item
    for (const item of items) {
      item.raw_materials = await contractRepository.findDesignSheetDetails(dbPool, id, item.product_id);
      const production = await contractRepository.getPdfProduction(dbPool, id, item.product_id);
      item.has_production = production.has_production;
      item.processes = production.processes;
      contract.production_labour += production.labour;
      contract.production_operation += production.operation;
    }

    const productionOrders = await contractRepository.findProductionOrdersByContractId(dbPool, id);
    const inspectionReports = await contractRepository.findInspectionReportsByContractId(dbPool, id);
    const siteDetailsRows = await dbPool.query("SELECT * FROM sitesettings_details WHERE status = 'Y' LIMIT 1", {
      type: QueryTypes.SELECT
    });
    const siteSettingRows = await dbPool.query('SELECT * FROM sitesettings LIMIT 1', {
      type: QueryTypes.SELECT
    });

    return { 
      contract, 
      items, 
      productionOrders, 
      inspectionReports,
      site_details: siteDetailsRows[0] || null,
      sitesetting: siteSettingRows[0] || null
    };
  }

  async getFormData(dbPool) {
    return await contractRepository.getFormData(dbPool);
  }

  async createContract(dbPool, data) {
    const payload = normalizeContractPayload(data);
    await this.validateContractPayload(dbPool, payload);

    const transaction = await dbPool.transaction();
    try {
      const contractId = await contractRepository.createContract(dbPool, payload, transaction);
      await contractRepository.upsertBom(dbPool, contractId, payload, transaction);
      await contractRepository.replaceFinishedProducts(dbPool, contractId, payload.finished_products, transaction);

      await transaction.commit();
      return contractId;
    } catch (error) {
      await transaction.rollback();
      throw error;
    }
  }

  async updateContract(dbPool, id, data) {
    const contract = await contractRepository.findById(dbPool, id);
    if (!contract) {
      const error = new Error('Contract not found.');
      error.statusCode = 404;
      throw error;
    }

    await this.ensureEditable(dbPool, id);
    const payload = normalizeContractPayload(data);
    await this.validateContractPayload(dbPool, payload, id);

    const transaction = await dbPool.transaction();
    try {
      await contractRepository.updateContract(dbPool, id, payload, transaction);
      await contractRepository.upsertBom(dbPool, id, payload, transaction);
      await contractRepository.replaceFinishedProducts(dbPool, id, payload.finished_products, transaction);

      await transaction.commit();
      return id;
    } catch (error) {
      await transaction.rollback();
      throw error;
    }
  }

  async deleteContract(dbPool, id) {
    const contract = await contractRepository.findById(dbPool, id);
    if (!contract) {
      const error = new Error('Contract not found.');
      error.statusCode = 404;
      throw error;
    }

    await this.ensureEditable(dbPool, id);

    const transaction = await dbPool.transaction();
    try {
      await contractRepository.deleteContractCascade(dbPool, id, transaction);
      await transaction.commit();
    } catch (error) {
      await transaction.rollback();
      throw error;
    }
  }

  async ensureEditable(dbPool, id) {
    const designSheetCount = await contractRepository.countDesignSheets(dbPool, id);
    if (designSheetCount > 0) {
      const error = new Error('Contract cannot be edited or deleted because a design sheet already exists.');
      error.statusCode = 409;
      throw error;
    }
  }

  async validateContractPayload(dbPool, payload, excludeId = null) {
    if (!payload.supplier_id || !(await contractRepository.vendorExists(dbPool, payload.supplier_id))) {
      const error = new Error('Your entered supplier does not exists.');
      error.statusCode = 400;
      throw error;
    }

    if (!payload.title || !payload.workorder || !payload.cost || !payload.operation_cost || !payload.labour_cost || !payload.issuedate || !payload.contract_start_date || !payload.contract_end_date) {
      const error = new Error('Please fill all required contract fields.');
      error.statusCode = 400;
      throw error;
    }

    if (await contractRepository.titleExists(dbPool, payload.title, excludeId)) {
      const error = new Error('Your entered Contract already exists.');
      error.statusCode = 409;
      throw error;
    }

    const validProducts = payload.finished_products.filter(product => product.product_id && product.quantity);
    if (validProducts.length === 0) {
      const error = new Error('Please add at least one finished product.');
      error.statusCode = 400;
      throw error;
    }

    const productIds = validProducts.map(product => String(product.product_id));
    if (new Set(productIds).size !== productIds.length) {
      const error = new Error('You cannot add the same finished product multiple times.');
      error.statusCode = 400;
      throw error;
    }

    payload.finished_products = validProducts;
  }
}

module.exports = new ContractService();
