'use client';

import React, { useState } from 'react';
import { InspectionRecord } from '../../services/dashboard.service';
import { StatusBadge } from './StatusBadge';
import { Search, ClipboardCheck, Eye } from 'lucide-react';
import { formatDate } from '../../utils/dateFormatter';

interface RecentInspectionTableProps {
  data: InspectionRecord[];
}

export const RecentInspectionTable: React.FC<RecentInspectionTableProps> = ({ data = [] }) => {
  const [searchTerm, setSearchTerm] = useState('');

  const filteredData = data.filter(record => 
    (record.name && record.name.toLowerCase().includes(searchTerm.toLowerCase())) ||
    (record.remark && record.remark.toLowerCase().includes(searchTerm.toLowerCase()))
  );

  return (
    <div className="bg-white border border-slate-200/80 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition duration-200">
      
      {/* Header */}
      <div className="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div className="flex items-center gap-2">
          <div className="p-2 bg-purple-50 text-purple-600 rounded-lg">
            <ClipboardCheck className="w-4 h-4" />
          </div>
          <div>
            <h3 className="text-xs font-bold text-slate-800 uppercase tracking-wider">Latest QC Inspections</h3>
            <p className="text-[10px] text-slate-400 font-medium mt-0.5">Quality audit approvals</p>
          </div>
        </div>

        {/* Search */}
        <div className="relative">
          <Search className="absolute left-2.5 top-2 h-3.5 w-3.5 text-slate-400" />
          <input
            type="text"
            placeholder="Search Inspection / Remark..."
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
              <th className="px-6 py-3">Inspection Name</th>
              <th className="px-6 py-3">Work Order</th>
              <th className="px-6 py-3">Inspection Date</th>
              <th className="px-6 py-3">Remarks</th>
              <th className="px-6 py-3">Status</th>
              <th className="px-6 py-3 text-right">Action</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100 text-xs font-medium text-slate-700">
            {filteredData.length > 0 ? (
              filteredData.map((record) => (
                <tr key={record.id} className="hover:bg-slate-50/50 transition cursor-pointer">
                  <td className="px-6 py-4 text-slate-900 font-bold">{record.name || 'QC Inspection Report'}</td>
                  <td className="px-6 py-4 text-slate-600">WO-{record.work_order_no || 'N/A'}</td>
                  <td className="px-6 py-4 text-slate-400">
                    {record.date ? formatDate(record.date) : '-'}
                  </td>
                  <td className="px-6 py-4 text-slate-500 italic max-w-[150px] truncate" title={record.remark}>
                    {record.remark || 'No remarks'}
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
                <td colSpan={6} className="text-center py-10 text-slate-400 text-xs">
                  No inspection logs found
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

    </div>
  );
};
