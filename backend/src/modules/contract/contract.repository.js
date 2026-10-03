const { Op, QueryTypes, col, fn, literal } = require('sequelize');

class ContractRepository {
  async findFiltered(dbPool, filters = {}) {
    const { contracts, vendors } = dbPool.models;
    
    // Ensure association exists (idempotent)
    if (!contracts.associations.vendor) {
      contracts.belongsTo(vendors, { foreignKey: 'supplier_id', as: 'vendor' });
    }

    const where = {};
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
      where.contract_start_date = { [Op.gte]: filters.datefrom };
    }
    if (filters.dateto && filters.dateto !== '1970-01-01') {
      where.contract_end_date = { [Op.lte]: filters.dateto };
    }

    const vendorWhere = {};
    if (filters.vendor_name) {
      vendorWhere.name = { [Op.like]: `%${filters.vendor_name}%` };
    }

    const isInnerJoin = Object.keys(vendorWhere).length > 0;

    return await contracts.findAndCountAll({
      attributes: [
        'id', 'title', 'workorder', 'cost', 'contract_start_date', 'contract_end_date', 'issuedate', 'description', 'status', 'added_time',
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
      order: [['id', 'DESC']],
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
        0 as prepared_qty
      FROM bom_finisedproduct bfp
      LEFT JOIN st_additem i ON bfp.product_id = i.id
      LEFT JOIN st_measurementunits u ON i.uom = u.id
      WHERE bfp.contract_id = :contractId
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
      SELECT dsd.item_id, dsd.item_qty as as_per_design, dsd.is_group, a.item_name, a.category_id
      FROM designsheetdetails dsd
      JOIN st_additem a ON a.id = dsd.item_id
      WHERE dsd.designsheetno = :sheetNo
      ORDER BY dsd.is_group ASC
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
          SELECT s.item_id, a.item_name, ROUND(SUM(s.quantity), 2) as issued_qty
          FROM st_stock_register s
          JOIN st_additem a ON a.id = s.item_id
          WHERE s.contract_id = :contractId AND s.finishedproduct_id = :productId AND s.store_type = '2' 
            AND a.category_id = :categoryId
          GROUP BY s.item_id`, {
          replacements: { contractId, productId, categoryId: row.category_id },
          type: QueryTypes.SELECT
        });
      } else {
        issuedItems = await dbPool.query(`
          SELECT s.item_id, a.item_name, ROUND(SUM(s.quantity), 2) as issued_qty
          FROM st_stock_register s
          JOIN st_additem a ON a.id = s.item_id
          WHERE s.contract_id = :contractId AND s.finishedproduct_id = :productId AND s.store_type = '2' 
            AND s.item_id = :itemId
          GROUP BY s.item_id`, {
          replacements: { contractId, productId, itemId: row.item_id },
          type: QueryTypes.SELECT
        });
      }

      totalIssued = issuedItems.reduce((sum, item) => sum + (Number(item.issued_qty) || 0), 0);

      result.push({
        id: row.item_id,
        item_name: row.item_name,
        as_per_design: row.as_per_design,
        total_issued: totalIssued,
        pending_qty: Math.max(0, row.as_per_design - totalIssued),
        issued_items: issuedItems
      });
    }

    return result;
  }

  async findProductionOrdersByContractId(dbPool, contractId) {
    const query = `
      SELECT 
        po.po_id, po.issuedate, po.plannedqty, po.startdate, po.enddate, po.status,
        i.item_name as product_name, 0 as prepared_qty
      FROM productionorder po
      LEFT JOIN st_additem i ON po.item_id = i.id
      WHERE po.contract_id = :contractId
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
      where: { work_order_no: contractId },
      raw: true
    });
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
