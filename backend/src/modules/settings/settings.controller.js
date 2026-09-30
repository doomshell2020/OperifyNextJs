const repo = require('./settings.repository');
const fs = require('fs');
const path = require('path');
const { centralSequelize } = require('../../config/sequelize');
const bcrypt = require('bcryptjs');

class SettingsController {
  // ─── LOGO SETTINGS ─────────────────────────────────────────
  async uploadLogo(req, res, next) {
    try {
      if (!req.file) {
        return res.status(400).json({ success: false, message: 'No file uploaded' });
      }
      
      const dbName = req.user?.db || 'default';
      const logoUrl = `/public/uploads/logos/${req.file.filename}`;
      
      res.json({ success: true, message: 'Logo uploaded successfully', logoUrl });
    } catch (e) {
      next(e);
    }
  }

  async getLogo(req, res, next) {
    try {
      const dbName = req.user?.db || 'default';
      const dirPath = path.join(__dirname, '../../../public/uploads/logos');
      
      if (fs.existsSync(dirPath)) {
        const files = fs.readdirSync(dirPath);
        const logoFile = files.find(f => f.startsWith(`${dbName}_logo.`));
        if (logoFile) {
          return res.json({ success: true, logoUrl: `/public/uploads/logos/${logoFile}?t=${Date.now()}` });
        }
      }
      
      // Fallback to default
      res.json({ success: true, logoUrl: 'https://staging.operify.in/image/logo.png' });
    } catch (e) {
      next(e);
    }
  }

  async getProfile(req, res, next) {
    let connection;
    try {
      connection = await req.dbPool.getConnection();
      
      const [settings] = await connection.query(`
        SELECT 
          s.id,
          s.first_name,
          s.last_name,
          s.mobile,
          s.contact_email,
          sd.address1,
          sd.address2,
          sd.phone,
          sd.fax,
          sd.website,
          sd.status,
          sd.company_name,
          sd.pan_number,
          sd.gst_no,
          sd.tin_date,
          sd.account_number,
          sd.ifsc,
          sd.address,
          sd.alias,
          sd.affiliation_no
        FROM sitesettings s
        LEFT JOIN sitesettings_details sd ON s.id = sd.sitesettings_id
        WHERE s.id = 1
      `);
      
      if (!settings || settings.length === 0) {
        return res.json({ success: true, profile: null });
      }
      
      return res.json({ success: true, profile: settings[0] });
    } catch (error) {
      console.error('Error fetching profile:', error);
      next(error);
    } finally {
      if (connection) connection.release();
    }
  }

  async updateProfile(req, res, next) {
    let connection;
    try {
      connection = await req.dbPool.getConnection();
      
      const {
        first_name, last_name, mobile, contact_email,
        address1, address2, phone, fax, website, status,
        company_name, pan_number, gst_no, tin_date, account_number, ifsc, address,
        company_number, alias
      } = req.body;

      await connection.beginTransaction();

      // Update sitesettings
      await connection.query(`
        UPDATE sitesettings 
        SET first_name = ?, last_name = ?, mobile = ?, contact_email = ?
        WHERE id = 1
      `, [first_name || '', last_name || '', mobile || '', contact_email || '']);

      // Update sitesettings_details
      // Map company_number from frontend to affiliation_no in DB as per user requirement (no new columns)
      await connection.query(`
        UPDATE sitesettings_details 
        SET address1 = ?, address2 = ?, phone = ?, fax = ?, website = ?, status = ?,
            company_name = ?, pan_number = ?, gst_no = ?, tin_date = ?, account_number = ?, 
            ifsc = ?, address = ?, alias = ?, affiliation_no = ?
        WHERE sitesettings_id = 1
      `, [
        address1 || '', address2 || '', phone || '', fax || '', website || '', status || 'Y',
        company_name || '', pan_number || '', gst_no || '', tin_date || null, account_number || '', 
        ifsc || '', address || '', alias || '', company_number || ''
      ]);

      await connection.commit();
      
      return res.json({ success: true, message: 'Profile updated successfully' });
    } catch (error) {
      if (connection) await connection.rollback();
      console.error('Error updating profile:', error);
      next(error);
    } finally {
      if (connection) connection.release();
    }
  }

