// pool handled via req.dbPool
const fs = require('fs');
const path = require('path');

const getUploadDir = () => path.join(__dirname, '../../../../frontend/public/designsheet');

exports.index = async (req, res, next) => {
  try {
    const { contract_id, datestart, dateto, page = 1, limit = 20 } = req.query;
    
    let query = `
      SELECT d.*, c.title as contract_title, c.workorder, a.item_name 
      FROM designsheet d
      LEFT JOIN contracts c ON d.contract_id = c.id
      LEFT JOIN st_additem a ON d.item_id = a.id
      WHERE 1=1
    `;
    const params = [];
    
    if (contract_id) {
      query += ' AND d.contract_id LIKE ?';
      params.push(`%${contract_id}%`);
    }
    
    if (datestart && datestart !== '1970-01-01') {
      query += ' AND DATE(d.datefrom) >= ?';
      params.push(datestart);
    }
    
    if (dateto && dateto !== '1970-01-01') {
      query += ' AND DATE(d.datefrom) <= ?';
      params.push(dateto);
    }
    
    query += ' ORDER BY d.id DESC LIMIT ? OFFSET ?';
    const offset = (page - 1) * limit;
    params.push(parseInt(limit), parseInt(offset));
    
    const [designs] = await req.dbPool.query(query, params);
    
    // Count for pagination
    let countQuery = 'SELECT COUNT(*) as total FROM designsheet d WHERE 1=1';
    const countParams = [];
    if (contract_id) { countQuery += ' AND d.contract_id LIKE ?'; countParams.push(`%${contract_id}%`); }
    if (datestart && datestart !== '1970-01-01') { countQuery += ' AND DATE(d.datefrom) >= ?'; countParams.push(datestart); }
    if (dateto && dateto !== '1970-01-01') { countQuery += ' AND DATE(d.datefrom) <= ?'; countParams.push(dateto); }
    
    const [countResult] = await req.dbPool.query(countQuery, countParams);
    const total = countResult[0].total;
    
    res.json({ data: designs, total, page, limit });
  } catch (error) {
    next(error);
  }
};

exports.getById = async (req, res, next) => {
  try {
    const { id } = req.params;
    const [desheet] = await req.dbPool.query(`
      SELECT d.*, c.title as contract_title, c.workorder, a.item_name as product_name
      FROM designsheet d
      LEFT JOIN contracts c ON d.contract_id = c.id
      LEFT JOIN st_additem a ON d.item_id = a.id
      WHERE d.id = ?
    `, [id]);
    if (desheet.length === 0) return res.status(404).json({ message: 'Design sheet not found' });
    
    const [product] = await req.dbPool.query(`
      SELECT pd.*, a.item_name 
      FROM designsheetdetails pd
      LEFT JOIN st_additem a ON pd.item_id = a.id
      WHERE pd.designsheet_id = ?
    `, [id]);
    
    // Fetch finished products for the contract
    const contractid = desheet[0].contract_id;
    const [bomfinishedproduct] = await req.dbPool.query('SELECT * FROM bom_finisedproduct WHERE contract_id = ?', [contractid]);
    const finishitem = [];
    for (const value of bomfinishedproduct) {
      const [item] = await req.dbPool.query('SELECT * FROM st_additem WHERE id = ?', [value.product_id]);
      if (item.length > 0) finishitem.push(item[0]);
    }
    
    const rawitem = product.map(p => p.item_id);
    
    res.json({ desheet: desheet[0], product, finishitem, rawitem });
  } catch (error) {
    next(error);
  }
};

