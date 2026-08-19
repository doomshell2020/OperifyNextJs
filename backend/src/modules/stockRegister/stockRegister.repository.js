const { QueryTypes } = require('sequelize');

class StockRegisterRepository {
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
      WHERE p.itemtype = 'RawMaterial' AND p.status = 'Y' AND p.category_id != 25
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
    
    const sqlOpening = `
      SELECT item_id, 
      SUM(CASE WHEN store_type IN ('0','1','3') THEN quantity ELSE 0 END) as grn_sum,
      SUM(CASE WHEN store_type IN ('2','4') THEN quantity ELSE 0 END) as indent_sum
      FROM st_stock_register 
      WHERE item_id IN (:productIds) AND issue_date < :date
      GROUP BY item_id
    `;
    const openingData = await dbPool.query(sqlOpening, { replacements: { productIds, date }, type: QueryTypes.SELECT });
    const openingLookup = {};
    for (const row of openingData) {
      openingLookup[row.item_id] = (parseFloat(row.grn_sum) || 0) - (parseFloat(row.indent_sum) || 0);
    }
    
    const sqlReceived = `
      SELECT item_id, 
      SUM(CASE WHEN store_type IN ('0','1') THEN quantity ELSE 0 END) as received_qty,
      SUM(CASE WHEN store_type IN ('3') THEN quantity ELSE 0 END) as reverse_qty
      FROM st_stock_register
      WHERE status != 'N' AND item_id IN (:productIds) AND store_type IN ('0','1','3') 
        AND DATE(issue_date) = :date
      GROUP BY item_id
    `;
    const receivedData = await dbPool.query(sqlReceived, { replacements: { productIds, date }, type: QueryTypes.SELECT });
    const receivedLookup = {};
    for (const row of receivedData) {
      receivedLookup[row.item_id] = row;
    }
    
    const sqlIssued = `
      SELECT item_id, 
      SUM(CASE WHEN store_type IN ('2') THEN quantity ELSE 0 END) as issued_qty,
      SUM(CASE WHEN store_type IN ('4') THEN quantity ELSE 0 END) as return_qty
      FROM st_stock_register
      WHERE status != 'N' AND item_id IN (:productIds) AND store_type IN ('2','4') 
        AND DATE(created) = :date
      GROUP BY item_id
    `;
    const issuedData = await dbPool.query(sqlIssued, { replacements: { productIds, date }, type: QueryTypes.SELECT });
    const issuedLookup = {};
    for (const row of issuedData) {
      issuedLookup[row.item_id] = row;
    }
    
    const results = [];
    for (const product of products) {
      const pId = product.item_id;
      
      const opening = openingLookup[pId] || 0.0;
      const received = receivedLookup[pId] ? parseFloat(receivedLookup[pId].received_qty) : 0.0;
      const reverse = receivedLookup[pId] ? parseFloat(receivedLookup[pId].reverse_qty) : 0.0;
      const issued = issuedLookup[pId] ? parseFloat(issuedLookup[pId].issued_qty) : 0.0;
      const return_qty = issuedLookup[pId] ? parseFloat(issuedLookup[pId].return_qty) : 0.0;
      
      const closing = opening + received + reverse - issued - return_qty;
      
      if (opening === 0 && received === 0 && issued === 0 && reverse === 0 && return_qty === 0 && closing === 0) {
        if (!category_ids || category_ids.length === 0 || category_ids.includes('All')) {
          continue;
        }
      }
      
      results.push({
        item_id: pId,
        item_name: product.item_name,
        category_name: product.category_name,
        opening_stock: opening.toFixed(2),
        received_stock: received.toFixed(2),
        issued_stock: issued.toFixed(2),
        reverse_stock: reverse.toFixed(2),
        return_stock: return_qty.toFixed(2),
        closing_stock: closing.toFixed(2)
      });
    }
    
