const reverseIndentService = require('./reverseIndent.service');

class ReverseIndentController {
  async getNextReverseId(req, res, next) {
    try {
      const id = await reverseIndentService.getNextReverseId(req.dbPool);
      res.json({ next_id: `I-${id}` });
    } catch (err) {
      next(err);
    }
  }

  async getReverseIndents(req, res, next) {
    try {
      const filters = req.query;
      const data = await reverseIndentService.getReverseIndents(req.dbPool, filters);
      res.json(data);
    } catch (err) {
      next(err);
    }
  }

  async saveReverseIndent(req, res, next) {
    try {
      const data = req.body;
      const result = await reverseIndentService.saveReverseIndent(req.dbPool, data);
      res.status(201).json({ message: 'Reverse Indent saved successfully', id: result });
    } catch (err) {
      next(err);
    }
  }

  async getReverseIndentDetails(req, res, next) {
    try {
      const { id } = req.params;
      const details = await reverseIndentService.getReverseIndentDetails(req.dbPool, id);
      if (!details) {
        return res.status(404).json({ message: 'Reverse Indent not found' });
      }
      res.json(details);
    } catch (err) {
      next(err);
    }
  }

  async deleteReverseIndent(req, res, next) {
    try {
      const { id } = req.params;
      await reverseIndentService.deleteReverseIndent(req.dbPool, id);
      res.json({ message: 'Reverse Indent deleted successfully' });
    } catch (err) {
      next(err);
    }
  }
  async exportPDF(req, res, next) {
    try {
      const { id } = req.params;
      const reverseIndentService = require('./reverseIndent.service');
      const details = await reverseIndentService.getReverseIndentDetails(req.dbPool, id);
      
      if (!details) {
        return res.status(404).json({ success: false, message: 'Reverse Indent not found' });
      }

      const { generateReverseIndentPDF } = require('./reverseIndent.pdf');
      const tenantDb = req.user?.db || 'default';
      const pdfBuffer = await generateReverseIndentPDF(details, tenantDb);
      
      res.setHeader('Content-Type', 'application/pdf');
      res.setHeader('Content-Disposition', `inline; filename="reverse-indent-${id}.pdf"`);
      res.end(pdfBuffer, 'binary');
    } catch (err) {
      next(err);
    }
  }
}

module.exports = new ReverseIndentController();
