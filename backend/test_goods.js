const mysql = require('mysql2/promise');
async function run() {
  const conn = await mysql.createConnection({ host: 'localhost', user: 'root', password: '', database: 'tirupati_tppl' });
  const [rows] = await conn.execute("SELECT goods_id FROM st_stock_register WHERE item_id=256 AND store_type IN ('0', '1', '3') AND (DATE(created)='2026-08-04' OR DATE(issue_date)='2026-08-04' OR DATE(delivery_date)='2026-08-04')");
  console.log('goods_id for those rows:', rows);
  
  const goodsIds = rows.map(r => r.goods_id).filter(id => id != null);
  if (goodsIds.length > 0) {
    const [gr] = await conn.execute(`SELECT id, purchaseorder_id, bill_no FROM st_goodsreceive WHERE id IN (${goodsIds.join(',')})`);
    console.log('GR matched by goods_id:', gr);
  }
  conn.end();
}
run();
