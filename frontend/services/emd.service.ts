import apiClient from './apiClient';

export interface EmdRecord {
  id: number;
  bg_for: string;
  datefrom: string;
  bankguaranteeno: string;
  favour_of: string;
  po_no: string;
  amount: number;
  validupto: string;
  extenstionupto: string;
  lastdate: string;
  relese_date: string;
  po_or_rma: string;
  contect_per: string;
  board_name: string;
  currency_type: string;
  claim_upto: string;
  status: string;
  invoice_file: string;
}

export interface EmdAmount {
  id: number;
  bank_guarantee_id: number;
  recive_amount: number;
  recive_date: string;
  total_amount: number;
  description: string;
  invoice_file: string;
}

export interface EmdRemark {
  id: number;
  bank_guarantee_id: number;
  remark: string;
  remarked_by: string;
  created: string;
}

export interface EmdDetail extends EmdRecord {
  totalReceived: number;
  remainingAmount: number;
  amounts: EmdAmount[];
  remarks: EmdRemark[];
}

export interface EmdFilters {
  bg_for?: string;
  status?: string;
  bankguaranteeno?: string;
  datefrom?: string;
  dateto?: string;
  due_from?: string;
  due_to?: string;
}

export const emdService = {
  async getList(filters: EmdFilters = {}): Promise<EmdRecord[]> {
    const params = Object.fromEntries(Object.entries(filters).filter(([, v]) => v));
    const res = await apiClient.get('/emd', { params });
    return res.data.data;
  },

  async getDetail(id: number): Promise<EmdDetail> {
    const res = await apiClient.get(`/emd/${id}/details`);
    return res.data.data;
  },
};
