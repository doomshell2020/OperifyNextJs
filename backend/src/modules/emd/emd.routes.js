const permission=require('../jobChallan/legacyPermission');
const express = require('express');
const router = express.Router();
const emdController = require('./emd.controller');
const authenticate = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');

router.use(authenticate);
router.use(tenantMiddleware);

router.get('/', permission('emd','index'), (req, res, next) => emdController.getEmdList(req, res, next));
router.get('/:id/details', permission('emd','viewamount'), (req, res, next) => emdController.getEmdDetail(req, res, next));

module.exports = router;
