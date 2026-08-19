const stockRegisterService = require('./stockRegister.service');

class StockRegisterController {
  async getCategories(req, res, next) {
    try {
      const data = await stockRegisterService.getCategories(req.dbPool);
      res.json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }

  async getDailyStock(req, res, next) {
    try {
      const filters = {
        date: req.query.date,
        category_ids: req.query.category_ids
      };

      if (filters.category_ids && typeof filters.category_ids === 'string') {
        filters.category_ids = filters.category_ids.split(',');
      }

      const data = await stockRegisterService.getDailyStockAsOfDate(req.dbPool, filters);
      
      res.json({
        success: true,
        message: 'Daily Stock retrieved successfully',
        data
      });
    } catch (error) {
      next(error);
    }
  }

  async exportDailyStockExcel(req, res, next) {
    try {
      const filters = {
        date: req.query.date,
        category_ids: req.query.category_ids
      };

      if (filters.category_ids && typeof filters.category_ids === 'string') {
        filters.category_ids = filters.category_ids.split(',');
      }

      await stockRegisterService.exportDailyStockExcel(req.dbPool, filters, res);
    } catch (error) {
      next(error);
    }
  }

  async getStockRegister(req, res, next) {
    try {
      const filters = {
        product_id: req.query.product_id,
        category_id: req.query.category_id,
        date_from: req.query.date_from,
        date_to: req.query.date_to
      };

      const data = await stockRegisterService.getStockRegister(req.dbPool, filters);
      
      res.json({
        success: true,
        message: 'Stock Register retrieved successfully',
        data
      });
    } catch (error) {
      next(error);
    }
  }

  async getReceivedStockDetails(req, res, next) {
    try {
      const filters = {
        product_id: req.query.product_id,
        date: req.query.date
      };
      const data = await stockRegisterService.getReceivedStockDetails(req.dbPool, filters);
      res.json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }

  async getDispatchedStockDetails(req, res, next) {
    try {
      const filters = {
        product_id: req.query.product_id,
        date: req.query.date
      };
      const data = await stockRegisterService.getDispatchedStockDetails(req.dbPool, filters);
      res.json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }

  async exportExcel(req, res, next) {
    try {
      const filters = {
        product_id: req.query.product_id,
        category_id: req.query.category_id,
        date_from: req.query.date_from,
        date_to: req.query.date_to
      };

      await stockRegisterService.exportExcel(req.dbPool, filters, res);
    } catch (error) {
      next(error);
    }
  }
}

module.exports = new StockRegisterController();