    return results;
  }

  async getStockRegister(dbPool, filters = {}) {
    const { product_id, category_id, date_from, date_to } = filters;

    // 1. Get products matching filters
    let pQuery = `
      SELECT p.id as item_id, p.item_name, p.item_isbn, c.category_name, u.unit_name as unit
      FROM st_additem p
      LEFT JOIN st_categorymaster c ON p.category_id = c.id
      LEFT JOIN st_measurementunits u ON p.uom = u.id
      WHERE p.itemtype = 'RawMaterial' AND p.status = 'Y' AND p.category_id != 25
    `;
    const pReplacements = {};
    if (product_id) {
      pQuery += ` AND p.id = :product_id`;
      pReplacements.product_id = product_id;
    }
    if (category_id) {
      pQuery += ` AND p.category_id = :category_id`;
      pReplacements.category_id = category_id;
    }
    pQuery += ` ORDER BY p.item_name ASC`;
    
    const products = await dbPool.query(pQuery, { replacements: pReplacements, type: QueryTypes.SELECT });
    
    if (products.length === 0) return [];

    const productIds = products.map(p => p.item_id);

    // 2. Fetch all stock movements for these products
    let sql = `
      SELECT item_id, store_type, 
             DATE(COALESCE(delivery_date, issue_date, created)) as grn_date, 
             DATE(created) as indent_date, 
             SUM(quantity) as total_qty 
      FROM st_stock_register 
      WHERE status != 'N' AND item_id IN (:productIds) 
      GROUP BY item_id, store_type, grn_date, indent_date
    `;
    
    const stockData = await dbPool.query(sql, { 
      replacements: { productIds }, 
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
            
            const formatDbDate = (dbVal) => {
              if (!dbVal) return null;
              const d = dbVal instanceof Date ? dbVal : new Date(dbVal);
              if (isNaN(d.getTime())) return null;
              return formatDate(d);
            };

            const grnDate = formatDbDate(row.grn_date);
            const indentDate = formatDbDate(row.indent_date);
            
            // GRN / Received
            if (storeType === '0' || storeType === '1' || storeType === '3') {
              if (grnDate && grnDate < currDateStr) {
                openingStock += qty;
              } else if (grnDate === currDateStr) {
                receivedStock += qty;
              }
            }
            
            // Indent / Dispatched
            if (storeType === '2' || storeType === '4') {
              if (indentDate && indentDate < currDateStr) {
                openingStock -= qty;
              } else if (indentDate === currDateStr) {
                dispatchedStock += qty;
              }
            }
          }
        }
        
        openingStock = Math.round(openingStock * 100) / 100;
        receivedStock = Math.round(receivedStock * 100) / 100;
        dispatchedStock = Math.round(dispatchedStock * 100) / 100;
        const closingStock = Math.round((openingStock + receivedStock - dispatchedStock) * 100) / 100;
        
        if (openingStock === 0 && receivedStock === 0 && dispatchedStock === 0 && closingStock === 0) {
          // Keep it to match old software
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
        sr.po_id, 
        po.added_time as po_date, 
        po.is_revised,
        po.id as purchaseorder_id,
        v.name as vendor_name, 
        v.contact_no, 
        v.email,
        gr.inwarddate,
        gr.bill_no,
        sr.quantity,
        sr.sub_contractors_id,
        sc.name as sc_name,
        sc.mobile as sc_mobile,
        sc.email as sc_email,
        sr.issue_date
      FROM st_stock_register sr
      LEFT JOIN st_goodsreceive gr ON sr.goods_id = gr.id
      LEFT JOIN st_purchaseorder po ON sr.purchaseorder_id = po.id
      LEFT JOIN vendors v ON gr.vendor_id = v.id
      LEFT JOIN sub_contractors sc ON sr.sub_contractors_id = sc.id
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
      LEFT JOIN st_indentmaster i ON sr.indent_id = i.indent_id AND sr.item_id = i.item_id
      WHERE sr.item_id = :product_id 
        AND sr.store_type IN ('2', '4')
        AND sr.status != 'N'
        AND DATE(sr.created) = :date
      ORDER BY sr.id DESC
    `;
    
    return await dbPool.query(sql, { 
      replacements: { product_id, date }, 
      type: QueryTypes.SELECT 
    });
  }
}

module.exports = new StockRegisterRepository();
