const grnInspectionRepository = require('./grnInspection.repository');
const {paginationInput,paginationResult} = require('../../utils/listPagination');

class GrnInspectionService {
  async listInspections(dbPool, filters, page, limit) {
    ({page,limit} = paginationInput({page,limit},'grnInspection'));
    const offset = (page - 1) * limit;
    const { data, total } = await grnInspectionRepository.list(dbPool, filters, limit, offset);
    return {
      data,
      pagination: paginationResult(total,page,limit,data.length)
    };
  }

  async getDetails(dbPool, id) {
    const inspection = await grnInspectionRepository.findById(dbPool, id);
    if (!inspection) {
      throw new Error('Inspection not found');
    }
    const items = await grnInspectionRepository.getItemsByInspectionId(dbPool, inspection.inspection_id);
    return { ...inspection, items };
  }

  async createInspection(dbPool, inspection, items) {
    return await grnInspectionRepository.create(dbPool, inspection, items);
  }

  async getNextInspectionNumber(dbPool) {
    const nextId = await grnInspectionRepository.getNextId(dbPool);
    return nextId;
  }
  
  async getPoDetails(dbPool, po_id) {
    return await grnInspectionRepository.getPoDetails(dbPool, po_id);
  }
}

module.exports = new GrnInspectionService();
