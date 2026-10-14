// CakePHP index and detail templates expose export/print/view links without
// separate action gates. Preserve explicit configured action denials.
function legacyReversePermissions(configuredLabels, permissions) {
  const configured=new Set(configuredLabels.map(row=>String(row.url || '').trim().toLowerCase()));
  const result=new Set(permissions);
  for (const [action,parent] of [['excel','index'],['viewreverseindent','index'],['viewreverseindentpdf','viewreverseindent']]) {
    const url=`admin/reverseindent/${action}`;
    if(!configured.has(url) && result.has(`legacy:admin/reverseindent/${parent}`)) result.add(`legacy:${url}`);
  }
  return [...result];
}
module.exports={legacyReversePermissions};
