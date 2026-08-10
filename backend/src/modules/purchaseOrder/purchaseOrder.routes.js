const express = require('express');
const purchaseOrderController = require('./purchaseOrder.controller');
const authenticate = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');

const router = express.Router();

// Enforce auth and multi-tenancy contexts
router.use(authenticate);
router.use(tenantMiddleware);

router.get('/', purchaseOrderController.listPurchaseOrders);
router.post('/', purchaseOrderController.createPurchaseOrder);
router.get('/next-id', purchaseOrderController.getNextPoNumber);
router.get('/item/:itemId/history', purchaseOrderController.getItemHistory);
router.get('/:id', purchaseOrderController.getDetails); // Alias for consistency with new API standard
router.get('/:id/pdf', purchaseOrderController.generatePdf);
router.get('/:id/hover', purchaseOrderController.getHoverDetails);
router.get('/:id/details', purchaseOrderController.getDetails);
router.put('/:id', purchaseOrderController.revisePurchaseOrder);
router.delete('/:id', purchaseOrderController.deletePurchaseOrder);
router.post('/:id/delivery-note', purchaseOrderController.addDeliveryNote);

module.exports = router;
