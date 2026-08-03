const mysql = require('mysql2/promise');

async function main() {
  const connection = await mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'operify'
  });

  const [rows1] = await connection.query('SHOW TABLES');
  console.log("=== tables ===");
  console.table(rows1);
  
  await connection.end();
}

main().catch(console.error);
