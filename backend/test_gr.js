const mysql = require('mysql2/promise');
async function run() {
  const conn = await mysql.createConnection({ host: 'localhost', user: 'root', password: '', database: 'tirupati_tppl' });
  const [gr] = await conn.execute("SELECT id, purchaseorder_id, bill_no FROM st_goodsreceive WHERE purchaseorder_id IN (2800, 2808)");
  console.log('GR linked by purchaseorder.id:', gr);
  conn.end();
}
run();
