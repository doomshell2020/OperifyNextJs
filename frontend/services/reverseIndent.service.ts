import apiClient from './apiClient';

export interface ReverseIndent {
  id: number;
  reverse_id: string;
  contract_id: number;
  contract_name?: string;
  workorder?: string;
  finishedproduct_id: number;
  product_name?: string;
  machine_id?: number;
  machine_name?: string;
  received_name: string;
  issue_date: string;
  items?: ReverseIndentItem[];
}

export interface ReverseIndentItem {
  item_id: number;
  quantity: string | number;
  item_name?: string;
  uom?: string;
}

export const reverseIndentService = {
  async getNextReverseId() {
    const res = await apiClient.get('/reverse-indent/next-id');
    return res.data.next_id;
  },

  async listReverseIndents(filters = {}) {
    const res = await apiClient.get('/reverse-indent', { params: filters });
    return res.data;
  },

  async getReverseIndentDetails(id: string | number) {
    const res = await apiClient.get(`/reverse-indent/${id}`);
    return res.data;
  },

  async saveReverseIndent(data: any) {
    const res = await apiClient.post('/reverse-indent', data);
    return res.data;
  },

  async deleteReverseIndent(id: string | number) {
    const res = await apiClient.delete(`/reverse-indent/${id}`);
    return res.data;
  }
};
