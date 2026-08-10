import apiClient from './apiClient';

export interface PurchaseOrderHoverData {
  id: number;
  po_number: string;
  po_date: string;
  vendor_name: string;
  vendor_code: string;
  contact_person: string;
  mobile: string;
  email: string;
  quantity: number;
  amount: number;
  delivery_date: string;
  status: string;
  created_by: string;
}

export interface PurchaseOrderItem {
  id: number;
  item_name: string;
  order_qty: number;
  pending_qty: number;
  rate: number;
  price: number;
  tax_percentage: number;
  tax_amt: number;
  amount: number;
  uom: string;
}

export interface PurchaseOrderDetailsData {
  po: {
    id: number;
    po_number: string;
    po_date: string;
    amendment_no: number;
    amendment_date: string;
    delivery_date: string;
    status: string;
    vendor_name: string;
    gst_number: string;
    total_amount: number;
  };
  items: PurchaseOrderItem[];
}

export interface PurchaseOrderListItem {
  id: number;
  po_number: string;
  po_date: string;
  vendor_id: number;
  vendor_name: string;
  mobile: string;
  quantity: number;
  received_qty: number;
  amount: number;
  delivery_date: string;
  status: string;
}

export interface PaginatedResponse<T> {
  items: T[];
  total: number;
  page: number;
  limit: number;
  totalPages: number;
}

class PurchaseOrderService {
  async listPurchaseOrders(params: any): Promise<PaginatedResponse<PurchaseOrderListItem>> {
    const response = await apiClient.get('/purchase-orders', { params });
    return response.data;
  }

  async getHoverDetails(id: number | string): Promise<PurchaseOrderHoverData> {
    const response = await apiClient.get(`/purchase-orders/${id}/hover`);
    return response.data.data;
  }

  async getDetails(id: number | string): Promise<PurchaseOrderDetailsData> {
    const response = await apiClient.get(`/purchase-orders/${id}/details`);
    return response.data.data;
  }

  async revisePurchaseOrder(id: number | string, data: any): Promise<void> {
    await apiClient.put(`/purchase-orders/${id}`, data);
  }

  async addDeliveryNote(id: number | string, data: any): Promise<void> {
    await apiClient.post(`/purchase-orders/${id}/delivery-note`, data);
  }

  async deletePurchaseOrder(id: number | string): Promise<void> {
    await apiClient.delete(`/purchase-orders/${id}`);
  }

  async getNextPoNumber(): Promise<string> {
    const response = await apiClient.get('/purchase-orders/next-id');
    return response.data.nextId;
  }

  async getItemHistory(itemId: string): Promise<any[]> {
    const response = await apiClient.get(`/purchase-orders/item/${itemId}/history`);
    return response.data.data;
  }

  async createPurchaseOrder(data: any): Promise<any> {
    const response = await apiClient.post('/purchase-orders', data);
    return response.data;
  }
}

export default new PurchaseOrderService();
