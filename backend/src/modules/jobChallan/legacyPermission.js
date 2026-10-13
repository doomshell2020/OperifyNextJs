const { requirePermission } = require('../../middleware/permission');
// Session grants are authoritative. Auth applies the scoped PHP fallbacks for
// absent action labels; configured role denials remain denied here.
module.exports = (controller, action) =>
  requirePermission(`legacy:admin/${controller}/${action}`.toLowerCase());
