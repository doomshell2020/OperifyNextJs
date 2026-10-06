const {escapeHtml:e, amount, ucfirst, date, logoSrc, document, render} = require('../../utils/legacyPdf');
function words(value) {
  const units = ['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'];
  const tens = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];
  const single = n => n < 20 ? units[n] : `${tens[Math.floor(n/10)]} ${units[n%10]}`.trim();
  const parts = []; let rupees = Math.trunc(Number(value));
  for (const [scale,label] of [[10000000,'Crore'],[100000,'Lakh'],[1000,'Thousand'],[100,'Hundred']]) {
    const count = Math.trunc(rupees/scale); rupees %= scale;
    if (count) parts.push(`${single(count)} ${label}`);
  }
  if (rupees) parts.push(single(rupees));
  // PHP truncates the fractional string to two digits before spelling Paisa.
  const fractional = String(value).split('.')[1] || '';
  const paisa = Number(fractional.slice(0,2).padEnd(2,'0'));
  return `${parts.join(' ')} Rupees${paisa ? ` and ${single(paisa)} Paisa` : ''} Only`.trim();
}
function buildGrnHtml(data) {
  const {grn:g, items = [], site_details:s = {}, sitesetting:setting = {}} = data;
  const logo = logoSrc(s);
  const sum = items.reduce((sum,item)=>sum+Number(item.amount || 0),0);
  const net = sum+Number(g.freight || 0);
  const last = items.at(-1);
  const status = last ? (Number(last.cost_price) === Number(last.amount) ? 'Tax Included' : 'Tax Excluded') : '';
  const line = (label,value) => `<tr><th>${label}</th><td>:</td><td>${e(value)}</td><td></td></tr>`;
  const rows = items.map((item,i)=> {
    const rates=item.tax_rates || [];
    const taxAmounts=rates.length===2 ? [Number(item.tax || 0)/2,Number(item.tax || 0)/2] : [item.tax];
    return `<tr><td>${i+1}.</td><td>${e(ucfirst(item.item_name))}</td><td class="center">${e(item.order_qty)} ${e(item.uom)}</td><td class="center">${e(item.quantity)} ${e(item.uom)}</td><td class="right">${amount(item.rate)}</td><td class="right">${amount(item.cost_price)}</td><td class="center">${rates.map(e).join('<br>')}</td><td class="right">${taxAmounts.map(amount).join('<br>')}</td><td class="right">${amount(item.amount)}</td></tr>`;
  }).join('');
  return document(`<div class="grn-box"><div class="grn-header">${logo?`<img src="${logo}" alt="">`:''}<b class="grn-company">${e(setting.first_name || s.company_name)}</b><div class="grn-address">${e(s.address1)}<br><b>Phone</b> : ${e(s.phone)}<br><b>Email</b> : <u>${e(s.email)}</u><br><b>Website</b> : ${e(s.website)}</div></div>
    <h5 class="grn-title">GOOD RECEIPT NOTE (GRN)</h5>
    <table class="grn-details"><tbody><tr><td><table><colgroup><col style="width:27%"><col style="width:5%"><col style="width:66%"><col style="width:2%"></colgroup><tbody>${line('GRN No.',g.id)}${line('Inward Date',date(g.inwarddate))}${line('Bill Date',date(g.bill_date))}${line('Bill No',g.bill_no)}</tbody></table></td><td><table><colgroup><col style="width:32%"><col style="width:5%"><col style="width:61%"><col style="width:2%"></colgroup><tbody>${line('GSTIN NO.',g.vendor_gstin)}${line('Vendor Name',g.vendor_name)}${line('PO No.',g.purchaseorder_id)}</tbody></table></td></tr></tbody></table>
    <table class="grid grn-items"><colgroup>${[4,18.12,11.76,11.76,9.6,15.96,9.6,9.6,9.6].map(w=>`<col style="width:${w/1.1}%">`).join('')}</colgroup><thead><tr><th>S.No</th><th>ITEM</th><th class="center">ORDER QTY.</th><th class="center">RECEIVED QTY.</th><th class="right">RATE</th><th class="right">PRICE (INR)</th><th class="center">TAX RATE</th><th class="right">TAX AMT</th><th class="right">AMOUNT</th></tr></thead><tbody>${rows}</tbody></table>
    <table class="grn-amount"><colgroup><col style="width:71.2%"><col style="width:10%"><col style="width:18.8%"></colgroup><tbody><tr><td class="right"><b>Amount</b></td><td class="center"><b>${status}</b></td><td class="right">${amount(sum)}</td></tr></tbody></table>
    <table class="grn-totals"><colgroup><col style="width:19.2%"><col style="width:44.8%"><col style="width:27.72%"><col style="width:8.28%"></colgroup><tbody><tr><td colspan="2"></td><th>Freight Charges</th><td class="right">${amount(g.freight)}</td></tr><tr><th>Amount (In Words)</th><td>${e(words(net))}</td><th>Total Amount</th><td class="right">${amount(net)}</td></tr></tbody></table>
    <table class="grn-remarks"><tbody><tr><th style="width:20%">Remarks</th><td>${e(ucfirst(String(g.remark || '').toLowerCase()))}</td></tr></tbody></table>
    <table class="grn-signatures"><colgroup><col style="width:25%"><col style="width:15%"><col style="width:15%"><col style="width:15%"><col style="width:30%"></colgroup><tbody><tr><td><b>For</b> ${e(g.vendor_name)}</td><th class="center">Inspected By</th><th class="center">Store Incharge</th><th class="center">Checked by</th><th class="center">Signature Authority</th></tr></tbody></table></div><script>
      // TCPDF wraps a leading spacer before a wide 9pt amount onto its own line.
      const context = document.createElement('canvas').getContext('2d');
      context.font = '12px Arial';
      const printWidth = (210 - 20) * 96 / 25.4 - 2;
      document.querySelectorAll('.grn-items tbody td:last-child').forEach(cell => {
        const columnWidth = cell.clientWidth / cell.closest('table').clientWidth * printWidth;
        if (context.measureText(cell.textContent + '  ').width > columnWidth - 2) {
          cell.style.paddingTop = '10pt'; cell.style.height = '30pt';
        }
      });
    </script>`, `
    body {padding:0}
    .grn-box {margin-top:12pt; border:1pt solid #000;}
    .grn-header {height:91.5pt; position:relative;}
    .grn-header img {position:absolute;left:16pt;top:1pt;width:62pt;height:62pt;object-fit:contain}
    .grn-company {position:absolute;left:13pt;bottom:13pt;font-size:10pt}
    .grn-address {position:absolute;right:1pt;top:14pt;width:50%;text-align:right;font-size:10pt;line-height:12.5pt;white-space:pre-line}
    .grn-title {margin:0;height:25.5pt;line-height:25.5pt;text-align:center;font-size:12pt;border-top:.6pt solid #000;border-bottom:.6pt solid #000}
    .grn-details>tbody>tr>td {padding:8pt 4pt 6pt}
    .grn-details table td {overflow-wrap:break-word}
    .grn-details table th,.grn-details table td {padding:0;line-height:25pt;height:25pt;font-size:8pt}
    .grn-items th,.grn-items td {padding:0 1pt;line-height:10pt;font-size:8pt}
    .grn-items th {line-height:12pt}
    .grn-items td:last-child {font-size:9pt}
    .grn-amount td {padding:0 1pt;line-height:12pt;border-bottom:.6pt solid #000;font-size:8pt;white-space:nowrap}
    .grn-totals td,.grn-totals th {padding:0 1pt;line-height:12pt;border-bottom:.6pt solid #000}
    .grn-totals td:nth-child(2),.grn-totals td[colspan="2"] {border-right:.6pt solid #000}
    .grn-remarks {margin:0 0 12pt}
    .grn-remarks th,.grn-remarks td {padding:0 8pt;line-height:12pt}
    .grn-signatures {border-top:.6pt solid #000;break-inside:avoid}
    .grn-signatures td,.grn-signatures th {padding:0 1pt;height:45pt;line-height:16pt}
  `);
}
async function generateGrnPDF(data) { return render(buildGrnHtml(data)); }
module.exports = {generateGrnPDF, buildGrnHtml, words};
