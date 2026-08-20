const express = require('express');
const router = express.Router();
const reverseIndentController = require('./reverseIndent.controller');
const authMiddleware = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');

router.use(authMiddleware);
router.use(tenantMiddleware);

router.get('/', reverseIndentController.getReverseIndents);
router.get('/next-id', reverseIndentController.getNextReverseId);
router.post('/', reverseIndentController.saveReverseIndent);
router.get('/:id', reverseIndentController.getReverseIndentDetails);
router.get('/:id/pdf', reverseIndentController.exportPDF);
router.delete('/:id', reverseIndentController.deleteReverseIndent);

module.exports = router;
