import apiClient from './apiClient';

export interface ContractListItem {
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
}

export interface ContractItemProduct {
  id: number;
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
  };
  items: ContractItemProduct[];
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
  contract_name?: string;
  vendor_name?: string;
  cost?: string;
  datefrom?: string;
  dateto?: string;
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

  async getContracts(filters: ContractFilters = {}): Promise<ContractListItem[]> {
    const params = new URLSearchParams();
    if (filters.contract_name) params.append('contract_name', filters.contract_name);
    if (filters.vendor_name) params.append('vendor_name', filters.vendor_name);
    if (filters.cost) params.append('cost', filters.cost);
    if (filters.datefrom) params.append('datefrom', filters.datefrom);
    if (filters.dateto) params.append('dateto', filters.dateto);

    const response = await apiClient.get(`/contracts?${params.toString()}`);
    return response.data.data;
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
