import apiClient from './apiClient';

export interface InspectionPoItem {
  id: number;
  item_id: number;
  item_name: string;
  order_qty: number;
  pending_qty: number;
  previously_received_qty: number;
  received_qty: number;
  rate: number;
  tax_rate: number;
  tax_id: number | null;
  order_base: number;
  order_tax: number;
  order_amount: number;
  uom: string;
  delivery_schedule_id: number | null;
}

export interface InspectionPoDetails {
  po: { id: number; purchaseorder_id: string; vendor_id: number; vendor_name: string; delivery_date: string | null };
  items: InspectionPoItem[];
}

export interface InspectionCreatePayload {
  inspection: {
    po_id: string;
    inspection_id: string;
    vendor_id?: number;
    inwarddate: string;
    bill_no: string;
    bill_date: string;
    remark: string;
    total_qty: number;
    total_tax: number;
    total_amt: number;
  };
  items: Array<{
    item_id: number;
    quantity: number;
    rate: number;
    tax_id: number | null;
    delivery_schedule_id: number | null;
    cost_price: number;
    tax: number;
    amount: number;
  }>;
}

class GrnInspectionService {
  async exportInspections() {
    const response = await apiClient.get('/grn-inspection/export/excel', { responseType: 'blob' });
    return response.data;
  }
  async listInspections(params: Record<string, string | number | undefined>) {
    const response = await apiClient.get('/grn-inspection', { params });
    return response.data;
  }

  async getDetails(id: number | string) {
    const response = await apiClient.get(`/grn-inspection/${id}`);
    return response.data;
  }

  async getPoDetails(po_id: string): Promise<InspectionPoDetails | null> {
    const response = await apiClient.get(`/grn-inspection/po/${encodeURIComponent(po_id)}`);
    return response.data.data;
  }

  async createInspection(data: InspectionCreatePayload) {
    const response = await apiClient.post('/grn-inspection', data);
    return response.data;
  }

  async getNextId() {
    const response = await apiClient.get('/grn-inspection/next-id');
    return response.data;
  }
}

const grnInspectionService = new GrnInspectionService();
export default grnInspectionService;
