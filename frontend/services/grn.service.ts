import apiClient from './apiClient';

export interface Grn {
  id?: number;
  purchaseorder_id: string;
  vendor_id: number;
  vendor_name?: string;
  bill_no: string;
  bill_date: string;
  inwarddate: string;
  remark?: string;
  total_qty?: number;
  total_amt?: number;
  status?: string;
}

export interface GrnItem {
  id?: number;
  item_id: number;
  item_name?: string;
  order_qty?: number;
  pending_qty?: number;
  received_qty: number;
  rate: number;
  tax_rate?: number;
  uom?: string;
}

export interface GrnPayload extends Grn {
  inspection_id?: string;
  items: GrnItem[];
}

class GrnService {
  async getList(params: { page?: number; limit?: number; po_id?: string; vendor_id?: string; from_date?: string; to_date?: string }) {
    const response = await apiClient.get('/grn', { params });
    return response.data;
  }

  async getInspectionForGrn(inspectionId: string) {
    const response = await apiClient.get(`/grn/inspection/${inspectionId}`);
    return response.data;
  }

  async updateGrn(id: string | number, payload: any): Promise<any> {
    const { data } = await apiClient.put(`/grn/${id}`, payload);
    return data;
  }

  async exportGrns(params: {
    po_id?: string;
    vendor_id?: string;
    from_date?: string;
    to_date?: string;
  }): Promise<Blob> {
    const { data } = await apiClient.get('/grn/export', {
      params,
      responseType: 'blob'
    });
    return data;
  }

  async create(payload: GrnPayload) {
    const response = await apiClient.post('/grn', payload);
    return response.data;
  }

  async getDetails(id: string) {
    const response = await apiClient.get(`/grn/${id}`);
    return response.data;
  }
}

export const grnService = new GrnService();
