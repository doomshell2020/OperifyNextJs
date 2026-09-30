const { Sequelize, QueryTypes } = require('sequelize');
const sequelize = new Sequelize('operify_erp', 'root', '', { host: 'localhost', dialect: 'mysql' });
async function run() {
  const res = await sequelize.query("SELECT * FROM st_stock_register WHERE item_id = 975 LIMIT 10;", { type: QueryTypes.SELECT });
  console.log("Tx Count for Alloy Rod T-4:", res.length);
  console.log(res);
}
run();
