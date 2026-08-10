const mysql = require('mysql2/promise');
const fs = require('fs');
async function test() {
  const pool = mysql.createPool({ host: 'localhost', user: 'root', password: '', database: 'tirupati_tppl' });
  try {
    const [cols] = await pool.execute('DESCRIBE vendors');
    const [cols2] = await pool.execute('DESCRIBE st_purchaseorder');
    const [cols3] = await pool.execute('DESCRIBE sitesettings');
    const [cols4] = await pool.execute('DESCRIBE sitesettings_details');
    fs.writeFileSync('tables.txt', JSON.stringify({
      vendors: cols.map(c => c.Field),
      st_purchaseorder: cols2.map(c => c.Field),
      sitesettings: cols3.map(c => c.Field),
      sitesettings_details: cols4.map(c => c.Field)
    }, null, 2));
  } catch (e) { console.error('Error:', e.message); }
  process.exit();
}
test();
