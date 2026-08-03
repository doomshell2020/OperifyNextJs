import fs from "node:fs/promises";
import { SpreadsheetFile, Workbook } from "@oai/artifact-tool";

const outputDir = "D:/operify.in/outputs/code_review_bug_sheet";
await fs.mkdir(outputDir, { recursive: true });

const rows = [
  [
    "Bug ID",
    "Module",
    "File Location",
    "Issue Title",
    "Issue Description",
    "Impact",
    "Priority",
    "Suggested Fix",
    "Risk If Not Fixed",
  ],
  [
    "BUG-001",
    "Environment / Configuration",
    "D:/operify.in/config/app.php:12; D:/operify.in/config/bootstrap.php:261-262",
    "Debug mode defaults to enabled",
    "The application uses env('DEBUG', true), so production runs with debug enabled when DEBUG is not explicitly configured. DebugKit is also loaded whenever debug is true.",
    "Runtime errors, stack traces, SQL details, and debug tooling can be exposed in production.",
    "High",
    "Default DEBUG to false, require explicit local override, and ensure DebugKit is not installed or loaded in production.",
    "Sensitive internals may leak and attackers gain easier reconnaissance from verbose errors.",
  ],
  [
    "BUG-002",
    "Security / JWT / Cookies",
    "D:/operify.in/config/app.php:67",
    "Static fallback security salt is committed",
    "Security.salt has a hard-coded fallback value. This value signs hashes/JWTs and can be reused if SECURITY_SALT is missing.",
    "JWTs, remember-me cookies, and signed values become forgeable if the source or default config is exposed.",
    "Critical",
    "Remove the fallback secret, require SECURITY_SALT from environment, and rotate all tokens/sessions after changing it.",
    "Attackers can mint or tamper with signed authentication data.",
  ],
  [
    "BUG-003",
    "Secrets / Messaging",
    "D:/operify.in/config/bootstrap.php:63-66; D:/operify.in/src/Controller/AppController.php:447,483,760-763; D:/operify.in/src/Controller/Component/EmailComponent.php:100-113; D:/operify.in/src/Controller/Component/WhatsAppComponent.php:18; D:/operify.in/src/Controller/CronController.php:59-62",
    "SMTP and WhatsApp credentials are hard-coded",
    "SMTP passwords, WhatsApp tokens, and API keys are embedded directly in PHP source/config.",
    "Secrets can be leaked through source access, backups, logs, or accidental deployment exposure.",
    "Critical",
    "Move all credentials to environment variables or a secrets manager, revoke the exposed credentials, and rotate them.",
    "Compromised mail/API accounts can send fraudulent messages, leak data, or get the domain blacklisted.",
  ],
  [
    "BUG-004",
    "Public Webroot / Cloud Credentials",
    "D:/operify.in/webroot/operify-cad4a-d47af56c4f8a.json; D:/operify.in/src/Controller/AppController.php:660",
    "Google service account file is stored under public webroot",
    "The Firebase service account JSON is referenced from WWW_ROOT, placing a credential file in the public document tree.",
    "A direct web request or misconfigured server can expose cloud private keys.",
    "Critical",
    "Move the service account file outside webroot, load its path from environment, restrict filesystem permissions, and rotate the service account key.",
    "Cloud project access can be compromised, including FCM abuse and access to any scopes granted to the service account.",
  ],
  [
    "BUG-005",
    "Authentication Bootstrap",
    "D:/operify.in/src/Controller/AppController.php:58-76; D:/operify.in/src/Controller/Api/AppController.php:35-56",
    "Auth setup reads missing request fields directly",
    "initialize() accesses request->data['email'], ['mobile'], and ['password'] without checking whether those keys exist.",
    "GET requests and incomplete form/API submissions trigger PHP notices/warnings; on newer PHP versions this can break request handling or leak debug output.",
    "Medium",
    "Use getData()/getData('field') with null checks and configure authentication independent of raw array offsets.",
    "Login and public pages can produce noisy runtime errors and unstable auth behavior.",
  ],
  [
    "BUG-006",
    "Permission Enforcement",
    "D:/operify.in/src/Controller/AppController.php:291-296",
    "Permission check dereferences null records",
    "checkfinalpermission() assumes PermissionModules and Permissions queries always return rows, then reads $moduleId['id'] and $modulePermission['permission'].",
    "Routes without permission rows or deleted permission records can crash with null offset errors instead of denying access.",
    "High",
    "Handle missing module/permission records explicitly and fail closed with an authorization error or redirect.",
    "Users can hit runtime failures, and authorization behavior becomes unpredictable.",
  ],
  [
    "BUG-007",
    "Login / SQL / Authentication",
    "D:/operify.in/src/Controller/LoginsController.php:75-89",
    "Login performs raw SQL with mobile and password",
    "newindex() builds SELECT * FROM users using unsanitized mobile/password and compares against confirm_pass before switching tenant DB.",
    "SQL injection and plaintext password dependency in the login path.",
    "Critical",
    "Use ORM/query builder with bound parameters and authenticate only against hashed password fields.",
    "Attackers can bypass login, extract data, or manipulate tenant selection.",
  ],
  [
    "BUG-008",
    "Login / Runtime Error Handling",
    "D:/operify.in/src/Controller/LoginsController.php:78-80",
    "Login assumes lookup returns a user row",
    "$run_data['db'] is accessed immediately after fetch('assoc') without checking for false/null.",
    "Invalid credentials can produce runtime warnings/errors before the normal invalid-login branch.",
    "High",
    "Check whether $run_data exists before reading db and return a generic invalid-login response.",
    "Failed login attempts may crash or reveal implementation details.",
  ],
  [
    "BUG-009",
    "Remember Me / Authentication",
    "D:/operify.in/src/Controller/LoginsController.php:118-121; D:/operify.in/src/Controller/LoginsController.php:186-189",
    "Remember-me stores plaintext password in a cookie",
    "When remember_me is selected, the submitted password is written to a client cookie and later read back into the login view.",
    "Anyone with browser/cookie access can recover the user's password.",
    "Critical",
    "Replace with a random server-side remember token stored hashed in the database; never store raw passwords client-side.",
    "Account takeover is possible from cookie theft, browser compromise, or shared devices.",
  ],
  [
    "BUG-010",
    "Password Reset",
    "D:/operify.in/src/Controller/LoginsController.php:20,278-319",
    "Password reset flow is incomplete and weak",
    "forgot() generates rand(1,10000), stores fkey via raw SQL, has no expiry, and beforeFilter allows setpass even though no setpass action exists in this controller.",
    "Reset links are brute-forceable and may route to missing actions, causing failures or insecure reset behavior.",
    "High",
    "Use cryptographically secure random tokens, hash tokens at rest, add expiry/single-use checks, bind SQL parameters, and implement/lock down the reset action.",
    "User accounts can be reset by guessing weak tokens or the reset flow can fail at runtime.",
  ],
  [
    "BUG-011",
    "Mobile API Authorization",
    "D:/operify.in/src/Controller/MobileController.php:25-57",
    "Most mobile API endpoints are publicly allowed",
    "beforeFilter() allows dashboard, purchaseorder, grn, stock, reports, PDFs, and search endpoints without requiring JWT/session authentication.",
    "Unauthenticated users can call business-data endpoints directly.",
    "Critical",
    "Only allow login/version/public endpoints; require JWT auth for data endpoints and verify tenant/user authorization on every request.",
    "ERP data can be exposed or modified without login.",
  ],
  [
    "BUG-012",
    "Mobile API / Tenant Isolation",
    "D:/operify.in/src/Controller/MobileController.php:72-96; D:/operify.in/src/Controller/MobileController.php:249-274",
    "Client-supplied erpID selects database connection",
    "db($dbs) switches the default connection to whatever database name is supplied by the request, and uploadToken accepts erpID directly.",
    "A caller can attempt cross-tenant database access or trigger connection failures by supplying arbitrary database names.",
    "Critical",
    "Resolve tenant database from the authenticated user/session, validate against an allowlist, and reject client-supplied database identifiers.",
    "Cross-company data exposure and unstable database routing can occur.",
  ],
  [
    "BUG-013",
    "Legacy API Authentication",
    "D:/operify.in/src/Controller/Api/MobileController.php:16-66,100-149",
    "Legacy API uses hard-coded DB credentials and plaintext password comparison",
    "beforeFilter() hard-codes the tpplerp database credentials and allows most endpoints; login compares the submitted password to users.confirm_pass.",
    "Authentication bypass risk, plaintext password exposure, and environment-specific runtime failures.",
    "Critical",
    "Remove hard-coded credentials, configure through environment, require JWT for protected routes, and validate passwords with DefaultPasswordHasher.",
    "A leaked or guessed plaintext password grants API access; deployment breaks when the hard-coded DB is unavailable.",
  ],
  [
    "BUG-014",
    "Company Admin / Database Provisioning",
    "D:/operify.in/src/Controller/Admin/UsersController.php:230-248,360-380",
    "Database and user SQL are built from form input",
    "add() concatenates school_database into SHOW DATABASES/CREATE DATABASE/exec mysql commands and inserts/updates users through raw SQL strings built from request data.",
    "SQL injection, command injection, malformed database names, and failed provisioning are possible.",
    "Critical",
    "Validate database names with a strict allowlist pattern, quote identifiers safely, remove shell exec from request flow, and use bound parameters/ORM for data writes.",
    "An admin form submission can execute unintended SQL/commands or corrupt tenant databases.",
  ],
  [
    "BUG-015",
    "Change Password",
    "D:/operify.in/src/Controller/Admin/UsersController.php:71-95",
    "Change password does not verify current password",
    "changepassword() only checks new_password equals confirm_pass; it never validates the user's existing password before updating credentials.",
    "A hijacked session or unattended authenticated browser can change the account password without knowing the old password.",
    "High",
    "Require current password, verify it with DefaultPasswordHasher, then update only through ORM/bound statements.",
    "Attackers with temporary session access can permanently take over accounts.",
  ],
  [
    "BUG-016",
    "Cron / Backups",
    "D:/operify.in/src/Controller/CronController.php:29-34,50-62,132-162",
    "Database backup endpoints are publicly allowed",
    "beforeFilter() allows dataBaseBackup and sendDatabaseBackup; the actions enumerate databases and dump tables, then email backups using hard-coded SMTP credentials.",
    "Unauthenticated backup generation can leak full databases and consume heavy server resources.",
    "Critical",
    "Protect cron endpoints with CLI-only execution or a strong secret, restrict database scope, and move mail credentials to environment variables.",
    "Full database exfiltration and denial of service are possible.",
  ],
  [
    "BUG-017",
    "File Upload API",
    "D:/operify.in/src/Controller/Api/AppController.php:71-100",
    "Upload helper is public and has broken file handling",
    "move_images/upload_images are Auth-allowed, accept caller-controlled paths, use end(explode(...)) by reference, reference undefined $i/$kk, and move_uploaded_file() writes to $folder instead of the generated filename.",
    "Runtime warnings, failed uploads, arbitrary-path write risk, and unvalidated file storage.",
    "High",
    "Remove these helpers from public routes, validate MIME/extension/size, generate server-side destination paths, and initialize variables.",
    "Attackers can abuse upload paths or users experience intermittent upload failures.",
  ],
  [
    "BUG-018",
    "Inspection Upload",
    "D:/operify.in/src/Controller/Admin/InspectionController.php:49-59",
    "Inspection documents are uploaded without validation",
    "add() accepts doc_upload name/tmp_name and moves it into WWW_ROOT/InspectionReport using the original extension-derived filename without checking upload errors, MIME type, allowed extension, or size.",
    "Malicious or oversized files can be stored under the public webroot.",
    "High",
    "Validate upload error/size/MIME/extension, store files outside webroot when possible, and serve downloads through an authorized controller.",
    "Public file exposure, storage abuse, or executable-file risk under permissive server configs.",
  ],
  [
    "BUG-019",
    "Public Webroot",
    "D:/operify.in/webroot/.htaccess:1-7; D:/operify.in/webroot/admin/images/tacher_horizontal.phtml; D:/operify.in/webroot/webfonts/Open_Sans/static/OpenSans_Condensed/OpenSans_Condensed-ExtraBoldItalic.phtml",
    "Public executable file rules and files are suspicious",
    "webroot/.htaccess denies py/exe/php generally but explicitly allows suspicious PHP filenames, while .phtml files exist under public asset directories.",
    "Public executable artifacts can become remote-code-execution entry points if server handler rules permit them.",
    "Critical",
    "Remove suspicious public executable files and allow-list entries, audit for compromise, and block PHP/PHTML execution in upload/static directories.",
    "Attackers may execute server-side code from the public document tree.",
  ],
];