exports.create = async (req, res, next) => {
  try {
    const { contract_id, item_id, quantity, datefrom, pitemname, km_item_qty, pitemquantity, unit_name, is_group } = req.body;
    let { designsheetno } = req.body;
    
    if (!contract_id) return res.status(400).json({ message: 'Contract does not exist.' });
    if (!item_id) return res.status(400).json({ message: 'Product does not exist.' });
    
    // Generate designsheetno if not provided correctly
    if (!designsheetno) {
       const [lastDesign] = await req.dbPool.query('SELECT designsheetno FROM designsheet ORDER BY designsheetno DESC LIMIT 1');
       if (lastDesign.length > 0 && lastDesign[0].designsheetno) {
           designsheetno = String(parseInt(lastDesign[0].designsheetno) + 1);
       } else {
           designsheetno = "1001";
       }
    }
    
    let design_sheet = null;
    if (req.files && req.files['design_sheet']) {
      design_sheet = req.files['design_sheet'][0].filename;
    }
    
    const dFrom = new Date(datefrom).toISOString().slice(0, 19).replace('T', ' ');
    
    const [insertResult] = await req.dbPool.query(
      'INSERT INTO designsheet (contract_id, designsheetno, item_id, quantity, datefrom, design_sheet, created) VALUES (?, ?, ?, ?, ?, ?, NOW())',
      [contract_id, designsheetno, item_id, quantity, dFrom, design_sheet]
    );
    
    const lstid = insertResult.insertId;
    
    // Process details
    let pitemnameArr = [];
    if (pitemname) pitemnameArr = Array.isArray(pitemname) ? pitemname : [pitemname];
    let km_item_qtyArr = km_item_qty ? (Array.isArray(km_item_qty) ? km_item_qty : [km_item_qty]) : [];
    let pitemquantityArr = pitemquantity ? (Array.isArray(pitemquantity) ? pitemquantity : [pitemquantity]) : [];
    let unit_nameArr = unit_name ? (Array.isArray(unit_name) ? unit_name : [unit_name]) : [];
    let is_groupArr = is_group ? (Array.isArray(is_group) ? is_group : [is_group]) : [];
    
    for (let i = 0; i < pitemnameArr.length; i++) {
       const pItemId = pitemnameArr[i];
       const kmQty = km_item_qtyArr[i] || 0;
       const itemQty = pitemquantityArr[i] || 0;
       const unit = unit_nameArr[i] || '';
       const isGrp = is_groupArr[i] || '0';
       
       await req.dbPool.query(
         'INSERT INTO designsheetdetails (designsheet_id, designsheetno, contract_id, item_id, km_item_qty, item_qty, is_group, uom) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
         [lstid, designsheetno, contract_id, pItemId, kmQty, itemQty, isGrp, unit]
       );
    }
    
    res.status(201).json({ message: 'Design Sheet has been saved successfully.' });
  } catch (error) {
    next(error);
  }
};