  // ─── CATEGORIES ─────────────────────────────────────────
  async listCategories(req, res, next) {
    try {
      const data = await repo.getCategories(req.dbPool, { search: req.query.search });
      res.json({ success: true, data });
    } catch (e) { next(e); }
  }
  async getCategory(req, res, next) {
    try {
      const data = await repo.getCategoryById(req.dbPool, req.params.id);
      if (!data) return res.status(404).json({ success: false, message: 'Not found' });
      res.json({ success: true, data });
    } catch (e) { next(e); }
  }
  async createCategory(req, res, next) {
    try {
      const { category_name, description } = req.body;
      if (!category_name?.trim()) return res.status(400).json({ success: false, message: 'Category name is required' });
      const id = await repo.createCategory(req.dbPool, { category_name: category_name.trim(), description });
      res.json({ success: true, id, message: 'Category created successfully' });
    } catch (e) { next(e); }
  }
  async updateCategory(req, res, next) {
    try {
      const { category_name, description } = req.body;
      if (!category_name?.trim()) return res.status(400).json({ success: false, message: 'Category name is required' });
      await repo.updateCategory(req.dbPool, req.params.id, { category_name: category_name.trim(), description });
      res.json({ success: true, message: 'Category updated successfully' });
    } catch (e) { next(e); }
  }
  async toggleCategoryStatus(req, res, next) {
    try {
      const { status } = req.body;
      await repo.toggleCategoryStatus(req.dbPool, req.params.id, status);
      res.json({ success: true, message: 'Status updated' });
    } catch (e) { next(e); }
  }
  async deleteCategory(req, res, next) {
    try {
      await repo.deleteCategory(req.dbPool, req.params.id);
      res.json({ success: true, message: 'Category deleted' });
    } catch (e) { next(e); }
  }

  // ─── PRODUCTS ───────────────────────────────────────────
  async listProducts(req, res, next) {
    try {
      const { search, category_id, status, itemtype } = req.query;
      const data = await repo.getProducts(req.dbPool, {
        search, status,
        category_id: category_id ? parseInt(category_id) : undefined,
        itemtype,
      });
      res.json({ success: true, data });
    } catch (e) { next(e); }
  }
  async getProduct(req, res, next) {
    try {
      const data = await repo.getProductById(req.dbPool, req.params.id);
      if (!data) return res.status(404).json({ success: false, message: 'Not found' });
      res.json({ success: true, data });
    } catch (e) { next(e); }
  }
  async createProduct(req, res, next) {
    try {
      if (!req.body.item_name) {
        return res.status(400).json({ success: false, message: 'Item name is required' });
      }
      
      const exists = await repo.checkItemExists(req.dbPool, req.body.item_name);
      if (exists) {
        return res.status(400).json({ success: false, message: 'Your entered Item already exists.' });
      }

      let sale_price = parseFloat(req.body.sale_price || 0);
      let discount = parseFloat(req.body.discount || 0);
      let final_sale_price = sale_price - discount;

      const productprocess_id = Array.isArray(req.body.productprocess_id) 
        ? req.body.productprocess_id.join(',') 
        : (req.body.productprocess_id || '');

      const payload = {
        ...req.body,
        sale_price: final_sale_price,
        productprocess_id
      };
      
      await repo.createProduct(req.dbPool, payload);
      res.json({ success: true, message: 'Item added successfully.' });
    } catch (e) {
      next(e);
    }
  }
  async updateProduct(req, res, next) {
    try {
      if (!req.body.item_name) {
        return res.status(400).json({ success: false, message: 'Item name is required' });
      }
      
      const id = req.params.id;
      const exists = await repo.checkItemExistsForEdit(req.dbPool, req.body.item_name, id);
      if (exists) {
        return res.status(400).json({ success: false, message: 'Your entered Item already exists.' });
      }

      const payload = {
        item_name: req.body.item_name,
        category_id: req.body.category_id,
        uom: req.body.uom
      };
      
      await repo.updateProduct(req.dbPool, id, payload);
      res.json({ success: true, message: 'Item name successfully updated.' });
    } catch (e) {
      next(e);
    }
  }
  async getFinishedProcessList(req, res, next) {
    try {
      const data = await repo.getFinishedProcessList(req.dbPool);
      res.json({ success: true, data });
    } catch (e) { next(e); }
  }
  async toggleProductStatus(req, res, next) {
    try {
      const { status } = req.body;
      await repo.toggleProductStatus(req.dbPool, req.params.id, status);
      res.json({ success: true, message: 'Status updated' });
    } catch (e) { next(e); }
  }
  async getCategoryList(req, res, next) {
    try {
      const data = await repo.getCategoryList(req.dbPool);
      res.json({ success: true, data });
    } catch (e) { next(e); }
  }
  async getUomList(req, res, next) {
    try {
      const data = await repo.getUomList(req.dbPool);
      res.json({ success: true, data });
    } catch (e) { next(e); }
  }

