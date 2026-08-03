# Database Documentation (operify & tirupati_tppl)

This document provides complete details of all MySQL tables, schemas, relationships, indexes, and business logic purposes for both databases in the system:
1. **Central Database (`operify`)**: Manages multi-tenant configuration, schools (tenants/branches), user credentials, global lookup data, and central authorization templates.
2. **Tenant Database (`tirupati_tppl`)**: Holds branch-specific transactional ERP data including inventory, purchase orders, BOMs, sales, and employee registers.

## 1. Central Database Schema (`operify`)

Total tables: 31

### Table: `academic_year`

**Business Purpose**: Stores global academic/financial years for tenant reference.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | - | - |
| `academicyear` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `cities`

**Business Purpose**: Global lookup table for cities, associated with states.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `c_id` | `int` | No | `5` | - | - |
| `name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `demo_request`

**Business Purpose**: Logs demo and trial requests submitted from the landing page.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `employees`

**Business Purpose**: Stores administrator and support staff records at the central platform level.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `fname` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `ip_ranges`

**Business Purpose**: Spam prevention tool that stores whitelisted/blacklisted IP addresses for logins.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `start_ip` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `machine_master`

**Business Purpose**: Master registry of machines across the platform.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `machine_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `maintenance`

**Business Purpose**: Logs platform-level machine maintenance records.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `machine_id` | `int` | Yes | `NULL` | - | - |
| `breakdown_type` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `managesettings`

**Business Purpose**: General platform administration configurations.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `drop_out_student` | `enum` | Yes | `NULL` | - | - |

---

### Table: `payments`

**Business Purpose**: Global platform transaction and payments logging.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `vendor_id` | `int` | Yes | `NULL` | - | - |
| `store_type` | `int` | Yes | `1` | - | - |
| `inwarddate` | `date` | Yes | `NULL` | - | - |
| `bill_no` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `permission_access`

**Business Purpose**: Stores module-level access control permissions mapped to different roles.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `role_id` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `permission_label`

**Business Purpose**: Stores human-readable labels for permission modules for UI mapping.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `manager_id` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `permission_manager`

**Business Purpose**: Reference logs of administrator-level permissions.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `permission_module`

**Business Purpose**: Central definition of controllers and actions requiring authorization.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `user_id` | `int` | Yes | `NULL` | - | - |
| `module` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `permissions`

**Business Purpose**: Global authorization mappings of roles to specific permissions.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `module_id` | `int` | No | `NULL` | - | - |
| `role_id` | `int` | No | `NULL` | - | - |
| `permission` | `enum` | Yes | `NULL` | - | - |

---

### Table: `roles`

**Business Purpose**: Global roles (SuperAdmin, CenterHead, StoreStaff, etc.).

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `schools`

**Business Purpose**: Central tenant/branch directory. Represents the company branches with their database name, logo, and active status.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `school_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `seos`

**Business Purpose**: Configures meta tags, titles, and SEO options for marketing pages.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `orgid` | `int` | No | `NULL` | - | - |
| `page` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `sitesettings`

**Business Purpose**: Central configurations for platform settings.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `first_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `sitesettings_details`

**Business Purpose**: Branch-specific details, logos, signatures, and template configurations.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `sitesettings_id` | `int` | Yes | `NULL` | - | - |
| `logo` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_additem`

**Business Purpose**: Central catalog of raw materials and products.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `category_id` | `int` | Yes | `NULL` | - | - |
| `uom` | `int` | Yes | `NULL` | - | - |
| `item_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_categorymaster`

**Business Purpose**: Central item category hierarchy catalog.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `category_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_companymaster`

**Business Purpose**: Centralized legal entity details.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `cname` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_inspection_report`

**Business Purpose**: Global inspection templates and quality check definitions.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_purchasereturn`

**Business Purpose**: Centralized purchase return configs.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `retrundate` | `date` | Yes | `NULL` | - | - |
| `vendor_id` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_taxmaster`

**Business Purpose**: Centralized tax config (CGST, SGST, IGST).

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `parent` | `int` | Yes | `0` | - | - |
| `tax_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `states`

**Business Purpose**: Global state lookup directory.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `c_id` | `int` | No | `NULL` | - | - |
| `name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `store_details`

**Business Purpose**: Central branch store location mappings.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `template`

