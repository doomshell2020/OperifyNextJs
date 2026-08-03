# Operify CakePHP to Node.js + Next.js Migration Roadmap

Source analyzed:

- ZIP: `D:/MasterBackup/Website/operify.zip`
- Extracted workspace: `D:/operify.in`
- CakePHP version family: CakePHP 3.3
- Current app shape: multi-tenant ERP using dynamic MySQL database switching, CakePHP controllers/templates, Cake ORM table classes, raw SQL, PDF/Excel reports, public/mobile APIs, uploaded documents, and static assets.

Generated inventory:

- `D:/operify.in/outputs/migration_analysis/operify_inventory.json`
- `D:/operify.in/outputs/migration_analysis/operify_inventory.md`

## 1. Codebase Summary

The application is an ERP/admin system with public marketing pages, login, super-admin company/database provisioning, role/permission control, purchase and inventory workflows, production/BOM workflows, contracts, vendors, GRN/inspection, payments/EMD, maintenance, employees, store sales/returns, imports, reports, and mobile API endpoints.

Inventory from code:

| Area | Count |
|---|---:|
| Controllers | 83 |
| CakePHP Table/model files | 190 |
| Templates/views | 638 |
| Inferred modules | 232 |
| Report/PDF/Excel-like templates | 207 |
| Controller files with raw SQL indicators | 55 |
| Mapped database tables | 187 |

Important architecture observations:

- Tenant selection is currently performed by switching CakePHP's default DB connection to a user/company database.
- Most admin screens follow CakePHP MVC: controller action prepares lists/entities, `.ctp` template renders forms/tables, helper methods build repeated UI.
- Many workflows use temporary tables such as `indenttemp`, purchase/order temp tables, and return/temp tables.
- Reports are heavily template-driven and often mix business query logic with presentation.
- Several controllers use raw SQL in addition to Cake ORM.
- PDF/Excel exports are rendered from CakePHP templates and libraries such as TCPDF, mPDF, PHPExcel, and CSVView.
- Authentication and authorization logic is centralized partly in `AppController`, `LoginsController`, `MobileController`, and permission controllers.

## 2. Target Architecture

Create two separate applications:

```text
backend/
  src/
    app.js
    server.js
    config/
    db/
    modules/
    middleware/
    services/
    utils/
    validators/
    reports/
    jobs/
    storage/
  tests/
  package.json

frontend/
  app/
    (public)/
    (auth)/
    (admin)/
    api/
  components/
  features/
  hooks/
  lib/
  services/
  contexts/
  styles/
  public/
  package.json
```

Backend principles:

- Express.js REST API.
- Clean module boundaries: route -> controller -> service -> repository/query layer.
- MySQL database access through a single query layer. Recommended: Knex for parity-friendly SQL migration, or Prisma only after schema normalization is complete.
- Tenant-aware DB middleware. Do not accept raw tenant DB names from clients; derive tenant from authenticated user/company.
- JWT-based API authentication plus refresh/session handling.
- Role/permission middleware based on the existing `permission_module`, `permissions`, `roles`, and `users` logic.
- Central error handler with consistent JSON responses.
- Zod/Joi validation for every request body/query/param.
- Report services separate data gathering from file rendering.

Frontend principles:

- Next.js App Router.
- Server components for static/page shell where useful.
- Client components for forms, data grids, modals, filters, report controls, and uploads.
- Feature folders aligned to backend modules.
- API client layer wraps fetch, auth token handling, tenant context, and errors.
- Reusable admin layout, sidebar, permission-aware navigation, data table, form fields, date pickers, selectors, PDF/Excel download controls.

## 3. Recommended Backend Folder Structure

```text
backend/
  src/
    app.js
    server.js
    config/
      env.js
      constants.js
    db/
      basePool.js
      tenantPool.js
      transaction.js
      repositories/
        baseRepository.js
    middleware/
      authenticate.js
      authorize.js
      tenant.js
      validate.js
      errorHandler.js
      upload.js
      requestContext.js
    modules/
      auth/
        auth.routes.js
        auth.controller.js
        auth.service.js
        auth.repository.js
        auth.validation.js
      companies/
      permissions/
      masters/
        item-categories/
        item-locations/
        item-names/
        items/
        vendors/
        taxes/
        measurement-units/
        machines/
        process/
      purchasing/
        indents/
        purchase-orders/
        quotations/
        goods-received/
        purchase-returns/
      inventory/
        stock-register/
        branch-requests/
        sales/
        sales-returns/
        student-purchase-returns/
      production/
        contracts/
        design-sheets/
        bom/
        production-orders/
        daily-sheets/
      inspection/
      maintenance/
      emd/
      payments/
      employees/
      reports/
      mobile/
      public-site/
    services/
      email.service.js
      whatsapp.service.js
      notification.service.js
      fileStorage.service.js
      export.service.js
    utils/
      dates.js
      numbers.js
      strings.js
      pagination.js
      tenantName.js
    validators/
      common.schemas.js
    jobs/
      backup.job.js
```

