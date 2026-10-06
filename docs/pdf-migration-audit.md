# PDF migration audit — 6 October 2026

The PHP/CakePHP templates in `operify-cake-php-old-project` were treated as the source of truth. Purchase Order was preserved. The existing Puppeteer setup was used as a rendering reference, while each affected module retains its own legacy layout.

## 1. PDFs checked

| PDF | Legacy source under `src/Template/Admin` | Result |
| --- | --- | --- |
| Purchase Order | `Purchaseorder` templates and current generation setup | Already working according to the reported baseline; no edits made to its implementation. |
| Contract | `Production/viewcontractdetailspdf.ctp` | Confirmed design, missing-field and calculation mismatches corrected. Both download and Design Sheet's Contract print link use the corrected template. |
| Indent PO / material issue | `Indentpo/viewindentpopdf.ctp` | Confirmed layout and omitted stock-row mismatches corrected. Detail and modal print actions use the same PDF. |
| Reverse Indent | `Reverseindent/viewreverseindentpdf.ctp` | Confirmed layout and omitted stock-row mismatches corrected. |
| Job Challan | `Jobchallan/viewpdf.ctp` | Confirmed recipient, unit, date/time and layout mismatches corrected. |
| Sister-company Job Work Challan | `Jobchallan/viewsubcontractorpdf.ctp` | Separate legacy format implemented, with source-company settings and stored item amounts. Offline comparison completed; no existing challans were available in the linked sister-company database for a live comparison. |
| GRN | `Goodsreceived/view.ctp` | Confirmed company, quantity, tax, totals, table and footer mismatches corrected. Print and the existing Next download route now share this PDF. |
| Design Sheet | `Designsheet/viewdesignsheetpdf.ctp` | Confirmed letterhead, metadata, raw-material table and page-format mismatches corrected. |
| Purchase Requisition | `Indent/view.ctp` | Confirmed landscape format, session-user, category mapping, totals and signature-layout mismatches corrected. |
| GRN Inspection print screens | Inspected existing Next print routes and available PHP inspection views | No corresponding completed legacy PDF template was identified. These screens were left unchanged; their parity is unverified. The inspection screen's Purchase Order print route was also preserved. |

Legacy-only PDF/report outputs without a current Next PDF entry point were not newly implemented. This work corrects the existing migrated PDF flows; it does not claim that every historical PHP report has been migrated.

## 2. PDFs already working

Purchase Order was the accepted working baseline. No Purchase Order template, API, query, service, print page or download handler was edited by this task. Purchase Order and unrelated files already had local changes before the audit; those changes were preserved.

None of the seven other matching migrated modules was considered fully correct at the start of the audit.

## 3. Design issues corrected

- Restored each module's title, metadata labels, section order, fonts, alignment, borders and column proportions from its own PHP template.
- Restored A4 portrait for the matching portrait templates and A4 landscape for Purchase Requisition. PHP's effective page settings, rather than unused PDF constructors, were followed.
- Restored tenant letterheads and logos for the templates that use them. An absent tenant logo is not replaced with a different company's logo.
- Restored Indent and Reverse Indent signature wording and spacing, GRN totals/remarks/signatures, Contract production and inspection sections, and both distinct Job Challan formats.
- Removed conflicting browser-only PDF layouts from the migrated print routes. The existing GRN download URL now proxies the backend PDF instead of generating a second, hardcoded React-PDF design.
- Checked repeated table headers, page breaks and page count. Contract 84 renders four pages in both PHP and Next, with the same last material row on pages 1, 2 and 3. Small renderer differences remain possible in glyph metrics and border positioning; this is not a pixel-identical certification across every record.
- Did not add Purchase Order Terms & Conditions to other documents. No additional Terms & Conditions section was present in the seven matched source templates.

## 4. Missing data corrected

