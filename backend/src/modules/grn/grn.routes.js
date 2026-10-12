const permission = require('../jobChallan/legacyPermission');
const express = require('express');
const router = express.Router();
const grnController = require('./grn.controller');
const requireAuth = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');
const { requireAnyPermission } = require('../../middleware/permission');

router.use(requireAuth);
router.use(tenantMiddleware);

router.get('/', permission('goodsreceived','index'), grnController.listGrns);
router.post('/', permission('goodsreceived','add'), grnController.createGrn);
router.get('/export', permission('goodsreceived','grnexcel'), grnController.exportGrns);
router.get('/inspection/:inspectionId', grnController.getInspectionForGrn);
router.get('/:id/pdf', requireAnyPermission(['legacy:admin/goodsreceived/index', 'legacy:admin/goodsreceived/view', 'legacy:admin/goodsreceived/viewgrndetail']), grnController.downloadPdf);
router.get('/:id', requireAnyPermission(['legacy:admin/goodsreceived/index', 'legacy:admin/goodsreceived/viewgrndetail']), grnController.getGrnDetails);

module.exports = router;
