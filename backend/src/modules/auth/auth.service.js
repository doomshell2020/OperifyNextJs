const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const authRepository = require('./auth.repository');

class AuthService {
  /**
   * Authenticates user and returns tokens.
   * 
   * @param {string} mobile 
   * @param {string} password 
   * @returns {Promise<Object>} Authenticated user and tokens
   */
  async login(mobile, password) {
    // 1. Locate user database mapping in the central registry
    const centralUser = await authRepository.findCentralUserByMobile(mobile);
    if (!centralUser) {
      throw this._createAuthError('Invalid mobile number or password');
    }

    const tenantDbName = centralUser.db;
    if (!tenantDbName) {
      throw this._createAuthError('User database mapping not found');
    }

    // 2. Fetch user profile from their designated tenant database
    const tenantUser = await authRepository.findTenantUserByMobile(mobile, tenantDbName);
    if (!tenantUser) {
      throw this._createAuthError('Invalid mobile number or password');
    }

    // 3. Password Verification (Bcrypt Hash with Plaintext fallback)
    let isPasswordCorrect = false;
    let shouldUpgradeHash = false;

    // Check hashed password (if it exists)
    if (tenantUser.password) {
      // PHP bcrypt starts with $2y$, Node bcrypt handles this as $2a$
      const hash = tenantUser.password.replace(/^\$2y\$/, '$2a$');
      isPasswordCorrect = await bcrypt.compare(password, hash);
    }

    // Fallback: Check plaintext confirm_pass (legacy compatibility)
    if (!isPasswordCorrect && tenantUser.confirm_pass) {
      isPasswordCorrect = (password === tenantUser.confirm_pass);
      if (isPasswordCorrect) {
        // Plaintext matches, mark for password hash upgrade
        shouldUpgradeHash = true;
      }
    }

    if (!isPasswordCorrect) {
      throw this._createAuthError('Invalid mobile number or password');
    }

    // 4. Secure Hash Auto-Upgrade
    if (shouldUpgradeHash) {
      try {
        const salt = await bcrypt.genSalt(10);
        const newHash = await bcrypt.hash(password, salt);
        await authRepository.updateTenantUserPasswordHash(tenantUser.id, newHash, tenantDbName);
      } catch (err) {
        console.error('Failed to upgrade legacy password hash:', err);
      }
    }

    // 5. Token Generation (Access & Refresh)
    const tokenPayload = {
      id: tenantUser.id,
      user_name: tenantUser.user_name || tenantUser.email,
      email: tenantUser.email,
      mobile: tenantUser.mobile,
      db: tenantDbName,
      role_id: tenantUser.role_id,
      tech_id: tenantUser.tech_id || null,
      c_id: tenantUser.c_id || null,
      board: tenantUser.board || null
    };

    const accessToken = jwt.sign(
      tokenPayload,
      process.env.JWT_SECRET || 'super_secret_key',
      { expiresIn: '2h' }
    );

    const refreshToken = jwt.sign(
      { id: tenantUser.id, db: tenantDbName },
      process.env.JWT_REFRESH_SECRET || 'super_secret_refresh_key',
      { expiresIn: '7d' }
    );

    return {
      user: {
        id: tenantUser.id,
        user_name: tenantUser.user_name,
        email: tenantUser.email,
        mobile: tenantUser.mobile,
        role_id: tenantUser.role_id,
        db: tenantDbName
      },
      accessToken,
      refreshToken
    };
  }

  /**
   * Refreshes JWT access token using a valid refresh token.
   * 
   * @param {string} refreshToken 
   * @returns {Promise<Object>} New access token
   */
  async refreshAccessToken(refreshToken) {
    try {
      const decoded = jwt.verify(
        refreshToken,
        process.env.JWT_REFRESH_SECRET || 'super_secret_refresh_key'
      );
      
      const tenantUser = await authRepository.findTenantUserByMobile(decoded.mobile || '', decoded.db);
      if (!tenantUser) {
        throw new Error('User not found');
      }

      const tokenPayload = {
        id: tenantUser.id,
        user_name: tenantUser.user_name,
        email: tenantUser.email,
        mobile: tenantUser.mobile,
        db: decoded.db,
        role_id: tenantUser.role_id,
        tech_id: tenantUser.tech_id || null,
        c_id: tenantUser.c_id || null,
        board: tenantUser.board || null
      };

      const newAccessToken = jwt.sign(
        tokenPayload,
        process.env.JWT_SECRET || 'super_secret_key',
        { expiresIn: '2h' }
      );

      return { accessToken: newAccessToken };
    } catch (err) {
      const error = new Error('Invalid or expired refresh token');
      error.status = 401;
      error.code = 'UNAUTHORIZED';
      throw error;
    }
  }

  _createAuthError(message) {
    const error = new Error(message);
    error.status = 401;
    error.code = 'UNAUTHORIZED';
    return error;
  }
}

module.exports = new AuthService();
