const purchaseOrderService = require('./purchaseOrder.service');
const puppeteer = require('puppeteer');

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
      const { page = 1, limit = 10, ...filters } = req.query;
      const data = await purchaseOrderService.listPurchaseOrders(req.dbPool, filters, parseInt(page), parseInt(limit));
      res.status(200).json({ success: true, ...data });
    } catch (error) {
      next(error);
    }
  }

  async revisePurchaseOrder(req, res, next) {
    try {
      const { id } = req.params;
      await purchaseOrderService.revisePurchaseOrder(req.dbPool, id, req.body);
      res.status(200).json({ success: true, message: 'Purchase Order revised successfully' });
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
    try {
      const { id } = req.params;
      const token = req.query.token || (req.headers.authorization ? req.headers.authorization.split(' ')[1] : '');
      
      const browser = await puppeteer.launch({ args: ['--no-sandbox', '--disable-setuid-sandbox'] });
      const page = await browser.newPage();
      
      const url = `http://localhost:3000/purchase-orders/${id}/pdf?token=${token}`;
      await page.goto(url, { waitUntil: 'networkidle0' });
      
      // Hide any potential print buttons or overlays that might show in the PDF
      await page.addStyleTag({ content: '.print\\\\:hidden { display: none !important; }' });

      const pdfUint8Array = await page.pdf({
        format: 'A4',
        printBackground: true,
        margin: { top: '0px', bottom: '0px', left: '0px', right: '0px' }
      });
      const pdfBuffer = Buffer.from(pdfUint8Array);
      
      await browser.close();
      
      res.setHeader('Content-Type', 'application/pdf');
      res.setHeader('Content-Disposition', `inline; filename="PO-${id}.pdf"`);
      res.send(pdfBuffer);
    } catch (error) {
      console.error('Error generating PDF:', error);
      res.status(500).json({ success: false, message: 'Failed to generate PDF' });
    }
  }
}

module.exports = new PurchaseOrderController();
