'use client';

import React from 'react';
import { useQuery } from '@tanstack/react-query';
import purchaseOrderService from '../services/purchaseOrder.service';
import { X, Printer, AlertCircle, Loader } from 'lucide-react';
import { StatusBadge } from './dashboard/StatusBadge';
import { formatDate } from '../utils/dateFormatter';

interface PurchaseOrderDetailsModalProps {
  id: number | string;
  isOpen: boolean;
  onClose: () => void;
}

export const PurchaseOrderDetailsModal: React.FC<PurchaseOrderDetailsModalProps> = ({
  id,
  isOpen,
  onClose
}) => {
  const { data, isLoading, isError } = useQuery({
    queryKey: ['purchase-order', 'details', id],
    queryFn: () => purchaseOrderService.getDetails(id),
    enabled: isOpen && !!id,
    staleTime: 5 * 60 * 1000
  });

  if (!isOpen) return null;

  const handlePrint = () => {
    window.open(`/dashboard/purchase/inspections/print-po/${id}`, '_blank');
  };

  
  return (
    <div className="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm animate-in fade-in duration-200">
      
      {/* Modal Dialog Box */}
      <div className="bg-white border border-slate-200 shadow-2xl rounded-2xl max-w-4xl w-full p-6 flex flex-col relative overflow-hidden animate-in zoom-in-95 duration-200 max-h-[90vh]">
        
        {/* Top Control Bar */}
        <div className="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
          <h3 className="text-sm font-extrabold text-slate-950 tracking-tight uppercase">
            Purchase Order Details
          </h3>
          <div className="flex items-center gap-3">
            {data && (
              <button
                onClick={handlePrint}
                className="flex items-center gap-1.5 px-3 py-1.5 bg-cyan-600 hover:bg-cyan-500 text-white rounded-lg text-xs font-semibold shadow-sm transition cursor-pointer print:hidden"
              >
                <Printer className="w-3.5 h-3.5" />
                Print PO
              </button>
            )}
            <button
              onClick={onClose}
              className="p-1.5 text-slate-400 hover:text-slate-700 bg-slate-50 border border-slate-200 rounded-lg transition cursor-pointer print:hidden"
            >
              <X className="w-4 h-4" />
            </button>
          </div>
        </div>

        {/* Content Section */}
        {isLoading ? (
          <div className="flex-1 flex flex-col items-center justify-center py-20 text-slate-400 gap-2">
            <Loader className="w-8 h-8 animate-spin text-cyan-600" />
            <span className="text-xs font-medium">Loading items data...</span>
          </div>
        ) : isError || !data ? (
          <div className="flex-1 flex flex-col items-center justify-center py-20 text-rose-500 gap-2">
            <AlertCircle className="w-8 h-8" />
            <span className="text-xs font-medium">Failed to sync Purchase Order detail items.</span>
          </div>
        ) : (
          <div className="flex-1 flex flex-col overflow-y-auto space-y-6 select-none pr-1">
            
            {/* Header info blocks */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-xs font-medium text-slate-600 bg-slate-50 border border-slate-150 rounded-xl p-5">
              <div className="space-y-2">
                <div>
                  <span className="text-slate-400 font-bold uppercase text-[9px] tracking-wider">PO Reference</span>
                  <p className="text-sm font-bold text-slate-950 mt-0.5">PO No. : {data.po.po_number}</p>
                </div>
                <div>
                  <span className="text-slate-400 font-bold uppercase text-[9px] tracking-wider">Generation Date</span>
                  <p className="text-slate-800 font-semibold mt-0.5">Date : {formatDate(data.po.po_date)}</p>
                </div>
                {data.po.amendment_no > 0 && (
                  <div>
                    <span className="text-slate-400 font-bold uppercase text-[9px] tracking-wider">Amendments</span>
                    <p className="text-slate-800 font-semibold mt-0.5">
                      Amendment No : {data.po.amendment_no} (Date : {formatDate(data.po.amendment_date)})
                    </p>
                  </div>
                )}
                <div>
                  <span className="text-slate-400 font-bold uppercase text-[9px] tracking-wider">Registration Status</span>
                  <div className="mt-1 flex items-center gap-1.5">
                    <span className="text-slate-500">Status :</span>
                    <StatusBadge status={data.po.status} />
                  </div>
                </div>
              </div>

              <div className="space-y-2">
                <div>
                  <span className="text-slate-400 font-bold uppercase text-[9px] tracking-wider">Delivery Commitments</span>
                  <p className="text-slate-800 font-semibold mt-0.5">Delivery Date : {formatDate(data.po.delivery_date)}</p>
                </div>
                <div>
                  <span className="text-slate-400 font-bold uppercase text-[9px] tracking-wider">Vendor Connection</span>
                  <p className="text-sm font-bold text-slate-950 mt-0.5">{data.po.vendor_name}</p>
                </div>
                <div>
                  <span className="text-slate-400 font-bold uppercase text-[9px] tracking-wider">Tax Registration</span>
                  <p className="text-slate-800 font-semibold mt-0.5">GSTIN NO. : {data.po.gst_number}</p>
                </div>
              </div>
            </div>

            {/* Products Table section */}
            <div className="space-y-3">
              <span className="text-xs font-extrabold text-slate-400 uppercase tracking-wider block">
                Products Ordered
              </span>
              
              <div className="border border-slate-200 rounded-xl overflow-x-auto shadow-sm">
                <table className="w-full text-left border-collapse text-[11px] font-medium text-slate-700">
                  <thead>
                    <tr className="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200">
                      <th className="px-4 py-2.5 text-center">S.No.</th>
                      <th className="px-4 py-2.5">Item Description</th>
                      <th className="px-4 py-2.5 text-right">Order Qty</th>
                      <th className="px-4 py-2.5 text-right">Pending Qty</th>
                      <th className="px-4 py-2.5 text-right">Rate</th>
                      <th className="px-4 py-2.5 text-right">Price (INR)</th>
                      <th className="px-4 py-2.5 text-center">Tax</th>
                      <th className="px-4 py-2.5 text-right">Tax Amt</th>
                      <th className="px-4 py-2.5 text-right">Amount</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100 bg-white">
                    {data.items.map((item, idx) => (
                      <tr key={item.id} className="hover:bg-slate-50/50 transition">
                        <td className="px-4 py-3 text-center text-slate-400 font-bold">{idx + 1}.</td>
                        <td className="px-4 py-3 text-slate-900 font-bold max-w-[240px] truncate" title={item.item_name}>
                          {item.item_name}
                        </td>
                        <td className="px-4 py-3 text-right font-semibold">
                          {Number(item.order_qty).toLocaleString()} {item.uom}
                        </td>
                        <td className="px-4 py-3 text-right font-semibold text-slate-500">
                          {Number(item.pending_qty).toLocaleString()} {item.uom}
                        </td>
                        <td className="px-4 py-3 text-right font-semibold">
                          ₹{Number(item.rate).toLocaleString('en-IN', { minimumFractionDigits: 2 })}
                        </td>
                        <td className="px-4 py-3 text-right font-semibold text-slate-800">
                          ₹{Number(item.price).toLocaleString('en-IN', { minimumFractionDigits: 2 })}
                        </td>
                        <td className="px-4 py-3 text-center font-bold text-slate-500">
                          {item.tax_percentage}%
                        </td>
                        <td className="px-4 py-3 text-right font-semibold text-slate-500">
                          ₹{Number(item.tax_amt).toLocaleString('en-IN', { minimumFractionDigits: 2 })}
                        </td>
                        <td className="px-4 py-3 text-right font-bold text-slate-950">
                          ₹{Number(item.amount).toLocaleString('en-IN', { minimumFractionDigits: 2 })}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                  <tfoot>
                    <tr className="bg-slate-50/70 border-t border-slate-200 text-xs font-bold text-slate-900">
                      <td colSpan={5} className="px-4 py-3 text-left uppercase text-slate-400">
                        Tax Excluded
                      </td>
                      <td colSpan={4} className="px-4 py-3 text-right text-indigo-700">
                        Total Amount: ₹{Number(data.po.total_amount).toLocaleString('en-IN', { minimumFractionDigits: 2 })}
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>

            {/* Goods Received Data */}
            {data.grns && data.grns.length > 0 && (
              <div className="space-y-3 mt-6">
                <span className="text-xs font-extrabold text-slate-400 uppercase tracking-wider block">
                  Goods Received Data
                </span>
                
                {data.grns.map((grn: any, index: number) => (
                  <div key={grn.id} className="border border-slate-200 rounded-xl overflow-hidden shadow-sm mb-4">
                    <div className="bg-slate-50 border-b border-slate-200 p-3 grid grid-cols-4 gap-4 text-[11px]">
                      <div>
                        <span className="text-slate-400 font-bold uppercase block mb-0.5">GRN No. / ID</span>
                        <span className="font-semibold text-slate-900">{grn.grn_number || grn.id}</span>
                      </div>
                      <div>
                        <span className="text-slate-400 font-bold uppercase block mb-0.5">Inward Date</span>
                        <span className="font-semibold text-slate-900">{formatDate(grn.inward_date)}</span>
                      </div>
                      <div>
                        <span className="text-slate-400 font-bold uppercase block mb-0.5">Bill No.</span>
                        <span className="font-semibold text-slate-900">{grn.bill_no}</span>
                      </div>
                      <div>
                        <span className="text-slate-400 font-bold uppercase block mb-0.5">Bill Date</span>
                        <span className="font-semibold text-slate-900">{formatDate(grn.bill_date)}</span>
                      </div>
                    </div>
                    
                    <div className="overflow-x-auto">
                      <table className="w-full text-left border-collapse text-[11px] font-medium text-slate-700">
                        <thead>
                          <tr className="bg-white text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                          <th className="px-4 py-2.5 text-center w-12">S.No.</th>
                          <th className="px-4 py-2.5">Item Description</th>
                          <th className="px-4 py-2.5 text-right">Rec Qty.</th>
                          <th className="px-4 py-2.5 text-right">Rate/Unit</th>
                          <th className="px-4 py-2.5 text-center">Tax</th>
                          <th className="px-4 py-2.5 text-right">Tax Amt</th>
                          <th className="px-4 py-2.5 text-right">Amount</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-slate-50 bg-white">
                        {grn.items && grn.items.map((item: any, idx: number) => (
                          <tr key={item.id} className="hover:bg-slate-50/50 transition">
                            <td className="px-4 py-2 text-center text-slate-400 font-bold">{idx + 1}.</td>
                            <td className="px-4 py-2 text-slate-900 font-bold">
                              {item.item_name}
                            </td>
                            <td className="px-4 py-2 text-right font-semibold">
                              {Number(item.item_qty).toLocaleString()} {item.uom}
                            </td>
                            <td className="px-4 py-2 text-right font-semibold">
                              ₹{Number(item.price).toLocaleString('en-IN', { minimumFractionDigits: 2 })}
                            </td>
                            <td className="px-4 py-2 text-center font-bold text-slate-500">
                              {item.tax_percentage}%
                            </td>
                            <td className="px-4 py-2 text-right font-semibold text-slate-500">
                              ₹{Number(item.tax_amt).toLocaleString('en-IN', { minimumFractionDigits: 2 })}
                            </td>
                            <td className="px-4 py-2 text-right font-bold text-slate-950">
                              ₹{Number(item.amount).toLocaleString('en-IN', { minimumFractionDigits: 2 })}
                            </td>
                          </tr>
                        ))}
                      </tbody>
                      <tfoot>
                        <tr className="bg-slate-50/70 border-t border-slate-100 text-[11px] font-bold text-slate-900">
                          <td colSpan={2} className="px-4 py-2 text-left uppercase text-slate-400">
                            GRN Totals
                          </td>
                          <td className="px-4 py-2 text-right text-slate-700">
                            Qty: {Number(grn.total_qty).toLocaleString()}
                          </td>
                          <td colSpan={4} className="px-4 py-2 text-right text-emerald-700">
                            Total: ₹{Number(grn.total_amt).toLocaleString('en-IN', { minimumFractionDigits: 2 })}
                          </td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
                ))}
              </div>
            )}

          </div>
        )}

      </div>
    </div>
  );
};
