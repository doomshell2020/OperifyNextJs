const { centralSequelize } = require('../../config/sequelize');
const { select } = require('./legacyStore');
// The current PHP enables these modules for all authenticated users and leaves
// its per-user beforeFilter check commented out. Honour configured action labels
// when present, without inventing grants or writing permission records.
function permission(controller, action) {
  return async (req, res, next) => {
    try {
      if (Number(req.user?.role_id) === 101) return next();
      const url = `admin/${controller}/${action}`.toLowerCase();
      const rows = await select(centralSequelize, 'SELECT url FROM permission_label WHERE LOWER(TRIM(url))=:url', { url });
      if (rows.length && !(req.user?.permissions || []).includes(`legacy:${url}`)) {
        return res.status(403).json({ success: false, message: 'You do not have permission for this action.' });
      }
      next();
    } catch (error) { next(error); }
  };
}
module.exports = permission;
