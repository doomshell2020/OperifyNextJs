const { escapeHtml:e, amount, ucfirst, date, letterhead, document, render } = require('../../utils/legacyPdf');
function buildReverseIndentHtml(details) {
  return document(`<div class="document-heading">${letterhead(details.site_details, details.sitesetting)}
    <h3 class="document-title">Reverse Indent Details</h3>
    <table class="metadata"><tbody>
      <tr><td><b>Reverse Id :-</b> ${e(details.reverse_id)}</td><td><b>Contract name :-</b> ${e(details.contract_name)}(${e(details.workorder)})</td></tr>
      <tr><td><b>Product :-</b> ${e(details.product_name)}</td><td><b>Machine Name :-</b> ${e(details.machine_name)}</td></tr>
      <tr><td><b>Received By :-</b> ${e(details.received_name)}</td><td><b>Received Date :-</b> ${date(details.issue_date)}</td></tr>
      <tr><td><b>Last Updated Date :-</b> ${date(details.updated)}</td><td></td></tr>
    </tbody></table></div>
    <h6 class="section-title">Raw Material</h6>
    <table class="grid"><colgroup><col style="width:8%"><col style="width:62%"><col style="width:20%"><col style="width:10%"></colgroup>
      <thead><tr><th>S.No.</th><th>Item</th><th>Received Qty</th><th>UOM</th></tr></thead>
      <tbody>${(details.items || []).map((item,i)=>`<tr><td>${i+1}.</td><td>${e(ucfirst(item.item_name))}</td><td class="right">${amount(item.quantity)}</td><td>${e(item.uom)}</td></tr>`).join('')}</tbody>
    </table>
    <table class="signatures" style="margin-top:30pt"><tbody><tr><td style="width:33%"><b>INDENTER</b></td><td style="width:33%" class="center"><b>ISSUED BY</b></td><td style="width:34%" class="right"><b>RECEIVED BY</b></td></tr></tbody></table>`);
}
async function generateReverseIndentPDF(details) { return render(buildReverseIndentHtml(details)); }
module.exports = { generateReverseIndentPDF, buildReverseIndentHtml };
