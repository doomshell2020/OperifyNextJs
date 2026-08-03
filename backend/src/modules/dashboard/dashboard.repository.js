class DashboardRepository {
  // --- Summary metrics queries ---
  async getContractsCount(dbPool) {
    const totalQuery = 'SELECT COUNT(*) as total FROM contracts';
    const todayQuery = 'SELECT COUNT(*) as today FROM contracts WHERE DATE(added_time) = CURDATE()';
    const weekQuery = 'SELECT COUNT(*) as week FROM contracts WHERE YEARWEEK(added_time, 1) = YEARWEEK(CURDATE(), 1)';
    const monthQuery = 'SELECT COUNT(*) as month FROM contracts WHERE MONTH(added_time) = MONTH(CURDATE()) AND YEAR(added_time) = YEAR(CURDATE())';
    
    const [[total], [today], [week], [month]] = await Promise.all([
      dbPool.execute(totalQuery),
      dbPool.execute(todayQuery),
      dbPool.execute(weekQuery),
      dbPool.execute(monthQuery)
    ]);
    
    return { total: total[0].total, today: today[0].today, week: week[0].week, month: month[0].month };
  }

  async getPurchaseOrdersCount(dbPool) {
    const totalQuery = 'SELECT COUNT(*) as total FROM st_purchaseorder';
    const todayQuery = 'SELECT COUNT(*) as today FROM st_purchaseorder WHERE DATE(added_time) = CURDATE()';
    const weekQuery = 'SELECT COUNT(*) as week FROM st_purchaseorder WHERE YEARWEEK(added_time, 1) = YEARWEEK(CURDATE(), 1)';
    const monthQuery = 'SELECT COUNT(*) as month FROM st_purchaseorder WHERE MONTH(added_time) = MONTH(CURDATE()) AND YEAR(added_time) = YEAR(CURDATE())';
    
    const [[total], [today], [week], [month]] = await Promise.all([
      dbPool.execute(totalQuery),
      dbPool.execute(todayQuery),
      dbPool.execute(weekQuery),
      dbPool.execute(monthQuery)
    ]);
    
    return { total: total[0].total, today: today[0].today, week: week[0].week, month: month[0].month };
  }

  async getGrnCount(dbPool) {
    const totalQuery = 'SELECT COUNT(*) as total FROM st_goodsreceive';
    const todayQuery = 'SELECT COUNT(*) as today FROM st_goodsreceive WHERE DATE(created_date) = CURDATE()';
    const weekQuery = 'SELECT COUNT(*) as week FROM st_goodsreceive WHERE YEARWEEK(created_date, 1) = YEARWEEK(CURDATE(), 1)';
    const monthQuery = 'SELECT COUNT(*) as month FROM st_goodsreceive WHERE MONTH(created_date) = MONTH(CURDATE()) AND YEAR(created_date) = YEAR(CURDATE())';
    
    const [[total], [today], [week], [month]] = await Promise.all([
      dbPool.execute(totalQuery),
      dbPool.execute(todayQuery),
      dbPool.execute(weekQuery),
      dbPool.execute(monthQuery)
    ]);
    
    return { total: total[0].total, today: today[0].today, week: week[0].week, month: month[0].month };
  }

  async getVendorsCount(dbPool) {
    const totalQuery = "SELECT COUNT(*) as total FROM vendors WHERE type = 'Vendor'";
    const todayQuery = "SELECT COUNT(*) as today FROM vendors WHERE type = 'Vendor' AND DATE(created_date) = CURDATE()";
    const weekQuery = "SELECT COUNT(*) as week FROM vendors WHERE type = 'Vendor' AND YEARWEEK(created_date, 1) = YEARWEEK(CURDATE(), 1)";
    const monthQuery = "SELECT COUNT(*) as month FROM vendors WHERE type = 'Vendor' AND MONTH(created_date) = MONTH(CURDATE()) AND YEAR(created_date) = YEAR(CURDATE())";
    
    const [[total], [today], [week], [month]] = await Promise.all([
      dbPool.execute(totalQuery),
      dbPool.execute(todayQuery),
      dbPool.execute(weekQuery),
      dbPool.execute(monthQuery)
    ]);
    
    return { total: total[0].total, today: today[0].today, week: week[0].week, month: month[0].month };
  }

  async getMaintenanceCount(dbPool) {
    const totalQuery = 'SELECT COUNT(*) as total FROM maintenance';
    const todayQuery = 'SELECT COUNT(*) as today FROM maintenance WHERE DATE(created) = CURDATE()';
    const weekQuery = 'SELECT COUNT(*) as week FROM maintenance WHERE YEARWEEK(created, 1) = YEARWEEK(CURDATE(), 1)';
    const monthQuery = 'SELECT COUNT(*) as month FROM maintenance WHERE MONTH(created) = MONTH(CURDATE()) AND YEAR(created) = YEAR(CURDATE())';
    
    const [[total], [today], [week], [month]] = await Promise.all([
      dbPool.execute(totalQuery),
      dbPool.execute(todayQuery),
      dbPool.execute(weekQuery),
      dbPool.execute(monthQuery)
    ]);
    
    return { total: total[0].total, today: today[0].today, week: week[0].week, month: month[0].month };
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
    const [rows] = await dbPool.execute(query);
    
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
    const query = 'SELECT postatus as status, COUNT(*) as count FROM st_purchaseorder GROUP BY postatus';
    const [rows] = await dbPool.execute(query);
    return rows;
  }

  async getProductionStatus(dbPool) {
    const query = 'SELECT status, COUNT(*) as count FROM production GROUP BY status';
    const [rows] = await dbPool.execute(query);
    return rows;
  }

  async getMaintenanceStatus(dbPool) {
    const query = 'SELECT maintenance_status as status, COUNT(*) as count FROM maintenance GROUP BY maintenance_status';
    const [rows] = await dbPool.execute(query);
    return rows;
  }

  // --- Latest 5 records queries ---
  async getLatestPurchaseOrders(dbPool) {
    const query = `
      SELECT po.id, po.purchaseorder_id as po_no, po.total_amt as amount, po.status, po.postatus, po.added_time as date, v.name as vendor_name 
      FROM st_purchaseorder po 
      LEFT JOIN vendors v ON po.vendor_id = v.id 
      ORDER BY po.id DESC 
      LIMIT 5
    `;
    const [rows] = await dbPool.execute(query);
    return rows;
  }

  async getLatestProduction(dbPool) {
    const query = `
      SELECT p.id, p.manpower_day, p.plan_qty, p.status, p.created as date, p.machine_id, m.machine_name
      FROM production p
      LEFT JOIN machine_master m ON p.machine_id = m.id
      ORDER BY p.id DESC 
      LIMIT 5
    `;
    const [rows] = await dbPool.execute(query);
    return rows;
  }

  async getLatestMaintenance(dbPool) {
    const query = `
      SELECT m.id, m.breakdown_type, m.assigned_to, m.created as date, m.maintenance_status as status, mm.machine_name 
      FROM maintenance m 
      LEFT JOIN machine_master mm ON m.machine_id = mm.id 
      ORDER BY m.id DESC 
      LIMIT 5
    `;
    const [rows] = await dbPool.execute(query);
    return rows;
  }

  async getLatestInspection(dbPool) {
    const query = `
      SELECT id, name, work_order_no, file, remark, inspection_date as date, status, created_at
      FROM st_inspection_report 
      ORDER BY id DESC 
      LIMIT 5
    `;
    const [rows] = await dbPool.execute(query);
    return rows;
  }

  async getLatestGrn(dbPool) {
    const query = `
      SELECT grn.id, grn.purchaseorder_id as po_no, grn.bill_no, grn.inwarddate as date, grn.total_amt as amount, grn.status, v.name as vendor_name 
      FROM st_goodsreceive grn 
      LEFT JOIN vendors v ON grn.vendor_id = v.id 
      ORDER BY grn.id DESC 
      LIMIT 5
    `;
    const [rows] = await dbPool.execute(query);
    return rows;
  }
}

module.exports = new DashboardRepository();
