const indentRepository = require('./indent.repository');

class IndentService {
  async getNextIndentId(dbPool) {
    return indentRepository.getNextIndentId(dbPool);
  }

  async searchItems(dbPool, query) {
    if (!query || query.trim().length === 0) return [];
    return indentRepository.searchItems(dbPool, query.trim());
  }

  async addTempItem(dbPool, data, userId) {
    const { indent_id, item_id, size_id, quantity } = data;
    if (!indent_id || !item_id || !quantity || quantity <= 0) {
      throw new Error('indent_id, item_id, and quantity are required');
    }

    // Get item price for amount calculation
    const [itemRows] = await dbPool.execute(
      'SELECT cost_price FROM st_additem WHERE id = ?',
      [item_id]
    );
    const costPrice = itemRows[0]?.cost_price || 0;
    const amount = costPrice * quantity;

    return indentRepository.addTempItem(dbPool, {
      indent_id,
      item_id,
      size_id: size_id || null,
      quantity,
      sale_price: costPrice,
      amount,
      added_by: userId
    });
  }

  async removeTempItem(dbPool, id) {
    const deleted = await indentRepository.removeTempItem(dbPool, id);
    if (!deleted) throw new Error('Temp item not found');
    return true;
  }

  async getTempItems(dbPool, indent_id) {
    return indentRepository.getTempItems(dbPool, indent_id);
  }

  async finalizeIndent(dbPool, indent_id, userId) {
    if (!indent_id) throw new Error('indent_id is required');
    return indentRepository.finalizeIndent(dbPool, indent_id, userId);
  }

  async listIndents(dbPool, filters) {
    return indentRepository.listIndents(dbPool, filters);
  }

  async getIndentDetail(dbPool, indent_id) {
    const detail = await indentRepository.getIndentDetail(dbPool, indent_id);
    if (!detail || detail.items.length === 0) {
      throw new Error('Indent not found');
    }
    return detail;
  }

  async getPendingIndents(dbPool) {
    return indentRepository.getPendingIndents(dbPool);
  }
}

module.exports = new IndentService();
