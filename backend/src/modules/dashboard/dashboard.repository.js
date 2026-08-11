const { Op, QueryTypes, literal, fn, col } = require('sequelize');

class DashboardRepository {
  // --- Summary metrics queries ---
  async getContractsCount(dbPool) {
    const { contracts } = dbPool.models;
    const [total, today, week, month] = await Promise.all([
      contracts.count(),
      contracts.count({ where: literal('DATE(added_time) = CURDATE()') }),
      contracts.count({ where: literal('YEARWEEK(added_time, 1) = YEARWEEK(CURDATE(), 1)') }),
      contracts.count({ where: literal('MONTH(added_time) = MONTH(CURDATE()) AND YEAR(added_time) = YEAR(CURDATE())') })
    ]);
    return { total, today, week, month };
  }

  async getPurchaseOrdersCount(dbPool) {
    const { st_purchaseorder } = dbPool.models;
    const [total, today, week, month] = await Promise.all([
      st_purchaseorder.count(),
      st_purchaseorder.count({ where: literal('DATE(added_time) = CURDATE()') }),
      st_purchaseorder.count({ where: literal('YEARWEEK(added_time, 1) = YEARWEEK(CURDATE(), 1)') }),
      st_purchaseorder.count({ where: literal('MONTH(added_time) = MONTH(CURDATE()) AND YEAR(added_time) = YEAR(CURDATE())') })
    ]);
    return { total, today, week, month };
  }

  async getGrnCount(dbPool) {
    const { st_goodsreceive } = dbPool.models;
    const [total, today, week, month] = await Promise.all([
      st_goodsreceive.count(),
      st_goodsreceive.count({ where: literal('DATE(created_date) = CURDATE()') }),
      st_goodsreceive.count({ where: literal('YEARWEEK(created_date, 1) = YEARWEEK(CURDATE(), 1)') }),
      st_goodsreceive.count({ where: literal('MONTH(created_date) = MONTH(CURDATE()) AND YEAR(created_date) = YEAR(CURDATE())') })
    ]);
    return { total, today, week, month };
  }

  async getVendorsCount(dbPool) {
    const { vendors } = dbPool.models;
    const [total, today, week, month] = await Promise.all([
      vendors.count({ where: literal("type = 'Vendor'") }),
      vendors.count({ where: literal("type = 'Vendor' AND DATE(created_date) = CURDATE()") }),
      vendors.count({ where: literal("type = 'Vendor' AND YEARWEEK(created_date, 1) = YEARWEEK(CURDATE(), 1)") }),
      vendors.count({ where: literal("type = 'Vendor' AND MONTH(created_date) = MONTH(CURDATE()) AND YEAR(created_date) = YEAR(CURDATE())") })
    ]);
    return { total, today, week, month };
  }

  async getMaintenanceCount(dbPool) {
    const { maintenance } = dbPool.models;
    const [total, today, week, month] = await Promise.all([
      maintenance.count(),
      maintenance.count({ where: literal('DATE(created) = CURDATE()') }),
      maintenance.count({ where: literal('YEARWEEK(created, 1) = YEARWEEK(CURDATE(), 1)') }),
      maintenance.count({ where: literal('MONTH(created) = MONTH(CURDATE()) AND YEAR(created) = YEAR(CURDATE())') })
    ]);
    return { total, today, week, month };
  }

  // --- Historical Sparkline Data (last 7 days counts) ---
  async getSparklineData(dbPool, tableName, dateColumn) {
    const query = `
      SELECT DATE(${dateColumn}) as date, COUNT(*) as count 
      FROM ${tableName} 
      WHERE ${dateColumn} >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
      GROUP BY DATE(${dateColumn}) 
      ORDER BY date ASC
    `;
    const rows = await dbPool.query(query, { type: QueryTypes.SELECT });
    
    // Fill in last 7 days with zeros if missing
    const dataMap = {};
    rows.forEach(r => {
      const dateStr = new Date(r.date).toISOString().split('T')[0];
      dataMap[dateStr] = r.count;
    });

    const sparkline = [];
    for (let i = 6; i >= 0; i--) {
      const d = new Date();
      d.setDate(d.getDate() - i);
      const dateStr = d.toISOString().split('T')[0];
      sparkline.push(dataMap[dateStr] || 0);
    }
    return sparkline;
  }

