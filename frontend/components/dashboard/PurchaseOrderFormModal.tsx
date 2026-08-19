"use client";

import React, { useState, useEffect } from 'react';
import { useMutation, useQueryClient, useQuery } from '@tanstack/react-query';
import purchaseOrderService, { PurchaseOrderDetailsData, PurchaseOrderItem } from '../../services/purchaseOrder.service';
import { Loader, X, Save, AlertCircle, Plus, Trash2 } from 'lucide-react';
import { formatQty, formatAmt } from '@/utils/formatters';
import { DatePicker } from '../ui/DatePicker';

interface PurchaseOrderFormModalProps {
  poId: number;
  onClose: () => void;
}

export function PurchaseOrderFormModal({ poId, onClose }: PurchaseOrderFormModalProps) {
  const queryClient = useQueryClient();
  const [formData, setFormData] = useState<any>({});
  const [items, setItems] = useState<Partial<PurchaseOrderItem>[]>([]);

  const { data, isLoading, isError } = useQuery({
    queryKey: ['purchase-order-details', poId],
    queryFn: () => purchaseOrderService.getDetails(poId),
    enabled: !!poId,
  });

  useEffect(() => {
    if (data) {
      setFormData(data.po);
      setItems(data.items);
    }
  }, [data]);

  const updateMutation = useMutation({
    mutationFn: (payload: any) => purchaseOrderService.revisePurchaseOrder(poId, payload),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['purchase-orders'] });
      queryClient.invalidateQueries({ queryKey: ['purchase-order-details', poId] });
      onClose();
    }
  });

  const handlePoChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
    setFormData((prev: any) => ({ ...prev, [e.target.name]: e.target.value }));
  };

  const handleItemChange = (index: number, field: string, value: any) => {
    const newItems = [...items];
    (newItems[index] as any)[field] = value;
    
    // Auto-calculate amount
    if (field === 'order_qty' || field === 'rate' || field === 'tax_percentage') {
      const qty = parseFloat(newItems[index].order_qty as any) || 0;
      const rate = parseFloat(newItems[index].rate as any) || 0;
      const tax_p = parseFloat(newItems[index].tax_percentage as any) || 0;
      
      const price = qty * rate;
      const tax = price * (tax_p / 100);
      newItems[index].price = price;
      newItems[index].tax_amt = tax;
      newItems[index].amount = price + tax;
    }
    
    setItems(newItems);
  };

  const calculateTotal = () => {
    let total_qty = 0;
    let total_amt = 0;
    items.forEach(item => {
      total_qty += parseFloat(item.order_qty as any) || 0;
      total_amt += parseFloat(item.amount as any) || 0;
    });
    return { total_qty, total_amt };
  };

  const handleSave = () => {
    const totals = calculateTotal();
    const payload = {
      po: {
        ...formData,
        total_qty: totals.total_qty,
        total_amt: totals.total_amt
      },
      items: items
    };
    updateMutation.mutate(payload);
  };

  if (!poId) return null;

  return (
    <div className="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm animate-in fade-in duration-200">
      <div className="bg-white border border-slate-200 shadow-2xl rounded-2xl max-w-4xl w-full p-6 flex flex-col relative overflow-hidden animate-in zoom-in-95 duration-200 max-h-[90vh]">
        
        <div className="absolute top-4 right-4 z-10 flex gap-2">
          <button
            onClick={handleSave}
            disabled={updateMutation.isPending || isLoading}
            className="flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded font-bold shadow-sm transition cursor-pointer text-sm disabled:opacity-50"
          >
            {updateMutation.isPending ? <Loader className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
            Revise PO
          </button>
          <button
            onClick={onClose}
            className="p-2 text-slate-400 hover:text-slate-700 bg-white border border-slate-200 rounded transition cursor-pointer shadow-sm"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        <h2 className="text-xl font-bold text-slate-800 mb-6">
          Revise Purchase Order
        </h2>

        {isLoading && (
          <div className="flex flex-col items-center justify-center py-20 text-slate-500">
            <Loader className="w-8 h-8 animate-spin mb-4 text-blue-500" />
            <p>Loading PO data...</p>
          </div>
        )}

        {isError && (
          <div className="flex flex-col items-center justify-center py-20 text-red-500 bg-red-50 rounded-xl">
            <AlertCircle className="w-10 h-10 mb-2" />
            <p className="font-semibold">Failed to load PO</p>
          </div>
        )}

        {!isLoading && !isError && formData && (
          <div className="flex-1 overflow-y-auto pr-2 custom-scrollbar">
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
              <div>
                <label className="block text-xs font-semibold text-slate-500 uppercase mb-1">PO Number</label>
                <input type="text" value={formData.po_number || ''} disabled className="w-full border p-2 rounded bg-slate-50 text-slate-500 cursor-not-allowed" />
              </div>
              <div>
                <label className="block text-xs font-semibold text-slate-500 uppercase mb-1">PO Date</label>
                <DatePicker value={formData.po_date?.split('T')[0] || ''} disabled className="w-full border p-2 rounded bg-slate-50 text-slate-500 cursor-not-allowed" />
              </div>
              <div>
                <label className="block text-xs font-semibold text-slate-500 uppercase mb-1">Vendor</label>
                <input type="text" value={formData.vendor_name || ''} disabled className="w-full border p-2 rounded bg-slate-50 text-slate-500 cursor-not-allowed" />
              </div>
              <div>
                <label className="block text-xs font-semibold text-slate-500 uppercase mb-1">Delivery Date</label>
                <DatePicker name="delivery_date" value={formData.delivery_date?.split('T')[0] || ''} onChange={handlePoChange} className="w-full border p-2 rounded" />
              </div>
            </div>

            <div className="mb-6">
              <label className="block text-xs font-semibold text-slate-500 uppercase mb-1">Remarks</label>
              <textarea name="remarks" value={formData.remarks || ''} onChange={handlePoChange} className="w-full border p-2 rounded" rows={2} />
            </div>

            <h3 className="text-sm font-bold text-slate-800 mb-2">Items</h3>
            <div className="border border-slate-200 rounded-lg overflow-hidden">
              <table className="w-full text-left border-collapse text-sm">
                <thead>
                  <tr className="bg-slate-50 border-b border-slate-200">
                    <th className="p-2 font-semibold text-slate-600">Item</th>
                    <th className="p-2 font-semibold text-slate-600 w-24">Qty</th>
                    <th className="p-2 font-semibold text-slate-600 w-24">Rate</th>
                    <th className="p-2 font-semibold text-slate-600 w-24">Tax %</th>
                    <th className="p-2 font-semibold text-slate-600 w-32">Total</th>
                  </tr>
                </thead>
                <tbody>
                  {items.map((item, idx) => (
                    <tr key={idx} className="border-b border-slate-100 last:border-0">
                      <td className="p-2">
                        <input type="text" disabled value={item.item_name || ''} className="w-full p-1 border rounded bg-slate-50" />
                      </td>
                      <td className="p-2">
                        <input type="number" min="0" value={item.order_qty || 0} onChange={(e) => handleItemChange(idx, 'order_qty', e.target.value)} className="w-full p-1 border rounded" />
                      </td>
                      <td className="p-2">
                        <input type="number" min="0" step="0.01" value={item.rate || 0} onChange={(e) => handleItemChange(idx, 'rate', e.target.value)} className="w-full p-1 border rounded" />
                      </td>
                      <td className="p-2">
                        <input type="number" min="0" step="0.01" value={item.tax_percentage || 0} onChange={(e) => handleItemChange(idx, 'tax_percentage', e.target.value)} className="w-full p-1 border rounded" />
                      </td>
                      <td className="p-2 font-medium text-slate-800">
                        ₹{formatAmt(item.amount)}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>

            <div className="mt-4 flex justify-end">
              <div className="bg-slate-50 p-4 rounded-lg border border-slate-200 text-right">
                <div className="text-sm text-slate-500 mb-1">Total Quantity: <span className="font-bold text-slate-800">{formatQty(calculateTotal().total_qty)}</span></div>
                <div className="text-sm text-slate-500">Total Amount: <span className="font-bold text-slate-800">₹{formatAmt(calculateTotal().total_amt)}</span></div>
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
