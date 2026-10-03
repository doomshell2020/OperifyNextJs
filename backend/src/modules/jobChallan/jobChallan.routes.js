const express = require('express');
const jobChallanController = require('./jobChallan.controller');
const authenticate = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');

const router = express.Router();

router.use(authenticate);
router.use(tenantMiddleware);

// Lookup / helper endpoints
router.get('/vendors',           jobChallanController.listVendors.bind(jobChallanController));
router.get('/tax-master',        jobChallanController.listTaxMaster.bind(jobChallanController));
router.get('/search-items',      jobChallanController.searchItems.bind(jobChallanController));
router.get('/item-stock',        jobChallanController.getItemStock.bind(jobChallanController));
router.get('/vendor-gst',        jobChallanController.getVendorGst.bind(jobChallanController));

// Core CRUD
router.get('/',    jobChallanController.list.bind(jobChallanController));
router.post('/',   jobChallanController.create.bind(jobChallanController));
router.get('/:id/pdf', jobChallanController.downloadPdf.bind(jobChallanController));
router.get('/:id', jobChallanController.getDetail.bind(jobChallanController));
router.delete('/:id', jobChallanController.remove.bind(jobChallanController));

module.exports = router;
