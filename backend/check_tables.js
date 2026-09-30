const mysql = require('mysql2/promise');
require('dotenv').config();
async function run() {
  const conn = await mysql.createConnection({
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_NAME || 'operify'
  });
  
  const [tables] = await conn.execute('SHOW TABLES;');
  console.log('Total Tables:', tables.length);
  const st_tables = tables.map(t => Object.values(t)[0]).filter(t => t.includes('stock'));
  console.log('st_tables with stock:', st_tables);
  await conn.end();
}
run().catch(console.error);
