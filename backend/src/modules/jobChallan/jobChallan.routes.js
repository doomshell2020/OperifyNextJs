const express = require('express');
const jobChallanController = require('./jobChallan.controller');
const authenticate = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');

const router = express.Router();
const permission = require('./legacyPermission');
const { requireAnyPermission } = require('../../middleware/permission');
const formAccess = requireAnyPermission(['legacy:admin/jobchallan/add','legacy:admin/jobchallan/itemreceived']);

router.use(authenticate);
router.use(tenantMiddleware);

// Lookup / helper endpoints
router.get('/vendors', requireAnyPermission(['legacy:admin/jobchallan/add','legacy:admin/jobchallan/itemreceived','legacy:admin/jobchallan/index','legacy:admin/jobchallan/receiveindex','legacy:admin/jobchallan/receiveadd','legacy:admin/gatepasses/index','legacy:admin/gatepasses/add','legacy:admin/gatepasses/edit']), jobChallanController.listVendors.bind(jobChallanController));
router.post('/vendors', permission('jobchallan','ajaxaddsubcontractor'), jobChallanController.addVendor.bind(jobChallanController));
router.get('/tax-master', (req,res,next)=>permission('jobchallan',req.query.context==='receive'?'itemreceived':'add')(req,res,next), jobChallanController.listTaxMaster.bind(jobChallanController));
router.get('/search-items', formAccess, jobChallanController.searchItems.bind(jobChallanController));
router.get('/item-stock', formAccess, jobChallanController.getItemStock.bind(jobChallanController));
router.get('/vendor-gst', formAccess, jobChallanController.getVendorGst.bind(jobChallanController));
router.get('/permissions', async (req, res, next) => {
  try {
    const { select } = require('./legacyStore');
    const { centralSequelize } = require('../../config/sequelize');
    const rows = await select(centralSequelize, "SELECT LOWER(TRIM(url)) AS url FROM permission_label WHERE LOWER(url) LIKE 'admin/jobchallan/%' OR LOWER(url) LIKE 'admin/gatepasses/%'");
    res.json({ success: true, data: { configured: rows.map(row => `legacy:${row.url}`) } });
  } catch (error) { next(error); }
});

// Core CRUD
router.get('/', permission('jobchallan','index'), jobChallanController.list.bind(jobChallanController));
router.post('/', permission('jobchallan','add'), jobChallanController.create.bind(jobChallanController));
router.post('/:id/receive', permission('jobchallan','itemreceived'), async (req,res,next) => {
  try { res.json({ success:true, data:await require('../jobReceive/jobReceive.service').create(req.dbName,req.body,req.params.id) }); }
  catch(error) { next(error); }
});
router.get('/:id/pdf', permission('jobchallan','viewpdf'), jobChallanController.downloadPdf.bind(jobChallanController));
router.get('/:id', permission('jobchallan','view'), jobChallanController.getDetail.bind(jobChallanController));
router.delete('/:id', permission('jobchallan','delete'), jobChallanController.remove.bind(jobChallanController));

module.exports = router;