| Module | Correction |
| --- | --- |
| Contract | Added production-process rows, prepared quantities, production-derived labour/operational costs, raw-material group names and issued subitems. |
| Indent PO | Included all stock rows linked to the indent, matching PHP instead of silently restricting rows to one stock type. |
| Reverse Indent | Included all stock rows linked to the reverse indent, matching PHP. |
| Job Challan | Loaded the recipient and GST from `sub_contractors`, unit from the measurement master, and company settings from the actual source tenant. Vendor IDs had previously been incorrectly interpreted through the vendor table. |
| GRN | Loaded order quantity from the latest matching PO item, unit from the measurement master, tax labels from the tax master and tenant company details. Removed the default 18% tax and hardcoded company information. |
| Design Sheet | Uses the existing detail query, with stable detail-row ordering, and the actual design-sheet/company fields. |
| Purchase Requisition | Corrected category joins to `st_categorymaster`, including the temporary-preview query, and prints the current authenticated user's name as PHP does. |

Some existing Purchase Requisition rows reference item masters that are absent. Their item/category labels are blank in both the old PHP output and the corrected PDF. No records were inserted or repaired to conceal this data condition.

## 5. Logic and API issues corrected

- Contract prepared quantities use process 8 and both shifts, as in PHP. Process rows use ID-ordered first/last production dates and unique PO numbers. Labour sums day/night manpower; operational cost sums meter differences. These computed PDF fields do not overwrite the contract's stored edit-form fields.
- Contract material quantities sum duplicate design detail rows. Issued totals subtract returns, retain negative pending values and show nonzero category subitems. Individual material stock totals retain rows even when the item master is absent.
- GRN totals use saved stock-register `cost_price`, `tax` and `amount` values plus freight. They are not taxed a second time. Tax-included/excluded wording follows PHP's last-row comparison. Cancelled stock rows are filtered as in the source. Indian amount words follow PHP's numbering and fractional handling.
- Job Challan prints the first source item and uses the legacy GST-state split. The sister-company format preserves a truthy saved amount and uses charges plus tax only when that saved amount is zero, matching PHP.
- Job Challan recipient dropdown, GST lookup, list mapping and recipient validation now use the subcontractor reference consistently. The list query excludes model-only columns absent from the existing legacy table, allowing the PDF navigation list to load.
- Job Challan PDF source selection supports the current tenant and linked sister-company databases. An unlinked `sender_db` is rejected; missing PDFs return 404.
- Dates preserve the stored MySQL calendar date and timestamp rather than shifting with the server's local timezone.
- Added authenticated PDF endpoints for Design Sheet, GRN and Purchase Requisition; retained the existing module endpoints and GRN Next download URL. Blob handling returns PDF bytes correctly and releases browser resources after generation.

## 6. Files changed by this task

Backend:

- `backend/src/utils/legacyPdf.js` — new shared rendering and legacy letterhead helpers.
- `backend/src/modules/contract/contract.pdf.js`
- `backend/src/modules/contract/contract.repository.js`
- `backend/src/modules/contract/contract.service.js`
- `backend/src/modules/indentpo/indentpo.pdf.js`
- `backend/src/modules/indentpo/indentpo.repository.js`
- `backend/src/modules/reverseIndent/reverseIndent.pdf.js`
- `backend/src/modules/reverseIndent/reverseIndent.service.js`
- `backend/src/modules/jobChallan/jobChallan.pdf.js`
- `backend/src/modules/jobChallan/jobChallan.subcontractor.pdf.js` — new separate sister-company template.
- `backend/src/modules/jobChallan/jobChallan.service.js`
- `backend/src/modules/jobChallan/jobChallan.controller.js`
- `backend/src/modules/grn/grn.pdf.js` — new legacy GRN template.
- `backend/src/modules/grn/grn.repository.js`
- `backend/src/modules/grn/grn.service.js`
- `backend/src/modules/grn/grn.controller.js`
- `backend/src/modules/grn/grn.routes.js`
- `backend/src/modules/designsheet/designsheet.pdf.js` — new legacy Design Sheet template.
- `backend/src/modules/designsheet/designsheet.controller.js` — PDF response and row ordering only, alongside existing user changes.
- `backend/src/modules/designsheet/designsheet.routes.js` — PDF route only, alongside existing user changes.
- `backend/src/modules/indent/indent.pdf.js` — new legacy Purchase Requisition template.
- `backend/src/modules/indent/indent.repository.js`
- `backend/src/modules/indent/indent.controller.js`
- `backend/src/modules/indent/indent.routes.js`
- `backend/tests/pdf-parity.test.cjs` — six focused in-memory regression tests.

