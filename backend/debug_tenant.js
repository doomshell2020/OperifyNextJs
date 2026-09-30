const { getTenantModels } = require('./src/config/sequelize');
async function run() {
  const models = await getTenantModels('tirupati_tppl');
  const user = await models.users.findOne({ where: { mobile: '9829013043' }, raw: true });
  console.log('Tenant user keys:', Object.keys(user || {}));
  console.log('is_status:', user?.is_status);
  console.log('password:', user?.password ? 'SET' : 'NULL');
  console.log('confirm_pass:', user?.confirm_pass);
  process.exit(0);
}
run().catch(e => { console.error(e.message); process.exit(1); });
