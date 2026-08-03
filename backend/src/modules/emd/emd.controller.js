const emdService = require('./emd.service');

class EmdController {
  async getEmdList(req, res, next) {
    try {
      const dbPool = req.dbPool;
      const { bg_for, status, bankguaranteeno, datefrom, dateto, due_from, due_to } = req.query;

      const filters = {
        bg_for: bg_for || undefined,
        status: status || undefined,
        bankguaranteeno: bankguaranteeno || undefined,
        datefrom: datefrom || undefined,
        dateto: dateto || undefined,
        due_from: due_from || undefined,
        due_to: due_to || undefined,
      };

      const data = await emdService.getEmdList(dbPool, filters);
      res.json({ success: true, data });
    } catch (err) {
      next(err);
    }
  }

  async getEmdDetail(req, res, next) {
    try {
      const dbPool = req.dbPool;
      const { id } = req.params;
      const data = await emdService.getEmdDetail(dbPool, parseInt(id, 10));
      if (!data) return res.status(404).json({ success: false, message: 'EMD record not found' });
      res.json({ success: true, data });
    } catch (err) {
      next(err);
    }
  }
}

module.exports = new EmdController();
