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

    const rawCategories = filters.category_ids;
    const categoryIds = rawCategories == null || rawCategories === '' ? []
      : (Array.isArray(rawCategories) ? rawCategories : String(rawCategories).split(','));
    if (categoryIds.some(id => !/^[1-9]\d*$/.test(String(id)))) {
      const error = new Error('Valid category IDs are required.');
      error.status = 400;
      throw error;
    }
    return await stockRegisterRepository.getStockRegister(dbPool, {
      ...filters, category_ids: [...new Set(categoryIds.map(String))]
    });
  }

  async getReceivedStockDetails(dbPool, filters) {
    const { date, product_id } = filters;
    if (typeof date !== 'string' || !/^\d{4}-\d{2}-\d{2}$/.test(date) ||
        Number.isNaN(Date.parse(date)) || new Date(date).toISOString().slice(0, 10) !== date ||
        !/^[1-9]\d*$/.test(String(product_id))) {
      const error = new Error('A valid date (YYYY-MM-DD) and product_id are required.');
      error.status = 400;
      throw error;
    }
    return await stockRegisterRepository.getReceivedStockDetails(dbPool, filters);
  }

  async getReceivedStockProduct(dbPool, product_id) {
    return stockRegisterRepository.getReceivedStockProduct(dbPool, product_id);
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
