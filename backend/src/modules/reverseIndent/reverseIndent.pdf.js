const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');
const { formatQty } = require('../../utils/formatters');

function formatDate(dateString) {
  if (!dateString) return '';
  const d = new Date(dateString);
  const day = String(d.getDate()).padStart(2, '0');
  const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
  const year = d.getFullYear();
  return `${day}-${monthNames[d.getMonth()]}-${year}`;
}

async function generateReverseIndentPDF(details, tenantDb = 'default') {
  let logoSrc = 'https://staging.operify.in/image/logo.png';
  try {
    const dirPath = path.join(__dirname, '../../../public/uploads/logos');
    if (fs.existsSync(dirPath)) {
      const files = fs.readdirSync(dirPath);
      const logoFile = files.find(f => f.startsWith(`${tenantDb}_logo.`));
      if (logoFile) {
        const logoPath = path.join(dirPath, logoFile);
        const ext = path.extname(logoFile).substring(1);
        const base64Data = fs.readFileSync(logoPath).toString('base64');
        logoSrc = `data:image/${ext};base64,${base64Data}`;
      }
    }
  } catch (e) {
    console.error('Error loading logo for PDF:', e);
  }

  let html = `
  <!DOCTYPE html>
  <html>
  <head>
    <meta charset="utf-8">
    <style>
      body {
        font-family: Arial, sans-serif;
        font-size: 13px;
        margin: 0;
        padding: 20px;
        color: #000;
      }
      table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
      }
      th, td {
        border: 1px solid #000;
        padding: 6px 8px;
        text-align: left;
      }
      th {
        background-color: #f8f9fa;
        font-weight: bold;
      }
      .header-table td {
        border: none;
        padding: 5px;
      }
      .header-table {
        border: 1px solid #000;
        margin-bottom: 20px;
      }
      .title {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        margin: 15px 0;
      }
      .logo {
        max-width: 150px;
        max-height: 80px;
      }
    </style>
  </head>
  <body>
    <table class="header-table">
      <tr>
        <td style="width: 30%; text-align: center;">
          <img src="${logoSrc}" class="logo" />
        </td>
        <td style="width: 70%; text-align: right; font-size: 11px;">
          B-141(A), Rd Number 9D, Vishwakarma Industrial Area, Jaipur,<br/>
          Rajasthan 302013<br/>
          <b>Phone:</b> 9829287189<br/>
          <b>Email:</b> contact@tirupatiplastomatics.com<br/>
          <b>Website:</b> www.tirupatiplastomatics.com
        </td>
      </tr>
      <tr>
        <td colspan="2" style="border-top: 1px solid #000; padding: 10px; font-weight: bold; font-size: 16px;">
          TIRUPATI PLASTOMATICS PVT. LTD.
        </td>
      </tr>
    </table>

    <div class="title">Reverse Indent Details</div>

    <table style="border: 1px solid #000;">
      <tr>
        <td style="width: 15%; border: none;"><b>Indent Id:-</b></td>
        <td style="width: 35%; border: none;">${details.reverse_id || ''}</td>
        <td style="width: 15%; border: none;"><b>Contract Name:-</b></td>
        <td style="width: 35%; border: none;">${details.contract_name || ''}(${details.workorder || ''})</td>
      </tr>
      <tr>
        <td style="border: none;"><b>Product:-</b></td>
        <td style="border: none;">${details.product_name || ''}</td>
        <td style="border: none;"><b>Machine Name:-</b></td>
        <td style="border: none;">${details.machine_name || ''}</td>
      </tr>
      <tr>
        <td style="border: none;"><b>Received By:-</b></td>
        <td style="border: none; text-transform: uppercase;">${details.received_name || ''}</td>
        <td style="border: none;"><b>Received Date:-</b></td>
        <td style="border: none;">${formatDate(details.issue_date)}</td>
      </tr>
    </table>

    <div class="title" style="margin-top: 25px;">Raw Material</div>

    <table>
      <thead>
        <tr>
          <th style="width: 8%; text-align: center;">S.No.</th>
          <th style="width: 62%;">Item</th>
          <th style="width: 20%; text-align: right;">Received Qty</th>
          <th style="width: 10%; text-align: center;">UOM</th>
        </tr>
      </thead>
      <tbody>
  `;

  if (details.items && details.items.length > 0) {
    details.items.forEach((item, idx) => {
      html += `
        <tr>
          <td style="text-align: center;">${idx + 1}.</td>
          <td>${item.item_name || ''}</td>
          <td style="text-align: right;">${formatQty(item.quantity)}</td>
          <td style="text-align: center;">${item.uom || ''}</td>
        </tr>
      `;
    });
  } else {
    html += `<tr><td colspan="4" style="text-align: center;">No raw materials found.</td></tr>`;
  }

  html += `
      </tbody>
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
  generateReverseIndentPDF
};