**Business Purpose**: Defines email, SMS, and page templates used by the system.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `type_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `transporter`

**Business Purpose**: Central directory of transport and shipping vendors.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `transport_id` | `int` | Yes | `NULL` | - | - |
| `transport_to` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `users`

**Business Purpose**: Core platform user logins containing hashed password, email, mobile number, role, and current active database name.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `c_id` | `int` | No | `NULL` | - | - |
| `user_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `vendors`

**Business Purpose**: Central directory of registered suppliers and vendors.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `name` | `varchar` | Yes | `NULL` | - | - |

---

## 2. Tenant ERP Database Schema (`tirupati_tppl`)

Total tables: 93

### Table: `academic_year`

**Business Purpose**: Stores academic and financial years specific to the tenant/branch.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `academicyear` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `board`

**Business Purpose**: Organizational board or brand entity mapping (legacy educational configuration).

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `boards`

**Business Purpose**: Education board lookup table (legacy configuration).

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `bom`

**Business Purpose**: Bills of Materials header. Links a finished product to its manufacturing recipe.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `contract_id` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `bom_finisedproduct`

**Business Purpose**: Details of the finished product being manufactured under a BOM.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `contract_id` | `int` | No | `NULL` | - | - |
| `product_id` | `int` | No | `NULL` | - | - |
| `price` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `bom_rawmaterial`

**Business Purpose**: Raw material requirements, quantities, and wastage coefficients for a BOM.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `bom_id` | `int` | No | `NULL` | - | - |
| `product_id` | `int` | No | `NULL` | - | - |
| `price` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `cities`

**Business Purpose**: Cities lookup for the tenant.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `c_id` | `int` | No | `5` | - | - |
| `name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `contracts`

**Business Purpose**: Stores active procurement contracts, pricing agreements, and contract terms with suppliers.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `supplier_id` | `int` | Yes | `0` | - | - |
| `title` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `copper_stock`

**Business Purpose**: Specialized inventory ledger to track raw copper stocks, weights, and grade sizes.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `product_id` | `int` | Yes | `NULL` | - | - |
| `type` | `text` | Yes | `NULL` | - | - |
| `tppl` | `text` | Yes | `NULL` | - | - |
| `kcpl` | `text` | Yes | `NULL` | - | - |
| `created_at` | `datetime` | Yes | `CURRENT_TIMESTAMP` | - | - |

---

### Table: `country`

**Business Purpose**: Country lookup table.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `departments`

**Business Purpose**: Staff departments (Production, Quality, Store, HR, etc.) for attendance and permissions.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `designations`

**Business Purpose**: Staff job titles (Store Manager, QA Inspector, Extrusion Operator, etc.).

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `designsheet`

**Business Purpose**: Production design sheet headers containing item size, weight, and customer spec references.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `designsheetno` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `designsheetdetails`

**Business Purpose**: Detailed dimensions, material list, insulation thickness, and specs for design sheets.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `designsheet_id` | `int` | No | `NULL` | - | - |
| `designsheetno` | `int` | No | `NULL` | - | - |
| `item_id` | `int` | Yes | `NULL` | - | - |
| `km_item_qty` | `float` | Yes | `NULL` | - | - |
| `item_qty` | `float` | Yes | `NULL` | - | - |
| `uom` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `emd_amount`

**Business Purpose**: Earnest Money Deposit (EMD) financial records for contracts/tenders.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `bank_guarantee_id` | `int` | Yes | `NULL` | - | - |
| `recive_amount` | `decimal` | Yes | `NULL` | - | - |

---

### Table: `emd_guarantees`

**Business Purpose**: Bank guarantee details associated with Earnest Money Deposits.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `bg_for` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `emd_remarks`

**Business Purpose**: Follow-up remarks and history logs for Earnest Money Deposits.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `bank_guarantee_id` | `int` | Yes | `NULL` | - | - |
| `remark` | `text` | Yes | `NULL` | - | - |
| `remarked_by` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `employees`

**Business Purpose**: Staff records containing salary, contact details, status, and related login credentials.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `fname` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `finishedproduct_process`

**Business Purpose**: Sequence of manufacturing process stages required for a finished product.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | - | - |
| `process_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `grn_inspection`

