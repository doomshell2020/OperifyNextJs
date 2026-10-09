/**
 * Permission verification middleware
 */

/**
 * Ensures the authenticated user has the specified permission.
 * Assumes auth.js middleware has already run and attached req.user
 * 
 * @param {string} requiredPermission 
 */
exports.requirePermission = (requiredPermission) => {
  return (req, res, next) => {
    // Check if user object and permissions array exist
    if (!req.user || !Array.isArray(req.user.permissions)) {
      return res.status(403).json({
        success: false,
        error: {
          code: 'FORBIDDEN',
          message: 'Access Denied: Permission context missing.'
        }
      });
    }

    const userPermissions = req.user.permissions;

    // Check if user has the specific permission
    if (!userPermissions.includes(requiredPermission)) {
      return res.status(403).json({
        success: false,
        error: {
          code: 'FORBIDDEN',
          message: `Access Denied: You do not have the required permission (${requiredPermission}).`
        }
      });
    }

    next();
  };
};

exports.requireAnyPermission = (requiredPermissions) => {
  return (req, res, next) => {
    if (!req.user || !Array.isArray(req.user.permissions)) {
      return res.status(403).json({
        success: false,
        error: {
          code: 'FORBIDDEN',
          message: 'Access Denied: Permission context missing.'
        }
      });
    }

    const userPermissions = req.user.permissions;
    const hasAnyPermission = requiredPermissions.some((permission) => userPermissions.includes(permission));

    if (!hasAnyPermission) {
      return res.status(403).json({
        success: false,
        error: {
          code: 'FORBIDDEN',
          message: `Access Denied: You do not have the required permission (${requiredPermissions.join(' or ')}).`
        }
      });
    }

    next();
  };
};
