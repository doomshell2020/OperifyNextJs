const { requirePermission } = require('../../middleware/permission');
// The existing PHP role URL grant is authoritative. Missing labels deny access.
module.exports = (controller, action) =>
  requirePermission(`legacy:admin/${controller}/${action}`.toLowerCase());
