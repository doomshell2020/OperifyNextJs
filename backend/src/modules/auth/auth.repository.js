const { centralPool, getTenantPool } = require('../../config/db');

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
    const query = 'SELECT * FROM users WHERE mobile = ? LIMIT 1';
    const [rows] = await centralPool.execute(query, [mobile]);
    return rows.length > 0 ? rows[0] : null;
  }

  /**
   * Finds a user record in a tenant database by mobile number.
   * 
   * @param {string} mobile 
   * @param {string} dbName 
   * @returns {Promise<Object|null>} User record or null
   */
  async findTenantUserByMobile(mobile, dbName) {
    const pool = await getTenantPool(dbName);
    const query = 'SELECT * FROM users WHERE mobile = ? LIMIT 1';
    const [rows] = await pool.execute(query, [mobile]);
    return rows.length > 0 ? rows[0] : null;
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
    const pool = await getTenantPool(dbName);
    const query = 'UPDATE users SET password = ? WHERE id = ?';
    await pool.execute(query, [passwordHash, userId]);
  }
}

module.exports = new AuthRepository();
