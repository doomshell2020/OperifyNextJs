const permission = require('../jobChallan/legacyPermission');
const express = require('express');
const vendorController = require('./vendor.controller');
const authenticate = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');

const router = express.Router();

router.use(authenticate);
router.use(tenantMiddleware);

router.get('/search', vendorController.searchVendors);
router.get('/:id', permission('vendors','viewdetail'), vendorController.getVendor);
router.put('/:id', permission('vendors','add'), vendorController.updateVendor);

module.exports = router;
