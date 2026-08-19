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
      { id: tenantUser.id, db: tenantDbName, mobile: centralUser.mobile },
      process.env.JWT_REFRESH_SECRET || 'super_secret_refresh_key',
      { expiresIn: '7d' }
    );

    // Get assigned companies
    const assignedCompanies = await authRepository.getAssignedCompanies(centralUser);

    return {
      user: {
        id: tenantUser.id,
        user_name: tenantUser.user_name,
        email: tenantUser.email,
        mobile: tenantUser.mobile,
        role_id: tenantUser.role_id,
        db: tenantDbName,
        companies: assignedCompanies
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
      
      const centralUser = await authRepository.findCentralUserByMobile(decoded.mobile || '');
      if (!centralUser) {
        throw new Error('User not found');
      }

      const tokenPayload = {
        id: centralUser.id,
        user_name: centralUser.user_name,
        email: centralUser.email,
        mobile: centralUser.mobile,
        db: decoded.db, // Use the DB from the decoded refresh token
        role_id: centralUser.role_id,
        tech_id: centralUser.tech_id || null,
        c_id: centralUser.c_id || null,
        board: centralUser.board || null
      };

      const newAccessToken = jwt.sign(
        tokenPayload,
        process.env.JWT_SECRET || 'super_secret_key',
        { expiresIn: '2h' }
      );

      // Return both access token and updated user details so frontend can update its context
      const assignedCompanies = await authRepository.getAssignedCompanies(centralUser);

      return { 
        accessToken: newAccessToken,
        user: {
          ...tokenPayload,
          companies: assignedCompanies
        }
      };
    } catch (err) {
      const error = new Error('Invalid or expired refresh token');
      error.status = 401;
      error.code = 'UNAUTHORIZED';
      throw error;
    }
  }

  /**
   * Switch the active company/database.
   */
  async switchCompany(mobile, currentDb, newDb) {
    const centralUser = await authRepository.findCentralUserByMobile(mobile);
    if (!centralUser) {
      throw this._createAuthError('User not found');
    }

    const assignedCompanies = await authRepository.getAssignedCompanies(centralUser);
    const hasAccess = assignedCompanies.some(c => c.school_database === newDb);
    if (!hasAccess) {
      throw this._createAuthError('You do not have permission to access this company');
    }

    // New token payload with the new db
    const tokenPayload = {
      id: centralUser.id,
      user_name: centralUser.user_name || centralUser.email,
      email: centralUser.email,
      mobile: centralUser.mobile,
      db: newDb,
      role_id: centralUser.role_id,
      tech_id: centralUser.tech_id || null,
      c_id: centralUser.c_id || null,
      board: centralUser.board || null
    };

    const accessToken = jwt.sign(
      tokenPayload,
      process.env.JWT_SECRET || 'super_secret_key',
      { expiresIn: '2h' }
    );

    const refreshToken = jwt.sign(
      { id: centralUser.id, db: newDb, mobile: centralUser.mobile }, // ensure mobile is here for refresh
      process.env.JWT_REFRESH_SECRET || 'super_secret_refresh_key',
      { expiresIn: '7d' }
    );

    return {
      user: {
        ...tokenPayload,
        companies: assignedCompanies
      },
      accessToken,
      refreshToken
    };
  }


  _createAuthError(message) {
    const error = new Error(message);
    error.status = 401;
    error.code = 'UNAUTHORIZED';
    return error;
  }
}

module.exports = new AuthService();
