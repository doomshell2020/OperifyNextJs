const mysql = require('mysql2/promise');
async function run() {
  const conn = await mysql.createConnection({ host: 'localhost', user: 'root', password: '', database: 'tirupati_tppl' });
  const sql = "SELECT item_id, store_type, DATE(COALESCE(delivery_date, issue_date, created)) as grn_date, DATE(COALESCE(issue_date, created)) as indent_date, SUM(quantity) as total_qty FROM st_stock_register WHERE status != 'N' AND item_id = 256 GROUP BY item_id, store_type, grn_date, indent_date";
  const [rows] = await conn.execute(sql);
  console.log(rows);
  conn.end();
}
run();
