const permission = require('../jobChallan/legacyPermission');
const express = require('express');
const router = express.Router();
const grnController = require('./grn.controller');
const requireAuth = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');

router.use(requireAuth);
router.use(tenantMiddleware);

router.get('/', permission('goodsreceived','index'), grnController.listGrns);
router.post('/', permission('goodsreceived','add'), grnController.createGrn);
router.get('/export', permission('goodsreceived','grnexcel'), grnController.exportGrns);
router.get('/inspection/:inspectionId', grnController.getInspectionForGrn);
router.get('/:id/pdf', permission('goodsreceived','view'), grnController.downloadPdf);
router.get('/:id', permission('goodsreceived','viewgrndetail'), grnController.getGrnDetails);

module.exports = router;
