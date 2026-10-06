import apiClient from './apiClient';

export interface ContractListItem {
  supplier_id: number;
  id: number;
  title: string;
  workorder: string;
  cost: string;
  contract_start_date: string;
  contract_end_date: string;
  issuedate: string;
  description: string;
  status: 'Y' | 'N';
  added_time: string;
  vendor_name: string;
  designsheet_count?: number;
}

export interface ContractItemProduct {
  id: number;
  product_id: number | string;
  price: string;
  quantity: string;
  item_name: string;
  uom: string;
}

export interface ContractDetailsData {
  contract: {
    id: number;
    title: string;
    workorder: string;
    cost: string;
    operation_cost: string;
    labour_cost: string;
    description: string;
    status: 'Y' | 'N';
    contract_start_date: string;
    contract_end_date: string;
    issuedate: string;
    vendor_name: string;
    gst_number: string;
    supplier_id?: number | string;
  };
  items: ContractItemProduct[];
  productionOrders: Record<string, any>[];
  inspectionReports: Record<string, any>[];
}

export interface FinishedProductInput {
  product_id: string | number;
  quantity: string;
  price: string;
}

export interface CreateContractPayload {
  supplier_id: string | number;
  title: string;
  workorder: string;
  cost: string;
  operation_cost: string;
  labour_cost: string;
  issuedate: string;
  contract_start_date: string;
  contract_end_date: string;
  description: string;
  finished_products: FinishedProductInput[];
}

export interface ContractFormData {
  vendors: { id: number; name: string }[];
  items: { id: number; name: string }[];
}
export interface ContractFilters {
  sort?: string;
  direction?: string;
  contract_id?: number;
  vendor_id?: number;
  contract_name?: string;
  vendor_name?: string;
  cost?: string;
  datefrom?: string;
  dateto?: string;
  page?: number;
  limit?: number;
}

export interface PaginatedContracts {
  data: ContractListItem[];
  total: number;
  page: number;
  limit: number;
  totalPages: number;
  current: number;
  hasPrevious: boolean;
  hasNext: boolean;
}

class ContractService {
  async getFormData(): Promise<ContractFormData> {
    const response = await apiClient.get('/contracts/form-data');
    return response.data.data;
  }

  async createContract(data: CreateContractPayload): Promise<any> {
    const response = await apiClient.post('/contracts', data);
    return response.data;
  }

  async updateContract(id: number | string, data: CreateContractPayload): Promise<any> {
    const response = await apiClient.put(`/contracts/${id}`, data);
    return response.data;
  }

  async deleteContract(id: number | string): Promise<any> {
    const response = await apiClient.delete(`/contracts/${id}`);
    return response.data;
  }

  async getContracts(filters: ContractFilters = {}): Promise<PaginatedContracts> {
    const params = new URLSearchParams();
    if (filters.contract_name) params.append('contract_name', filters.contract_name);
    if (filters.vendor_name) params.append('vendor_name', filters.vendor_name);
    if (filters.contract_id) params.append('contract_id', String(filters.contract_id));
    if (filters.vendor_id) params.append('vendor_id', String(filters.vendor_id));
    if (filters.sort) params.append('sort', filters.sort);
    if (filters.direction) params.append('direction', filters.direction);
    if (filters.cost) params.append('cost', filters.cost);
    if (filters.datefrom) params.append('datefrom', filters.datefrom);
    if (filters.dateto) params.append('dateto', filters.dateto);
    if (filters.page) params.append('page', filters.page.toString());
    if (filters.limit) params.append('limit', filters.limit.toString());

    const response = await apiClient.get(`/contracts?${params.toString()}`);
    return response.data;
  }

  async getDetails(id: number | string): Promise<ContractDetailsData> {
    const response = await apiClient.get(`/contracts/${id}/details`);
    return response.data.data;
  }

  async downloadPDF(id: number | string): Promise<void> {
    const token = localStorage.getItem('accessToken');
    const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:5000/api';
    const response = await fetch(`${API_URL}/contracts/${id}/pdf`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });

    if (!response.ok) {
      throw new Error(`Error generating PDF: ${response.status}`);
    }

    const blob = await response.blob();
    const pdfBlob = new Blob([blob], { type: 'application/pdf' });
    const url = window.URL.createObjectURL(pdfBlob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `contract-${id}.pdf`);
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
  }
}

export default new ContractService();
