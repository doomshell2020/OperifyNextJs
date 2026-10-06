# Operify Phase 1 PHP → Next.js audit

Date: 6 October 2026. Overall status: **NEEDS ATTENTION — do not deploy as fully audited/live-ready yet.**

**NO DATABASE RECORDS WERE MODIFIED** during this audit. No schema changes, test inserts, stock adjustments, live saves or deletions were executed. All new workflow fixtures are held in memory. The PHP working tree was read as the source of truth; existing PHP edits were preserved.

## What the status means

FIXED means an identified difference was corrected and checked. PASS means the inspected behavior and available sample checks agree with PHP. Neither means that a live create/edit/delete was executed. NEEDS ATTENTION means a specific source, data or acceptance gap remains. This report deliberately uses that value in individual columns where PASS/FIXED would overstate the evidence.

Previous PDF, pagination and JC migration work already existed in the repository. Its reports are supplementary evidence, identified below. Historical database migrations recorded there were not performed or reapplied during this audit.

## All 12 modules

| Module | Status | Functionality | Business logic | Permissions | Data/API | Search/filter | Pagination | PDF/print/export | Related workflow |
|---|---|---|---|---|---|---|---|---|---|
| Contract | NEEDS ATTENTION | FIXED / NEEDS ATTENTION | FIXED / NEEDS ATTENTION | FIXED | FIXED | PASS | PASS | PASS for previously compared PDF samples; current PDF endpoint passed | NEEDS ATTENTION |
| Design Sheet | NEEDS ATTENTION | FIXED | FIXED | Existing guards preserved | FIXED | PASS | PASS | PASS for previously compared sample; current PDF endpoint passed | NEEDS ATTENTION |
| Purchase Order | FIXED, acceptance pending | FIXED | FIXED | Existing action guards preserved | FIXED | PASS | PASS | Accepted PO PDF implementation preserved | FIXED |
| GRN Inspection | FIXED, acceptance pending | FIXED | FIXED | FIXED | FIXED | PASS | PASS | Excel PASS; no completed PHP inspection PDF identified | FIXED |
| GRN | NEEDS ATTENTION | FIXED | FIXED | FIXED | FIXED | PASS | PASS | PASS for prior comparison and current authenticated PDF response | NEEDS ATTENTION |
| Indents | NEEDS ATTENTION | FIXED | FIXED | FIXED | FIXED | FIXED | FIXED | Material issue Excel FIXED; both indent PDF endpoints passed | NEEDS ATTENTION |
| JC Challan | NEEDS ATTENTION | PASS in available current checks | PASS in memory workflow checks | PASS for current PHP/configured policy | PASS in available tenant data | PASS | PASS | Existing-record PDF generation PASS; cross-company acceptance remains | NEEDS ATTENTION |
| JC Receive | NEEDS ATTENTION | PASS in available current checks | PASS in memory workflow checks | PASS for current PHP/configured policy | PASS in available tenant data | PASS | PASS | Existing return PDF generation PASS; full visual acceptance remains | NEEDS ATTENTION |
| Getpass | NEEDS ATTENTION | PASS in available current checks | PASS in memory workflow checks | PASS for current PHP/configured policy | PASS for TPPL existing record | PASS | PASS | Existing TPPL PDF generation PASS; full visual acceptance remains | NEEDS ATTENTION |
| Stock Register | NEEDS ATTENTION | FIXED | FIXED | FIXED | FIXED | PASS | PHP full-list behavior preserved | Excel FIXED and real downloads checked | NEEDS ATTENTION |
| Products | NEEDS ATTENTION | FIXED | FIXED | FIXED | FIXED / NEEDS ATTENTION | FIXED | FIXED | Excel FIXED; source PDF N/A | NEEDS ATTENTION |
| Categories | FIXED, acceptance pending | FIXED | FIXED | FIXED | FIXED | PASS | PHP client DataTable behavior preserved, 50/page | N/A | FIXED |

### Contract

- Existing finished-product IDs survive edits. Existing quantities/prices stay read-only as in PHP; additions/removals no longer replace every child record. The edit no longer updates the PHP-commented BOM workflow.
- Finished-product choices use active FinishedProduct records. Contract details display actual production processes instead of always saying production has not started.
- Restored reverse-expenditure drill-down and linked GRN details. It uses design item membership, all ledger statuses, latest GRN by ledger ID at/before reverse creation date, and PHP's string tax multiplier. The unusual one-digit tax behavior is preserved, not replaced with a new costing rule.
- **Remaining:** `Contracts/viewcontractdetail.ctp` and `viewexpenditure.ctp` call `CommanHelper::rawindentitemqty()`, which is absent from the supplied PHP source. The expenditure screen and complete cost/pending-cost summary cannot be certified or faithfully restored from that source. Reverse-cost live samples contained zero qualifying design-linked rows; calculation behavior is checked in memory.
- TPPL has six finished-product BOM rows with missing contracts and three with missing product masters.

