var DataTypes = require("sequelize").DataTypes;
var _academic_year = require("./academic_year");
var _board = require("./board");
var _boards = require("./boards");
var _bom = require("./bom");
var _bom_finisedproduct = require("./bom_finisedproduct");
var _bom_rawmaterial = require("./bom_rawmaterial");
var _cities = require("./cities");
var _contracts = require("./contracts");
var _copper_stock = require("./copper_stock");
var _country = require("./country");
var _departments = require("./departments");
var _designations = require("./designations");
var _designsheet = require("./designsheet");
var _designsheetdetails = require("./designsheetdetails");
var _emd_amount = require("./emd_amount");
var _emd_guarantees = require("./emd_guarantees");
var _emd_remarks = require("./emd_remarks");
var _employees = require("./employees");
var _finishedproduct_process = require("./finishedproduct_process");
var _grn_inspection = require("./grn_inspection");
var _grn_inspection_details = require("./grn_inspection_details");
var _indentpo = require("./indentpo");
var _machine_master = require("./machine_master");
var _maintenance = require("./maintenance");
var _officers_name = require("./officers_name");
var _particular_pay_receive = require("./particular_pay_receive");
var _particular_payments = require("./particular_payments");
var _payments = require("./payments");
var _permission_module = require("./permission_module");
var _permissions = require("./permissions");
var _po_delivery_note = require("./po_delivery_note");
var _process = require("./process");
var _product = require("./product");
var _production = require("./production");
var _production_sheet = require("./production_sheet");
var _productiondetails = require("./productiondetails");
var _productionorder = require("./productionorder");
var _productionsheet_item = require("./productionsheet_item");
var _purchase = require("./purchase");
var _purchase_details = require("./purchase_details");
var _report_template = require("./report_template");
var _reverseindent = require("./reverseindent");
var _roles = require("./roles");
var _salesreturn = require("./salesreturn");
var _salesreturndetails = require("./salesreturndetails");
var _sitesettings = require("./sitesettings");
var _sitesettings_details = require("./sitesettings_details");
var _solditems = require("./solditems");
var _solditemsdetail = require("./solditemsdetail");
var _st_additem = require("./st_additem");
var _st_cancel_stock_register = require("./st_cancel_stock_register");
var _st_categorymaster = require("./st_categorymaster");
var _st_categorywise = require("./st_categorywise");
var _st_companymaster = require("./st_companymaster");
var _st_goodsreceive = require("./st_goodsreceive");
var _st_indentmaster = require("./st_indentmaster");
var _st_indentmaster_temp = require("./st_indentmaster_temp");
var _st_indentpreview = require("./st_indentpreview");
var _st_inspection_report = require("./st_inspection_report");
var _st_itemlocation = require("./st_itemlocation");
var _st_measurementunits = require("./st_measurementunits");
var _st_mrn = require("./st_mrn");
var _st_paymentterms = require("./st_paymentterms");
var _st_planned_type = require("./st_planned_type");
var _st_purchaseorder = require("./st_purchaseorder");
var _st_purchaseorder_temp = require("./st_purchaseorder_temp");
var _st_purchaseorderdetails = require("./st_purchaseorderdetails");
var _st_purchasereturn = require("./st_purchasereturn");
var _st_purchasereturn_details = require("./st_purchasereturn_details");
var _st_quotations = require("./st_quotations");
var _st_quotations_details = require("./st_quotations_details");
var _st_received_quotations = require("./st_received_quotations");
var _st_received_quotations_details = require("./st_received_quotations_details");
var _st_send_quotations = require("./st_send_quotations");
var _st_sizemanager = require("./st_sizemanager");
var _st_stock_available = require("./st_stock_available");
var _st_stock_cancel_sales_return = require("./st_stock_cancel_sales_return");
var _st_stock_register = require("./st_stock_register");
var _st_store_location_permission = require("./st_store_location_permission");
var _st_supplier = require("./st_supplier");
var _st_taxmaster = require("./st_taxmaster");
var _st_vendorbillto = require("./st_vendorbillto");
var _st_vendorshipfrom = require("./st_vendorshipfrom");
var _states = require("./states");
var _temp_purchesreturn = require("./temp_purchesreturn");
var _tempbranchrequest = require("./tempbranchrequest");
var _template_category = require("./template_category");
var _tempsalereturn = require("./tempsalereturn");
var _tempsold_items = require("./tempsold_items");
var _transporter = require("./transporter");
var _users = require("./users");
var _users_device = require("./users_device");
var _vendors = require("./vendors");

