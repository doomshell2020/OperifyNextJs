const router = require('express').Router();
const jcMaterial = require('./jcMaterial.service');
const auth = require('../../middleware/auth');
const tenant = require('../../middleware/tenant');

router.use(auth);
router.use((req, res, next) => req.user?.db ? next() : res.status(400).json({ success: false, error: { message: 'Select a company.' } }));
router.use(tenant);

router.get('/options', async (req, res, next) => {
  try {
    const data = await jcMaterial.getOptions(req.dbPool);
    res.json({ success: true, data });
  } catch (error) {
    next(error);
  }
});

router.get('/', async (req, res, next) => {
  try {
    const page = req.query.page || 1;
    const limit = req.query.limit || 50;
    const result = await jcMaterial.jcResults(req.dbPool, req.query, page, limit);
    res.json({ success: true, ...result });
  } catch (error) {
    next(error);
  }
});

router.get('/export', async (req, res, next) => {
  try {
    await jcMaterial.exportExcel(req.dbPool, req.query, res);
  } catch (error) {
    if (res.headersSent) res.destroy(error);
    else next(error);
  }
});

module.exports = router;
