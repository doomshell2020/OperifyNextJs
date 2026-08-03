const mysql = require('mysql2/promise');
const contractRepository = require('./modules/contract/contract.repository');

async function main() {
  const connection = await mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'tirupati_tppl'
  });

  try {
    console.log('Querying directly with { contract_id: 89 }...');
    const rows = await contractRepository.findFiltered(connection, { contract_id: 89 });
    console.log('Direct Query Row Count:', rows.length);
    console.log('Direct Query Rows:', JSON.stringify(rows, null, 2));

    console.log('\nQuerying directly with no filters...');
    const allRows = await contractRepository.findFiltered(connection, {});
    console.log('All Rows Count:', allRows.length);
  } catch (err) {
    console.error('Error:', err.message);
  } finally {
    await connection.end();
  }
}

main();
