const mysql = require('mysql2/promise');
async function run() {
  const conn = await mysql.createConnection({ host: 'localhost', user: 'root', password: '', database: 'tirupati_tppl' });
  const [rows] = await conn.execute("SELECT id, purchaseorder_id, bill_no, total_qty FROM st_goodsreceive WHERE id IN (2171, 2178)");
  console.log('GR rows:', rows);
  conn.end();
}
run();
