const purchaseOrderService = require('./purchaseOrder.service');
const { launchPdfBrowser } = require('../../utils/pdfBrowser');
const { FRONTEND_URL } = require('../../config/environment');

class PurchaseOrderController {
  async getHoverDetails(req, res, next) {
    try {
      const { id } = req.params;
      const data = await purchaseOrderService.getHoverDetails(req.dbPool, id);
      return res.status(200).json({ success: true, data });
    } catch (error) {
      if (error.message === 'Purchase Order not found') {
        return res.status(404).json({ success: false, message: error.message });
      }
      next(error);
    }
  }

  async getDetails(req, res, next) {
    try {
      const { id } = req.params;
      const data = await purchaseOrderService.getDetails(req.dbPool, id);
      return res.status(200).json({ success: true, data });
    } catch (error) {
      if (error.message === 'Purchase Order not found') {
        return res.status(404).json({ success: false, message: error.message });
      }
      next(error);
    }
  }

  async getItemHistory(req, res, next) {
    try {
      const { itemId } = req.params;
      const data = await purchaseOrderService.getItemHistory(req.dbPool, itemId);
      return res.status(200).json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }

  async listPurchaseOrders(req, res, next) {
    try {
      const { page, limit, ...filters } = req.query;
      const data = await purchaseOrderService.listPurchaseOrders(req.dbPool, filters, page, limit);
      res.status(200).json({ success: true, ...data });
    } catch (error) {
      next(error);
    }
  }

  async revisePurchaseOrder(req, res, next) {
    try {
      const { id } = req.params;
      const result = await purchaseOrderService.revisePurchaseOrder(req.dbPool, id, req.body);
      res.status(201).json({ success: true, message: 'Purchase Order revised successfully', data: result.data });
    } catch (error) {
      next(error);
    }
  }

  async addDeliveryNote(req, res, next) {
    try {
      const { id } = req.params;
      await purchaseOrderService.addDeliveryNote(req.dbPool, id, req.body);
      res.status(200).json({ success: true, message: 'Delivery Note added successfully' });
    } catch (error) {
      next(error);
    }
  }

  async deletePurchaseOrder(req, res, next) {
    try {
      const { id } = req.params;
      await purchaseOrderService.deletePurchaseOrder(req.dbPool, id);
      res.status(200).json({ success: true, message: 'Purchase Order deleted successfully' });
    } catch (error) {
      next(error);
    }
  }

  async getNextPoNumber(req, res, next) {
    try {
      const nextId = await purchaseOrderService.getNextPoNumber(req.dbPool);
      res.status(200).json({ success: true, nextId });
    } catch (error) {
      next(error);
    }
  }

  async createPurchaseOrder(req, res, next) {
    try {
      const { po, items } = req.body;
      const result = await purchaseOrderService.createPurchaseOrder(req.dbPool, po, items);
      res.status(201).json({ success: true, message: 'Purchase Order created successfully', data: result.data });
    } catch (error) {
      next(error);
    }
  }

  async generatePdf(req, res, next) {
    let browser;
    try {
      const { id } = req.params;
      const token = req.query.token || (req.headers.authorization ? req.headers.authorization.split(' ')[1] : '');
      const frontendUrl = FRONTEND_URL;
      if (!frontendUrl) {
        const error = new Error('Set FRONTEND_URL in the backend environment to the deployed frontend address, then restart the backend.');
        error.code = 'PDF_FRONTEND_NOT_CONFIGURED';
        throw error;
      }
      let url;
      try {
        url = new URL(`/purchase-orders/${encodeURIComponent(id)}/pdf`, frontendUrl);
        if (!['http:', 'https:'].includes(url.protocol)) throw new Error('Invalid protocol');
      } catch {
        const error = new Error('FRONTEND_URL must be a valid HTTP or HTTPS frontend address.');
        error.code = 'PDF_FRONTEND_NOT_CONFIGURED';
        throw error;
      }
      url.searchParams.set('token', token);
      
      browser = await launchPdfBrowser();
      const page = await browser.newPage();
      
      let response;
      try {
        response = await page.goto(url.href, { waitUntil: 'domcontentloaded', timeout: 30000 });
      } catch {
        const error = new Error(`The PDF server could not load ${url.origin}${url.pathname}. Check FRONTEND_URL and confirm the frontend is reachable from the backend server.`);
        error.code = 'PDF_FRONTEND_UNREACHABLE';
        error.status = 502;
        throw error;
      }
      if (response && !response.ok()) throw new Error(`Purchase order print page returned HTTP ${response.status()}`);
      try {
        await page.waitForFunction(() => document.querySelector('[data-pdf-ready="true"], [data-pdf-error="true"]'), { timeout: 30000 });
      } catch {
        throw new Error('Purchase order content did not load. Check the frontend API configuration and authentication.');
      }
      if (await page.$('[data-pdf-error="true"]')) {
        throw new Error('Unable to load purchase order details for the PDF.');
      }
      // Allow fonts and logos to settle without blocking forever on a missing asset.
      await page.evaluate(async () => {
        await Promise.race([
          Promise.all([
            document.fonts.ready,
            ...Array.from(document.images).map(img => img.complete ? Promise.resolve() : new Promise(resolve => {
              img.addEventListener('load', resolve, { once: true });
              img.addEventListener('error', resolve, { once: true });
            })),
          ]),
          new Promise(resolve => setTimeout(resolve, 5000)),
        ]);
      });
      
      // Hide any potential print buttons or overlays that might show in the PDF
      await page.addStyleTag({ content: '.print\\\\:hidden { display: none !important; }' });

      const pdfUint8Array = await page.pdf({
        format: 'A4',
        printBackground: true,
        margin: { top: '0px', bottom: '0px', left: '0px', right: '0px' }
      });
      const pdfBuffer = Buffer.from(pdfUint8Array);
      
      res.setHeader('Content-Type', 'application/pdf');
      res.setHeader('Content-Disposition', `inline; filename="PO-${id}.pdf"`);
      res.send(pdfBuffer);
    } catch (error) {
      console.error('Error generating PDF:', error);
      next(error);
    } finally {
      if (browser) await browser.close();
    }
  }
}

module.exports = new PurchaseOrderController();
