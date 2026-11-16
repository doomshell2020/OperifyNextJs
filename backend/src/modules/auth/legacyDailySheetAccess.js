// Daily Sheet templates expose date-wise actions through Add and the date links.
// Older catalogues have no labels for them. Use existing module grants only;
// an explicit configured label remains authoritative, including denials.
function legacyDailySheetPermissions(labels,permissions) {
  const configured=new Set(labels.map(l=>String(l.url || '').trim().toLowerCase()));
  const result=new Set(permissions);
  for(const [action,source] of [['add_datewise_dailysheet','add'],['edit_datewise_dailysheet','index']]) {
    const url='admin/production/'+action;
    if(!configured.has(url) && result.has('legacy:admin/production/'+source)) result.add('legacy:'+url);
  }
  return [...result];
}
module.exports={legacyDailySheetPermissions};
