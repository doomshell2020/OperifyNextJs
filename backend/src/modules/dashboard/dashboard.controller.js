const dashboardService = require('./dashboard.service');

class DashboardController {
  async getSummary(req, res, next) {
    try {
      const data = await dashboardService.getSummary(req.dbPool);
      return res.status(200).json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }

  async getCharts(req, res, next) {
    try {
      const data = await dashboardService.getCharts(req.dbPool);
      return res.status(200).json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }

  async getLatestPurchaseOrders(req, res, next) {
    try {
      const data = await dashboardService.getLatestPurchaseOrders(req.dbPool);
      return res.status(200).json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }

  async getLatestProduction(req, res, next) {
    try {
      const data = await dashboardService.getLatestProduction(req.dbPool);
      return res.status(200).json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }

  async getLatestMaintenance(req, res, next) {
    try {
      const data = await dashboardService.getLatestMaintenance(req.dbPool);
      return res.status(200).json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }

  async getLatestInspection(req, res, next) {
    try {
      const data = await dashboardService.getLatestInspection(req.dbPool);
      return res.status(200).json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }

  async getLatestGrn(req, res, next) {
    try {
      const data = await dashboardService.getLatestGrn(req.dbPool);
      return res.status(200).json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }
}

module.exports = new DashboardController();
