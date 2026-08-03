const indentpoRepository = require('./indentpo.repository');

class IndentpoService {
  async getNextIndentId(dbPool) {
    return indentpoRepository.getNextIndentId(dbPool);
  }

  async searchContracts(dbPool, query) {
    if (!query) return [];
    return indentpoRepository.searchContracts(dbPool, query.trim());
  }

  async getContractProducts(dbPool, contractId) {
    if (!contractId) return [];
    return indentpoRepository.getContractProducts(dbPool, contractId);
  }

  async searchMachines(dbPool, query) {
    if (!query) return [];
    return indentpoRepository.searchMachines(dbPool, query.trim());
  }

  async getDesignSheetDetails(dbPool, contractId, itemId) {
    if (!contractId || !itemId) return [];
    return indentpoRepository.getDesignSheetDetails(dbPool, contractId, itemId);
  }

  async saveIndentpo(dbPool, data, userId) {
    // Validate Header
    if (!data.indent_id || !data.contract_id || !data.finishedproduct_id || !data.machine_id) {
      throw new Error('Missing required indentpo fields.');
    }
    // Validate Details
    if (!data.items || !Array.isArray(data.items) || data.items.length === 0) {
      throw new Error('At least one item with issue quantity is required.');
    }

    // Verify stock rules again before saving
    const designSheet = await this.getDesignSheetDetails(dbPool, data.contract_id, data.finishedproduct_id);
    const designMap = new Map();
    designSheet.forEach(ds => {
      designMap.set(Number(ds.item_id), ds);
      if (Number(ds.is_group) === 1 && ds.group_items) {
        ds.group_items.forEach(child => {
          designMap.set(Number(child.id), {
            ...ds,
            item_id: child.id,
            item_name: child.item_name,
            inhand_stock: child.inhand_stock
          });
        });
      }
    });

    for (const item of data.items) {
      const issueQty = Number(item.issue_qty);
      if (issueQty <= 0) continue;

      const dsInfo = designMap.get(Number(item.item_id));
      if (!dsInfo) {
        throw new Error(`Item ID ${item.item_id} is not in the design sheet.`);
      }

      if (issueQty > dsInfo.pending_qty) {
        throw new Error(`Issue quantity (${issueQty}) exceeds pending quantity (${dsInfo.pending_qty}) for item ${dsInfo.item_name}.`);
      }
      if (issueQty > dsInfo.inhand_stock) {
        throw new Error(`Issue quantity (${issueQty}) exceeds in-hand stock (${dsInfo.inhand_stock}) for item ${dsInfo.item_name}.`);
      }
    }

    return indentpoRepository.saveIndentpo(dbPool, data, userId);
  }

  async listIndentpo(dbPool, filters) {
    return indentpoRepository.listIndentpo(dbPool, filters);
  }

  async getIndentpoDetail(dbPool, indentId) {
    const detail = await indentpoRepository.getIndentpoDetail(dbPool, indentId);
    if (!detail) {
      throw new Error('Indentpo not found');
    }

    // Include the design sheet info in the items for the view
    const designSheet = await this.getDesignSheetDetails(dbPool, detail.contract_id, detail.finishedproduct_id);
    const designMap = new Map();
    designSheet.forEach(ds => designMap.set(Number(ds.item_id), ds));

    const enrichedItems = detail.items.map(item => {
      const dsInfo = designMap.get(Number(item.item_id));
      return {
        ...item,
        design_qty: dsInfo ? dsInfo.design_qty : 0,
        pending_qty: dsInfo ? dsInfo.pending_qty : 0 // pending AFTER this was issued
      };
    });

    return { ...detail, items: enrichedItems };
  }
}

module.exports = new IndentpoService();
