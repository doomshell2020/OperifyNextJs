const mysql = require('mysql2/promise');
require('dotenv').config();

// Default central database connection configuration
const dbConfig = {
  host: process.env.DB_HOST || 'localhost',
  user: process.env.DB_USER || 'root',
  password: process.env.DB_PASSWORD || '',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0
};

// Central DB pool pointing to 'operify' (or process.env.DB_NAME)
const centralPool = mysql.createPool({
  ...dbConfig,
  database: process.env.DB_NAME || 'operify'
});

// Cache map to store instantiated tenant connection pools
const tenantPools = {};

/**
 * Returns a connection pool for a specific tenant database.
 * If the database name matches the central database name, or is empty,
 * it returns the central pool.
 * Otherwise, it dynamically creates a pool, caches it, and returns it.
 * 
 * @param {string} dbName - Tenant database name
 * @returns {Promise<mysql.Pool>} mysql2 Pool instance
 */
async function getTenantPool(dbName) {
  const centralDbName = process.env.DB_NAME || 'operify';
  
  if (!dbName || dbName === centralDbName) {
    return centralPool;
  }
  
  if (tenantPools[dbName]) {
    return tenantPools[dbName];
  }
  
  // Initialize dynamic pool for the tenant
  const dynamicPool = mysql.createPool({
    ...dbConfig,
    database: dbName
  });
  
  // Cache the pool
  tenantPools[dbName] = dynamicPool;
  
  return dynamicPool;
}

module.exports = {
  centralPool,
  getTenantPool
};
