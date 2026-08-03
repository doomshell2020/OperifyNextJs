import apiClient from './apiClient';

export interface SummaryDetail {
  total: number;
  today: number;
  week: number;
  month: number;
  trend: {
    percentage: string;
    isUp: boolean;
    label: string;
  };
  sparkline: number[];
}

export interface SummaryData {
  contracts: SummaryDetail;
  purchaseOrders: SummaryDetail;
  grn: SummaryDetail;
  vendors: SummaryDetail;
  maintenance: SummaryDetail;
}

export interface ChartItem {
  name: string;
  value: number;
}

export interface ChartData {
  purchaseOrder: ChartItem[];
  production: ChartItem[];
  maintenance: ChartItem[];
}

export interface PurchaseOrderRecord {
  id: number;
  po_no: string;
  vendor_name: string;
  amount: number;
  status: string;
  postatus: string;
  date: string;
}

export interface ProductionRecord {
  id: number;
  manpower_day: string;
  plan_qty: string;
  status: string;
  date: string;
  machine_name: string;
}

export interface MaintenanceRecord {
  id: number;
  breakdown_type: string;
  assigned_to: string;
  date: string;
  status: string;
  machine_name: string;
}

export interface InspectionRecord {
  id: number;
  name: string;
  work_order_no: number;
  file: string;
  remark: string;
  date: string;
  status: string;
}

export interface GrnRecord {
  id: number;
  po_no: string;
  bill_no: string;
  date: string;
  amount: number;
  status: string;
  vendor_name: string;
}

class DashboardService {
  async getSummary(): Promise<SummaryData> {
    const response = await apiClient.get('/dashboard/summary');
    return response.data.data;
  }

  async getCharts(): Promise<ChartData> {
    const response = await apiClient.get('/dashboard/charts');
    return response.data.data;
  }

  async getLatestPurchaseOrders(): Promise<PurchaseOrderRecord[]> {
    const response = await apiClient.get('/dashboard/latest-purchase-orders');
    return response.data.data;
  }

  async getLatestProduction(): Promise<ProductionRecord[]> {
    const response = await apiClient.get('/dashboard/latest-production');
    return response.data.data;
  }

  async getLatestMaintenance(): Promise<MaintenanceRecord[]> {
    const response = await apiClient.get('/dashboard/latest-maintenance');
    return response.data.data;
  }

  async getLatestInspection(): Promise<InspectionRecord[]> {
    const response = await apiClient.get('/dashboard/latest-inspection');
    return response.data.data;
  }

  async getLatestGrn(): Promise<GrnRecord[]> {
    const response = await apiClient.get('/dashboard/latest-grn');
    return response.data.data;
  }
}

export default new DashboardService();
