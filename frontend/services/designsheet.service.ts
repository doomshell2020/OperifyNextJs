import apiClient from './apiClient';

export interface DesignSheetFilter {
  contract_id?: string;
  datestart?: string;
  dateto?: string;
  page?: number;
  limit?: number;
}

export const designsheetService = {
  getDesignSheets: async (filters?: DesignSheetFilter) => {
    const { data } = await apiClient.get('/designsheets', { params: filters });
    return data;
  },
  
  getDesignSheetById: async (id: string | number) => {
    const { data } = await apiClient.get(`/designsheets/${id}`);
    return data;
  },

  getDesignSheetForView: async (designsheetno: string) => {
    const { data } = await apiClient.get(`/designsheets/view/${designsheetno}`);
    return data;
  },

  getContractDetails: async (contractId: string) => {
    const { data } = await apiClient.get(`/designsheets/contract-details/${contractId}`);
    return data;
  },

  createDesignSheet: async (formData: FormData) => {
    const { data } = await apiClient.post('/designsheets', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    return data;
  },

  updateDesignSheet: async (id: string | number, formData: FormData) => {
    const { data } = await apiClient.put(`/designsheets/${id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    return data;
  },

  deleteDesignSheet: async (id: string | number) => {
    const { data } = await apiClient.delete(`/designsheets/${id}`);
    return data;
  },

  deleteDetail: async (id: string | number) => {
    const { data } = await apiClient.delete(`/designsheets/details/${id}`);
    return data;
  },

  checkItem: async (itemid: string | number, contractid: string | number) => {
    const { data } = await apiClient.get('/designsheets/check-item', { params: { itemid, contractid } });
    return data;
  },

  getBomProducts: async (contractid: string | number) => {
    const { data } = await apiClient.get('/designsheets/bom-products', { params: { contractid } });
    return data;
  },

  getIndentItems: async (fetch: string | number) => {
    const { data } = await apiClient.get('/designsheets/indent-items', { params: { fetch } });
    return data;
  },

  searchItems: async (query: string) => {
    const { data } = await apiClient.get('/designsheets/search-items', { params: { query } });
    return data;
  },

  searchContracts: async (query: string) => {
    const { data } = await apiClient.get('/designsheets/search-contracts', { params: { query } });
    return data;
  },

  getBomFinishedProducts: async (contractId: string) => {
    const { data } = await apiClient.get(`/designsheets/bom-products/${contractId}`);
    return data;
  },

  checkDesignSheetItem: async (contractid: string, itemid: string) => {
    const { data } = await apiClient.get('/designsheets/check-item', { params: { contractid, itemid } });
    return data;
  },

  getItemCatg: async (fetch: string | number) => {
    const { data } = await apiClient.get('/designsheets/item-category', { params: { fetch } });
    return data;
  }
};
