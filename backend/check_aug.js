const mysql = require('mysql2/promise');
async function run() {
  const conn = await mysql.createConnection({ host: 'localhost', user: 'root', password: '', database: 'tirupati_tppl', dateStrings: true });
  const [rows] = await conn.execute("SELECT id, created, issue_date, delivery_date, quantity, store_type FROM st_stock_register WHERE item_id = 256 AND created >= '2026-08-01' AND created < '2026-08-10'");
  console.log(rows);
  conn.end();
}
run();
