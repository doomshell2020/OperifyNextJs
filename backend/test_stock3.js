const StockRegisterRepository = require('./src/modules/stockRegister/stockRegister.repository.js');
const { getTenantSequelize } = require('./src/config/sequelize');
async function run() {
  const dbPool = await getTenantSequelize('tirupati_tppl');
  const results = await StockRegisterRepository.getStockRegister(dbPool, {
    product_id: 256,
    date_from: '2026-08-01',
    date_to: '2026-08-05'
  });
  console.log(results);
  process.exit(0);
}
run();
