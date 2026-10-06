const stockRegisterRepository = require('./stockRegister.repository');

class StockRegisterService {
  async getCategories(dbPool) {
    return await stockRegisterRepository.getCategories(dbPool);
  }

  async getDailyStockAsOfDate(dbPool, filters) {
    if (!filters.date) {
      throw new Error('date is required.');
    }
    return await stockRegisterRepository.getDailyStockAsOfDate(dbPool, filters);
  }

  async exportDailyStockExcel(dbPool, filters, res) {
    const data = await this.getDailyStockAsOfDate(dbPool, filters);
    return require('./stockRegister.export').daily(dbPool, filters, data, res);
  }

  async getStockRegister(dbPool, filters) {
    if (!filters.date_from || !filters.date_to) {
      throw new Error('date_from and date_to are required.');
    }

    // Validate that Date From cannot be greater than Date To
    if (new Date(filters.date_from) > new Date(filters.date_to)) {
      throw new Error('Date From cannot be greater than Date To.');
    }

    return await stockRegisterRepository.getStockRegister(dbPool, filters);
  }

  async getReceivedStockDetails(dbPool, filters) {
    if (!filters.date || !filters.product_id) {
      throw new Error('date and product_id are required.');
    }
    return await stockRegisterRepository.getReceivedStockDetails(dbPool, filters);
  }

  async getDispatchedStockDetails(dbPool, filters) {
    if (!filters.date || !filters.product_id) {
      throw new Error('date and product_id are required.');
    }
    return await stockRegisterRepository.getDispatchedStockDetails(dbPool, filters);
  }

  async exportExcel(dbPool, filters, res) {
    const data = await this.getStockRegister(dbPool, filters);
    return require('./stockRegister.export').detailed(dbPool, filters, data, res);
  }
}

module.exports = new StockRegisterService();
