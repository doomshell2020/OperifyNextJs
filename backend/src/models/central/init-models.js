var DataTypes = require("sequelize").DataTypes;
var _academic_year = require("./academic_year");
var _cities = require("./cities");
var _demo_request = require("./demo_request");
var _employees = require("./employees");
var _ip_ranges = require("./ip_ranges");
var _machine_master = require("./machine_master");
var _maintenance = require("./maintenance");
var _managesettings = require("./managesettings");
var _payments = require("./payments");
var _permission_access = require("./permission_access");
var _permission_label = require("./permission_label");
var _permission_manager = require("./permission_manager");
var _permission_module = require("./permission_module");
var _permissions = require("./permissions");
var _roles = require("./roles");
var _schools = require("./schools");
var _seos = require("./seos");
var _sitesettings = require("./sitesettings");
var _sitesettings_details = require("./sitesettings_details");
var _st_additem = require("./st_additem");
var _st_categorymaster = require("./st_categorymaster");
var _st_companymaster = require("./st_companymaster");
var _st_inspection_report = require("./st_inspection_report");
var _st_purchasereturn = require("./st_purchasereturn");
var _st_taxmaster = require("./st_taxmaster");
var _states = require("./states");
var _store_details = require("./store_details");
var _template = require("./template");
var _transporter = require("./transporter");
var _users = require("./users");
var _vendors = require("./vendors");

function initModels(sequelize) {
  var academic_year = _academic_year(sequelize, DataTypes);
  var cities = _cities(sequelize, DataTypes);
  var demo_request = _demo_request(sequelize, DataTypes);
  var employees = _employees(sequelize, DataTypes);
  var ip_ranges = _ip_ranges(sequelize, DataTypes);
  var machine_master = _machine_master(sequelize, DataTypes);
  var maintenance = _maintenance(sequelize, DataTypes);
  var managesettings = _managesettings(sequelize, DataTypes);
  var payments = _payments(sequelize, DataTypes);
  var permission_access = _permission_access(sequelize, DataTypes);
  var permission_label = _permission_label(sequelize, DataTypes);
  var permission_manager = _permission_manager(sequelize, DataTypes);
  var permission_module = _permission_module(sequelize, DataTypes);
  var permissions = _permissions(sequelize, DataTypes);
  var roles = _roles(sequelize, DataTypes);
  var schools = _schools(sequelize, DataTypes);
  var seos = _seos(sequelize, DataTypes);
  var sitesettings = _sitesettings(sequelize, DataTypes);
  var sitesettings_details = _sitesettings_details(sequelize, DataTypes);
  var st_additem = _st_additem(sequelize, DataTypes);
  var st_categorymaster = _st_categorymaster(sequelize, DataTypes);
  var st_companymaster = _st_companymaster(sequelize, DataTypes);
  var st_inspection_report = _st_inspection_report(sequelize, DataTypes);
  var st_purchasereturn = _st_purchasereturn(sequelize, DataTypes);
  var st_taxmaster = _st_taxmaster(sequelize, DataTypes);
  var states = _states(sequelize, DataTypes);
  var store_details = _store_details(sequelize, DataTypes);
  var template = _template(sequelize, DataTypes);
  var transporter = _transporter(sequelize, DataTypes);
  var users = _users(sequelize, DataTypes);
  var vendors = _vendors(sequelize, DataTypes);


  return {
    academic_year,
    cities,
    demo_request,
    employees,
    ip_ranges,
    machine_master,
    maintenance,
    managesettings,
    payments,
    permission_access,
    permission_label,
    permission_manager,
    permission_module,
    permissions,
    roles,
    schools,
    seos,
    sitesettings,
    sitesettings_details,
    st_additem,
    st_categorymaster,
    st_companymaster,
    st_inspection_report,
    st_purchasereturn,
    st_taxmaster,
    states,
    store_details,
    template,
    transporter,
    users,
    vendors,
  };
}
module.exports = initModels;
module.exports.initModels = initModels;
module.exports.default = initModels;
