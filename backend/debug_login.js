const { centralSequelize } = require('./src/config/sequelize');
async function run() {
  const [rows] = await centralSequelize.query("SELECT id, mobile, db FROM users WHERE mobile='9829013043'");
  console.log('User row:', JSON.stringify(rows));

  if (rows.length > 0) {
    const dbName = rows[0].db;
    const [tenantRows] = await centralSequelize.query(
      `SELECT id, mobile, password, confirm_pass FROM ${dbName}.users WHERE mobile='9829013043' LIMIT 1`
    );
    console.log('Tenant user:', JSON.stringify(tenantRows));
  }
  process.exit(0);
}
run().catch(e => { console.error(e.message); process.exit(1); });
