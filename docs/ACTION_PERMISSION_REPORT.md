# Action permission correction report

## Root cause

The existing session already loaded allowed role URLs, but shared frontend and API checks treated an absent permission label as permission to act. Both layers also bypassed action checks for an administrator role. Several Users, Reverse Indent, Stock, dashboard and hover controls lacked checks. PO displayed an Action menu even when every operation was denied. Substring-based URL aliases could match a different operation, and the permission editor used tenant tables while authentication read central tables.

## PHP source of truth

- `src/View/Helper/PermissionHelper.php`: read the logged-in user's role, select central `permission_access` entries with `is_permission = 1`, and resolve `p_lable_id` to `permission_label.url`. The helper has no blanket administrator action bypass. Managers group labels; grouping does not grant every action in that manager.
- `Admin/PermissionController.php`: stores role-to-label grants. `Admin/PermissionmodulesController.php` and `CommanHelper::finduserpermisson` implement the older tenant `permission_module` records keyed by user/controller/action, with individual edit/delete flags. Those records are not extra role URL grants and were not merged into them.
- The current header and migrated manager templates use role URL checks. Older user-specific Edit/Delete consumers include managers whose transactional screens have not been implemented in Next.js. The PHP per-user `beforeFilter` authorization call is commented out; the older `check_permission` / `checkfinalpermission` functions have no active callers in the supplied controllers.
- Indentpo row Edit/Delete require today's issue date in Asia/Kolkata. Its PHP Action cell has no Print button. Contract and Design Sheet dependency restrictions remain intact. Roles uses `roles/add` for both creating and editing users. EMD details use `emd/viewamount`.

## Files and shared components changed

Core frontend changes: `contexts/PermissionContext.tsx`, `contexts/AuthContext.tsx`, `components/ui/useLegacyActionAccess.ts`, `components/jobChallan/useJcAccess.ts`, new `components/ui/LegacyRouteAccess.tsx`, and new `utils/legacyActionDate.ts`.

Manager/detail controls changed under `app/dashboard`, including Indentpo, PO, Users, Suppliers, Reverse, EMD, Stock and Daily Stock. Contract and Gate Pass edit forms now have separate authorized data reads. Shared dashboard/header, PO hover/link, vendor modal and JC list components were corrected; nonfunctional placeholder action buttons were removed. `hooks/useDashboard.ts` now requests manager rows only with the corresponding View grant.

Backend changes: `middleware/permission.js`, new `middleware/legacyActionDate.js`, `modules/jobChallan/legacyPermission.js`, `modules/auth/auth.repository.js`, the permission controller, and relevant routes under Contract, Design Sheet, PO, GRN Inspection, Indentpo, JC, Gate Pass, Reverse Indent, Stock, Settings, Vendor, EMD, Payment and Dashboard. Existing GRN, Indent, Quotation and JC Receive guards inherit the corrected shared enforcement.

## Coverage and backend enforcement

Coverage includes Contract, Design Sheet, PO, GRN Inspection, GRN, both Indent managers, JC Challan, JC Receive, Gate Pass, Stock Register, Daily Stock, Products, Categories, Suppliers, Users, Reverse Indent, EMD, Payments and Quotation. Production, Daily Sheet, Maintenance and other existing placeholder screens have no transactional actions to authorize; their existing access boundaries remain applicable.

API actions require their specific saved legacy URL grant. Missing grants deny with HTTP 403, including missing labels and direct requests. Print modes, revision, delivery notes, receive, subcontractor creation, exports and status changes do not inherit the list permission. Independent Design Sheet row removal checks `designsheet/deletedata`. Indent Edit/Delete also check the persisted tenant issue date. Form reads use their owning action; lookup reads reused by authorized forms remain reusable.

Authentication still reloads current grants on protected requests. Authenticated identity, assigned-company checks, selected tenant routing and linked-branch validation remain in place. Browser permissions refresh on navigation/focus. The permission editor now reads/writes the same central tables as authentication, but no editor mutation was executed during this task. No permission or database records were modified.

## Validation and remaining differences

- `npm run check:permissions`: passed 1,266 backend grant/route checks, exact role-label loading, print-mode isolation and persisted-date restrictions. Fixtures cover no permissions, View only, View+Edit, Add+View, View+Delete and full standard action grants.
- Rendered actual rows from 12 frontend managers with read-only fixtures. Verified action visibility, blank cells, historical Indent restrictions, empty PO menus, and direct form access.
- `npm run check`: backend syntax checks and frontend production build passed.
- Live PHP/Next.js comparison using the same real accounts was **not performed**. Database-backed company/branch execution and browser sessions with real permission combinations still need acceptance testing. Test fixtures do not establish live-user parity.
- PHP contains public/unguarded helper actions. Next.js requires saved action grants instead of reproducing those security gaps, as requested. Unconfigured actions are denied. Existing incomplete transactional modules were not implemented as part of this correction.

Regression checks are in `scripts/test-permissions.cjs`; they do not connect to or modify a database.

## Follow-up: missing job-work navigation

The PHP header explicitly leaves JC Challan, JC Receive and Gate Pass navigation unguarded. Applying action permission checks to these menu links hid them for users without those URL grants. The header now preserves PHP's menu visibility for signed-in users in its ordinary ERP navigation. Page/list access, row actions and API operations still require their existing saved grants. A navigation regression test verifies that all three links appear with an empty grant list and that this does not grant any protected action. No permission records were added or changed.
