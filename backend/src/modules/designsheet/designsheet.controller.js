const { Op, QueryTypes, col, fn, literal } = require('sequelize');
const fs = require('fs');
const path = require('path');
const {paginationInput,paginationResult,listOrder} = require('../../utils/listPagination');

const {uploadDir} = require('./designsheet.files');

exports.index = async (req, res, next) => {
  try {
    const { contract_id, contract_name, datestart, dateto } = req.query;
    const {page,limit,offset} = paginationInput(req.query,'designsheet');

    let query = `
      SELECT d.*, c.title as contract_title, c.workorder, a.item_name, (SELECT COUNT(*) FROM indentpo i WHERE i.contract_id = d.contract_id AND i.finishedproduct_id = d.item_id) as indentpo_count
      FROM designsheet d
      LEFT JOIN contracts c ON d.contract_id = c.id
      LEFT JOIN st_additem a ON d.item_id = a.id
      WHERE 1=1
    `;
    const params = {};

    if (contract_id) {
      query += ' AND d.contract_id = :contract_id';
      params.contract_id = contract_id;
    }
    if (contract_name && !contract_id) {
      query += " AND (c.title LIKE :contract_name OR c.workorder LIKE :contract_name OR CONCAT(c.title, '(', c.workorder, ')') LIKE :contract_name)";
      params.contract_name = `%${contract_name.trim()}%`;
    }

    if (datestart && datestart !== '1970-01-01') {
      query += ' AND DATE(d.datefrom) >= :datestart';
      params.datestart = datestart;
    }

    if (dateto && dateto !== '1970-01-01') {
      query += ' AND DATE(d.datefrom) <= :dateto';
      params.dateto = dateto;
    }

    let countQuery = query.replace('SELECT d.*, c.title as contract_title, c.workorder, a.item_name, (SELECT COUNT(*) FROM indentpo i WHERE i.contract_id = d.contract_id AND i.finishedproduct_id = d.item_id) as indentpo_count', 'SELECT COUNT(*) as total');

    const sort = listOrder(req.query.sort,req.query.direction,{id:'d.id',designsheetno:'d.designsheetno',datefrom:'d.datefrom',quantity:'d.quantity',contract_id:'d.contract_id'},'d.id');
    query += ` ORDER BY ${sort.column} ${sort.direction}${sort.column === 'd.id' ? '' : ', d.id DESC'} LIMIT :limit OFFSET :offset`;
    params.limit = parseInt(limit);
    params.offset = parseInt(offset);

    const designs = await req.dbPool.query(query, { replacements: params, type: QueryTypes.SELECT });
    const countResult = await req.dbPool.query(countQuery, { replacements: params, type: QueryTypes.SELECT });
    const total = countResult[0].total;

    res.json({ data: designs, ...paginationResult(total,page,limit,designs.length) });
  } catch (error) {
    next(error);
  }
};

async function countIndentPo(req, contractId, itemId) {
  return await req.models.indentpo.count({
    where: {
      contract_id: contractId,
      finishedproduct_id: itemId
    }
  });
}

function normalizeArray(value) {
  if (!value) return [];
  return Array.isArray(value) ? value : [value];
}

function hasDuplicateValues(values) {
  const normalized = values.filter(Boolean).map(value => String(value));
  return new Set(normalized).size !== normalized.length;
}

