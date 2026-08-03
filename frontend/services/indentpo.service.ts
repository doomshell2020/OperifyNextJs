import apiClient from './apiClient';

export interface IndentpoFilter {
  contract_id?: string;
  machine_id?: string;
  product_id?: string;
  date_from?: string;
  date_to?: string;
}

export interface Indentpo {
  id: number;
  indent_id: string;
  contract_id?: number;
  contract_name: string;
  workorder: string;
  product_name: string;
  machine_name: string;
  issued_name: string;
  issue_date: string;
  created_by?: string;
  items?: IndentpoItem[];
}

export interface IndentpoItem {
  item_id: number;
  raw_material_name: string;
  unit_name: string;
  design_qty: number;
  quantity?: number; // In detail view, this is what was issued
  issue_qty?: number; // During creation
  pending_qty: number;
  inhand_stock?: number;
  is_group?: number;
  category_id?: number;
  group_items?: { id: number; item_name: string; inhand_stock: number }[];
  is_added_from_group?: boolean;
}

export const indentpoService = {
  // Search Active Contracts
  searchContracts: async (query: string) => {
    const response = await apiClient.get(`/indentpo/contracts/search?q=${encodeURIComponent(query)}`);
    return response.data;
  },

  // Get Finished Products for Contract
  getContractProducts: async (contractId: string) => {
    const response = await apiClient.get(`/indentpo/contracts/${contractId}/products`);
    return response.data;
  },

  // Search Machines
  searchMachines: async (query: string) => {
    const response = await apiClient.get(`/indentpo/machines/search?q=${encodeURIComponent(query)}`);
    return response.data;
  },

  // Get Design Sheet (Raw Materials) Details
  getDesignSheetDetails: async (contractId: string, itemId: string) => {
    const response = await apiClient.get(`/indentpo/designsheet?contract_id=${contractId}&item_id=${itemId}`);
    return response.data;
  },

  // Get Next Indent ID
  getNextIndentId: async () => {
    const response = await apiClient.get(`/indentpo/next-id`);
    return response.data;
  },

  // Save IndentPO
  saveIndentpo: async (data: any) => {
    const response = await apiClient.post('/indentpo', data);
    return response.data;
  },

  async getIndentPoDetails(id: string | number): Promise<any> {
    const res = await apiClient.get(`/indentpo/view-details/${id}`);
    return res.data;
  },

  // List IndentPOs
  listIndentpo: async (filters: IndentpoFilter = {}) => {
    const params = new URLSearchParams();
    if (filters.contract_id) params.append('contract_id', filters.contract_id);
    if (filters.machine_id) params.append('machine_id', filters.machine_id);
    if (filters.product_id) params.append('product_id', filters.product_id);
    if (filters.date_from) params.append('date_from', filters.date_from);
    if (filters.date_to) params.append('date_to', filters.date_to);

    const response = await apiClient.get(`/indentpo?${params.toString()}`);
    return response.data;
  },

  // Get IndentPO Details
  getIndentpoDetail: async (indentId: string) => {
    const response = await apiClient.get(`/indentpo/${indentId}/detail`);
    return response.data;
  }
};
