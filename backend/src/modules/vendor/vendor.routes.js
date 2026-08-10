const express = require('express');
const vendorController = require('./vendor.controller');
const authenticate = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');

const router = express.Router();

router.use(authenticate);
router.use(tenantMiddleware);

router.get('/search', vendorController.searchVendors);
router.get('/:id', vendorController.getVendor);
router.put('/:id', vendorController.updateVendor);

module.exports = router;
