import apiClient from './apiClient';

export interface Vendor {
  id: number;
  name: string;
  address: string;
  state_id: number | null;
  contact_no: string;
  email: string;
  vat_no: string | null;
  tin_no: string | null;
  tin_date: string | null;
  gst_number: string | null;
  pancard_number: string | null;
  tds: '0' | '1';
  description: string | null;
  status: 'Y' | 'N';
  contact_person: string | null;
  type: 'Vendor' | 'Transporter' | 'Customer';
}

class VendorService {
  async getDetails(id: number): Promise<Vendor> {
    const res = await apiClient.get<{ success: boolean; data: Vendor }>(`/vendors/${id}`);
    return res.data.data;
  }

  async updateVendor(id: number, data: Partial<Vendor>): Promise<void> {
    await apiClient.put(`/vendors/${id}`, data);
  }
}

export default new VendorService();
