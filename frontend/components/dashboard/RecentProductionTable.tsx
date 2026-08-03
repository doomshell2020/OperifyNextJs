'use client';

import React, { useState } from 'react';
import { ProductionRecord } from '../../services/dashboard.service';
import { StatusBadge } from './StatusBadge';
import { Search, Hammer, Eye } from 'lucide-react';

interface RecentProductionTableProps {
  data: ProductionRecord[];
}

export const RecentProductionTable: React.FC<RecentProductionTableProps> = ({ data = [] }) => {
  const [searchTerm, setSearchTerm] = useState('');

  const filteredData = data.filter(record => 
    (record.machine_name && record.machine_name.toLowerCase().includes(searchTerm.toLowerCase())) ||
    (record.manpower_day && record.manpower_day.toLowerCase().includes(searchTerm.toLowerCase()))
  );

  return (
    <div className="bg-white border border-slate-200/80 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition duration-200">
      
      {/* Header */}
      <div className="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div className="flex items-center gap-2">
          <div className="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
            <Hammer className="w-4 h-4" />
          </div>
          <div>
            <h3 className="text-xs font-bold text-slate-800 uppercase tracking-wider">Latest Production Orders</h3>
            <p className="text-[10px] text-slate-400 font-medium mt-0.5">Active shop floor releases</p>
          </div>
        </div>

        {/* Search */}
        <div className="relative">
          <Search className="absolute left-2.5 top-2 h-3.5 w-3.5 text-slate-400" />
          <input
            type="text"
            placeholder="Search Machine / Manpower..."
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
              <th className="px-6 py-3">Machine</th>
              <th className="px-6 py-3">Plan Qty</th>
              <th className="px-6 py-3">Manpower (Day)</th>
              <th className="px-6 py-3">Date</th>
              <th className="px-6 py-3">Status</th>
              <th className="px-6 py-3 text-right">Action</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100 text-xs font-medium text-slate-700">
            {filteredData.length > 0 ? (
              filteredData.map((record) => (
                <tr key={record.id} className="hover:bg-slate-50/50 transition cursor-pointer">
                  <td className="px-6 py-4 text-slate-900 font-bold">{record.machine_name || `Machine #${record.id}`}</td>
                  <td className="px-6 py-4 text-slate-600 font-semibold">{record.plan_qty || 'N/A'}</td>
                  <td className="px-6 py-4 text-slate-500">{record.manpower_day || '-'}</td>
                  <td className="px-6 py-4 text-slate-400">
                    {record.date ? new Date(record.date).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: '2-digit' }) : '-'}
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
                  No production runs found
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

    </div>
  );
};