**Business Purpose**: Goods Received Note quality inspection headers.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `po_id` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `grn_inspection_details`

**Business Purpose**: Detailed checks, parameters, values, and status (Accepted/Rejected) of GRN items.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `purchaseorder_id` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `indentpo`

**Business Purpose**: Links indent requests to final Purchase Orders for audit trails.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `indent_id` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `machine_master`

**Business Purpose**: List of factory machines, speeds, and operation parameters.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `machine_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `maintenance`

**Business Purpose**: Logs machine maintenance events, alarms, readings, and downtime.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `machine_id` | `int` | Yes | `NULL` | - | - |
| `breakdown_type` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `officers_name`

**Business Purpose**: Directory of vendor officers and client contacts.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `particular_pay_receive`

**Business Purpose**: Ledger of detailed cash/bank payment voucher transactions.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `particular_id` | `int` | No | `NULL` | - | - |
| `recive_amount` | `decimal` | Yes | `NULL` | - | - |

---

### Table: `particular_payments`

**Business Purpose**: Voucher payments allocation details.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `particular` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `payments`

**Business Purpose**: Main payments ledger linking purchase bills to bank vouchers.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `vendor_id` | `int` | Yes | `NULL` | - | - |
| `store_type` | `int` | Yes | `1` | - | - |
| `inwarddate` | `date` | Yes | `NULL` | - | - |
| `bill_no` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `permission_module`

**Business Purpose**: Tenant-specific custom permission modules.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `user_id` | `int` | Yes | `NULL` | - | - |
| `module` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `permissions`

**Business Purpose**: Authorization mapping of tenant roles to modules.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `module` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `po_delivery_note`

**Business Purpose**: Tracks shipping dates, delivery schedules, and partial delivery notes against POs.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `po_id` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `process`

**Business Purpose**: List of manufacturing operations (Extrusion, Annealing, Taping, Braiding).

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `process_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `product`

**Business Purpose**: Product directory (legacy configuration).

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `parent_id` | `int` | Yes | `NULL` | - | - |
| `code` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `production`

**Business Purpose**: Main production header tracking date, shift, supervisor, and output weight.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `po_id` | `int` | Yes | `NULL` | - | - |
| `machine_id` | `int` | Yes | `NULL` | - | - |
| `item_id` | `int` | Yes | `NULL` | - | - |
| `manpower_day` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `production_sheet`

**Business Purpose**: Daily production scheduling sheets.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `datefrom` | `date` | Yes | `NULL` | - | - |
| `machines_id` | `int` | Yes | `NULL` | - | - |
| `totalconsumption` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `productiondetails`

**Business Purpose**: Details of consumed raw materials and output weights per production batch.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `production_id` | `int` | No | `NULL` | - | - |
| `materialid` | `int` | No | `NULL` | - | - |
| `material_desginqty` | `int` | Yes | `NULL` | - | - |
| `material_issuedqty` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `productionorder`

**Business Purpose**: Production orders issued to the factory floor specifying targets and BOM configurations.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `po_id` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `productionsheet_item`

**Business Purpose**: Logs of machine-wise production totals.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `productionseet_id` | `int` | Yes | `NULL` | - | - |
| `contractid` | `int` | Yes | `NULL` | - | - |
| `process_id` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `purchase`

**Business Purpose**: Legacy purchase transaction logs.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `vendor_id` | `int` | No | `NULL` | - | - |
| `invoice_no` | `int` | No | `NULL` | - | - |
| `invoice_date` | `date` | No | `NULL` | - | - |
| `goods_received_date` | `date` | No | `NULL` | - | - |
| `remarks` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `purchase_details`

**Business Purpose**: Legacy purchase items table.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `purchase_id` | `int` | No | `NULL` | - | - |
| `product_id` | `int` | No | `NULL` | - | - |
| `attribute_id` | `int` | No | `NULL` | - | - |
| `value` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `report_template`

**Business Purpose**: Configuration options for building PDF/Excel reports.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `reverseindent`

**Business Purpose**: Reverse indent header representing stock return from franchise/branch to store/HO.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `reverse_id` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `roles`

**Business Purpose**: Tenant roles mapping.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `salesreturn`

