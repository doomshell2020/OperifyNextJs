# GRN Inspection and GRN manager corrections

Compared with the local PHP Goodsreceived index, grninspection, viewgrndetail and view templates; Purchaseorder viewpodetail and viewpodetailspdf templates; and their controllers and CommanHelper queries.

## Result

- GRN Inspection PO ID opens the corresponding PO popup, including Print PDF.
- GRN Action contains Print PDF and Download PDF. GRN No. opens GRN details; PO ID opens PO details. Both popups include Print PDF.
- Popups use the PHP table layout and include supplier/GST, dates, bill references, quantities, prices, taxes and totals. PO details also include received GRNs, amendment data and delivery schedules.
- PO popup printing uses the dedicated Purchase Order Details document, rather than the standard Purchase Order document. Its header, Products, Goods Received Note and Delivery Schedule sections follow the PHP details PDF. Existing GRN and standard PO print templates remain available.

## Root causes and corrections

1. Separate detail/print grants blocked functionality exposed to authorized PHP manager readers. Read routes and controls now accept the corresponding manager grants, while authentication, assigned-company selection and tenant routing remain enforced. This grants read/print access only; Add/Edit/Delete, revision and delivery actions retain separate guards.
2. PO links passed formatted PO numbers, but printing converted them to numeric IDs. Lists now return the latest non-deleted PO record ID using parameterized Sequelize queries; popup printing uses the loaded PO's actual record ID.
3. PO receiving sections queried inspection tables instead of st_goodsreceive/st_stock_register. They now load the actual received goods, stored receipt prices, tax and amounts. Pending quantities are rounded to two decimals. Product prices follow PHP's quantity multiplied by rate, with rates resolved through the stored tax ID.
4. GRN details substituted received quantities for ordered quantities, invented an 18 percent fallback, recalculated receipt amounts, and added header totals incorrectly. The popup now uses actual ordered/received quantities, tax-master values, stored stock prices/amounts, and the sum of line amounts. The standalone GRN page also avoids adding tax twice.
5. GRN downloads bypassed the shared API client's expired-token refresh/session protection and revoked download URLs immediately. Print/download now use the configured authenticated API client; the browser preview is opened synchronously and object URLs remain available during loading.

New API: GET /api/purchase-orders/:id/details/pdf.

## Verification

- Next.js production build and TypeScript passed.
- Backend JavaScript syntax checks passed.
- Permission regression suite passed, including 1,260 base grant/route checks, contextual receiving/detail/PDF grants, denied unrelated grants, and print-mode isolation.
- Actual modal print handlers tested with a formatted PO number: the request uses the loaded numeric record ID and the dedicated details PDF.
- PDF service tests passed for print/download, unexpected response types, HTTP failures, popup blocking, download URL lifetime, dedicated details routing and existing standard PO routing.
- Existing revision/delivery/cache regression suite passed.
- Read-only comparisons against tirupati_tppl and tirupati_kcpl passed for receiving lists, selected PO records, receipt stock items, ordered quantities, rates, prices and amounts.
- Authenticated HTTP detail and PDF requests passed; nonexistent GRNs returned 404 and unauthenticated PDF requests returned 401.
- Browser checks passed for inspection PO popup/Print PDF, GRN PO popup/Print PDF, GRN number popup/Print PDF, Action Print PDF and Download PDF. The downloaded PDF's extracted content matches the independently verified backend document.
- Existing revised PO with five delivery schedule rows verified in the browser and rendered PDF. Corrected price, tax and pending-quantity output inspected visually.
- GRN output compared visually with the available legacy GRN PDF. PO details layout checked against the PHP details template. Pixel-for-pixel equivalence across every record and multipage case is not claimed.
- No business data, permission records or schema were changed. No INSERT, UPDATE or DELETE was used for test data.

## Files changed

Backend:

- backend/src/modules/grn/grn.repository.js
- backend/src/modules/grn/grn.routes.js
- backend/src/modules/grnInspection/grnInspection.repository.js
- backend/src/modules/purchaseOrder/purchaseOrder.controller.js
- backend/src/modules/purchaseOrder/purchaseOrder.repository.js
- backend/src/modules/purchaseOrder/purchaseOrder.routes.js
- backend/src/modules/purchaseOrder/purchaseOrder.details.pdf.js (new)

Frontend:

- frontend/app/dashboard/legacy.css
- frontend/app/dashboard/purchase/grn/page.tsx
- frontend/app/dashboard/purchase/grn/view/[id]/page.tsx
- frontend/app/dashboard/purchase/inspections/page.tsx
- frontend/components/GrnDetailsModal.tsx
- frontend/components/PurchaseOrderDetailsModal.tsx
- frontend/components/ReceivingDetailsTable.tsx (new)
- frontend/components/ui/LegacyRouteAccess.tsx
- frontend/services/grnPdf.service.ts
- frontend/services/purchaseOrderPdf.service.ts

Checks and documentation:

- scripts/test-permissions.cjs
- scripts/test-grn-pdf.cjs (new)
- docs/GRN_MANAGER_FIX_REPORT.md (new)

Local read-only acceptance scripts and screenshots are under tmp/grn-manager-check and tmp/check-grn-*.cjs; they are ignored verification artifacts. Changes are local and have not been deployed.
