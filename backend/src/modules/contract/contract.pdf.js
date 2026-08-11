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

async function generateContractPDF(contractData, tenantDb = 'default') {
  const { contract, items, productionOrders, inspectionReports } = contractData;

  // Resolve Logo
  let logoSrc = 'https://staging.operify.in/image/logo.png';
  try {
    const dirPath = path.join(__dirname, '../../../public/uploads/logos');
    if (fs.existsSync(dirPath)) {
      const files = fs.readdirSync(dirPath);
      const logoFile = files.find(f => f.startsWith(`${tenantDb}_logo.`));
      if (logoFile) {
        const logoPath = path.join(dirPath, logoFile);
        const ext = path.extname(logoFile).substring(1); // e.g. png
        const base64Data = fs.readFileSync(logoPath).toString('base64');
        logoSrc = `data:image/${ext};base64,${base64Data}`;
      }
    }
  } catch(e) {
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
        font-size: 11px;
        margin: 0;
        padding: 20px;
        color: #000;
      }
      table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
      }
      th, td {
        border: 1px solid #000;
        padding: 4px 6px;
        text-align: left;
      }
      th {
        background-color: #f2f2f2;
        font-weight: bold;
      }
      .header-table {
        border: 1px solid #000;
        margin-bottom: 10px;
        width: 100%;
      }
      .header-table td {
        border: none;
      }
      .title-box {
        text-align: center;
        font-size: 14px;
        font-weight: bold;
        border: 1px solid #000;
        padding: 5px;
        background-color: #f2f2f2;
        margin-bottom: 10px;
      }
      .section-title {
        text-align: center;
        font-size: 12px;
        font-weight: bold;
        margin: 10px 0;
      }
      .no-data {
        text-align: center;
        font-style: italic;
      }
    </style>
  </head>
  <body>
    <!-- Header -->
    <table class="header-table">
      <tr>
        <td style="width: 30%; vertical-align: top;">
          <img src="${logoSrc}" alt="Logo" style="max-height: 50px;">
          <div style="font-weight: bold; font-size: 14px; margin-top: 10px;">TIRUPATI PLASTOMATICS PVT. LTD.</div>
        </td>
        <td style="width: 70%; text-align: right; vertical-align: top; font-size: 10px;">
          B-141(A), Rd Number 9D, Vishwakarma Industrial Area, Jaipur,<br>
          Rajasthan 302013<br>
          <b>Phone:</b> 9829287189<br>
          <b>Email:</b> contact@tirupatiplastomatics.com<br>
          <b>Website:</b> www.tirupatiplastomatics.com
        </td>
      </tr>
    </table>

    <div class="title-box">Contract Details</div>

    <table>
      <tr>
        <td colspan="2"><b>Work Order:-</b> ${contract.workorder || ''}</td>
      </tr>
      <tr>
        <td style="width: 50%;"><b>Title:-</b> ${contract.title || ''}</td>
        <td style="width: 50%;"><b>Issue Date:-</b> ${formatDate(contract.issuedate)}</td>
      </tr>
      <tr>
        <td><b>Contract Start Date:-</b> ${formatDate(contract.contract_start_date)}</td>
        <td><b>Contract End Date:-</b> ${formatDate(contract.contract_end_date)}</td>
      </tr>
      <tr>
        <td><b>Supplier Name:-</b> ${contract.vendor_name || ''}</td>
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
          <td><b>Product:-</b> ${item.item_name || ''}</td>
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
              <th colspan="4" style="text-align: center;">Raw Material</th>
            </tr>
            <tr>
              <th style="width: 5%;">No.</th>
              <th style="width: 45%;">Item Name</th>
              <th style="width: 25%; text-align: right;">Qty(As per Design)</th>
              <th style="width: 25%; text-align: right;">Pending Qty</th>
            </tr>
          </thead>
          <tbody>
        `;
        rmList.forEach((rm, idx) => {
          html += `
            <tr>
              <td>${idx + 1}.</td>
              <td>${rm.item_name}</td>
              <td style="text-align: right;">${formatQty(rm.as_per_design)}</td>
              <td style="text-align: right;">${formatQty(rm.pending_qty)}</td>
            </tr>
          `;
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
          <td>${po.product_name || ''}</td>
          <td style="text-align: right;">${formatQty(po.plannedqty)}</td>
          <td style="text-align: right;">${formatQty(po.prepared_qty)}</td>
          <td>${formatDate(po.startdate)}</td>
          <td>${formatDate(po.enddate)}</td>
          <td>${po.status || ''}</td>
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
          <td>${ir.inspector_name || ''}</td>
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
