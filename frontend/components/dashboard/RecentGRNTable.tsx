'use client';

import React, { useState } from 'react';
import { GrnRecord } from '../../services/dashboard.service';
import { StatusBadge } from './StatusBadge';
import { PurchaseOrderLink } from '../PurchaseOrderLink';
import { Search, PackageCheck, Eye } from 'lucide-react';
import { formatDate } from '../../utils/dateFormatter';

interface RecentGRNTableProps {
  data: GrnRecord[];
}

export const RecentGRNTable: React.FC<RecentGRNTableProps> = ({ data = [] }) => {
  const [searchTerm, setSearchTerm] = useState('');

  const filteredData = data.filter(record => 
    record.po_no.toLowerCase().includes(searchTerm.toLowerCase()) ||
    (record.bill_no && record.bill_no.toLowerCase().includes(searchTerm.toLowerCase())) ||
    (record.vendor_name && record.vendor_name.toLowerCase().includes(searchTerm.toLowerCase()))
  );

  return (
    <div className="bg-white border border-slate-200/80 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition duration-200">
      
      {/* Header */}
      <div className="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div className="flex items-center gap-2">
          <div className="p-2 bg-amber-50 text-amber-600 rounded-lg">
            <PackageCheck className="w-4 h-4" />
          </div>
          <div>
            <h3 className="text-xs font-bold text-slate-800 uppercase tracking-wider">Latest Goods Received (GRN)</h3>
            <p className="text-[10px] text-slate-400 font-medium mt-0.5">Incoming warehouse receipts</p>
          </div>
        </div>

        {/* Search */}
        <div className="relative">
          <Search className="absolute left-2.5 top-2 h-3.5 w-3.5 text-slate-400" />
          <input
            type="text"
            placeholder="Search GRN / PO..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="pl-8 pr-3 py-1.5 w-full sm:w-52 bg-slate-50 border border-slate-200 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 rounded-lg text-slate-800 placeholder-slate-400 text-xs outline-none transition"
          />
        </div>
      </div>

      {/* Table */}
      <div className="overflow-x-auto">
        <table className="w-full text-left border-collapse">
          <thead>
            <tr className="bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
              <th className="px-6 py-3">PO Reference</th>
              <th className="px-6 py-3">Bill Number</th>
              <th className="px-6 py-3">Vendor Name</th>
              <th className="px-6 py-3">Amount</th>
              <th className="px-6 py-3">Inward Date</th>
              <th className="px-6 py-3">Status</th>
              <th className="px-6 py-3 text-right">Action</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100 text-xs font-medium text-slate-700">
            {filteredData.length > 0 ? (
              filteredData.map((record) => (
                <tr key={record.id} className="hover:bg-slate-50/50 transition cursor-pointer">
                  <td className="px-6 py-4">
                    <PurchaseOrderLink id={record.po_no} poNumber={record.po_no} />
                  </td>
                  <td className="px-6 py-4 text-slate-600">{record.bill_no || '-'}</td>
                  <td className="px-6 py-4 text-slate-600">{record.vendor_name || 'N/A'}</td>
                  <td className="px-6 py-4 text-slate-900 font-semibold">
                    ₹{Number(record.amount).toLocaleString('en-IN', { minimumFractionDigits: 2 })}
                  </td>
                  <td className="px-6 py-4 text-slate-400">
                    {record.date ? formatDate(record.date) : '-'}
                  </td>
                  <td className="px-6 py-4">
                    <StatusBadge status={record.status} />
                  </td>
                  <td className="px-6 py-4 text-right">
                    <button className="p-1.5 text-slate-400 hover:text-cyan-600 bg-slate-50 border border-slate-100 hover:border-slate-200 rounded-lg transition cursor-pointer">
                      <Eye className="w-3.5 h-3.5" />
                    </button>
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td colSpan={7} className="text-center py-10 text-slate-400 text-xs">
                  No GRN receipts found
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

    </div>
  );
};
