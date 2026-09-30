const mysql = require('mysql2/promise');
async function run() {
  const conn = await mysql.createConnection({ host: 'localhost', user: 'root', password: '', database: 'tirupati_tppl' });
  const [rows] = await conn.execute("SELECT id, po_id, purchaseorder_id, goods_id, vendor_id FROM st_stock_register WHERE store_type='1' AND po_id IS NOT NULL LIMIT 5");
  console.log(rows);
  conn.end();
}
run();
