const mysql = require('mysql2/promise');
async function run() {
  const conn = await mysql.createConnection({ host: 'localhost', user: 'root', password: '', database: 'tirupati_tppl' });
  const [rows] = await conn.execute("SELECT id, po_id, purchaseorder_id, goods_id, quantity, created, issue_date, delivery_date FROM st_stock_register WHERE quantity=12057");
  console.log('12057 rows:', rows);
  
  const [rows2] = await conn.execute("SELECT id, po_id, purchaseorder_id, goods_id, quantity, created, issue_date, delivery_date FROM st_stock_register WHERE quantity=30636");
  console.log('30636 rows:', rows2);
  conn.end();
}
run();
