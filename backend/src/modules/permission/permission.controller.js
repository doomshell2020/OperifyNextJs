const permissionService = require('./permission.service');

class PermissionController {
  
  _checkSuperadmin = (req) => {
    if (req.user.role_id != 101) {
      const error = new Error('Forbidden: Superadmin access required');
      error.status = 403;
      throw error;
    }
  }

  getManagers = async (req, res, next) => {
    try {
      this._checkSuperadmin(req);
      const data = await permissionService.getManagers(req.dbPool);
      return res.status(200).json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }

  addManager = async (req, res, next) => {
    try {
      this._checkSuperadmin(req);
      await permissionService.addManager(req.dbPool, req.body.name);
      return res.status(200).json({ success: true, message: 'Manager added successfully' });
    } catch (error) {
      next(error);
    }
  }

  addLabel = async (req, res, next) => {
    try {
      this._checkSuperadmin(req);
      await permissionService.addLabel(req.dbPool, req.body);
      return res.status(200).json({ success: true, message: 'Url added successfully.' });
    } catch (error) {
      next(error);
    }
  }

  getRoles = async (req, res, next) => {
    try {
      this._checkSuperadmin(req);
      const data = await permissionService.getRoles(req.dbPool);
      return res.status(200).json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }

  getAccess = async (req, res, next) => {
    try {
      this._checkSuperadmin(req);
      const { role_id } = req.query;
      if (!role_id) throw new Error('role_id is required');
      const data = await permissionService.getAccess(req.dbPool, role_id);
      return res.status(200).json({ success: true, data });
    } catch (error) {
      next(error);
    }
  }

  updateAccess = async (req, res, next) => {
    try {
      this._checkSuperadmin(req);
      await permissionService.updateAccess(req.dbPool, req.body);
      return res.status(200).json({ success: true, message: 'Permissions updated successfully.' });
    } catch (error) {
      next(error);
    }
  }
}

module.exports = new PermissionController();
