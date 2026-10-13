import apiClient from './apiClient';

export interface MonthlyStockFilters {
  year: number;
  month?: string;
  product_id?: string;
  category_ids?: string[];
}
export interface MonthlyStockRecord {
  item_id: number;
  item_name: string;
  category_name: string;
  month: string;
  opening_stock: number | string;
  received_stock: number | string;
  dispatched_stock: number | string;
  closing_stock: number | string;
  physical_stock: null;
}
export interface MonthlyStockResult {
  data: MonthlyStockRecord[];
  pagination: { page: number; page_size: number; total: number; total_pages: number };
}
function params(filters: MonthlyStockFilters) {
  return { ...filters, category_ids: filters.category_ids?.join(',') || undefined };
}
export const monthlyStockService = {
  async getOptions(): Promise<{ products: {id: number; item_name: string; category_id: number}[]; categories: {id: number; category_name: string}[] }> {
    return (await apiClient.get('/stock-register/monthly/options')).data.data;
  },
  async getReport(filters: MonthlyStockFilters, page: number, page_size: number): Promise<MonthlyStockResult> {
    return (await apiClient.get('/stock-register/monthly', { params: { ...params(filters), page, page_size } })).data;
  },
  async exportExcel(filters: MonthlyStockFilters) {
    const response = await apiClient.get('/stock-register/monthly/export', { params: params(filters), responseType: 'blob' });
    const url = URL.createObjectURL(response.data);
    const link = document.createElement('a');
    link.href = url;
    link.download = `Month_wise_Stock_Register_${filters.year}${filters.month ? '_'+filters.month.padStart(2,'0') : ''}.xlsx`;
    document.body.appendChild(link);
    link.click();
    link.remove();
    URL.revokeObjectURL(url);
  }
};
