import apiClient from './apiClient';

export interface Payment {
  id: number;
  vendor_id: number;
  store_type: number;
  inwarddate: string;
  bill_no: string;
  receipt_no: string;
  bill_date: string;
  total_amt: number;
  remark: string;
  created_date: string;
  pay_date: string;
  goods_id: string;
  status: string;
  vendor_name: string;
  contact_no: string;
  gst_number: string;
}

export interface ParticularPayment {
  id: number;
  particular: string;
  consignee: string;
  po_no: string;
  invoice: string;
  due_period: string;
  datefrom: string;
  bill_dis_date: string;
  amount: number;
  status: string;
}

export interface Vendor {
  id: number;
  name: string;
}

export interface PaymentFilters {
  vendor_id?: number;
  bill_no?: string;
  status?: string;
  datefrom?: string;
  dateto?: string;
}

export const paymentService = {
  async getList(filters: PaymentFilters = {}): Promise<Payment[]> {
    const params = Object.fromEntries(Object.entries(filters).filter(([, v]) => v !== undefined && v !== ''));
    const res = await apiClient.get('/payments', { params });
    return res.data.data;
  },

  async getDetail(id: number): Promise<Payment> {
    const res = await apiClient.get(`/payments/${id}/details`);
    return res.data.data;
  },

  async getParticularPayments(filters: Partial<{ po_no: string; status: string; datefrom: string; dateto: string }> = {}): Promise<ParticularPayment[]> {
    const params = Object.fromEntries(Object.entries(filters).filter(([, v]) => v));
    const res = await apiClient.get('/payments/particular', { params });
    return res.data.data;
  },

  async getVendors(): Promise<Vendor[]> {
    const res = await apiClient.get('/payments/vendors');
    return res.data.data;
  },
};
