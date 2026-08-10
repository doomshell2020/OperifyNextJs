const grnInspectionRepository = require('./grnInspection.repository');

class GrnInspectionService {
  async listInspections(dbPool, filters, page, limit) {
    const offset = (page - 1) * limit;
    const { data, total } = await grnInspectionRepository.list(dbPool, filters, limit, offset);
    return {
      data,
      pagination: {
        total,
        page,
        limit,
        totalPages: Math.ceil(total / limit)
      }
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
