# JC Challan / JC Receive repair

## Follow-up: simple Receive Add form from the PHP screenshot

Add New Receive now opens the simple party-based form shown in the supplied PHP screenshot. Removed visible JC Type / Receive Type radios and the alternate party-name input. Restored Receive Add heading; Challan No, Reference JC No, blank Challan Date, Vehicle No, From Company / Party plus button, GST; and the Product / Receive Qty / Action table with unit beside quantity. Searchable dropdowns use the PHP Select2 appearance, row removal uses the red icon, Add Item uses the cyan button, and Save JC Receive is red and right-aligned. Existing local JC Receive links still open their pending-quantity flow without type switches.

The form submits the existing manual-receive API payload with a selected party; no backend calculations or stock workflow were changed. Browser checks using existing local records verified party search/GST, product search/unit and Add/Remove rows under a GET-only API and SELECT/SHOW-only database guard. No records were saved. Frontend build and existing permission/stock transaction checks passed.

## Follow-up: access denied and missing action buttons after live restart

The live screenshots showed the new layout with Add/View/PDF/Delete absent and JC Receive denied. The initial fix preserved an incorrect migration assumption: it required per-action URL labels even though the active PHP JC templates do not gate these actions and the active controller's permission-check block is commented out. The checked local permission catalogue contains no JC labels at all.

Session permission loading now applies the PHP authenticated fallback only to the known JC operations when their action label is absent. If an action label is configured, the saved role grant remains mandatory, including explicit denied or missing role-access rows. Other modules have no fallback. Authentication, active-user checks, selected-company authorization and local-only JC data boundaries remain enforced. No role/label records were inserted or updated.

This resolves the earlier local testing limitation: real existing-user GET requests now pass for the JC list, JC Receive list, receive options/details, JC details and tracking. Unauthenticated requests still return 401. Regression tests cover absent/configured/denied JC labels and the actual rendered Add/View/PDF/Delete and Add New Receive controls. Deploy this follow-up backend commit and refresh the browser to reload `/auth/me`; there is no database migration.

The current PHP `JobchallanController.php` and its active `Jobchallan` templates were compared with the Next.js lists, forms, details, tracking, APIs, permissions and three PDF templates. Dated backup PHP files were not treated as the current implementation.

## Confirmed differences and fixes

| Area | Difference found | Fix |
| --- | --- | --- |
| Company scope | Receive options/list, details, tracking and receipt creation read or wrote sister-company databases | All JC operations use only the selected company. Requests naming a different database return 403. Linked database search and PDF fallback were removed. |
| Index | Extra Amount heading without a matching cell; missing JC-number tracking link; Received/Cancelled options instead of Closed | Restored PHP's seven columns, Search Job Challan box, Add location, tracking link and status options. PDF links open a separate tab. |
| Details | A new print-like card combined view and tracking, and showed incorrect/missing GST/company fields | Restored the PHP Challan Info and items table. Tracking has its own route. Monetary total uses all item totals rather than the PHP view's accidental last-row-only subtotal. |
| Tracking | Missing grouped receipt history and Returned Items section | Restored receipt groups, Return Challan links, Production & Return Tracking, and returned item totals. Stock tracking reads only the selected company's stock. |
| Receive list | Linked-only records, Completed badge in the quantity cell, missing search/date controls | Uses local challans and local manual receipts; source table columns and Completed placement restored. Search, dates, pagination and Reset work against local records. |
| Return entry | Wrong permission for loading pending items; one shared date/vehicle field | API accepts the saved return-entry grant. PHP-style modal now provides item selection, per-row date/vehicle/rate/GST, tax/total, Add More, Remove, Save and Close. Direct return route remains available. |
| Forms | Reset navigated away; Back button inside form could submit; stale autocomplete responses could overwrite a row | Reset clears the form; Back is explicitly non-submit; stale raw-material stock/search responses are guarded and editing item text clears its old ID. Semi-finished fields use the PHP table layout. |
| Party modal | Wrong title/width, address was a single-line input, missing field length limits | Restored Add Company / Party title, 999px width, blue header, three-column fields, address textarea, limits and Submit. Portal prevents nested forms. |
| Receipt edits | Pending refetch could attach index-keyed edits to a different item | Edits are keyed by product ID and saved receive/stock queries are invalidated together. |
| Dates | Date filters compared `String(Date)` with YYYY-MM-DD | Calendar-date normalization and valid date-range checks. |
| PDF data | Missing settings could display another company's hardcoded identity | Company name/address/GST/PAN come from settings and party records. Existing legacy document layouts retained. |
| Stock integrity | Schema-aware insert could silently omit JC stock relationship fields | Required movement columns are checked; receipt/header and IN/OUT writes stay in one transaction. Missing columns fail instead of saving an incomplete movement. |

