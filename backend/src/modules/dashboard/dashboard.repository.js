const { Op, QueryTypes, literal, fn, col } = require('sequelize');

class DashboardRepository {
  // Match the legacy overview: April financial year and rolling seven-day activity.
  async getPeriodCounts(dbPool, table, dateColumn, totalCondition, todayCondition = '1=1') {
    const [row] = await dbPool.query(`
      SELECT
        SUM(CASE WHEN ${totalCondition} THEN 1 ELSE 0 END) AS total,
        SUM(CASE WHEN ${todayCondition} AND DATE(${dateColumn}) = CURDATE() THEN 1 ELSE 0 END) AS today,
        SUM(CASE WHEN DATE(${dateColumn}) > DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) AS week,
        SUM(CASE WHEN DATE(${dateColumn}) >= DATE_FORMAT(CURDATE(), '%Y-%m-01') THEN 1 ELSE 0 END) AS month
      FROM ${table}
    `, { type: QueryTypes.SELECT });
    return Object.fromEntries(['total', 'today', 'week', 'month'].map(key => [key, Number(row[key] || 0)]));
  }

  financialYearCondition(column) {
    return `DATE(${column}) >= CONCAT(YEAR(CURDATE()) - (MONTH(CURDATE()) < 4), '-04-01')`;
  }

  async getContractsCount(dbPool) {
    const [row] = await dbPool.query(`
      SELECT
        (SELECT COUNT(*) FROM contracts WHERE ${this.financialYearCondition('added_time')}) AS total,
        COUNT(DISTINCT CASE WHEN DATE(production_date) = CURDATE() THEN contract_id END) AS today,
        COUNT(DISTINCT CASE WHEN DATE(production_date) > DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN contract_id END) AS week,
        COUNT(DISTINCT CASE WHEN DATE(production_date) >= DATE_FORMAT(CURDATE(), '%Y-%m-01') THEN contract_id END) AS month
      FROM production
    `, { type: QueryTypes.SELECT });
    return Object.fromEntries(['total', 'today', 'week', 'month'].map(key => [key, Number(row[key] || 0)]));
  }

  async getPurchaseOrdersCount(dbPool) {
    return this.getPeriodCounts(dbPool, 'st_purchaseorder', 'added_time', this.financialYearCondition('added_time'));
  }

  async getGrnCount(dbPool) {
    return this.getPeriodCounts(dbPool, 'st_goodsreceive', 'created_date', this.financialYearCondition('created_date'));
  }

  async getVendorsCount(dbPool) {
    return this.getPeriodCounts(dbPool, 'vendors', 'created_date', "status = 'Y'");
  }

  async getMaintenanceCount(dbPool) {
    return this.getPeriodCounts(dbPool, 'maintenance', 'datefrom', this.financialYearCondition('datefrom'), "status = 'Y'");
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
    const [row] = await dbPool.query(`
      SELECT COUNT(*) AS total,
        SUM(CASE WHEN postatus = 'C' THEN 1 ELSE 0 END) AS complete,
        (SELECT COUNT(DISTINCT purchaseorder_id) FROM st_goodsreceive) AS received
      FROM st_purchaseorder
    `, { type: QueryTypes.SELECT });
    const complete = Number(row.complete || 0);
    const active = Math.min(Number(row.total) - complete, Math.max(0, Number(row.received) - complete));
    return [
      { status: 'Complete', count: complete },
      { status: 'Active', count: active },
      { status: 'Pending', count: Math.max(0, Number(row.total) - complete - active) }
    ];
  }

  async getProductionStatus(dbPool) {
    const [row] = await dbPool.query(`
      SELECT COUNT(*) AS total,
        SUM(CASE WHEN status = 'C' THEN 1 ELSE 0 END) AS complete,
        (SELECT COUNT(DISTINCT po_id, item_id) FROM production) AS started
      FROM productionorder
    `, { type: QueryTypes.SELECT });
    const complete = Number(row.complete || 0);
    const active = Math.min(Number(row.total) - complete, Math.max(0, Number(row.started) - complete));
    return [
      { status: 'Complete', count: complete },
      { status: 'Active', count: active },
      { status: 'Pending', count: Math.max(0, Number(row.total) - complete - active) }
    ];
  }

