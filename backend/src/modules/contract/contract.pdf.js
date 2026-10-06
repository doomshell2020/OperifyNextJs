const { escapeHtml:e, amount, date, letterhead, document, render } = require('../../utils/legacyPdf');
const cols = widths => `<colgroup>${widths.map(width => `<col style="width:${width}%">`).join('')}</colgroup>`;
function buildContractHtml(data) {
  const {contract:c, items = [], productionOrders = [], inspectionReports = []} = data;
  let body = `<div class="document-heading">${letterhead(data.site_details, data.sitesetting)}<h3 class="document-title">Contract Details</h3>
    <table class="metadata"><tbody>
      <tr><td><b>Work Order:-</b> ${e(c.workorder)}</td><td></td></tr>
      <tr><td><b>Title:-</b> ${e(c.title)}</td><td><b>Issue Date:-</b> ${date(c.issuedate,true)}</td></tr>
      <tr><td><b>Contract Start Date:-</b> ${date(c.contract_start_date,true)}</td><td><b>Contract End Date:-</b> ${date(c.contract_end_date,true)}</td></tr>
      <tr><td><b>Supplier Name:-</b> ${e(c.vendor_name)}</td><td><b>Cost:-</b> ${amount(c.cost)}</td></tr>
      <tr><td><b>Labour Cost:-</b> ${e(c.production_labour ?? 0)}</td><td><b>Operational Cost:-</b> ${amount(c.production_operation)}</td></tr>
    </tbody></table></div><h6 class="section-title">Finished Products</h6>`;
  for (const [itemIndex, item] of items.entries()) {
    body += `<table class="grid product-summary">${cols([31.5,16,19.5,16,17])}<tbody><tr><td><b>Product:-</b> ${e(item.item_name)}</td><td><b>Quantity:-</b> ${amount(item.quantity)} KM</td><td><b>Planned Qty:-</b> ${amount(item.planned_qty)} KM</td><td><b>Prep Qty:-</b> ${amount(item.prepared_qty)} KM</td><td><b>Price:-</b> ${amount(item.price)}</td></tr></tbody></table>`;
    body += `<table class="grid">${cols([5,17.5,9,9,34,13.5,12])}`;
    if (item.has_production) {
      body += `<thead><tr><th>S.No.</th><th>Process Name</th><th>Start Date</th><th>End Date</th><th>PO No.</th><th>Planned Qty(KM)</th><th>Prep Qty(KM)</th></tr></thead><tbody>`;
      (item.processes || []).forEach((p,i)=> { body += `<tr><td>${i+1}.</td><td>${e(p.process_name)}</td><td>${date(p.start_date)}</td><td>${date(p.end_date)}</td><td>${e(p.po_numbers)}</td><td class="right">${amount(p.quantity)}</td><td class="right">${amount(p.quantity)}</td></tr>`; });
      body += '</tbody>';
    } else body += '<tbody><tr><td colspan="7" class="center">Production Not Started Yet.</td></tr></tbody>';
    body += '</table><table class="grid"><tbody><tr><th class="center">Raw Material</th></tr></tbody></table>';
    body += `<table class="grid">${cols([4,54,16,13,13])}<thead><tr><th>No.</th><th>Item Name</th><th>Qty(As per Design)</th><th>Issued Qty</th><th>Pending Qty</th></tr></thead><tbody>`;
    (item.raw_materials || []).forEach((rm,i)=> {
      body += `<tr><td>${i+1}.</td><td>${e(rm.item_name)}</td><td class="right">${amount(rm.as_per_design)}</td><td class="right">${amount(rm.total_issued)}</td><td class="right">${amount(rm.pending_qty)}</td></tr>`;
      (rm.issued_items || []).forEach(issued=> { body += `<tr><td></td><td>${e(issued.item_name)}</td><td></td><td class="right">${amount(issued.issued_qty)}</td><td></td></tr>`; });
    });
    body += `</tbody></table><div style="height:${itemIndex === items.length - 1 ? 12.5 : 19}pt"></div>`;
  }
  body += `<h6 class="section-title">Production Orders</h6><table class="grid production-orders">${cols([6,9,41,10,10,9,9,6])}<thead><tr><th>PO No.</th><th>Issue Date</th><th>Product</th><th>Planned Qty(KM)</th><th>Prepared Qty(KM)</th><th>Start Date</th><th>End Date</th><th>Status</th></tr></thead><tbody>`;
  productionOrders.forEach(po=> { body += `<tr><th>${e(po.po_id)}</th><th>${date(po.issuedate)}</th><th>${e(po.product_name)}</th><th class="right">${amount(po.plannedqty)}</th><th class="right">${amount(po.prepared_qty)}</th><th>${date(po.startdate)}</th><th>${date(po.enddate)}</th><th>${po.status === 'C' ? 'Close' : 'Open'}</th></tr>`; });
  body += `</tbody></table><h6 class="section-title">Inspection Report</h6><table class="grid inspection">${cols([15,55,30])}<thead><tr><th>S.No.</th><th>Inspector Name</th><th>Inspection Date</th></tr></thead><tbody>`;
  inspectionReports.forEach(ir=> { body += `<tr><td>1.</td><td>${e(ir.inspector_name)}</td><td>${date(ir.inspection_date,true)}</td></tr>`; });
  // Chromium reserves the collapsed border at a page end; compensate for TCPDF's border overlap.
  return document(body+'</tbody></table>', '@page {margin-bottom:calc(20mm - 3pt)} .metadata td {padding-top:2.9pt;padding-bottom:2.9pt} .grid + .grid {margin-top:-.6pt} .production-orders tbody th {font-weight:normal} .inspection th,.inspection td {padding:5pt} .product-summary {break-inside:avoid; break-after:avoid}');
}
async function generateContractPDF(data) { return render(buildContractHtml(data)); }
module.exports = { generateContractPDF, buildContractHtml };