exports.update = async (req, res, next) => {
  try {
    const { id } = req.params;
    const { contract_id, designsheetno, item_id, quantity, datefrom, pitemname, km_item_qty, pitemquantity, unit_name, is_group, pitemname11, is_group11, pitemquantity11, pitemquantity12, unit_name111 } = req.body;
    
    if (!contract_id) return res.status(400).json({ message: 'Contract does not exist.' });
    if (!item_id) return res.status(400).json({ message: 'Product does not exist.' });
    
    const [desheetArr] = await req.dbPool.query('SELECT * FROM designsheet WHERE id = ?', [id]);
    if (desheetArr.length === 0) return res.status(404).json({ message: 'Design sheet not found' });
    const desheet = desheetArr[0];
    
    const dFrom = new Date(datefrom).toISOString().slice(0, 19).replace('T', ' ');
    
    const updateParams = [contract_id, designsheetno, item_id, quantity, dFrom];
    let updateQuery = 'UPDATE designsheet SET contract_id=?, designsheetno=?, item_id=?, quantity=?, datefrom=?, updated=NOW()';
    
    if (req.files && req.files['design_sheet']) {
       if (desheet.design_sheet) {
           const oldFile = path.join(getUploadDir(), desheet.design_sheet);
           if (fs.existsSync(oldFile)) fs.unlinkSync(oldFile);
       }
       updateQuery += ', design_sheet=?';
       updateParams.push(req.files['design_sheet'][0].filename);
    }
    
    // Revisions
    for (let i = 1; i <= 5; i++) {
        if (req.files && req.files[`r${i}`]) {
            if (desheet[`r${i}`]) {
                const oldFile = path.join(getUploadDir(), desheet[`r${i}`]);
                if (fs.existsSync(oldFile)) fs.unlinkSync(oldFile);
            }
            updateQuery += `, r${i}=?`;
            updateParams.push(req.files[`r${i}`][0].filename);
        }
    }
    
    updateQuery += ' WHERE id=?';
    updateParams.push(id);
    
    await req.dbPool.query(updateQuery, updateParams);
    
    // Update existing items
    if (pitemname11) {
        let pitemname11Arr = Array.isArray(pitemname11) ? pitemname11 : [pitemname11];
        let is_group11Arr = is_group11 ? (Array.isArray(is_group11) ? is_group11 : [is_group11]) : [];
        for (let i = 0; i < pitemname11Arr.length; i++) {
            const val = pitemname11Arr[i];
            const isGrp = is_group11Arr[i] || '0';
            await req.dbPool.query(
                'UPDATE designsheetdetails SET is_group=? WHERE designsheetno=? AND item_id=?',
                [isGrp, desheet.designsheetno, val]
            );
        }
    }
    
    // Add new items
    if (pitemname) {
        let pitemnameArr = Array.isArray(pitemname) ? pitemname : [pitemname];
        let km_item_qtyArr = km_item_qty ? (Array.isArray(km_item_qty) ? km_item_qty : [km_item_qty]) : [];
        let pitemquantityArr = pitemquantity ? (Array.isArray(pitemquantity) ? pitemquantity : [pitemquantity]) : [];
        let unit_nameArr = unit_name ? (Array.isArray(unit_name) ? unit_name : [unit_name]) : [];
        let is_groupArr = is_group ? (Array.isArray(is_group) ? is_group : [is_group]) : [];
        
        for (let i = 0; i < pitemnameArr.length; i++) {
           const pItemId = pitemnameArr[i];
           const kmQty = km_item_qtyArr[i] || 0;
           const itemQty = pitemquantityArr[i] || 0;
           const unit = unit_nameArr[i] || '';
           const isGrp = is_groupArr[i] || '0';
           
           await req.dbPool.query(
             'INSERT INTO designsheetdetails (designsheet_id, designsheetno, contract_id, item_id, km_item_qty, item_qty, is_group, uom) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
             [id, designsheetno, contract_id, pItemId, kmQty, itemQty, isGrp, unit]
           );
        }
    }
    
    res.json({ message: 'Design Sheet has been updated successfully.' });
  } catch (error) {
    next(error);
  }
};

exports.deleteSheet = async (req, res, next) => {
  try {
    const { id } = req.params;
    const [desheetArr] = await req.dbPool.query('SELECT * FROM designsheet WHERE id = ?', [id]);
    if (desheetArr.length === 0) return res.status(404).json({ message: 'Not found' });
    
    const desheet = desheetArr[0];
    const files = [desheet.design_sheet, desheet.r1, desheet.r2, desheet.r3, desheet.r4, desheet.r5];
    
    for (const f of files) {
        if (f) {
            const p = path.join(getUploadDir(), f);
            if (fs.existsSync(p)) fs.unlinkSync(p);
        }
    }
    
    await req.dbPool.query('DELETE FROM designsheetdetails WHERE designsheet_id = ?', [id]);
    await req.dbPool.query('DELETE FROM designsheet WHERE id = ?', [id]);
    
    res.json({ message: 'Production Sheet deleted successfully' });
  } catch (error) {
    next(error);
  }
};

exports.deleteDetailData = async (req, res, next) => {
  try {
    const { id } = req.params; // this acts as the "fetch" param in old code
    await req.dbPool.query('DELETE FROM designsheetdetails WHERE id = ?', [id]);
    res.json({ result: true });
  } catch (error) {
    next(error);
  }
};

exports.getBomFinishedProduct = async (req, res, next) => {
  try {
    const { contractid } = req.query;
    const [bom] = await req.dbPool.query('SELECT * FROM bom_finisedproduct WHERE contract_id = ?', [contractid]);
    
    const items = [];
    for (const value of bom) {
      const [item] = await req.dbPool.query('SELECT * FROM st_additem WHERE id = ?', [value.product_id]);
      if (item.length > 0) items.push(item[0]);
    }
    
    res.json({ item: items });
  } catch (error) {
    next(error);
  }
};

