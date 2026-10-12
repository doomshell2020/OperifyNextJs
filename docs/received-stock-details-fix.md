# Received Stock Details repair

## Follow-up: quantity did not open for the live user

Browser inspection on 12 October 2026 confirmed that Brijkiran's COPPER ROD (CCR), product 6, had an 80.00 received quantity rendered as plain text. Direct navigation to the implemented receipt page showed "You do not have permission to access this page." The same user's PHP page displayed PO 2627-2, revision 1, PO primary key 2856, inward date 12-10-2026 and bill 1001. The separate `stockregister/receivedstock` grant was incorrectly required for a drill-down that PHP exposes from the stock index.

The received quantity, direct page guard and API now accept either the existing `stockregister/index` or `stockregister/receivedstock` grant. Missing permissions and unrelated/daily-stock grants still deny receipt access. No saved role grants or database records are changed. Receipt PDF visibility now accepts `purchaseorder/index` as well as `purchaseorder/view`, matching the existing PDF endpoint. Regression tests render the actual index and details components and check the direct-page/API permission combinations, including index-only access.

## Root cause

The migration implemented Received Stock as `StockDetailModal`, with no dedicated Next.js details route. The modal omitted the PHP DataTable search/pagination and PO PDF link. The received-detail SQL selected `sr.sub_contractors_id` and joined `sub_contractors`; these do not exist in the tenant schema. That made receipt retrieval fail before rendering the PO/GRN data. The modal also displayed inward date under Bill Date.

The PHP source reviewed was `StockregisterController::receivedstock`, `StockregisterTable`, `receivedstock.ctp`, `searchstockregister.ctp`, and the `example14` DataTable initialization in the admin footer. PHP links open a separate tab, use five PO receipt columns, fixed ten-row paging, search, no user sorting, and descending stock register ID order.

## Implemented

- Received quantities link to `/dashboard/inventory/stock/received/[date]/[productId]` in a separate tab, preserving the index's search results.
- Dedicated page displays the item name retrieved from the tenant database and the five PHP PO receipt columns. It includes search, ten-row pagination, entry counts, loading, empty and error states.
- Corrected Sequelize raw SQL keeps bound replacements and joins `goods_id` to the GRN primary key and `purchaseorder_id` to the exact PO primary key. Vendor information comes from the GRN vendor. PO date, revision count, bill number and stored bill date are returned.
- PDF buttons reuse the existing authenticated PO PDF service with the exact PO primary key, so an older receipt cannot silently open the latest revision. PDF access still requires `purchaseorder/view`; details access still requires `stockregister/receivedstock`, including direct page navigation.
- Product/date validation returns HTTP 400 for invalid filters; a missing product returns 404. The existing API array response remains compatible, with product metadata added alongside it.
- No database writes, model/schema migrations, stock-calculation changes, tenant switching changes, or permission expansions were made. Existing company/branch session routing and stock filters remain in place.

## Legacy inconsistencies deliberately corrected

The current PHP summary (and current Next.js summary) counts stock types 0/1/3 using delivery date, then issue date, then creation date. PHP's older details action instead restricts type 1 by creation date and INNER JOINs PO/GRN. This means PHP details can be empty or incomplete despite a positive summary quantity. All 20 sampled selections differed from that legacy creation-date query. The new details query uses the current index's receipt-date and stock-type criteria, preserving the displayed stock calculations and making its rows reconcile with the clicked quantity. Records lacking PO/GRN links remain visible with blank document fields instead of being discarded.

The PHP PO table prints GRN inward date in the Bill Date column. The new page uses `st_goodsreceive.bill_date`, as requested. None of the sampled records had differing bill/inward dates.

The PHP JC fallback depends on subcontractor fields missing from this database and cannot be copied literally. The new query does not invent JC or vendor relationships for stock rows without PO/GRN links.

## Validation

`node backend/test_received_stock.cjs` passed against existing `tirupati_tppl` records using SELECT queries only:

- 20 product/date selections; 22 receipts, including 20 revised PO receipts.
- Receipt fields matched independently queried stock/GRN/PO/vendor joins.
- Receipt quantities reconciled with existing stock-register summary totals.
- The real controller returned the expected records and tenant product metadata.
- Existing PO PDF data lookup returned the exact primary key and amendment count for every linked receipt.
- Invalid date, impossible calendar date and invalid product ID were rejected; an empty historical date returned no records.
- The comparison to the literal PHP details query identified the legacy date discrepancy above.

`npm run check:backend` passed. `npm run check:permissions` passed, including 1,260 backend grant/route checks. `npm --prefix frontend run build` passed compilation, TypeScript checking and route generation. `git diff --check` passed.

Interactive browser search/paging and actual PDF rendering/download were not exercised. The old PHP application was not run; parity was assessed from its source and equivalent read-only queries against existing records. Full literal legacy row equality is intentionally not claimed because of the documented date/type discrepancy.

## Files

- `backend/src/modules/stockRegister/stockRegister.repository.js`
- `backend/src/modules/stockRegister/stockRegister.service.js`
- `backend/src/modules/stockRegister/stockRegister.controller.js`
- `frontend/services/stockRegister.service.ts`
- `frontend/app/dashboard/inventory/stock/page.tsx`
- `frontend/app/dashboard/inventory/stock/received/[date]/[productId]/page.tsx`
- `frontend/components/ui/LegacyRouteAccess.tsx`
- `backend/test_received_stock.cjs`
- `docs/received-stock-details-fix.md`
