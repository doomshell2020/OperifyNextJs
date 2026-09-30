const mysql = require('mysql2/promise');
async function run() {
  const conn = await mysql.createConnection({ host: 'localhost', user: 'root', password: '', database: 'tirupati_tppl' });
  const [gr] = await conn.execute("SELECT id, purchaseorder_id, bill_no FROM st_goodsreceive WHERE id IN (2800, 2808)");
  console.log('GR:', gr);
  const [po] = await conn.execute("SELECT id, purchaseorder_id FROM st_purchaseorder WHERE id IN (2800, 2808) OR purchaseorder_id IN ('2627-299', '2627-160')");
  console.log('PO:', po);
  conn.end();
}
run();
