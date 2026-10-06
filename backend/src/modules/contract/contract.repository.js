const { Op, QueryTypes, col, fn, literal, where: sqlWhere } = require('sequelize');
const {listOrder} = require('../../utils/listPagination');

class ContractRepository {
  async findFiltered(dbPool, filters = {}) {
    const { contracts, vendors } = dbPool.models;
    
    // Ensure association exists (idempotent)
    if (!contracts.associations.vendor) {
      contracts.belongsTo(vendors, { foreignKey: 'supplier_id', as: 'vendor' });
    }

    const where = {};
    if (filters.contract_id) where.id = filters.contract_id;
    if (filters.vendor_id) where.supplier_id = filters.vendor_id;
    if (filters.contract_name) {
      where[Op.or] = [
        { title: { [Op.like]: `%${filters.contract_name}%` } },
        { workorder: { [Op.like]: `%${filters.contract_name}%` } }
      ];
    }
    if (filters.cost) {
      where.cost = { [Op.like]: `%${filters.cost}%` };
    }
    if (filters.datefrom && filters.datefrom !== '1970-01-01') {
      where[Op.and] = [...(where[Op.and] || []), sqlWhere(fn('DATE',col('contracts.contract_start_date')), { [Op.gte]: filters.datefrom })];
    }
    if (filters.dateto && filters.dateto !== '1970-01-01') {
      where[Op.and] = [...(where[Op.and] || []), sqlWhere(fn('DATE',col('contracts.contract_end_date')), { [Op.lte]: filters.dateto })];
    }

    const vendorWhere = {};
    if (filters.vendor_name) {
      vendorWhere.name = { [Op.like]: `%${filters.vendor_name}%` };
    }

    const isInnerJoin = Object.keys(vendorWhere).length > 0;

    const sort = listOrder(filters.sort,filters.direction,{id:'id',title:'title',workorder:'workorder',cost:'cost',contract_start_date:'contract_start_date',contract_end_date:'contract_end_date',issuedate:'issuedate'},'id');
    return await contracts.findAndCountAll({
      attributes: [
        'id', 'supplier_id', 'title', 'workorder', 'cost', 'contract_start_date', 'contract_end_date', 'issuedate', 'description', 'status', 'added_time',
        [col('vendor.name'), 'vendor_name'],
        [literal(`(SELECT COUNT(*) FROM designsheet WHERE contract_id = contracts.id)`), 'designsheet_count']
      ],
      include: [{
        model: vendors,
        as: 'vendor',
        attributes: [],
        where: isInnerJoin ? vendorWhere : undefined,
        required: isInnerJoin
      }],
      where,
      order: sort.column === 'id' ? [['id',sort.direction]] : [[sort.column,sort.direction],['id','DESC']],
      limit: filters.limit ? parseInt(filters.limit, 10) : undefined,
      offset: filters.offset ? parseInt(filters.offset, 10) : undefined,
      raw: true
    });
  }

  async findById(dbPool, id) {
    const { contracts, vendors } = dbPool.models;
    
    if (!contracts.associations.vendor) {
      contracts.belongsTo(vendors, { foreignKey: 'supplier_id', as: 'vendor' });
    }

    const contract = await contracts.findOne({
      attributes: [
        'id', 'supplier_id', 'title', 'workorder', 'cost', 'operation_cost', 'labour_cost', 'description', 'status', 'contract_start_date', 'contract_end_date', 'issuedate',
        [col('vendor.name'), 'vendor_name'],
        [fn('COALESCE', col('vendor.gst_number'), 'N/A'), 'gst_number']
      ],
      include: [{
        model: vendors,
        as: 'vendor',
        attributes: [],
        required: false
      }],
      where: { id },
      raw: true
    });

    return contract || null;
  }

  async titleExists(dbPool, title, excludeId = null) {
    const where = { title };
    if (excludeId) where.id = { [Op.ne]: excludeId };
    return await dbPool.models.contracts.count({ where }) > 0;
  }

  async vendorExists(dbPool, supplierId) {
    if (!supplierId) return false;
    return await dbPool.models.vendors.count({
      where: { id: supplierId, status: 'Y' }
    }) > 0;
  }

  async countDesignSheets(dbPool, contractId) {
    return await dbPool.models.designsheet.count({
      where: { contract_id: contractId }
    });
  }

  async findItemsByContractId(dbPool, contractId) {
    const query = `
      SELECT 
        bfp.id, bfp.product_id, bfp.price, bfp.quantity, i.item_name, COALESCE(u.unit_name, 'KG') as uom,
        (
          SELECT COALESCE(SUM(plannedqty), 0)
          FROM productionorder po
          WHERE po.contract_id = bfp.contract_id AND po.item_id = bfp.product_id
        ) as planned_qty,
        (SELECT COALESCE(SUM(COALESCE(p.production_shift_a, 0) + COALESCE(p.production_shift_b, 0)), 0)
         FROM production p WHERE p.contract_id = bfp.contract_id AND p.item_id = bfp.product_id
           AND p.productprocess_id = '8') as prepared_qty
      FROM bom_finisedproduct bfp
      LEFT JOIN st_additem i ON bfp.product_id = i.id
      LEFT JOIN st_measurementunits u ON i.uom = u.id
      WHERE bfp.contract_id = :contractId
      ORDER BY bfp.id ASC
    `;
    return await dbPool.query(query, {
      replacements: { contractId },
      type: QueryTypes.SELECT
    });
  }

