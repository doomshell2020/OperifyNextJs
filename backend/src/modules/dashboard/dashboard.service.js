const dashboardRepository = require('./dashboard.repository');

class DashboardService {
  async getSummary(dbPool) {
    const [contracts, po, grn, vendors, maintenance] = await Promise.all([
      dashboardRepository.getContractsCount(dbPool),
      dashboardRepository.getPurchaseOrdersCount(dbPool),
      dashboardRepository.getGrnCount(dbPool),
      dashboardRepository.getVendorsCount(dbPool),
      dashboardRepository.getMaintenanceCount(dbPool)
    ]);

    // Fetch sparklines (historical counts over last 7 days)
    const [contractsSpark, poSpark, grnSpark, vendorsSpark, maintenanceSpark] = await Promise.all([
      dashboardRepository.getSparklineData(dbPool, 'contracts', 'added_time'),
      dashboardRepository.getSparklineData(dbPool, 'st_purchaseorder', 'added_time'),
      dashboardRepository.getSparklineData(dbPool, 'st_goodsreceive', 'created_date'),
      dashboardRepository.getSparklineData(dbPool, 'vendors', 'created_date'),
      dashboardRepository.getSparklineData(dbPool, 'maintenance', 'created')
    ]);

    // Helper to compute percentage trend and up/down status
    const calculateTrend = (stats) => {
      const current = stats.month;
      const total = stats.total;
      const rate = total > 0 ? (current / total) * 100 : 0;
      return {
        percentage: `${Math.round(rate)}%`,
        isUp: current >= stats.week,
        label: 'vs total volume'
      };
    };

    return {
      contracts: {
        ...contracts,
        trend: calculateTrend(contracts),
        sparkline: contractsSpark
      },
      purchaseOrders: {
        ...po,
        trend: calculateTrend(po),
        sparkline: poSpark
      },
      grn: {
        ...grn,
        trend: calculateTrend(grn),
        sparkline: grnSpark
      },
      vendors: {
        ...vendors,
        trend: calculateTrend(vendors),
        sparkline: vendorsSpark
      },
      maintenance: {
        ...maintenance,
        trend: calculateTrend(maintenance),
        sparkline: maintenanceSpark
      }
    };
  }

  async getCharts(dbPool) {
    const [poStatus, prodStatus, maintStatus] = await Promise.all([
      dashboardRepository.getPurchaseOrderStatus(dbPool),
      dashboardRepository.getProductionStatus(dbPool),
      dashboardRepository.getMaintenanceStatus(dbPool)
    ]);

    // Format PO Status (O -> Open, C -> Closed, others)
    const formattedPo = poStatus.map(r => ({
      name: r.status === 'O' ? 'Open' : r.status === 'C' ? 'Closed' : 'Other',
      value: r.count
    }));

    // Format Production Status (O -> Open, C -> Closed)
    const formattedProd = prodStatus.map(r => ({
      name: r.status === 'O' ? 'Pending' : r.status === 'C' ? 'Completed' : 'Draft',
      value: r.count
    }));

    // Format Maintenance Status (pending, assigned, complete)
    const formattedMaint = maintStatus.map(r => ({
      name: r.status ? r.status.charAt(0).toUpperCase() + r.status.slice(1) : 'Unknown',
      value: r.count
    }));

    return {
      purchaseOrder: formattedPo,
      production: formattedProd,
      maintenance: formattedMaint
    };
  }

  async getLatestPurchaseOrders(dbPool) {
    return dashboardRepository.getLatestPurchaseOrders(dbPool);
  }

  async getLatestProduction(dbPool) {
    return dashboardRepository.getLatestProduction(dbPool);
  }

  async getLatestMaintenance(dbPool) {
    return dashboardRepository.getLatestMaintenance(dbPool);
  }

  async getLatestInspection(dbPool) {
    return dashboardRepository.getLatestInspection(dbPool);
  }

  async getLatestGrn(dbPool) {
    return dashboardRepository.getLatestGrn(dbPool);
  }
}

module.exports = new DashboardService();
