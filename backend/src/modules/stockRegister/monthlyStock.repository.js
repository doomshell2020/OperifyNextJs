const { QueryTypes, Transaction } = require('sequelize');
const rules = require('./stockRules');

function monthlyQuery(filters) {
  const replacements = { receiptTypes: rules.RECEIPT_TYPES, dispatchTypes: rules.DISPATCH_TYPES };
  const months = filters.month ? [filters.month] : Array.from({ length: 12 }, (_, i) => i + 1);
  const periods = months.map((month, index) => {
    replacements[`start${index}`] = `${filters.year}-${String(month).padStart(2, '0')}-01`;
    replacements[`end${index}`] = month === 12 ? `${filters.year + 1}-01-01`
      : `${filters.year}-${String(month + 1).padStart(2, '0')}-01`;
    return `SELECT CAST(:start${index} AS DATE) AS month_start, CAST(:end${index} AS DATE) AS month_end`;
  }).join(' UNION ALL ');
  let productWhere = rules.eligibleProduct('p.');
  if (filters.product_id) {
    productWhere += ' AND p.id=:product_id';
    replacements.product_id = filters.product_id;
  }
  if (filters.category_ids.length) {
    productWhere += ' AND p.category_id IN (:category_ids)';
    replacements.category_ids = filters.category_ids;
  }
  const effectiveDate = `CASE WHEN sr.store_type IN (:receiptTypes) THEN ${rules.receiptDate('sr.')} ELSE ${rules.dispatchDate('sr.')} END`;
  // Use the same cumulative boundary for closing and the next opening, even
  // when legacy FLOAT quantities have more than two decimal places.
  const net = `CASE WHEN sr.store_type IN (:receiptTypes) THEN COALESCE(sr.quantity,0) ELSE -COALESCE(sr.quantity,0) END`;
  const sql = `SELECT p.id AS item_id, p.item_name, c.category_name,
    DATE_FORMAT(m.month_start,'%Y-%m') AS month,
    ROUND(SUM(CASE WHEN ${effectiveDate}<m.month_start THEN ${net} ELSE 0 END),2) AS opening_stock,
    ROUND(SUM(CASE WHEN ${effectiveDate}>=m.month_start AND ${effectiveDate}<m.month_end AND sr.store_type IN (:receiptTypes) THEN COALESCE(sr.quantity,0) ELSE 0 END),2) AS received_stock,
    ROUND(SUM(CASE WHEN ${effectiveDate}>=m.month_start AND ${effectiveDate}<m.month_end AND sr.store_type IN (:dispatchTypes) THEN COALESCE(sr.quantity,0) ELSE 0 END),2) AS dispatched_stock,
    ROUND(SUM(${net}),2) AS closing_stock
    FROM st_additem p CROSS JOIN (${periods}) m
    LEFT JOIN st_categorymaster c ON c.id=p.category_id
    LEFT JOIN st_stock_register sr ON sr.item_id=p.id AND sr.status!='N'
      AND sr.store_type IN (:receiptTypes,:dispatchTypes) AND ${effectiveDate}<m.month_end
    WHERE ${productWhere}
    GROUP BY p.id,p.item_name,c.category_name,m.month_start
    HAVING opening_stock!=0 OR received_stock!=0 OR dispatched_stock!=0 OR closing_stock!=0`;
  return { sql, replacements };
}

async function getMonthlyStock(db, filters, exportAll = false) {
  const { sql, replacements } = monthlyQuery(filters);
  return db.transaction({ isolationLevel: Transaction.ISOLATION_LEVELS.REPEATABLE_READ, readOnly: true }, async transaction => {
    const query = statement => db.query(statement, { replacements, type: QueryTypes.SELECT, transaction });
    let total;
    if (!exportAll) {
      const [count] = await query(`SELECT COUNT(*) AS total FROM (${sql}) report`);
      total = Number(count.total);
      replacements.limit = filters.page_size;
      replacements.offset = (filters.page - 1) * filters.page_size;
    }
    // Stable order prevents duplicate/missing rows between adjacent pages.
    const data = await query(`SELECT * FROM (${sql}) report ORDER BY month,item_name,item_id${exportAll ? '' : ' LIMIT :limit OFFSET :offset'}`);
    const rows = data.map(row => ({ ...row, physical_stock: null }));
    return exportAll ? rows : { data: rows, pagination: { page: filters.page, page_size: filters.page_size, total, total_pages: Math.ceil(total / filters.page_size) } };
  });
}

async function getOptions(db) {
  const products = await db.query(`SELECT p.id,p.item_name,p.category_id FROM st_additem p
    WHERE ${rules.eligibleProduct('p.')} ORDER BY p.item_name,p.id`, { type: QueryTypes.SELECT });
  const categories = await db.query('SELECT id,category_name FROM st_categorymaster WHERE id!=25 ORDER BY category_name', { type: QueryTypes.SELECT });
  return { products, categories };
}
module.exports = { getMonthlyStock, getOptions };
