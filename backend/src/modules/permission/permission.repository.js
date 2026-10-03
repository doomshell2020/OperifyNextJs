class PermissionRepository {
  async getManagers(dbPool) {
    return await dbPool.models.permission_manager.findAll({
      order: [['name', 'ASC']],
      raw: true
    });
  }

  async getManagerByName(dbPool, name) {
    return await dbPool.models.permission_manager.findOne({
      where: { name },
      raw: true
    });
  }

  async createManager(dbPool, data) {
    return await dbPool.models.permission_manager.create(data);
  }

  async getLabelByUrlAndManager(dbPool, manager_id, url) {
    return await dbPool.models.permission_label.findOne({
      where: { manager_id, url },
      raw: true
    });
  }

  async createLabel(dbPool, data) {
    return await dbPool.models.permission_label.create(data);
  }

  async getRoles(dbPool) {
    const { Op } = require('sequelize');
    return await dbPool.models.roles.findAll({
      where: {
        status: 'Y',
        id: { [Op.ne]: 101 }
      },
      order: [['name', 'ASC']],
      raw: true
    });
  }

  async getLabelsWithManagers(dbPool) {
    const labels = await dbPool.models.permission_label.findAll({
      raw: true
    });
    const managers = await this.getManagers(dbPool);
    const managerMap = {};
    managers.forEach(m => managerMap[m.id] = m.name);

    return labels.map(l => ({
      ...l,
      manager_name: managerMap[l.manager_id] || 'Unknown'
    }));
  }

  async getAccessByRole(dbPool, role_id) {
    return await dbPool.models.permission_access.findAll({
      where: { role_id },
      raw: true
    });
  }

  async getAccessByRoleAndLabel(dbPool, role_id, p_lable_id) {
    return await dbPool.models.permission_access.findOne({
      where: { role_id, p_lable_id }
    });
  }

  async createAccess(dbPool, data) {
    return await dbPool.models.permission_access.create(data);
  }
}

module.exports = new PermissionRepository();
