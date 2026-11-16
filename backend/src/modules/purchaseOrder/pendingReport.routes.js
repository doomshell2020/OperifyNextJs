const router = require('express').Router();
const report = require('./pendingReport');
const { requireAnyPermission } = require('../../middleware/permission');
router.use(require('../../middleware/auth'));
router.use((req, res, next) => req.user?.db ? next() : res.status(400).json({ success: false, error: { message: 'Select a company.' } }));
router.use(require('../../middleware/tenant'));
router.use(requireAnyPermission(['legacy:admin/purchaseorder/index', 'legacy:admin/purchaseorder/posummaryreport']));
router.get('/options', async (req, res, next) => {
  try {
    const { QueryTypes } = require('sequelize');
    const [items, suppliers] = await Promise.all([
      req.dbPool.query("SELECT id,item_name name FROM st_additem WHERE itemtype='RawMaterial' ORDER BY item_name,id", { type: QueryTypes.SELECT }),
      req.dbPool.query('SELECT id,name FROM vendors ORDER BY name,id', { type: QueryTypes.SELECT }),
    ]);
    res.json({ success: true, data: { items, suppliers } });
  } catch (error) { next(error); }
});
router.get('/', async (req, res, next) => {
  try { res.json({ success: true, ...await report.list(req.dbPool, req.query) }); } catch (error) { next(error); }
});
router.get('/export', async (req, res, next) => {
  try { await report.exportExcel(req.dbPool, req.query, res); } catch (error) { if (res.headersSent) res.destroy(error); else next(error); }
});
module.exports = router;
