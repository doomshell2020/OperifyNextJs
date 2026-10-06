const permission = require('../jobChallan/legacyPermission');
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
router.post('/temp', permission('indent','add'), indentController.addTempItem.bind(indentController));
router.delete('/temp/:id', permission('indent','add'), indentController.removeTempItem.bind(indentController));
router.get('/temp/:indent_id', indentController.getTempItems.bind(indentController));

// Finalize
router.post('/finalize', permission('indent','add'), indentController.finalizeIndent.bind(indentController));

// List & filters
router.get('/', permission('indent','index'), indentController.listIndents.bind(indentController));
router.get('/pending', permission('indent','pendingindent'), indentController.getPendingIndents.bind(indentController));

// Single indent detail
router.get('/:indent_id/detail', permission('indent','view'), indentController.getIndentDetail.bind(indentController));
router.get('/:indent_id/pdf', permission('indent','view'), (req,res,next)=>{req.pdfDownload=true;next();}, indentController.getIndentDetail.bind(indentController));

module.exports = router;
