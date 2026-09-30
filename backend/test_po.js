const mysql = require('mysql2/promise');
async function run() {
  const conn = await mysql.createConnection({ host: 'localhost', user: 'root', password: '', database: 'tirupati_tppl' });
  const [rows] = await conn.execute("SELECT id, quantity, po_id, purchaseorder_id, goods_id, created, issue_date, delivery_date FROM st_stock_register WHERE item_id=256 AND quantity IN (12057, 30636)");
  console.log(rows);
  conn.end();
}
run();