Dispatch preserves raw-material/semi-finished stock checks, quantity × rate, GST, and one OUT entry per line. Local JC receiving preserves pending-quantity caps, one receipt and one IN entry per accepted line, and Pending / Partially Returned / Completed transitions. Manual receipts stay local and do not update a referenced JC. No database records, schema or saved role grants were modified.

## Validation

- Existing-record read-only checks: TPPL has 104 challans, 112 receive-list rows and 86 eligible local challans. Checked 13 product totals across 12 existing challans against independent dispatch/receipt aggregates, including date/number filters. KCPL has no local challans; empty-list behavior checked.
- The database verifier rejects any statement except SELECT/SHOW and rejects any qualified database other than the selected company.
- Stock snapshots before/after checks remained unchanged: TPPL 11,633 rows, KCPL 315 rows.
- In-memory transaction tests passed for dispatch quantities and totals, repeated product budgets, duplicate challan numbers, partial and complete receipt, manual receipt, over-receipt rollback, completed-receipt replay, per-row valuation/date/vehicle, and cross-company rejection. No test records were inserted in a real database.
- Permission checks passed, including the new tracking and return-detail grants, actual rendered list/detail/form fields, local navigation and existing manager checks.
- Production build/TypeScript/route generation and backend syntax checks passed.
- Existing JC 111 and receipt 110 were rendered to A4 PDFs and visually inspected. Both are one page, with readable tables and no overlap/clipping.
- The initial HTTP audit verified unauthenticated 401 and exposed a 403 caused by absent JC labels. The follow-up above resolves this: existing-user read-only HTTP checks now pass. No saved role records were changed. Full interactive browser testing of the deployed follow-up remains outstanding.

## Intentional differences / remaining limits

1. Your explicit instruction overrides PHP's cross-company system: all data and stock writes stay in the selected company, and tracking shows local company stock rather than subcontractor database stock.
2. Configured action permissions are enforced. For JC actions without a configured label, the follow-up restores the PHP authenticated workflow; configured denials still block the action. Other modules' access rules are unchanged.
3. Duplicate product lines are budgeted as a group and transactions roll back atomically. The old PHP receives use the first dispatch row and its local-return workflow can partially save before an error; those defects are not copied.
4. The source JC PDF layouts use the first dispatched item; that format is retained. Old company-identity fallbacks are removed to avoid inventing document data.
5. The code is local and has not been deployed in this task. No production submissions, deletions, role edits or database migrations were performed.

## Files changed

- `backend/src/modules/jobChallan/jobChallan.service.js`
- `backend/src/modules/jobChallan/jobChallan.routes.js`
- `backend/src/modules/jobChallan/legacyStore.js`
- `backend/src/modules/jobChallan/jobChallan.pdf.js`
- `backend/src/modules/jobChallan/jobChallan.subcontractor.pdf.js`
- `backend/src/modules/jobReceive/jobReceive.service.js`
- `backend/src/modules/jobReceive/jobReceive.routes.js`
- `backend/src/modules/jobReceive/jobReceive.pdf.js`
- `frontend/app/dashboard/jc-challan/page.tsx`
- `frontend/app/dashboard/jc-challan/create/page.tsx`
- `frontend/app/dashboard/jc-challan/[id]/page.tsx`
- `frontend/app/dashboard/jc-challan/[id]/tracking/page.tsx` (new)
- `frontend/app/dashboard/jc-challan/[id]/receive/page.tsx`
- `frontend/app/dashboard/jc-receive/page.tsx`
- `frontend/components/jobChallan/JcReceiveList.tsx` (new)
- `frontend/components/jobChallan/ReturnForm.tsx` (new)
- `frontend/components/jobChallan/ReceiveForm.tsx`
- `frontend/components/jobChallan/SubcontractorModal.tsx`
- `frontend/components/jobChallan/JcModuleList.tsx` (removed cross-company navigation from compatibility path; Gate Pass behavior preserved)
- `frontend/components/ProductSearchSelect.tsx` (narrowed required product shape to ID/name; runtime unchanged)
- `frontend/app/dashboard/legacy.css` (JC-scoped styles)
- `scripts/test-permissions.cjs`
- `scripts/test-jc-local.cjs` (new)
- `backend/scripts/verify-jc-local.cjs` (new)
- `backend/scripts/verify-jc-http.cjs` (new)
- `backend/scripts/verify-jc-pdf.cjs` (new)
- `docs/jc-local-parity-report.md` (new)
