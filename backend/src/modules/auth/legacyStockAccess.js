// CakePHP searchstockregister.ctp exposes summaryexcel to index users without
// an additional permission gate. Keep any explicitly configured denial intact.
function legacyStockPermissions(configuredLabels, allowedPermissions) {
  const exportUrl = 'admin/stockregister/summaryexcel';
  const configured = configuredLabels.some(label => String(label.url || '').trim().toLowerCase() === exportUrl);
  const result = new Set(allowedPermissions);
  if (!configured && result.has('legacy:admin/stockregister/index')) result.add(`legacy:${exportUrl}`);
  return [...result];
}
module.exports = { legacyStockPermissions };
