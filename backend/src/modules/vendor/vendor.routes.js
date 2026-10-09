const permission = require('../jobChallan/legacyPermission');
const express = require('express');
const vendorController = require('./vendor.controller');
const authenticate = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');
const { requireAnyPermission } = require('../../middleware/permission');

const router = express.Router();

router.use(authenticate);
router.use(tenantMiddleware);

router.get('/search', vendorController.searchVendors);
// PHP exposes Vendor Details from both authorized vendor and PO lists.
router.get('/:id', requireAnyPermission(['legacy:admin/vendors/viewdetail', 'legacy:admin/vendors/index', 'legacy:admin/purchaseorder/index']), vendorController.getVendor);
router.put('/:id', permission('vendors','add'), vendorController.updateVendor);

module.exports = router;
