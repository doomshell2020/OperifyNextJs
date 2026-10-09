const express = require('express');
const permission = require('../jobChallan/legacyPermission');
const dashboardController = require('./dashboard.controller');
const authenticate = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');

const router = express.Router();

// All dashboard endpoints require authentication & tenant context mapping
router.use(authenticate);
router.use(tenantMiddleware);

router.get('/summary', dashboardController.getSummary);
router.get('/charts', dashboardController.getCharts);
router.get('/latest-purchase-orders', permission('purchaseorder','index'), dashboardController.getLatestPurchaseOrders);
router.get('/latest-production', permission('production','productionorders'), dashboardController.getLatestProduction);
router.get('/latest-maintenance', permission('maintenance','index'), dashboardController.getLatestMaintenance);
router.get('/latest-inspection', permission('goodsreceived','grninspection'), dashboardController.getLatestInspection);
router.get('/latest-grn', permission('goodsreceived','index'), dashboardController.getLatestGrn);

module.exports = router;