  // ─── TAXES ──────────────────────────────────────────────
  async listTaxes(req, res, next) {
    try {
      const data = await repo.getTaxes(req.dbPool);
      res.json({ success: true, data });
    } catch (e) { next(e); }
  }

  // ─── SUPPLIERS ──────────────────────────────────────────
  async listSuppliers(req, res, next) {
    try {
      const { search, status, type } = req.query;
      const data = await repo.getSuppliers(req.dbPool, { search, status, type });
      res.json({ success: true, data });
    } catch (e) { next(e); }
  }
  async getSupplier(req, res, next) {
    try {
      const data = await repo.getSupplierById(req.dbPool, req.params.id);
      if (!data) return res.status(404).json({ success: false, message: 'Not found' });
      res.json({ success: true, data });
    } catch (e) { next(e); }
  }
  async createSupplier(req, res, next) {
    try {
      const { name } = req.body;
      if (!name?.trim()) return res.status(400).json({ success: false, message: 'Supplier name is required' });
      const id = await repo.createSupplier(req.dbPool, req.body);
      res.json({ success: true, id, message: 'Supplier created successfully' });
    } catch (e) { next(e); }
  }
  async updateSupplier(req, res, next) {
    try {
      const { name } = req.body;
      if (!name?.trim()) return res.status(400).json({ success: false, message: 'Supplier name is required' });
      await repo.updateSupplier(req.dbPool, req.params.id, req.body);
      res.json({ success: true, message: 'Supplier updated successfully' });
    } catch (e) { next(e); }
  }
  async toggleSupplierStatus(req, res, next) {
    try {
      const { status } = req.body;
      await repo.toggleSupplierStatus(req.dbPool, req.params.id, status);
      res.json({ success: true, message: 'Status updated' });
    } catch (e) { next(e); }
  }

  // ─── USERS ──────────────────────────────────────────────
  async listUsers(req, res, next) {
    try {
      const { search, is_status } = req.query;
      const data = await repo.getUsers(req.dbPool, { search, is_status });
      res.json({ success: true, data });
    } catch (e) { next(e); }
  }
  async getUser(req, res, next) {
    try {
      const data = await repo.getUserById(req.dbPool, req.params.id);
      if (!data) return res.status(404).json({ success: false, message: 'Not found' });
      res.json({ success: true, data });
    } catch (e) { next(e); }
  }
  async toggleUserStatus(req, res, next) {
    try {
      const { status } = req.body;
      const id = req.params.id;
      
      const oldUser = await repo.getUserById(req.dbPool, id);
      await repo.toggleUserStatus(req.dbPool, id, status);
      
      if (oldUser && oldUser.mobile) {
        try {
          await repo.toggleUserStatusCentral(centralSequelize, oldUser.mobile, status);
        } catch (centralErr) {
          console.error('Error syncing status toggle to central DB:', centralErr);
        }
      }
      
      res.json({ success: true, message: 'Status updated' });
    } catch (e) { next(e); }
  }
  
