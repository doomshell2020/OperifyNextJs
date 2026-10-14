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
  status?: 'draft' | 'finalized';
  draft_stock_clean?: number;
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

  async updateReverseIndent(id: string, data: any) {
    return (await apiClient.put(`/reverse-indent/${encodeURIComponent(id)}`, data)).data;
  },
  async getEditData(id: string) {
    return (await apiClient.get(`/reverse-indent/${encodeURIComponent(id)}/edit-data`)).data;
  },
  async lookup(kind: 'contracts' | 'machines' | 'products' | 'semi-products', q = '') {
    return (await apiClient.get(`/reverse-indent/lookups/${kind}`, { params: { q } })).data;
  },
  async contractProducts(id: number | string) {
    return (await apiClient.get(`/reverse-indent/contracts/${id}/products`)).data;
  },
  async designSheet(contract: number | string, product: number | string) {
    return (await apiClient.get(`/reverse-indent/contracts/${contract}/products/${product}/design-sheet`)).data;
  },
  async exportExcel(filters: any) {
    const res = await apiClient.get('/reverse-indent/export', {params: filters, responseType: 'blob'});
    const url = URL.createObjectURL(res.data);
    const link = document.createElement('a'); link.href=url; link.download='Reverse_Indent_Summary.xlsx'; link.click();
    window.setTimeout(()=>URL.revokeObjectURL(url),1000);
  },

  async deleteReverseIndent(id: string | number) {
    const res = await apiClient.delete(`/reverse-indent/${id}`);
    return res.data;
  }
};
