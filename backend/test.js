const mysql = require('mysql2/promise');
async function main() {
  const connection = await mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'tirupati_tppl'
  });
  
  const [items] = await connection.query('SELECT id, item_name, itemtype, category_id FROM st_additem WHERE item_name LIKE "%TRACTION%"');
  console.log('Items:', items);

  await connection.end();
}
main();