  async createUser(req, res, next) {
    try {
      const { user_name, email, mobile, password, role_id } = req.body;
      if (!user_name || !email || !password || !role_id) {
        return res.status(400).json({ success: false, message: 'Required fields are missing' });
      }

      // Check mobile exists in tenant
      if (mobile) {
        const existing = await repo.getUserByMobile(req.dbPool, mobile);
        if (existing) {
          return res.status(400).json({ success: false, message: 'The mobile number already exists. Please use a different number.' });
        }
      }

      const hashedPassword = await bcrypt.hash(password, 10);
      const is_admin = role_id >= 100 ? 'Y' : 'N';
      const c_id = req.user?.c_id || 1;
      const academic_year = req.user?.academic_year || '2023-2024';
      const dbName = req.user?.db || 'default';
      const board = req.user?.board || '1';
      
      const payload = {
        user_name, email, mobile, password: hashedPassword, role_id, 
        c_id, academic_year, db: dbName, board, is_admin, confirm_pass: password
      };

      const newUserId = await repo.createUser(req.dbPool, payload);

      // Sync to central DB as required by old CakePHP logic
      try {
        await repo.createUserCentral(centralSequelize, payload);
      } catch (centralErr) {
        console.error('Error syncing user to central DB:', centralErr);
      }
      
      res.json({ success: true, message: 'User has been saved.', id: newUserId });
    } catch (e) { next(e); }
  }

  async updateUser(req, res, next) {
    try {
      const { user_name, email, mobile, password, role_id } = req.body;
      const id = req.params.id;

      if (!user_name || !email || !role_id) {
        return res.status(400).json({ success: false, message: 'Required fields are missing' });
      }

      // We need old user to know the old mobile if we want to sync properly, 
      // but if mobile is not updated, we use the current mobile.
      const oldUser = await repo.getUserById(req.dbPool, id);
      if (!oldUser) {
        return res.status(404).json({ success: false, message: 'User not found' });
      }

      // Check mobile exists
      if (mobile) {
        const existing = await repo.getUserByMobile(req.dbPool, mobile);
        if (existing && existing.id != id) {
          return res.status(400).json({ success: false, message: 'The mobile number already exists. Please use a different number.' });
        }
      }

      const payload = { user_name, email, mobile, role_id };
      
      if (password) {
        payload.password = await bcrypt.hash(password, 10);
        payload.confirm_pass = password;
      }

      await repo.updateUser(req.dbPool, id, payload);

      // Sync to central DB
      try {
        await repo.updateUserCentral(centralSequelize, oldUser.mobile, payload);
      } catch (centralErr) {
        console.error('Error syncing updated user to central DB:', centralErr);
      }

      res.json({ success: true, message: 'User has been updated.' });
    } catch (e) { next(e); }
  }

  async deleteUser(req, res, next) {
    try {
      const id = req.params.id;
      const oldUser = await repo.getUserById(req.dbPool, id);
      
      await repo.deleteUser(req.dbPool, id);

      if (oldUser && oldUser.mobile) {
        try {
          await repo.deleteUserCentral(centralSequelize, oldUser.mobile);
        } catch (centralErr) {
          console.error('Error deleting user from central DB:', centralErr);
        }
      }

      res.json({ success: true, message: 'User deleted successfully' });
    } catch (e) { next(e); }
  }

  // ─── ROLES ──────────────────────────────────────────────
  async listRoles(req, res, next) {
    try {
      const data = await repo.getRoles(req.dbPool);
      res.json({ success: true, data });
    } catch (e) { next(e); }
  }
}

module.exports = new SettingsController();
