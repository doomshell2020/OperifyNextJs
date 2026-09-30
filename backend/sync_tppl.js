const { getTenantSequelize, getTenantModels } = require('./src/config/sequelize');

async function syncTirupatiTppl() {
  try {
    const dbName = 'tirupati_tppl';
    console.log(`Syncing models for database: ${dbName}`);
    
    // This instantiates the connection to tirupati_tppl and loads models
    const sequelize = await getTenantSequelize(dbName);
    const models = await getTenantModels(dbName);

    // Sync only the new tables
    await models.job_challans.sync({ alter: true });
    console.log('job_challans synced');
    
    await models.job_challan_items.sync({ alter: true });
    console.log('job_challan_items synced');
    
    await models.job_challan_receives.sync({ alter: true });
    console.log('job_challan_receives synced');
    
    console.log(`Successfully synced Job Challan module for tenant: ${dbName}`);
    process.exit(0);
  } catch (error) {
    console.error('Error syncing:', error);
    process.exit(1);
  }
}

syncTirupatiTppl();
