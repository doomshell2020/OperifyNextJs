// Active headernew.ctp and Gatepasses templates expose these actions without
// PermissionHelper gates, and AppController's user-action check is disabled.
// Match that authenticated PHP access only for absent catalogue entries.
// Configured actions still require their explicit saved role grant.
const actions=['index','add','edit','view','gatepasspdf','delete'];
function legacyGatepassPermissions(configuredLabels,allowedPermissions) {
  const configured=new Set(configuredLabels.map(label=>String(label.url || '').trim().toLowerCase()));
  const result=new Set(allowedPermissions);
  for(const action of actions){
    const url=`admin/gatepasses/${action}`;
    if(!configured.has(url))result.add(`legacy:${url}`);
  }
  return [...result];
}
module.exports={legacyGatepassPermissions,actions};
