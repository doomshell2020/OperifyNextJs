'use client';

import React from 'react';
import { useAuth } from '../../contexts/AuthContext';
import { Database, Filter, CalendarCheck } from 'lucide-react';

export const DashboardFilters: React.FC = () => {
  const { user } = useAuth();
  
  // Static years for mock selector
  const financialYears = ['2026-27', '2025-26', '2024-25'];
  
  return (
    <div className="bg-white border border-slate-200 rounded-xl p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4 shadow-sm">
      
      {/* Filters label */}
      <div className="flex items-center gap-2">
        <div className="p-1.5 bg-slate-100 rounded-lg text-slate-500">
          <Filter className="w-4 h-4" />
        </div>
        <div>
          <h2 className="text-xs font-bold text-slate-800 uppercase tracking-wider">Console Context</h2>
          <p className="text-[10px] text-slate-400 font-medium">Tenant routing & context parameters</p>
        </div>
      </div>

      {/* Selectors grid */}
      <div className="flex flex-wrap items-center gap-3">
        
        {/* Tenant DB Switcher */}
        <div className="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5">
          <Database className="w-3.5 h-3.5 text-cyan-600" />
          <span className="text-xs font-semibold text-slate-700">Database:</span>
          <span className="text-xs font-bold text-slate-900 uppercase">{user?.db || 'central'}</span>
        </div>

        {/* Financial Year Selector */}
        <div className="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5">
          <CalendarCheck className="w-3.5 h-3.5 text-purple-600" />
          <span className="text-xs font-semibold text-slate-700">Financial Year:</span>
          <select className="bg-transparent text-xs font-bold text-slate-950 outline-none border-none cursor-pointer pr-1">
            {financialYears.map(yr => (
              <option key={yr} value={yr}>{yr}</option>
            ))}
          </select>
        </div>

      </div>

    </div>
  );
};
