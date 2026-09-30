const mysql = require('mysql2/promise');
async function run() {
  const conn = await mysql.createConnection({ host: 'localhost', user: 'root', password: '', database: 'tirupati_tppl' });
  const date = '2026-08-11';
  const item_id = 256;
  const [grnRows] = await conn.execute("SELECT ROUND(SUM(quantity), 2) as sum FROM st_stock_register WHERE item_id = ? AND COALESCE(delivery_date, issue_date, created) < ? AND store_type IN ('0', '1', '3') AND status != 'N'", [item_id, date + ' 00:00:00']);
  const [indentRows] = await conn.execute("SELECT ROUND(SUM(quantity), 2) as sum FROM st_stock_register WHERE item_id = ? AND COALESCE(issue_date, created) < ? AND store_type IN ('2', '4') AND status != 'N'", [item_id, date + ' 00:00:00']);
  console.log('GRN:', grnRows[0].sum, 'Indent:', indentRows[0].sum, 'Opening:', (grnRows[0].sum || 0) - (indentRows[0].sum || 0));
  conn.end();
}
run();
