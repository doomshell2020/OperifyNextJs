const mysql = require('mysql2/promise');
async function run() {
  const conn = await mysql.createConnection({ host: 'localhost', user: 'root', password: '' });
  const [dbs] = await conn.execute('SHOW DATABASES');
  for (let db of dbs) {
    if(!['information_schema', 'mysql', 'performance_schema', 'sys'].includes(db.Database)) {
       const [rows] = await conn.execute(`SELECT COUNT(*) as c FROM ${db.Database}.st_stock_register`).catch(e => [[{c: -1}]]);
       console.log(db.Database, 'st_stock_register rows:', rows[0].c);
    }
  }
  conn.end();
}
run();