**Business Purpose**: Stores headers for customer sales return transactions.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `branch_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `salesreturndetails`

**Business Purpose**: List of items, prices, and taxes in customer sales returns.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `salereturn_id` | `int` | Yes | `NULL` | - | - |
| `item_id` | `int` | Yes | `NULL` | - | - |
| `category_id` | `int` | Yes | `NULL` | - | - |
| `item_amount` | `float` | Yes | `NULL` | - | - |
| `created` | `timestamp` | Yes | `CURRENT_TIMESTAMP` | - | - |
| `status` | `enum` | Yes | `NULL` | - | - |

---

### Table: `sitesettings`

**Business Purpose**: Tenant configuration profile.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `first_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `sitesettings_details`

**Business Purpose**: Tenant-specific email credentials, logos, headers, and signatures.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `sitesettings_id` | `int` | Yes | `NULL` | - | - |
| `logo` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `solditems`

**Business Purpose**: Store sales billing header (records invoice number, customer details, totals, and date).

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `branch_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `solditemsdetail`

**Business Purpose**: Store sales billing detail (records items sold, quantities, item price, and GST).

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `sold_id` | `int` | Yes | `NULL` | - | - |
| `item_id` | `int` | Yes | `NULL` | - | - |
| `category_id` | `int` | Yes | `NULL` | - | - |
| `item_amount` | `float` | Yes | `NULL` | - | - |
| `created` | `timestamp` | Yes | `NULL` | - | - |
| `status` | `enum` | Yes | `NULL` | - | - |

---

### Table: `st_additem`

**Business Purpose**: Tenant master catalog of inventory items, codes, HSN numbers, and units.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `category_id` | `int` | Yes | `NULL` | - | - |
| `uom` | `int` | Yes | `NULL` | - | - |
| `item_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_cancel_stock_register`

**Business Purpose**: Tracks inventory rollback records when transactions are cancelled.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `po_id` | `int` | No | `NULL` | - | it |
| `purchaseorder_id` | `int` | Yes | `NULL` | - | purchase order primary key |
| `goods_id` | `int` | Yes | `NULL` | - | goods received primary id |
| `indent_id` | `int` | Yes | `NULL` | - | - |
| `item_id` | `int` | No | `NULL` | - | - |
| `created` | `datetime` | No | `CURRENT_TIMESTAMP` | - | - |
| `issue_date` | `datetime` | Yes | `NULL` | - | - |
| `delivery_date` | `datetime` | Yes | `NULL` | - | - |
| `quantity` | `int` | No | `NULL` | - | - |
| `rate` | `double` | Yes | `NULL` | - | - |

---

### Table: `st_categorymaster`

**Business Purpose**: Item category master catalog.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `category_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_categorywise`

**Business Purpose**: Categorized inventory lookup utility.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `item_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_companymaster`

**Business Purpose**: Profile details of the tenant legal entity (GSTIN, PAN, Bank Details).

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `cname` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_goodsreceive`

**Business Purpose**: Goods Received Note (GRN) headers (records challan no, vendor, date, gate pass, totals).

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `purchaseorder_id` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_indentmaster`

**Business Purpose**: Procurement Indent headers (requisitions made by the store or production departments).

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `forward_to` | `int` | Yes | `NULL` | - | - |
| `approved_date` | `date` | Yes | `NULL` | - | - |
| `remark` | `text` | Yes | `NULL` | - | - |
| `cancel_by` | `int` | Yes | `NULL` | - | - |
| `cancel_date` | `date` | Yes | `NULL` | - | - |
| `po_id` | `int` | Yes | `NULL` | - | - |
| `po_status` | `enum` | Yes | `NULL` | - | - |

---

### Table: `st_indentmaster_temp`

**Business Purpose**: Temporary table holding draft indent items during multi-page form creation.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `indent_id` | `int` | No | `NULL` | - | - |
| `item_id` | `int` | No | `NULL` | - | - |
| `size_id` | `int` | Yes | `NULL` | - | - |
| `sale_price` | `double` | Yes | `NULL` | - | - |

---

### Table: `st_indentpreview`

**Business Purpose**: Saves pre-calculated indent rates and specs before submission.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `forward_to` | `int` | Yes | `NULL` | - | - |
| `approved_date` | `date` | Yes | `NULL` | - | - |
| `remark` | `text` | Yes | `NULL` | - | - |
| `cancel_by` | `int` | Yes | `NULL` | - | - |
| `cancel_date` | `date` | Yes | `NULL` | - | - |
| `po_id` | `int` | Yes | `NULL` | - | - |
| `po_status` | `enum` | Yes | `NULL` | - | - |

---

### Table: `st_inspection_report`

**Business Purpose**: Stores inspection report numbers and overall audit ratings.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_itemlocation`

