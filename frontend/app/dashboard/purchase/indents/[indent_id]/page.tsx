'use client';

import React, { useEffect } from 'react';
import { useParams, useSearchParams } from 'next/navigation';
import { useQuery } from '@tanstack/react-query';
import indentService from '../../../../../services/indent.service';
import { Loader2, AlertCircle, Printer, ArrowLeft } from 'lucide-react';
import Link from 'next/link';
import { formatDate } from '../../../../../utils/dateFormatter';

// ─── Helpers ──────────────────────────────────────────────────────────────────



// ─── Print styles injected into head ─────────────────────────────────────────

const PRINT_STYLE = `
  @media print {
    body * { visibility: hidden; }
    #indent-print-area, #indent-print-area * { visibility: visible; }
    #indent-print-area { position: absolute; left: 0; top: 0; width: 100%; }
    .no-print { display: none !important; }
  }
`;

// ─── Main Page ────────────────────────────────────────────────────────────────

export default function IndentDetailPage() {
  const params = useParams();
  const searchParams = useSearchParams();
  const isPreview = searchParams.get('preview') === '1';
  const indent_id = params.indent_id as string;

  const { data, isLoading, isError } = useQuery({
    queryKey: ['indent-detail', indent_id],
    queryFn: () => indentService.getIndentDetail(indent_id),
    enabled: !!indent_id,
  });

  // Auto-print if opened as preview
  useEffect(() => {
    if (isPreview && data && data.items.length > 0) {
      setTimeout(() => window.print(), 600);
    }
  }, [isPreview, data]);

  // Inject print CSS
  useEffect(() => {
    const style = document.createElement('style');
    style.innerHTML = PRINT_STYLE;
    document.head.appendChild(style);
    return () => document.head.removeChild(style);
  }, []);

  if (isLoading) {
    return (
      <div className="flex items-center justify-center min-h-[60vh]">
        <Loader2 className="w-8 h-8 text-cyan-500 animate-spin" />
      </div>
    );
  }

  if (isError || !data) {
    return (
      <div className="flex flex-col items-center justify-center min-h-[60vh] gap-3 text-center">
        <div className="p-3 bg-rose-50 rounded-full text-rose-500">
          <AlertCircle className="w-8 h-8" />
        </div>
        <p className="text-sm font-semibold text-slate-700">Indent not found</p>
        <Link
          href="/dashboard/purchase/indents"
          className="text-xs text-cyan-600 hover:underline"
        >
          ← Back to Indents
        </Link>
      </div>
    );
  }

  const { items, is_temp } = data;
  const firstItem = items[0];
  const createdBy = firstItem?.created_by ?? 'N/A';
  const createdDate = firstItem?.added_time ?? null;
  const totalQty = items.reduce((s, i) => s + Number(i.quantity), 0);

  return (
    <div className="max-w-3xl mx-auto">
      {/* Toolbar — hidden on print */}
      <div className="no-print flex items-center gap-3 mb-6">
        <Link
          href="/dashboard/purchase/indents"
          className="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 transition-colors"
        >
          <ArrowLeft className="w-5 h-5" />
        </Link>
        <div className="flex-1">
          <h1 className="text-lg font-bold text-slate-900">
            Indent #{indent_id}
            {is_temp && (
              <span className="ml-2 text-xs font-semibold text-amber-600 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">
                Preview (Not Finalized)
              </span>
            )}
          </h1>
          <p className="text-xs text-slate-400 mt-0.5">Purchase Requisition</p>
        </div>
        <button
          onClick={() => window.print()}
          className="flex items-center gap-2 h-9 px-4 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-xs font-semibold transition-colors shadow-sm"
        >
          <Printer className="w-3.5 h-3.5" />
          Print
        </button>
      </div>

      {/* ── Printable Area ─────────────────────────────────────────────────── */}
      <div
        id="indent-print-area"
        className="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden"
        style={{ fontFamily: 'Arial, Helvetica, sans-serif' }}
      >
        {/* Company Header */}
        <div className="flex items-start justify-between px-8 py-6 border-b border-slate-200">
          <div>
            <div className="text-2xl font-extrabold text-slate-900 tracking-tight">TIRUPATI</div>
            <div className="text-xs text-slate-500 font-medium mt-0.5">ERP Console</div>
          </div>
          <div className="text-right text-xs text-slate-600 space-y-0.5">
            <div className="font-semibold text-slate-800">Purchase Requisition</div>
            <div>Indent No.: <span className="font-bold text-cyan-700">#{indent_id}</span></div>
            <div>From: <span className="font-semibold">{createdBy}</span></div>
            <div>Date: <span className="font-semibold">{formatDate(createdDate)}</span></div>
          </div>
        </div>

        {/* Items Table */}
        <div className="px-8 py-5">
          <table className="w-full border-collapse text-sm">
            <thead>
              <tr className="bg-slate-800 text-white">
                <th className="px-4 py-3 text-left font-semibold rounded-tl-lg w-12">S.No.</th>
                <th className="px-4 py-3 text-left font-semibold">Item</th>
                <th className="px-4 py-3 text-left font-semibold">Category</th>
                <th className="px-4 py-3 text-center font-semibold rounded-tr-lg">Qty Requested</th>
              </tr>
            </thead>
            <tbody>
              {items.map((item, idx) => (
                <tr
                  key={item.id}
                  className={idx % 2 === 0 ? 'bg-white' : 'bg-slate-50'}
                >
                  <td className="px-4 py-3 text-slate-500">{idx + 1}</td>
                  <td className="px-4 py-3 font-medium text-slate-800">
                    {item.item_name}
                    {item.size_name && (
                      <span className="ml-1.5 text-slate-400 text-xs">({item.size_name})</span>
                    )}
                  </td>
                  <td className="px-4 py-3 text-slate-600">{item.category_name || '—'}</td>
                  <td className="px-4 py-3 text-center font-bold text-rose-600">{item.quantity}</td>
                </tr>
              ))}
            </tbody>
            <tfoot>
              <tr className="border-t-2 border-slate-300 bg-slate-50">
                <td colSpan={3} className="px-4 py-3 text-right font-bold text-slate-700">
                  Total Quantity
                </td>
                <td className="px-4 py-3 text-center font-extrabold text-slate-900 text-base">
                  {totalQty}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>

        {/* Signature Row */}
        <div className="flex items-end justify-between px-8 py-8 mt-4 border-t border-slate-200">
          <div className="text-center">
            <div className="border-t-2 border-slate-400 w-48 mb-1" />
            <p className="text-xs text-slate-500 font-medium">
              Signature of Sanctioning Authority
            </p>
          </div>
          <div className="text-center">
            <div className="border-t-2 border-slate-400 w-48 mb-1" />
            <p className="text-xs text-slate-500 font-medium">
              Signature of Person Requesting Items
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}
