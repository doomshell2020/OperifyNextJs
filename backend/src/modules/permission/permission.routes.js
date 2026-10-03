const express = require('express');
const permissionController = require('./permission.controller');
const authenticate = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');

const router = express.Router();

router.use(authenticate);
router.use(tenantMiddleware);

// Only allow superadmin (role 101)? Well, let's add a check in controller.

router.get('/managers', permissionController.getManagers);
router.post('/managers', permissionController.addManager);

router.post('/labels', permissionController.addLabel);

router.get('/roles', permissionController.getRoles);

router.get('/access', permissionController.getAccess);
router.post('/access', permissionController.updateAccess);

module.exports = router;