exports.getById = async (req, res, next) => {
  try {
    const { id } = req.params;
    const desheet = await req.dbPool.query(`
      SELECT d.*, c.title as contract_title, c.workorder, a.item_name as product_name
      FROM designsheet d
      LEFT JOIN contracts c ON d.contract_id = c.id
      LEFT JOIN st_additem a ON d.item_id = a.id
      WHERE d.id = :id
    `, { replacements: { id }, type: QueryTypes.SELECT });

    if (desheet.length === 0) return res.status(404).json({ message: 'Design sheet not found' });

    const product = await req.dbPool.query(`
      SELECT pd.*, a.item_name
      FROM designsheetdetails pd
      LEFT JOIN st_additem a ON pd.item_id = a.id
      WHERE pd.designsheet_id = :id
    `, { replacements: { id }, type: QueryTypes.SELECT });

    // Fetch finished products for the contract
    const contractid = desheet[0].contract_id;
    const bomfinishedproduct = await req.models.bom_finisedproduct.findAll({
      where: { contract_id: contractid },
      raw: true
    });

    const finishitem = [];
    for (const value of bomfinishedproduct) {
      const item = await req.models.st_additem.findOne({ where: { id: value.product_id }, raw: true });
      if (item) finishitem.push(item);
    }

    const rawitem = product.map(p => p.item_id);
    const indentpo_count = await countIndentPo(req, desheet[0].contract_id, desheet[0].item_id);

    res.json({ desheet: { ...desheet[0], indentpo_count }, product, finishitem, rawitem, indentpo_count });
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

    const contract = await req.models.contracts.findOne({ attributes: ['id'], where: { id: contract_id, status: 'Y' }, raw: true });
    if (!contract) return res.status(400).json({ message: 'Your enterd Contract does not exists.' });

    const bomItem = await req.models.bom_finisedproduct.findOne({
      attributes: ['quantity'],
      where: { contract_id, product_id: item_id },
      raw: true
    });
    if (!bomItem) return res.status(400).json({ message: 'Your enterd Product does not exists.' });

    const existingSheet = await req.models.designsheet.findOne({
      attributes: ['id'],
      where: { contract_id, item_id },
      raw: true
    });
    if (existingSheet) return res.status(409).json({ message: 'This Product Already Exits' });

    if (!designsheetno) {
       const lastDesign = await req.models.designsheet.findOne({
           attributes: ['designsheetno'],
           order: [['designsheetno', 'DESC']],
           raw: true
       });
       if (lastDesign && lastDesign.designsheetno) {
           designsheetno = String(parseInt(lastDesign.designsheetno) + 1);
       } else {
           designsheetno = "1001";
       }
    }

    let design_sheet = null;
    if (req.files && req.files['design_sheet']) {
      design_sheet = req.files['design_sheet'][0].filename;
    }

    if (hasDuplicateValues(normalizeArray(pitemname))) return res.status(400).json({message:'This Item Already added'});
    const dFrom = new Date(datefrom).toISOString().slice(0, 19).replace('T', ' ');

    await req.dbPool.transaction(async transaction => {
    const insertResult = await req.models.designsheet.create({
        contract_id, designsheetno, item_id, quantity, datefrom: dFrom, design_sheet, created: fn('NOW')
    }, {transaction});

    const lstid = insertResult.id;

    // Process details
    let pitemnameArr = normalizeArray(pitemname);
    let km_item_qtyArr = normalizeArray(km_item_qty);
    let pitemquantityArr = normalizeArray(pitemquantity);
    let unit_nameArr = normalizeArray(unit_name);
    let is_groupArr = normalizeArray(is_group);


    const detailsToInsert = [];
    for (let i = 0; i < pitemnameArr.length; i++) {
       detailsToInsert.push({
           designsheet_id: lstid,
           designsheetno,
           contract_id,
           item_id: pitemnameArr[i],
           km_item_qty: km_item_qtyArr[i] || 0,
           item_qty: pitemquantityArr[i] || 0,
           is_group: is_groupArr[i] || '0',
           uom: unit_nameArr[i] || ''
       });
    }
    if (detailsToInsert.length > 0) {
        await req.models.designsheetdetails.bulkCreate(detailsToInsert, {transaction});
    }

    });

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

    const desheet = await req.models.designsheet.findOne({ where: { id }, raw: true });
    if (!desheet) return res.status(404).json({ message: 'Design sheet not found' });

    const contract = await req.models.contracts.findOne({ attributes: ['id'], where: { id: contract_id, status: 'Y' }, raw: true });
    if (!contract) return res.status(400).json({ message: 'Your enterd Contract does not exists.' });

    const bomItem = await req.models.bom_finisedproduct.findOne({
      attributes: ['quantity'],
      where: { contract_id, product_id: item_id },
      raw: true
    });
    if (!bomItem) return res.status(400).json({ message: 'Your enterd Product does not exists.' });

    const persistedDetails = await req.models.designsheetdetails.findAll({attributes:['item_id'],where:{designsheet_id:id},raw:true});
    if (hasDuplicateValues([...persistedDetails.map(row=>row.item_id),...normalizeArray(pitemname)])) return res.status(400).json({message:'This Item Already added'});
    if (String(desheet.designsheetno)!==String(designsheetno)) return res.status(400).json({message:'Design sheet number cannot be changed.'});
    const obsoleteFiles = [];
    const dFrom = new Date(datefrom).toISOString().slice(0, 19).replace('T', ' ');

    const updateData = {
        contract_id, designsheetno, item_id, quantity, datefrom: dFrom, updated: fn('NOW')
    };

    await req.dbPool.transaction(async transaction => {
    if (req.files && req.files['design_sheet']) {
       if (desheet.design_sheet) {
           const oldFile = path.join(uploadDir(req), desheet.design_sheet);
           obsoleteFiles.push(oldFile);
       }
       updateData.design_sheet = req.files['design_sheet'][0].filename;
    }

    // Revisions
    for (let i = 1; i <= 5; i++) {
        if (req.files && req.files[`r${i}`]) {
            if (desheet[`r${i}`]) {
                const oldFile = path.join(uploadDir(req), desheet[`r${i}`]);
                obsoleteFiles.push(oldFile);
            }
            updateData[`r${i}`] = req.files[`r${i}`][0].filename;
        }
    }

    await req.models.designsheet.update(updateData, { where: { id }, transaction });

    // Update existing items
    if (pitemname11) {
        let pitemname11Arr = normalizeArray(pitemname11);
        let is_group11Arr = normalizeArray(is_group11);
        for (let i = 0; i < pitemname11Arr.length; i++) {
            await req.models.designsheetdetails.update(
                { is_group: is_group11Arr[i] || '0' },
                { where: { designsheet_id:id, item_id: pitemname11Arr[i] }, transaction }
            );
        }
    }

    // Add new items
    if (pitemname) {
        let pitemnameArr = normalizeArray(pitemname);
        let km_item_qtyArr = normalizeArray(km_item_qty);
        let pitemquantityArr = normalizeArray(pitemquantity);
        let unit_nameArr = normalizeArray(unit_name);
        let is_groupArr = normalizeArray(is_group);
        const newDetails = [];
        for (let i = 0; i < pitemnameArr.length; i++) {
           newDetails.push({
               designsheet_id: id,
               designsheetno,
               contract_id,
               item_id: pitemnameArr[i],
               km_item_qty: km_item_qtyArr[i] || 0,
               item_qty: pitemquantityArr[i] || 0,
               is_group: is_groupArr[i] || '0',
               uom: unit_nameArr[i] || ''
           });
        }
        if (newDetails.length > 0) {
            await req.models.designsheetdetails.bulkCreate(newDetails, {transaction});
        }
    }

    });
    for (const file of obsoleteFiles) if (path.dirname(file)===uploadDir(req) && fs.existsSync(file)) fs.unlinkSync(file);

    res.json({ message: 'Design Sheet has been updated successfully.' });
  } catch (error) {
    next(error);
  }
};

