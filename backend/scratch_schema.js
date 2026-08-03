const mysql = require('mysql2/promise');

async function main() {
  const connection = await mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'operify'
  });

  const [rows1] = await connection.query('DESCRIBE designsheet');
  console.log("=== designsheet schema ===");
  console.table(rows1);

  const [rows2] = await connection.query('DESCRIBE designsheetdetails');
  console.log("=== designsheetdetails schema ===");
  console.table(rows2);
  
  // also contracts and additem to see joins
  const [rows3] = await connection.query('DESCRIBE contracts');
  console.log("=== contracts schema ===");
  console.table(rows3);
  
  await connection.end();
}

main().catch(console.error);
