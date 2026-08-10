const express = require('express');
const router = express.Router();
const grnController = require('./grn.controller');
const requireAuth = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');

router.use(requireAuth);
router.use(tenantMiddleware);

router.get('/', grnController.listGrns);
router.post('/', grnController.createGrn);
router.get('/export', grnController.exportGrns);
router.get('/inspection/:inspectionId', grnController.getInspectionForGrn);
router.get('/:id', grnController.getGrnDetails);
router.put('/:id', grnController.updateGrn);
router.delete('/:id', grnController.deleteGrn);

module.exports = router;
