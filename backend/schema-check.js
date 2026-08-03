const mysql = require('mysql2/promise');
require('dotenv').config();

async function run() {
  const conn = await mysql.createConnection({
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
  });

  try {
    await conn.changeUser({ database: 'tirupati_tppl' });
    const [tables] = await conn.execute(`SHOW TABLES LIKE '%designsheet%'`);
    console.log(tables);
  } catch (e) {
    console.log(e);
  }
  conn.end();
}
run();