### Design Sheet

- Header/detail create, edit and delete operations are transactional. Duplicate validation happens before creating a header. Detail failure rolls back the header. Persisted sheet identity is retained, and old attachment cleanup occurs only after a successful update.
- **Remaining:** existing category group conflicts block material issue according to PHP: 166 sheet/category combinations in TPPL and 15 in KCPL. These counts are combinations, not distinct sheets. Uploaded-file cleanup on all failed request paths and completeness of historical uploads have not been certified.

### Purchase Order

- Revisions lock the source/family and reject a stale revision. Deletion rejects any family with a GRN and rejects an older active revision.
- New creation checks the supplier/items, reads tax percentages from the tax master, and derives quantity/tax/amount on the server using the PHP included/excluded formulas. Submitted header and line totals cannot replace those calculations.
- Number generation uses the creation transaction and a row lock. A repeated number is rejected before inserting header/details, including resubmission after the original save. The form sends its explicit tax mode. No PO PDF/template code was changed.
- **Remaining:** real multi-session create/revise/receive races and live write acceptance were not executed under the database restriction. In-memory duplicate/rollback/revision tests are not a substitute for that acceptance exercise.

### GRN Inspection

- Inspection quantity budgets are derived from PO lines and prior inspections, with duplicate inputs and excess quantities rejected. Server valuation preserves PO base/tax/amount, including tax-inclusive amounts. Vendor, dates, delivery schedule and inspection numbering are retained.
- The form prorates the saved PO valuations consistently in both line and header displays. Permissions protect the API and Add action. Export dates preserve the stored calendar date.
- **Remaining:** TPPL has no active inspection available for a read-only receiving sample; KCPL has one and its saved valuations reached the receiving API unchanged. No live inspection save or numbering race was executed. PHP provides inspection Excel and PO navigation; a completed separate inspection PDF counterpart was not identified. Existing Next-only inspection print screens are not certified as PHP parity.

### GRN

- Receiving reads the locked inspection's saved quantities and valuations, derives its PO/vendor, aggregates repeated item budgets, and ignores tampered client stock lines.
- GRN, payment, ledger, inspection consumption, delivery schedule and PO-family status changes share one transaction. Repeated receiving is rejected. Ledger IDs distinguish the PO's internal row ID from its displayed family number. Actual saved tax IDs are joined, without a default 18% substitution.
- **Remaining:** `st_stock_available.stock_available` is `int(11)` in both tenants. Where a cache row exists, a fractional receipt cannot be retained exactly in that cache, although the ledger remains authoritative. This needs a reviewed precision strategy before fractional receiving is accepted. No cache/schema repair was attempted.

### Indents

- Both flows were inspected: material issue `indentpo`, used by the main Indents menu, and purchase requisition `indent`.
- Material issues now use real 50-row server pagination and matching totals/search/date filters. PHP edit/delete/Excel actions were restored with API permissions. Edit updates existing ledger IDs rather than inserting another stock deduction.
- Group siblings consume a shared category pending budget. Group issued quantities include all category members. PHP's tenant `stock_update` switch controls in-hand enforcement. Source group/non-group conflicts are rejected with a useful error.
- Requisition finalization locks its temporary cart and rejects an already finalized indent before writes. Its PHP client-side pagination remains separate from material-issue server pagination. No new approval workflow was invented.
- **Remaining:** existing conflicting sheets must be reviewed before their material issues can be created. Memory checks cover updates/transactions, but no live edit/delete/finalization was performed.

### JC Challan, JC Receive and Getpass

- Rechecked current schema, lists, detail mappings, pending/received history, subcontractor and company restrictions, linked-tenant protection and authentication using the existing migration audit.
- 24 existing memory tests cover dispatch/receive quantities, repeated item budgets, duplicate documents, stock movements, local product mapping, schema errors, Getpass mapping/edit rules and transactions.
- TPPL currently has 104 JCs, one Getpass and seven receive rows. KCPL has zero local JCs/Getpasses and 69 receive rows. Existing TPPL JC, Getpass and return PDFs generate successfully.
- **Remaining:** no complete live sister-company dispatch→receive→Getpass write workflow was executed. KCPL has no local persisted JC/Getpass example. Current PHP leaves per-user beforeFilter enforcement commented; the backend honors configured action labels and tenant restrictions. JC/Getpass action labels are currently absent, so these actions fall back to authenticated access as in current PHP. If deployment requires a narrower role/user matrix, that policy must be supplied/configured and verified rather than silently invented.
- The earlier JC report records approved historical schema migrations. This audit did not reapply them, backfill receipts or change records.