  async findDesignSheetDetails(dbPool, contractId, productId) {
    // 1. Find designsheetno
    const designSheet = await dbPool.models.designsheet.findOne({
      attributes: ['designsheetno'],
      where: { contract_id: contractId, item_id: productId },
      raw: true
    });

    if (!designSheet) return [];
    const sheetNo = designSheet.designsheetno;

    // 2. Fetch design sheet details
    const query = `
      SELECT dsd.item_id,
        (SELECT COALESCE(SUM(d.item_qty), 0) FROM designsheetdetails d
         WHERE d.designsheetno = dsd.designsheetno AND d.item_id = dsd.item_id) as as_per_design,
        dsd.is_group, a.item_name, a.category_id, c.category_name
      FROM designsheetdetails dsd
      LEFT JOIN st_additem a ON a.id = dsd.item_id
      LEFT JOIN st_categorymaster c ON c.id = a.category_id
      WHERE dsd.designsheetno = :sheetNo
      ORDER BY dsd.id ASC
    `;
    const details = await dbPool.query(query, {
      replacements: { sheetNo },
      type: QueryTypes.SELECT
    });

    const result = [];
    for (const row of details) {
      let issuedItems = [];
      let totalIssued = 0;

      if (row.is_group == 1 && row.category_id) {
        issuedItems = await dbPool.query(`
          SELECT s.item_id, a.item_name,
            SUM(CASE WHEN s.store_type = '2' THEN s.quantity ELSE -s.quantity END) as issued_qty
          FROM st_stock_register s
          JOIN st_additem a ON a.id = s.item_id
          WHERE s.contract_id = :contractId AND s.finishedproduct_id = :productId AND s.store_type IN ('2', '3')
            AND a.category_id = :categoryId
          GROUP BY s.item_id, a.item_name ORDER BY s.item_id ASC`, {
          replacements: { contractId, productId, categoryId: row.category_id },
          type: QueryTypes.SELECT
        });
      } else {
        issuedItems = await dbPool.query(`
          SELECT s.item_id, a.item_name,
            SUM(CASE WHEN s.store_type = '2' THEN s.quantity ELSE -s.quantity END) as issued_qty
          FROM st_stock_register s
          LEFT JOIN st_additem a ON a.id = s.item_id
          WHERE s.contract_id = :contractId AND s.finishedproduct_id = :productId AND s.store_type IN ('2', '3')
            AND s.item_id = :itemId
          GROUP BY s.item_id, a.item_name ORDER BY s.item_id ASC`, {
          replacements: { contractId, productId, itemId: row.item_id },
          type: QueryTypes.SELECT
        });
      }

      totalIssued = issuedItems.reduce((sum, item) => sum + (Number(item.issued_qty) || 0), 0);

      result.push({
        id: row.item_id,
        item_name: Number(row.is_group) > 0 ? row.category_name : row.item_name,
        as_per_design: row.as_per_design,
        total_issued: totalIssued,
        pending_qty: Number(row.as_per_design) - totalIssued,
        issued_items: Number(row.is_group) > 0 ? issuedItems.filter(item => Number(item.issued_qty) !== 0) : []
      });
    }

    return result;
  }

  async findProductionOrdersByContractId(dbPool, contractId) {
    const query = `
      SELECT 
        po.po_id, po.issuedate, po.plannedqty, po.startdate, po.enddate, po.status,
        i.item_name as product_name,
        (SELECT COALESCE(SUM(COALESCE(p.production_shift_a, 0) + COALESCE(p.production_shift_b, 0)), 0)
         FROM production p WHERE p.po_id = po.po_id AND p.productprocess_id = '8') as prepared_qty
      FROM productionorder po
      LEFT JOIN st_additem i ON po.item_id = i.id
      WHERE po.contract_id = :contractId
      ORDER BY po.id DESC
    `;
    return await dbPool.query(query, {
      replacements: { contractId },
      type: QueryTypes.SELECT
    });
  }

  async findInspectionReportsByContractId(dbPool, contractId) {
    return await dbPool.models.st_inspection_report.findAll({
      attributes: [
        ['id', 's_no'],
        ['name', 'inspector_name'],
        'inspection_date'
      ],
      where: { work_order_no: contractId, status: 'Y' },
      order: [['id', 'DESC']],
      raw: true
    });
  }

