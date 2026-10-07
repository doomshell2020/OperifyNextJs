async function launchPdfBrowser() {
  // Puppeteer is an ES module; load it only when a PDF is requested.
  const { default: puppeteer } = await import('puppeteer');
  try {
    return await puppeteer.launch({
      headless: true,
      args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage']
    });
  } catch (cause) {
    console.error('PDF browser launch failed:', cause);
    const error = new Error('The server PDF browser could not start. Run npm run pdf:check in the backend deployment and check the hosting logs.');
    error.code = 'PDF_BROWSER_UNAVAILABLE';
    error.cause = cause;
    throw error;
  }
}

module.exports = { launchPdfBrowser };
