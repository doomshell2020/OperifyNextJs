const authService = require('./auth.service');

class AuthController {
  /**
   * Processes login request.
   */
  async login(req, res, next) {
    try {
      const { mobile, password } = req.body;
      const result = await authService.login(mobile, password);
      
      // Set secure cookie for refresh token if needed, or simply send back in payload
      res.cookie('refreshToken', result.refreshToken, {
        httpOnly: true,
        secure: process.env.NODE_ENV === 'production',
        sameSite: 'strict',
        maxAge: 7 * 24 * 60 * 60 * 1000 // 7 days
      });

      return res.status(200).json({
        success: true,
        message: 'Login successful',
        data: {
          user: result.user,
          accessToken: result.accessToken,
          refreshToken: result.refreshToken
        }
      });
    } catch (error) {
      next(error);
    }
  }

  /**
   * Renews access token using refresh token.
   */
  async refresh(req, res, next) {
    try {
      const { refreshToken } = req.body;
      const result = await authService.refreshAccessToken(refreshToken);
      return res.status(200).json({
        success: true,
        message: 'Access token refreshed successfully',
        data: {
          accessToken: result.accessToken
        }
      });
    } catch (error) {
      next(error);
    }
  }

  /**
   * Retrieves profile details of the logged in user.
   */
  async me(req, res, next) {
    try {
      // req.user was attached by the authenticate middleware
      // We also need to fetch assigned companies to preserve them on page refresh
      const authRepository = require('./auth.repository');
      const tenantUser = await authRepository.findTenantUserByMobile(req.user.mobile || '', req.user.db);
      let companies = [];
      if (tenantUser) {
        companies = await authRepository.getAssignedCompanies(tenantUser);
      }

      return res.status(200).json({
        success: true,
        message: 'User profile retrieved successfully',
        data: {
          user: {
            ...req.user,
            companies
          }
        }
      });
    } catch (error) {
      next(error);
    }
  }

  /**
   * Switch the active company.
   */
  async switchCompany(req, res, next) {
    try {
      const { newDb } = req.body;
      const currentDb = req.user.db;
      const mobile = req.user.mobile;
      
      const result = await authService.switchCompany(mobile, currentDb, newDb);

      res.cookie('refreshToken', result.refreshToken, {
        httpOnly: true,
        secure: process.env.NODE_ENV === 'production',
        sameSite: 'strict',
        maxAge: 7 * 24 * 60 * 60 * 1000 // 7 days
      });

      return res.status(200).json({
        success: true,
        message: 'Company switched successfully',
        data: {
          user: result.user,
          accessToken: result.accessToken,
          refreshToken: result.refreshToken
        }
      });
    } catch (error) {
      next(error);
    }
  }

  /**
   * Processes logout.
   */
  async logout(req, res, next) {
    try {
      res.clearCookie('refreshToken');
      return res.status(200).json({
        success: true,
        message: 'Logged out successfully'
      });
    } catch (error) {
      next(error);
    }
  }
}

module.exports = new AuthController();
