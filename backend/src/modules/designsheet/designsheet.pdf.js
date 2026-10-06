const {escapeHtml:e,amount,date,letterhead,document,render} = require('../../utils/legacyPdf');
function buildDesignSheetHtml(data) {
  const {designsheet:d, designsheetdetails:items = []} = data;
  return document(`<div class="document-heading">${letterhead(data.site_details,data.sitesetting)}<h3 class="document-title">Design Sheet Details</h3>
    <table class="metadata"><tbody><tr><td><b>Design Sheet No:-</b> ${e(d.designsheetno)}</td><td><b>Issue Date:-</b> ${date(d.datefrom,true)}</td></tr><tr><td><b>Contract:-</b> ${e(d.contract_no)}.</td><td><b>Finished Product:-</b> ${e(d.item_name)}</td></tr><tr><td><b>Quantity:-</b> ${e(d.quantity)} KM</td><td></td></tr></tbody></table></div>
    <h6 class="section-title">Raw Material</h6><table class="grid"><colgroup>${[5,61,13,11,10].map(w=>`<col style="width:${w}%">`).join('')}</colgroup><thead><tr><th>S.No.</th><th>Item Name</th><th>Qty(Per KM)</th><th>Total Qty</th><th>UOM</th></tr></thead><tbody>${items.map((item,i)=>`<tr><td>${i+1}.</td><td>${e(item.item_name)}</td><td class="right">${amount(item.km_item_qty)}</td><td>${amount(item.item_qty)}</td><td>${e(item.uom)}</td></tr>`).join('')}</tbody></table>`);
}
async function generateDesignSheetPDF(data) { return render(buildDesignSheetHtml(data)); }
module.exports = {buildDesignSheetHtml,generateDesignSheetPDF};
