'use client';

import React, { useEffect } from 'react';
import { useQuery } from '@tanstack/react-query';
import purchaseOrderService from '../../../../services/purchaseOrder.service';
import { useParams } from 'next/navigation';
import { AlertCircle, Loader, Globe } from 'lucide-react';
import { StatusBadge } from '../../../../components/dashboard/StatusBadge';

export default function PurchaseOrderPdfPage() {
  const params = useParams();
  const id = params?.id as string;

  const { data, isLoading, isError } = useQuery({
    queryKey: ['purchase-order', 'details', id],
    queryFn: () => purchaseOrderService.getDetails(id),
    enabled: !!id,
    staleTime: 10 * 60 * 1000
  });

  // Automatically trigger print dialog once data is loaded
  useEffect(() => {
    if (data) {
      const timer = setTimeout(() => {
        window.print();
      }, 800); // Small delay to ensure styles and font assets are loaded
      return () => clearTimeout(timer);
    }
  }, [data]);

  if (isLoading) {
    return (
      <div className="min-h-screen bg-white flex flex-col items-center justify-center text-slate-400 gap-2 font-sans select-none">
        <Loader className="w-8 h-8 animate-spin text-cyan-600" />
        <span className="text-xs font-semibold">Generating print layout...</span>
      </div>
    );
  }

  if (isError || !data) {
    return (
      <div className="min-h-screen bg-white flex flex-col items-center justify-center text-rose-500 gap-2 font-sans select-none">
        <AlertCircle className="w-8 h-8" />
        <span className="text-xs font-semibold">Failed to load purchase order print details.</span>
      </div>
    );
  }

  const formatDate = (dateStr?: string) => {
    if (!dateStr) return 'N/A';
    return new Date(dateStr).toLocaleDateString('en-IN', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric'
    });
  };

  return (
    <div className="min-h-screen bg-slate-100 py-8 px-4 print:bg-white print:py-0 print:px-0 font-serif text-slate-900 flex justify-center">
      
      {/* Printable Sheet (Standard A4 Dimensions) */}
      <div className="w-[800px] bg-white border border-slate-300 print:border-none p-8 print:p-0 shadow-lg print:shadow-none flex flex-col space-y-6">
        
        {/* Print Action Instructions (hidden on print) */}
        <div className="bg-slate-50 border border-slate-200 rounded-lg p-3 flex items-center justify-between print:hidden text-xs font-sans font-medium text-slate-600">
          <span>Print preview generated automatically. If the print prompt did not open:</span>
          <button
            onClick={() => window.print()}
            className="px-3 py-1 bg-cyan-600 hover:bg-cyan-500 text-white rounded text-xs font-semibold cursor-pointer shadow-sm"
          >
            Open Printer Dialog
          </button>
        </div>

        {/* Double-border frame around the whole header details for strict formatting */}
        <div className="border border-slate-950 p-4 space-y-4">
          
          {/* Company Letterhead header */}
          <div className="flex items-start justify-between border-b border-slate-950 pb-4">
            
            {/* Logo and Name */}
            <div className="flex items-center gap-3">
              {/* Globe SVG Logo Replicating tirupati logo */}
              <div className="w-12 h-12 flex items-center justify-center border border-slate-900 rounded-full shrink-0">
                <Globe className="w-7 h-7 text-slate-800" />
              </div>
              <div>
                <h1 className="text-base font-extrabold tracking-tight text-slate-950 uppercase">
                  Tirupati Plastomatics Pvt. Ltd.
                </h1>
                <p className="text-[10px] font-bold text-slate-600 uppercase tracking-wide">
                  Manufacturer of High Quality Compounds & Wires
                </p>
              </div>
            </div>

            {/* Address */}
            <div className="text-right text-[9px] font-bold text-slate-700 leading-tight max-w-[280px]">
              <p>B-141(A), Rd Number 9D, Vishwakarma Industrial Area,</p>
              <p>Jaipur, Rajasthan 302013</p>
              <p className="mt-0.5">Phone: 9829278189</p>
              <p>Email: contact@tirupatiplastomatics.com</p>
              <p>Website: www.tirupatiplastomatics.com</p>
            </div>

          </div>

          {/* Section Document Title */}
          <div className="text-center font-bold text-xs uppercase tracking-wider text-slate-900">
            Purchase Order Details
          </div>

          {/* Metadata Grid */}
          <div className="grid grid-cols-2 gap-x-8 gap-y-3 text-[10px] font-bold text-slate-800 border-t border-slate-900 pt-3">
            <div className="space-y-1">
              <p>Purchase Order No. :- {data.po.po_number}</p>
              <p>Purchase Order Date :- {formatDate(data.po.po_date)}</p>
              <p>GSTIN NO. :- {data.po.gst_number}</p>
              <p>Status :- <span className="uppercase">{data.po.status}</span></p>
            </div>
            <div className="space-y-1">
              {data.po.amendment_no > 0 ? (
                <p>Amendment No :- {data.po.amendment_no} (Date : {formatDate(data.po.amendment_date)})</p>
              ) : (
                <p>Amendment No :- 0</p>
              )}
              <p>Delivery Date :- {formatDate(data.po.delivery_date)}</p>
              <p>Vendor Name :- {data.po.vendor_name}</p>
            </div>
          </div>

        </div>

        {/* Products Title */}
        <div className="text-center font-bold text-[11px] uppercase tracking-wider text-slate-900">
          Products
        </div>

        {/* Products Table */}
        <div className="border border-slate-950">
          <table className="w-full text-left border-collapse text-[10px] font-bold text-slate-900">
            <thead>
              <tr className="border-b border-slate-950 bg-slate-50">
                <th className="px-3 py-2 text-center border-r border-slate-950 w-12">No.</th>
                <th className="px-3 py-2 border-r border-slate-950">Item Description</th>
                <th className="px-3 py-2 text-right border-r border-slate-950 w-24">Order Qty</th>
                <th className="px-3 py-2 text-right border-r border-slate-950 w-24">Pending Qty</th>
                <th className="px-3 py-2 text-right border-r border-slate-950 w-16">Rate</th>
                <th className="px-3 py-2 text-right border-r border-slate-950 w-24">Price (INR)</th>
                <th className="px-3 py-2 text-center border-r border-slate-950 w-12">Tax</th>
                <th className="px-3 py-2 text-right border-r border-slate-950 w-24">Tax Amt</th>
                <th className="px-3 py-2 w-28 text-right">Amount</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-950">
              {data.items.map((item, idx) => (
                <tr key={item.id}>
                  <td className="px-3 py-2 text-center border-r border-slate-950">{idx + 1}</td>
                  <td className="px-3 py-2 border-r border-slate-950">{item.item_name}</td>
                  <td className="px-3 py-2 text-right border-r border-slate-950">{item.order_qty} {item.uom.toUpperCase()}</td>
                  <td className="px-3 py-2 text-right border-r border-slate-950">{item.pending_qty} {item.uom.toUpperCase()}</td>
                  <td className="px-3 py-2 text-right border-r border-slate-950">{item.rate}</td>
                  <td className="px-3 py-2 text-right border-r border-slate-950">
                    {Number(item.price).toLocaleString('en-IN', { minimumFractionDigits: 2 })}
                  </td>
                  <td className="px-3 py-2 text-center border-r border-slate-950">{item.tax_percentage}%</td>
                  <td className="px-3 py-2 text-right border-r border-slate-950">
                    {Number(item.tax_amt).toLocaleString('en-IN', { minimumFractionDigits: 2 })}
                  </td>
                  <td className="px-3 py-2 text-right">
                    {Number(item.amount).toLocaleString('en-IN', { minimumFractionDigits: 2 })}
                  </td>
                </tr>
              ))}
            </tbody>
            <tfoot>
              <tr className="border-t border-slate-950 text-xs font-bold text-slate-950 bg-slate-50/50">
                <td colSpan={5} className="px-3 py-2 text-left uppercase">
                  Tax Excluded
                </td>
                <td colSpan={4} className="px-3 py-2 text-right">
                  Total Amount : ₹{Number(data.po.total_amount).toLocaleString('en-IN', { minimumFractionDigits: 2 })}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>

      </div>

      {/* CSS Styling to hide standard headers/footers during document print */}
      <style jsx global>{`
        @media print {
          body {
            background-color: white !important;
            padding: 0 !important;
            margin: 0 !important;
          }
          @page {
            size: A4;
            margin: 1.5cm;
          }
        }
      `}</style>

    </div>
  );
}
