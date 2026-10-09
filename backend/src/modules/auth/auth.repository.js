const { centralModels, getTenantModels } = require('../../config/sequelize');

/**
 * AuthRepository executes database operations for authentication
 */
class AuthRepository {
  /**
   * Finds a user record in the central database by mobile number.
   *
   * @param {string} mobile
   * @returns {Promise<Object|null>} User record or null
   */
  async findCentralUserByMobile(mobile) {
    const user = await centralModels.users.findOne({
      where: { mobile },
      raw: true
    });
    return user || null;
  }

  /**
   * Finds a user record in a tenant database by mobile number.
   *
   * @param {string} mobile
   * @param {string} dbName
   * @returns {Promise<Object|null>} User record or null
   */
  async findTenantUserByMobile(mobile, dbName) {
    const tenantModels = await getTenantModels(dbName);
    const user = await tenantModels.users.findOne({
      where: { mobile },
      raw: true
    });
    return user || null;
  }

  /**
   * Updates the password hash in the tenant database for a user.
   * Helps migrate plaintext accounts to hashed accounts automatically.
   *
   * @param {number} userId
   * @param {string} passwordHash
   * @param {string} dbName
   */
  async updateTenantUserPasswordHash(userId, passwordHash, dbName) {
    const tenantModels = await getTenantModels(dbName);
    await tenantModels.users.update(
      { password: passwordHash },
      { where: { id: userId } }
    );
  }

  /**
   * Legacy getfranchise() reads central users grouped by db, not schools.
   * Keep the existing active-school access boundary, then intersect it with
   * that registry list. A school alone must not create a dropdown option.
   */
  async getAssignedCompanies(user) {
    const { Op } = require('sequelize');
    const role = Number(user.role_id);
    const cid = user.c_id || 0;

    let schools;
    if (role === 101) {
      // SuperAdmin: all companies
      schools = await centralModels.schools.findAll({
        where: { status: 'Y' },
        attributes: ['id', 'school_name', 'school_database'],
        raw: true
      });
    } else if (role === 105) {
      // ErpHead: parent and all franchises
      schools = await centralModels.schools.findAll({
        where: {
          status: 'Y',
          [Op.or]: [
            { id: cid },
            { franchise_type: String(cid) }
          ]
        },
        attributes: ['id', 'school_name', 'school_database'],
        raw: true
      });
    } else {
      // Normal user: just their own company
      schools = await centralModels.schools.findAll({
        where: {
          status: 'Y',
          school_database: user.db
        },
        attributes: ['id', 'school_name', 'school_database'],
        raw: true
      });
    }

    const { fn, col } = require('sequelize');
    const prefix = String(user.db || '').split('_')[0];
    if (!prefix) return [];
    const registry = await centralModels.users.findAll({
      attributes: [[fn('MIN', col('id')), 'id'], 'db'],
      where: role === 105
        ? { db: { [Op.like]: `${prefix.replace(/[\\%_]/g, '\\$&')}%` } }
        : role === 101 ? {} : { db: user.db },
      group: ['db'],
      order: [[fn('MIN', col('id')), 'ASC']],
      raw: true
    });
    const allowed = new Map(schools.map(school => [school.school_database, school]));
    return registry.filter(row => allowed.has(row.db)).map(row => ({
      ...allowed.get(row.db),
      // headernew.ctp uses ucfirst(explode('_', db)[1]).
      school_name: row.db.includes('_')
        ? row.db.split('_')[1].replace(/^./, character => character.toUpperCase())
        : allowed.get(row.db).school_name
    }));
  }

  /**
   * Retrieves permissions for a user based on their role_id.
   * Joins permission_access and permission_label to get allowed URLs.
   */
  async getUserPermissions(roleId) {
    if (!roleId) return [];

    try {
      // Find all permission_access entries for this role_id where is_permission = 1
      const accesses = await centralModels.permission_access.findAll({
        where: {
          role_id: String(roleId),
          is_permission: '1'
        },
        raw: true
      });

      if (!accesses || accesses.length === 0) return [];

      const labelIds = accesses.map(acc => acc.p_lable_id);

      // Find the corresponding URLs from permission_label
      const labels = await centralModels.permission_label.findAll({
        where: {
          id: labelIds
        },
        attributes: ['url'],
        raw: true
      });

      // Map CakePHP URLs to our standard permission keys
      const permissions = [];

      labels.forEach(label => {
        const url = label.url ? label.url.toLowerCase().trim() : '';
        if (!url) return;

        // Basic mapping logic
        if (url === 'admin/contracts/index') permissions.push('contracts:view');
        if (url === 'admin/contracts/edit') permissions.push('contracts:edit');
        if (url === 'admin/contracts/delete') permissions.push('contracts:delete');
        if (url === 'admin/contracts/add') permissions.push('contracts:add');

        if (url === 'admin/purchaseorder/index') permissions.push('purchaseorder:view');
        if (url === 'admin/purchaseorder/add') permissions.push('purchaseorder:add');
        if (url === 'admin/purchaseorder/revised') permissions.push('purchaseorder:revise');
        if (url === 'admin/purchaseorder/delete') permissions.push('purchaseorder:delete');
        if (url === 'admin/purchaseorder/deliverynote') permissions.push('purchaseorder:deliverynote');

        if (url === 'admin/designsheet/index') permissions.push('designsheet:view');
        if (url === 'admin/designsheet/add') permissions.push('designsheet:add');
        if (url === 'admin/designsheet/edit') permissions.push('designsheet:edit');
        if (url === 'admin/designsheet/delete') permissions.push('designsheet:delete');
        if (url === 'admin/designsheet/viewdesignsheet') permissions.push('designsheet:viewdetails');

        if (url === 'admin/production/productionorders') permissions.push('production:view');
        if (url === 'admin/stockregister/index') permissions.push('stock:view');
        if (url === 'admin/jobchallan/index') permissions.push('jobchallan:view');
        if (url === 'admin/goodsreceived/index') permissions.push('grn:view');

        // Add the raw url as well, to be safe during migration
        permissions.push(`legacy:${url}`);
      });

      return [...new Set(permissions)]; // Return unique permissions
    } catch (err) {
      console.error('Error fetching user permissions:', err);
      return [];
    }
  }
}

module.exports = new AuthRepository();