Frontend:

- `frontend/services/pdf.service.ts` — authenticated native PDF viewer helper.
- `frontend/components/ModulePdfPreview.tsx` — shared authenticated preview loader, without a generic PDF design.
- `frontend/app/api/purchase/grn/[id]/pdf/route.ts`
- `frontend/components/purchase/grn/GrnPdf.tsx` — removed obsolete, unused hardcoded template.
- `frontend/app/dashboard/purchase/grn/view/[id]/page.tsx`
- `frontend/app/dashboard/purchase/indentpo/[id]/page.tsx`
- `frontend/app/dashboard/purchase/indentpo/page.tsx`
- `frontend/app/dashboard/purchase/indents/[indent_id]/page.tsx`
- `frontend/app/dashboard/reverse/[id]/page.tsx`
- `frontend/app/dashboard/jc-challan/[id]/page.tsx`
- `frontend/app/dashboard/jc-challan/[id]/pdf/page.tsx` — new preview route.
- `frontend/app/dashboard/design-sheet/print/[designsheetno]/page.tsx`
- `frontend/app/dashboard/production/viewcontractdetailspdf/[id]/page.tsx`
- `docs/pdf-migration-audit.md` — this report.

Temporary read-only audit scripts and rendered comparison PDFs/images were created under `tmp/pdf-audit` during verification. They are not application changes. Raw database snapshots were removed after the comparisons.

## 7. Verification and remaining issues

Completed checks:

- Regenerated the original PHP templates offline with TCPDF using existing-record snapshots and read-only helper lookups; regenerated the Next PDFs from the corresponding Sequelize data. Visually compared the page images and inspected PDF text, dimensions and page boundaries.
- Compared Contract 130, Contract 122 with an existing production order, and four-page Contract 84; Indent 4781; Reverse Indent 1088; Job Challan 111; GRN 2314; Design Sheet 1001; Purchase Requisition 1001.
- All seven matching module endpoints returned authenticated HTTP 200 with PDF bytes and rejected unauthenticated requests with 401. Missing GRN returned 404; an unlinked Job Challan sender returned 403.
- The restarted local backend and the existing Next GRN download proxy both returned HTTP 200 with PDF bytes.
- Six regression tests passed, covering production aggregation, returns/negative quantities, saved totals and GST-state splits, dates, missing/zero tax data, escaping and Indian amount words. Fixtures exist only in memory; no testing database records were added.
- All 24 affected backend source files passed syntax checks. The six newly created/replaced frontend PDF helper/preview/route files passed targeted ESLint.

Remaining limits:

1. GRN Inspection has no identified completed PHP PDF counterpart; its print screens remain unchanged and unverified. Legacy-only reports with no current Next PDF entry point are also outside this completed correction.
2. No existing sister-company challan was available for live source-tenant validation. Its layout and tax logic were checked offline using existing item values; production-process aggregation also has regression coverage, but the compared live Contract samples did not contain production rows.
3. Existing orphaned Purchase Requisition item references remain in the database. Their blank labels match PHP; this task did not alter records.
4. The full frontend type check is still blocked by pre-existing errors in Purchase Order add, Reverse Indent add, ContractDetailsModal, DashboardHeader, DeliveryNoteModal, schedules and PermissionContext. No type errors were reported in the changed PDF code. A clean whole-project build is not claimed.
5. Existing Job Challan create/model schema differences outside PDF generation remain (the legacy table lacks several fields declared by the current model). No creation, update, deletion or schema migration was executed as part of PDF validation.
6. Chromium and TCPDF have slightly different text/border metrics. Sample data, module structure, page size/orientation and the audited multi-page boundaries were compared; untested record lengths or tenants still need acceptance review before claiming universal pixel parity.

No database records were created, updated or deleted. No PHP source files were edited by this task. The local backend was restarted to load the corrected PDF code.
