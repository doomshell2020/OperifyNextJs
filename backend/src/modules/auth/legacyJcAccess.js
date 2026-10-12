// The active CakePHP Jobchallan templates expose these authenticated actions
// without PermissionHelper gates. Respect explicit configured role controls;
// do not turn an absent migration label into a new deny rule.
const actions = [
  'index','add','view','jcinfo','viewpdf','viewreturnpdf',
  'itemreceived','receiveindex','receiveadd','delete','ajaxaddsubcontractor'
];
function legacyJcPermissions(configuredLabels, allowedPermissions) {
  const configured = new Set(configuredLabels.map(label => String(label.url || '').trim().toLowerCase()));
  const result = new Set(allowedPermissions);
  for (const action of actions) {
    const url = `admin/jobchallan/${action}`;
    if (!configured.has(url)) result.add(`legacy:${url}`);
  }
  return [...result];
}
module.exports = { legacyJcPermissions, actions };
