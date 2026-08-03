const indentpoService = require('./indentpo.service');

class IndentpoController {
  async getNextIndentId(req, res, next) {
    try {
      const id = await indentpoService.getNextIndentId(req.dbPool);
      res.json({ next_id: id });
    } catch (err) {
      next(err);
    }
  }

  async searchContracts(req, res, next) {
    try {
      const { q } = req.query;
      const contracts = await indentpoService.searchContracts(req.dbPool, q);
      res.json(contracts);
    } catch (err) {
      next(err);
    }
  }

  async getContractProducts(req, res, next) {
    try {
      const { contract_id } = req.params;
      const products = await indentpoService.getContractProducts(req.dbPool, contract_id);
      res.json(products);
    } catch (err) {
      next(err);
    }
  }

  async searchMachines(req, res, next) {
    try {
      const { q } = req.query;
      const machines = await indentpoService.searchMachines(req.dbPool, q);
      res.json(machines);
    } catch (err) {
      next(err);
    }
  }

  async getDesignSheetDetails(req, res, next) {
    try {
      const { contract_id, item_id } = req.query;
      const details = await indentpoService.getDesignSheetDetails(req.dbPool, contract_id, item_id);
      res.json(details);
    } catch (err) {
      next(err);
    }
  }

  async saveIndentpo(req, res, next) {
    try {
      const data = req.body;
      const userId = req.user.id;
      const result = await indentpoService.saveIndentpo(req.dbPool, req.body, userId);
      res.status(201).json({ message: 'Indent PO saved successfully', id: result });
    } catch (err) {
      next(err);
    }
  }

  async getIndentPoDetails(req, res, next) {
    try {
      const { id } = req.params;
      
      const [headerRes] = await req.dbPool.query(`
        SELECT 
          i.*,
          c.title as contract_name, c.workorder,
          p.item_name as product_name,
          m.machine_name
        FROM indentpo i
        LEFT JOIN contracts c ON i.contract_id = c.id
        LEFT JOIN st_additem p ON i.finishedproduct_id = p.id
        LEFT JOIN machine_master m ON i.machine_id = m.id
        WHERE i.id = ?
      `, [id]);
      
      console.log("Fetching IndentPO Details for ID:", id);
      console.log("Query Result Length:", headerRes.length);
      console.log("Query Result:", headerRes);
      
      if (headerRes.length === 0) {
        return res.status(404).json({ message: 'Indent PO not found' });
      }
      
      const [itemsRes] = await req.dbPool.query(`
        SELECT 
          s.item_id, s.quantity as issue_qty,
          a.item_name,
          u.unit_name as uom
        FROM st_stock_register s
        LEFT JOIN st_additem a ON s.item_id = a.id
        LEFT JOIN st_measurementunits u ON a.uom = u.id
        WHERE s.indent_id = ? AND s.store_type = '2'
      `, [headerRes[0].indent_id]);
      
      res.json({
        header: headerRes[0],
        items: itemsRes
      });
    } catch (err) {
      next(err);
    }
  }

  async listIndentpo(req, res, next) {
    try {
      const filters = req.query;
      const result = await indentpoService.listIndentpo(req.dbPool, filters);
      res.json(result);
    } catch (err) {
      next(err);
    }
  }

  async getIndentpoDetail(req, res, next) {
    try {
      const { indent_id } = req.params;
      const detail = await indentpoService.getIndentpoDetail(req.dbPool, indent_id);
      if (!detail) {
        return res.status(404).json({ error: 'IndentPO not found' });
      }
      res.json(detail);
    } catch (err) {
      next(err);
    }
  }
}

module.exports = new IndentpoController();