## 4. Recommended Frontend Folder Structure

```text
frontend/
  app/
    (public)/
      page.tsx
      about/page.tsx
      pricing/page.tsx
      product/page.tsx
      contact/page.tsx
    (auth)/
      login/page.tsx
      forgot-password/page.tsx
      reset-password/[token]/page.tsx
    (admin)/
      layout.tsx
      dashboard/page.tsx
      users/page.tsx
      users/change-password/page.tsx
      masters/
      purchasing/
      inventory/
      production/
      inspection/
      maintenance/
      emd/
      payments/
      employees/
      reports/
    api/
  components/
    layout/
    navigation/
    forms/
    data-table/
    modals/
    upload/
    report-viewer/
    feedback/
  features/
    auth/
    permissions/
    masters/
    purchasing/
    inventory/
    production/
    reports/
  hooks/
    useApi.ts
    useAuth.ts
    usePermissions.ts
    useTenant.ts
    useDebouncedSearch.ts
  services/
    apiClient.ts
    authApi.ts
    reportApi.ts
  contexts/
    AuthContext.tsx
    TenantContext.tsx
    PermissionContext.tsx
```

## 5. Foundation Modules

These must be migrated first because nearly every module depends on them.

| Foundation Module | Existing CakePHP Files | Tables | Backend APIs | Frontend |
|---|---|---|---|---|
| Auth/Login | `src/Controller/LoginsController.php`, `src/Controller/AppController.php`, `src/Template/Logins/*` | `users`, `schools`, `roles`, `device` | `POST /api/auth/login`, `POST /api/auth/logout`, `POST /api/auth/forgot-password`, `POST /api/auth/reset-password`, `GET /api/auth/me` | login, forgot/reset password, auth context |
| Tenant/company context | `src/Controller/Admin/UsersController.php`, `AppController::connection`, `erpLogin`, `getbranchdetail` | `schools`, tenant DB `users` | `GET /api/companies`, `POST /api/companies`, `GET /api/tenants/current`, `POST /api/tenants/switch` | company selector, super-admin company setup |
| Permissions | `src/Controller/Admin/PermissionController.php`, `PermissionmodulesController.php`, `AppController::checkfinalpermission` | `permission_module`, `permissions`, `permission_access`, `permission_label`, `roles` | `GET /api/permissions/modules`, `PUT /api/permissions/roles/:roleId`, `GET /api/permissions/me` | permission management, permission-aware sidebar |
| Shared settings | `SitesettingsController`, `SeoController`, `TemplateController` | `sitesettings`, `sitesettings_details`, `seo`, `template`, `template_category`, `emailtemplate` | settings CRUD, email template CRUD, SEO CRUD | profile/settings screens |
| Common lookups | states/cities/country/taxes/measurement units/item locations/categories | many master tables | lookup endpoints and master CRUD | reusable select components |

## 6. Business Module Map