exports.deleteSheet = async (req, res, next) => {
  try {
    const { id } = req.params;
    const desheet = await req.models.designsheet.findOne({ where: { id }, raw: true });
    if (!desheet) return res.status(404).json({ message: 'Not found' });

    const indentCount = await countIndentPo(req, desheet.contract_id, desheet.item_id);
    if (indentCount > 0) {
      return res.status(409).json({ message: 'Design Sheet cannot be deleted because an indent PO already exists.' });
    }

    const files = [desheet.design_sheet, desheet.r1, desheet.r2, desheet.r3, desheet.r4, desheet.r5];
    await req.dbPool.transaction(async transaction=>{
      await req.models.designsheetdetails.destroy({where:{designsheet_id:id},transaction});
      await req.models.designsheet.destroy({where:{id},transaction});
    });
    for(const filename of files){
      if(!filename) continue;
      const file=path.resolve(uploadDir(req),filename);
      if(path.dirname(file)===uploadDir(req) && fs.existsSync(file)) fs.unlinkSync(file);
    }

    res.json({ message: 'Production Sheet deleted successfully' });
  } catch (error) {
    next(error);
  }
};

exports.deleteDetailData = async (req, res, next) => {
  try {
    const { id } = req.params;
    const detail = await req.models.designsheetdetails.findOne({ where: { id }, raw: true });
    if (!detail) return res.status(404).json({ message: 'Design sheet detail not found' });
    const sheet = await req.models.designsheet.findOne({ where: { id: detail.designsheet_id }, raw: true });
    if (sheet) {
      const indentCount = await countIndentPo(req, sheet.contract_id, sheet.item_id);
      if (indentCount > 0) {
        return res.status(409).json({ message: 'Design Sheet item cannot be deleted because an indent PO already exists.' });
      }
    }
    await req.models.designsheetdetails.destroy({ where: { id } });
    res.json({ result: true });
  } catch (error) {
    next(error);
  }
};

