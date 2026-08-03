const express = require('express');
const router = express.Router();
const ctrl = require('./settings.controller');
const authenticate = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');

router.use(authenticate);
router.use(tenantMiddleware);
const multer = require('multer');
const path = require('path');

const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    cb(null, path.join(__dirname, '../../../public/uploads/logos'));
  },
  filename: (req, file, cb) => {
    const ext = path.extname(file.originalname);
    const dbName = req.user?.db || 'default';
    
    // Delete any existing logos for this DB before saving
    const dirPath = path.join(__dirname, '../../../public/uploads/logos');
    const fs = require('fs');
    if (fs.existsSync(dirPath)) {
      const files = fs.readdirSync(dirPath);
      files.forEach(f => {
        if (f.startsWith(`${dbName}_logo.`)) {
          try { fs.unlinkSync(path.join(dirPath, f)); } catch(e){}
        }
      });
    }

    cb(null, `${dbName}_logo${ext}`);
  }
});
const upload = multer({ storage });

// Logo settings
router.post('/upload-logo', upload.single('logo'), (req, res, next) => ctrl.uploadLogo(req, res, next));
router.get('/logo', (req, res, next) => ctrl.getLogo(req, res, next));

// Categories
router.get('/categories', (req, res, next) => ctrl.listCategories(req, res, next));
router.get('/categories/:id', (req, res, next) => ctrl.getCategory(req, res, next));
router.post('/categories', (req, res, next) => ctrl.createCategory(req, res, next));
router.put('/categories/:id', (req, res, next) => ctrl.updateCategory(req, res, next));
router.patch('/categories/:id/status', (req, res, next) => ctrl.toggleCategoryStatus(req, res, next));
router.delete('/categories/:id', (req, res, next) => ctrl.deleteCategory(req, res, next));

// Products
router.get('/products', (req, res, next) => ctrl.listProducts(req, res, next));
router.get('/products/categories', (req, res, next) => ctrl.getCategoryList(req, res, next));
router.get('/products/uom', (req, res, next) => ctrl.getUomList(req, res, next));
router.get('/products/:id', (req, res, next) => ctrl.getProduct(req, res, next));
router.patch('/products/:id/status', (req, res, next) => ctrl.toggleProductStatus(req, res, next));

// Suppliers
router.get('/suppliers', (req, res, next) => ctrl.listSuppliers(req, res, next));
router.get('/suppliers/:id', (req, res, next) => ctrl.getSupplier(req, res, next));
router.post('/suppliers', (req, res, next) => ctrl.createSupplier(req, res, next));
router.put('/suppliers/:id', (req, res, next) => ctrl.updateSupplier(req, res, next));
router.patch('/suppliers/:id/status', (req, res, next) => ctrl.toggleSupplierStatus(req, res, next));

// Users
router.get('/users', (req, res, next) => ctrl.listUsers(req, res, next));
router.get('/users/:id', (req, res, next) => ctrl.getUser(req, res, next));
router.patch('/users/:id/status', (req, res, next) => ctrl.toggleUserStatus(req, res, next));

module.exports = router;
