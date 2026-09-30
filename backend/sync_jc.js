const { centralSequelize } = require('./src/config/sequelize');
const { DataTypes } = require('sequelize');

async function syncTables() {
  try {
    const models = require('./src/models/tenant/init-models')(centralSequelize, DataTypes);
    
    await models.job_challans.sync({ alter: true });
    console.log('job_challans synced');
    
    await models.job_challan_items.sync({ alter: true });
    console.log('job_challan_items synced');

    await models.job_challan_receives.sync({ alter: true });
    console.log('job_challan_receives synced');

    process.exit(0);
  } catch (err) {
    console.error(err);
    process.exit(1);
  }
}

syncTables();