  async getPdfProduction(dbPool, contractId, productId) {
    const rows = await dbPool.query(`
      SELECT p.*, f.process_name
      FROM production p
      LEFT JOIN finishedproduct_process f ON f.id = p.productprocess_id
      WHERE p.contract_id = :contractId AND p.item_id = :productId
      ORDER BY p.id ASC`, { replacements: { contractId, productId }, type: QueryTypes.SELECT });
    const grouped = new Map();
    for (const row of rows) {
      // PHP iterates the process master, so orphan process IDs have no displayed row.
      if (!row.process_name) continue;
      const key = String(row.productprocess_id);
      if (!grouped.has(key)) grouped.set(key, {process_id:Number(key), process_name:row.process_name, start_date:row.production_date, end_date:row.production_date, po_numbers:[], quantity:0});
      const process = grouped.get(key);
      process.end_date = row.production_date;
      if (!process.po_numbers.includes(row.po_id)) process.po_numbers.push(row.po_id);
      process.quantity += Number(row.production_shift_a || 0) + Number(row.production_shift_b || 0);
    }
    return {
      has_production:rows.length > 0,
      labour:rows.reduce((sum,p)=>sum+Number(p.manpower_day || 0)+Number(p.manpower_night || 0),0),
      operation:rows.reduce((sum,p)=>sum+Number(p.nextday8am || 0)-Number(p.reading8am || 0),0),
      processes:Array.from(grouped.values()).sort((a,b)=>a.process_id-b.process_id).map(p=>({...p, po_numbers:p.po_numbers.join(',')}))
    };
  }

  async getFormData(dbPool) {
    const vendors = await dbPool.models.vendors.findAll({
      attributes: ['id', 'name'],
      order: [['name', 'ASC']],
      raw: true
    });
    
    const query = `
      SELECT id, item_name as name 
      FROM st_additem 
      WHERE itemtype = 'FinishedProduct' 
         OR category_id IN (SELECT id FROM st_categorymaster WHERE category_name LIKE '%FINISH%')
      ORDER BY item_name ASC
    `;
    const items = await dbPool.query(query, { type: QueryTypes.SELECT });
    return { vendors, items };
  }

  async createContract(dbPool, data, transaction) {
    const contract = await dbPool.models.contracts.create({
      supplier_id: data.supplier_id || null,
      title: data.title || null,
      workorder: data.workorder || null,
      cost: data.cost || null,
      operation_cost: data.operation_cost || null,
      labour_cost: data.labour_cost || null,
      issuedate: data.issuedate || null,
      contract_start_date: data.contract_start_date || null,
      contract_end_date: data.contract_end_date || null,
      description: data.description || null,
      added_time: new Date(),
      status: 'Y'
    }, { transaction });
    return contract.id;
  }

  async updateContract(dbPool, contractId, data, transaction) {
    await dbPool.models.contracts.update({
      supplier_id: data.supplier_id || null,
      title: data.title || null,
      workorder: data.workorder || null,
      cost: data.cost || null,
      operation_cost: data.operation_cost || null,
      labour_cost: data.labour_cost || null,
      issuedate: data.issuedate || null,
      contract_start_date: data.contract_start_date || null,
      contract_end_date: data.contract_end_date || null,
      description: data.description || null,
      added_time: new Date()
    }, {
      where: { id: contractId },
      transaction
    });
  }

  async upsertBom(dbPool, contractId, data, transaction) {
    const bomPayload = {
      contract_id: contractId,
      comment: data.description || null,
      operation_cost: data.operation_cost || null,
      labour_cost: data.labour_cost || null,
      created: data.issuedate || new Date()
    };

    const existing = await dbPool.models.bom.findOne({
      where: { contract_id: String(contractId) },
      transaction
    });

    if (existing) {
      await existing.update(bomPayload, { transaction });
      return existing.id;
    }

    const bom = await dbPool.models.bom.create(bomPayload, { transaction });
    return bom.id;
  }

  async replaceFinishedProducts(dbPool, contractId, products, transaction) {
    await dbPool.models.bom_finisedproduct.destroy({
      where: { contract_id: contractId },
      transaction
    });

    for (const product of products || []) {
      if (product.product_id) {
        await this.addFinishedProduct(dbPool, contractId, product, transaction);
      }
    }
  }

  async addFinishedProduct(dbPool, contractId, product, transaction) {
    await dbPool.models.bom_finisedproduct.create({
      contract_id: contractId,
      product_id: product.product_id,
      price: product.price || '0',
      quantity: product.quantity || '0'
    }, { transaction });
  }

  async deleteContractCascade(dbPool, contractId, transaction) {
    await dbPool.models.bom.destroy({
      where: { contract_id: String(contractId) },
      transaction
    });
    await dbPool.models.bom_finisedproduct.destroy({
      where: { contract_id: contractId },
      transaction
    });
    await dbPool.models.contracts.destroy({
      where: { id: contractId },
      transaction
    });
  }
}

module.exports = new ContractRepository();
