const permission = require('../jobChallan/legacyPermission');
const express = require('express');
const router = express.Router();
const stockRegisterController = require('./stockRegister.controller');
const tenantMiddleware = require('../../middleware/tenant');
const authMiddleware = require('../../middleware/auth');
const { requireAnyPermission } = require('../../middleware/permission');

router.use(authMiddleware);
router.use(tenantMiddleware);

router.get('/categories', stockRegisterController.getCategories);
router.get('/daily', permission('stockregister','dailystock'), stockRegisterController.getDailyStock);
router.get('/daily/export', permission('stockregister','dailystockexcel'), stockRegisterController.exportDailyStockExcel);
router.get('/', permission('stockregister','index'), stockRegisterController.getStockRegister);
router.get('/export', permission('stockregister','summaryexcel'), stockRegisterController.exportExcel);
// PHP links receipt drill-down from the index without a separate action grant.
router.get('/details/received', requireAnyPermission([
  'legacy:admin/stockregister/index', 'legacy:admin/stockregister/receivedstock'
]), stockRegisterController.getReceivedStockDetails);
router.get('/details/dispatched', permission('stockregister','dispatchedstock'), stockRegisterController.getDispatchedStockDetails);

module.exports = router;
