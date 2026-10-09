// Matches the PHP purchaseorderitems.ctp quantity-change calculation.
export function inspectionAmounts(item: { rate: number; order_qty: number; tax_rate: number; order_tax: number; received_qty: number }) {
  const rate = Number(Number(item.rate).toFixed(2));
  const cost = rate * (Number(item.received_qty) || 0);
  const excludedTax = Number((rate * item.order_qty * item.tax_rate / 100).toFixed(2));
  const included = excludedTax !== Number(item.order_tax);
  const tax = included ? cost - cost * (100 / (100 + item.tax_rate)) : cost * item.tax_rate / 100;
  return {
    cost_price: Number(cost.toFixed(2)),
    tax: Number(tax.toFixed(2)),
    amount: Number((included ? cost : cost + tax).toFixed(2))
  };
}
