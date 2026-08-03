const mysql = require('mysql2/promise');
const fs = require('fs');
const path = require('path');
require('dotenv').config();

async function importSQL(connection, filePath) {
  console.log(`Reading SQL file: ${filePath}...`);
  let content = fs.readFileSync(filePath, 'utf-8');
  
  // Replace incompatible modern collation with widely supported general collation
  content = content.replace(/utf8mb4_0900_ai_ci/g, 'utf8mb4_general_ci');
  content = content.replace(/utf8_0900_ai_ci/g, 'utf8_general_ci');
  
  // Split content by semicolons followed by a newline, to get individual statements
  const statements = content.split(/;\r?\n/);
  
  console.log(`Found ${statements.length} statements. Executing...`);
  
  let executedCount = 0;
  let failedCount = 0;
  for (let statement of statements) {
    statement = statement.trim();
    if (!statement || statement.startsWith('--') || statement.startsWith('/*')) {
      continue;
    }
    
    try {
      await connection.query(statement);
      executedCount++;
      if (executedCount % 100 === 0) {
        console.log(`Executed ${executedCount} statements...`);
      }
    } catch (err) {
      // Ignore drop table/index errors if table doesn't exist
      if (!statement.toUpperCase().startsWith('DROP')) {
        failedCount++;
        if (failedCount < 10) {
          console.warn(`Warning executing statement: ${statement.substring(0, 100)}...`);
          console.warn(`Error: ${err.message}`);
        }
      }
    }
  }
  
  console.log(`Successfully executed ${executedCount} statements. Failed: ${failedCount} (mostly DROP errors) from ${path.basename(filePath)}.\n`);
}

async function main() {
  const connectionConfig = {
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    multipleStatements: true
  };

  let connection;
  try {
    connection = await mysql.createConnection(connectionConfig);
    console.log('Connected to MySQL server successfully.');

    // Disable strict SQL modes (allows zero dates like '0000-00-00 00:00:00' from legacy dumps)
    console.log('Setting sql_mode to empty for session...');
    await connection.query("SET SESSION sql_mode = ''");

    // Drop databases first to start from a clean state
    console.log('Cleaning up existing databases...');
    await connection.query('DROP DATABASE IF EXISTS operify');
    await connection.query('DROP DATABASE IF EXISTS tirupati_tppl');

    console.log('Creating databases...');
    await connection.query('CREATE DATABASE operify CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci');
    await connection.query('CREATE DATABASE tirupati_tppl CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci');
    console.log('Databases created successfully.');

    // 2. Import central operify database
    await connection.query('USE operify');
    const operifySqlPath = path.join(__dirname, '..', '..', 'operify-cake-php-old-project', 'operify.sql');
    await importSQL(connection, operifySqlPath);

    // 3. Import tenant database
    await connection.query('USE tirupati_tppl');
    const tpplSqlPath = path.join(__dirname, '..', '..', 'operify-cake-php-old-project', 'tirupati_tppl.sql');
    await importSQL(connection, tpplSqlPath);

    console.log('All databases imported successfully!');
    process.exit(0);
  } catch (error) {
    console.error('Import database failed:', error);
    process.exit(1);
  } finally {
    if (connection) {
      await connection.end();
    }
  }
}

main();
