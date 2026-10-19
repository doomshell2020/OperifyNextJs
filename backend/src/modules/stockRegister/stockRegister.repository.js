const { QueryTypes } = require('sequelize');
const rules = require('./stockRules');

class StockRegisterRepository {
  async getRegisterOptions(dbPool) {
    // index() lists every category; getitemname() lists all active products,
    // even those subsequently excluded by the report's RawMaterial rules.
    const [categories, products] = await Promise.all([
      dbPool.query('SELECT id, category_name FROM st_categorymaster ORDER BY id', { type: QueryTypes.SELECT }),
      dbPool.query(`SELECT p.id, p.item_name, p.size_id, s.size_name
        FROM st_additem p LEFT JOIN st_sizemanager s ON s.id=p.size_id
        WHERE p.status='Y' ORDER BY p.id`, { type: QueryTypes.SELECT })
    ]);
    return { categories, products: products.map(product => ({
      id: product.id,
      item_name: product.item_name + (product.size_name && Number(product.size_id) !== 6 ? ` (${product.size_name})` : '')
    })) };
  }
  async getCategories(dbPool) {
    const sql = `
      SELECT id, category_name
      FROM st_categorymaster
      WHERE id != 25
      ORDER BY category_name ASC
    `;
    return await dbPool.query(sql, { type: QueryTypes.SELECT });
  }

  async getDailyStockAsOfDate(dbPool, filters = {}) {
    const { date, category_ids } = filters;

    let pQuery = `
      SELECT p.id as item_id, p.item_name, c.category_name, u.unit_name as unit
      FROM st_additem p
      LEFT JOIN st_categorymaster c ON p.category_id = c.id
      LEFT JOIN st_measurementunits u ON p.uom = u.id
      WHERE ${rules.eligibleProduct('p.')}
    `;
    const pReplacements = {};

    if (category_ids && Array.isArray(category_ids) && category_ids.length > 0 && !category_ids.includes('All') && !category_ids.includes('1')) {
      pQuery += ` AND p.category_id IN (:category_ids)`;
      pReplacements.category_ids = category_ids;
    }

    pQuery += ` ORDER BY p.item_name ASC`;

    const products = await dbPool.query(pQuery, { replacements: pReplacements, type: QueryTypes.SELECT });

    if (products.length === 0) return [];
    const productIds = products.map(p => p.item_id);

    // Current PHP daily stock is cumulative by creation date, including all
    // active stock types through the selected date (not daily movements).
    const movements = await dbPool.query(`SELECT item_id,store_type,SUM(quantity) AS quantity
      FROM st_stock_register WHERE status!='N' AND DATE(created)<=:date AND item_id IN (:productIds)
      GROUP BY item_id,store_type`, {replacements:{productIds,date},type:QueryTypes.SELECT});
    const totals = new Map();
    for (const movement of movements) {
      const key = String(movement.item_id);
      if (!totals.has(key)) totals.set(key,{});
      totals.get(key)[String(movement.store_type)] = Number(movement.quantity) || 0;
    }
    return products.map(product => {
      const t = totals.get(String(product.item_id)) || {};
      const received=(t['0']||0)+(t['1']||0)+(t['3']||0),issued=(t['2']||0)+(t['4']||0);
      return {...product,opening_stock:(received-issued).toFixed(2),received_stock:received.toFixed(2),issued_stock:issued.toFixed(2),reverse_stock:(t['3']||0).toFixed(2),return_stock:(t['4']||0).toFixed(2),closing_stock:(received-issued).toFixed(2)};
    });
  }

