# Complete Module List

This document lists every module discovered in the legacy CakePHP project, mapped to its business scope, target routes, and associated database tables.

---

### 1. Authentication & Session Module
*   **Legacy Components**: `LoginsController.php`, `AppController::erpLogin()`, `AppController::connection()`
*   **Business Scope**: Handles user logins (via mobile/password or email/password), session initialization, dynamic switching of database connections based on tenant mapping, password retrieval, and forgot/reset password email links.
*   **Tables**: `users`, `schools`, `roles`, `users_device`

### 2. Role & Permission Module
*   **Legacy Components**: `PermissionController.php`, `PermissionmodulesController.php`, `RolesController.php`
*   **Business Scope**: Manages user roles, auto-registers new actions and controllers into the system registry, maps roles to module permissions, and filters sidebar navigation options dynamically.
*   **Tables**: `roles`, `permission_module`, `permissions`, `permission_access`, `permission_label`

### 3. Dashboard Module
*   **Legacy Components**: `DashboardsController.php`
*   **Business Scope**: Central portal displaying real-time branch performance metrics, sales numbers, production totals, stock levels, and interactive graphs.
*   **Tables**: `st_stock_available`, `st_purchaseorder`, `production`, `solditems`

### 4. Geography Masters Module
*   **Legacy Components**: `CountryController.php`, `StatesController.php`, `CitiesController.php`, `LocationsController.php`
*   **Business Scope**: Manages countries, states, cities directories, and transport route codes for local logistics mapping.
*   **Tables**: `country`, `states`, `cities`, `locations`

### 5. Item Masters Module
*   **Legacy Components**: `ItemcategoryController.php`, `ItemnameController.php`, `AdditemController.php`, `CategorywiseController.php`
*   **Business Scope**: Defines raw materials and product details, structural gauges/sizes, categories, sub-categories, physical racks/shelves, and units of measurement.
*   **Tables**: `st_additem`, `st_categorymaster`, `st_categorywise`, `st_sizemanager`, `st_measurementunits`

### 6. Vendor & Supplier Masters Module
*   **Legacy Components**: `VendorsController.php`, `SuppliersController.php`, `TransporterController.php`
*   **Business Scope**: Manages approved vendor directories, shipping/billing locations, payment terms, and cargo transporters profiles.
*   **Tables**: `vendors`, `st_vendorshipfrom`, `st_vendorbillto`, `st_supplier`, `transporter`, `st_paymentterms`

### 7. Contract Management Module
*   **Legacy Components**: `ContractsController.php`
*   **Business Scope**: Registers procurement contracts with suppliers, contract terms, pricing parameters, and tracks total expenditures against contracts.
*   **Tables**: `contracts`, `bom`, `designsheet`

### 8. Design Sheet & BOM Module
*   **Legacy Components**: `DesignsheetController.php`, `ProductionController::getbom()`
*   **Business Scope**: Formulates raw material requirements (copper, insulation, packaging) and wastage coefficients needed to manufacture specific product gauges.
*   **Tables**: `designsheet`, `designsheetdetails`, `bom`, `bom_finisedproduct`, `bom_rawmaterial`

### 9. Indent Module
*   **Legacy Components**: `IndentController.php`
*   **Business Scope**: Initiates internal procurement requisitions. Manages draft tables (`st_indentmaster_temp`) and approved indents.
*   **Tables**: `st_indentmaster`, `st_indentmaster_temp`, `st_indentpreview`

### 10. Quotation & RFQ Module
*   **Legacy Components**: `QuotationController.php`
*   **Business Scope**: Sends RFQ details to multiple suppliers. Logs vendor bids, rates, and tax details to highlight active bid candidates.
*   **Tables**: `st_quotations`, `st_quotations_details`, `st_send_quotations`, `st_received_quotations`, `st_received_quotations_details`

### 11. Purchase Order (PO) Module
*   **Legacy Components**: `PurchaseorderController.php`
*   **Business Scope**: Handles PO generations, revisions (`revised`, `revisedV1`), tax additions, delivery schedules, and downloads.
*   **Tables**: `st_purchaseorder`, `st_purchaseorderDetails`, `st_purchaseorder_temp`, `po_delivery_note`

### 12. Goods Received Note (GRN) Module
*   **Legacy Components**: `GoodsreceivedController.php`
*   **Business Scope**: Logs warehouse receipts against POs, challan details, gate pass numbers, and vendor invoices.
*   **Tables**: `st_goodsreceive`, `st_stock_register`

### 13. Quality QC & Inspection Module
*   **Legacy Components**: `InspectionController.php`, `GoodsreceivedController::grninspection()`
*   **Business Scope**: Runs quality checks on raw materials. Verifies product specs, log accepted/rejected quantities, and saves conditional holds.
*   **Tables**: `grn_inspection`, `grn_inspection_details`, `st_inspection_report`

