import apiClient from './apiClient';

class PermissionService {
  async getManagers() {
    const res = await apiClient.get('/permission/managers');
    return res.data.data;
  }

  async addManager(name: string) {
    const res = await apiClient.post('/permission/managers', { name });
    return res.data;
  }

  async addLabel(data: { manager_id: number; label_name: string; url: string }) {
    const res = await apiClient.post('/permission/labels', data);
    return res.data;
  }

  async getRoles() {
    const res = await apiClient.get('/permission/roles');
    return res.data.data;
  }

  async getAccess(role_id: number) {
    const res = await apiClient.get(`/permission/access?role_id=${role_id}`);
    return res.data.data;
  }

  async updateAccess(role_id: number, permissions: Record<string, { is_permission: number }>) {
    const res = await apiClient.post('/permission/access', { role_id, permissions });
    return res.data;
  }
}

export const permissionService = new PermissionService();
