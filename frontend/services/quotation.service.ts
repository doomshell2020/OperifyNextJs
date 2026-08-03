import apiClient from './apiClient';

export interface Quotation {
  id: number;
  quotation_id: string;
  vendor_id: number;
  is_award: number;
  delivery_date: string;
  acceptance_date: string;
  freight: string;
  payment_terms: string;
  remark: string;
  total_qty: number;
  total_tax: number;
  total_amt: number;
  is_revised: number;
  status: string;
  added_time: string;
  postatus: string;
  vendor_name: string;
  contact_no: string;
  email: string;
  gst_number: string;
}

export interface QuotationDetail extends Quotation {
  address: string;
  contact_person: string;
  transit_insurance: string;
  details: QuotationLineItem[];
}

export interface QuotationLineItem {
  id: number;
  item_id: number;
  item_amt: number;
  item_qty: number;
  item_tax_amt: number;
  item_total_amount: number;
  uom: string;
  item_name: string;
  item_description: string;
  uom_name: string;
}

export interface QuotationFilters {
  quotation_id?: string;
  vendor_id?: number;
  status?: string;
  is_award?: number;
  datefrom?: string;
  dateto?: string;
}

export const quotationService = {
  async getList(filters: QuotationFilters = {}): Promise<Quotation[]> {
    const params = Object.fromEntries(Object.entries(filters).filter(([, v]) => v !== undefined && v !== ''));
    const res = await apiClient.get('/quotations', { params });
    return res.data.data;
  },

  async getDetail(id: number): Promise<QuotationDetail> {
    const res = await apiClient.get(`/quotations/${id}/details`);
    return res.data.data;
  },

  async getVendors(): Promise<{ id: number; name: string }[]> {
    const res = await apiClient.get('/quotations/vendors');
    return res.data.data;
  },
};
