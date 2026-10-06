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

    // SuperAdmin role bypass (if you want superadmins to bypass permissions)
    if (req.user.role_id == '101') {
      return next(); 
    }

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

    if (req.user.role_id == '101') {
      return next();
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
