import apiClient from './apiClient';

class GrnInspectionService {
  async listInspections(params: any) {
    const response = await apiClient.get('/grn-inspection', { params });
    return response.data;
  }

  async getDetails(id: number | string) {
    const response = await apiClient.get(`/grn-inspection/${id}`);
    return response.data;
  }

  async getPoDetails(po_id: string) {
    const response = await apiClient.get(`/grn-inspection/po/${po_id}`);
    return response.data;
  }

  async createInspection(data: any) {
    const response = await apiClient.post('/grn-inspection', data);
    return response.data;
  }

  async getNextId() {
    const response = await apiClient.get('/grn-inspection/next-id');
    return response.data;
  }
}

const grnInspectionService = new GrnInspectionService();
export default grnInspectionService;
