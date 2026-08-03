const express = require('express');
const router = express.Router();
const indentpoController = require('./indentpo.controller');
const auth = require('../../middleware/auth');
const tenant = require('../../middleware/tenant');

// All routes require authentication and tenant db connection
router.use(auth);
router.use(tenant);

// Endpoints
router.get('/next-id', indentpoController.getNextIndentId);
router.get('/contracts/search', indentpoController.searchContracts);
router.get('/contracts/:contract_id/products', indentpoController.getContractProducts);
router.get('/machines/search', indentpoController.searchMachines);
router.get('/designsheet', indentpoController.getDesignSheetDetails);

// CRUD
router.post('/', indentpoController.saveIndentpo);
router.get('/', indentpoController.listIndentpo);
router.get('/:indent_id/detail', indentpoController.getIndentpoDetail);
router.get('/view-details/:id', indentpoController.getIndentPoDetails);

module.exports = router;
