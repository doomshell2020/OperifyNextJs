const indentService = require('./indent.service');

class IndentController {
  async getNextIndentId(req, res, next) {
    try {
      const next_id = await indentService.getNextIndentId(req.dbPool);
      return res.status(200).json({ success: true, data: { next_id } });
    } catch (error) {
      next(error);
    }
  }

  async searchItems(req, res, next) {
    try {
      const { q } = req.query;
      const items = await indentService.searchItems(req.dbPool, q || '');
      return res.status(200).json({ success: true, data: items });
    } catch (error) {
      next(error);
    }
  }

  async addTempItem(req, res, next) {
    try {
      const userId = req.user?.id;
      const item = await indentService.addTempItem(req.dbPool, req.body, userId);
      return res.status(201).json({ success: true, data: item });
    } catch (error) {
      if (error.message.includes('required')) {
        return res.status(400).json({ success: false, error: { message: error.message } });
      }
      next(error);
    }
  }

  async removeTempItem(req, res, next) {
    try {
      await indentService.removeTempItem(req.dbPool, req.params.id);
      return res.status(200).json({ success: true, message: 'Item removed' });
    } catch (error) {
      if (error.message === 'Temp item not found') {
        return res.status(404).json({ success: false, error: { message: error.message } });
      }
      next(error);
    }
  }

  async getTempItems(req, res, next) {
    try {
      const items = await indentService.getTempItems(req.dbPool, req.params.indent_id);
      return res.status(200).json({ success: true, data: items });
    } catch (error) {
      next(error);
    }
  }

  async finalizeIndent(req, res, next) {
    try {
      const userId = req.user?.id;
      await indentService.finalizeIndent(req.dbPool, req.body.indent_id, userId);
      return res.status(200).json({ success: true, message: 'Indent finalized successfully' });
    } catch (error) {
      if (error.message === 'No items to finalize' || error.message.includes('required')) {
        return res.status(400).json({ success: false, error: { message: error.message } });
      }
      next(error);
    }
  }

  async listIndents(req, res, next) {
    try {
      const { indent_id, date_from, date_to } = req.query;
      const indents = await indentService.listIndents(req.dbPool, { indent_id, date_from, date_to });
      return res.status(200).json({ success: true, data: indents });
    } catch (error) {
      next(error);
    }
  }

  async getIndentDetail(req, res, next) {
    try {
      const detail = await indentService.getIndentDetail(req.dbPool, req.params.indent_id);
      return res.status(200).json({ success: true, data: detail });
    } catch (error) {
      if (error.message === 'Indent not found') {
        return res.status(404).json({ success: false, error: { message: error.message } });
      }
      next(error);
    }
  }

  async getPendingIndents(req, res, next) {
    try {
      const indents = await indentService.getPendingIndents(req.dbPool);
      return res.status(200).json({ success: true, data: indents });
    } catch (error) {
      next(error);
    }
  }
}

module.exports = new IndentController();