| Module | CakePHP Files Involved | Database Tables Used | Backend APIs To Create | Next.js Pages/Components | Depends On |
|---|---|---|---|---|---|
| Dashboard | `src/Controller/Admin/DashboardsController.php`, `src/Template/Admin/Dashboards/*` | users, stock, purchase, production, branch/company tables | `GET /api/dashboard/overview`, `GET /api/dashboard/branches` | `(admin)/dashboard`, KPI cards, charts | auth, tenant, permissions |
| Company/User Admin | `Admin/UsersController.php`, `Template/Admin/Users/*`, `SchoolsTable.php`, `UsersTable.php` | `schools`, `users`, tenant DB users | company CRUD, clone/provision hooks, users CRUD, change password | users, company setup, change password | auth, permissions, tenant |
| Roles & Permissions | `RolesController.php`, `PermissionController.php`, `PermissionmodulesController.php` | `roles`, `permission_module`, `permissions`, `permission_access`, `permission_label` | role CRUD, module sync, permission matrix | roles page, permission matrix | auth |
| Geography Masters | `CountryController.php`, `StatesController.php`, `CitiesController.php` | `country`, `states`, `cities` | CRUD + status/sort/search | master list/form pages | permissions |
| Item Masters | `ItemcategoryController.php`, `ItemlocationController.php`, `ItemnameController.php`, `AdditemController.php`, `StoreitemsController.php` | `st_itemcategory`, `st_itemlocation`, `st_itemname`, `st_additem`, `st_storeitem`, `st_measurementunits`, `st_sizemanager`, `st_taxmaster` | CRUD, search, item detail, sublocation/category endpoints, export | item category/location/name/item pages, item search dialogs | geography, taxes, units |
| Vendor/Supplier Masters | `VendorsController.php`, `SuppliersController.php`, `TransporterController.php` | `vendors`, `st_vendorshipfrom`, `st_vendorbillto`, `suppliers`, `transporter`, states/cities/taxes | CRUD, ship-from/bill-to, vendor detail/export | vendors pages, address subforms | geography, taxes |
| Tax/Unit/Size/Machine/Process Masters | `Taxmaster`, `Measurementunit`, `Sizemanager`, `Machine`, `Process` controllers/templates | `st_taxmaster`, `st_measurementunits`, `st_sizemanager`, `machine_master`, `process` | CRUD/status/search | small master managers | permissions |
| Contracts | `ContractsController.php`, `Template/Admin/Contracts/*` | `contracts`, `bom`, `bom_finished_product`, `bom_raw_material`, `designsheet`, `productionorder`, `inspection_report`, vendors, taxes | contract CRUD, item search, contract detail, expenditure, reverse view | contracts list/form/detail pages | item masters, vendors, BOM |
| Design Sheets & BOM | `DesignsheetController.php`, `ProductionController::addbom/editaddbom/billsofmaterials`, related templates | `designsheet`, `designsheetdetails`, `bom`, `bom_raw_material`, `bom_finished_product`, `finishedprocess`, additem/tax/unit | design sheet CRUD, BOM CRUD, detail endpoints, PDF | design sheet pages, BOM builder | item masters, contracts |
| Indent | `IndentController.php`, `Template/Admin/Indent/*` | `indent`, `indenttemp`, `indentpreview`, additem/item category/location | indent CRUD, temp item operations, pending indent, search | indent list/form/detail | item masters |
| Indent PO | `IndentpoController.php`, templates | `indentpo`, `indentpodetails`, stock, design sheets, machines | indent PO CRUD, category/design detail, PDF/Excel | indent PO pages | indent, stock, design sheets |
| Quotation | `QuotationController.php`, templates | `quotation`, `quotation_details`, `quotation_send`, `quotation_received`, `quotation_received_details`, vendors, schools, taxes | quotation CRUD, send quotation, vendor quotation, received quotation views | quotation workflow screens | vendors, items, taxes |
| Purchase Orders | `PurchaseorderController.php`, 32 templates | `st_purchaseorder`, `st_purchaseorderDetails`, `st_purchaseorderitem`, `st_purchaseordertemp`, `po_delivery_note`, vendors, goods received, indent, taxes | PO CRUD, revise/award, item endpoints, supplier add, delivery note, summaries, PDF/Excel | purchasing PO list/form/detail/revision/export pages | vendors, items, indent, quotation |
| Goods Received / GRN | `GoodsreceivedController.php`, templates | `st_goodsreceive`, `grn_inspection`, `grn_inspection_details`, purchase order/detail, stock, payments | GRN CRUD, inspection GRN, purchase order item fetch, vendor reports, PDF/Excel | GRN pages, inspection modal, report pages | purchase orders, vendors, stock |
| Stock Register | `StockregisterController.php`, templates | `st_stock_register`, `st_stock_available`, goods received, purchase order, production order, job challan | stock list, received/dispatched stock, daily/weekly/summary/detail reports, required stock | stock dashboard, report filters/downloads | items, purchase, production |
| Branch Requests/MRN | `BranchitemrequestController.php`, templates | `branchrequest`, `branchrequestdetail`, `stockregister`, categorywise, sold items | request CRUD, MRN generation, billing, category requests | branch request/MRN pages | stock, item masters |
| Sales / Sold Items / Returns | `SalesController.php`, `SolditemsController.php`, `SalereturnController.php`, templates | `solditem`, `solditemdetails`, `salesreturn`, `salesreturndetails`, stock/category/tax | sales screens, sold item CRUD, returns, bill generation, store/HO reports | sales funnel, invoice/order/return pages | stock, vendors/items |
| Purchase Returns | `PurchasereturnController.php`, `StudentpurchasereturnController.php`, templates | `purchasereturn`, `purchasereturn_details`, `student_purchasereturn`, `student_purchasereturn_details`, temp tables | returns CRUD, item delete/temp operations, bill generation | return pages | purchase, stock, items |
| Production | `ProductionController.php`, templates | `production`, `productionsheet`, `productionsheetitem`, `productionorder`, `finishedprocess`, machine, planned type, BOM/design/contract | production entries, daily sheets, production orders, status/process completion, PDF/Excel | production orders, daily sheet, BOM, reports | contracts, BOM, machines, stock |
| Job Challan | `JobchallanController.php`, templates | `job_challans`, `job_challan_items`, `job_challan_receives`, subcontractors, stock | challan CRUD, item received, stock queries, PDF | job challan pages | stock, items, subcontractors |
| Inspection | `InspectionController.php`, templates | `inspection_report`, contracts | inspection add/list/search, document upload | inspection pages/upload | contracts, file storage |
| Maintenance | `MaintenanceController.php`, templates | `maintenance`, `machine_master`, users/device/sitesettings | maintenance CRUD, status, export, notification | maintenance list/form | machines, notifications |
| EMD / Payment Manager | `EmdController.php`, `PaymentmanagerController.php`, `PaymentsController.php`, templates | `emd_amount`, `emd_guarantees`, `emd_remarks`, `paymentmanager`, `particularpayments`, `particular_pay_receive`, `payments` | EMD CRUD/view amount/remarks, payment import/export, payment views | EMD/payment pages | vendors, reports |
| Employees | `EmployeesController.php`, 20 templates | `employees`, `employeeattendance`, `employeesalary`, `documents`, `documentcategory`, departments/designations/users | employee CRUD, attendance, docs, SMS/email, image upload, reports | employee pages, attendance grid, document tabs | auth/users, departments, docs |
| Data Import | `DatarecordController.php`, templates | many school/ERP import tables | import/export endpoints per dataset, validation, dry-run | import wizard pages | target modules |
| Reports | `ReportController.php`, `ReportNewController.php`, 158 templates | broad: students, fees, employees, books, documents, purchase, transport, etc. | report metadata, filters, data endpoints, PDF/Excel downloads | report catalog, filter forms, report viewer/download | all source modules |
| Mobile API | `MobileController.php`, `Api/MobileController.php` | device, users, contracts, PO, GRN, stock, production, maintenance | mobile auth, dashboard, PO/GRN/vendor/stock/contract/search/PDF endpoints | external/mobile consumers, no Next pages required except admin token views | auth, tenant, source modules |
| Public Site | `HomesController.php`, public templates/assets | contacts, demo requests, email template, IP ranges | contact/demo request APIs, static content endpoints | public marketing pages | email service |

