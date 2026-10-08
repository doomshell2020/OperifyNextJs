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
   * Retrieves assigned companies for a user based on role and c_id.
   */
  async getAssignedCompanies(user) {
    const { Op } = require('sequelize');
    const role = user.role_id;
    const cid = user.c_id || 0;

    if (role === 101) {
      // SuperAdmin: all companies
      return await centralModels.schools.findAll({
        where: { status: 'Y' },
        attributes: ['id', 'school_name', 'school_database'],
        raw: true
      });
    } else if (role === 105) {
      // ErpHead: parent and all franchises
      return await centralModels.schools.findAll({
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
      return await centralModels.schools.findAll({
        where: {
          status: 'Y',
          school_database: user.db
        },
        attributes: ['id', 'school_name', 'school_database'],
        raw: true
      });
    }
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
        if (url.includes('contracts/index')) permissions.push('contracts:view');
        if (url.includes('contracts/edit')) permissions.push('contracts:edit');
        if (url.includes('contracts/delete')) permissions.push('contracts:delete');
        if (url.includes('contracts/add')) permissions.push('contracts:add');

        if (url.includes('purchaseorder/index')) permissions.push('purchaseorder:view');
        if (url.includes('purchaseorder/add')) permissions.push('purchaseorder:add');
        if (url.includes('purchaseorder/viewpodetailspdf')) permissions.push('purchaseorder:pdf');
        if (url === 'admin/purchaseorder/view') permissions.push('purchaseorder:pdf');
        if (url.includes('purchaseorder/printallpo')) permissions.push('purchaseorder:pdf');
        if (url.includes('purchaseorder/revised')) permissions.push('purchaseorder:revise');
        if (url.includes('purchaseorder/delete')) permissions.push('purchaseorder:delete');
        if (url.includes('purchaseorder/deliverynote')) permissions.push('purchaseorder:deliverynote');

        if (url.includes('designsheet/index')) permissions.push('designsheet:view');
        if (url.includes('designsheet/add')) permissions.push('designsheet:add');
        if (url.includes('designsheet/edit')) permissions.push('designsheet:edit');
        if (url.includes('designsheet/delete')) permissions.push('designsheet:delete');
        if (url.includes('designsheet/viewdesignsheet')) permissions.push('designsheet:viewdetails');

        if (url.includes('production/index')) permissions.push('production:view');
        if (url.includes('stockregister/index')) permissions.push('stock:view');
        if (url.includes('jobchallan/index')) permissions.push('jobchallan:view');
        if (url.includes('goodsreceived/index')) permissions.push('grn:view');

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
