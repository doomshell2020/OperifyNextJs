import apiClient from './apiClient';

export interface StockRegisterFilters {
  date_from: string;
  date_to: string;
  product_id?: string;
  category_id?: string;
  category_ids?: string[];
}

function stockRegisterParams({ category_ids, ...filters }: StockRegisterFilters) {
  const params = Object.fromEntries(Object.entries(filters).filter(([, value]) => value !== undefined && value !== ''));
  if (category_ids?.length) params.category_ids = category_ids.join(',');
  return params;
}

export interface StockRecord {
  item_id: number;
  item_name: string;
  category_name: string;
  date_range: string;
  opening_stock: string;
  received_stock: string;
  dispatched_stock: string;
  closing_stock: string;
}

export interface StockRegisterOptions {
  products: { id: number; item_name: string }[];
  categories: { id: number; category_name: string }[];
}

export interface DailyStockRecord {
  item_id: number;
  item_name: string;
  category_name: string;
  opening_stock: number;
  received_stock: number;
  issued_stock: number;
  reverse_stock: number;
  return_stock: number;
  closing_stock: number;
}

export interface ReceivedStockDetail {
  id: number;
  store_type: string;
  item_name: string;
  po_id: string | null;
  po_date: string | null;
  is_revised: number | null;
  purchaseorder_id: number | null;
  vendor_name: string | null;
  contact_no: string | null;
  email: string | null;
  inwarddate: string | null;
  bill_no: string | null;
  bill_date: string | null;
  quantity: number;
}

export interface DispatchedStockDetail {
  indent_id: string | null;
  quantity: number;
  issue_date: string | null;
  created: string | null;
  sub_contractors_id: number | null;
  subcontractor_name: string | null;
  subcontractor_mobile: string | null;
  subcontractor_email: string | null;
  indent_status: string | null;
}

export const stockRegisterService = {
  async getRegisterOptions(): Promise<StockRegisterOptions> {
    const res = await apiClient.get('/stock-register/options');
    return res.data.data;
  },
  async getCategories(): Promise<{id: number, category_name: string}[]> {
    const res = await apiClient.get('/stock-register/categories');
    return res.data.data;
  },

  async getDailyStock(filters: { date: string; category_ids?: string[] }): Promise<DailyStockRecord[]> {
    const params = new URLSearchParams();
    if (filters.date) params.append('date', filters.date);
    if (filters.category_ids && filters.category_ids.length > 0) {
      params.append('category_ids', filters.category_ids.join(','));
    }
    const res = await apiClient.get('/stock-register/daily', { params });
    return res.data.data;
  },

  async exportDailyStockExcel(filters: { date: string; category_ids?: string[] }) {
    const params = new URLSearchParams();
    if (filters.date) params.append('date', filters.date);
    if (filters.category_ids && filters.category_ids.length > 0) {
      params.append('category_ids', filters.category_ids.join(','));
    }
    const res = await apiClient.get('/stock-register/daily/export', { params, responseType: 'blob' });
    const url = window.URL.createObjectURL(new Blob([res.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', 'Daily_Stock_Register.xlsx');
    document.body.appendChild(link);
    link.click();
    link.parentNode?.removeChild(link);
  },
  async getStockRegister(filters: StockRegisterFilters): Promise<StockRecord[]> {
    const params = stockRegisterParams(filters);
    const res = await apiClient.get('/stock-register', { params });
    return res.data.data;
  },

  async exportExcel(filters: StockRegisterFilters) {
    const params = stockRegisterParams(filters);
    const res = await apiClient.get('/stock-register/export', { params, responseType: 'blob' }).catch(async error => {
      if (error.response?.data instanceof Blob) {
        try { error.response.data = JSON.parse(await error.response.data.text()); } catch { /* Preserve original error. */ }
      }
      throw error;
    });
    const url = window.URL.createObjectURL(new Blob([res.data]));
    const link = document.createElement('a');
    link.href = url;
    const disposition = res.headers['content-disposition'] || '';
    const filename = disposition.match(/filename="([^"]+)"/)?.[1];
    link.setAttribute('download', filename || (filters.product_id ? 'Export_Summary_Stock_Item.xlsx' : 'Export_Consolidated_Stock_Register.xlsx'));
    document.body.appendChild(link);
    link.click();
    link.parentNode?.removeChild(link);
    window.setTimeout(() => window.URL.revokeObjectURL(url), 1000);
  },

  async getReceivedStockDetails(filters: { date: string; product_id: string }): Promise<ReceivedStockDetail[]> {
    const res = await apiClient.get('/stock-register/details/received', { params: filters });
    return res.data.data;
  },

  async getReceivedStockPage(filters: { date: string; product_id: string }): Promise<{ data: ReceivedStockDetail[]; product: { id: number; item_name: string } }> {
    const res = await apiClient.get('/stock-register/details/received', { params: filters });
    return res.data;
  },

  async getDispatchedStockDetails(filters: { date: string; product_id: string }): Promise<DispatchedStockDetail[]> {
    const res = await apiClient.get('/stock-register/details/dispatched', { params: filters });
    return res.data.data;
  }
};
