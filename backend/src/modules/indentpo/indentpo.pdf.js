const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

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

function formatQty(value) {
  return Number(value || 0).toFixed(2);
}

function titleName(value) {
  return String(value || '')
    .toLowerCase()
    .replace(/\b\w/g, char => char.toUpperCase());
}

function getLogoSrc(siteDetails, tenantDb) {
  const logoFile = siteDetails?.small_logo || siteDetails?.logo || '';
  const logoDir = path.join(__dirname, '../../../public/uploads/logos');
  const candidates = [
    logoFile,
    `${tenantDb}_logo.png`,
    `${tenantDb}_logo.jpg`,
    'd80960ce77aede66a5c3c8eef8dfafda.png',
    'tirupati_tppl_logo.png'
  ].filter(Boolean);

  for (const candidate of candidates) {
    const logoPath = path.join(logoDir, candidate);
    if (fs.existsSync(logoPath)) {
      const ext = path.extname(candidate).substring(1).toLowerCase() || 'png';
      const mime = ext === 'jpg' || ext === 'jpeg' ? 'jpeg' : ext;
      return `data:image/${mime};base64,${fs.readFileSync(logoPath).toString('base64')}`;
    }
  }

  return '';
}

async function generateIndentpoPDF(details, tenantDb = 'default') {
  const siteDetails = details.site_details || {};
  const siteSetting = details.sitesetting || {};
  const logoSrc = getLogoSrc(siteDetails, tenantDb);
  const companyName = siteSetting.first_name || siteDetails.company_name || 'TIRUPATI PLASTOMATICS PVT. LTD.';

  const itemRows = (details.items || []).map((item, index) => `
    <tr>
      <th width="8%">${index + 1}.</th>
      <td width="62%">${escapeHtml(item.raw_material_name || item.item_name || '')}</td>
      <td width="20%" style="text-align:right;">${formatQty(item.quantity)}</td>
      <td width="10%">${escapeHtml(item.unit_name || item.uom || '-')}</td>
    </tr>
  `).join('') || `
    <tr>
      <td colspan="4" style="text-align:center;">No raw materials found.</td>
    </tr>
  `;

  const html = `
    <!doctype html>
    <html>
      <head>
        <meta charset="utf-8" />
        <style>
          @page { size: A4 portrait; margin: 10mm; }
          html, body {
            margin: 0;
            padding: 0;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8px;
            background: #fff;
          }
          table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
          }
          td, th {
            box-sizing: border-box;
            font-size: 8px;
            line-height: 10px;
            padding: 3px;
            vertical-align: top;
          }
          .outer {
            border: 1px solid #000;
          }
          .legacy-header {
            position: relative;
            height: 96px;
            border-bottom: 1px solid #000;
          }
          .legacy-logo {
            position: absolute;
            top: 25px;
            left: 16px;
            height: 42px;
            max-width: 110px;
            object-fit: contain;
          }
          .legacy-company {
            position: absolute;
            left: 16px;
            bottom: 9px;
            color: #000;
            font-size: 10px;
            line-height: 12px;
            font-weight: bold;
          }
          .legacy-address {
            position: absolute;
            top: 4px;
            right: 0;
            width: 50%;
            text-align: center;
            color: #000;
            font-size: 7px;
            line-height: 9px;
          }
          .title {
            margin: 0;
            padding: 0;
            text-align: center;
            font-size: 10px;
            line-height: 15px;
            border-bottom: 1px solid #000;
            height: 15px;
          }
          .section-title {
            margin: 10px 0 2px;
            text-align: center;
            font-size: 10px;
            line-height: 12px;
          }
          .raw-table th,
          .raw-table td {
            border: 1px solid #000;
          }
          .signatures {
            margin-top: 24px;
          }
          .signatures th,
          .signatures td {
            border: 0;
            font-weight: bold;
          }
        </style>
      </head>
      <body>
        <table class="outer">
          <tr>
            <td>
              <div class="legacy-header">
                ${logoSrc ? `<img src="${logoSrc}" alt="" class="legacy-logo" />` : ''}
                <div class="legacy-company">${escapeHtml(companyName)}</div>
                <div class="legacy-address">
                  ${escapeHtml(siteDetails.address1 || '')}<br>
                  <b>Phone</b> :${escapeHtml(siteDetails.phone || '')}<br>
                  <b>Email</b> : <u>${escapeHtml(siteDetails.email || '')}</u><br>
                  <b>Website</b> :&nbsp;${escapeHtml(siteDetails.website || '')}
                </div>
              </div>

              <h3 class="title">Indent Details</h3>

              <table>
                <thead>
                  <tr>
                    <td><b>Indent Id :-</b> ${escapeHtml(details.indent_id || '')}</td>
                    <td><b>Contract name :-</b> ${escapeHtml(details.contract_name || '')}(${escapeHtml(details.workorder || '')})</td>
                  </tr>
                  <tr>
                    <td><b>Product :-</b> ${escapeHtml(details.product_name || '')}</td>
                    <td><b>Machine Name :-</b> ${escapeHtml(details.machine_name || '')}</td>
                  </tr>
                  <tr>
                    <td><b>Issue Date :-</b> ${formatDate(details.issue_date)}</td>
                    <td></td>
                  </tr>
                </thead>
              </table>
            </td>
          </tr>
        </table>

        <h6 class="section-title">Raw Material</h6>
        <table class="raw-table">
          <thead>
            <tr>
              <th width="8%"><strong>S.No.</strong></th>
              <th width="62%"><strong>Item</strong></th>
              <th width="20%"><strong>Issue Qty</strong></th>
              <th width="10%"><strong>UOM</strong></th>
            </tr>
          </thead>
          <tbody>
            ${itemRows}
          </tbody>
        </table>

        <table class="signatures">
          <thead>
            <tr>
              <th width="33%"><strong><span>${escapeHtml(titleName(details.created_by))}</span><br>INDENTER</strong></th>
              <td width="33%" style="text-align:center;"><strong><span>${escapeHtml(titleName(details.issued_name))}</span><br>ISSUED BY</strong></td>
              <td width="34%" style="text-align:right;"><strong><span></span><br>RECEIVED BY</strong></td>
            </tr>
          </thead>
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
    margin: { top: '10mm', right: '10mm', bottom: '10mm', left: '10mm' },
    printBackground: true
  });

  await browser.close();
  return pdfBuffer;
}

module.exports = {
  generateIndentpoPDF
};
