const permissionRepository = require('./permission.repository');

class PermissionService {
  async getManagers(dbPool) {
    return await permissionRepository.getManagers(dbPool);
  }

  async addManager(dbPool, name) {
    const existing = await permissionRepository.getManagerByName(dbPool, name);
    if (existing) {
      throw new Error('Manager already exists.');
    }
    await permissionRepository.createManager(dbPool, {
      name,
      created: new Date()
    });
  }

  async addLabel(dbPool, { manager_id, label_name, url }) {
    const existing = await permissionRepository.getLabelByUrlAndManager(dbPool, manager_id, url);
    if (existing) {
      throw new Error('The Url already exists for this manager.');
    }
    await permissionRepository.createLabel(dbPool, {
      manager_id,
      label_name,
      url,
      created: new Date()
    });
  }

  async getRoles(dbPool) {
    return await permissionRepository.getRoles(dbPool);
  }

  async getAccess(dbPool, role_id) {
    const labels = await permissionRepository.getLabelsWithManagers(dbPool);
    const accessRecords = await permissionRepository.getAccessByRole(dbPool, role_id);

    const accessMap = {};
    accessRecords.forEach(a => {
      accessMap[a.p_lable_id] = String(a.is_permission);
    });

    const grouped = {};
    labels.forEach(label => {
      const isChecked = accessMap[label.id] == '1';
      if (!grouped[label.manager_id]) {
        grouped[label.manager_id] = [];
      }
      grouped[label.manager_id].push({
        label_name: label.label_name,
        p_lable_id: label.id,
        url: label.url,
        checked: isChecked,
        manager_name: label.manager_name
      });
    });

    return grouped;
  }

  async updateAccess(dbPool, { role_id, permissions }) {
    for (const p_lable_id of Object.keys(permissions)) {
      const is_permission = permissions[p_lable_id].is_permission;
      const existing = await permissionRepository.getAccessByRoleAndLabel(dbPool, role_id, p_lable_id);
      
      if (existing) {
        existing.is_permission = is_permission;
        existing.updated = new Date();
        await existing.save();
      } else {
        await permissionRepository.createAccess(dbPool, {
          role_id,
          p_lable_id,
          is_permission,
          created: new Date()
        });
      }
    }
  }
}

module.exports = new PermissionService();
