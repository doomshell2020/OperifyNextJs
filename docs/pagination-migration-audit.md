# Pagination migration audit — 6 October 2026

The PHP controllers, models, query conditions, AJAX handlers, views, shared paginator and bundled DataTables are the reference. No database records were inserted, updated or deleted. Purchase Order PDF generation was not changed.

| Module | Verified PHP behavior | Next.js mismatch and fix | Read-only verification |
| --- | --- | --- | --- |
| Contract | Server pagination, 50 rows; ID descending; exact selected contract/vendor, cost substring, independent start/end date bounds | UI requested 20, API defaulted to 10. Both now default to 50; numeric totals/page metadata, exact selected IDs, suggestions beyond the current page, inclusive DATE comparisons and complete controls | 90 records / 2 pages |
| Design Sheet | Server pagination, 50 rows; ID descending; contract and independent date filters | UI already requested 50; API defaulted to 20. Numeric metadata/default corrected; record count and empty-result controls added; wide table can show all columns; revision links no longer depend on the original attachment; numeric dependency counts control delete availability | 112 records / 3 pages |
| Purchase Order | Server pagination, 50 rows; ID descending. Initial list includes Y/R records. AJAX searches use matching PO-detail numbers and optional item/status/vendor/date filters, preserving revision rows. Date range requires both endpoints; delivery mode uses delivery date | UI/API requested 10. Shared list/count conditions now reproduce initial/search behavior without multiplying revisions; filters apply with Search and persist across pages; Reset restores initial list. Product/vendor suggestions and date-type filters restored. Received quantity uses actual stock receipts, not delivery schedules. Action menu renders outside the table overflow boundary | 2,832 records / 57 pages |
| GRN Inspection | Server pagination, 50 rows; ID descending; all inspection statuses; PO/vendor/inward-date filters | UI/API requested 10. Added date filters, exact PO matching, metadata and controls; corrected filter routing to the inspection list. Previously placeholder export now exports the complete inspection list like PHP | 1,398 records / 28 pages |
| GRN | Server pagination, 50 rows; ID descending; all statuses; exact PO/vendor and independent inward-date filters | UI requested 10. Fixed default, exact PO matching, DATE bounds, applied filters, metadata and controls. Export uses applied filters with the same exact PO/date predicates | 2,274 records / 46 pages |
| Indents | Complete grouped list, browser DataTables pagination. Bundled `jquery.dataTables.min.js` sets `iDisplayLength:50`; length changes and sorting disabled; table search enabled | No browser pagination/search, broken stock-table/unit references, potential duplicate summaries. Corrected unique groups and complete child totals; bulk child query; browser paging/search at 50; conditional Create PO points to the add page | 2 live indents / 1 page; 126 in-memory browser rows verified 3 pages without database fixtures |

## Reference behavior

- `src/Controller/AppController.php:137` sets the server limit to 50. Cake's paginator caps requested limits at 100, numbers pages from 1, calculates `ceil(count/limit)`, and rejects a page beyond the final page with 404. An empty list is page 1 of 0.
- `src/Template/Element/admin/pagination.ctp` supplies First, Previous, numbered pages, Next, Last and count information.
- Contract: `ContractsController::index/searchitem`, Contracts model and list/AJAX templates.
- Design Sheet: `DesignsheetController::index/searchitem`, Designsheet model and list/AJAX templates.
- PO: `PurchaseorderController::index/searchitem`, Purchaseorder/Details models, list/AJAX templates and `CommanHelper::checkgrn` for received quantity.
- GRN/Inspection: `GoodsreceivedController::index/searchitem/grninspection/grninspectionexcel`, Goodsreceived/InspectionGrn models and their views.
- Indents: `IndentController::index/searchitem`, Indent model, helpers, `Indent/index.ctp`, admin footer initialization and bundled DataTables defaults.

## API and Sequelize

Five server lists use validated numeric page/limit/offset, matching count conditions, database LIMIT/OFFSET, stable ID ordering and numeric total/page/limit/totalPages/current/previous/next metadata. Alternate sort fields are allowlisted with an ID tie-breaker. Indents intentionally uses the PHP browser-side model and returns total/defaultLimit/paginationMode plus the complete matching list; it is not converted into a fake server-paginated endpoint.

