const repo = require('./vendor.repository');

class VendorController {
  async getVendor(req, res, next) {
    try {
      const { id } = req.params;
      if (!/^[1-9]\d*$/.test(id) || !Number.isSafeInteger(Number(id))) return res.status(400).json({success:false,message:'Invalid vendor ID'});
      const data = await repo.getVendorById(req.dbPool, id);
      if (!data) {
        return res.status(404).json({ success: false, message: 'Vendor not found' });
      }
      res.json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }

  async searchVendors(req, res, next) {
    try {
      const q = req.query.q || '';
      if (!q || q.length < 2) {
        return res.json({ success: true, data: [] });
      }
      const data = await repo.searchVendors(req.dbPool, q);
      res.json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }

  async updateVendor(req, res, next) {
    try {
      const { id } = req.params;
      if (!/^[1-9]\d*$/.test(id) || !Number.isSafeInteger(Number(id))) return res.status(400).json({success:false,message:'Invalid vendor ID'});
      const existing = await repo.getVendorById(req.dbPool, id);
      if (!existing) return res.status(404).json({success:false,message:'Vendor not found'});
      await req.dbPool.transaction(transaction => repo.updateVendor(req.dbPool, id, {...existing,...req.body}, transaction));
      res.json({ success: true, message: 'Vendor updated successfully' });
    } catch (error) {
      next(error);
    }
  }
}

module.exports = new VendorController();
