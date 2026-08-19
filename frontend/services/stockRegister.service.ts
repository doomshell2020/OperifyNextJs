import apiClient from './apiClient';

export interface StockRecord {
  item_id: number;
  item_name: string;
  category_name: string;
  date_range: string;
  opening_stock: number;
  received_stock: number;
  dispatched_stock: number;
  closing_stock: number;
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

export const stockRegisterService = {
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
  async getStockRegister(filters: { date_from: string; date_to: string; product_id?: string; category_id?: string }): Promise<StockRecord[]> {
    const params = Object.fromEntries(Object.entries(filters).filter(([, v]) => v !== undefined && v !== ''));
    const res = await apiClient.get('/stock-register', { params });
    return res.data.data;
  },

  async exportExcel(filters: { date_from: string; date_to: string; product_id?: string; category_id?: string }) {
    const params = Object.fromEntries(Object.entries(filters).filter(([, v]) => v !== undefined && v !== ''));
    const res = await apiClient.get('/stock-register/export', { params, responseType: 'blob' });
    const url = window.URL.createObjectURL(new Blob([res.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', 'Stock_Register.xlsx');
    document.body.appendChild(link);
    link.click();
    link.parentNode?.removeChild(link);
  },

  async getReceivedStockDetails(filters: { date: string; product_id: string }): Promise<any[]> {
    const res = await apiClient.get('/stock-register/details/received', { params: filters });
    return res.data.data;
  },

  async getDispatchedStockDetails(filters: { date: string; product_id: string }): Promise<any[]> {
    const res = await apiClient.get('/stock-register/details/dispatched', { params: filters });
    return res.data.data;
  }
};
