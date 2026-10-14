'use client';
import { formatQty } from '@/utils/formatters';
import React from 'react';

export const receivingMoney = (value: unknown) => Number(value || 0).toLocaleString('en-IN', {
  minimumFractionDigits: Number(value) % 1 ? 2 : 0, maximumFractionDigits: 2,
});

export function ReceivingDetailsTable({items, purchaseOrder = false}: {items: any[]; purchaseOrder?: boolean}) {
  const last = items.at(-1);
  const total = items.reduce((sum, item) => sum + Number(item.amount || 0), 0);
  const status = last ? (Number(last.price ?? last.cost_price) === Number(last.amount) ? 'Tax Included' : 'Tax Excluded') : '';
  return <div className="overflow-x-auto p-2"><table className="w-full border-collapse">
    <thead><tr>{['S.No.', 'Item', 'Order Qty.', purchaseOrder ? 'Pending Qty.' : 'Received Qty.', 'Rate', 'Price (INR)', 'Tax Rate(%)', 'Tax Amt', 'Amount'].map(label => <th key={label}>{label}</th>)}</tr></thead>
    <tbody>{items.map((item, index) => {
      const taxes: unknown[] = item.tax_rates || (item.tax_percentage == null ? [] : [item.tax_percentage]);
      const taxAmount = Number(item.tax_amt ?? item.tax ?? 0);
      return <tr key={item.id ?? index}>
        <td>{index + 1}.</td><td>{item.item_name}</td>
        <td>{item.order_qty == null ? '' : formatQty(item.order_qty)} {item.uom}</td>
        <td>{formatQty(purchaseOrder ? item.pending_qty : (item.quantity ?? item.item_qty))} {item.uom}</td>
        <td className="text-right">{receivingMoney(item.rate)}</td>
        <td className="text-right">{receivingMoney(item.price ?? item.cost_price)}</td>
        <td>{taxes.map((tax,index)=><div key={index}>{String(tax)}</div>)}</td>
        <td className="text-right">{(taxes.length === 2 ? [taxAmount / 2, taxAmount / 2] : [taxAmount]).map((tax,index)=><div key={index}>{receivingMoney(tax)}</div>)}</td>
        <td className="text-right">{receivingMoney(item.amount)}</td>
      </tr>;
    })}<tr><td colSpan={6} className="text-right"><b>{status}</b></td><td colSpan={3} className="text-right"><b>Total Amount :-</b> {receivingMoney(total)}</td></tr></tbody>
  </table></div>;
}