exports.getBomFinishedProduct = async (req, res, next) => {
  try {
    const { contractid } = req.query;
    const bom = await req.models.bom_finisedproduct.findAll({ where: { contract_id: contractid }, raw: true });

    const items = [];
    for (const value of bom) {
      const item = await req.models.st_additem.findOne({ where: { id: value.product_id }, raw: true });
      if (item) items.push(item);
    }

    res.json({ item: items });
  } catch (error) {
    next(error);
  }
};

exports.checkDesignSheetItem = async (req, res, next) => {
  try {
    const { itemid, contractid } = req.query;
    if (!itemid || !contractid) return res.status(400).json({ message: 'Missing parameters' });

    const checkdesign = await req.models.designsheet.findOne({ attributes: ['id'], where: { item_id: itemid, contract_id: contractid }, raw: true });
    const itemqty = await req.models.bom_finisedproduct.findOne({ attributes: ['quantity'], where: { product_id: itemid, contract_id: contractid }, raw: true });

    res.json({
        checkdesign: checkdesign ? checkdesign : null,
        itemqty: itemqty ? itemqty.quantity : null
    });
  } catch (error) {
    next(error);
  }
};

exports.indentItems = async (req, res, next) => {
  try {
    const { fetch } = req.query;
    const itemnameArr = await req.dbPool.query(`
      SELECT a.*, m.unit_name
      FROM st_additem a
      LEFT JOIN st_measurementunits m ON a.uom = m.id
      WHERE a.status = 'Y' AND a.id = :fetch
      ORDER BY a.id ASC LIMIT 1
    `, { replacements: { fetch }, type: QueryTypes.SELECT });
    const itemname = itemnameArr.length > 0 ? itemnameArr[0] : null;

    const tax = await req.models.st_taxmaster.findAll({
        attributes: ['id', 'tax'],
        where: { status: 'Y', parent: '0' },
        order: [['id', 'ASC']],
        raw: true
    });

    res.json({ itemname, tax });
  } catch (error) {
    next(error);
  }
};

exports.searchItems = async (req, res, next) => {
  try {
    const { query } = req.query;
    if (!query) return res.json({ items: [] });
    const items = await req.models.st_additem.findAll({
        attributes: ['id', 'item_name'],
        where: { status: 'Y', itemtype: 'RawMaterial', item_name: { [Op.like]: `%${query}%` } },
        order: [['item_name', 'ASC']],
        limit: 20,
        raw: true
    });
    res.json({ items });
  } catch (err) {
    next(err);
  }
};

exports.viewDesignSheet = async (req, res, next) => {
  try {
    const { designsheetno } = req.params;
    const where = req.params.id ? {id:req.params.id} : {designsheetno};
    const designsheet = await req.models.designsheet.findOne({ where, raw: true });
    if (!designsheet) return res.status(404).json({message:'Design sheet not found'});

    const designsheetdetails = await req.dbPool.query(`
        SELECT d.*, a.item_name, c.title, c.workorder
        FROM designsheetdetails d
        LEFT JOIN st_additem a ON d.item_id = a.id
        LEFT JOIN contracts c ON d.contract_id = c.id
        WHERE d.designsheet_id = :sheetId
        ORDER BY d.id ASC
    `, { replacements: { sheetId: designsheet.id }, type: QueryTypes.SELECT });

    const sitesetting = await req.models.sitesettings.findOne({ raw: true });
    const site_details = await req.models.sitesettings_details.findOne({ where: { status: 'Y' }, raw: true });

    if (designsheet) {
        const a = await req.models.st_additem.findOne({ attributes: ['item_name'], where: { id: designsheet.item_id }, raw: true });
        if (a) designsheet.item_name = a.item_name;
        const c = await req.models.contracts.findOne({ attributes: ['title', 'workorder'], where: { id: designsheet.contract_id }, raw: true });
        if (c) designsheet.contract_no = `${c.title}(${c.workorder})`;
    }

    if (req.pdfDownload) {
      if (!designsheet) return res.status(404).json({message:'Design sheet not found'});
      const {generateDesignSheetPDF} = require('./designsheet.pdf');
      const pdf = await generateDesignSheetPDF({designsheet, designsheetdetails, sitesetting, site_details});
      res.setHeader('Content-Type','application/pdf');
      res.setHeader('Content-Disposition',`inline; filename="Design_Sheet_${Number(designsheet.designsheetno)}.pdf"`);
      return res.send(pdf);
    }
    res.json({ designsheet, designsheetdetails, sitesetting, site_details });
  } catch (error) {
    next(error);
  }
};

