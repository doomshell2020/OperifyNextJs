const mysql = require('mysql2/promise');

async function main() {
  const conn = await mysql.createConnection({ host: 'localhost', user: 'root', password: '', database: 'tirupati_tppl' });
  try {
    const tables = ['payments', 'particular_payments', 'st_quotations', 'st_quotations_details', 'st_indentmaster', 'indentpo'];
    for (const table of tables) {
      try {
        const [cols] = await conn.execute(`DESCRIBE ${table}`);
        console.log(`\n${table}: [${cols.map(r => `"${r.Field}"`).join(', ')}]`);
      } catch (e) {
        console.log(`${table}: NOT FOUND`);
      }
    }
    // Also check vendors table
    const [v] = await conn.execute('DESCRIBE vendors');
    console.log(`\nvendors: [${v.map(r => `"${r.Field}"`).join(', ')}]`);

    // sample payments rows
    const [rows] = await conn.execute('SELECT * FROM payments LIMIT 2');
    console.log('\npayments sample:', JSON.stringify(rows, null, 2));
  } finally {
    await conn.end();
  }
}

main().catch(console.error);
