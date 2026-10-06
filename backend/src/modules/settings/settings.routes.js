const permission = require('../jobChallan/legacyPermission');
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

// Profile settings
router.get('/profile', (req, res, next) => ctrl.getProfile(req, res, next));
router.put('/profile', (req, res, next) => ctrl.updateProfile(req, res, next));

// Categories
router.get('/categories', permission('itemcategory','index'), (req, res, next) => ctrl.listCategories(req, res, next));
router.get('/categories/:id', permission('itemcategory','edit'), (req, res, next) => ctrl.getCategory(req, res, next));
router.post('/categories', permission('itemcategory','add'), (req, res, next) => ctrl.createCategory(req, res, next));
router.put('/categories/:id', permission('itemcategory','edit'), (req, res, next) => ctrl.updateCategory(req, res, next));
router.patch('/categories/:id/status', permission('itemcategory','status'), (req, res, next) => ctrl.toggleCategoryStatus(req, res, next));
router.patch('/categories/:id/print-status', permission('itemcategory','printstatus'), async(req,res,next)=>{
  try {
    if(!['Y','N'].includes(req.body.is_print))return res.status(400).json({success:false,message:'Invalid print status'});
    if(!await require('./settings.repository').getCategoryById(req.dbPool,req.params.id))return res.status(404).json({success:false,message:'Category not found'});
    const {QueryTypes}=require('sequelize');
    await req.dbPool.query('UPDATE st_categorymaster SET is_print=:is_print WHERE id=:id',{replacements:{is_print:req.body.is_print,id:req.params.id},type:QueryTypes.UPDATE});
    res.json({success:true});
  }catch(error){next(error);}
});
router.delete('/categories/:id', permission('itemcategory','delete'), (req, res, next) => ctrl.deleteCategory(req, res, next));

// Products
router.get('/products/export', permission('additem','viewitemexcel'), async(req,res,next)=>{
  try{await require('./products.export')(req.dbPool,req.query,res);}catch(error){next(error);}
});
router.get('/products/list', permission('additem','index'), async(req,res,next)=>{try{res.json({success:true,...await require('./products.list')(req.dbPool,req.query)});}catch(error){next(error);}});
router.get('/products/form-data', async(req,res,next)=>{
  try {
    const {QueryTypes}=require('sequelize');
    const select=sql=>req.dbPool.query(sql,{type:QueryTypes.SELECT});
    const [sizes,locations,companies,taxes]=await Promise.all([
      select("SELECT id,size_name,status FROM st_sizemanager ORDER BY id"),
      select("SELECT id,location_name,parent,status FROM st_itemlocation ORDER BY location_name"),
      select("SELECT id,cname,status FROM st_companymaster WHERE status='Y' ORDER BY id"),
      select("SELECT id,tax,tax_name FROM st_taxmaster WHERE status='Y' ORDER BY id")
    ]);
    res.json({success:true,data:{sizes,locations,companies,taxes}});
  } catch(error){next(error);}
});
router.get('/products', (req, res, next) => ctrl.listProducts(req, res, next));
router.post('/products', permission('additem','add'), (req, res, next) => ctrl.createProduct(req, res, next));
router.get('/products/categories', (req, res, next) => ctrl.getCategoryList(req, res, next));
router.get('/products/uom', (req, res, next) => ctrl.getUomList(req, res, next));
router.get('/products/finished-processes', (req, res, next) => ctrl.getFinishedProcessList(req, res, next));
router.get('/products/:id', permission('additem','edit'), (req, res, next) => ctrl.getProduct(req, res, next));
router.put('/products/:id', permission('additem','edit'), (req, res, next) => ctrl.updateProduct(req, res, next));
router.patch('/products/:id/status', permission('additem','status'), (req, res, next) => ctrl.toggleProductStatus(req, res, next));

router.delete('/products/:id', permission('additem','delete'), (req,res,next)=>ctrl.deleteProduct(req,res,next));

// Taxes
router.get('/taxes', (req, res, next) => ctrl.listTaxes(req, res, next));

// Suppliers
router.get('/suppliers', (req, res, next) => ctrl.listSuppliers(req, res, next));
router.get('/suppliers/:id', (req, res, next) => ctrl.getSupplier(req, res, next));
router.post('/suppliers', (req, res, next) => ctrl.createSupplier(req, res, next));
router.put('/suppliers/:id', (req, res, next) => ctrl.updateSupplier(req, res, next));
router.patch('/suppliers/:id/status', (req, res, next) => ctrl.toggleSupplierStatus(req, res, next));

// Roles
router.get('/roles', (req, res, next) => ctrl.listRoles(req, res, next));

// Users
router.get('/users', (req, res, next) => ctrl.listUsers(req, res, next));
router.get('/users/:id', (req, res, next) => ctrl.getUser(req, res, next));
router.post('/users', (req, res, next) => ctrl.createUser(req, res, next));
router.put('/users/:id', (req, res, next) => ctrl.updateUser(req, res, next));
router.delete('/users/:id', (req, res, next) => ctrl.deleteUser(req, res, next));
router.patch('/users/:id/status', (req, res, next) => ctrl.toggleUserStatus(req, res, next));

module.exports = router;
