'use client';

import React from 'react';
import { useQuery } from '@tanstack/react-query';
import grnInspectionService from '../../../../../../services/grnInspection.service';
import { useParams, useRouter } from 'next/navigation';
import { ArrowLeft, Printer, Box, Loader, AlertCircle } from 'lucide-react';
import { formatQty, formatAmt } from '@/utils/formatters';

export default function ViewGrnInspectionPage() {
  const params = useParams();
  const router = useRouter();
  const id = params.id as string;

  const { data, isLoading, isError } = useQuery({
    queryKey: ['grn-inspection-details', id],
    queryFn: () => grnInspectionService.getDetails(id),
    enabled: !!id
  });

  if (isLoading) {
    return (
      <div className="flex flex-col items-center justify-center min-h-[60vh]">
        <Loader className="w-10 h-10 animate-spin text-cyan-600 mb-4" />
        <p className="text-slate-600 font-medium">Loading inspection details...</p>
      </div>
    );
  }

  if (isError || !data) {
    return (
      <div className="flex flex-col items-center justify-center min-h-[60vh] text-red-500">
        <AlertCircle className="w-12 h-12 mb-4 opacity-80" />
        <p className="text-lg font-semibold">Inspection not found or error loading details</p>
        <button onClick={() => router.back()} className="mt-4 px-4 py-2 bg-slate-100 text-slate-700 rounded-md hover:bg-slate-200 transition">Go Back</button>
      </div>
    );
  }

  const handlePrint = () => {
    window.print();
  };

  return (
    <main className="max-w-5xl w-full mx-auto px-6 py-8 space-y-6 print:py-0 print:px-0 print:space-y-4">
      {/* Header - Hidden on Print */}
      <div className="flex justify-between items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm print:hidden">
        <div className="flex items-center gap-3">
          <button onClick={() => router.back()} className="p-2 hover:bg-slate-100 rounded-full transition">
            <ArrowLeft className="w-5 h-5 text-slate-600" />
          </button>
          <div>
            <h1 className="text-xl font-bold text-slate-900">Inspection #{data.inspection_id}</h1>
            <p className="text-sm text-slate-500">View GRN Inspection Details</p>
          </div>
        </div>
        <button onClick={handlePrint} className="flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-md font-medium shadow-sm transition">
          <Printer className="w-4 h-4" /> Print Document
        </button>
      </div>

      {/* Print Document Container */}
      <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden print:border-none print:shadow-none print:rounded-none">
        
        {/* Document Header */}
        <div className="p-8 border-b border-slate-200 flex justify-between items-start print:pb-4">
          <div>
            <h2 className="text-2xl font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
              <Box className="w-8 h-8 text-cyan-600 print:text-black" /> GRN INSPECTION
            </h2>
            <div className="mt-4 space-y-1 text-sm text-slate-600">
              <p><span className="font-semibold text-slate-800">Inspection No:</span> {data.inspection_id}</p>
              <p><span className="font-semibold text-slate-800">PO Number:</span> {data.po_id}</p>
              <p><span className="font-semibold text-slate-800">Inward Date:</span> {new Date(data.inwarddate).toLocaleDateString()}</p>
            </div>
          </div>
          <div className="text-right">
            <h3 className="font-bold text-slate-800 text-lg">{data.vendor_name}</h3>
            <p className="text-sm text-slate-600 mt-1 max-w-[250px]">{data.vendor_address || 'Address not provided'}</p>
            <p className="text-sm text-slate-600 mt-1"><span className="font-semibold">GST:</span> {data.gst_number || 'N/A'}</p>
            <p className="text-sm text-slate-600 mt-1"><span className="font-semibold">Bill No:</span> {data.bill_no}</p>
            <p className="text-sm text-slate-600"><span className="font-semibold">Bill Date:</span> {new Date(data.bill_date).toLocaleDateString()}</p>
          </div>
        </div>

        {/* Item Details */}
        <div className="p-8 print:py-4">
          <h3 className="text-lg font-bold text-slate-800 mb-4 uppercase tracking-wide text-sm border-b border-slate-200 pb-2">Item Details</h3>
          <table className="w-full text-left text-sm border border-slate-200">
            <thead className="bg-slate-50">
              <tr>
                <th className="p-3 border-b border-slate-200 font-semibold text-slate-700">S.No</th>
                <th className="p-3 border-b border-slate-200 font-semibold text-slate-700">Item Description</th>
                <th className="p-3 border-b border-slate-200 font-semibold text-slate-700 text-center">Qty</th>
                <th className="p-3 border-b border-slate-200 font-semibold text-slate-700 text-right">Rate</th>
                <th className="p-3 border-b border-slate-200 font-semibold text-slate-700 text-right">Tax</th>
                <th className="p-3 border-b border-slate-200 font-semibold text-slate-700 text-right">Amount</th>
              </tr>
            </thead>
            <tbody>
              {data.items.map((item: any, idx: number) => (
                <tr key={idx} className="border-b border-slate-100">
                  <td className="p-3 text-slate-600">{idx + 1}</td>
                  <td className="p-3 font-medium text-slate-800">{item.item_name}</td>
                  <td className="p-3 text-center text-slate-600">{formatQty(item.quantity)} <span className="text-xs text-slate-400 ml-1">{item.unit_name}</span></td>
                  <td className="p-3 text-right text-slate-600">{formatAmt(item.rate)}</td>
                  <td className="p-3 text-right text-slate-600">{formatAmt(item.tax)}</td>
                  <td className="p-3 text-right font-semibold text-slate-800">{formatAmt(item.amount)}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {/* Summary Footer */}
        <div className="p-8 bg-slate-50 border-t border-slate-200 flex justify-end print:bg-white print:border-none">
          <div className="w-72 space-y-3 text-sm">
            <div className="flex justify-between border-b border-slate-200 pb-2">
              <span className="font-semibold text-slate-600">Total Quantity:</span>
              <span className="font-bold text-slate-800">{formatQty(data.total_qty)}</span>
            </div>
            <div className="flex justify-between border-b border-slate-200 pb-2">
              <span className="font-semibold text-slate-600">Total Tax:</span>
              <span className="font-bold text-slate-800">₹ {formatAmt(data.total_tax)}</span>
            </div>
            <div className="flex justify-between pt-2">
              <span className="text-lg font-black text-slate-800">Grand Total:</span>
              <span className="text-lg font-black text-cyan-700">₹ {formatAmt(data.total_amt)}</span>
            </div>
          </div>
        </div>

        {/* Remarks Section */}
        {data.remark && (
          <div className="p-8 border-t border-slate-200">
            <h4 className="font-bold text-slate-800 mb-2">Remarks:</h4>
            <p className="text-slate-600 bg-slate-50 p-4 rounded-md border border-slate-200">{data.remark}</p>
          </div>
        )}

      </div>
    </main>
  );
}
