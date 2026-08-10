const grnInspectionService = require('./grnInspection.service');
const puppeteer = require('puppeteer');

class GrnInspectionController {
  async listInspections(req, res, next) {
    try {
      const { page = 1, limit = 10, ...filters } = req.query;
      const data = await grnInspectionService.listInspections(req.dbPool, filters, parseInt(page), parseInt(limit));
      res.status(200).json({ success: true, ...data });
    } catch (error) {
      next(error);
    }
  }

  async getDetails(req, res, next) {
    try {
      const { id } = req.params;
      const data = await grnInspectionService.getDetails(req.dbPool, id);
      return res.status(200).json({ success: true, data });
    } catch (error) {
      if (error.message === 'Inspection not found') {
        return res.status(404).json({ success: false, message: error.message });
      }
      next(error);
    }
  }

  async createInspection(req, res, next) {
    try {
      const { inspection, items } = req.body;
      const result = await grnInspectionService.createInspection(req.dbPool, inspection, items);
      res.status(201).json({ success: true, message: 'GRN Inspection created successfully', data: result.data });
    } catch (error) {
      next(error);
    }
  }

  async getNextInspectionNumber(req, res, next) {
    try {
      const nextId = await grnInspectionService.getNextInspectionNumber(req.dbPool);
      res.status(200).json({ success: true, nextId });
    } catch (error) {
      next(error);
    }
  }
  
  async getPoDetails(req, res, next) {
    try {
      const { po_id } = req.params;
      const data = await grnInspectionService.getPoDetails(req.dbPool, po_id);
      res.status(200).json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }
}

module.exports = new GrnInspectionController();