exports.checkDesignSheetItem = async (req, res, next) => {
  try {
    const { itemid, contractid } = req.query;
    const [checkdesign] = await req.dbPool.query('SELECT * FROM designsheet WHERE item_id = ? AND contract_id = ? LIMIT 1', [itemid, contractid]);
    const [itemqty] = await req.dbPool.query('SELECT * FROM bom_finisedproduct WHERE product_id = ? AND contract_id = ? LIMIT 1', [itemid, contractid]);
    
    res.json({
        checkdesign: checkdesign.length > 0 ? checkdesign[0] : null,
        itemqty: itemqty.length > 0 ? itemqty[0].quantity : null
    });
  } catch (error) {
    next(error);
  }
};

exports.indentItems = async (req, res, next) => {
  try {
    const { fetch } = req.query;
    const [itemnameArr] = await req.dbPool.query('SELECT a.*, m.unit_name FROM st_additem a LEFT JOIN st_measurementunits m ON a.unit_id = m.id WHERE a.status = "Y" AND a.id = ? ORDER BY a.id ASC LIMIT 1', [fetch]);
    const itemname = itemnameArr.length > 0 ? itemnameArr[0] : null;
    
    const [tax] = await req.dbPool.query('SELECT id, tax FROM st_taxmaster WHERE status = "Y" AND parent = "0" ORDER BY id ASC');
    
    res.json({ itemname, tax });
  } catch (error) {
    next(error);
  }
};

exports.searchItems = async (req, res, next) => {
  try {
    const { query } = req.query;
    if (!query) return res.json({ items: [] });
    const [items] = await req.dbPool.query('SELECT id, item_name FROM st_additem WHERE status = "Y" AND item_name LIKE ? ORDER BY item_name ASC LIMIT 20', [`%${query}%`]);
    res.json({ items });
  } catch (err) {
    next(err);
  }
};

exports.viewDesignSheet = async (req, res, next) => {
  try {
    const { designsheetno } = req.params;
    const [designsheetArr] = await req.dbPool.query('SELECT * FROM designsheet WHERE designsheetno = ? LIMIT 1', [designsheetno]);
    const designsheet = designsheetArr.length > 0 ? designsheetArr[0] : null;
    
    const [designsheetdetails] = await req.dbPool.query(`
        SELECT d.*, a.item_name, c.title, c.workorder 
        FROM designsheetdetails d 
        LEFT JOIN st_additem a ON d.item_id = a.id
        LEFT JOIN contracts c ON d.contract_id = c.id
        WHERE d.designsheetno = ?`, 
        [designsheetno]
    );
    
    const [sitesetting] = await req.dbPool.query('SELECT * FROM sitesettings LIMIT 1');
    const [site_details] = await req.dbPool.query('SELECT * FROM sitesettings_details WHERE status = "Y" LIMIT 1');
    
    // also resolve item_name and contract_no for the main design sheet
    if (designsheet) {
        const [a] = await req.dbPool.query('SELECT item_name FROM st_additem WHERE id = ?', [designsheet.item_id]);
        if (a.length) designsheet.item_name = a[0].item_name;
        const [c] = await req.dbPool.query('SELECT title, workorder FROM contracts WHERE id = ?', [designsheet.contract_id]);
        if (c.length) designsheet.contract_no = `${c[0].title}(${c[0].workorder})`;
    }
    
    res.json({ designsheet, designsheetdetails, sitesetting: sitesetting[0], site_details: site_details[0] });
  } catch (error) {
    next(error);
  }
};

exports.getItemCatg = async (req, res, next) => {
  try {
    const { fetch } = req.query;
    const [unitid] = await pool.query('SELECT * FROM st_additem WHERE category_id = ? LIMIT 1', [fetch]);
    res.json({ id: unitid.length > 0 ? unitid[0].category_id : null });
  } catch (error) {
    next(error);
  }
};

