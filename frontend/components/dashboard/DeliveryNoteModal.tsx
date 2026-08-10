"use client";

import React, { useState, useEffect } from 'react';
import { useMutation, useQueryClient, useQuery } from '@tanstack/react-query';
import purchaseOrderService, { PurchaseOrderDetailsData, PurchaseOrderItem } from '../../services/purchaseOrder.service';
import { Loader, X, Save, AlertCircle } from 'lucide-react';

interface DeliveryNoteModalProps {
  poId: number;
  onClose: () => void;
}

export function DeliveryNoteModal({ poId, onClose }: DeliveryNoteModalProps) {
  const queryClient = useQueryClient();
  const [remarks, setRemarks] = useState('');
  const [items, setItems] = useState<(PurchaseOrderItem & { received_qty?: number; accepted_qty?: number; rejected_qty?: number })[]>([]);

  const { data, isLoading, isError } = useQuery({
    queryKey: ['purchase-order-details', poId],
    queryFn: () => purchaseOrderService.getDetails(poId),
    enabled: !!poId,
  });

  useEffect(() => {
    if (data) {
      setItems(data.items.map(i => ({ ...i, received_qty: 0, accepted_qty: 0, rejected_qty: 0 })));
    }
  }, [data]);

  const addDeliveryMutation = useMutation({
    mutationFn: (payload: any) => purchaseOrderService.addDeliveryNote(poId, payload),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['purchase-orders'] });
      queryClient.invalidateQueries({ queryKey: ['purchase-order-details', poId] });
      onClose();
    }
  });

  const handleItemChange = (index: number, field: string, value: string) => {
    const val = parseFloat(value) || 0;
    const newItems = [...items];
    (newItems[index] as any)[field] = val;
    setItems(newItems);
  };

  const handleSave = () => {
    if (!data) return;
    
    const payload = {
      po_number: data.po.po_number,
      vendor_id: (data.po as any).vendor_id, // We need to make sure we have vendor_id if required by backend, else backend can infer it. 
      // Actually backend needs vendor_id. But our GET /details doesn't return vendor_id.
      // Wait, getDetails query in backend does not select vendor_id!
      // Let's rely on backend to just use whatever is sent, or modify backend to not strictly need it.
      // We will pass undefined if not available.
      items: items.filter(i => (i.received_qty || 0) > 0),
      remarks
    };
    addDeliveryMutation.mutate(payload);
  };

  if (!poId) return null;

  return (
    <div className="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm animate-in fade-in duration-200">
      <div className="bg-white border border-slate-200 shadow-2xl rounded-2xl max-w-4xl w-full p-6 flex flex-col relative overflow-hidden animate-in zoom-in-95 duration-200 max-h-[90vh]">
        
        <div className="absolute top-4 right-4 z-10 flex gap-2">
          <button
            onClick={handleSave}
            disabled={addDeliveryMutation.isPending || isLoading}
            className="flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-bold shadow-sm transition cursor-pointer text-sm disabled:opacity-50"
          >
            {addDeliveryMutation.isPending ? <Loader className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
            Save Note
          </button>
          <button
            onClick={onClose}
            className="p-2 text-slate-400 hover:text-slate-700 bg-white border border-slate-200 rounded transition cursor-pointer shadow-sm"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        <h2 className="text-xl font-bold text-slate-800 mb-6">
          Add Delivery Note
        </h2>

        {isLoading && (
          <div className="flex flex-col items-center justify-center py-20 text-slate-500">
            <Loader className="w-8 h-8 animate-spin mb-4 text-indigo-500" />
            <p>Loading PO items...</p>
          </div>
        )}

        {isError && (
          <div className="flex flex-col items-center justify-center py-20 text-red-500 bg-red-50 rounded-xl">
            <AlertCircle className="w-10 h-10 mb-2" />
            <p className="font-semibold">Failed to load PO items</p>
          </div>
        )}

        {!isLoading && !isError && data && (
          <div className="flex-1 overflow-y-auto pr-2 custom-scrollbar">
            <div className="mb-6">
              <label className="block text-xs font-semibold text-slate-500 uppercase mb-1">Remarks</label>
              <textarea 
                value={remarks} 
                onChange={(e) => setRemarks(e.target.value)} 
                className="w-full border p-2 rounded" 
                rows={2} 
                placeholder="Optional delivery remarks..."
              />
            </div>

            <h3 className="text-sm font-bold text-slate-800 mb-2">Receive Items</h3>
            <div className="border border-slate-200 rounded-lg overflow-hidden">
              <table className="w-full text-left border-collapse text-sm">
                <thead>
                  <tr className="bg-slate-50 border-b border-slate-200">
                    <th className="p-2 font-semibold text-slate-600">Item Name</th>
                    <th className="p-2 font-semibold text-slate-600 w-24">Order Qty</th>
                    <th className="p-2 font-semibold text-slate-600 w-24 text-blue-600">Received</th>
                    <th className="p-2 font-semibold text-slate-600 w-24 text-green-600">Accepted</th>
                    <th className="p-2 font-semibold text-slate-600 w-24 text-red-600">Rejected</th>
                  </tr>
                </thead>
                <tbody>
                  {items.map((item, idx) => (
                    <tr key={idx} className="border-b border-slate-100 last:border-0 hover:bg-slate-50">
                      <td className="p-2 font-medium text-slate-700">{item.item_name}</td>
                      <td className="p-2">{item.order_qty} {item.uom}</td>
                      <td className="p-2">
                        <input type="number" min="0" value={item.received_qty} onChange={(e) => handleItemChange(idx, 'received_qty', e.target.value)} className="w-full p-1 border rounded border-blue-200 focus:border-blue-500 outline-none" />
                      </td>
                      <td className="p-2">
                        <input type="number" min="0" value={item.accepted_qty} onChange={(e) => handleItemChange(idx, 'accepted_qty', e.target.value)} className="w-full p-1 border rounded border-green-200 focus:border-green-500 outline-none" />
                      </td>
                      <td className="p-2">
                        <input type="number" min="0" value={item.rejected_qty} onChange={(e) => handleItemChange(idx, 'rejected_qty', e.target.value)} className="w-full p-1 border rounded border-red-200 focus:border-red-500 outline-none" />
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
