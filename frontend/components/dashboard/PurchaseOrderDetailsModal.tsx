"use client";

import React from 'react';
import { useQuery } from '@tanstack/react-query';
import purchaseOrderService from '../../services/purchaseOrder.service';
import { Loader, AlertCircle, X, Printer } from 'lucide-react';

interface PurchaseOrderDetailsModalProps {
  poId: number;
  onClose: () => void;
}

export function PurchaseOrderDetailsModal({ poId, onClose }: PurchaseOrderDetailsModalProps) {
  const { data: details, isLoading, isError } = useQuery({
    queryKey: ['purchase-order-details', poId],
    queryFn: () => purchaseOrderService.getDetails(poId),
    enabled: !!poId,
  });

  return (
    <div className="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm animate-in fade-in duration-200">
      <div className="bg-white border border-slate-200 shadow-2xl rounded-2xl max-w-4xl w-full p-6 flex flex-col relative overflow-hidden animate-in zoom-in-95 duration-200 max-h-[90vh]">
        
        <div className="absolute top-4 right-4 z-10 flex gap-2">
          <button
            onClick={onClose}
            className="p-2 text-slate-400 hover:text-slate-700 bg-white border border-slate-200 rounded transition cursor-pointer shadow-sm"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        <h2 className="text-xl font-bold text-slate-800 mb-6">
          Purchase Order Details
        </h2>

        {isLoading && (
          <div className="flex flex-col items-center justify-center py-20 text-slate-500">
            <Loader className="w-8 h-8 animate-spin mb-4 text-cyan-500" />
            <p>Loading PO details...</p>
          </div>
        )}

        {isError && (
          <div className="flex flex-col items-center justify-center py-20 text-red-500 bg-red-50 rounded-xl">
            <AlertCircle className="w-10 h-10 mb-2" />
            <p className="font-semibold">Failed to load PO details</p>
          </div>
        )}

        {details && (
          <div className="flex-1 overflow-y-auto pr-2 custom-scrollbar">
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
              <div>
                <label className="block text-xs font-semibold text-slate-500 uppercase mb-1">PO Number</label>
                <p className="font-medium">{details.po.po_number}</p>
              </div>
              <div>
                <label className="block text-xs font-semibold text-slate-500 uppercase mb-1">PO Date</label>
                <p className="font-medium">{details.po.po_date?.split('T')[0]}</p>
              </div>
              <div>
                <label className="block text-xs font-semibold text-slate-500 uppercase mb-1">Vendor</label>
                <p className="font-medium">{details.po.vendor_name}</p>
              </div>
              <div>
                <label className="block text-xs font-semibold text-slate-500 uppercase mb-1">Delivery Date</label>
                <p className="font-medium">{details.po.delivery_date?.split('T')[0] || 'N/A'}</p>
              </div>
            </div>

            <div className="mb-6">
              <label className="block text-xs font-semibold text-slate-500 uppercase mb-1">Status</label>
              <span className={`px-2 py-1 rounded text-xs font-semibold ${details.po.status === 'Open' ? 'bg-cyan-100 text-cyan-700' : 'bg-slate-100 text-slate-700'}`}>
                {details.po.status}
              </span>
            </div>

            <h3 className="text-sm font-bold text-slate-800 mb-2">Items</h3>
            <div className="border border-slate-200 rounded-lg overflow-hidden">
              <table className="w-full text-left border-collapse text-sm">
                <thead>
                  <tr className="bg-slate-50 border-b border-slate-200">
                    <th className="p-2 font-semibold text-slate-600">Item</th>
                    <th className="p-2 font-semibold text-slate-600 text-right">Qty</th>
                    <th className="p-2 font-semibold text-slate-600 text-right">Rate</th>
                    <th className="p-2 font-semibold text-slate-600 text-right">Tax %</th>
                    <th className="p-2 font-semibold text-slate-600 text-right">Total</th>
                  </tr>
                </thead>
                <tbody>
                  {details.items.map((item, idx) => (
                    <tr key={idx} className="border-b border-slate-100 last:border-0">
                      <td className="p-2 text-slate-700">{item.item_name}</td>
                      <td className="p-2 text-right">{item.order_qty} {item.uom}</td>
                      <td className="p-2 text-right">₹{parseFloat(item.rate as any).toFixed(2)}</td>
                      <td className="p-2 text-right">{parseFloat(item.tax_percentage as any).toFixed(2)}%</td>
                      <td className="p-2 text-right font-medium text-slate-800">
                        ₹{parseFloat(item.amount as any).toFixed(2)}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>

            <div className="mt-4 flex justify-end">
              <div className="bg-slate-50 p-4 rounded-lg border border-slate-200 text-right">
                <div className="text-sm text-slate-500">Total Amount: <span className="font-bold text-slate-800 text-lg">₹{details.po.total_amount?.toLocaleString('en-IN', { minimumFractionDigits: 2 })}</span></div>
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