exports.searchContracts = async (req, res, next) => {
  try {
    const { query } = req.query;
    if (!query) return res.json({ contracts: [] });
    
    const [contracts] = await req.dbPool.query(`
      SELECT id, title, workorder 
      FROM contracts 
      WHERE status = 'Y' 
      AND (workorder LIKE ? OR title LIKE ?)
      ORDER BY id DESC LIMIT 20
    `, [`%${query}%`, `%${query}%`]);
    
    res.json({ contracts });
  } catch (error) {
    next(error);
  }
};

exports.getBomFinishedProducts = async (req, res, next) => {
  try {
    const { contractId } = req.params;
    
    const [products] = await req.dbPool.query(`
      SELECT a.id, a.item_name 
      FROM bom_finisedproduct b
      JOIN st_additem a ON b.product_id = a.id
      WHERE b.contract_id = ?
      ORDER BY a.item_name ASC
    `, [contractId]);
    
    res.json({ products });
  } catch (error) {
    next(error);
  }
};

exports.checkDesignSheetItem = async (req, res, next) => {
  try {
    const { itemid, contractid } = req.query;
    if (!itemid || !contractid) return res.status(400).json({ message: 'Missing parameters' });
    
    const [checkdesign] = await req.dbPool.query(
      'SELECT id FROM designsheet WHERE item_id = ? AND contract_id = ? LIMIT 1', 
      [itemid, contractid]
    );
    
    const [bomItems] = await req.dbPool.query(
      'SELECT quantity FROM bom_finisedproduct WHERE product_id = ? AND contract_id = ? LIMIT 1',
      [itemid, contractid]
    );
    
    res.json({
      checkdesign: checkdesign.length > 0,
      itemqty: bomItems.length > 0 ? bomItems[0].quantity : 0
    });
  } catch (error) {
    next(error);
  }
};

