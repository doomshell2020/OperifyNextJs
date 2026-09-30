const fs = require('fs');
const path = require('path');

const moduleDir = path.join(__dirname, 'src', 'modules', 'jobChallan');
if (!fs.existsSync(moduleDir)) {
  fs.mkdirSync(moduleDir, { recursive: true });
}

// routes
fs.writeFileSync(path.join(moduleDir, 'jobChallan.routes.js'), `const express = require('express');
const jobChallanController = require('./jobChallan.controller');
const authenticate = require('../../middleware/auth');
const tenantMiddleware = require('../../middleware/tenant');

const router = express.Router();

router.use(authenticate);
router.use(tenantMiddleware);

router.get('/', jobChallanController.list.bind(jobChallanController));
router.post('/', jobChallanController.create.bind(jobChallanController));
router.get('/:id', jobChallanController.getDetail.bind(jobChallanController));
router.delete('/:id', jobChallanController.remove.bind(jobChallanController));

module.exports = router;
`);

// controller
fs.writeFileSync(path.join(moduleDir, 'jobChallan.controller.js'), `const JobChallanService = require('./jobChallan.service');

class JobChallanController {
  async list(req, res, next) {
    try {
      const data = await JobChallanService.list(req.tenantDb, req.query);
      res.json({ success: true, data });
    } catch (err) {
      next(err);
    }
  }

  async create(req, res, next) {
    try {
      const data = await JobChallanService.create(req.tenantDb, req.body, req.user);
      res.json({ success: true, data, message: 'Job Challan created successfully' });
    } catch (err) {
      next(err);
    }
  }

  async getDetail(req, res, next) {
    try {
      const data = await JobChallanService.getDetail(req.tenantDb, req.params.id);
      res.json({ success: true, data });
    } catch (err) {
      next(err);
    }
  }

  async remove(req, res, next) {
    try {
      await JobChallanService.remove(req.tenantDb, req.params.id);
      res.json({ success: true, message: 'Job Challan deleted successfully' });
    } catch (err) {
      next(err);
    }
  }
}

module.exports = new JobChallanController();
`);

// service
fs.writeFileSync(path.join(moduleDir, 'jobChallan.service.js'), `const { getTenantModels, getTenantSequelize } = require('../../config/sequelize');
const { Op } = require('sequelize');

class JobChallanService {
  async list(dbName, query) {
    const models = await getTenantModels(dbName);
    const { fromDate, toDate, vendorId, status, challanNo, page = 1, limit = 10 } = query;
    const offset = (page - 1) * limit;

    let where = {};
    if (challanNo) where.challan_no = { [Op.like]: \`%\${challanNo}%\` };
    if (status) where.status = status;
    if (vendorId) where.sub_contractors_id = vendorId;
    if (fromDate && toDate) where.jc_date = { [Op.between]: [fromDate, toDate] };

    const { count, rows } = await models.job_challans.findAndCountAll({
      where,
      include: [{ model: models.vendors, as: 'vendor', attributes: ['id', 'name'] }],
      order: [['jc_date', 'DESC'], ['id', 'DESC']],
      limit: parseInt(limit),
      offset: parseInt(offset)
    });

    return { total: count, items: rows, page: parseInt(page), limit: parseInt(limit) };
  }

  async create(dbName, payload, user) {
    const models = await getTenantModels(dbName);
    const sequelize = await getTenantSequelize(dbName);

    return await sequelize.transaction(async (t) => {
      // Validate unique challan_no
      const existing = await models.job_challans.findOne({ where: { challan_no: payload.challan_no }, transaction: t });
      if (existing) {
        throw new Error('Challan number already exists.');
      }

      // Check stock
      for (const item of payload.items) {
        if (item.quantity <= 0) throw new Error('Quantity must be greater than 0.');
        
        // Stock calculation
        const inward = await models.st_stock_register.sum('inward_qty', {
          where: { item_id: item.item_id, store_type: { [Op.in]: ['0','1','3'] } },
          transaction: t
        }) || 0;
        
        const outward = await models.st_stock_register.sum('outward_qty', {
          where: { item_id: item.item_id, store_type: { [Op.in]: ['2','4'] } },
          transaction: t
        }) || 0;

        const available = inward - outward;
        if (available < item.quantity) {
          throw new Error(\`Insufficient stock for item ID \${item.item_id}. Available: \${available}\`);
        }
      }

      const challan = await models.job_challans.create({
        challan_no: payload.challan_no,
        jc_date: payload.jc_date,
        sub_contractors_id: payload.sub_contractors_id,
        processing_type: payload.processing_type,
        vehicle_no: payload.vehicle_no,
        total_amount: payload.total_amount,
        gst_amount: payload.gst_amount,
        final_amount: payload.final_amount,
        added_by: user.id
      }, { transaction: t });

      for (const item of payload.items) {
        await models.job_challan_items.create({
          challan_id: challan.id,
          item_id: item.item_id,
          quantity: item.quantity,
          uom_id: item.uom_id,
          rate: item.rate,
          tax_rate: item.tax_rate,
          tax_amount: item.tax_amount,
          amount: item.amount,
          total: item.total
        }, { transaction: t });

        // Insert to stock register
        await models.st_stock_register.create({
          store_type: '2', // Dispatch
          item_id: item.item_id,
          outward_qty: item.quantity,
          date: payload.jc_date,
          user_id: user.id,
          added_time: new Date(),
          voucherno: challan.id, // reference
          remark: 'Job Challan Dispatch'
        }, { transaction: t });
      }
      return challan;
    });
  }

  async getDetail(dbName, id) {
    const models = await getTenantModels(dbName);
    const challan = await models.job_challans.findByPk(id, {
      include: [
        { model: models.vendors, as: 'vendor' },
        { 
          model: models.job_challan_items, 
          as: 'job_challan_items',
          include: [{ model: models.st_additem, as: 'item' }]
        }
      ]
    });
    if (!challan) throw new Error('Job Challan not found');
    return challan;
  }

  async remove(dbName, id) {
    const models = await getTenantModels(dbName);
    const sequelize = await getTenantSequelize(dbName);

    return await sequelize.transaction(async (t) => {
      const receives = await models.job_challan_receives.count({ where: { challan_id: id }, transaction: t });
      if (receives > 0) {
        throw new Error('Cannot delete Job Challan because it has already been used in JC Receive.');
      }

      await models.st_stock_register.destroy({ where: { voucherno: id, store_type: '2', remark: 'Job Challan Dispatch' }, transaction: t });
      await models.job_challan_items.destroy({ where: { challan_id: id }, transaction: t });
      await models.job_challans.destroy({ where: { id }, transaction: t });
    });
  }
}

module.exports = new JobChallanService();
`);
