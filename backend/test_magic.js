const mysql = require('mysql2/promise');
async function run() {
  const conn = await mysql.createConnection({ host: 'localhost', user: 'root', password: '', database: 'tirupati_tppl' });
  const [rows] = await conn.execute("SELECT * FROM st_stock_register WHERE quantity=12057");
  console.log('Qty 12057 rows:', rows);
  const [rows2] = await conn.execute("SELECT * FROM st_stock_register WHERE po_id='2627-299'");
  console.log('PO 2627-299 rows:', rows2);
  conn.end();
}
run();
