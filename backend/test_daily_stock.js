const repository = require('./src/modules/stockRegister/stockRegister.repository');

// Mock dbPool
const dbPool = {
  query: async (sql, options) => {
    console.log("Mock Query executed");
    return [];
  }
};

async function test() {
  try {
    const cats = await repository.getCategories(dbPool);
    console.log("Categories OK");
    const daily = await repository.getDailyStockAsOfDate(dbPool, { date: '2023-01-01' });
    console.log("Daily OK");
  } catch (err) {
    console.error("Test failed", err);
  }
}
test();
