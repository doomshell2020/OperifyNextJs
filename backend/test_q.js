const mysql = require('mysql2/promise');
async function run() {
  const conn = await mysql.createConnection({ host: 'localhost', user: 'root', password: '', database: 'tirupati_tppl' });
  const [rows] = await conn.execute("SELECT id, po_id, purchaseorder_id, quantity, created, issue_date, delivery_date FROM st_stock_register WHERE item_id=256 AND store_type IN ('0', '1', '3') AND (DATE(created)='2026-08-04' OR DATE(issue_date)='2026-08-04' OR DATE(delivery_date)='2026-08-04')");
  console.log(rows);
  conn.end();
}
run();