  async getStockRegister(dbPool, filters = {}) {
    const { product_id, category_id, category_ids, date_from, date_to } = filters;

    // 1. Get products matching filters
    let pQuery = `
      SELECT p.id as item_id, p.item_name, p.item_isbn, c.category_name, u.unit_name as unit
      FROM st_additem p
      LEFT JOIN st_categorymaster c ON p.category_id = c.id
      LEFT JOIN st_measurementunits u ON p.uom = u.id
      WHERE ${rules.eligibleProduct('p.')}
    `;
    const pReplacements = {};
    if (product_id) {
      pQuery += ` AND p.id = :product_id`;
      pReplacements.product_id = product_id;
    }
    if (category_ids?.length) {
      pQuery += ` AND p.category_id IN (:category_ids)`;
      pReplacements.category_ids = category_ids;
    } else if (category_id) {
      pQuery += ` AND p.category_id = :category_id`;
      pReplacements.category_id = category_id;
    }
    pQuery += ` ORDER BY p.item_name ASC`;

    const products = await dbPool.query(pQuery, { replacements: pReplacements, type: QueryTypes.SELECT });

    if (products.length === 0) return [];

    const productIds = products.map(p => p.item_id);

    // Collapse history to one opening bucket per type and only retain daily
    // movements in the requested range. This bounds transfer/memory by products
    // and report days rather than the tenant's entire transaction history.
    let sql = `
      SELECT item_id, store_type,
             CASE WHEN effective_date IS NULL OR effective_date < :date_from THEN 'opening' ELSE effective_date END AS movement_date,
             SUM(quantity) as total_qty
      FROM (
        SELECT item_id, store_type, quantity,
          CASE WHEN store_type IN ('0','1','3') THEN ${rules.receiptDate('')}
               ELSE ${rules.dispatchDate('')} END AS effective_date
        FROM st_stock_register
        WHERE status != 'N' AND item_id IN (:productIds)
          AND store_type IN ('0','1','2','3','4')
      ) movements
      WHERE effective_date IS NULL OR effective_date <= :date_to
      GROUP BY item_id, store_type, movement_date
    `;

    const stockData = await dbPool.query(sql, {
      replacements: { productIds, date_from, date_to },
      type: QueryTypes.SELECT
    });

    // Build efficient lookup
    const stockLookup = {};
    for (const row of stockData) {
      if (!stockLookup[row.item_id]) {
        stockLookup[row.item_id] = [];
      }
      stockLookup[row.item_id].push(row);
    }

    // 3. Build date-wise results
    const results = [];

    const parseLocalDate = (dStr) => {
      const [y, m, d] = dStr.split('-').map(Number);
      return new Date(y, m - 1, d);
    };

    const startDate = parseLocalDate(date_from);
    const endDate = parseLocalDate(date_to);

    const formatDate = (dateObj) => {
      const year = dateObj.getFullYear();
      const month = String(dateObj.getMonth() + 1).padStart(2, '0');
      const day = String(dateObj.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
    };

    let loopDate = new Date(startDate);

    while (loopDate <= endDate) {
      const currDateStr = formatDate(loopDate);

      for (const product of products) {
        let openingStock = 0;
        let receivedStock = 0;
        let dispatchedStock = 0;

        if (stockLookup[product.item_id]) {
          for (const row of stockLookup[product.item_id]) {
            const storeType = String(row.store_type);
            const qty = parseFloat(row.total_qty) || 0;

            const movementDate = row.movement_date;
            const before = movementDate === 'opening' || movementDate < currDateStr;

            // GRN / Received
            if (rules.RECEIPT_TYPES.includes(storeType)) {
              if (before) {
                openingStock += qty;
              } else if (movementDate === currDateStr) {
                receivedStock += qty;
              }
            }

            // Indent / Dispatched
            if (rules.DISPATCH_TYPES.includes(storeType)) {
              if (before) {
                openingStock -= qty;
              } else if (movementDate === currDateStr) {
                dispatchedStock += qty;
              }
            }
          }
        }

        // PHP round() uses half away from zero, including negative balances.
        const round = value => Math.sign(value) * Math.round((Math.abs(value) + Number.EPSILON * Math.abs(value)) * 100) / 100;
        openingStock = round(openingStock);
        receivedStock = round(receivedStock);
        dispatchedStock = round(dispatchedStock);
        const closingStock = round(openingStock + receivedStock - dispatchedStock);

        if (openingStock === 0 && receivedStock === 0 && dispatchedStock === 0 && closingStock === 0) {
          continue; // The current PHP skips rows with no stock or movements.
        }

        results.push({
          item_id: product.item_id,
          product_code: product.item_isbn,
          item_name: product.item_name,
          category_name: product.category_name,
          unit: product.unit,
          date_range: currDateStr,
          opening_stock: openingStock.toFixed(2),
          received_stock: receivedStock.toFixed(2),
          dispatched_stock: dispatchedStock.toFixed(2),
          closing_stock: closingStock.toFixed(2)
        });
      }

      loopDate.setDate(loopDate.getDate() + 1);
    }

    return results;
  }

  async getReceivedStockDetails(dbPool, { product_id, date }) {
    const sql = `
      SELECT
        sr.id,
        sr.store_type,
        p.item_name,
        sr.po_id,
        po.added_time as po_date,
        po.is_revised,
        po.id as purchaseorder_id,
        v.name as vendor_name,
        v.contact_no,
        v.email,
        gr.inwarddate,
        gr.bill_no,
        gr.bill_date,
        sr.quantity,
        sr.issue_date
      FROM st_stock_register sr
      LEFT JOIN st_additem p ON sr.item_id = p.id
      LEFT JOIN st_goodsreceive gr ON sr.goods_id = gr.id
      LEFT JOIN st_purchaseorder po ON sr.purchaseorder_id = po.id
      LEFT JOIN vendors v ON gr.vendor_id = v.id
      WHERE sr.item_id = :product_id
        AND sr.store_type IN ('0', '1', '3')
        AND sr.status != 'N'
        AND DATE(COALESCE(sr.delivery_date, sr.issue_date, sr.created)) = :date
      ORDER BY sr.id DESC
    `;

    return await dbPool.query(sql, {
      replacements: { product_id, date },
      type: QueryTypes.SELECT
    });
  }

  async getReceivedStockProduct(dbPool, product_id) {
    const products = await dbPool.query('SELECT id, item_name FROM st_additem WHERE id = :product_id', {
      replacements: { product_id }, type: QueryTypes.SELECT
    });
    return products[0] || null;
  }

  async getDispatchedStockDetails(dbPool, { product_id, date }) {
    const sql = `
      SELECT
        sr.indent_id,
        sr.quantity,
        sr.issue_date,
        sr.created,
        sr.sub_contractors_id,
        sc.name as subcontractor_name,
        sc.mobile as subcontractor_mobile,
        sc.email as subcontractor_email,
        i.indent_status
      FROM st_stock_register sr
      LEFT JOIN sub_contractors sc ON sr.sub_contractors_id = sc.id
      LEFT JOIN st_indentmaster i ON i.id=(SELECT j.id FROM st_indentmaster j WHERE j.indent_id=sr.indent_id AND j.item_id=sr.item_id ORDER BY j.id DESC LIMIT 1)
      WHERE sr.item_id = :product_id
        AND sr.store_type IN ('2', '4')
        AND sr.status != 'N'
        AND DATE(COALESCE(sr.issue_date, sr.created)) = :date
      ORDER BY sr.id DESC
    `;

    return await dbPool.query(sql, {
      replacements: { product_id, date },
      type: QueryTypes.SELECT
    });
  }
}

module.exports = new StockRegisterRepository();
