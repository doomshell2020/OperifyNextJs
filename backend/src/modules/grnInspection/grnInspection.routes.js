const permission = require('../jobChallan/legacyPermission');
const express = require('express');
const grnInspectionController = require('./grnInspection.controller');
const authenticate = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');

const router = express.Router();

router.use(authenticate);
router.use(tenantMiddleware);

router.get('/', permission('goodsreceived','grninspection'), grnInspectionController.listInspections);
router.post('/', permission('goodsreceived','add'), permission('goodsreceived','add_inspection_grn'), grnInspectionController.createInspection);
router.get('/next-id', grnInspectionController.getNextInspectionNumber);
router.get('/export/excel', permission('goodsreceived','grninspectionexcel'), grnInspectionController.exportInspections);
router.get('/po/:po_id', grnInspectionController.getPoDetails);
router.get('/:id', permission('goodsreceived','grninspection'), grnInspectionController.getDetails);

module.exports = router;
