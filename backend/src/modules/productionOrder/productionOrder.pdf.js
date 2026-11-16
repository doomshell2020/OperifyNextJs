const P = require('../../utils/legacyPdf');
function html(data) {
  const {order:o,processes,materials} = data, e=P.escapeHtml, n=P.amount, d=P.date;
  const table=(head,rows)=>'<table class="grid"><thead><tr>'+head.map(v=>'<th>'+e(v)+'</th>').join('')+'</tr></thead><tbody>'+rows.map(row=>'<tr>'+row.map(v=>'<td>'+e(v)+'</td>').join('')+'</tr>').join('')+'</tbody></table>';
  return P.document('<div class="document-heading">'+P.letterhead(data.site_details,data.sitesetting)+'<h1 class="document-title">Production Order Details</h1>'+table(['Production Order No.','Issue Date','Product','Contract Name'],[[o.po_id,d(o.issuedate,true),o.item_name,(o.title || '')+'('+(o.workorder || '')+')']])+table(['Quantity','Start Date','End Date'],[[data.quantity+' KM',d(o.startdate,true),d(o.enddate,true)]])+table(['Planned Qty','Prepared Qty','Pending Qty'],[[n(o.plannedqty)+' KM',n(data.pending_qty)+' KM',n(data.pending_qty)+' KM']])+'</div><h2 class="section-title">Process Details</h2>'+table(['S.No.','Process Name','Start Date','End Date','Prepared Qty(KM)'],processes.map((p,i)=>[i+1,p.process_name,d(p.startdate),d(p.enddate),n(p.prepared_qty)]))+'<h2 class="section-title">Raw Material</h2>'+table(['S.No.','Item Name','Required Qty','Available Qty'],materials.map((m,i)=>[i+1,m.item_name,n(m.required_qty),n(m.available_qty)])));
}
// PHP's PDF prints pending quantity in both Prepared and Pending summary cells.
module.exports = {html,generate:data=>P.render(html(data))};