**Business Purpose**: Store racks, sub-stores, and physical locations.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `location_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_measurementunits`

**Business Purpose**: Standard units of measurement (UoM) (Kg, Meter, Set, Piece).

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `unit_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_mrn`

**Business Purpose**: Material Receipt Note (MRN) headers issued when branch requests are fulfilled.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `mrn_date` | `datetime` | Yes | `NULL` | - | - |
| `bill_challan_no` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_paymentterms`

**Business Purpose**: Predefined payment terms (COD, Net 30, Advance).

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `description` | `text` | No | `NULL` | - | - |
| `status` | `enum` | Yes | `NULL` | - | - |

---

### Table: `st_planned_type`

**Business Purpose**: Defines categories of planned production or planned maintenance.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | - | - |
| `name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_purchaseorder`

**Business Purpose**: Purchase Order (PO) headers (stores PO Number, supplier, contract link, grand totals).

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `purchaseorder_id` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_purchaseorderDetails`

**Business Purpose**: PO line items (price, unit, CGST, SGST, IGST, final amounts).

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `purchaseorder_id` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_purchaseorder_temp`

**Business Purpose**: Temporary table holding draft PO items during PO generation.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `purchaseorder_id` | `int` | No | `NULL` | - | - |
| `item_id` | `int` | No | `NULL` | - | - |
| `item_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_purchasereturn`

**Business Purpose**: Vendor purchase return headers.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `retrundate` | `date` | Yes | `NULL` | - | - |
| `vendor_id` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_purchasereturn_details`

**Business Purpose**: Items returned to suppliers with reference to GRN/PO.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `purchasereturn_id` | `int` | Yes | `NULL` | - | - |
| `vendor_id` | `int` | Yes | `NULL` | - | - |
| `item_id` | `int` | Yes | `NULL` | - | - |
| `item_amt` | `int` | Yes | `NULL` | - | - |
| `item_qty` | `int` | Yes | `NULL` | - | - |
| `item_price` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_quotations`

**Business Purpose**: RFQ/Request for Quotation headers.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `quotation_id` | `int` | No | `NULL` | - | - |
| `vendor_id` | `int` | Yes | `NULL` | - | - |
| `selected_bid_id` | `int` | Yes | `NULL` | - | - |
| `is_award` | `enum` | Yes | `NULL` | - | - |

---

### Table: `st_quotations_details`

**Business Purpose**: Line items and parameters for RFQs.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `quotation_id` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_received_quotations`

**Business Purpose**: Supplier bid submissions (records supplier rates, discounts, and validity).

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `quotation_id` | `int` | Yes | `NULL` | - | - |
| `vendor_id` | `int` | Yes | `NULL` | - | - |
| `quotation_date` | `date` | Yes | `NULL` | - | - |
| `delivery_date` | `date` | Yes | `NULL` | - | - |
| `remark` | `text` | Yes | `NULL` | - | - |
| `total_qty` | `float` | Yes | `NULL` | - | - |
| `total_tax` | `double` | Yes | `NULL` | - | - |

---

### Table: `st_received_quotations_details`

**Business Purpose**: Line-item bids received from different suppliers.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `quotation_id` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_send_quotations`

**Business Purpose**: Tracks history of quotation requests emailed to suppliers.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `quotation_id` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_sizemanager`

**Business Purpose**: Size master catalog for wire and cable gauges.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `size_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_stock_available`