## 7. Reusable Business Logic To Convert

| CakePHP Logic | Current Location | Node.js Conversion |
|---|---|---|
| Tenant DB switching | `AppController::initialize`, `connection`, module `db()` methods | `tenant.middleware.js` derives tenant from JWT/company and attaches a tenant DB connection/repository context |
| Permission bootstrap/check | `check_permission`, `checkfinalpermission`, `PermissionmodulesController` | `authorize(permissionKey)` middleware plus `permission.service.js`; sync modules from backend route registry |
| Financial year calculation | `AppController::financialyear` | `utils/dates.js#getFinancialYear(date)` |
| Emoji stripping | `removeEmojis` in multiple controllers | `utils/strings.js#removeEmojis` |
| WhatsApp sending | `AppController::whatsappmsg`, `WhatsAppComponent` | `services/whatsapp.service.js`; env-based credentials; retries and logging |
| Email sending | `EmailComponent`, `AppController::send_email` | `services/email.service.js`; env-based SMTP; attachment validation |
| FCM notifications | `sendNotification`, `getGoogleAccessToken` | `services/notification.service.js`; service account outside public root |
| File uploads | multiple controllers and API helpers | `middleware/upload.js` + `fileStorage.service.js`; MIME/size validation; private storage |
| Date/Excel conversion | payment/import/report controllers | `utils/dates.js`, import services with typed parsing |
| Report filters and exports | report controllers/templates | report service layer returning JSON, CSV/XLSX/PDF renderers |
| Temporary item workflows | indent, PO, branch request, returns | transactional service methods, either preserve temp tables initially or replace with draft tables after parity |

## 8. REST API Architecture

Route conventions:

- `/api/auth/*` authentication
- `/api/me/*` user/session/permission state
- `/api/admin/*` super-admin/company/role/permission
- `/api/masters/*` master data
- `/api/purchasing/*` indent, quotation, purchase order, GRN, returns
- `/api/inventory/*` stock, branch request, sales/returns
- `/api/production/*` contracts, design sheets, BOM, production
- `/api/reports/*` report metadata, report data, export downloads
- `/api/mobile/*` mobile-specific compatibility endpoints

