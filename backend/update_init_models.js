const fs = require('fs');
const path = require('path');

const initModelsPath = path.join(__dirname, 'src', 'models', 'tenant', 'init-models.js');
let content = fs.readFileSync(initModelsPath, 'utf8');

// Insert requires
if (!content.includes('var _job_challans = require("./jobChallan");')) {
  content = content.replace(
    'function initModels(sequelize) {',
    `var _job_challans = require("./jobChallan");
var _job_challan_items = require("./jobChallanItem");
var _job_challan_receives = require("./jobChallanReceive");

function initModels(sequelize) {`
  );
  
  // Insert initializations
  content = content.replace(
    'return {',
    `  var job_challans = _job_challans(sequelize, DataTypes);
  var job_challan_items = _job_challan_items(sequelize, DataTypes);
  var job_challan_receives = _job_challan_receives(sequelize, DataTypes);

  job_challans.belongsTo(vendors, { as: "vendor", foreignKey: "sub_contractors_id"});
  vendors.hasMany(job_challans, { as: "job_challans", foreignKey: "sub_contractors_id"});

  job_challan_items.belongsTo(job_challans, { as: "job_challan", foreignKey: "challan_id"});
  job_challans.hasMany(job_challan_items, { as: "job_challan_items", foreignKey: "challan_id"});

  job_challan_receives.belongsTo(job_challans, { as: "job_challan", foreignKey: "challan_id"});
  job_challans.hasMany(job_challan_receives, { as: "job_challan_receives", foreignKey: "challan_id"});

  job_challan_items.belongsTo(st_additem, { as: "item", foreignKey: "item_id"});
  st_additem.hasMany(job_challan_items, { as: "job_challan_items", foreignKey: "item_id"});

  return {`
  );

  content = content.replace(
    'return {',
    `return {
    job_challans,
    job_challan_items,
    job_challan_receives,`
  );
  
  fs.writeFileSync(initModelsPath, content);
  console.log('init-models.js updated');
} else {
  console.log('Already updated');
}