**Business Purpose**: Real-time inventory lookup (tracks item balances, reserved stock, and physical stock).

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `item_id` | `int` | Yes | `NULL` | - | - |
| `stock_available` | `int` | Yes | `NULL` | - | - |
| `sold_stock` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_stock_cancel_sales_return`

**Business Purpose**: Stock audit trail for cancelled sales returns.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `po_id` | `int` | No | `NULL` | - | it |
| `purchaseorder_id` | `int` | Yes | `NULL` | - | purchase order primary key |
| `goods_id` | `int` | Yes | `NULL` | - | goods received primary id |
| `indent_id` | `int` | Yes | `NULL` | - | - |
| `item_id` | `int` | No | `NULL` | - | - |
| `created` | `datetime` | No | `CURRENT_TIMESTAMP` | - | - |
| `issue_date` | `datetime` | Yes | `NULL` | - | - |
| `delivery_date` | `datetime` | Yes | `NULL` | - | - |
| `quantity` | `int` | No | `NULL` | - | - |
| `rate` | `double` | Yes | `NULL` | - | - |

---

### Table: `st_stock_register`

**Business Purpose**: Primary inventory ledger. Tracks every transaction (grn, issue, adjustment, balance) per item.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `po_id` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_store_location_permission`

**Business Purpose**: Permission mapping linking store staff to allowed store locations/racks.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `location_id` | `int` | No | `NULL` | - | - |
| `staff_id` | `int` | No | `NULL` | - | - |
| `date_added` | `datetime` | No | `CURRENT_TIMESTAMP` | - | - |
| `status` | `enum` | Yes | `NULL` | - | - |

---

### Table: `st_supplier`

**Business Purpose**: Approved supplier directories.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `supplier_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_taxmaster`

**Business Purpose**: Tax master storing active GST brackets (CGST, SGST, IGST combinations).

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `parent` | `int` | Yes | `0` | - | - |
| `tax_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_vendorbillto`

**Business Purpose**: Vendor billing addresses.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `vendor_id` | `int` | No | `NULL` | - | - |
| `state_id` | `int` | Yes | `NULL` | - | - |
| `city_id` | `int` | Yes | `NULL` | - | - |
| `gst_number` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `st_vendorshipfrom`

**Business Purpose**: Vendor warehouse/dispatch locations.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `vendor_id` | `int` | No | `NULL` | - | - |
| `state_id` | `int` | Yes | `NULL` | - | - |
| `city_id` | `int` | Yes | `NULL` | - | - |
| `gst_number` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `states`

**Business Purpose**: State lookup registry.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `c_id` | `int` | No | `NULL` | - | - |
| `name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `temp_purchesreturn`

**Business Purpose**: Temporary scratch table holding draft items during purchase return creation.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `category_id` | `int` | Yes | `NULL` | - | - |
| `item_id` | `int` | Yes | `NULL` | - | - |
| `item_price` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `tempbranchrequest`

**Business Purpose**: Temporary scratch table holding draft items during branch request creation.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `category_id` | `int` | Yes | `NULL` | - | - |
| `item_id` | `int` | Yes | `NULL` | - | - |
| `quantity` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `template_category`

**Business Purpose**: Categories of layouts for printouts (A4, Half-Page, Labels).

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `tempsalereturn`

**Business Purpose**: Temporary scratch table for sales returns.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `category_id` | `int` | Yes | `NULL` | - | - |
| `item_id` | `int` | Yes | `NULL` | - | - |
| `quantity` | `int` | Yes | `NULL` | - | - |
| `created` | `datetime` | Yes | `NULL` | - | - |
| `branch_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `tempsold_items`

**Business Purpose**: Temporary scratch table for point of sale transactions.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `category_id` | `int` | Yes | `NULL` | - | - |
| `item_id` | `int` | Yes | `NULL` | - | - |
| `quantity` | `int` | Yes | `NULL` | - | - |
| `created` | `timestamp` | Yes | `CURRENT_TIMESTAMP` | - | - |
| `category_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `transporter`

**Business Purpose**: Logistics carriers list.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `transport_id` | `int` | Yes | `NULL` | - | - |
| `transport_to` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `users`

**Business Purpose**: User login profiles for the tenant.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `c_id` | `int` | No | `NULL` | - | - |
| `user_name` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `users_device`

**Business Purpose**: FCM tokens and device IDs for push notifications.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `device_id` | `varchar` | Yes | `NULL` | - | - |

---

### Table: `vendors`

**Business Purpose**: Supplier directory for the tenant.

| Column | Data Type | Nullable | Default | Keys/Extra | Description |
| --- | --- | --- | --- | --- | --- |
| `id` | `int` | No | `NULL` | AUTO_INC | - |
| `name` | `varchar` | Yes | `NULL` | - | - |

---

