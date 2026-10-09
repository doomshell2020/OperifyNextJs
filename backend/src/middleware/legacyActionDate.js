const { QueryTypes } = require('sequelize');

function isLegacyToday(value, now = new Date()) {
  if (!value) return false;
  const date = value instanceof Date ? value.toISOString().slice(0, 10) : String(value).slice(0, 10);
  return date === new Intl.DateTimeFormat('en-CA', {
    timeZone: 'Asia/Kolkata', year: 'numeric', month: '2-digit', day: '2-digit',
  }).format(now);
}

// PHP Indentpo/index.ctp displays Edit/Delete only on the issue day. Read the
// persisted date in the selected tenant, never a date supplied in the request.
async function requireCurrentIndent(req, res, next) {
  try {
    const [row] = await req.dbPool.query('SELECT issue_date FROM indentpo WHERE indent_id=:id LIMIT 1', {
      replacements: { id: req.params.indent_id }, type: QueryTypes.SELECT,
    });
    if (!row) return res.status(404).json({ success: false, message: 'Indent not found' });
    if (!isLegacyToday(row.issue_date)) return res.status(403).json({ success: false,
      error: { code: 'FORBIDDEN', message: 'Indents can only be edited or deleted on their issue day.' } });
    next();
  } catch (error) { next(error); }
}
module.exports = { isLegacyToday, requireCurrentIndent };
