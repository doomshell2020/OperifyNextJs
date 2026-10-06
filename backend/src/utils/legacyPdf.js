const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');
const escapeHtml = value => String(value ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
const amount = value => Number(value || 0).toFixed(2);
const ucfirst = value => String(value || '').replace(/^./u, char => char.toUpperCase());
const titleName = value => String(value || '').toLowerCase().replace(/\b\w/g, char => char.toUpperCase());
function date(value, textualMonth = false) {
  if (!value) return '';
  const match = typeof value === 'string' && value.match(/^(\d{4})-(\d{2})-(\d{2})/);
  const d = match ? null : new Date(value);
  if (!match && Number.isNaN(d.getTime())) return '';
  // Sequelize's MySQL connection reads DATETIME values as UTC; keep their stored calendar date.
  const [year, month, day] = match ? match.slice(1) : [d.getUTCFullYear(), String(d.getUTCMonth() + 1).padStart(2, '0'), String(d.getUTCDate()).padStart(2, '0')];
  return `${day}-${textualMonth ? ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][Number(month)-1] : month}-${year}`;
}
function logoSrc(site = {}) {
  const file = site.small_logo || site.logo;
  if (!file || path.basename(file) !== file) return '';
  const roots = [path.resolve(__dirname, '../../public/uploads/logos'), path.resolve(__dirname, '../../../operify-cake-php-old-project/webroot/images')];
  for (const root of roots) {
    const filename = path.join(root, file);
    if (fs.existsSync(filename)) {
      const extension = path.extname(file).slice(1).toLowerCase();
      return `data:image/${extension === 'jpg' ? 'jpeg' : extension};base64,${fs.readFileSync(filename).toString('base64')}`;
    }
  }
  return '';
}
// These four PHP templates share this letterhead; their document bodies remain module-specific.
function letterhead(site = {}, setting = {}) {
  site = site || {}; setting = setting || {};
  const logo = logoSrc(site);
  return `<div class="letterhead">${logo ? `<img class="letterhead-logo" src="${logo}" alt="">` : ''}<div class="letterhead-company">${escapeHtml(setting.first_name || site.company_name || '')}</div><div class="letterhead-address">${escapeHtml(site.address1)}<br><b>Phone</b> :${escapeHtml(site.phone)}<br><b>Email</b> : <u>${escapeHtml(site.email)}</u><br><b>Website</b> : ${escapeHtml(site.website)}</div></div>`;
}
const css = `
  @page { size: A4 portrait; margin: 10mm 10mm 20mm; }
  html, body { margin:0; padding:0; color:#000; background:#fff; font:8pt Arial, Helvetica, sans-serif; }
  body {padding:0 1.8pt;}
  * { box-sizing:border-box; }
  table { width:100%; border-collapse:collapse; border-spacing:0; table-layout:fixed; }
  th, td { padding:2.6pt 3pt; line-height:10pt; vertical-align:top; text-align:left; font-size:8pt; font-weight:normal; }
  th { font-weight:bold; }
  .grid th, .grid td { border:.6pt solid #000; }
  thead { display:table-header-group; }
  tr { break-inside:avoid; }
  .letterhead { height:82.1pt; position:relative; }
  .letterhead-logo { position:absolute; left:22.2pt; top:.4pt; width:62pt; height:62pt; object-fit:contain; }
  .letterhead-company { position:absolute; left:22.8pt; bottom:4pt; font-size:10pt; line-height:12.5pt; font-weight:bold; }
  .letterhead-address { position:absolute; top:2pt; right:1pt; width:50%; text-align:right; font-size:8pt; line-height:10pt; white-space:pre-line; }
  .document-heading { border:.6pt solid #000; break-inside:avoid; }
  .document-title { margin:0; border-top:.6pt solid #000; border-bottom:.6pt solid #000; height:21pt; line-height:12.5pt; padding-top:0; font-size:10pt; font-weight:bold; text-align:center; }
  .metadata { margin:0 .2pt; width:calc(100% - .4pt); }
  .section-title { font-size:10pt; line-height:12.5pt; font-weight:bold; text-align:center; margin:7.5pt 0; break-after:avoid; }
  .right { text-align:right; }
  .center { text-align:center; }
  .signatures { break-inside:avoid; }
  .signatures td:first-child {padding-left:7pt}
  .signatures td:last-child {padding-right:7pt}
`;
async function render(html, options = {}) {
  const browser = await puppeteer.launch({ headless:true, args:['--no-sandbox','--disable-setuid-sandbox'] });
  try {
    const page = await browser.newPage();
    await page.setContent(html, {waitUntil:'networkidle0'});
    await page.evaluate(async () => { await document.fonts.ready; await Promise.all(Array.from(document.images).map(image => image.decode().catch(() => {}))); });
    return Buffer.from(await page.pdf({format:'A4', preferCSSPageSize:true, printBackground:true, ...options}));
  } finally { await browser.close(); }
}
const document = (body, extraCss = '') => `<!doctype html><html><head><meta charset="utf-8"><style>${css}${extraCss}</style></head><body>${body}</body></html>`;
module.exports = { escapeHtml, amount, ucfirst, titleName, date, logoSrc, letterhead, css, document, render };
