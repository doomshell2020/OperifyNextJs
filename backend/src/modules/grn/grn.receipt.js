const V = require('../../utils/receiptValidation');

module.exports = async function receive(db, payload = {}) {
  if (!payload.inspection_id) throw V.invalid('Inspection is required.');
  if (!String(payload.remark || '').trim()) throw V.invalid('Remarks are required.');
  return db.transaction(async transaction => {
    // Read the PO number before taking locks; re-read the inspection under lock.
    const [source] = await V.select(db, 'SELECT po_id FROM grn_inspection WHERE inspection_id=:inspectionId LIMIT 1', { inspectionId: payload.inspection_id }, transaction);
    if (!source) throw V.invalid('Inspection not found.', 404);
    const po = await V.purchaseOrder(db, source.po_id, transaction);
    const [inspection] = await V.select(db, 'SELECT * FROM grn_inspection WHERE inspection_id=:inspectionId FOR UPDATE', { inspectionId: payload.inspection_id }, transaction);
    if (inspection.status !== 'Y') throw V.invalid('Inspection has already been received.', 409);
    if (String(inspection.po_id) !== String(source.po_id) || String(po.vendor_id) !== String(inspection.vendor_id)) throw V.invalid('Inspection does not match the Purchase Order.', 409);
    if (payload.purchaseorder_id && String(payload.purchaseorder_id) !== String(inspection.po_id)) throw V.invalid('Purchase Order does not match the inspection.');
    if (payload.vendor_id && String(payload.vendor_id) !== String(inspection.vendor_id)) throw V.invalid('Vendor does not match the inspection.');

    // PHP's GRN rows are read-only inspection quantities and saved valuations.
    // Never recalculate an inspection's included tax or accept client stock rows.
    const items = await V.select(db, "SELECT * FROM grn_inspection_details WHERE inspection_id=:inspectionId AND status!='N' ORDER BY id ASC FOR UPDATE", { inspectionId: inspection.inspection_id }, transaction);
    if (!items.length) throw V.invalid('No items received in this inspection.');
    const ordered = await V.select(db, 'SELECT item_id,SUM(item_qty) AS quantity FROM st_purchaseorderdetails WHERE poprimary_id=:id GROUP BY item_id', { id: po.id }, transaction);
    const received = await V.select(db, "SELECT item_id,SUM(quantity) AS quantity FROM st_stock_register WHERE po_id=:poNumber AND store_type='1' AND status!='N' GROUP BY item_id", { poNumber: inspection.po_id }, transaction);
    const budgets = new Map(ordered.map(row => [String(row.item_id), Number(row.quantity)]));
    for (const row of received) budgets.set(String(row.item_id), (budgets.get(String(row.item_id)) || 0) - Number(row.quantity));
    let totalQty = 0, totalTax = 0, totalAmt = 0;
    for (const item of items) {
      const key = String(item.item_id), qty = V.number(item.quantity, 'Inspection quantity', true);
      if (!budgets.has(key) || qty - budgets.get(key) > 0.0001) throw V.invalid('Inspection quantity exceeds the remaining PO quantity.', 409);
      budgets.set(key, budgets.get(key) - qty);
      V.number(item.rate, 'Inspection rate');
      V.number(item.cost_price, 'Inspection base amount');
      totalQty += qty;
      totalTax += V.number(item.tax ?? 0, 'Inspection tax');
      totalAmt += V.number(item.amount, 'Inspection amount');
    }
    const values = {
      poNumber: inspection.po_id, vendor: inspection.vendor_id,
      inwarddate: V.storedDate(inspection.inwarddate, 'Inward date'),
      bill_date: V.storedDate(inspection.bill_date, 'Bill date'),
      bill_no: inspection.bill_no || '', remark: String(payload.remark).trim(),
      totalQty, totalTax: V.round(totalTax), totalAmt: V.round(totalAmt)
    };
    const goodsId = await V.insert(db, `INSERT INTO st_goodsreceive
      (purchaseorder_id,vendor_id,inwarddate,bill_date,bill_no,remark,freight,total_qty,total_tax,total_amt,status)
      VALUES (:poNumber,:vendor,:inwarddate,:bill_date,:bill_no,:remark,0,:totalQty,:totalTax,:totalAmt,'O')`, values, transaction);
    await V.insert(db, `INSERT INTO payments (vendor_id,inwarddate,bill_date,bill_no,total_amt,remark,store_type,goods_id)
      VALUES (:vendor,:inwarddate,:bill_date,:bill_no,:totalAmt,:remark,1,:goodsId)`, { ...values, goodsId }, transaction);
    for (const item of items) {
      await V.insert(db, `INSERT INTO st_stock_register
        (purchaseorder_id,po_id,goods_id,vendor_id,item_id,quantity,rate,cost_price,tax_id,tax,amount,issue_date,delivery_date,delivery_schedule_id,store_type,status)
        VALUES (:poId,:poNumber,:goodsId,:vendor,:itemId,:quantity,:rate,:base,:taxId,:tax,:amount,:inwarddate,:inwarddate,:schedule,'1','Y')`, {
        ...values, goodsId, poId: po.id, itemId: item.item_id, quantity: item.quantity,
        rate: item.rate, base: item.cost_price, taxId: item.tax_id ?? null,
        tax: item.tax ?? 0, amount: item.amount, schedule: item.delivery_schedule_id ?? null
      }, transaction);
      const stocks = await V.select(db, 'SELECT id FROM st_stock_available WHERE item_id=:itemId FOR UPDATE', { itemId: item.item_id }, transaction);
      if (stocks.length > 1) throw V.invalid('Multiple stock availability rows exist for this product; resolve the data before receiving.', 409);
      if (stocks.length) await V.write(db, 'UPDATE st_stock_available SET stock_available=COALESCE(stock_available,0)+:quantity WHERE id=:id', { id: stocks[0].id, quantity: item.quantity }, transaction);
      // The legacy cache is optional; the stock ledger remains authoritative.
      if (item.delivery_schedule_id) await V.write(db, "UPDATE po_delivery_note SET status='N' WHERE id=:id AND po_id=:poNumber", { id: item.delivery_schedule_id, poNumber: inspection.po_id }, transaction);
    }
    const previousQty = received.reduce((sum, row) => sum + Number(row.quantity), 0);
    const poStatus = V.round(previousQty + totalQty) >= V.round(po.total_qty) ? 'C' : 'O';
    await V.write(db, 'UPDATE st_purchaseorder SET postatus=:poStatus WHERE purchaseorder_id=:poNumber', { poStatus, poNumber: inspection.po_id }, transaction);
    await V.write(db, 'UPDATE st_goodsreceive SET status=:poStatus WHERE purchaseorder_id=:poNumber', { poStatus, poNumber: inspection.po_id }, transaction);
    await V.write(db, "UPDATE grn_inspection SET status='N' WHERE id=:id", { id: inspection.id }, transaction);
    return { goods_id: goodsId, poStatus };
  });
};
