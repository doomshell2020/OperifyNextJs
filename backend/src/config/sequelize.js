const { Sequelize } = require('sequelize');
require('dotenv').config();

// Default central database connection configuration
const dbConfig = {
  host: process.env.DB_HOST || 'localhost',
  username: process.env.DB_USER || 'root',
  password: process.env.DB_PASSWORD || '',
  dialect: 'mysql',
  logging: false, // Do not log queries to keep console clean
  pool: {
    max: 10,
    min: 0,
    acquire: 30000,
    idle: 10000
  },
  define: {
    timestamps: false,
    freezeTableName: true
  }
};

const centralDbName = process.env.DB_NAME || 'operify';

const initCentralModels = require('../models/central/init-models');
const initTenantModels = require('../models/tenant/init-models');

// Central DB Sequelize instance
const centralSequelize = new Sequelize(centralDbName, dbConfig.username, dbConfig.password, dbConfig);
const centralModels = initCentralModels(centralSequelize);

// Cache map to store instantiated tenant connection pools
const tenantSequelizes = {};
const tenantModelsCache = {};

/**
 * Returns a Sequelize instance for a specific tenant database.
 * If the database name matches the central database name, or is empty,
 * it returns the central pool.
 * Otherwise, it dynamically creates a pool, caches it, and returns it.
 * 
 * @param {string} dbName - Tenant database name
 * @returns {Promise<Sequelize>} Sequelize instance
 */
async function getTenantSequelize(dbName) {
  if (!dbName || dbName === centralDbName) {
    return centralSequelize;
  }
  
  if (tenantSequelizes[dbName]) {
    return tenantSequelizes[dbName];
  }
  
  // Initialize dynamic Sequelize instance for the tenant
  const dynamicSequelize = new Sequelize(dbName, dbConfig.username, dbConfig.password, dbConfig);
  
  // Cache the instance
  tenantSequelizes[dbName] = dynamicSequelize;
  tenantModelsCache[dbName] = initTenantModels(dynamicSequelize);
  
  return dynamicSequelize;
}

/**
 * Helper to get models for a tenant database
 */
async function getTenantModels(dbName) {
  if (!dbName || dbName === centralDbName) {
    return centralModels;
  }
  if (!tenantModelsCache[dbName]) {
    await getTenantSequelize(dbName);
  }
  return tenantModelsCache[dbName];
}

module.exports = {
  centralSequelize,
  getTenantSequelize,
  centralModels,
  getTenantModels
};
