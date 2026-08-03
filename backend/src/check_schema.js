const mysql = require('mysql2/promise');

async function main() {
  const conn = await mysql.createConnection({ host: 'localhost', user: 'root', password: '', database: 'tirupati_tppl' });

  try {
    const tables = ['emd_guarantees', 'emd_amount', 'emd_remarks'];
    for (const table of tables) {
      try {
        const [cols] = await conn.execute(`DESCRIBE ${table}`);
        console.log(`${table}:`, JSON.stringify(cols.map(r => r.Field)));
      } catch (e) {
        console.log(`${table}: NOT FOUND (${e.message})`);
      }
    }

    // list payment tables
    const [allTables] = await conn.execute('SHOW TABLES');
    const key = Object.keys(allTables[0])[0];
    const paymentTables = allTables.filter(r => r[key].toLowerCase().includes('payment'));
    console.log('Payment tables:', JSON.stringify(paymentTables.map(r => r[key])));

    const quotationTables = allTables.filter(r => r[key].toLowerCase().includes('quotation'));
    console.log('Quotation tables:', JSON.stringify(quotationTables.map(r => r[key])));

    const indentTables = allTables.filter(r => r[key].toLowerCase().includes('indent'));
    console.log('Indent tables:', JSON.stringify(indentTables.map(r => r[key])));

  } finally {
    await conn.end();
  }
}

main().catch(console.error);
