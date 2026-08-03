# Operify Migration Inventory

Generated from `D:/operify.in`.

## Counts

- Controllers: 83
- Table/model files: 190
- Templates/views: 638
- Inferred modules: 232
- Report/PDF/Excel-like templates: 207
- Controller files using raw SQL indicators: 55

## Largest Modules

Module | Controllers | Actions | Models | Templates | Report/PDF/Excel Templates | Raw SQL Calls | Loaded Models
--- | ---: | ---: | ---: | ---: | ---: | ---: | ---
Report | 1 | 263 | 0 | 155 | 155 | 52 | AdmissionClasses, Advancesalary, Applicant, Boards, Book, BookCategory, BookCopyDetail, BookStatus, Cities, Classections, Classes, Classfee
Employees | 1 | 54 | 1 | 20 | 2 | 28 | Address, Advancesalary, Cities, Classections, Classes, Country, Departments, Designations, Documentcategory, Documents, DropOutEmployee, Employeeattendance
Purchaseorder | 1 | 44 | 1 | 32 | 9 | 19 | Additem, Cities, Companymaster, Device, Emailtemplate, Goodsreceived, Indent, Itemcategory, Itemlocation, Measurementunit, Officersname, Podeliverynote
Datarecord | 1 | 25 | 0 | 22 | 0 | 33 | Additem, Book, BookCategory, Classes, Classteachers, Companymaster, DiscountCategory, Employees, Employeesalary, Events, Eventtypes, Feesheads
Production | 1 | 51 | 1 | 35 | 5 | 12 | Additem, Bom, Bomfinishedproduct, Bomrawmaterial, Contracts, Designsheet, Designsheetdetails, Finishedprocess, Indentpo, InspectionReport, Machinemaster, Measurementunit
ReportNew | 1 | 58 | 0 | 3 | 3 | 14 | Book, BookCategory, BookCopyDetail, BookStatus, Cities, Classections, Classes, Classfee, Country, Department, Departments, Designations
Solditems | 1 | 26 | 0 | 16 | 3 | 9 | Additem, Branchrequest, Categorywise, Classes, Companymaster, Itemcategory, Sitesettings, SitesettingsDetails, Sizemanager, Solditem, Solditemdetails, StockAvailable
Goodsreceived | 1 | 19 | 1 | 15 | 5 | 11 | Additem, Companymaster, Goodsreceived, Indent, Indenttemp, InspectionGrn, InspectionGrnDetails, Itemcategory, Itemlocation, Itemname, Measurementunit, Payments
Mobile | 2 | 47 | 0 | 0 | 0 | 1 | Additem, Contracts, Device, Goodsreceived, Indentpo, InspectionReport, Machinemaster, Maintenance, Payments, Po_delivery_note, Production, Productionorder
Branchitemrequest | 1 | 22 | 0 | 12 | 0 | 5 | Additem, Branchrequest, Branchrequestdetail, Categorywise, Companymaster, Itemcategory, Mrndetail, Sitesettings, SitesettingsDetails, St_mrn, StockAvailable, Stockregister
Salereturn | 1 | 14 | 0 | 6 | 0 | 11 | Additem, Categorywise, Companymaster, Itemcategory, Salesreturn, Salesreturndetails, Salesreturndetils, Sitesettings, SitesettingsDetails, StockAvailable, Stockregister, Taxmaster
Stockregister | 1 | 16 | 1 | 17 | 6 | 0 | Additem, Goodsreceived, Itemcategory, JobChallanReceives, Productionorder, Purchaseorder, Sizemanager, StockAvailable, Stockregister
Users | 1 | 9 | 1 | 6 | 0 | 9 | Board, Classfee, Examtemplategroup, Examtemplates, Schools, States, Users
Indentpo | 1 | 12 | 1 | 11 | 2 | 4 | Additem, Designsheet, Designsheetdetails, Device, Indentpo, Indentpodetails, Machinemaster, Sitesettings, SitesettingsDetails, Stockregister, Users
Reverseindent | 1 | 11 | 1 | 11 | 2 | 4 | Additem, Designsheet, Designsheetdetails, Device, Machinemaster, Measurementunit, Reverseindent, Sitesettings, SitesettingsDetails, Stockregister, Users
App | 2 | 23 | 0 | 0 | 0 | 1 | AcademicYear, Classections, ClasstimeTabs, PermissionModules, Permissions, Sitesettings, SitesettingsDetails, Template, Users
Itemlocation | 1 | 16 | 1 | 11 | 0 | 0 | Itemlocation
Vendors | 1 | 17 | 0 | 11 | 4 | 0 | Additem, Branchrequest, Branchrequestdetail, Cities, Goodsreceived, States, Taxmaster, Vendor, Vendorbillto, Vendors, Vendorshipfrom
Inventory | 1 | 13 | 0 | 12 | 0 | 0 | 
Sales | 1 | 12 | 0 | 12 | 0 | 0 | Vendor
Indent | 1 | 13 | 1 | 7 | 0 | 1 | Additem, Companymaster, Indent, Indentpreview, Indenttemp, Itemcategory, Itemlocation, Itemname, Measurementunit, Sitesettings, SitesettingsDetails, Sizemanager
Homes | 1 | 13 | 0 | 9 | 0 | 0 | Contacts, Demorequest, Emailtemplate, IpRanges
Quotation | 1 | 11 | 1 | 8 | 0 | 1 | Quotation, QuotationDetails, QuotationReceived, QuotationReceivedDetails, QuotationSend, Schools, Taxmaster, Vendors
Contracts | 1 | 11 | 1 | 8 | 0 | 0 | Additem, Bom, Bomfinishedproduct, Bomrawmaterial, Contracts, Designsheet, Designsheetdetails, Finishedprocess, InspectionReport, Measurementunit, Productionorder, Taxmaster
Designsheet | 1 | 12 | 1 | 7 | 1 | 0 | Additem, Bom, Bomfinishedproduct, Bomrawmaterial, Designsheet, Designsheetdetails, Indent, Measurementunit, Sitesettings, SitesettingsDetails, Taxmaster, Vendorbillto
Additem | 1 | 12 | 1 | 6 | 1 | 0 | Additem, Bomfinishedproduct, Companymaster, Designsheetdetails, Finishedprocess, Itemcategory, Itemlocation, Itemname, Measurementunit, PurchaseorderDetails, Sizemanager, Taxmaster
Paymentmanager | 1 | 11 | 1 | 6 | 1 | 0 | EmdRemarks, Particularpayments, Particularpayreceive, Paymentmanager
Studentpurchasereturn | 1 | 12 | 0 | 4 | 0 | 1 | Additem, Categorywise, Companymaster, Itemcategory, Sitesettings, SitesettingsDetails, Solditem, Solditemdetails, StudentPurchasereturn, StudentPurchasereturnDetails, Students, Taxmaster
Controller1.php | 1 | 17 | 0 | 0 | 0 | 0 | Machinemaster, Plannedtype, Production
Itemcategory | 1 | 10 | 1 | 6 | 0 | 0 | Itemcategory
Jobchallan | 1 | 11 | 0 | 6 | 1 | 0 | Additem, JobChallanItems, JobChallanReceives, JobChallans, Jobchallans, Sizemanager, Stockregister, SubContractors, Taxmaster
Permissionmodules | 1 | 4 | 0 | 3 | 0 | 5 | Assignments, Classections, Classes, Employees, PermissionModules, Sections, Subjectclass, Subjects, Users
Emd | 1 | 9 | 0 | 7 | 1 | 0 | EmdAmount, EmdGuarantees, EmdRemarks
Maintenance | 1 | 10 | 1 | 5 | 1 | 0 | Device, Machinemaster, Maintenance, Role, SitesettingsDetails, Users
Template | 1 | 10 | 1 | 5 | 1 | 0 | Emailtemplate
Cities | 1 | 9 | 1 | 5 | 0 | 0 | Cities, Country, States
Element | 0 | 0 | 0 | 20 | 0 | 0 | 
Itemname | 1 | 8 | 1 | 4 | 0 | 1 | Companymaster, Itemcategory, Itemlocation, Itemname, Measurementunit, Taxmaster
Seo | 1 | 8 | 1 | 6 | 0 | 0 | Seo
Sitesettings | 1 | 4 | 1 | 4 | 0 | 3 | Boards, Roles, SitesettingsDetails, Template, TemplateCategory, Users
Transporter | 1 | 8 | 1 | 6 | 1 | 0 | States, Transporter, Vendor, Vendors
Jobseeker | 1 | 10 | 1 | 3 | 0 | 0 | Additem, Itemcategory, Itemlocation, Itemname, Jobseeker, Measurementunit, Vendors
Payments | 1 | 8 | 0 | 4 | 1 | 1 | Goodsreceived, Payments, Vendors
Storeitems | 1 | 9 | 0 | 5 | 0 | 0 | Additem, Measurementunit, Purchaseorder, Sizemanager, Stockregister, Storeitem, Vendor
Categorywise | 1 | 8 | 1 | 4 | 0 | 0 | Additem, Categorywise, Itemcategory, Measurementunit, Sizemanager, Taxmaster
Logins | 1 | 7 | 0 | 2 | 0 | 2 | Schools, Users
Error | 1 | 5 | 0 | 7 | 0 | 0 | 
Goodsissue | 1 | 7 | 0 | 5 | 0 | 0 | Additem, Companymaster, Goodsissue, Goodsreceived, Indent, Indenttemp, Itemcategory, Itemname, Measurementunit, Purchaseorder, Purchaseorderitem, Purchaseordertemp
Purchasereturn | 1 | 7 | 1 | 4 | 0 | 0 | Additem, Cities, Goodsreceived, Measurementunit, Purchasereturn, Sitesettings, SitesettingsDetails, States, Stockregister, Taxmaster, Vendor, Vendors
States | 1 | 7 | 1 | 4 | 0 | 0 | Country, States
Taxmaster | 1 | 6 | 1 | 3 | 0 | 1 | Taxmaster
Companymaster | 1 | 6 | 1 | 3 | 0 | 0 | Companymaster
Country | 1 | 6 | 1 | 3 | 0 | 0 | Country
Pages | 1 | 6 | 1 | 3 | 0 | 0 | 
Storelocationpermission | 1 | 7 | 1 | 0 | 0 | 1 | Itemlocation, Storelocationpermission, Users
Inspection | 1 | 6 | 0 | 3 | 0 | 0 | Contracts, InspectionReport
Locations | 1 | 8 | 1 | 0 | 0 | 0 | Locations, Routemaster
Machine | 1 | 5 | 0 | 4 | 1 | 0 | Machinemaster
Permission | 1 | 6 | 0 | 3 | 0 | 0 | Manager, PermissionAccess, PermissionLabel, PermissionModules, Roles, Users
Roles | 1 | 6 | 0 | 1 | 0 | 1 | Board, Roles, Users
