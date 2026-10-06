const permission = require('../jobChallan/legacyPermission');
const express = require('express');
const router = express.Router();
const stockRegisterController = require('./stockRegister.controller');
const tenantMiddleware = require('../../middleware/tenant');
const authMiddleware = require('../../middleware/auth');

router.use(authMiddleware);
router.use(tenantMiddleware);

router.get('/categories', stockRegisterController.getCategories);
router.get('/daily', permission('stockregister','dailystock'), stockRegisterController.getDailyStock);
router.get('/daily/export', permission('stockregister','dailystockexcel'), stockRegisterController.exportDailyStockExcel);
router.get('/', permission('stockregister','index'), stockRegisterController.getStockRegister);
router.get('/export', permission('stockregister','summaryexcel'), stockRegisterController.exportExcel);
router.get('/details/received', stockRegisterController.getReceivedStockDetails);
router.get('/details/dispatched', stockRegisterController.getDispatchedStockDetails);

module.exports = router;
