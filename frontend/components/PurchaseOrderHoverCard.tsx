'use client';

import React from 'react';
import { PurchaseOrderHoverData } from '../services/purchaseOrder.service';
import { StatusBadge } from './dashboard/StatusBadge';
import { Eye, Edit3, Printer, Download, Copy, Check, Loader, Phone, Mail, User, AlertCircle } from 'lucide-react';
import { formatDate } from '../utils/dateFormatter';

interface PurchaseOrderHoverCardProps {
  data?: PurchaseOrderHoverData;
  isLoading: boolean;
  isError: boolean;
  copied: boolean;
  onCopy: (text: string) => void;
  onViewDetails: () => void;
}

export const PurchaseOrderHoverCard: React.FC<PurchaseOrderHoverCardProps> = ({
  data,
  isLoading,
  isError,
  copied,
  onCopy,
  onViewDetails
}) => {
  if (isLoading) {
    return (
      <div className="w-80 p-5 bg-white border border-slate-200 shadow-xl rounded-xl space-y-4 animate-pulse select-none">
        <div className="flex items-center justify-between">
          <div className="h-4 w-24 bg-slate-200 rounded"></div>
          <div className="h-4 w-12 bg-slate-200 rounded-full"></div>
        </div>
        <div className="space-y-2">
          <div className="h-3 w-40 bg-slate-200 rounded"></div>
          <div className="h-3 w-32 bg-slate-200 rounded"></div>
        </div>
        <div className="h-px bg-slate-100"></div>
        <div className="grid grid-cols-2 gap-3">
          <div className="h-8 bg-slate-100 rounded"></div>
          <div className="h-8 bg-slate-100 rounded"></div>
        </div>
      </div>
    );
  }

  if (isError || !data) {
    return (
      <div className="w-80 p-5 bg-white border border-slate-200 shadow-xl rounded-xl flex items-start gap-3 select-none">
        <AlertCircle className="w-5 h-5 text-rose-500 shrink-0 mt-0.5" />
        <div>
          <h4 className="text-xs font-bold text-slate-800 uppercase tracking-wider">Sync Failure</h4>
          <p className="text-[10px] text-slate-400 font-medium mt-0.5">Could not fetch purchase order hover metadata.</p>
        </div>
      </div>
    );
  }

  const formattedDate = (dateStr?: string) => {
    if (!dateStr) return 'N/A';
    return formatDate(dateStr);
  };

  return (
    <div className="w-[340px] bg-white border border-slate-200/90 shadow-xl shadow-slate-200/80 rounded-xl p-5 select-none text-xs font-medium text-slate-600 space-y-4 transition duration-150 ease-out transform scale-100">
      
      {/* Title Header */}
      <div className="flex items-center justify-between border-b border-slate-100 pb-3">
        <div>
          <h4 className="text-sm font-extrabold text-slate-900 tracking-tight">PO #{data.po_number}</h4>
          <p className="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">
            Date: {formattedDate(data.po_date)}
          </p>
        </div>
        <StatusBadge status={data.status} />
      </div>

      {/* Main Vendor Details */}
      <div className="space-y-2">
        <div>
          <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Vendor Connection</span>
          <span className="text-xs font-bold text-slate-800 block mt-0.5">{data.vendor_name}</span>
          <span className="text-[10px] text-cyan-600 font-bold uppercase tracking-wide">Code: {data.vendor_code}</span>
        </div>

        {/* Contacts details */}
        <div className="grid grid-cols-1 gap-1 text-[11px] pt-1 text-slate-500">
          <div className="flex items-center gap-1.5">
            <User className="w-3.5 h-3.5 text-slate-400 shrink-0" />
            <span className="truncate">{data.contact_person}</span>
          </div>
          <div className="flex items-center gap-1.5">
            <Phone className="w-3.5 h-3.5 text-slate-400 shrink-0" />
            <span>{data.mobile}</span>
          </div>
          <div className="flex items-center gap-1.5">
            <Mail className="w-3.5 h-3.5 text-slate-400 shrink-0" />
            <span className="truncate">{data.email}</span>
          </div>
        </div>
      </div>

      {/* Numerical Metrics */}
      <div className="grid grid-cols-2 gap-3 bg-slate-50 border border-slate-100 rounded-lg p-3">
        <div>
          <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Quantity</span>
          <span className="text-xs font-bold text-slate-800 mt-0.5 block">
            {Number(data.quantity).toLocaleString()} units
          </span>
        </div>
        <div>
          <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Amount</span>
          <span className="text-xs font-bold text-slate-800 mt-0.5 block text-indigo-600">
            ₹{Number(data.amount).toLocaleString('en-IN', { minimumFractionDigits: 2 })}
          </span>
        </div>
        <div className="col-span-2 border-t border-slate-100/50 pt-2 flex items-center justify-between text-[11px]">
          <span className="text-slate-400">Delivery Date:</span>
          <span className="font-bold text-slate-800">{formattedDate(data.delivery_date)}</span>
        </div>
      </div>

      {/* Meta created type */}
      <div className="text-[10px] text-slate-400 flex items-center justify-between border-t border-slate-50 pt-3">
        <span>Created By: <strong>{data.created_by}</strong></span>
      </div>

      {/* Quick Action Buttons Grid */}
      <div className="grid grid-cols-2 gap-2 pt-1 border-t border-slate-50">
        <button
          onClick={onViewDetails}
          className="flex items-center justify-center gap-1.5 px-2.5 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg text-slate-700 hover:text-slate-900 transition cursor-pointer text-[10px] font-bold uppercase tracking-wider"
        >
          <Eye className="w-3.5 h-3.5" />
          View
        </button>

        <button
          onClick={() => alert(`Editing PO: ${data.po_number}`)}
          className="flex items-center justify-center gap-1.5 px-2.5 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg text-slate-700 hover:text-slate-900 transition cursor-pointer text-[10px] font-bold uppercase tracking-wider"
        >
          <Edit3 className="w-3.5 h-3.5" />
          Edit
        </button>

        <button
          onClick={() => alert(`Printing PO: ${data.po_number}`)}
          className="flex items-center justify-center gap-1.5 px-2.5 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg text-slate-700 hover:text-slate-900 transition cursor-pointer text-[10px] font-bold uppercase tracking-wider"
        >
          <Printer className="w-3.5 h-3.5" />
          Print
        </button>

        <button
          onClick={() => window.open(`/purchase-orders/${data.id}/pdf`, '_blank')}
          className="flex items-center justify-center gap-1.5 px-2.5 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg text-slate-700 hover:text-slate-900 transition cursor-pointer text-[10px] font-bold uppercase tracking-wider"
        >
          <Download className="w-3.5 h-3.5" />
          PDF
        </button>

        <button
          onClick={() => onCopy(data.po_number)}
          className="col-span-2 flex items-center justify-center gap-1.5 px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 hover:text-indigo-900 border border-indigo-100 rounded-lg transition cursor-pointer text-[10px] font-bold uppercase tracking-wider"
        >
          {copied ? (
            <>
              <Check className="w-3.5 h-3.5 text-emerald-600 animate-scale" />
              Copied!
            </>
          ) : (
            <>
              <Copy className="w-3.5 h-3.5" />
              Copy PO Number
            </>
          )}
        </button>
      </div>

    </div>
  );
};
