const express = require('express');
const purchaseOrderController = require('./purchaseOrder.controller');
const authenticate = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');
const { requirePermission, requireAnyPermission } = require('../../middleware/permission');

const router = express.Router();

// Enforce auth and multi-tenancy contexts
router.use(authenticate);
router.use(tenantMiddleware);

router.get('/', purchaseOrderController.listPurchaseOrders);
router.post('/', requirePermission('purchaseorder:add'), purchaseOrderController.createPurchaseOrder);
router.get('/next-id', purchaseOrderController.getNextPoNumber);
router.get('/item/:itemId/history', purchaseOrderController.getItemHistory);
router.get('/:id', purchaseOrderController.getDetails); // Alias for consistency with new API standard
router.get('/:id/pdf', requireAnyPermission([
  'purchaseorder:pdf',
  'purchaseorder:view',
  'legacy:admin/purchaseorder/view',
  'legacy:admin/purchaseorder/viewpodetailspdf',
  'legacy:admin/purchaseorder/printallpo'
]), purchaseOrderController.generatePdf);
router.get('/:id/hover', purchaseOrderController.getHoverDetails);
router.get('/:id/details', purchaseOrderController.getDetails);
router.put('/:id', requireAnyPermission(['purchaseorder:revise', 'legacy:admin/purchaseorder/revised']), purchaseOrderController.revisePurchaseOrder);
router.delete('/:id', requireAnyPermission(['purchaseorder:delete', 'legacy:admin/purchaseorder/delete']), purchaseOrderController.deletePurchaseOrder);
router.post('/:id/delivery-note', requireAnyPermission(['purchaseorder:deliverynote', 'legacy:admin/purchaseorder/deliverynote']), purchaseOrderController.addDeliveryNote);

module.exports = router;