Filters and page numbers stay active through page changes, full reloads and browser Back using query-string state, including allowlisted sort/direction parameters. Sorting remains disabled for the Indent DataTable, as in PHP. Editing a draft filter leaves the active results intact until Search; Search and Reset start at page 1. Removed previous-page placeholder rows that could expose stale actions. Corrected the DatePicker event name so named date filters update the intended field. Empty results show zero counts and disabled navigation. No vertical table scrollbars were added.

## Tests

Read-only tenant `tirupati_tppl` was compared with independent SQL conditions derived from PHP. Every server page was traversed and compared by primary ID, proving complete coverage and no duplication. First, second, middle, last, Previous/Next revisits, search, filters, combined filters, reset, empty results, counts, ascending sorting, limits and out-of-range responses passed.

Browser checks passed for all six pages: row counts, first/second/middle/last navigation where available, Previous/Next, draft-filter behavior, real search with date filters, reset, empty results, reload/browser Back state restoration and absence of vertical clipping. Indents' later pages and global table search passed using response fixtures held only in browser memory. Its live database has only two records. PO Action menu visibility and product/vendor autocomplete ID mapping passed after the fixes. Browser checks also confirmed sorted rows on page 2, Previous and reload across all five server lists.

Additional checks passed for PO detail membership/item/status/delivery-date/partial-date rules, actual received quantities, Contract date bounds, GRN export filter matching, and inspection-export workbook count (1,398 data rows). All changed backend files passed syntax checks.

Repeat the API audit from the repository root with `node backend/scripts/audit-list-pagination.js`; run focused query checks with `node backend/scripts/audit-list-pagination-details.js`. Browser verification uses `AUDIT_BROWSER_ONLY=1` and the running frontend on port 3000. `AUDIT_UI_ONLY` limits a follow-up to one module; `AUDIT_INDENT_FIXTURE=1` enables browser-only Indent rows.

The project-wide TypeScript check reports the same 14 pre-existing errors, including PO add-form Zod/resolver types, Reverse add, Contract detail types, dashboard/modal types and PermissionContext. None is in the changed list pages or pagination controls. A clean production build cannot be confirmed until these existing errors are resolved.

## Intentional corrections to legacy defects

- Design Sheet AJAX searches used an exact contract ID while its ordinary index used substring matching. The applied exact filter now remains exact on subsequent pages.
- Inspection's old filters used the GRN alias/AJAX/paginator destination. Its filters and pagination now remain within GRN Inspection.
- Filtered legacy Indents omitted grouping and could repeat the same indent. Next.js retains unique summaries and full child totals, with deterministic latest-child ID ordering.
- Existing free-text contract/vendor and inspection bill searches remain available as extensions. Selected Contract/vendor suggestions use exact numeric IDs. PO product/vendor suggestions carry numeric IDs into the list query.

## Files changed for this audit

- `backend/src/utils/listPagination.js`
- Contract controller/repository; Design Sheet controller index handler
- PO controller/service list handlers and repository list/count predicates/received quantity
- GRN controller/service/repository list handlers and export filter predicates
- GRN Inspection controller/service/repository/routes, including export
- Indent controller/repository list handlers
- `frontend/components/ui/ListPagination.tsx`, `useListLocation.ts` and `DatePicker.tsx`
- Contract, Design Sheet, PO, GRN Inspection, GRN and Indents list pages
- `frontend/services/contract.service.ts`, `indent.service.ts`, `grnInspection.service.ts`
- `backend/scripts/audit-list-pagination.js`, `audit-list-pagination-browser.js`, `audit-list-pagination-details.js`
- This report

Existing user edits, old PHP files and PDF templates were preserved. Actions that create, revise or delete records were inspected but not executed, respecting the database restrictions. Attachment destinations were checked in list rendering; legacy-upload file completeness was not audited.

## Runtime

The fixes passed using a separate current-code backend and the existing frontend. Automatic approval review rejected restarting the existing development backend with “blocked by policy” and no more specific reason. The server on port 5000 must be restarted to load the API changes.
