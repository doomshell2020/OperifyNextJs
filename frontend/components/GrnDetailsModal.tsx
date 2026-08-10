'use client';

import React from 'react';
import { useQuery } from '@tanstack/react-query';
import { grnService } from '../services/grn.service';
import { X, AlertCircle, Loader } from 'lucide-react';

interface GrnDetailsModalProps {
  id: number | string;
  isOpen: boolean;
  onClose: () => void;
}

export const GrnDetailsModal: React.FC<GrnDetailsModalProps> = ({
  id,
  isOpen,
  onClose
}) => {
  const { data, isLoading, isError } = useQuery({
    queryKey: ['grn-details', id],
    queryFn: () => grnService.getDetails(id.toString()),
    enabled: isOpen && !!id,
    staleTime: 5 * 60 * 1000
  });

  if (!isOpen) return null;

  const formatDate = (dateStr?: string) => {
    if (!dateStr) return 'N/A';
    return new Date(dateStr).toLocaleDateString('en-IN', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric'
    });
  };

  return (
    <div className="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm animate-in fade-in duration-200">
      
      {/* Modal Dialog Box */}
      <div className="bg-white border border-slate-200 shadow-2xl rounded-2xl max-w-5xl w-full p-6 flex flex-col relative overflow-hidden animate-in zoom-in-95 duration-200 max-h-[90vh]">
        
        {/* Top Control Bar */}
        <div className="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
          <h3 className="text-sm font-extrabold text-slate-950 tracking-tight uppercase">
            GRN Details
          </h3>
          <button
            onClick={onClose}
            className="p-1.5 text-slate-400 hover:text-slate-700 bg-slate-50 border border-slate-200 rounded-lg transition cursor-pointer"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        {/* Content Section */}
        {isLoading ? (
          <div className="flex-1 flex flex-col items-center justify-center py-20 text-slate-400 gap-2">
            <Loader className="w-8 h-8 animate-spin text-cyan-600" />
            <span className="text-xs font-medium">Loading GRN data...</span>
          </div>
        ) : isError || !data?.data?.grn ? (
          <div className="flex-1 flex flex-col items-center justify-center py-20 text-rose-500 gap-2">
            <AlertCircle className="w-8 h-8" />
            <span className="text-xs font-medium">Failed to sync GRN detail items.</span>
          </div>
        ) : (
          <div className="flex-1 flex flex-col overflow-y-auto space-y-6 select-none pr-1">
            
            {/* Header info blocks */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-xs font-medium text-slate-600 bg-slate-50 border border-slate-150 rounded-xl p-5">
              <div className="space-y-2">
                <div>
                  <span className="text-slate-400 font-bold uppercase text-[9px] tracking-wider">GRN Reference</span>
                  <p className="text-sm font-bold text-slate-950 mt-0.5">GRN No. :- {data.data.grn.id}</p>
                </div>
                <div>
                  <span className="text-slate-400 font-bold uppercase text-[9px] tracking-wider">Inward Date</span>
                  <p className="text-slate-800 font-semibold mt-0.5">Inward Date :- {formatDate(data.data.grn.inwarddate)}</p>
                </div>
                <div>
                  <span className="text-slate-400 font-bold uppercase text-[9px] tracking-wider">Bill No</span>
                  <p className="text-slate-800 font-semibold mt-0.5">Bill No :- {data.data.grn.bill_no}</p>
                </div>
                <div>
                  <span className="text-slate-400 font-bold uppercase text-[9px] tracking-wider">Vendor</span>
                  <p className="text-slate-800 font-semibold mt-0.5">Vendor Name :- {data.data.grn.vendor_name}</p>
                </div>
              </div>

              <div className="space-y-2">
                <div>
                  <span className="text-slate-400 font-bold uppercase text-[9px] tracking-wider">PO Reference</span>
                  <p className="text-sm font-bold text-slate-950 mt-0.5">PO No. :- {data.data.grn.purchaseorder_id}</p>
                </div>
                <div>
                  <span className="text-slate-400 font-bold uppercase text-[9px] tracking-wider">Bill Date</span>
                  <p className="text-slate-800 font-semibold mt-0.5">Bill Date :- {formatDate(data.data.grn.bill_date)}</p>
                </div>
                <div>
                  <span className="text-slate-400 font-bold uppercase text-[9px] tracking-wider">GSTIN NO</span>
                  <p className="text-slate-800 font-semibold mt-0.5">GSTIN NO. :- {data.data.grn.vendor_gstin || 'N/A'}</p>
                </div>
              </div>
            </div>

            {/* Title for items */}
            <div className="text-center font-bold text-slate-800 uppercase tracking-wider text-sm mt-4">
              Received Products
            </div>

            {/* Table of items */}
            <div className="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-sm">
              <table className="w-full text-left text-xs border-collapse">
                <thead>
                  <tr className="bg-slate-100 border-b border-slate-200 text-slate-600 uppercase tracking-wider">
                    <th className="p-3 font-semibold w-12 text-center">S.No.</th>
                    <th className="p-3 font-semibold">Item</th>
                    <th className="p-3 font-semibold text-right">Order Qty.</th>
                    <th className="p-3 font-semibold text-right">Received Qty.</th>
                    <th className="p-3 font-semibold text-right">Rate</th>
                    <th className="p-3 font-semibold text-right">Price (INR)</th>
                    <th className="p-3 font-semibold text-center">Tax Rate(%)</th>
                    <th className="p-3 font-semibold text-right">Tax Amt</th>
                    <th className="p-3 font-semibold text-right">Amount</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                  {data.data.items && data.data.items.length > 0 ? (
                    data.data.items.map((item: any, idx: number) => {
                      const qty = Number(item.quantity) || 0;
                      const rate = Number(item.rate) || 0;
                      const taxRate = Number(item.tax) ? (Number(item.tax) / (qty * rate)) * 100 : 0; // approximate tax rate if not directly available
                      
                      const price = qty * rate;
                      const taxAmt = Number(item.tax) || 0;
                      const amount = price + taxAmt;

                      return (
                        <tr key={item.id} className="hover:bg-slate-50 transition-colors">
                          <td className="p-3 text-center text-slate-500 font-medium">{idx + 1}.</td>
                          <td className="p-3 text-slate-800 font-medium">{item.item_name}</td>
                          <td className="p-3 text-right text-slate-600">{qty} {item.uom}</td>
                          <td className="p-3 text-right text-slate-800 font-medium">{qty} {item.uom}</td>
                          <td className="p-3 text-right text-slate-600">{rate.toLocaleString('en-IN', { minimumFractionDigits: 2 })}</td>
                          <td className="p-3 text-right text-slate-800 font-medium">{price.toLocaleString('en-IN', { minimumFractionDigits: 2 })}</td>
                          <td className="p-3 text-center text-slate-600">{Math.round(taxRate) || 18}</td>
                          <td className="p-3 text-right text-slate-600">{taxAmt.toLocaleString('en-IN', { minimumFractionDigits: 2 })}</td>
                          <td className="p-3 text-right text-slate-800 font-bold">{amount.toLocaleString('en-IN', { minimumFractionDigits: 2 })}</td>
                        </tr>
                      );
                    })
                  ) : (
                    <tr>
                      <td colSpan={9} className="p-8 text-center text-slate-400 font-medium">
                        No received products found.
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
              
              {/* Footer Total */}
              {data.data.grn && (
                <div className="bg-slate-50 border-t border-slate-200 p-3 flex justify-between items-center text-xs">
                  <span className="font-bold text-slate-600 pl-4 uppercase">Tax Excluded</span>
                  <div className="text-right">
                    <span className="font-bold text-slate-900 pr-2">Total Amount :-</span>
                    <span className="font-extrabold text-cyan-700 text-sm">{Number(data.data.grn.total_amt + data.data.grn.total_tax).toLocaleString('en-IN', { minimumFractionDigits: 2 })}</span>
                  </div>
                </div>
              )}
            </div>

          </div>
        )}
      </div>
    </div>
  );
};
