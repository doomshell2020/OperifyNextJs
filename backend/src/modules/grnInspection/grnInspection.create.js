const V = require('../../utils/receiptValidation');
const amounts = require('./grnInspection.amounts');

module.exports = async function create(db, inspection = {}, inputItems = []) {
  if (!inspection.po_id || !Array.isArray(inputItems) || !inputItems.length) throw V.invalid('Purchase Order and inspection items are required.');
  V.date(inspection.inwarddate, 'Inward date');
  V.date(inspection.bill_date, 'Bill date');
  if (!String(inspection.bill_no || '').trim() || !String(inspection.remark || '').trim()) throw V.invalid('Bill number and remarks are required.');
  return db.transaction(async transaction => {
    const po = await V.purchaseOrder(db, inspection.po_id, transaction);
    if (inspection.vendor_id && Number(inspection.vendor_id) !== Number(po.vendor_id)) throw V.invalid('Vendor does not match the Purchase Order.');
    // Serialize number generation across POs on the same tenant and connection.
    const lockName = `inspection:${db.getDatabaseName()}`;
    const [lock] = await V.select(db, 'SELECT GET_LOCK(:lockName,10) AS acquired', { lockName }, transaction);
    if (Number(lock.acquired) !== 1) throw V.invalid('Inspection numbering is busy; retry.', 409);
    try {
      const [last] = await V.select(db, 'SELECT inspection_id FROM grn_inspection ORDER BY id DESC LIMIT 1 FOR UPDATE', {}, transaction);
      const inspectionId = String(last ? Number(last.inspection_id) + 1 : 1001);
      if (!Number.isFinite(Number(inspectionId))) throw V.invalid('The last inspection number is invalid.', 409);
      const duplicates = await V.select(db, 'SELECT id FROM grn_inspection WHERE inspection_id=:inspectionId', { inspectionId }, transaction);
      if (duplicates.length) throw V.invalid('Inspection number already exists.', 409);
      const orders = await V.select(db, 'SELECT * FROM st_purchaseorderDetails WHERE poprimary_id=:id AND purchaseorder_id=:poNumber ORDER BY id ASC', { id: po.id, poNumber: po.purchaseorder_id }, transaction);
      const taxes = await V.select(db, 'SELECT id,tax FROM st_taxmaster', {}, transaction);
      const prior = await V.select(db, "SELECT item_id,SUM(quantity) AS quantity FROM grn_inspection_details WHERE purchaseorder_id=:poNumber AND status!='N' GROUP BY item_id", { poNumber: po.purchaseorder_id }, transaction);
      const receipts = await V.select(db, "SELECT item_id,ROUND(SUM(quantity),2) AS quantity FROM st_stock_register WHERE po_id=:poNumber AND store_type='1' AND status!='N' GROUP BY item_id", { poNumber: po.purchaseorder_id }, transaction);
      const budgets = new Map();
      for (const order of orders) budgets.set(String(order.item_id), (budgets.get(String(order.item_id)) || 0) + Number(order.item_qty));
      const ordered = new Map(budgets);
      for (const row of prior) budgets.set(String(row.item_id), (budgets.get(String(row.item_id)) || 0) - Number(row.quantity));
      // Retain the existing inspection reservation guard and also enforce PHP's
      // stock-ledger maximum. Do not subtract received inspections twice.
      for (const row of receipts) {
        const key = String(row.item_id);
        budgets.set(key, Math.min(budgets.get(key) || 0, (ordered.get(key) || 0) - Number(row.quantity)));
      }
      const details = [], seen = new Set();
      for (const input of inputItems) {
        const quantity = V.number(input.quantity, 'Inspection quantity');
        if (!quantity) continue;
        const key = String(input.item_id);
        if (seen.has(key)) throw V.invalid('This item has already been added.');
        seen.add(key);
        const order = orders.find(row => String(row.item_id) === key);
        if (!order || quantity - (budgets.get(key) || 0) > 0.0001) throw V.invalid('Inspection quantity exceeds the pending PO quantity.', 409);
        V.number(order.item_qty, 'Order quantity', true);
        V.number(order.item_amt, 'PO rate');
        V.number(order.item_tax_amt ?? 0, 'PO tax');
        const taxRate = V.number(taxes.find(row => String(row.id) === String(order.tax_id))?.tax ?? 0, 'PO tax rate');
        const valuation = amounts(order, quantity, taxRate);
        details.push({ item_id: order.item_id, quantity, ...valuation, tax_id: order.tax_id ?? null, delivery_schedule_id: input.delivery_schedule_id || null });
      }
      if (!details.length) throw V.invalid('At least one received quantity must be positive.');
      const header = { ...inspection, inspectionId, vendor: po.vendor_id,
        totalQty: details.reduce((sum, item) => sum + item.quantity, 0),
        totalTax: V.round(details.reduce((sum, item) => sum + item.tax, 0)),
        totalAmt: V.round(details.reduce((sum, item) => sum + item.amount, 0)) };
      const id = await V.insert(db, `INSERT INTO grn_inspection (po_id,inspection_id,vendor_id,inwarddate,bill_no,bill_date,total_qty,total_tax,total_amt,remark,status)
        VALUES (:po_id,:inspectionId,:vendor,:inwarddate,:bill_no,:bill_date,:totalQty,:totalTax,:totalAmt,:remark,'Y')`, header, transaction);
      for (const item of details) await V.insert(db, `INSERT INTO grn_inspection_details
        (purchaseorder_id,inspection_id,vendor_id,item_id,quantity,rate,cost_price,tax_id,tax,amount,issue_date,delivery_date,delivery_schedule_id,status)
        VALUES (:po_id,:inspectionId,:vendor,:item_id,:quantity,:rate,:cost_price,:tax_id,:tax,:amount,:inwarddate,:inwarddate,:delivery_schedule_id,'Y')`, { ...header, ...item }, transaction);
      // Release after inserts, before COMMIT: number reads lock the last row, so
      // a second writer waits for the commit before it can read the next number.
      await V.select(db, 'SELECT RELEASE_LOCK(:lockName)', { lockName }, transaction);
      return { data: { id, inspection_id: inspectionId } };
    } catch (error) {
      await V.select(db, 'SELECT RELEASE_LOCK(:lockName)', { lockName }, transaction);
      throw error;
    }
  });
};
