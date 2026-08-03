const express = require('express');
const indentController = require('./indent.controller');
const authenticate = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');

const router = express.Router();

router.use(authenticate);
router.use(tenantMiddleware);

// Utility
router.get('/next-id', indentController.getNextIndentId.bind(indentController));
router.get('/items/search', indentController.searchItems.bind(indentController));

// Temp item management (cart-style before finalize)
router.post('/temp', indentController.addTempItem.bind(indentController));
router.delete('/temp/:id', indentController.removeTempItem.bind(indentController));
router.get('/temp/:indent_id', indentController.getTempItems.bind(indentController));

// Finalize
router.post('/finalize', indentController.finalizeIndent.bind(indentController));

// List & filters
router.get('/', indentController.listIndents.bind(indentController));
router.get('/pending', indentController.getPendingIndents.bind(indentController));

// Single indent detail
router.get('/:indent_id/detail', indentController.getIndentDetail.bind(indentController));

module.exports = router;