function initModels(sequelize) {
  var academic_year = _academic_year(sequelize, DataTypes);
  var board = _board(sequelize, DataTypes);
  var boards = _boards(sequelize, DataTypes);
  var bom = _bom(sequelize, DataTypes);
  var bom_finisedproduct = _bom_finisedproduct(sequelize, DataTypes);
  var bom_rawmaterial = _bom_rawmaterial(sequelize, DataTypes);
  var cities = _cities(sequelize, DataTypes);
  var contracts = _contracts(sequelize, DataTypes);
  var copper_stock = _copper_stock(sequelize, DataTypes);
  var country = _country(sequelize, DataTypes);
  var departments = _departments(sequelize, DataTypes);
  var designations = _designations(sequelize, DataTypes);
  var designsheet = _designsheet(sequelize, DataTypes);
  var designsheetdetails = _designsheetdetails(sequelize, DataTypes);
  var emd_amount = _emd_amount(sequelize, DataTypes);
  var emd_guarantees = _emd_guarantees(sequelize, DataTypes);
  var emd_remarks = _emd_remarks(sequelize, DataTypes);
  var employees = _employees(sequelize, DataTypes);
  var finishedproduct_process = _finishedproduct_process(sequelize, DataTypes);
  var grn_inspection = _grn_inspection(sequelize, DataTypes);
  var grn_inspection_details = _grn_inspection_details(sequelize, DataTypes);
  var indentpo = _indentpo(sequelize, DataTypes);
  var machine_master = _machine_master(sequelize, DataTypes);
  var maintenance = _maintenance(sequelize, DataTypes);
  var officers_name = _officers_name(sequelize, DataTypes);
  var particular_pay_receive = _particular_pay_receive(sequelize, DataTypes);
  var particular_payments = _particular_payments(sequelize, DataTypes);
  var payments = _payments(sequelize, DataTypes);
  var permission_module = _permission_module(sequelize, DataTypes);
  var permissions = _permissions(sequelize, DataTypes);
  var po_delivery_note = _po_delivery_note(sequelize, DataTypes);
  var process = _process(sequelize, DataTypes);
  var product = _product(sequelize, DataTypes);
  var production = _production(sequelize, DataTypes);
  var production_sheet = _production_sheet(sequelize, DataTypes);
  var productiondetails = _productiondetails(sequelize, DataTypes);
  var productionorder = _productionorder(sequelize, DataTypes);
  var productionsheet_item = _productionsheet_item(sequelize, DataTypes);
  var purchase = _purchase(sequelize, DataTypes);
  var purchase_details = _purchase_details(sequelize, DataTypes);
  var report_template = _report_template(sequelize, DataTypes);
  var reverseindent = _reverseindent(sequelize, DataTypes);
  var roles = _roles(sequelize, DataTypes);
  var salesreturn = _salesreturn(sequelize, DataTypes);
  var salesreturndetails = _salesreturndetails(sequelize, DataTypes);
  var sitesettings = _sitesettings(sequelize, DataTypes);
  var sitesettings_details = _sitesettings_details(sequelize, DataTypes);
  var solditems = _solditems(sequelize, DataTypes);
  var solditemsdetail = _solditemsdetail(sequelize, DataTypes);
  var st_additem = _st_additem(sequelize, DataTypes);
  var st_cancel_stock_register = _st_cancel_stock_register(sequelize, DataTypes);
  var st_categorymaster = _st_categorymaster(sequelize, DataTypes);
  var st_categorywise = _st_categorywise(sequelize, DataTypes);
  var st_companymaster = _st_companymaster(sequelize, DataTypes);
  var st_goodsreceive = _st_goodsreceive(sequelize, DataTypes);
  var st_indentmaster = _st_indentmaster(sequelize, DataTypes);
  var st_indentmaster_temp = _st_indentmaster_temp(sequelize, DataTypes);
  var st_indentpreview = _st_indentpreview(sequelize, DataTypes);
  var st_inspection_report = _st_inspection_report(sequelize, DataTypes);
  var st_itemlocation = _st_itemlocation(sequelize, DataTypes);
  var st_measurementunits = _st_measurementunits(sequelize, DataTypes);
  var st_mrn = _st_mrn(sequelize, DataTypes);
  var st_paymentterms = _st_paymentterms(sequelize, DataTypes);
  var st_planned_type = _st_planned_type(sequelize, DataTypes);
  var st_purchaseorder = _st_purchaseorder(sequelize, DataTypes);
  var st_purchaseorder_temp = _st_purchaseorder_temp(sequelize, DataTypes);
  var st_purchaseorderdetails = _st_purchaseorderdetails(sequelize, DataTypes);
  var st_purchasereturn = _st_purchasereturn(sequelize, DataTypes);
  var st_purchasereturn_details = _st_purchasereturn_details(sequelize, DataTypes);
  var st_quotations = _st_quotations(sequelize, DataTypes);
  var st_quotations_details = _st_quotations_details(sequelize, DataTypes);
  var st_received_quotations = _st_received_quotations(sequelize, DataTypes);
  var st_received_quotations_details = _st_received_quotations_details(sequelize, DataTypes);
  var st_send_quotations = _st_send_quotations(sequelize, DataTypes);
  var st_sizemanager = _st_sizemanager(sequelize, DataTypes);
  var st_stock_available = _st_stock_available(sequelize, DataTypes);
  var st_stock_cancel_sales_return = _st_stock_cancel_sales_return(sequelize, DataTypes);
  var st_stock_register = _st_stock_register(sequelize, DataTypes);
  var st_store_location_permission = _st_store_location_permission(sequelize, DataTypes);
  var st_supplier = _st_supplier(sequelize, DataTypes);
  var st_taxmaster = _st_taxmaster(sequelize, DataTypes);
  var st_vendorbillto = _st_vendorbillto(sequelize, DataTypes);
  var st_vendorshipfrom = _st_vendorshipfrom(sequelize, DataTypes);
  var states = _states(sequelize, DataTypes);
  var temp_purchesreturn = _temp_purchesreturn(sequelize, DataTypes);
  var tempbranchrequest = _tempbranchrequest(sequelize, DataTypes);
  var template_category = _template_category(sequelize, DataTypes);
  var tempsalereturn = _tempsalereturn(sequelize, DataTypes);
  var tempsold_items = _tempsold_items(sequelize, DataTypes);
  var transporter = _transporter(sequelize, DataTypes);
  var users = _users(sequelize, DataTypes);
  var users_device = _users_device(sequelize, DataTypes);
  var vendors = _vendors(sequelize, DataTypes);


  return {
    academic_year,
    board,
    boards,
    bom,
    bom_finisedproduct,
    bom_rawmaterial,
    cities,
    contracts,
    copper_stock,
    country,
    departments,
    designations,
    designsheet,
    designsheetdetails,
    emd_amount,
    emd_guarantees,
    emd_remarks,
    employees,
    finishedproduct_process,
    grn_inspection,
    grn_inspection_details,
    indentpo,
    machine_master,
    maintenance,
    officers_name,
    particular_pay_receive,
    particular_payments,
    payments,
    permission_module,
    permissions,
    po_delivery_note,
    process,
    product,
    production,
    production_sheet,
    productiondetails,
    productionorder,
    productionsheet_item,
    purchase,
    purchase_details,
    report_template,
    reverseindent,
    roles,
    salesreturn,
    salesreturndetails,
    sitesettings,
    sitesettings_details,
    solditems,
    solditemsdetail,
    st_additem,
    st_cancel_stock_register,
    st_categorymaster,
    st_categorywise,
    st_companymaster,
    st_goodsreceive,
    st_indentmaster,
    st_indentmaster_temp,
    st_indentpreview,
    st_inspection_report,
    st_itemlocation,
    st_measurementunits,
    st_mrn,
    st_paymentterms,
    st_planned_type,
    st_purchaseorder,
    st_purchaseorder_temp,
    st_purchaseorderdetails,
    st_purchasereturn,
    st_purchasereturn_details,
    st_quotations,
    st_quotations_details,
    st_received_quotations,
    st_received_quotations_details,
    st_send_quotations,
    st_sizemanager,
    st_stock_available,
    st_stock_cancel_sales_return,
    st_stock_register,
    st_store_location_permission,
    st_supplier,
    st_taxmaster,
    st_vendorbillto,
    st_vendorshipfrom,
    states,
    temp_purchesreturn,
    tempbranchrequest,
    template_category,
    tempsalereturn,
    tempsold_items,
    transporter,
    users,
    users_device,
    vendors,
  };
}
module.exports = initModels;
module.exports.initModels = initModels;
module.exports.default = initModels;
