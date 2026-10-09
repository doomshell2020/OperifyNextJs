const permission = require('../jobChallan/legacyPermission');
const express = require('express');
const router = express.Router();
const reverseIndentController = require('./reverseIndent.controller');
const authMiddleware = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');

router.use(authMiddleware);
router.use(tenantMiddleware);

router.get('/', permission('reverseindent','index'), reverseIndentController.getReverseIndents);
router.get('/next-id', permission('reverseindent','add'), reverseIndentController.getNextReverseId);
router.post('/', permission('reverseindent','add'), reverseIndentController.saveReverseIndent);
router.get('/:id', permission('reverseindent','viewreverseindent'), reverseIndentController.getReverseIndentDetails);
router.get('/:id/pdf', permission('reverseindent','viewreverseindentpdf'), reverseIndentController.exportPDF);
router.delete('/:id', permission('reverseindent','delete'), reverseIndentController.deleteReverseIndent);

module.exports = router;
