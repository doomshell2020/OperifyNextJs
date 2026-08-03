const fs = require('fs');
const mysql = require('mysql2/promise');
const contractService = require('./src/modules/contract/contract.service');
const { generateContractPDF } = require('./src/modules/contract/contract.pdf');

async function main() {
  const dbPool = await mysql.createPool({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'tirupati_tppl'
  });
  try {
    const details = await contractService.getContractDetails(dbPool, 90);
    if (!details) {
      console.log('Contract not found');
      return;
    }
    const pdfBuffer = await generateContractPDF(details);
    console.log('PDF Buffer length:', pdfBuffer.length);
    console.log('Starts with:', pdfBuffer.toString('utf8', 0, 5));
    fs.writeFileSync('test-90.pdf', pdfBuffer);
    console.log('Saved to test-90.pdf');
  } catch (error) {
    console.error('Error:', error);
  } finally {
    process.exit(0);
  }
}

main();
