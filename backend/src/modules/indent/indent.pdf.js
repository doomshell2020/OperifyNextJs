const {escapeHtml:e,date,logoSrc,document,render} = require('../../utils/legacyPdf');
function buildIndentHtml(data, indentId, requestedBy) {
  const {items=[],is_temp,site_details:s={}}=data;
  const logo=logoSrc(s);
  const total=items.reduce((sum,item)=>sum+Number(item.quantity || 0),0);
  return document(`<table class="requisition-letterhead"><tbody><tr><td>${logo?`<img src="${logo}" alt="" style="width:130pt;height:60pt">`:''}</td><td class="right" style="padding-top:25pt">${e(s.address1)},<br>Phone :;${e(s.phone)}<br>Email : <u>${e(s.email)}</u></td></tr></tbody></table>
    <div class="requisition-box"><table class="grid requisition-meta"><tbody><tr><th colspan="3" class="center" style="font-size:14pt">Purchase Requisition</th></tr><tr><th>Indent No.: ${e(indentId)}${is_temp?' Temporary':''}</th><th>From: ${e(requestedBy)}</th><th>Date: ${date(items[0]?.added_time)}</th></tr></tbody></table>
    <table class="grid requisition-items"><colgroup><col style="width:25%"><col style="width:25%"><col style="width:25%"><col style="width:25%"></colgroup><thead><tr><td>S.No.</td><td>Item</td><td>Category</td><td>Qty. Requested</td></tr></thead><tbody>${items.map((item,i)=>`<tr><td>${i+1}</td><td>${e(item.item_name)}</td><td>${e(item.category_name)}</td><td>${e(item.quantity)}</td></tr>`).join('')}</tbody></table><table class="grid"><tbody><tr><td style="width:78%" class="right">Total Quantity</td><td>${e(total)}</td></tr></tbody></table></div>
    <table class="requisition-signatures"><tbody><tr><td style="width:25%;border-top:.6pt solid #000">Signature of Sanctioning Authority</td><td style="width:50%"></td><td class="right" style="width:25%;border-top:.6pt solid #000">Signature of Person Requesting The Items</td></tr></tbody></table>`, `
      @page {size:A4 landscape;margin:10mm 10mm 20mm}
      body {font-size:10pt}
      .requisition-letterhead td {font-size:10pt;line-height:12.5pt}
      .requisition-letterhead img {margin-left:16.7pt}
      .requisition-box {border:1.2pt solid #000;background:#fbfafb;padding:10pt;margin-top:18pt}
      .requisition-box th,.requisition-box td {font-size:10pt;line-height:12.5pt;padding:10pt}
      .requisition-meta {margin-bottom:20pt}
      .requisition-signatures {margin-top:70pt;break-inside:avoid}
      .requisition-signatures td {font-size:10pt;line-height:12.5pt}
    `);
}
async function generateIndentPDF(data,id,name) { return render(buildIndentHtml(data,id,name),{landscape:true}); }
module.exports={buildIndentHtml,generateIndentPDF};
