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

    if (tenantUser.is_status === 'N') {
      throw this._createAuthError('Your account is inactive. Please contact the administrator.');
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

    return this._issueTokens(await this.getSession(mobile, tenantDbName, centralUser, tenantUser));
  }

  /**
   * Keep identity and permissions in the authenticated user's home database.
   * The selected database controls data routing only; it never impersonates
   * the first administrator in another company (as PHP erpLogin did).
   */
  async getSession(mobile, selectedDb, centralUser, homeUser) {
    centralUser = centralUser || await authRepository.findCentralUserByMobile(mobile);
    if (!centralUser || centralUser.is_status === 'N' || !centralUser.db) {
      throw this._createAuthError('Your account is unavailable. Please log in again.');
    }
    homeUser = homeUser || await authRepository.findTenantUserByMobile(mobile, centralUser.db);
    if (!homeUser || homeUser.is_status === 'N') {
      throw this._createAuthError('Your account is inactive or unavailable.');
    }

    // The authenticated tenant role is authoritative; central c_id supplies
    // the existing parent/franchise relationship used for access checks.
    const companies = await authRepository.getAssignedCompanies({
      ...centralUser, role_id: homeUser.role_id
    });
    const db = selectedDb || centralUser.db;
    if (typeof db !== 'string' || !/^[a-zA-Z0-9_]+$/.test(db)) {
      throw this._createAuthError('Invalid company database');
    }
    if (db !== centralUser.db && !companies.some(company => company.school_database === db)) {
      const error = this._createAuthError('You do not have permission to access this company');
      error.status = 403;
      error.code = 'FORBIDDEN';
      throw error;
    }
    const permissions = await authRepository.getUserPermissions(homeUser.role_id);
    return {
      id: homeUser.id,
      user_name: homeUser.user_name || homeUser.email,
      email: homeUser.email,
      mobile: centralUser.mobile,
      db,
      home_db: centralUser.db,
      role_id: Number(homeUser.role_id),
      tech_id: homeUser.tech_id || null,
      c_id: homeUser.c_id || null,
      board: homeUser.board || null,
      companies,
      permissions
    };
  }

  _issueTokens(user) {
    const { companies, ...claims } = user;
    return {
      user,
      accessToken: jwt.sign(claims, process.env.JWT_SECRET || 'super_secret_key', { expiresIn: '2h' }),
      refreshToken: jwt.sign(
        { id: user.id, db: user.db, mobile: user.mobile, home_db: user.home_db },
        process.env.JWT_REFRESH_SECRET || 'super_secret_refresh_key',
        { expiresIn: '7d' }
      )
    };
  }

  async refreshAccessToken(refreshToken) {
    let decoded;
    try {
      decoded = jwt.verify(refreshToken, process.env.JWT_REFRESH_SECRET || 'super_secret_refresh_key');
    } catch {
      throw this._createAuthError('Invalid or expired refresh token');
    }
    const user = await this.getSession(decoded.mobile || '', decoded.db);
    const { companies, ...claims } = user;
    return {
      accessToken: jwt.sign(claims, process.env.JWT_SECRET || 'super_secret_key', { expiresIn: '2h' }),
      user
    };
  }

  async switchCompany(mobile, currentDb, newDb) {
    if (typeof newDb !== 'string' || !newDb) {
      const error = new Error('Select a company database');
      error.status = 400;
      error.code = 'INVALID_COMPANY';
      throw error;
    }
    const user = await this.getSession(mobile, newDb);
    // Do not commit a company switch when the registry references a database
    // that is unavailable; the browser can safely keep its current session.
    try {
      const { getTenantSequelize } = require('../../config/sequelize');
      await (await getTenantSequelize(user.db)).authenticate();
    } catch {
      const error = new Error('This company database is unavailable. Please contact the administrator.');
      error.status = 503;
      error.code = 'COMPANY_UNAVAILABLE';
      throw error;
    }
    return this._issueTokens(user);
  }


  _createAuthError(message) {
    const error = new Error(message);
    error.status = 401;
    error.code = 'UNAUTHORIZED';
    return error;
  }
}

module.exports = new AuthService();
