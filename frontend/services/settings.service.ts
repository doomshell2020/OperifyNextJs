import apiClient from './apiClient';

export interface Category {
  id: number;
  category_name: string;
  description: string;
  status: string;
  added_time: string;
  updated_time: string;
}

export interface Product {
  id: number;
  item_name: string;
  category_id: number;
  category_name: string;
  uom: number;
  uom_name: string;
  tax: number;
  itemtype: string;
  cost_price: number;
  sale_price: number;
  min_order_qty: number;
  status: string;
  added_time: string;
}

export interface Supplier {
  id: number;
  name: string;
  address: string;
  contact_no: string;
  email: string;
  gst_number: string;
  pancard_number: string;
  tin_no: string;
  tds: string;
  contact_person: string;
  type: string;
  description: string;
  status: string;
  created_date: string;
}

export interface AppUser {
  id: number;
  user_name: string;
  email: string;
  mobile: string;
  role_id: number;
  db: string;
  is_admin: string;
  is_status: string;
  created: string;
  last_login: string;
}

export const settingsService = {
  // Categories
  async getCategories(search?: string): Promise<Category[]> {
    const res = await apiClient.get('/settings/categories', { params: search ? { search } : {} });
    return res.data.data;
  },
  async getCategoryById(id: number): Promise<Category> {
    const res = await apiClient.get(`/settings/categories/${id}`);
    return res.data.data;
  },
  async createCategory(data: { category_name: string; description?: string }): Promise<void> {
    await apiClient.post('/settings/categories', data);
  },
  async updateCategory(id: number, data: { category_name: string; description?: string }): Promise<void> {
    await apiClient.put(`/settings/categories/${id}`, data);
  },
  async toggleCategoryStatus(id: number, status: string): Promise<void> {
    await apiClient.patch(`/settings/categories/${id}/status`, { status });
  },
  async deleteCategory(id: number): Promise<void> {
    await apiClient.delete(`/settings/categories/${id}`);
  },

  // Products
  async getProducts(filters: { search?: string; category_id?: number; status?: string; itemtype?: string } = {}): Promise<Product[]> {
    const params = Object.fromEntries(Object.entries(filters).filter(([, v]) => v !== undefined && v !== ''));
    const res = await apiClient.get('/settings/products', { params });
    return res.data.data;
  },
  async getProductCategoryList(): Promise<{ id: number; category_name: string }[]> {
    const res = await apiClient.get('/settings/products/categories');
    return res.data.data;
  },
  async getUomList(): Promise<{ id: number; unit_name: string }[]> {
    const res = await apiClient.get('/settings/products/uom');
    return res.data.data;
  },
  async toggleProductStatus(id: number, status: string): Promise<void> {
    await apiClient.patch(`/settings/products/${id}/status`, { status });
  },

  // Taxes
  async getTaxes(): Promise<{ id: number; tax: number }[]> {
    const res = await apiClient.get('/settings/taxes');
    return res.data.data;
  },

  // Suppliers
  async getSuppliers(filters: { search?: string; status?: string; type?: string } = {}): Promise<Supplier[]> {
    const params = Object.fromEntries(Object.entries(filters).filter(([, v]) => v !== undefined && v !== ''));
    const res = await apiClient.get('/settings/suppliers', { params });
    return res.data.data;
  },
  async createSupplier(data: Partial<Supplier>): Promise<void> {
    await apiClient.post('/settings/suppliers', data);
  },
  async updateSupplier(id: number, data: Partial<Supplier>): Promise<void> {
    await apiClient.put(`/settings/suppliers/${id}`, data);
  },
  async toggleSupplierStatus(id: number, status: string): Promise<void> {
    await apiClient.patch(`/settings/suppliers/${id}/status`, { status });
  },

  // Users
  async getUsers(filters: { search?: string; is_status?: string } = {}): Promise<AppUser[]> {
    const params = Object.fromEntries(Object.entries(filters).filter(([, v]) => v !== undefined && v !== ''));
    const res = await apiClient.get('/settings/users', { params });
    return res.data.data;
  },
  async toggleUserStatus(id: number, status: string): Promise<void> {
    await apiClient.patch(`/settings/users/${id}/status`, { status });
  },
};
