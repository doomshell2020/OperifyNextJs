const repository = require('./monthlyStock.repository');

function normalizeFilters(query) {
  const invalid = message => { const error = new Error(message); error.status = 400; throw error; };
  const integer = (value, label, min, max, fallback) => {
    if ((value === undefined || value === '') && fallback !== undefined) return fallback;
    if (!/^\d+$/.test(String(value)) || !Number.isSafeInteger(Number(value)) || Number(value) < min || Number(value) > max) invalid(`Valid ${label} (${min}-${max}) is required.`);
    return Number(value);
  };
  const year = integer(query.year, 'year', 1000, 9998);
  const month = integer(query.month, 'month', 1, 12, null);
  const product_id = integer(query.product_id, 'product ID', 1, Number.MAX_SAFE_INTEGER, null);
  const raw = query.category_ids ?? query.category_id;
  const category_ids = raw == null || raw === '' ? [] : (Array.isArray(raw) ? raw : String(raw).split(','));
  if (category_ids.some(id => !/^[1-9]\d*$/.test(String(id)) || !Number.isSafeInteger(Number(id)))) invalid('Valid category IDs are required.');
  return { year, month, product_id, category_ids: [...new Set(category_ids.map(String))],
    page: integer(query.page, 'page', 1, 1000000, 1), page_size: integer(query.page_size, 'page size', 1, 100, 25) };
}
module.exports = {
  normalizeFilters,
  getOptions: repository.getOptions,
  getMonthlyStock: async (db, query) => repository.getMonthlyStock(db, normalizeFilters(query)),
  async exportExcel(db, query, res) {
    const filters = normalizeFilters(query);
    const rows = await repository.getMonthlyStock(db, filters, true);
    return require('./stockRegister.export').monthly(db, filters, rows, res);
  }
};
