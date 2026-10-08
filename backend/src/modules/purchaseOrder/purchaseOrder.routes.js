const permission=require('../jobChallan/legacyPermission');
const express = require('express');
const purchaseOrderController = require('./purchaseOrder.controller');
const authenticate = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');
const { requirePermission, requireAnyPermission } = require('../../middleware/permission');

const router = express.Router();

// Enforce auth and multi-tenancy contexts
router.use(authenticate);
router.use(tenantMiddleware);

router.get('/', permission('purchaseorder','index'), purchaseOrderController.listPurchaseOrders);
router.get('/export/excel', (req,res,next)=>permission('purchaseorder',req.query.type==='deli'?'deliveryreport':req.query.type==='comp'?'productcomparisonreport':'posummaryreport')(req,res,next),async(req,res,next)=>{try{const book=await require('./purchaseOrder.export').workbook(req.dbPool,req.query);res.setHeader('Content-Type','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');res.setHeader('Content-Disposition','attachment; filename=Purchase_Order_Report.xlsx');res.send(Buffer.from(await book.xlsx.writeBuffer()));}catch(error){next(error);}});
router.post('/', requirePermission('purchaseorder:add'), purchaseOrderController.createPurchaseOrder);
router.get('/next-id', purchaseOrderController.getNextPoNumber);
router.get('/item/:itemId/history', purchaseOrderController.getItemHistory);
router.get('/:id', permission('purchaseorder','viewpodetail'), purchaseOrderController.getDetails); // Alias for consistency with new API standard
router.get('/:id/pdf', permission('purchaseorder','view'), purchaseOrderController.generatePdf);
router.get('/:id/hover', permission('purchaseorder','viewpodetail'), purchaseOrderController.getHoverDetails);
router.get('/:id/details', permission('purchaseorder','viewpodetail'), purchaseOrderController.getDetails);
router.put('/:id', requireAnyPermission(['purchaseorder:revise', 'legacy:admin/purchaseorder/revised']), purchaseOrderController.revisePurchaseOrder);
router.delete('/:id', requireAnyPermission(['purchaseorder:delete', 'legacy:admin/purchaseorder/delete']), purchaseOrderController.deletePurchaseOrder);
router.post('/:id/delivery-note', requireAnyPermission(['purchaseorder:deliverynote', 'legacy:admin/purchaseorder/deliverynote']), purchaseOrderController.addDeliveryNote);

module.exports = router;
