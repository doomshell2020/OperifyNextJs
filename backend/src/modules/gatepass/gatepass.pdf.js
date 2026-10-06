const {escapeHtml:e,date,letterhead,document,render}=require('../../utils/legacyPdf');
function build(data) {
  const g=data.gatepass;
  const rows=g.items.map((item,index)=>`<tr><td class="center">${index+1}</td><td class="center"><b>${e(item.item_id)}</b></td><td><b>${e(item.item_name)}</b><br>${e(item.description).replace(/\n/g,'<br>')}</td><td class="center"><b>${e(item.quantity)}</b></td><td class="center"><b>${e(item.unit)}</b></td><td><b>${e(item.remarks).replace(/\n/g,'<br>')}</b></td></tr>`).join('');
  return document(`<div class="document-heading">${letterhead(data.site_details,data.sitesetting)}<h3 class="document-title">RETURNABLE GATE PASS</h3>
    <table class="metadata"><tr><td style="width:10%">No.</td><td style="width:50%"><b>${e(g.gatepass_no)}</b></td><td class="right" style="width:15%">Date</td><td><b>${date(g.date).replace(/-/g,'/')}</b></td></tr>
    <tr><td>M/s.</td><td colspan="3">${e(g.vendor_name || g.company_name)}</td></tr><tr><td></td><td colspan="3" style="height:20pt">${e(g.vendor_address)}</td></tr>
    <tr><td>Through</td><td>${e(g.vehicle_no)}</td><td class="right">Date of Return</td><td>${date(g.return_date).replace(/-/g,'/')}</td></tr></table></div>
    <table class="grid"><colgroup>${[6,14,40,10,10,20].map(width=>`<col style="width:${width}%">`).join('')}</colgroup><thead><tr><th>S.No.</th><th>Item Code</th><th>Description of Item</th><th>Qty.</th><th>Unit</th><th>Remarks</th></tr></thead><tbody>${rows}</tbody></table>
    <table class="signatures grid"><tr><td style="width:33%"><br><br><br>Head Store</td><td class="center" style="width:34%"><br><br><br>Authorised Signatory</td><td class="right"><br><br><br>Received by</td></tr></table>`, '@page {size:A5 landscape;margin:5mm 5mm 15mm} body{padding:0;font-size:8pt} .metadata td {padding:3pt} .grid td,.grid th{padding:3pt;overflow-wrap:anywhere} .document-title{margin:10pt 0} .document-heading{padding:7pt} .letterhead{height:65pt} .letterhead-logo{height:46pt;width:46pt} .signatures{border:.6pt solid #000}.signatures td{padding:10pt;border:0}');
}
module.exports={build,generate:data=>render(build(data))};
