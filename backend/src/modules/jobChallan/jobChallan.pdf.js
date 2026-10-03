const puppeteer = require('puppeteer');

function plain(value) {
  if (!value) return {};
  if (typeof value.get === 'function') return value.get({ plain: true });
  if (typeof value.toJSON === 'function') return value.toJSON();
  return value;
}

function escapeHtml(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function formatDate(dateString) {
  if (!dateString) return '';
  const d = new Date(dateString);
  if (Number.isNaN(d.getTime())) return '';
  const day = String(d.getDate()).padStart(2, '0');
  const month = String(d.getMonth() + 1).padStart(2, '0');
  return `${day}-${month}-${d.getFullYear()}`;
}

function formatTime(dateString) {
  if (!dateString) return '';
  const d = new Date(dateString);
  if (Number.isNaN(d.getTime())) return '';
  const hours = String(d.getHours()).padStart(2, '0');
  const minutes = String(d.getMinutes()).padStart(2, '0');
  return `${hours}:${minutes} Hrs`;
}

function money(value) {
  return Number(value || 0).toFixed(2);
}

function titleName(value) {
  return String(value || '')
    .toLowerCase()
    .replace(/\b\w/g, char => char.toUpperCase());
}

function stateCode(gstin, fallback = '08') {
  const digits = String(gstin || '').replace(/\D/g, '');
  return digits.substring(0, 2) || fallback;
}

async function generateJobChallanPDF(payload) {
  const challan = plain(payload.challan);
  const siteDetails = plain(payload.site_details);
  const siteSetting = plain(payload.sitesetting);
  const vendor = plain(challan.vendor);
  const items = (challan.job_challan_items || []).map(plain);
  const firstItem = items.find(item => item.return_type !== 'Semi-Finished Product') || items[0] || {};
  const item = plain(firstItem.item);

  const supplierName = siteSetting.first_name || siteDetails.company_name || 'Tirupati Plastomatics (P) Ltd.';
  const supplierAddress = [
    siteDetails.address1,
    siteDetails.address2
  ].filter(Boolean).join(', ') || siteDetails.address || 'Plot No. B-141-A, Road No. 9-D, V.K.I Area, Jaipur - 302013';
  const supplierGstin = siteDetails.gst_no && siteDetails.gst_no !== '00' ? siteDetails.gst_no : '08AAACT5317J1ZA';
  const supplierPan = siteDetails.pan_number || 'AAACT5317J';
  const supplierCity = supplierAddress.toLowerCase().includes('jaipur') ? 'Jaipur' : '';
  const taxRate = Number(firstItem.tax_rate || 0);
  const taxAmount = Number(firstItem.tax_amount || 0);
  const sameState = stateCode(supplierGstin) === stateCode(vendor.gst_no);
  const cgstRate = sameState ? `${(taxRate / 2).toFixed(1)}%` : '-';
  const sgstRate = sameState ? `${(taxRate / 2).toFixed(1)}%` : '-';
  const igstRate = sameState ? '-' : `${taxRate.toFixed(1)}%`;
  const cgstAmount = sameState ? money(taxAmount / 2) : '-';
  const sgstAmount = sameState ? money(taxAmount / 2) : '-';
  const igstAmount = sameState ? '-' : money(taxAmount);
  const issueDate = formatDate(challan.jc_date);
  const issueTime = formatTime(challan.created);
  const workDescription = challan.work_description && challan.work_description !== 'test'
    ? challan.work_description
    : '02 Drum';

  const html = `
    <!doctype html>
    <html>
      <head>
        <meta charset="utf-8" />
        <style>
          @page { size: A4 portrait; margin: 5mm; }
          html, body {
            margin: 0;
            padding: 0;
            color: #000;
            background: #fff;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5px;
          }
          table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
          }
          td {
            box-sizing: border-box;
            padding: 4px;
            vertical-align: top;
            font-size: 8.5px;
            line-height: 1.35;
          }
          .bt { border-top: 1px solid #000; }
          .br { border-right: 1px solid #000; }
          .bb { border-bottom: 1px solid #000; }
          .bl { border-left: 1px solid #000; }
          .part {
            text-align: center;
            background: #f2f2f2;
            font-weight: bold;
            font-size: 9px;
            line-height: 1.25;
          }
          .dots {
            color: #000;
            vertical-align: bottom;
            font-size: 8px;
          }
        </style>
      </head>
      <body>
        <table>
          <tr>
            <td width="70%" class="bt br bb bl" style="font-size:10px;">
              <b>ANNEXURE</b><br>
              <b>JOB CHALLAN</b><br>
              <span style="font-size:7.5px;">(For Movement of Inputs or partially processed goods from one Factory to another Factory for further Processing/Operation)</span>
            </td>
            <td width="30%" class="bt br bb" style="font-size:9px;">
              Original : Pink<br>
              Duplicate : Green<br>
              Triplicate : White<br><br>
              <b>S. L. No: ${escapeHtml(challan.challan_no)}</b>
            </td>
          </tr>
          <tr>
            <td width="40%" class="bl bb" style="font-size:9px;"><b>Name and address of the Suppliers/Manufacturer:</b></td>
            <td width="60%" class="br bb" style="font-size:9px;">
              ${escapeHtml(supplierName)}<br>
              ${escapeHtml(supplierAddress)}<br>
              <b>GSTIN:</b> ${escapeHtml(supplierGstin)} &nbsp;&nbsp;&nbsp;&nbsp; <b>PAN No:</b> ${escapeHtml(supplierPan)}
            </td>
          </tr>
          <tr><td colspan="2" class="part bl br bb">PART - I</td></tr>
          <tr><td width="60%" class="bl">1. Description of Goods</td><td width="40%" class="br">${escapeHtml(item.item_name || firstItem.item_name || '')}</td></tr>
          <tr><td class="bl">2. Identification marks and numbers if any</td><td class="br">${escapeHtml(workDescription)}</td></tr>
          <tr><td class="bl">3. Quantity (Nos./Weight/Litre/Metre)</td><td class="br">${money(firstItem.quantity)} KG</td></tr>
          <tr><td class="bl">4. HSN/SAC</td><td class="br">${escapeHtml(firstItem.hsn_code || '')}</td></tr>
          <tr><td class="bl">5. Estimated value of inputs</td><td class="br">${money(challan.estimated_values)}</td></tr>
          <tr>
            <td class="bl">6. GST<br>&nbsp;&nbsp;&nbsp;&nbsp;(A). CGST<br>&nbsp;&nbsp;&nbsp;&nbsp;(B). SGST<br>&nbsp;&nbsp;&nbsp;&nbsp;(C). IGST</td>
            <td class="br"><br>@ ${escapeHtml(cgstRate)} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Rs. ${escapeHtml(cgstAmount)}<br>@ ${escapeHtml(sgstRate)} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Rs. ${escapeHtml(sgstAmount)}<br>@ ${escapeHtml(igstRate)} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Rs. ${escapeHtml(igstAmount)}</td>
          </tr>
          <tr><td class="bl">7. Total amount of GST</td><td class="br">Rs. ${money(taxAmount)}</td></tr>
          <tr><td class="bl">8. Date and time of issue</td><td class="br">${issueDate} ${issueTime}</td></tr>
          <tr><td class="bl">9. Nature of processing/manufacturing required to be done</td><td class="br">${escapeHtml(titleName(challan.processing_type))}</td></tr>
          <tr><td class="bl">10. Factory/Place of processing/Manufacturing</td><td class="br"><b>${escapeHtml(titleName(vendor.name))}</b><br>${escapeHtml(vendor.address || '')}<br><b>GSTIN:</b> ${escapeHtml(vendor.gst_no || '')}</td></tr>
          <tr><td class="bl">11. Expected duration of processing/manufacturing</td><td class="br">${escapeHtml(challan.expected_days || '')} Day${Number(challan.expected_days) > 1 ? 's' : ''}</td></tr>
          <tr><td class="bl">12. Vehicle No.</td><td class="br">${escapeHtml(challan.vehicle_no || '')}</td></tr>
          <tr>
            <td colspan="2" class="bl br bb">
              <table>
                <tr>
                  <td width="55%" style="font-size:8.5px;"><b>Place:</b> ${escapeHtml(supplierCity)}<br><b>Date:</b> ${issueDate}</td>
                  <td width="45%" align="right" valign="bottom" height="35" style="font-size:8px;">Signature of manufacturer/Authorised Signatory</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr><td colspan="2" class="part bl br bb">PART - II</td></tr>
          <tr><td width="60%" class="bl" style="font-size:8px;">1. Date and time of despatch of finished goods to parent factory/another manufacturer and entry No. and date of Factory receipt in processing</td><td width="40%" class="br dots">........................................................................<br>........................................................................</td></tr>
          <tr><td class="bl" style="font-size:8px;">2. Quantity Despatched (Nos./Weight/Litre/Metre) and entered in Account</td><td class="br dots">........................................................................</td></tr>
          <tr><td class="bl" style="font-size:8px;">3. Nature of processing/manufacturing done</td><td class="br dots">........................................................................</td></tr>
          <tr><td class="bl" style="font-size:8px;">4. Quantity of waste material to be returned to the parent factory or cleared for home consumption. Invoice No. and date. Quantum of GST Paid (Both figures and words.)</td><td class="br dots">........................................................................<br>........................................................................</td></tr>
          <tr>
            <td colspan="2" class="bl br bb">
              <table>
                <tr>
                  <td width="55%" style="font-size:8px;"><b>Place:</b> ............................<br><b>Date:</b> &nbsp;............................</td>
                  <td width="45%" align="right" valign="bottom" height="35" style="font-size:8px;">Signature of processor/Name of factory, Address</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr><td colspan="2" class="part bl br bb">PART - III</td></tr>
          <tr>
            <td colspan="2" class="bl br" style="font-size:8px; text-align:justify;">
              <b>To be filled by parent factory in duplicate copy of challan on receipt of the same from the processing factory.</b><br>
              Certified that I/We have received the goods removed under the above challan, on .............................................. and have taken credit of the amount vide Entry No. .............................................. Dated ..............................................
            </td>
          </tr>
          <tr>
            <td colspan="2" class="bl br bb">
              <table>
                <tr>
                  <td width="55%" style="font-size:8px;"><b>Place:</b> ............................<br><b>Date:</b> &nbsp;............................</td>
                  <td width="45%" align="right" valign="bottom" height="35" style="font-size:8px;">Signature of manufacturer/Authorised Signatory</td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </body>
    </html>
  `;

  const browser = await puppeteer.launch({
    headless: 'new',
    args: ['--no-sandbox', '--disable-setuid-sandbox']
  });

  const page = await browser.newPage();
  await page.setContent(html, { waitUntil: 'networkidle0' });
  const pdfBuffer = await page.pdf({
    format: 'A4',
    margin: { top: '5mm', right: '5mm', bottom: '5mm', left: '5mm' },
    printBackground: true
  });

  await browser.close();
  return pdfBuffer;
}

module.exports = {
  generateJobChallanPDF
};
