const express = require('express');
const router = express.Router();
const indentpoController = require('./indentpo.controller');
const auth = require('../../middleware/auth');
const tenant = require('../../middleware/tenant');
const permission=require('../jobChallan/legacyPermission');
const { requireAnyPermission } = require('../../middleware/permission');
const { requireCurrentIndent } = require('../../middleware/legacyActionDate');
// Printing is available to readers of the Indent Manager and its details.
const printPermission = requireAnyPermission([
  'legacy:admin/indentpo/index',
  'legacy:admin/indentpo/viewindentpodetail',
  'legacy:admin/indentpo/viewindentpopdf',
]);

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
router.get('/export',permission('indentpo','indentpoexcel'),async(req,res,next)=>{try{await require('./indentpo.export')(req.dbPool,req.query,res);}catch(error){next(error);}});
router.get('/:indent_id/edit-data',permission('indentpo','edit'),requireCurrentIndent,async(req,res,next)=>{try{const data=await require('./indentpo.repository').getIndentpoDetail(req.dbPool,req.params.indent_id);if(!data)return res.status(404).json({message:'Indent not found'});res.json(data);}catch(error){next(error);}});
router.put('/:indent_id',permission('indentpo','edit'),requireCurrentIndent,async(req,res,next)=>{try{res.json(await require('./indentpo.mutations').update(req.dbPool,req.params.indent_id,req.body,req.user.id));}catch(error){next(error);}});
router.delete('/:indent_id',permission('indentpo','delete'),requireCurrentIndent,async(req,res,next)=>{try{await require('./indentpo.mutations').remove(req.dbPool,req.params.indent_id);res.json({success:true});}catch(error){next(error);}});
router.post('/', permission('indentpo','add'), indentpoController.saveIndentpo);
router.get('/', permission('indentpo','index'), indentpoController.listIndentpo);
router.get('/:indent_id/pdf', printPermission, indentpoController.downloadPdf);
router.get('/:indent_id/detail', permission('indentpo','viewindentpodetail'), indentpoController.getIndentpoDetail);
// Popup details and popup printing both identify the same database row.
router.get('/view-details/:id/pdf', printPermission, indentpoController.downloadPdfById);
// Opening a listed indent is part of reading the Indent Manager.
router.get('/view-details/:id', requireAnyPermission([
  'legacy:admin/indentpo/index',
  'legacy:admin/indentpo/viewindentpodetail',
]), indentpoController.getIndentPoDetails);

module.exports = router;
