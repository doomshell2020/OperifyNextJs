const express = require('express');
const permissionController = require('./permission.controller');
const authenticate = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');

const router = express.Router();

router.use(authenticate);
router.use(tenantMiddleware);

// Read-only compatibility catalog. Access is determined by session grants,
// never by whether a label is present or absent in this catalog.
router.get('/action-labels', async (req, res, next) => {
  try {
    const { centralSequelize } = require('../../config/sequelize');
    const { QueryTypes } = require('sequelize');
    const rows = await centralSequelize.query('SELECT LOWER(TRIM(url)) AS url FROM permission_label', { type: QueryTypes.SELECT });
    res.json({ success: true, data: { configured: rows.filter(row => row.url).map(row => `legacy:${row.url}`) } });
  } catch (error) { next(error); }
});

// Only allow superadmin (role 101)? Well, let's add a check in controller.

router.get('/managers', permissionController.getManagers);
router.post('/managers', permissionController.addManager);

router.post('/labels', permissionController.addLabel);

router.get('/roles', permissionController.getRoles);

router.get('/access', permissionController.getAccess);
router.post('/access', permissionController.updateAccess);

module.exports = router;
