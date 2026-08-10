const mysql = require('mysql2/promise');
require('dotenv').config();

async function test() {
  const pool = mysql.createPool({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'tirupati_tppl'
  });
  
  try {
    const query = `
      SELECT id FROM st_purchaseorder
      LIMIT ? OFFSET ?
    `;
    const [rows] = await pool.execute(query, [10, 0]);
    console.log("Success with integers");
  } catch (e) {
    console.error("Error with integers:", e.message);
  }

  try {
    const query = `
      SELECT id FROM st_purchaseorder
      LIMIT ? OFFSET ?
    `;
    const [rows] = await pool.execute(query, ["10", "0"]);
    console.log("Success with strings");
  } catch (e) {
    console.error("Error with strings:", e.message);
  }
  
  process.exit();
}
test();
