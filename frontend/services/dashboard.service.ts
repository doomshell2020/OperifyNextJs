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
  contact_no?: string;
  email?: string;
  total_qty?: number;
  delivery_date?: string;
  is_revised?: number;
}

export interface ProductionRecord {
  id: number;
  manpower_day: string;
  plan_qty: string;
  status: string;
  date: string;
  machine_name: string;
  po_no?: string;
  contract_id?: number;
  contract_name?: string;
  contract_number?: string;
  product_name?: string;
  start_date?: string;
  end_date?: string;
}

export interface MaintenanceRecord {
  id: number;
  breakdown_type: string;
  assigned_to: string;
  date: string;
  status: string;
  machine_name: string;
  total_time?: string;
  shift_incharge?: string;
  maintenance_incharge?: string;
  production_head?: string;
}

export interface InspectionRecord {
  id: number;
  name: string;
  work_order_no: number;
  file: string;
  remark: string;
  date: string;
  status: string;
  contract_name?: string;
  contract_number?: string;
  contract_id?: number;
}

export interface GrnRecord {
  id: number;
  po_no: string;
  bill_no: string;
  date: string;
  amount: number;
  status: string;
  vendor_name: string;
  bill_date?: string;
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
