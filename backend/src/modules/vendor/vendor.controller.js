const repo = require('./vendor.repository');

class VendorController {
  async getVendor(req, res, next) {
    try {
      const { id } = req.params;
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
    let connection;
    try {
      const { id } = req.params;
      
      connection = await req.dbPool.getConnection();
      await connection.beginTransaction();
      
      const success = await repo.updateVendor(connection, id, req.body);
      
      if (!success) {
        await connection.rollback();
        return res.status(404).json({ success: false, message: 'Vendor not found' });
      }

      await connection.commit();
      res.json({ success: true, message: 'Vendor updated successfully' });
    } catch (error) {
      if (connection) await connection.rollback();
      next(error);
    } finally {
      if (connection) connection.release();
    }
  }
}

module.exports = new VendorController();
