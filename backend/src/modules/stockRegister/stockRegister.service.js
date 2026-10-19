const stockRegisterRepository = require('./stockRegister.repository');

class StockRegisterService {
  async getRegisterOptions(dbPool) {
    return stockRegisterRepository.getRegisterOptions(dbPool);
  }
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
    const invalid = message => Object.assign(new Error(message), { status: 400 });
    for (const date of [filters.date_from, filters.date_to]) {
      if (typeof date !== 'string' || !/^\d{4}-\d{2}-\d{2}$/.test(date) ||
          !Number.isFinite(Date.parse(date)) || new Date(date).toISOString().slice(0, 10) !== date) {
        throw invalid('Valid Date From and Date To (YYYY-MM-DD) are required.');
      }
    }
    const days = (Date.parse(filters.date_to) - Date.parse(filters.date_from)) / 86400000;
    if (days < 0) throw invalid('Date To cannot be earlier than Date From.');
    if (filters.product_id && !/^[1-9]\d*$/.test(String(filters.product_id))) {
      throw invalid('A valid product ID is required.');
    }
    const rawCategories = filters.category_ids ?? filters.category_id;
    const categoryIds = rawCategories == null || rawCategories === '' ? []
      : (Array.isArray(rawCategories) ? rawCategories : String(rawCategories).split(','));
    if (categoryIds.some(id => !/^[1-9]\d*$/.test(String(id)))) {
      throw invalid('Valid category IDs are required.');
    }
    if (!filters.product_id) {
      if (days > 0) throw invalid('Only a single date is allowed for the Consolidated Stock Register.');
      if (!categoryIds.length) throw invalid('Please choose at least one category to generate the Consolidated report.');
    } else if (days > 31) {
      throw invalid('The maximum allowed date range for the Product-wise Stock Register is one month.');
    }
    // CakePHP deliberately ignores categories for product-wise searches/exports.
    return await stockRegisterRepository.getStockRegister(dbPool, {
      ...filters, category_id: undefined,
      category_ids: filters.product_id ? [] : [...new Set(categoryIds.map(String))]
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
