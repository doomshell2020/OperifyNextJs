const path = require('node:path');
const fs = require('node:fs/promises');

const fields = ['design_sheet', 'r1', 'r2', 'r3', 'r4', 'r5'];
const uploadRoot = path.resolve(process.env.DESIGNSHEET_UPLOAD_DIR || path.join(__dirname, '../../../storage/designsheet'));
function uploadDir(req) {
  const tenant = req.dbName;
  if (!tenant || !/^[a-zA-Z0-9_]+$/.test(tenant)) throw new Error('Design sheet company context is required');
  return path.join(uploadRoot, tenant);
}
async function resolveFile(req, filename) {
  if (typeof filename !== 'string' || !filename || filename !== path.basename(filename) || /[\\/\x00]/.test(filename)) return null;
  // Retain references to files uploaded by CakePHP and earlier Next deployments.
  const roots = [uploadDir(req), path.join(__dirname, '../../../../frontend/public/designsheet'), path.join(__dirname, '../../../public/designsheet'), path.join(__dirname, '../../../../operify-cake-php-old-project/webroot/designsheet')];
  for (const root of roots) {
    const target = path.resolve(root, filename);
    try {
      const realRoot = await fs.realpath(root);
      const realTarget = await fs.realpath(target);
      if (!realTarget.startsWith(realRoot + path.sep)) continue;
      if ((await fs.stat(realTarget)).isFile()) return realTarget;
    } catch (error) { if (!['ENOENT', 'ENOTDIR'].includes(error.code)) throw error; }
  }
  return null;
}
async function download(req, res, next) {
  try {
    const id = Number(req.params.id);
    const field = req.params.field;
    if (!Number.isSafeInteger(id) || id <= 0 || !fields.includes(field)) return res.status(400).json({message:'Invalid design sheet file'});
    const sheet = await req.models.designsheet.findByPk(id, {raw:true});
    if (!sheet) return res.status(404).json({message:'Design sheet not found'});
    const file = await resolveFile(req, sheet[field]);
    if (!file) return res.status(404).json({message:'Design sheet file not found'});
    res.setHeader('Cache-Control', 'private, no-store');
    res.download(file, sheet[field], error => { if (error) next(error); });
  } catch (error) { next(error); }
}
module.exports = {fields, uploadDir, resolveFile, download};