exports.getContractDetails = async (req, res, next) => {
  try {
    const { contractId } = req.params;
    
    // 1. Contract Info
    const [contractArr] = await req.dbPool.query(`
      SELECT c.*, s.supplier_name 
      FROM contracts c 
      LEFT JOIN st_supplier s ON c.supplier_id = s.id 
      WHERE c.id = ? LIMIT 1
    `, [contractId]);
    
    if (contractArr.length === 0) {
      return res.status(404).json({ message: 'Contract not found' });
    }
    const contract = contractArr[0];

    contract.labour_cost = 0;
    contract.operational_cost = 0;
    
    // Get cost from production table based on contract
    const [prodCost] = await req.dbPool.query(`
        SELECT SUM(manpower_day + manpower_night) as labour, SUM(nextday8am - reading8am) as operational 
        FROM production 
        WHERE contract_id = ?
    `, [contractId]);
    contract.labour_cost = prodCost[0]?.labour || 0;
    contract.operational_cost = prodCost[0]?.operational || 0;

    // 2. Finished Products
    const [finishedProducts] = await req.dbPool.query(`
      SELECT b.*, a.item_name
      FROM bom_finisedproduct b 
      LEFT JOIN st_additem a ON b.product_id = a.id 
      WHERE b.contract_id = ?
    `, [contractId]);

    // 3. For each finished product, fetch the design sheet raw materials
    for (let fp of finishedProducts) {
        const [designSheet] = await req.dbPool.query(`
            SELECT designsheetno 
            FROM designsheet 
            WHERE contract_id = ? AND item_id = ? LIMIT 1
        `, [contractId, fp.product_id]);
        
        fp.rawMaterials = [];
        if (designSheet.length > 0) {
            const dsNo = designSheet[0].designsheetno;
            const [rawMaterials] = await req.dbPool.query(`
                SELECT d.item_id, d.km_item_qty, d.item_qty, d.is_group, d.uom,
                       a.item_name, a.category_id,
                       c.category_name
                FROM designsheetdetails d
                LEFT JOIN st_additem a ON d.item_id = a.id
                LEFT JOIN st_categorymaster c ON a.category_id = c.id
                WHERE d.designsheetno = ?
            `, [dsNo]);
            
            for (let rm of rawMaterials) {
                if (rm.is_group === '1' || rm.is_group > 0) {
                    rm.display_name = rm.category_name || rm.item_name;
                } else {
                    rm.display_name = rm.item_name;
                }
                
                rm.qty_as_per_design = parseFloat(rm.item_qty) || 0;
                
                const [issueRes] = await req.dbPool.query(`
                    SELECT SUM(quantity) as issued_qty 
                    FROM st_stock_register 
                    WHERE contract_id = ? AND finishedproduct_id = ? AND item_id = ? AND store_type = 2
                `, [contractId, fp.product_id, rm.item_id]);
                
                const [reverseRes] = await req.dbPool.query(`
                    SELECT SUM(quantity) as reverse_qty 
                    FROM st_stock_register 
                    WHERE contract_id = ? AND finishedproduct_id = ? AND item_id = ? AND store_type = 3
                `, [contractId, fp.product_id, rm.item_id]);
                
                const issued = (parseFloat(issueRes[0]?.issued_qty) || 0) - (parseFloat(reverseRes[0]?.reverse_qty) || 0);
                rm.issued_qty = issued;
                rm.pending_qty = rm.qty_as_per_design - issued;
                
                if (rm.is_group === '1' || rm.is_group > 0) {
                    const [catItems] = await req.dbPool.query(`
                        SELECT id, item_name FROM st_additem WHERE category_id = ?
                    `, [rm.category_id]);
                    rm.subItems = [];
                    for (let ci of catItems) {
                        const [ciIssueRes] = await req.dbPool.query(`
                            SELECT SUM(quantity) as issued_qty 
                            FROM st_stock_register 
                            WHERE contract_id = ? AND finishedproduct_id = ? AND item_id = ? AND store_type = 2
                        `, [contractId, fp.product_id, ci.id]);
                        const [ciReverseRes] = await req.dbPool.query(`
                            SELECT SUM(quantity) as reverse_qty 
                            FROM st_stock_register 
                            WHERE contract_id = ? AND finishedproduct_id = ? AND item_id = ? AND store_type = 3
                        `, [contractId, fp.product_id, ci.id]);
                        const ciIssued = (parseFloat(ciIssueRes[0]?.issued_qty) || 0) - (parseFloat(ciReverseRes[0]?.reverse_qty) || 0);
                        if (ciIssued > 0) {
                            rm.subItems.push({
                                item_name: ci.item_name,
                                issued_qty: ciIssued
                            });
                        }
                    }
                }
            }
            fp.rawMaterials = rawMaterials;
        }

        const [poData] = await req.dbPool.query(`
            SELECT SUM(plannedqty) as planned_qty 
            FROM productionorder 
            WHERE contract_id = ? AND item_id = ?
        `, [contractId, fp.product_id]);
        fp.planned_qty = poData[0]?.planned_qty || 0;

        const [preparedData] = await req.dbPool.query(`
            SELECT SUM(production_shift_a + production_shift_b) as prepared_qty 
            FROM production 
            WHERE contract_id = ? AND item_id = ? AND productprocess_id = 8
        `, [contractId, fp.product_id]);
        fp.prepared_qty = preparedData[0]?.prepared_qty || 0;
    }

    // 4. Production Orders
    const [productionOrders] = await req.dbPool.query(`
        SELECT p.*, a.item_name
        FROM productionorder p
        LEFT JOIN st_additem a ON p.item_id = a.id
        WHERE p.contract_id = ?
        ORDER BY p.id DESC
    `, [contractId]);

    for (let po of productionOrders) {
        const [prepData] = await req.dbPool.query(`
            SELECT SUM(production_shift_a + production_shift_b) as prepared_qty 
            FROM production 
            WHERE po_id = ? AND productprocess_id = 8
        `, [po.po_id]);
        po.prepared_qty = prepData[0]?.prepared_qty || 0;
    }

    // 5. Inspection Report
    const [inspectionReports] = await req.dbPool.query(`
        SELECT * 
        FROM st_inspection_report 
        WHERE work_order_no = ? AND status = 'Y'
        ORDER BY id DESC
    `, [contractId]);

    res.json({
        contract,
        finishedProducts,
        productionOrders,
        inspectionReports
    });
  } catch (error) {
    next(error);
  }
};
