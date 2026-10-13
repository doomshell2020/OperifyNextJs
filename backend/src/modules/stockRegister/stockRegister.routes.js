const permission = require('../jobChallan/legacyPermission');
const express = require('express');
const router = express.Router();
const stockRegisterController = require('./stockRegister.controller');
const tenantMiddleware = require('../../middleware/tenant');
const authMiddleware = require('../../middleware/auth');
const { requireAnyPermission } = require('../../middleware/permission');

router.use(authMiddleware);
router.use(tenantMiddleware);

// The new report belongs to the existing Stock Register view permission.
const monthly = require('./monthlyStock.service');
router.get('/monthly/options', permission('stockregister','index'), async (req,res,next) => {
  try { res.json({ success: true, data: await monthly.getOptions(req.dbPool) }); } catch (error) { next(error); }
});
router.get('/monthly', permission('stockregister','index'), async (req,res,next) => {
  try { res.json({ success: true, ...await monthly.getMonthlyStock(req.dbPool,req.query) }); } catch (error) { next(error); }
});
router.get('/monthly/export', permission('stockregister','index'), async (req,res,next) => {
  try { await monthly.exportExcel(req.dbPool,req.query,res); } catch (error) { next(error); }
});

router.get('/categories', stockRegisterController.getCategories);
router.get('/daily', permission('stockregister','dailystock'), stockRegisterController.getDailyStock);
router.get('/daily/export', permission('stockregister','dailystockexcel'), stockRegisterController.exportDailyStockExcel);
router.get('/', permission('stockregister','index'), stockRegisterController.getStockRegister);
// CakePHP searchstockregister.ctp exposes summaryexcel to users who can view
// the register, without checking a separate export action label.
router.get('/export', requireAnyPermission([
  'legacy:admin/stockregister/index', 'legacy:admin/stockregister/summaryexcel'
]), stockRegisterController.exportExcel);
// PHP links receipt drill-down from the index without a separate action grant.
router.get('/details/received', requireAnyPermission([
  'legacy:admin/stockregister/index', 'legacy:admin/stockregister/receivedstock'
]), stockRegisterController.getReceivedStockDetails);
router.get('/details/dispatched', permission('stockregister','dispatchedstock'), stockRegisterController.getDispatchedStockDetails);

module.exports = router;
