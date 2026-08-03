const paymentRepository = require('./payment.repository');

class PaymentService {
  async getPaymentsList(dbPool, filters) {
    return await paymentRepository.findFiltered(dbPool, filters);
  }

  async getPaymentDetail(dbPool, id) {
    return await paymentRepository.findById(dbPool, id);
  }

  async getParticularPayments(dbPool, filters) {
    return await paymentRepository.findParticularPayments(dbPool, filters);
  }

  async getVendors(dbPool) {
    return await paymentRepository.getVendors(dbPool);
  }
}

module.exports = new PaymentService();
