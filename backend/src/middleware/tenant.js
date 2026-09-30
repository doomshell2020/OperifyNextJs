const { getTenantSequelize, getTenantModels } = require('../config/sequelize');

/**
 * Tenant switching middleware.
 * Attaches the correct tenant connection pool to req.dbPool
 * based on the user's authenticated session (JWT claim) or central routing logic.
 */
async function tenantMiddleware(req, res, next) {
  try {
    let dbName = null;
    
    if (req.user && req.user.db) {
      dbName = req.user.db;
    }

    // Guard against obviously invalid DB names
    if (dbName && !/^[a-zA-Z0-9_]+$/.test(dbName)) {
      return res.status(400).json({ success: false, error: { code: 'INVALID_DB', message: 'Invalid database name in session. Please log in again.' } });
    }
    
    const sequelize = await getTenantSequelize(dbName);
    const models    = await getTenantModels(dbName);
    
    req.dbPool = sequelize;
    req.models = models;
    req.dbName = dbName;
    
    next();
  } catch (error) {
    if (error.code === 'ER_BAD_DB_ERROR') {
      return res.status(401).json({ success: false, error: { code: 'SESSION_EXPIRED', message: 'Your session database is invalid. Please log out and log in again.' } });
    }
    next(error);
  }
}

module.exports = tenantMiddleware;
