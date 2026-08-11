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
}

module.exports = new AuthRepository();
