const express = require('express');
const router = express.Router();
const stockRegisterController = require('./stockRegister.controller');
const tenantMiddleware = require('../../middleware/tenant');
const authMiddleware = require('../../middleware/auth');

router.use(authMiddleware);
router.use(tenantMiddleware);

router.get('/categories', stockRegisterController.getCategories);
router.get('/daily', stockRegisterController.getDailyStock);
router.get('/daily/export', stockRegisterController.exportDailyStockExcel);
router.get('/', stockRegisterController.getStockRegister);
router.get('/export', stockRegisterController.exportExcel);
router.get('/details/received', stockRegisterController.getReceivedStockDetails);
router.get('/details/dispatched', stockRegisterController.getDispatchedStockDetails);

module.exports = router;
