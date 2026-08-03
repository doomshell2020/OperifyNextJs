const express = require('express');
const router = express.Router();
const paymentController = require('./payment.controller');
const authenticate = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');

router.use(authenticate);
router.use(tenantMiddleware);

router.get('/', (req, res, next) => paymentController.getPaymentsList(req, res, next));
router.get('/vendors', (req, res, next) => paymentController.getVendors(req, res, next));
router.get('/particular', (req, res, next) => paymentController.getParticularPayments(req, res, next));
router.get('/:id/details', (req, res, next) => paymentController.getPaymentDetail(req, res, next));

module.exports = router;
