const express = require('express');
const purchaseOrderController = require('./purchaseOrder.controller');
const authenticate = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');

const router = express.Router();

// Enforce auth and multi-tenancy contexts
router.use(authenticate);
router.use(tenantMiddleware);

router.get('/:id/hover', purchaseOrderController.getHoverDetails);
router.get('/:id/details', purchaseOrderController.getDetails);

module.exports = router;