exports.getItemCatg = async (req, res, next) => {
  try {
    const { fetch } = req.query;
    // Keeping exactly original logic: WHERE category_id = ? LIMIT 1
    const unitid = await req.models.st_additem.findOne({ attributes: ['category_id'], where: { category_id: fetch }, raw: true });
    res.json({ id: unitid ? unitid.category_id : null });
  } catch (error) {
    next(error);
  }
};

exports.searchContracts = async (req, res, next) => {
  try {
    const { query } = req.query;
    if (!query) return res.json({ contracts: [] });

    const contracts = await req.models.contracts.findAll({
        attributes: ['id', 'title', 'workorder'],
        where: {
            status: 'Y',
            [Op.or]: [
                { workorder: { [Op.like]: `%${query}%` } },
                { title: { [Op.like]: `%${query}%` } }
            ]
        },
        order: [['id', 'DESC']],
        limit: 20,
        raw: true
    });

    res.json({ contracts });
  } catch (error) {
    next(error);
  }
};

exports.getBomFinishedProducts = async (req, res, next) => {
  try {
    const { contractId } = req.params;

    const products = await req.dbPool.query(`
      SELECT a.id, a.item_name
      FROM bom_finisedproduct b
      JOIN st_additem a ON b.product_id = a.id
      WHERE b.contract_id = :contractId
      ORDER BY a.item_name ASC
    `, { replacements: { contractId }, type: QueryTypes.SELECT });

    res.json({ products });
  } catch (error) {
    next(error);
  }
};