### 14. Stock Register & Warehouse Module
*   **Legacy Components**: `StockregisterController.php`, `StoreitemsController.php`, `StorelocationpermissionController.php`
*   **Business Scope**: Primary stock ledger tracking additions and issues. Maps store locations and warehouse rack permissions for staff.
*   **Tables**: `st_stock_register`, `st_stock_available`, `st_itemlocation`, `st_store_location_permission`

### 15. Branch Requests & MRN Module
*   **Legacy Components**: `BranchitemrequestController.php`
*   **Business Scope**: Branches request materials from the head office. Fulfillments generate Material Receipt Notes (MRN) and deduct head office stocks.
*   **Tables**: `tempbranchrequest`, `st_mrn`, `st_stock_register`

### 16. POS Sales & Billing Module
*   **Legacy Components**: `SalesController.php`, `SolditemsController.php`
*   **Business Scope**: Renders point-of-sale invoicing screens, records retail customer transactions, generates invoices, and manages daily cash.
*   **Tables**: `solditems`, `solditemsdetail`, `tempsold_items`

### 17. Returns Module
*   **Legacy Components**: `SalereturnController.php`, `PurchasereturnController.php`, `StudentpurchasereturnController.php`
*   **Business Scope**: Logs customer sales returns and vendor purchase returns with reference to original invoices/GRNs.
*   **Tables**: `salesreturn`, `salesreturndetails`, `st_purchasereturn`, `st_purchasereturn_details`, `temp_purchesreturn`, `tempsalereturn`

### 18. Production Floor Module
*   **Legacy Components**: `ProductionController.php`
*   **Business Scope**: Records daily machine production output, raw material consumption details, shift details, scrap logs, and process completions.
*   **Tables**: `production`, `productionorder`, `productiondetails`, `production_sheet`, `productionsheet_item`, `finishedproduct_process`

### 19. Job Challan Module
*   **Legacy Components**: `JobchallanController.php`
*   **Business Scope**: Outsources manufacturing processes to subcontractors. Tracks raw material dispatches and processed item returns.
*   **Tables**: `job_challans` (not in tenant sql but used in controllers, wait, in sql it is `job_challans`, let's verify if there is `job_challans` in schema, wait, it is in `controller_actions.txt` as `JobChallanItemsTable` and `JobChallanReceivesTable` and `JobChallansTable`)

### 20. Maintenance Module
*   **Legacy Components**: `MaintenanceController.php`, `MachineController.php`
*   **Business Scope**: Registers preventive and breakdown maintenance schedules, machine operating hours, repair remarks, and downtime details.
*   **Tables**: `maintenance`, `machine_master`

### 21. Earnest Money Deposit (EMD) Module
*   **Legacy Components**: `EmdController.php`
*   **Business Scope**: Tracks earnest money deposits (EMD) submitted for government contracts. Monitors bank guarantees and refunds.
*   **Tables**: `emd_amount`, `emd_guarantees`, `emd_remarks`

### 22. Payment Manager Module
*   **Legacy Components**: `PaymentmanagerController.php`, `PaymentsController.php`
*   **Business Scope**: Manages accounts payable/receivable records, links vendor invoices to bank payments, and tracks payment terms.
*   **Tables**: `payments`, `particular_payments`, `particular_pay_receive`

### 23. Employee & Payroll Module
*   **Legacy Components**: `EmployeesController.php`
*   **Business Scope**: Manages staff profiles, daily attendance logs, payroll configurations, and onboarding documents.
*   **Tables**: `employees`, `departments`, `designations`, `employeeattendance`

### 24. Data Import Module
*   **Legacy Components**: `DatarecordController.php`
*   **Business Scope**: Excel parser allowing bulk import of masters (employees, vendors, items, category structures) with dry-run validations.
*   **Tables**: Reference target tables per import category.

### 25. Reports Module
*   **Legacy Components**: `ReportController.php`, `ReportNewController.php`
*   **Business Scope**: 320+ pre-built query templates for inventory checks, financial ledgers, salary lists, and legacy modules.
*   **Tables**: Mapped across all tables depending on the selected report layout.

### 26. Mobile API Compatibility Module
*   **Legacy Components**: `MobileController.php`, `Api/MobileController.php`
*   **Business Scope**: API routes serving the mobile client. Provides endpoints for checking orders, stock levels, and scanning barcodes.
*   **Tables**: `users_device`, `users`, `contracts`, `st_goodsreceive`, `st_purchaseorder`

### 27. Public website & Utilities Module
*   **Legacy Components**: `HomesController.php`, `CronController.php`, `SeoController.php`, `TemplateController.php`, `SpamController.php`
*   **Business Scope**: Renders landing page marketing templates, logs contact form inquiries, manages email layouts, and processes automated database backup crons.
*   **Tables**: `demo_request`, `seos`, `template`, `ip_ranges`