Example route/controller/service/repository flow:

```text
POST /api/purchasing/purchase-orders
  authenticate
  tenant
  authorize("Purchaseorder.add")
  validate(createPurchaseOrderSchema)
  purchaseOrderController.create
  purchaseOrderService.create
  purchaseOrderRepository.insertHeaderAndDetails(transaction)
```

Common response shape:

```json
{
  "success": true,
  "data": {},
  "message": "OK",
  "meta": { "page": 1, "limit": 25, "total": 0 }
}
```

Common error shape:

```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Invalid request",
    "details": []
  }
}
```

## 9. Dependency-Based Implementation Order

1. Backend/frontend foundations: repository pattern, tenant middleware, auth, error handling, validation, API client, admin layout.
2. Company/user/role/permission/settings modules.
3. Common lookup/master modules: geography, tax, units, size, locations, item categories/names/items, vendors/suppliers, machines/process.
4. Contract/design sheet/BOM foundations.
5. Indent and quotation workflows.
6. Purchase order workflow.
7. GRN/goods received and inspection.
8. Stock register and stock availability.
9. Branch request, sales, sold items, sales return, purchase return.
10. Production orders, daily sheets, process completion, job challans.
11. Maintenance, EMD, payment manager, payments.
12. Employees and employee attendance/documents.
13. Mobile API compatibility layer.
14. Data import/export tooling.
15. Reports, starting with operational ERP reports, then legacy school/fees/library reports.
16. Public site and contact/demo request flow.
17. Cron/background jobs and backup replacement.

## 10. Migration Checklist

| Milestone | Scope | Complexity | Exit Criteria |
|---|---|---:|---|
| M0 Inventory | Freeze current CakePHP behavior, route/action/template/table inventory | Medium | Inventory checked in and reviewed |
| M1 Foundation | Express/Next scaffolds, env, DB pools, auth, tenant, errors, validation | High | Login works and protected route returns current user |
| M2 Permissions | Role/module permission parity | High | Sidebar/API permissions match CakePHP behavior |
| M3 Masters | CRUD for lookup/item/vendor/tax/unit/machine masters | Medium | Lists/forms/search/status work |
| M4 Purchasing Core | Indent, quotation, PO create/edit/revise/detail/export | Very High | PO lifecycle matches legacy output |
| M5 GRN/Stock | GRN, inspection, stock register, reports | Very High | Stock movement totals match legacy |
| M6 Production | Contract, BOM, design sheet, production order/daily sheet | Very High | Production workflow matches legacy |
| M7 Inventory/Sales/Returns | Branch requests, sold items, sales returns, purchase returns | High | Bill/detail exports match legacy |
| M8 EMD/Payments/Maintenance | EMD, payment manager, maintenance notifications | Medium | CRUD/import/export/notification parity |
| M9 Employees | Employee CRUD, attendance, docs, user creation | High | Employee workflows and related users match |
| M10 Reports | Report catalog, filters, data, PDF/XLSX downloads | Very High | Report totals match legacy for sampled data |
| M11 Mobile API | Compatibility or v2 mobile API | High | Mobile app endpoints return compatible payloads |
| M12 Cutover | Parallel run, data validation, redirects, deploy | Very High | Users can complete core workflows in new app |

## 11. Implementation Notes

- Preserve current behavior first, including field names, statuses, totals, and report outputs.
- Keep raw SQL parity in repositories where the CakePHP code uses complex query strings, then refactor only after tests prove equivalent behavior.
- Do not expose tenant database names to clients.
- Move all secrets out of source and public webroot.
- Keep generated documents private by default and stream downloads through authorized APIs.
- Build report APIs as data-first endpoints, then attach PDF/XLSX renderers.
- Treat legacy `confirm_pass` as a migration hazard: preserve compatibility only long enough to migrate users to hashed passwords.

## 12. First Module To Implement

Start with Foundation/Auth because every module needs it.

First implementation target:

- Backend: `backend/src/modules/auth`
- Frontend: `frontend/app/(auth)/login`
- Shared: `backend/src/middleware/authenticate.js`, `backend/src/middleware/tenant.js`, `frontend/services/apiClient.ts`, `frontend/contexts/AuthContext.tsx`

Minimum parity scope:

- Login by mobile/password as existing system does.
- Resolve user company and tenant DB.
- Return JWT and current user payload.
- Protect `/api/me`.
- Next.js login screen stores token and redirects to admin dashboard shell.

Security improvement while preserving behavior:

- Support legacy `confirm_pass` comparison only as compatibility fallback.
- Prefer hashed password verification when available.
- Never store plaintext password in frontend cookies.
