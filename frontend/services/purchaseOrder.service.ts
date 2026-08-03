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

class PurchaseOrderService {
  async getHoverDetails(id: number | string): Promise<PurchaseOrderHoverData> {
    const response = await apiClient.get(`/purchase-orders/${id}/hover`);
    return response.data.data;
  }

  async getDetails(id: number | string): Promise<PurchaseOrderDetailsData> {
    const response = await apiClient.get(`/purchase-orders/${id}/details`);
    return response.data.data;
  }
}

export default new PurchaseOrderService();
