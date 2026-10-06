const permission = require('../jobChallan/legacyPermission');
const express = require('express');
const router = express.Router();
const contractController = require('./contract.controller');
const authenticate = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');

// Mount routes after authentication and tenant pool binding
router.use(authenticate);
router.use(tenantMiddleware);

router.get('/', permission('contracts','index'), contractController.getContracts);
router.post('/', permission('contracts','add'), contractController.createContract);
router.get('/form-data', contractController.getFormData);
router.get('/:id/details', permission('contracts','viewcontractdetail'), contractController.getDetails);
router.get('/:id/reverse-cost', permission('contracts','viewreverse'), async (req,res,next) => {
  try {
    const id=Number(req.params.id);
    if(!Number.isSafeInteger(id) || id<=0) return res.status(400).json({success:false,message:'Invalid contract ID'});
    const contract=await require('./contract.repository').findById(req.dbPool,id);
    if(!contract) return res.status(404).json({success:false,message:'Contract not found'});
    res.json({success:true,data:{contract,...await require('./contract.reverse-cost')(req.dbPool,id)}});
  } catch(error) { next(error); }
});
router.get('/:id/pdf', contractController.exportPDF);
router.put('/:id', permission('contracts','edit'), contractController.updateContract);
router.delete('/:id', permission('contracts','delete'), contractController.deleteContract);

module.exports = router;
