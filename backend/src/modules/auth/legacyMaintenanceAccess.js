// Maintenance/index exposes Add, PDF and status actions. Edit/delete additionally
// require the tenant's per-user permission_module flags (checked on each API call).
// Never override a configured action denial.
const actions=['add','edit','status','viewpdf','maintenancestatus','completemaintenance'];
function legacyMaintenancePermissions(labels,permissions) {
  const configured=new Set(labels.map(l=>String(l.url || '').trim().toLowerCase()));
  const result=new Set(permissions);
  if (result.has('legacy:admin/maintenance/index')) for(const action of actions) {
    const url='admin/maintenance/'+action;
    if(!configured.has(url))result.add('legacy:'+url);
  }
  return [...result];
}
module.exports={legacyMaintenancePermissions,actions};
