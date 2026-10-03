const dashboardService = require('./dashboard.service');

function isCentralDb(req) {
  const centralDbName = process.env.DB_NAME || 'operify';
  return !req.dbName || req.dbName === centralDbName;
}

const emptySummary = {
  contracts: { total: 0, today: 0, week: 0, month: 0, trend: { percentage: '0%', isUp: true, label: 'vs total volume' }, sparkline: [0,0,0,0,0,0,0] },
  purchaseOrders: { total: 0, today: 0, week: 0, month: 0, trend: { percentage: '0%', isUp: true, label: 'vs total volume' }, sparkline: [0,0,0,0,0,0,0] },
  grn: { total: 0, today: 0, week: 0, month: 0, trend: { percentage: '0%', isUp: true, label: 'vs total volume' }, sparkline: [0,0,0,0,0,0,0] },
  vendors: { total: 0, today: 0, week: 0, month: 0, trend: { percentage: '0%', isUp: true, label: 'vs total volume' }, sparkline: [0,0,0,0,0,0,0] },
  maintenance: { total: 0, today: 0, week: 0, month: 0, trend: { percentage: '0%', isUp: true, label: 'vs total volume' }, sparkline: [0,0,0,0,0,0,0] }
};

const emptyCharts = {
  purchaseOrder: [],
  production: [],
  maintenance: []
};

class DashboardController {
  async getSummary(req, res, next) {
    try {
      if (isCentralDb(req)) return res.status(200).json({ success: true, data: emptySummary });
      const data = await dashboardService.getSummary(req.dbPool);
      return res.status(200).json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }

  async getCharts(req, res, next) {
    try {
      if (isCentralDb(req)) return res.status(200).json({ success: true, data: emptyCharts });
      const data = await dashboardService.getCharts(req.dbPool);
      return res.status(200).json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }

  async getLatestPurchaseOrders(req, res, next) {
    try {
      if (isCentralDb(req)) return res.status(200).json({ success: true, data: [] });
      const data = await dashboardService.getLatestPurchaseOrders(req.dbPool);
      return res.status(200).json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }

  async getLatestProduction(req, res, next) {
    try {
      if (isCentralDb(req)) return res.status(200).json({ success: true, data: [] });
      const data = await dashboardService.getLatestProduction(req.dbPool);
      return res.status(200).json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }

  async getLatestMaintenance(req, res, next) {
    try {
      if (isCentralDb(req)) return res.status(200).json({ success: true, data: [] });
      const data = await dashboardService.getLatestMaintenance(req.dbPool);
      return res.status(200).json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }

  async getLatestInspection(req, res, next) {
    try {
      if (isCentralDb(req)) return res.status(200).json({ success: true, data: [] });
      const data = await dashboardService.getLatestInspection(req.dbPool);
      return res.status(200).json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }

  async getLatestGrn(req, res, next) {
    try {
      if (isCentralDb(req)) return res.status(200).json({ success: true, data: [] });
      const data = await dashboardService.getLatestGrn(req.dbPool);
      return res.status(200).json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }
}

module.exports = new DashboardController();
