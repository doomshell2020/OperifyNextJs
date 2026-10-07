require('dotenv').config();
const { render, document } = require('../src/utils/legacyPdf');

(async () => {
  try {
    console.log(`Checking PDF generation on Node ${process.version} (${process.platform})`);
    const pdf = await render(document('<h1>PDF server check</h1>'));
    if (pdf.subarray(0, 5).toString() !== '%PDF-') throw new Error('Renderer did not return a PDF');
    console.log(`PDF generation OK (${pdf.length} bytes)`);
  } catch (error) {
    console.error(error.cause?.message || error.message);
    console.error('Install Chrome with npm run pdf:install. If Chrome reports missing Linux libraries, install those on the hosting server.');
    process.exitCode = 1;
  }
})();
