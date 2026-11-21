// CakePHP purchaseorder templates expose viewpodetail, viewpodetailspdf, and reports
// without separate action gates in permission_label.
// Configured actions still require their explicit saved role grant.
function legacyPurchaseOrderPermissions(configuredLabels, allowedPermissions) {
  const configured = new Set(configuredLabels.map(label => String(label.url || '').trim().toLowerCase()));
  const result = new Set(allowedPermissions);

  if (result.has('legacy:admin/purchaseorder/index')) {
    for (const action of [
      'viewpodetail',
      'viewpodetailspdf',
      'posummaryreport',
      'deliveryreport',
      'productcomparisonreport',
      'poexcel'
    ]) {
      const url = `admin/purchaseorder/${action}`;
      if (!configured.has(url)) result.add(`legacy:${url}`);
    }
  }

  if (result.has('legacy:admin/goodsreceived/index') || result.has('legacy:admin/goodsreceived/grninspection')) {
    for (const action of ['viewpodetail', 'viewpodetailspdf']) {
      const url = `admin/purchaseorder/${action}`;
      if (!configured.has(url)) result.add(`legacy:${url}`);
    }
  }

  return [...result];
}

module.exports = { legacyPurchaseOrderPermissions };