### Stock Register

- Daily stock matches current PHP's cumulative active creation-date quantities. Reverse/return movements are not counted twice; zero-stock products remain in the daily display.
- Detailed dispatch uses issue date with created-date fallback and skips zero rows. Indent joins no longer multiply ledger quantities.
- Exports use actual tenant company/logo, PHP column identity and physical-stock blanks, full matching data, and the helper's midnight reverse/return cutoff. Fixed the daily export SQL replacement error found by the real workbook test.
- **Remaining:** stock rows reference absent product masters: nine in TPPL and one in KCPL. They need reviewed data reconciliation; the audit did not hide them by inventing masters. The integer cache issue above remains separate from the tested ledger balances.

### Products

- Actual company/tax/size/location masters replace hardcoded values. Names normalize consistently. Edit persists the PHP controller fields without applying add-time discount twice or wiping omitted fields. HSN, price, discount, type and process controls are editable, correcting the remaining disabled Next controls.
- A category used by a design sheet is read-only in the form and rejects API category changes. Deletion checks PHP's PO/BOM/design dependencies.
- Default active raw-material listing, AJAX all-type/status search, complete filters, stock calculations, stable sorting and 50-row SQL pagination now match the inspected source. Excel exports the complete filtered set using the PHP six columns.
- **Remaining:** active master links are incomplete: TPPL five missing UOMs; KCPL 34 missing categories and seven missing UOMs. `sub_location` and `consumble` are referenced by PHP controller writes but absent from both databases and not exposed as working fields in the supplied product templates. This is a source/schema inconsistency to reconcile, not justification for an automatic schema change. Empty Size/Location choices reflect empty existing masters.

### Categories

- Active-list behavior, duplicate prevention and weekly-stock inclusion are restored. PHP deletion deactivates the category rather than removing mapped records. Add/edit/delete API and buttons use actual action permissions.
- Existing lists contain 68 active TPPL categories and 48 active KCPL categories. No category/master records were added or changed.
- **Remaining:** only the common live write-acceptance limitation; no additional category-specific code blocker identified in the inspected scope.

## Verification

- **64 Node tests passed**, including the 24 JC workflow and six PDF regressions, plus new receiving, masters, exports, design/PO, contract-cost and indent tests.
- Full frontend TypeScript check and the final isolated production build passed, including all final form/modal changes and 47 generated static pages. The development build directory was not overwritten.
- Complete product/material-issue page traversal passed for both tenants: products 1,923 / 691; issues 3,779 / 112. Existing material-issue edit data returned the actual ledger IDs/quantities. Browser checks loaded TPPL issue 4782 with quantities 0.1 and 1 without saving. Contract 130's reverse drill-down loaded successfully. Product 390 showed a read-only design-linked category, editable pricing/type fields and working conditional process choices without saving.
- Independent PHP-derived SQL matched every returned daily/detailed stock balance: TPPL 1,216 / 283 rows; KCPL 443 / 26 rows on 6 October 2026.
- Real product, material-issue and stock Excel downloads were decoded and checked for complete counts and tenant identity. Material-issue exports contain 7,410 / 140 detail rows.
- Six existing list API audits passed, including filters/counts/out-of-range handling and the 1,398-row inspection export.
- Sixteen denied mutation actions returned 403 before reaching any database write. An edit-only token could load product edit data but could not add/delete it or list contracts. Synthetic tokens were used; no login or permission-table changes were required.
- Seven existing PDF endpoints returned authenticated PDF bytes, unauthenticated requests returned 401, missing GRN returned 404 and unlinked JC sender returned 403. PO PDF code remained untouched. This is response verification, not a new visual certification of every record/tenant.
- Production save/delete/receive/dispatch actions and real concurrent transactions were deliberately not performed. Broad lint, deployment environment setup and universal PDF pixel/page-break parity are not claimed.

Evidence: `D:/operify_next/docs/phase1-readonly-tirupati_tppl.json`, `phase1-readonly-tirupati_kcpl.json`, `phase1-relationships.json`; logs under `D:/operify_next/tmp/phase1-*.log`. Relationship evidence includes affected record IDs and sheet/category samples for review.

Prior reports: `D:/operify_next/docs/pdf-migration-audit.md`, `pagination-migration-audit.md`, `jc-receive-getpass-migration-report.md`. Their historical type/build/runtime blockers must not be treated as current results; current checks above supersede those where explicitly rerun. Their historical visual sample comparisons remain limited to the samples they name.

## Live blockers and required next evidence

