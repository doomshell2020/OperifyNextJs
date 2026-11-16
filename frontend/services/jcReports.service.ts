import apiClient from './apiClient';

export interface JcMaterialFilters {
  challan_no?: string;
  vendor_id?: string;
  item_id?: string;
  from_date?: string;
  to_date?: string;
  balance?: string;
}

export interface JcMaterialRecord {
  challan_id: number | string;
  challan_no: string;
  jc_date: string;
  vendor_id: number | string;
  supplier: string;
  issued_qty: number | null;
  received_qty: number | null;
  pending_qty: number | null;
  unit_name: string;
  jc_item_name: string;
  balance_status: string;
  reference_note: string;
  formatted_issued_qty?: string;
  formatted_received_qty?: string;
  formatted_pending_qty?: string;
}

export interface JcMaterialSummary {
  jc_count: number;
  pending_count: number;
  review_count: number;
  units: Record<string, {
    unit_name: string;
    created: number;
    received: number;
    pending: number;
    complete: boolean;
  }>;
}

export interface JcMaterialResult {
  data: JcMaterialRecord[];
  summary: JcMaterialSummary;
  pagination: {
    page: number;
    limit: number;
    total: number;
    pages: number;
  };
}

export interface RawMaterialTrackingFilters {
  from_date?: string;
  to_date?: string;
  challan_no?: string;
  category_id?: string;
  vendor_id?: string;
}

export interface ReceiptDetail {
  id: string;
  date: string;
  receipt_no: string;
  reference: string | null;
  product: string;
  receipt_qty: number;
  quantity: number;
  unit: string;
  jc_id: string;
  jc_number: string;
}

export interface RawMaterialTrackingGroup {
  category: string;
  company: string;
  unit: string;
  sent: number;
  received: number;
  loss: number;
  pending: number;
  count: number;
  numbers: string;
  jcs?: Record<string, string>;
  receipts?: ReceiptDetail[];
}

export interface RawMaterialTrackingResult {
  data: RawMaterialTrackingGroup[];
  summary: Record<string, {
    unit: string;
    sent: number;
    received: number;
    loss: number;
    pending: number;
  }>;
  options: {
    item_id?: Record<string, string>;
    category_id?: Record<string, string>;
    vendor_id?: Record<string, string>;
  };
  unmatched: Array<{
    id: string;
    challan: string;
    reference: string;
    company: string;
    product: string;
    quantity: number;
    unit: string;
  }>;
  pagination: {
    page: number;
    limit: number;
    total: number;
    pages: number;
  };
}

function cleanFilters<T extends Record<string, any>>(filters: T): Partial<T> {
  const result: Partial<T> = {};
  for (const [key, value] of Object.entries(filters)) {
    if (value !== undefined && value !== null && value !== '') {
      (result as any)[key] = value;
    }
  }
  return result;
}

export const jcReportsService = {
  async getJcMaterialOptions(): Promise<{ suppliers: { id: number; name: string }[]; items: { id: number; item_name: string }[] }> {
    return (await apiClient.get('/reports/jc-material/options')).data.data;
  },

  async getJcMaterialReport(filters: JcMaterialFilters, page: number = 1, limit: number = 50): Promise<JcMaterialResult> {
    const params = { ...cleanFilters(filters), page, limit };
    return (await apiClient.get('/reports/jc-material', { params })).data;
  },

  async exportJcMaterialExcel(filters: JcMaterialFilters): Promise<void> {
    const response = await apiClient.get('/reports/jc-material/export', {
      params: cleanFilters(filters),
      responseType: 'blob'
    });
    const url = URL.createObjectURL(response.data);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'jc-report.xlsx';
    document.body.appendChild(link);
    link.click();
    link.remove();
    setTimeout(() => URL.revokeObjectURL(url), 1000);
  },

  async getRawTrackingOptions(): Promise<{ categories: { id: number; name: string }[]; suppliers: { id: number; name: string }[] }> {
    return (await apiClient.get('/reports/raw-material-tracking/options')).data.data;
  },

  async getRawTrackingReport(filters: RawMaterialTrackingFilters, page: number = 1, limit: number = 50): Promise<RawMaterialTrackingResult> {
    const params = { ...cleanFilters(filters), page, limit };
    return (await apiClient.get('/reports/raw-material-tracking', { params })).data;
  },

  async exportRawTrackingExcel(filters: RawMaterialTrackingFilters): Promise<void> {
    const response = await apiClient.get('/reports/raw-material-tracking/export', {
      params: cleanFilters(filters),
      responseType: 'blob'
    });
    const url = URL.createObjectURL(response.data);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'category-wise-raw-material-tracking.xlsx';
    document.body.appendChild(link);
    link.click();
    link.remove();
    setTimeout(() => URL.revokeObjectURL(url), 1000);
  }
};
