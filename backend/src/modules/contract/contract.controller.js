const contractService = require('./contract.service');
const {paginationInput,paginationResult} = require('../../utils/listPagination');

class ContractController {
  async getContracts(req, res, next) {
    try {
      const dbPool = req.dbPool;
      const {
        contract_name,
        vendor_name,
        cost,
        datefrom,
        dateto,
      } = req.query;

      const {page,limit,offset} = paginationInput(req.query,'contract');

      const filters = {
        contract_id:req.query.contract_id,
        vendor_id:req.query.vendor_id,
        sort:req.query.sort,
        direction:req.query.direction,
        contract_name: contract_name ? String(contract_name).trim() : undefined,
        vendor_name: vendor_name ? String(vendor_name).trim() : undefined,
        cost: cost || undefined,
        datefrom: datefrom || undefined,
        dateto: dateto || undefined,
        limit,
        offset
      };

      const result = await contractService.getContractsList(dbPool, filters);

      return res.status(200).json({
        success: true,
        data: result.rows,
        ...paginationResult(result.count,page,limit,result.rows.length)
      });
    } catch (err) {
      next(err);
    }
  }

  async getDetails(req, res, next) {
    try {
      const dbPool = req.dbPool;
      const { id } = req.params;

      const contractId = parseInt(id, 10);
      if (isNaN(contractId)) {
        return res.status(400).json({
          success: false,
          message: 'Invalid contract ID provided.'
        });
      }

      const details = await contractService.getContractDetails(dbPool, contractId);
      if (!details) {
        return res.status(404).json({
          success: false,
          message: 'Contract not found.'
        });
      }

      return res.status(200).json({
        success: true,
        data: details
      });
    } catch (err) {
      next(err);
    }
  }

  async getFormData(req, res, next) {
    try {
      const dbPool = req.dbPool;
      const data = await contractService.getFormData(dbPool);
      return res.status(200).json({
        success: true,
        data
      });
    } catch (err) {
      next(err);
    }
  }

  async createContract(req, res, next) {
    try {
      const dbPool = req.dbPool;
      const contractData = req.body;
      
      const contractId = await contractService.createContract(dbPool, contractData);
      
      return res.status(201).json({
        success: true,
        message: 'Contract created successfully',
        data: { id: contractId }
      });
    } catch (err) {
      if (err.statusCode) {
        return res.status(err.statusCode).json({ success: false, message: err.message });
      }
      next(err);
    }
  }

  async updateContract(req, res, next) {
    try {
      const dbPool = req.dbPool;
      const { id } = req.params;
      const contractId = parseInt(id, 10);
      if (isNaN(contractId)) {
        return res.status(400).json({
          success: false,
          message: 'Invalid contract ID provided.'
        });
      }

      await contractService.updateContract(dbPool, contractId, req.body);

      return res.status(200).json({
        success: true,
        message: 'Contract successfully updated.'
      });
    } catch (err) {
      if (err.statusCode) {
        return res.status(err.statusCode).json({ success: false, message: err.message });
      }
      next(err);
    }
  }

  async deleteContract(req, res, next) {
    try {
      const dbPool = req.dbPool;
      const { id } = req.params;
      const contractId = parseInt(id, 10);
      if (isNaN(contractId)) {
        return res.status(400).json({
          success: false,
          message: 'Invalid contract ID provided.'
        });
      }

      await contractService.deleteContract(dbPool, contractId);

      return res.status(200).json({
        success: true,
        message: 'Contract deleted successfully'
      });
    } catch (err) {
      if (err.statusCode) {
        return res.status(err.statusCode).json({ success: false, message: err.message });
      }
      next(err);
    }
  }
  async exportPDF(req, res, next) {
    try {
      const dbPool = req.dbPool;
      const { id } = req.params;

      const contractId = parseInt(id, 10);
      if (isNaN(contractId)) {
        return res.status(400).json({
          success: false,
          message: 'Invalid contract ID provided.'
        });
      }

      const details = await contractService.getContractDetails(dbPool, contractId);
      if (!details || !details.contract) {
        return res.status(404).json({
          success: false,
          message: 'Contract not found.'
        });
      }
      
      const { generateContractPDF } = require('./contract.pdf');
      const tenantDb = req.user?.db || 'default';
      const pdfBuffer = await generateContractPDF(details, tenantDb);
      
      res.setHeader('Content-Type', 'application/pdf');
      res.setHeader('Content-Disposition', `inline; filename="contract-${id}.pdf"`);
      res.end(pdfBuffer, 'binary');
    } catch (err) {
      next(err);
    }
  }
}

module.exports = new ContractController();
