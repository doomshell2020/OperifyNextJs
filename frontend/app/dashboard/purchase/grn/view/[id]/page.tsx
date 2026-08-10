'use client';

import React from 'react';
import { useQuery } from '@tanstack/react-query';
import { useRouter } from 'next/navigation';
import { grnService } from '@/services/grn.service';
import { ArrowLeft, Printer, Loader2, AlertCircle } from 'lucide-react';

export default function ViewGrnPage({ params }: { params: Promise<{ id: string }> }) {
  const router = useRouter();
  const { id } = React.use(params);

  const { data, isLoading, isError } = useQuery({
    queryKey: ['grn-details', id],
    queryFn: () => grnService.getDetails(id),
  });

  const handlePrint = () => {
    window.print();
  };

  if (isLoading) {
    return (
      <div className="flex flex-col items-center justify-center min-h-[400px]">
        <Loader2 className="w-8 h-8 animate-spin text-cyan-600 mb-2" />
        <p className="text-sm font-medium text-slate-600">Loading GRN Details...</p>
      </div>
    );
  }

  if (isError || !data?.data?.grn) {
    return (
      <div className="flex flex-col items-center justify-center min-h-[400px] text-red-500">
        <AlertCircle className="w-10 h-10 mb-2 opacity-50" />
        <p className="text-sm font-semibold">Failed to load GRN details</p>
        <button onClick={() => router.back()} className="mt-4 px-4 py-2 bg-slate-100 text-slate-700 rounded-md text-sm font-medium hover:bg-slate-200 transition">
          Go Back
        </button>
      </div>
    );
  }

  const { grn, items } = data.data;

  return (
    <main className="max-w-5xl w-full mx-auto px-6 py-8 space-y-6 font-sans pb-24">
      {/* Header Actions (Hidden when printing) */}
      <div className="flex justify-between items-center print:hidden bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <div className="flex items-center gap-4">
          <button onClick={() => router.back()} className="p-2 bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-full transition cursor-pointer">
            <ArrowLeft className="w-5 h-5" />
          </button>
          <div>
            <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight">View GRN #{grn.id}</h1>
            <p className="text-sm text-slate-500 font-medium">Goods Received Note details and items</p>
          </div>
        </div>
        <button onClick={handlePrint} className="flex items-center gap-2 px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-md text-sm font-medium transition cursor-pointer shadow-sm">
          <Printer className="w-4 h-4" /> Print / PDF
        </button>
      </div>

      {/* Printable Area */}
      <div className="bg-white border border-slate-200 rounded-xl shadow-sm p-8 print:shadow-none print:border-none print:p-0">
        <div className="flex justify-between items-start border-b border-slate-200 pb-6 mb-6">
          <div>
            <h2 className="text-2xl font-bold text-slate-800 uppercase tracking-wider">Goods Received Note</h2>
            <div className="mt-4 space-y-1">
              <p className="text-sm"><span className="text-slate-500 font-medium w-24 inline-block">GRN No:</span> <span className="font-semibold text-slate-800">{grn.id}</span></p>
              <p className="text-sm"><span className="text-slate-500 font-medium w-24 inline-block">Inward Date:</span> <span className="font-semibold text-slate-800">{grn.inwarddate?.split('T')[0]}</span></p>
              <p className="text-sm"><span className="text-slate-500 font-medium w-24 inline-block">Status:</span> <span className={`font-semibold ${grn.status === 'C' ? 'text-emerald-600' : 'text-amber-600'}`}>{grn.status === 'C' ? 'Completed' : 'Open'}</span></p>
            </div>
          </div>
          <div className="text-right text-sm space-y-1">
            <p><span className="text-slate-500 font-medium">PO Number:</span> <span className="font-semibold text-slate-800">{grn.purchaseorder_id}</span></p>
            <p><span className="text-slate-500 font-medium">Bill No:</span> <span className="font-semibold text-slate-800">{grn.bill_no}</span></p>
            <p><span className="text-slate-500 font-medium">Bill Date:</span> <span className="font-semibold text-slate-800">{grn.bill_date?.split('T')[0]}</span></p>
          </div>
        </div>

        <div className="mb-8">
          <h3 className="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2">Vendor Details</h3>
          <div className="bg-slate-50 rounded-lg p-4 border border-slate-100">
            <p className="font-bold text-lg text-slate-800">{grn.vendor_name}</p>
            <p className="text-sm text-slate-600 mt-1">Vendor ID: {grn.vendor_id}</p>
          </div>
        </div>

        <div className="mb-8 overflow-hidden rounded-lg border border-slate-200">
          <table className="w-full text-left text-sm border-collapse">
            <thead>
              <tr className="bg-slate-50 text-slate-600 uppercase text-xs tracking-wider border-b border-slate-200">
                <th className="p-3 font-semibold">S.No</th>
                <th className="p-3 font-semibold">Item Name</th>
                <th className="p-3 font-semibold text-right">Qty Received</th>
                <th className="p-3 font-semibold">UOM</th>
                <th className="p-3 font-semibold text-right">Rate (₹)</th>
                <th className="p-3 font-semibold text-right">Tax (₹)</th>
                <th className="p-3 font-semibold text-right">Total (₹)</th>
              </tr>
            </thead>
            <tbody>
              {items && items.length > 0 ? items.map((item: any, index: number) => (
                <tr key={item.id} className="border-b border-slate-100 last:border-0">
                  <td className="p-3 text-slate-600">{index + 1}</td>
                  <td className="p-3 font-medium text-slate-800">{item.item_name}</td>
                  <td className="p-3 text-right font-medium text-slate-800">{item.quantity}</td>
                  <td className="p-3 text-slate-600">{item.uom}</td>
                  <td className="p-3 text-right text-slate-600">{parseFloat(item.rate).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                  <td className="p-3 text-right text-slate-600">{parseFloat(item.tax).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                  <td className="p-3 text-right font-medium text-slate-800">
                    {(parseFloat(item.amount) + parseFloat(item.tax)).toLocaleString('en-IN', {minimumFractionDigits: 2})}
                  </td>
                </tr>
              )) : (
                <tr>
                  <td colSpan={7} className="p-4 text-center text-slate-500">No items found for this GRN.</td>
                </tr>
              )}
            </tbody>
          </table>
        </div>

        <div className="flex flex-col items-end pt-4 border-t border-slate-200">
          <div className="w-full max-w-sm space-y-3 text-sm">
            <div className="flex justify-between">
              <span className="text-slate-500">Total Quantity:</span>
              <span className="font-semibold text-slate-800">{grn.total_qty}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-slate-500">Amount (Pre-tax):</span>
              <span className="font-semibold text-slate-800">₹ {parseFloat(grn.total_amt).toLocaleString('en-IN', {minimumFractionDigits: 2})}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-slate-500">Total Tax:</span>
              <span className="font-semibold text-slate-800">₹ {parseFloat(grn.total_tax).toLocaleString('en-IN', {minimumFractionDigits: 2})}</span>
            </div>
            <div className="flex justify-between pt-3 border-t border-slate-200 text-base">
              <span className="font-bold text-slate-800">Net Amount:</span>
              <span className="font-extrabold text-cyan-600">₹ {(parseFloat(grn.total_amt) + parseFloat(grn.total_tax)).toLocaleString('en-IN', {minimumFractionDigits: 2})}</span>
            </div>
          </div>
        </div>

        {grn.remark && (
          <div className="mt-8 p-4 bg-amber-50/50 rounded-lg border border-amber-100">
            <h4 className="text-xs font-bold text-amber-800 uppercase tracking-wider mb-1">Remarks</h4>
            <p className="text-sm text-slate-700 whitespace-pre-wrap">{grn.remark}</p>
          </div>
        )}
      </div>
    </main>
  );
}
