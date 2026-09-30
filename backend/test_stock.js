const mysql = require('mysql2/promise');
async function run() {
  const conn = await mysql.createConnection({ host: 'localhost', user: 'root', password: '', database: 'tirupati_tppl' });
  const sql = "SELECT item_id, store_type, DATE(COALESCE(delivery_date, issue_date, created)) as grn_date, DATE(COALESCE(issue_date, created)) as indent_date, SUM(quantity) as total_qty FROM st_stock_register WHERE status != 'N' AND item_id = 256 GROUP BY item_id, store_type, grn_date, indent_date";
  const [rows] = await conn.execute(sql);
  
  let openingStock = 0;
  for(let row of rows) {
    let storeType = String(row.store_type);
    let qty = parseFloat(row.total_qty);
    let grnDate = row.grn_date;
    if(grnDate) {
      grnDate.setHours(grnDate.getHours() - grnDate.getTimezoneOffset() / 60);
      grnDate = grnDate.toISOString().split('T')[0];
    }
    let indentDate = row.indent_date;
    if(indentDate) {
      indentDate.setHours(indentDate.getHours() - indentDate.getTimezoneOffset() / 60);
      indentDate = indentDate.toISOString().split('T')[0];
    }
    
    let currDate = '2026-08-11';
    
    if (storeType === '0' || storeType === '1' || storeType === '3') {
        if (grnDate && grnDate < currDate) {
            openingStock += qty;
        }
    }
    if (storeType === '2' || storeType === '4') {
        if (indentDate && indentDate < currDate) {
            openingStock -= qty;
        }
    }
  }
  console.log('Opening Stock on 2026-08-11:', openingStock);
  conn.end();
}
run();
