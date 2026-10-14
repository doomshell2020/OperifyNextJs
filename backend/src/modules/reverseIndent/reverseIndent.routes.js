const permission = require('../jobChallan/legacyPermission');
const express = require('express');
const router = express.Router();
const reverseIndentController = require('./reverseIndent.controller');
const authMiddleware = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');
const { requireAnyPermission } = require('../../middleware/permission');
const lookupAccess = requireAnyPermission(['index','add','edit'].map(action=>`legacy:admin/reverseindent/${action}`));

router.use(authMiddleware);
router.use(tenantMiddleware);

router.get('/', permission('reverseindent','index'), reverseIndentController.getReverseIndents);
router.get('/next-id', permission('reverseindent','add'), reverseIndentController.getNextReverseId);
router.post('/', permission('reverseindent','add'), reverseIndentController.saveReverseIndent);
router.get('/export', permission('reverseindent','excel'), reverseIndentController.exportExcel);
router.get('/lookups/:kind(contracts|machines|products|semi-products)', lookupAccess, reverseIndentController.lookup);
router.get('/contracts/:contractId/products', lookupAccess, reverseIndentController.contractProducts);
router.get('/contracts/:contractId/products/:productId/design-sheet', lookupAccess, reverseIndentController.designSheet);
router.get('/:id/edit-data', permission('reverseindent','edit'), reverseIndentController.getReverseIndentDetails);
router.put('/:id', permission('reverseindent','edit'), reverseIndentController.updateReverseIndent);
router.get('/:id', permission('reverseindent','viewreverseindent'), reverseIndentController.getReverseIndentDetails);
router.get('/:id/pdf', permission('reverseindent','viewreverseindentpdf'), reverseIndentController.exportPDF);
router.delete('/:id', permission('reverseindent','delete'), reverseIndentController.deleteReverseIndent);

module.exports = router;
