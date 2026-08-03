const { getTenantPool } = require('../config/db');

/**
 * Tenant switching middleware.
 * Attaches the correct tenant connection pool to req.dbPool
 * based on the user's authenticated session (JWT claim) or central routing logic.
 */
async function tenantMiddleware(req, res, next) {
  try {
    let dbName = null;
    
    // If user is authenticated, derive tenant DB from JWT claims
    if (req.user && req.user.db) {
      dbName = req.user.db;
    }
    
    // Attach the connection pool to the request context
    const pool = await getTenantPool(dbName);
    req.dbPool = pool;
    
    next();
  } catch (error) {
    next(error);
  }
}

module.exports = tenantMiddleware;
