const express = require('express');
const router = express.Router();
const quotationController = require('./quotation.controller');
const authenticate = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');

router.use(authenticate);
router.use(tenantMiddleware);

router.get('/', (req, res, next) => quotationController.getQuotationsList(req, res, next));
router.get('/vendors', (req, res, next) => quotationController.getVendors(req, res, next));
router.get('/:id/details', (req, res, next) => quotationController.getQuotationDetail(req, res, next));

module.exports = router;
