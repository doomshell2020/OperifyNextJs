const express = require('express');
const dashboardController = require('./dashboard.controller');
const authenticate = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');

const router = express.Router();

// All dashboard endpoints require authentication & tenant context mapping
router.use(authenticate);
router.use(tenantMiddleware);

router.get('/summary', dashboardController.getSummary);
router.get('/charts', dashboardController.getCharts);
router.get('/latest-purchase-orders', dashboardController.getLatestPurchaseOrders);
router.get('/latest-production', dashboardController.getLatestProduction);
router.get('/latest-maintenance', dashboardController.getLatestMaintenance);
router.get('/latest-inspection', dashboardController.getLatestInspection);
router.get('/latest-grn', dashboardController.getLatestGrn);

module.exports = router;
