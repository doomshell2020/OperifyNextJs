const express = require('express');
const grnInspectionController = require('./grnInspection.controller');
const authenticate = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');

const router = express.Router();

router.use(authenticate);
router.use(tenantMiddleware);

router.get('/', grnInspectionController.listInspections);
router.post('/', grnInspectionController.createInspection);
router.get('/next-id', grnInspectionController.getNextInspectionNumber);
router.get('/po/:po_id', grnInspectionController.getPoDetails);
router.get('/:id', grnInspectionController.getDetails);

module.exports = router;