  async getMaintenanceStatus(dbPool) {
    return await dbPool.models.maintenance.findAll({
      attributes: [['maintenance_status', 'status'], [fn('COUNT', literal('*')), 'count']],
      group: ['maintenance_status'],
      where: { status: 'Y' },
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
        'status', 'postatus', 'is_revised', 'total_qty', 'delivery_date', ['added_time', 'date'],
        [col('vendor.name'), 'vendor_name'], [col('vendor.contact_no'), 'contact_no'], [col('vendor.email'), 'email']
      ],
      include: [{ model: vendors, as: 'vendor', attributes: [] }],
      where: { status: { [Op.in]: ['Y', 'R'] } },
      order: [['id', 'DESC']],
      limit: 5,
      raw: true
    });
  }

  async getLatestProduction(dbPool) {
    return dbPool.query(`
      SELECT p.id, p.po_id AS po_no, p.contract_id, c.title AS contract_name, c.workorder AS contract_number,
        a.item_name AS product_name, p.plannedqty AS plan_qty, p.status,
        p.issuedate AS date, p.startdate AS start_date, p.enddate AS end_date
      FROM productionorder p
      LEFT JOIN contracts c ON p.contract_id = c.id
      LEFT JOIN st_additem a ON p.item_id = a.id
      ORDER BY p.id DESC LIMIT 5
    `, { type: QueryTypes.SELECT });
  }

  async getLatestMaintenance(dbPool) {
    const { maintenance, machine_master } = dbPool.models;
    if (!maintenance.associations.machine) {
      maintenance.belongsTo(machine_master, { foreignKey: 'machine_id', as: 'machine' });
    }
    return await maintenance.findAll({
      attributes: [
        'id', 'breakdown_type', 'assigned_to', ['datefrom', 'date'],
        ['maintenance_status', 'status'], 'total_time', 'shift_incharge', 'maintenance_incharge',
        'production_head', [col('machine.machine_name'), 'machine_name']
      ],
      include: [{ model: machine_master, as: 'machine', attributes: [] }],
      where: { status: 'Y' },
      order: [['datefrom', 'DESC'], ['id', 'DESC']],
      limit: 5,
      raw: true
    });
  }

  async getLatestInspection(dbPool) {
    return dbPool.query(`
      SELECT i.id, i.name, i.work_order_no, i.file, i.remark,
        i.inspection_date AS date, i.status, i.created_at, c.title AS contract_name,
        c.id AS contract_id, c.workorder AS contract_number
      FROM st_inspection_report i
      LEFT JOIN contracts c ON i.work_order_no = c.id
      WHERE i.status = 'Y'
      ORDER BY i.id DESC LIMIT 5
    `, { type: QueryTypes.SELECT });
  }

  async getLatestGrn(dbPool) {
    const { st_goodsreceive, vendors } = dbPool.models;
    if (!st_goodsreceive.associations.vendor) {
      st_goodsreceive.belongsTo(vendors, { foreignKey: 'vendor_id', as: 'vendor' });
    }
    return await st_goodsreceive.findAll({
      attributes: [
        'id', ['purchaseorder_id', 'po_no'], 'bill_no', 'bill_date', ['inwarddate', 'date'],
        ['total_amt', 'amount'], 'status', [col('vendor.name'), 'vendor_name']
      ],
      include: [{ model: vendors, as: 'vendor', attributes: [] }],
      order: [['inwarddate', 'DESC'], ['id', 'DESC']],
      limit: 5,
      raw: true
    });
  }
}

module.exports = new DashboardRepository();