const workbook = Workbook.create();
const sheet = workbook.worksheets.add("Bug Sheet");
sheet.showGridLines = false;
sheet.getRangeByIndexes(0, 0, rows.length, rows[0].length).values = rows;

sheet.freezePanes.freezeRows(1);
const table = sheet.tables.add(`A1:I${rows.length}`, true, "BugSheet");
table.style = "TableStyleMedium2";
table.showFilterButton = true;

sheet.getRange("A1:I1").format = {
  fill: "#1F4E78",
  font: { bold: true, color: "#FFFFFF" },
};
sheet.getRange(`A2:I${rows.length}`).format = {
  wrapText: true,
  verticalAlignment: "top",
};
sheet.getRange(`A2:A${rows.length}`).format = { horizontalAlignment: "center" };
sheet.getRange(`G2:G${rows.length}`).format = { horizontalAlignment: "center" };
sheet.getRange(`G2:G${rows.length}`).dataValidation = {
  rule: { type: "list", values: ["Critical", "High", "Medium", "Low"] },
};

const widths = [12, 24, 54, 34, 70, 48, 12, 70, 56];
for (let i = 0; i < widths.length; i++) {
  sheet.getRangeByIndexes(0, i, rows.length, 1).format.columnWidth = widths[i];
}
sheet.getRange("A1:I1").format.rowHeight = 32;
sheet.getRange(`A2:I${rows.length}`).format.rowHeight = 82;
sheet.getRange(`A1:I${rows.length}`).format.borders = {
  preset: "inside",
  style: "thin",
  color: "#D9E2F3",
};

const inspected = await workbook.inspect({
  kind: "table",
  range: `Bug Sheet!A1:I${rows.length}`,
  tableMaxRows: 5,
  tableMaxCols: 9,
  maxChars: 3000,
});
console.log(inspected.ndjson);

const errors = await workbook.inspect({
  kind: "match",
  searchTerm: "#REF!|#DIV/0!|#VALUE!|#NAME\\?|#N/A",
  options: { useRegex: true, maxResults: 50 },
  summary: "formula error scan",
});
console.log(errors.ndjson);

const preview = await workbook.render({
  sheetName: "Bug Sheet",
  autoCrop: "all",
  scale: 1,
  format: "png",
});
await fs.writeFile(`${outputDir}/bug_sheet_preview.png`, new Uint8Array(await preview.arrayBuffer()));

const output = await SpreadsheetFile.exportXlsx(workbook);
await output.save(`${outputDir}/operify_code_review_bug_sheet.xlsx`);
