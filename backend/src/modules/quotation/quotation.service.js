const quotationRepository = require('./quotation.repository');

class QuotationService {
  async getQuotationsList(dbPool, filters) {
    const records = await quotationRepository.findFiltered(dbPool, filters);
    return records.map(r => ({
      ...r,
      total_amt: parseFloat(r.total_amt) || 0,
      total_tax: parseFloat(r.total_tax) || 0,
    }));
  }

  async getQuotationDetail(dbPool, id) {
    const record = await quotationRepository.findById(dbPool, id);
    if (!record) return null;

    const details = await quotationRepository.findDetails(dbPool, id);
    return {
      ...record,
      total_amt: parseFloat(record.total_amt) || 0,
      total_tax: parseFloat(record.total_tax) || 0,
      details,
    };
  }

  async getVendors(dbPool) {
    return await quotationRepository.getVendors(dbPool);
  }
}

module.exports = new QuotationService();
