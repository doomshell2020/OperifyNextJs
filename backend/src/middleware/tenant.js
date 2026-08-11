const { getTenantSequelize, getTenantModels } = require('../config/sequelize');

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
    const sequelize = await getTenantSequelize(dbName);
    const models = await getTenantModels(dbName);
    
    // Keeping variable name req.dbPool for compatibility
    req.dbPool = sequelize;
    req.models = models;
    req.dbName = dbName;
    
    next();
  } catch (error) {
    next(error);
  }
}

module.exports = tenantMiddleware;
