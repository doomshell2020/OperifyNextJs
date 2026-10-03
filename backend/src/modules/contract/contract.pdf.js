const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');
const { formatQty, formatAmt } = require('../../utils/formatters');

function formatDate(dateString) {
  if (!dateString) return '';
  const d = new Date(dateString);
  const day = String(d.getDate()).padStart(2, '0');
  const month = String(d.getMonth() + 1).padStart(2, '0'); // January is 0!
  const year = d.getFullYear();
  const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
  return `${day}-${monthNames[d.getMonth()]}-${year}`;
}

function escapeHtml(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function resolveLogo(siteDetails, tenantDb) {
  const dirPath = path.join(__dirname, '../../../public/uploads/logos');
  const candidates = [
    siteDetails?.small_logo,
    siteDetails?.logo,
    `${tenantDb}_logo.png`,
    `${tenantDb}_logo.jpg`,
    'd80960ce77aede66a5c3c8eef8dfafda.png',
    'tirupati_tppl_logo.png'
  ].filter(Boolean);

  for (const candidate of candidates) {
    const logoPath = path.join(dirPath, candidate);
    if (fs.existsSync(logoPath)) {
      const ext = path.extname(candidate).substring(1).toLowerCase() || 'png';
      const mime = ext === 'jpg' || ext === 'jpeg' ? 'jpeg' : ext;
      return `data:image/${mime};base64,${fs.readFileSync(logoPath).toString('base64')}`;
    }
  }

  return '';
}

async function generateContractPDF(contractData, tenantDb = 'default') {
  const { contract, items, productionOrders, inspectionReports, site_details: siteDetails = {}, sitesetting = {} } = contractData;
  const logoSrc = resolveLogo(siteDetails, tenantDb);
  const companyName = sitesetting.first_name || siteDetails.company_name || 'TIRUPATI PLASTOMATICS PVT. LTD.';

  let html = `
  <!DOCTYPE html>
  <html>
  <head>
    <meta charset="utf-8">
    <style>
      body {
        font-family: Arial, sans-serif;
        font-size: 8px;
        margin: 0;
        padding: 0;
        color: #000;
      }
      table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
      }
      th, td {
        border: 1px solid #000;
        padding: 3px;
        text-align: left;
        vertical-align: top;
        font-size: 8px;
        line-height: 10px;
      }
      th {
        font-weight: bold;
      }
      .header-table {
        border: 1px solid #000;
        width: 100%;
      }
      .header-table td {
        border: none;
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
        font-size: 10px;
        line-height: 12px;
        font-weight: bold;
        color: #000;
      }
      .legacy-address {
        position: absolute;
        top: 4px;
        right: 0;
        width: 50%;
        text-align: center;
        font-size: 7px;
        line-height: 9px;
        color: #000;
      }
      .title-box {
        text-align: center;
        font-size: 10px;
        line-height: 15px;
        font-weight: bold;
        border-bottom: 1px solid #000;
        padding: 0;
        height: 15px;
      }
      .section-title {
        text-align: center;
        font-size: 10px;
        font-weight: bold;
        margin: 10px 0 2px;
      }
      .no-data {
        text-align: center;
      }
      .logo { display: block; height: 62px; max-width: 150px; object-fit: contain; }
      .company { display: block; font-size: 10px; font-weight: bold; }
    </style>
  </head>
  <body>
    <table class="header-table">
      <tr><td colspan="2" style="border: none; padding: 0;">
        <div class="legacy-header">
          ${logoSrc ? `<img src="${logoSrc}" alt="Logo" class="legacy-logo">` : ''}
          <div class="legacy-company">${escapeHtml(companyName)}</div>
          <div class="legacy-address">
            ${escapeHtml(siteDetails.address1 || '')}<br>
            <b>Phone</b> :${escapeHtml(siteDetails.phone || '')}<br>
            <b>Email</b> : <u>${escapeHtml(siteDetails.email || '')}</u><br>
            <b>Website</b> :&nbsp;${escapeHtml(siteDetails.website || '')}
          </div>
        </div>
      </td></tr>
      <tr><td colspan="2" style="border: none; padding: 0;"><div class="title-box">Contract Details </div></td></tr>
    </table>

    <table>
      <tr>
        <td colspan="2"><b>Work Order:-</b> ${escapeHtml(contract.workorder || '')}</td>
      </tr>
      <tr>
        <td style="width: 50%;"><b>Title:-</b> ${escapeHtml(contract.title || '')}</td>
        <td style="width: 50%;"><b>Issue Date:-</b> ${formatDate(contract.issuedate)}</td>
      </tr>
      <tr>
        <td><b>Contract Start Date:-</b> ${formatDate(contract.contract_start_date)}</td>
        <td><b>Contract End Date:-</b> ${formatDate(contract.contract_end_date)}</td>
      </tr>
      <tr>
        <td><b>Supplier Name:-</b> ${escapeHtml(contract.vendor_name || '')}</td>
        <td><b>Cost:-</b> ${formatAmt(contract.cost)}</td>
      </tr>
      <tr>
        <td><b>Labour Cost:-</b> ${formatAmt(contract.labour_cost)}</td>
        <td><b>Operational Cost:-</b> ${formatAmt(contract.operation_cost)}</td>
      </tr>
    </table>

    <div class="section-title">Finished Products</div>
  `;

  // Finished Products
  if (items && items.length > 0) {
    for (const item of items) {
      html += `
      <table>
        <tr>
          <td style="width:31.5%;"><b>Product:-</b> ${escapeHtml(item.item_name || '')}</td>
          <td><b>Quantity:-</b> ${formatQty(item.quantity)} ${item.uom || ''}</td>
          <td><b>Planned Qty:-</b> ${formatQty(item.planned_qty)} ${item.uom || ''}</td>
          <td><b>Prep Qty:-</b> ${formatQty(item.prepared_qty)} ${item.uom || ''}</td>
          <td><b>Price:-</b> ${formatAmt(item.price)}</td>
        </tr>
      </table>
      `;

      // Raw Material for this finished product
      const rmList = item.raw_materials;
      if (rmList && rmList.length > 0) {
        html += `
        <table>
          <thead>
            <tr>
              <th colspan="5" style="text-align: center;">Raw Material</th>
            </tr>
            <tr>
              <th style="width: 4%;">No.</th>
              <th style="width: 54%;">Item Name</th>
              <th style="width: 16%; text-align: right;">Qty(As per Design)</th>
              <th style="width: 13%; text-align: right;">Issued Qty</th>
              <th style="width: 13%; text-align: right;">Pending Qty</th>
            </tr>
          </thead>
          <tbody>
        `;
        rmList.forEach((rm, idx) => {
          html += `
            <tr>
              <td>${idx + 1}.</td>
              <td>${escapeHtml(rm.item_name)}</td>
              <td style="text-align: right;">${formatQty(rm.as_per_design)}</td>
              <td style="text-align: right;">${formatQty(rm.total_issued)}</td>
              <td style="text-align: right;">${formatQty(rm.pending_qty)}</td>
            </tr>
          `;
          if (rm.issued_items && rm.issued_items.length > 0) {
            rm.issued_items.forEach((issued) => {
              html += `
                <tr>
                  <td></td>
                  <td>${escapeHtml(issued.item_name)}</td>
                  <td></td>
                  <td style="text-align: right;">${formatQty(issued.issued_qty)}</td>
                  <td></td>
                </tr>
              `;
            });
          }
        });
        html += `
          </tbody>
        </table>
        `;
      } else {
        html += `<div class="no-data" style="margin-bottom: 10px; border: 1px solid #000; padding: 5px;">Production Not Started Yet.</div>`;
      }
    }
  } else {
    html += `<div class="no-data">No finished products found.</div>`;
  }

  // Production Orders
  html += `
    <div class="section-title">Production Orders</div>
    <table>
      <thead>
        <tr>
          <th>PO No.</th>
          <th>Issue Date</th>
          <th>Product</th>
          <th>Planned Qty(KM)</th>
          <th>Prepared Qty(KM)</th>
          <th>Start Date</th>
          <th>End Date</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
  `;
  if (productionOrders && productionOrders.length > 0) {
    productionOrders.forEach(po => {
      html += `
        <tr>
          <td>${po.po_id || ''}</td>
          <td>${formatDate(po.issuedate)}</td>
          <td>${escapeHtml(po.product_name || '')}</td>
          <td style="text-align: right;">${formatQty(po.plannedqty)}</td>
          <td style="text-align: right;">${formatQty(po.prepared_qty)}</td>
          <td>${formatDate(po.startdate)}</td>
          <td>${formatDate(po.enddate)}</td>
          <td>${po.status === 'C' ? 'Close' : 'Open'}</td>
        </tr>
      `;
    });
  } else {
    html += `<tr><td colspan="8" class="no-data">No production orders found.</td></tr>`;
  }
  html += `
      </tbody>
    </table>
  `;

  // Inspection Report
  html += `
    <div class="section-title">Inspection Report</div>
    <table>
      <thead>
        <tr>
          <th>S.No.</th>
          <th>Inspector Name</th>
          <th>Inspection Date</th>
        </tr>
      </thead>
      <tbody>
  `;
  if (inspectionReports && inspectionReports.length > 0) {
    inspectionReports.forEach(ir => {
      html += `
        <tr>
          <td>${ir.s_no || ''}</td>
          <td>${escapeHtml(ir.inspector_name || '')}</td>
          <td>${formatDate(ir.inspection_date)}</td>
        </tr>
      `;
    });
  } else {
    html += `<tr><td colspan="3" class="no-data">No inspection reports found.</td></tr>`;
  }
  html += `
      </tbody>
    </table>
  </body>
  </html>
  `;

  const browser = await puppeteer.launch({
    headless: "new",
    args: ['--no-sandbox', '--disable-setuid-sandbox']
  });
  
  const page = await browser.newPage();
  await page.setContent(html, { waitUntil: 'networkidle0' });
  
  const pdfBuffer = await page.pdf({
    format: 'A4',
    margin: { top: '10mm', right: '10mm', bottom: '10mm', left: '10mm' }
  });

  await browser.close();
  
  return pdfBuffer;
}

module.exports = {
  generateContractPDF
};
