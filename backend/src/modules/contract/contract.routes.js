const express = require('express');
const router = express.Router();
const contractController = require('./contract.controller');
const authenticate = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');

// Mount routes after authentication and tenant pool binding
router.use(authenticate);
router.use(tenantMiddleware);

router.get('/', contractController.getContracts);
router.post('/', contractController.createContract);
router.get('/form-data', contractController.getFormData);
router.get('/:id/details', contractController.getDetails);
router.get('/:id/pdf', contractController.exportPDF);

module.exports = router;
