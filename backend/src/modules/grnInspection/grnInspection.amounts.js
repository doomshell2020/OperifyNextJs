// Purchaseorder/purchaseorderitems.ctp: infer included tax from the saved PO tax.
module.exports = function amounts(order, quantity, taxRate) {
  const rate = Number(Number(order.item_amt).toFixed(2));
  const cost = rate * quantity;
  const excludedTax = Number((rate * Number(order.item_qty) * taxRate / 100).toFixed(2));
  const included = excludedTax !== Number(order.item_tax_amt ?? 0);
  const tax = included ? cost - cost * (100 / (100 + taxRate)) : cost * taxRate / 100;
  return {
    rate,
    cost_price: Number(cost.toFixed(2)),
    tax: Number(tax.toFixed(2)),
    amount: Number((included ? cost : cost + tax).toFixed(2))
  };
};
