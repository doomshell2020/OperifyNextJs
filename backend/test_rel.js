const mysql = require('mysql2/promise');
async function run() {
  const conn = await mysql.createConnection({ host: 'localhost', user: 'root', password: '', database: 'tirupati_tppl' });
  const [po] = await conn.execute("SELECT id FROM st_purchase_order WHERE id = 26");
  const [gr] = await conn.execute("SELECT id FROM st_goods_received WHERE id = 49");
  console.log('PO:', po, 'GR:', gr);
  conn.end();
}
run();
