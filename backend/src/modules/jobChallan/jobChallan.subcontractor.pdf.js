const {escapeHtml:e,amount,date,document} = require('../../utils/legacyPdf');
function buildSubcontractorChallanHtml(payload) {
  const c=payload.challan, s=payload.site_details || {}, setting=payload.sitesetting || {}, to=c.vendor || {};
  const item=c.job_challan_items?.[0] || {};
  const fromName=setting.first_name || s.company_name || 'KANHHA CABLES PVT. LTD.';
  const address=[s.address1,s.address2].filter(Boolean).join(', ') || s.address || 'Plot No. A-336 & A-336(A), Road No. 17, V.K.I. Area, Jaipur-302013';
  const gst=s.gst_no || s.gst;
  const fromGst=gst && gst!=='00' ? gst : '08AACCK1942L1ZE';
  const state=value=>String(value || '').replace(/\D/g,'').slice(0,2) || '08';
  const sameState=state(fromGst)===state(to.gst_no);
  const tax=Number(item.tax_amount || 0),qty=Number(item.quantity || 0),rate=Number(item.rate || 0);
  const unit=item.unit_name || item.item?.unit_name || 'KG';
  const name=item.item_name || item.item?.item_name || '';
  const charges=qty*rate, total=Number(item.amount) || charges+tax;
  const description=c.work_description && c.work_description!=='test' ? c.work_description : '';
  return document(`<table class="jc-frame"><tbody><tr><td style="width:70%"><b>GSTIN: ${e(fromGst)}<br>JOB WORK CHALLAN</b><br><span style="font-size:7.5pt">Subsidiary Challan For return of Goods in Piecemeals received</span></td><td style="width:30%;font-size:9pt">Original for Recipient-White<br>Duplicate for Transporter-Pink<br>Triplicate for Supplier-Green</td></tr></tbody></table>
    <table class="jc-frame"><tbody><tr><td class="center"><b style="font-size:13pt">${e(fromName)}</b><br><span style="font-size:9pt">${e(address)}</span></td></tr></tbody></table>
    <table class="jc-frame"><tbody><tr><td style="width:50%"><b>Challan No:</b> ${e(c.challan_no)}</td><td class="right"><b>Date:</b> ${date(c.jc_date)}</td></tr></tbody></table>
    <table class="jc-frame"><tbody><tr><td style="width:55%"><b>M/s.</b> ${e(to.name)}<br>${e(to.address)}<br><b>State:</b> Rajasthan &nbsp;&nbsp; <b>State Code:</b> ${state(to.gst_no)}<br><b>GSTIN:</b> ${e(to.gst_no)}</td><td><b>JAIPUR TO:</b> Jaipur<br><b>By:</b> .............................................................<br><b>Vehicle No.:</b> ${e(c.vehicle_no)}<br><b>G.R. No.:</b> .................... <b>Date:</b> ................</td></tr></tbody></table>
    <table class="jc-frame items"><colgroup>${[5,45,12,13,10,15].map(w=>`<col style="width:${w}%">`).join('')}</colgroup><thead><tr><th>S.No.</th><th>DESCRIPTION OF GOODS</th><th>BDLS./<br>Packages</th><th>HSN/SAC<br>Code</th><th>SIZE<br>GAUGE</th><th>QUANTITY<br>Net Weight</th></tr></thead><tbody><tr style="height:180pt"><td class="center">1.</td><td><b>${e(name)}</b><br>${e(description)}<br><br>CGST <span style="float:right">Rs. ${sameState?amount(tax/2):'-'}</span><br>SGST <span style="float:right">Rs. ${sameState?amount(tax/2):'-'}</span><br>IGST <span style="float:right">Rs. ${sameState?'-':amount(tax)}</span></td><td class="center">${e(payload.packages || '01 Box')}</td><td class="center">${e(item.hsn_code)}</td><td class="center">-</td><td class="center"><b>${amount(qty)} ${e(unit)}</b></td></tr></tbody></table>
    <table class="jc-frame"><tbody><tr><td style="width:75%;font-size:8.5pt"><b>Conversion A/c Not For Sale</b><table><tbody><tr><td style="width:50%;font-size:8pt">Description: ${e(name)}</td><td style="width:30%;font-size:8pt">Challan No: ${e(payload.original_challan_no || c.challan_no)}</td><td style="width:20%;font-size:8pt">Qty: ${amount(qty)}</td></tr></tbody></table></td><td style="padding:0;width:25%"><table class="charges"><tbody><tr><td style="width:60%">(A) Job Work Charges<br><span style="font-size:7.5pt">${amount(qty)} ${e(unit)} @ ${amount(rate)}</span></td><td class="right">${amount(charges)}</td></tr><tr><td>(B) GST @ ${e(item.tax_rate)}%</td><td class="right">${amount(tax)}</td></tr><tr><td>(C) GST Amount</td><td class="right">${amount(tax)}</td></tr><tr style="background:#f9f9f9"><td><b>Total Amount (A+C)</b></td><td class="right"><b>${amount(total)}</b></td></tr></tbody></table></td></tr></tbody></table>
    <table class="jc-frame"><tbody><tr><td style="width:50%;font-size:8pt">Received in Order &amp; good Condition.<br><br><b>Receiver's Signature:</b> ....................................................</td><td class="right" style="font-size:8.5pt">For <b>${e(fromName)}</b><br><br><br>Authorised Signatory</td></tr></tbody></table>`, `
      @page {size:A4 portrait;margin:${payload.return_receipt ? '5mm' : '10mm 5mm 5mm 10mm'}}
      body {padding:0;font-size:9pt}
      .jc-frame {border:1pt solid #000;border-bottom:0}
      .jc-frame:last-child {border-bottom:1pt solid #000}
      .jc-frame td {font-size:9pt;line-height:1.35;padding:4pt;border-bottom:1pt solid #000}
      .jc-frame:first-of-type {height:56pt}
      .jc-frame:first-of-type td:first-child {font-size:10pt}
      .jc-frame:nth-of-type(2) td {height:49pt;line-height:18.2pt}
      .jc-frame:nth-of-type(3)>tbody>tr>td+td,.jc-frame:nth-of-type(4)>tbody>tr>td+td,.jc-frame:last-of-type>tbody>tr>td+td {border-left:0}
      .jc-frame:nth-of-type(4) td {height:55pt}
      .jc-frame>tbody>tr>td+td {border-left:1pt solid #000}
      .jc-frame th {background:#f2f2f2;font-size:9pt;text-align:center;line-height:1.35;padding:4pt;border:1pt solid #000}
      .items td {font-size:8.5pt;border-left:1pt solid #000}
      .items th {line-height:11pt;height:30pt}
      .items th:first-child {overflow-wrap:anywhere}
      .jc-frame:nth-of-type(6)>tbody>tr>td:first-child {display:table-cell}
      .jc-frame:nth-of-type(6)>tbody>tr>td:first-child>b {display:inline-block;width:120pt;vertical-align:top}
      .jc-frame:nth-of-type(6)>tbody>tr>td:first-child>table {display:inline-table;width:calc(100% - 125pt);vertical-align:top}
      .jc-frame:nth-of-type(6)>tbody>tr>td:first-child>table td {padding:2pt;border:0;line-height:10pt}
      .charges td {font-size:8.5pt;border-bottom:1pt solid #ddd}
      .charges td+td {border-left:1pt solid #000;overflow-wrap:anywhere}
    `);
}
module.exports={buildSubcontractorChallanHtml};
