const { centralModels } = require('./src/config/sequelize');
async function run() {
  // Test findCentralUserByMobile
  const user = await centralModels.users.findOne({ where: { mobile: '9829013043' }, raw: true });
  console.log('Central user:', user ? { id: user.id, mobile: user.mobile, db: user.db } : null);

  // Check if central users model has 'db' field defined
  const cols = Object.keys(centralModels.users.rawAttributes);
  console.log('Central users model columns:', cols);
  process.exit(0);
}
run().catch(e => { console.error(e.message); process.exit(1); });