exports.getContractDetails = async (req, res, next) => {
  try {
    const { contractId } = req.params;

    // 1. Contract Info
    const contractArr = await req.dbPool.query(`
      SELECT c.*, s.supplier_name
      FROM contracts c
      LEFT JOIN st_supplier s ON c.supplier_id = s.id
      WHERE c.id = :contractId LIMIT 1
    `, { replacements: { contractId }, type: QueryTypes.SELECT });

    if (contractArr.length === 0) {
      return res.status(404).json({ message: 'Contract not found' });
    }
    const contract = contractArr[0];

    contract.labour_cost = 0;
    contract.operational_cost = 0;

    const prodCost = await req.dbPool.query(`
        SELECT SUM(manpower_day + manpower_night) as labour, SUM(nextday8am - reading8am) as operational
        FROM production
        WHERE contract_id = :contractId
    `, { replacements: { contractId }, type: QueryTypes.SELECT });
    contract.labour_cost = prodCost[0]?.labour || 0;
    contract.operational_cost = prodCost[0]?.operational || 0;

    // 2. Finished Products
    const finishedProducts = await req.dbPool.query(`
      SELECT b.*, a.item_name
      FROM bom_finisedproduct b
      LEFT JOIN st_additem a ON b.product_id = a.id
      WHERE b.contract_id = :contractId
    `, { replacements: { contractId }, type: QueryTypes.SELECT });

    // 3. For each finished product, fetch the design sheet raw materials
    for (let fp of finishedProducts) {
        const designSheet = await req.models.designsheet.findOne({
            attributes: ['designsheetno'],
            where: { contract_id: contractId, item_id: fp.product_id },
            raw: true
        });

        fp.rawMaterials = [];
        if (designSheet) {
            const dsNo = designSheet.designsheetno;
            const rawMaterials = await req.dbPool.query(`
                SELECT d.item_id, d.km_item_qty, d.item_qty, d.is_group, d.uom,
                       a.item_name, a.category_id,
                       c.category_name
                FROM designsheetdetails d
                LEFT JOIN st_additem a ON d.item_id = a.id
                LEFT JOIN st_categorymaster c ON a.category_id = c.id
                WHERE d.designsheetno = :dsNo
            `, { replacements: { dsNo }, type: QueryTypes.SELECT });

            for (let rm of rawMaterials) {
                if (rm.is_group === '1' || rm.is_group > 0) {
                    rm.display_name = rm.category_name || rm.item_name;
                } else {
                    rm.display_name = rm.item_name;
                }

                rm.qty_as_per_design = parseFloat(rm.item_qty) || 0;

                const issueRes = await req.dbPool.query(`
                    SELECT SUM(quantity) as issued_qty
                    FROM st_stock_register
                    WHERE contract_id = :contractId AND finishedproduct_id = :product_id AND item_id = :item_id AND store_type = 2
                `, { replacements: { contractId, product_id: fp.product_id, item_id: rm.item_id }, type: QueryTypes.SELECT });

                const reverseRes = await req.dbPool.query(`
                    SELECT SUM(quantity) as reverse_qty
                    FROM st_stock_register
                    WHERE contract_id = :contractId AND finishedproduct_id = :product_id AND item_id = :item_id AND store_type = 3
                `, { replacements: { contractId, product_id: fp.product_id, item_id: rm.item_id }, type: QueryTypes.SELECT });

                const issued = (parseFloat(issueRes[0]?.issued_qty) || 0) - (parseFloat(reverseRes[0]?.reverse_qty) || 0);
                rm.issued_qty = issued;
                rm.pending_qty = rm.qty_as_per_design - issued;

                if (rm.is_group === '1' || rm.is_group > 0) {
                    const catItems = await req.models.st_additem.findAll({
                        attributes: ['id', 'item_name'],
                        where: { category_id: rm.category_id },
                        raw: true
                    });
                    rm.subItems = [];
                    for (let ci of catItems) {
                        const ciIssueRes = await req.dbPool.query(`
                            SELECT SUM(quantity) as issued_qty
                            FROM st_stock_register
                            WHERE contract_id = :contractId AND finishedproduct_id = :product_id AND item_id = :item_id AND store_type = 2
                        `, { replacements: { contractId, product_id: fp.product_id, item_id: ci.id }, type: QueryTypes.SELECT });

                        const ciReverseRes = await req.dbPool.query(`
                            SELECT SUM(quantity) as reverse_qty
                            FROM st_stock_register
                            WHERE contract_id = :contractId AND finishedproduct_id = :product_id AND item_id = :item_id AND store_type = 3
                        `, { replacements: { contractId, product_id: fp.product_id, item_id: ci.id }, type: QueryTypes.SELECT });

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

        const poData = await req.dbPool.query(`
            SELECT SUM(plannedqty) as planned_qty
            FROM productionorder
            WHERE contract_id = :contractId AND item_id = :product_id
        `, { replacements: { contractId, product_id: fp.product_id }, type: QueryTypes.SELECT });
        fp.planned_qty = poData[0]?.planned_qty || 0;

        const preparedData = await req.dbPool.query(`
            SELECT SUM(production_shift_a + production_shift_b) as prepared_qty
            FROM production
            WHERE contract_id = :contractId AND item_id = :product_id AND productprocess_id = 8
        `, { replacements: { contractId, product_id: fp.product_id }, type: QueryTypes.SELECT });
        fp.prepared_qty = preparedData[0]?.prepared_qty || 0;
    }

    // 4. Production Orders
    const productionOrders = await req.dbPool.query(`
        SELECT p.*, a.item_name
        FROM productionorder p
        LEFT JOIN st_additem a ON p.item_id = a.id
        WHERE p.contract_id = :contractId
        ORDER BY p.id DESC
    `, { replacements: { contractId }, type: QueryTypes.SELECT });

    for (let po of productionOrders) {
        const prepData = await req.dbPool.query(`
            SELECT SUM(production_shift_a + production_shift_b) as prepared_qty
            FROM production
            WHERE po_id = :po_id AND productprocess_id = 8
        `, { replacements: { po_id: po.po_id }, type: QueryTypes.SELECT });
        po.prepared_qty = prepData[0]?.prepared_qty || 0;
    }

    // 5. Inspection Report
    const inspectionReports = await req.models.st_inspection_report.findAll({
        where: { work_order_no: contractId, status: 'Y' },
        order: [['id', 'DESC']],
        raw: true
    });

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
