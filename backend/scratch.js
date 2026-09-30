const mysql = require('mysql2/promise');
require('dotenv').config();
async function run() {
  const conn = await mysql.createConnection({
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    database: 'tirupati_tppl'
  });
  
  const [tables] = await conn.execute('SHOW TABLES LIKE "%stock%"');
  console.log('Tables:', tables);

  const tableName = 'st_stock_register';

  const [items] = await conn.execute('SELECT id, item_name FROM st_additem WHERE item_name LIKE "%ALUMINIUM ROD EC GRADE%"');
  console.log('Items:', items);
  
  if (items.length > 0) {
    const item_id = items[0].id;
    const date = '2026-08-11';
    
    // OLD Logic
    const [grnStock] = await conn.execute(`SELECT ROUND(SUM(quantity), 2) as sum FROM ${tableName} WHERE item_id = ? AND created < ? AND store_type IN ("0", "1", "3")`, [item_id, date]);
    const [indentStock] = await conn.execute(`SELECT ROUND(SUM(quantity), 2) as sum FROM ${tableName} WHERE item_id = ? AND created < ? AND store_type IN ("2", "4")`, [item_id, date]);
    console.log('Old Opening:', grnStock[0].sum - indentStock[0].sum);

    const [grnStockAll] = await conn.execute(`SELECT SUM(quantity) as sum FROM ${tableName} WHERE item_id = ? AND created < ? AND store_type IN ("0", "1", "3")`, [item_id, date]);
    const [indentStockAll] = await conn.execute(`SELECT SUM(quantity) as sum FROM ${tableName} WHERE item_id = ? AND created < ? AND store_type IN ("2", "4")`, [item_id, date]);
    console.log('Old Opening Without Round:', grnStockAll[0].sum - indentStockAll[0].sum);
    
    // NEW Logic
    const [newOpening] = await conn.execute(`
      SELECT ROUND(COALESCE(SUM(CASE WHEN store_type IN ('0','1','3') THEN quantity ELSE 0 END), 0) - 
            COALESCE(SUM(CASE WHEN store_type IN ('2','4') THEN quantity ELSE 0 END), 0), 3) as initial_opening
      FROM ${tableName}
      WHERE created < ? AND item_id = ?
    `, [date, item_id]);
    console.log('New Opening:', newOpening[0].initial_opening);

    // Get old transactions for the date range
    const [recStock] = await conn.execute(`SELECT ROUND(SUM(quantity), 2) as sum FROM ${tableName} WHERE DATE(created) >= "2026-08-11" AND DATE(created) <= "2026-08-18" AND item_id = ? AND store_type IN ("0", "1", "3") AND status != "N"`, [item_id]);
    const [dispStock] = await conn.execute(`SELECT ROUND(SUM(quantity), 2) as sum FROM ${tableName} WHERE DATE(created) >= "2026-08-11" AND DATE(created) <= "2026-08-18" AND item_id = ? AND store_type IN ("2", "4") AND status != "N"`, [item_id]);
    
    console.log('Old Total Received:', recStock[0].sum);
    console.log('Old Total Dispatched:', dispStock[0].sum);

    // Old day by day logic
    let curOpening = grnStock[0].sum - indentStock[0].sum;
    for (let i = 11; i <= 18; i++) {
        let loopDate = `2026-08-${String(i).padStart(2, '0')}`;
        const [r] = await conn.execute(`SELECT SUM(quantity) as sum FROM ${tableName} WHERE DATE(created) = ? AND item_id = ? AND store_type IN ("0", "1", "3") AND status != "N"`, [loopDate, item_id]);
        const [d] = await conn.execute(`SELECT SUM(quantity) as sum FROM ${tableName} WHERE DATE(created) = ? AND item_id = ? AND store_type IN ("2", "4") AND status != "N"`, [loopDate, item_id]);
        let rec = r[0].sum || 0;
        let dis = d[0].sum || 0;
        console.log(`Date: ${loopDate}, Opening: ${curOpening.toFixed(2)}, Rec: ${rec}, Dis: ${dis}, Closing: ${(curOpening + rec - dis).toFixed(2)}`);
        curOpening = curOpening + rec - dis;
    }
    
    // New logic day by day
    console.log("---- NEW LOGIC ----");
    const [newTxs] = await conn.execute(`
      SELECT DATE(created) as tx_date,
        ROUND(COALESCE(SUM(CASE WHEN store_type IN ('0','1','3') AND status != 'N' THEN quantity ELSE 0 END), 0), 3) as received_qty,
        ROUND(COALESCE(SUM(CASE WHEN store_type IN ('2','4') AND status != 'N' THEN quantity ELSE 0 END), 0), 3) as dispatched_qty
      FROM ${tableName}
      WHERE DATE(created) BETWEEN '2026-08-11' AND '2026-08-18' AND item_id = ?
      GROUP BY DATE(created) ORDER BY DATE(created) ASC
    `, [item_id]);
    console.log(newTxs);
  }
  
  await conn.end();
}
run().catch(console.error);