1. Supply the missing original `rawindentitemqty()` helper or another authoritative working PHP source for Contract expenditure and pending-cost calculations. The current source cannot establish them completely.
2. Review the exact broken master/BOM links and conflicting design groups in `phase1-relationships.json`. Agree on intended records before any repairs. This audit has no authorization to change them.
3. Resolve fractional stock-cache precision with a reviewed schema/cache strategy and backup/verification plan. No ALTER statement was executed or drafted with guessed field definitions.
4. Reconcile the stale PHP product controller fields with the authoritative deployed schema/templates. Do not assume that adding absent columns is the correct repair.
5. Approve a separate disposable acceptance environment or another explicit write-testing arrangement to verify the complete saved workflows, retry/concurrency behavior, restricted roles, and cross-company dispatch/receive/Getpass flow. Existing-record read-only checks and memory fixtures cannot certify those saved flows.
6. Complete visual acceptance for the remaining JC Receive/Getpass/cross-company print variants and the existing Next-only inspection print screens if they are intended to ship. Preserve the accepted PO output.

## Files changed in this audit

Paths below are relative to **D:/operify_next/**. Some files already contained user changes; only the focused additions described above belong to this audit. A subsequent user instruction authorized committing and pushing the tested Next.js changes, including related existing permission, design and PO edits. PHP edits, scratch files, uploads and temporary outputs are excluded from that commit.

Backend:

- `backend/src/middleware/errorHandler.js`
- `backend/src/modules/contract/contract.repository.js`, `contract.routes.js`, `contract.service.js`; new `contract.reverse-cost.js`
- `backend/src/modules/designsheet/designsheet.controller.js`
- `backend/src/modules/purchaseOrder/purchaseOrder.repository.js`, `purchaseOrder.service.js`; new `purchaseOrder.create-validation.js`
- `backend/src/modules/grn/grn.controller.js`, `grn.repository.js`, `grn.routes.js`, `grn.service.js`; new `grn.receipt.js`
- `backend/src/modules/grnInspection/grnInspection.controller.js`, `grnInspection.repository.js`, `grnInspection.routes.js`; new `grnInspection.create.js`
- `backend/src/modules/indent/indent.repository.js`, `indent.routes.js`
- `backend/src/modules/indentpo/indentpo.repository.js`, `indentpo.routes.js`, `indentpo.service.js`; new `indentpo.mutations.js`, `indentpo.export.js`
- `backend/src/modules/settings/settings.controller.js`, `settings.repository.js`, `settings.routes.js`; new `products.list.js`, `products.export.js`
- `backend/src/modules/stockRegister/stockRegister.repository.js`, `stockRegister.routes.js`, `stockRegister.service.js`; new `stockRegister.export.js`
- `backend/src/utils/listPagination.js`; new `receiptValidation.js`

Frontend:

- `frontend/app/dashboard/admin/categories/page.tsx`
- `frontend/app/dashboard/admin/products/page.tsx`, `products/add/page.tsx`, `products/edit/[id]/page.tsx`
- `frontend/app/dashboard/contracts/edit/[id]/page.tsx`
- `frontend/app/dashboard/purchase/orders/add/page.tsx`
- `frontend/app/dashboard/purchase/grn/page.tsx`, `grn/add/page.tsx`
- `frontend/app/dashboard/purchase/inspections/page.tsx`, `inspections/add/page.tsx`
- `frontend/app/dashboard/purchase/indentpo/page.tsx`, `indentpo/new/page.tsx`; new `indentpo/[id]/edit/page.tsx`
- `frontend/components/dashboard/ContractDetailsModal.tsx`, `DashboardHeader.tsx`, `DeliveryNoteModal.tsx`, `PurchaseOrderDetailsModal.tsx`; new `ContractReverseCost.tsx`
- `frontend/contexts/AuthContext.tsx` (permission typing)
- `frontend/services/contract.service.ts`, `indentpo.service.ts`, `settings.service.ts`

Verification/report files:

- New `backend/scripts/audit-phase1-readonly.cjs`, `audit-phase1-relationships.cjs`
- New `backend/tests/phase1-receiving.test.cjs`, `phase1-masters.test.cjs`, `phase1-exports.test.cjs`, `phase1-design-po.test.cjs`, `phase1-indentpo.test.cjs`, `phase1-contract-cost.test.cjs`, `phase1-po-create.test.cjs`
- New `docs/phase1-audit-report.md`, `phase1-readonly-tirupati_tppl.json`, `phase1-readonly-tirupati_kcpl.json`, `phase1-relationships.json`

Other dirty files, scratch files, PHP changes and historical artifacts existed before this audit and were not attributed to it. The local backend was restarted to load fixes; the frontend development output was protected by performing production builds in an isolated copy.