  // --- Status Chart metrics queries ---
  async getPurchaseOrderStatus(dbPool) {
    return await dbPool.models.st_purchaseorder.findAll({
      attributes: [['postatus', 'status'], [fn('COUNT', literal('*')), 'count']],
      group: ['postatus'],
      raw: true
    });
  }

  async getProductionStatus(dbPool) {
    return await dbPool.models.production.findAll({
      attributes: ['status', [fn('COUNT', literal('*')), 'count']],
      group: ['status'],
      raw: true
    });
  }

  async getMaintenanceStatus(dbPool) {
    return await dbPool.models.maintenance.findAll({
      attributes: [['maintenance_status', 'status'], [fn('COUNT', literal('*')), 'count']],
      group: ['maintenance_status'],
      raw: true
    });
  }

  // --- Latest 5 records queries ---
  async getLatestPurchaseOrders(dbPool) {
    const { st_purchaseorder, vendors } = dbPool.models;
    if (!st_purchaseorder.associations.vendor) {
      st_purchaseorder.belongsTo(vendors, { foreignKey: 'vendor_id', as: 'vendor' });
    }
    return await st_purchaseorder.findAll({
      attributes: [
        'id', ['purchaseorder_id', 'po_no'], ['total_amt', 'amount'],
        'status', 'postatus', ['added_time', 'date'], [col('vendor.name'), 'vendor_name']
      ],
      include: [{ model: vendors, as: 'vendor', attributes: [] }],
      order: [['id', 'DESC']],
      limit: 5,
      raw: true
    });
  }

  async getLatestProduction(dbPool) {
    const { production, machine_master } = dbPool.models;
    if (!production.associations.machine) {
      production.belongsTo(machine_master, { foreignKey: 'machine_id', as: 'machine' });
    }
    return await production.findAll({
      attributes: [
        'id', 'manpower_day', 'plan_qty', 'status', ['created', 'date'],
        'machine_id', [col('machine.machine_name'), 'machine_name']
      ],
      include: [{ model: machine_master, as: 'machine', attributes: [] }],
      order: [['id', 'DESC']],
      limit: 5,
      raw: true
    });
  }

  async getLatestMaintenance(dbPool) {
    const { maintenance, machine_master } = dbPool.models;
    if (!maintenance.associations.machine) {
      maintenance.belongsTo(machine_master, { foreignKey: 'machine_id', as: 'machine' });
    }
    return await maintenance.findAll({
      attributes: [
        'id', 'breakdown_type', 'assigned_to', ['created', 'date'],
        ['maintenance_status', 'status'], [col('machine.machine_name'), 'machine_name']
      ],
      include: [{ model: machine_master, as: 'machine', attributes: [] }],
      order: [['id', 'DESC']],
      limit: 5,
      raw: true
    });
  }

  async getLatestInspection(dbPool) {
    return await dbPool.models.st_inspection_report.findAll({
      attributes: [
        'id', 'name', 'work_order_no', 'file', 'remark',
        ['inspection_date', 'date'], 'status', 'created_at'
      ],
      order: [['id', 'DESC']],
      limit: 5,
      raw: true
    });
  }

  async getLatestGrn(dbPool) {
    const { st_goodsreceive, vendors } = dbPool.models;
    if (!st_goodsreceive.associations.vendor) {
      st_goodsreceive.belongsTo(vendors, { foreignKey: 'vendor_id', as: 'vendor' });
    }
    return await st_goodsreceive.findAll({
      attributes: [
        'id', ['purchaseorder_id', 'po_no'], 'bill_no', ['inwarddate', 'date'],
        ['total_amt', 'amount'], 'status', [col('vendor.name'), 'vendor_name']
      ],
      include: [{ model: vendors, as: 'vendor', attributes: [] }],
      order: [['id', 'DESC']],
      limit: 5,
      raw: true
    });
  }
}

module.exports = new DashboardRepository();
