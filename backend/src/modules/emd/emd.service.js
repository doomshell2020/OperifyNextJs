const emdRepository = require('./emd.repository');

class EmdService {
  async getEmdList(dbPool, filters) {
    const records = await emdRepository.findFiltered(dbPool, filters);
    return records.map(r => ({
      ...r,
      amount: parseFloat(r.amount) || 0,
    }));
  }

  async getEmdDetail(dbPool, id) {
    const record = await emdRepository.findById(dbPool, id);
    if (!record) return null;

    const amounts = await emdRepository.findAmounts(dbPool, id);
    const remarks = await emdRepository.findRemarks(dbPool, id);
    const totalReceived = await emdRepository.getTotalReceived(dbPool, id);
    const remainingAmount = parseFloat(record.amount) - totalReceived;

    return {
      ...record,
      amount: parseFloat(record.amount) || 0,
      totalReceived,
      remainingAmount,
      amounts,
      remarks,
    };
  }
}

module.exports = new EmdService();
