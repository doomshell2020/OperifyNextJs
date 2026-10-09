const express = require('express');
const router = express.Router();
const multer = require('multer');
const path = require('path');
const fs = require('fs');
const designsheetController = require('./designsheet.controller');
const authenticate = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');
const permission = require('../jobChallan/legacyPermission');
const { requireAnyPermission } = require('../../middleware/permission');
const sheetFiles = require('./designsheet.files');
const viewPermission = requireAnyPermission(['legacy:admin/designsheet/index', 'legacy:admin/designsheet/viewdesignsheet']);
const filePermission = requireAnyPermission(['legacy:admin/designsheet/index', 'legacy:admin/designsheet/viewdesignsheet', 'legacy:admin/designsheet/edit']);

// Enforce auth and multi-tenancy contexts
router.use(authenticate);
router.use(tenantMiddleware);

// Setup Multer for file uploads
const storage = multer.diskStorage({
  destination: function (req, file, cb) {
    const dir = sheetFiles.uploadDir(req);
    // Ensure directory exists
    if (!fs.existsSync(dir)){
        fs.mkdirSync(dir, { recursive: true });
    }
    cb(null, dir);
  },
  filename: function (req, file, cb) {
    // Keep a readable filename while preventing simultaneous-upload collisions.
    const ext = path.extname(file.originalname);
    const crypto = require('crypto');
    const stem = path.basename(file.originalname, ext).replace(/[^a-zA-Z0-9._ -]/g, '_').slice(0, 100) || 'design-sheet';
    const newName = `${crypto.randomUUID()}-${stem}${ext}`;
    cb(null, newName);
  }
});

const upload = multer({
  storage,
  fileFilter: function (req, file, cb) {
    const allowed = ['.pdf', '.jpg', '.jpeg', '.png'];
    const ext = path.extname(file.originalname).toLowerCase();
    if (!allowed.includes(ext)) {
      return cb(new Error('Upload PDF, JPG, JPEG or PNG files only'));
    }
    cb(null, true);
  }
});

router.get('/', permission('designsheet','index'), designsheetController.index);
router.get('/check-item', designsheetController.checkDesignSheetItem);
router.get('/bom-products', designsheetController.getBomFinishedProduct);
// API Routes for Add Workflow
router.get('/search-contracts', tenantMiddleware, designsheetController.searchContracts);
router.get('/bom-products/:contractId', tenantMiddleware, designsheetController.getBomFinishedProducts);
router.get('/check-item', tenantMiddleware, designsheetController.checkDesignSheetItem);

router.get('/search-items', tenantMiddleware, designsheetController.searchItems);
router.get('/indent-items', tenantMiddleware, designsheetController.indentItems);
router.get('/item-category', tenantMiddleware, designsheetController.getItemCatg);
router.get('/view/:designsheetno', viewPermission, designsheetController.viewDesignSheet);
router.get('/view/:designsheetno/pdf', viewPermission, (req, res, next) => { req.pdfDownload = true; next(); }, designsheetController.viewDesignSheet);
router.get('/records/:id', viewPermission, designsheetController.viewDesignSheet);
router.get('/records/:id/pdf', viewPermission, (req,res,next)=>{req.pdfDownload=true;next();}, designsheetController.viewDesignSheet);
router.get('/:id/files/:field', filePermission, sheetFiles.download);
router.get('/contract-details/:contractId', tenantMiddleware, designsheetController.getContractDetails);
router.get('/:id', permission('designsheet','edit'), designsheetController.getById);


// Handle multiple fields for revisions
const uploadFields = upload.fields([
  { name: 'design_sheet', maxCount: 1 },
  { name: 'r1', maxCount: 1 },
  { name: 'r2', maxCount: 1 },
  { name: 'r3', maxCount: 1 },
  { name: 'r4', maxCount: 1 },
  { name: 'r5', maxCount: 1 },
]);

router.post('/', permission('designsheet','add'), uploadFields, designsheetController.create);
router.put('/:id', permission('designsheet','edit'), uploadFields, designsheetController.update);
router.delete('/details/:id', permission('designsheet','deletedata'), designsheetController.deleteDetailData);
router.delete('/:id', permission('designsheet','delete'), designsheetController.deleteSheet);

module.exports = router;
