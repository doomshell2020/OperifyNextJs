const mysql = require('mysql2/promise');
async function run() {
  const conn = await mysql.createConnection({ host: 'localhost', user: 'root', password: '', database: 'tirupati_tppl' });
  const [rows] = await conn.execute("SELECT * FROM st_stock_register WHERE item_id = 256 AND created >= '2026-08-01'");
  console.log(rows);
  conn.end();
}
run();
