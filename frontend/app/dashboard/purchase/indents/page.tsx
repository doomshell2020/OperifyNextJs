'use client';

import React, { useState, useCallback } from 'react';
import Link from 'next/link';
import { useQuery } from '@tanstack/react-query';
import indentService, { IndentSummary } from '../../../../services/indent.service';
import {
import { DatePicker } from '../../../../components/ui/DatePicker';
import { formatDate } from '../../../../utils/dateFormatter';
  ListTodo,
  Plus,
  Search,
  FileText,
  ShoppingBag,
  RefreshCw,
  AlertCircle,
  ChevronDown,
  ChevronRight,
  Calendar,
  Package,
  Clock,
  User,
} from 'lucide-react';

// ─── Helpers ──────────────────────────────────────────────────────────────────

function formatDate(dateStr: string) {
  if (!dateStr) return '—';
  return formatDate(dateStr);
}

// ─── Nested Item Dropdown ────────────────────────────────────────────────────

function ItemsDropdown({ items }: { items: IndentSummary['items'] }) {
  const [open, setOpen] = useState(false);
  return (
    <div>
      <button
        onClick={() => setOpen(o => !o)}
        className="flex items-center gap-1.5 text-xs font-medium text-slate-600 hover:text-cyan-700 transition-colors"
      >
        <Package className="w-3.5 h-3.5" />
        <span>{items.length} item{items.length !== 1 ? 's' : ''}</span>
        {open ? <ChevronDown className="w-3 h-3" /> : <ChevronRight className="w-3 h-3" />}
      </button>
      {open && (
        <div className="mt-2 rounded-lg border border-slate-100 overflow-hidden shadow-sm">
          <table className="w-full text-xs">
            <thead>
              <tr className="bg-slate-50 text-slate-500 uppercase tracking-wide">
                <th className="px-3 py-2 text-left font-semibold">Item</th>
                <th className="px-3 py-2 text-center font-semibold">Req. Qty</th>
                <th className="px-3 py-2 text-center font-semibold text-emerald-600">In Stock</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-50">
              {items.map(item => (
                <tr key={item.id} className="bg-white hover:bg-slate-50 transition-colors">
                  <td className="px-3 py-2 font-medium text-slate-800">
                    {item.item_name}
                    {item.size_name && (
                      <span className="ml-1 text-slate-400">({item.size_name})</span>
                    )}
                  </td>
                  <td className="px-3 py-2 text-center font-bold text-rose-600">{item.quantity}</td>
                  <td className="px-3 py-2 text-center font-bold text-emerald-600">
                    {item.stock_in_hand ?? '—'}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}

// ─── Search Bar ───────────────────────────────────────────────────────────────

interface Filters {
  indent_id: string;
  date_from: string;
  date_to: string;
}

const EMPTY_FILTERS: Filters = { indent_id: '', date_from: '', date_to: '' };

function SearchBar({
  filters,
  onChange,
  onSearch,
  onReset,
}: {
  filters: Filters;
  onChange: (f: Filters) => void;
  onSearch: () => void;
  onReset: () => void;
}) {
  return (
    <div className="bg-white border border-slate-200 rounded-xl p-4 flex flex-wrap gap-3 items-end shadow-sm">
      <div className="flex flex-col gap-1 min-w-[140px]">
        <label className="text-[10px] font-semibold text-slate-500 uppercase tracking-wide">
          Indent ID
        </label>
        <input
          type="text"
          value={filters.indent_id}
          onChange={e => onChange({ ...filters, indent_id: e.target.value })}
          placeholder="e.g. 1023"
          className="h-9 px-3 rounded-lg border border-slate-200 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500"
        />
      </div>
      <div className="flex flex-col gap-1 min-w-[140px]">
        <label className="text-[10px] font-semibold text-slate-500 uppercase tracking-wide">
          Date From
        </label>
        <DatePicker  
          value={filters.date_from}
          onChange={e => onChange({ ...filters, date_from: e.target.value })}
          className="h-9 px-3 rounded-lg border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500"
        />
      </div>
      <div className="flex flex-col gap-1 min-w-[140px]">
        <label className="text-[10px] font-semibold text-slate-500 uppercase tracking-wide">
          Date To
        </label>
        <DatePicker  
          value={filters.date_to}
          onChange={e => onChange({ ...filters, date_to: e.target.value })}
          className="h-9 px-3 rounded-lg border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500"
        />
      </div>
      <button
        onClick={onSearch}
        className="h-9 px-4 flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-xs font-semibold transition-colors shadow-sm"
      >
        <Search className="w-3.5 h-3.5" />
        Search
      </button>
      <button
        onClick={onReset}
        className="h-9 px-4 flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition-colors"
      >
        <RefreshCw className="w-3.5 h-3.5" />
        Reset
      </button>
    </div>
  );
}

// ─── Main Page ────────────────────────────────────────────────────────────────

export default function IndentsListPage() {
  const [filters, setFilters] = useState<Filters>(EMPTY_FILTERS);
  const [appliedFilters, setAppliedFilters] = useState<Filters>(EMPTY_FILTERS);

  const { data, isLoading, isError, refetch } = useQuery({
    queryKey: ['indents', appliedFilters],
    queryFn: () =>
      indentService.listIndents({
        indent_id: appliedFilters.indent_id || undefined,
        date_from: appliedFilters.date_from || undefined,
        date_to: appliedFilters.date_to || undefined,
      }),
  });

  const handleSearch = useCallback(() => setAppliedFilters({ ...filters }), [filters]);
  const handleReset = useCallback(() => {
    setFilters(EMPTY_FILTERS);
    setAppliedFilters(EMPTY_FILTERS);
  }, []);

  const indents = data ?? [];

  return (
    <div className="max-w-7xl mx-auto space-y-5">
      {/* Page Header */}
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-3">
          <div className="h-9 w-9 rounded-lg bg-cyan-600 flex items-center justify-center shadow-sm">
            <ListTodo className="w-5 h-5 text-white" />
          </div>
          <div>
            <h1 className="text-lg font-bold text-slate-900 leading-none">Indent Manager</h1>
            <p className="text-xs text-slate-500 mt-0.5">Purchase Requisitions</p>
          </div>
        </div>
        <Link
          href="/dashboard/purchase/indents/new"
          className="flex items-center gap-2 h-9 px-4 bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-600 hover:to-cyan-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-all"
        >
          <Plus className="w-4 h-4" />
          New Indent
        </Link>
      </div>

      {/* Filters */}
      <SearchBar
        filters={filters}
        onChange={setFilters}
        onSearch={handleSearch}
        onReset={handleReset}
      />

      {/* Table Card */}
      <div className="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        {isLoading && (
          <div className="divide-y divide-slate-100">
            {[...Array(6)].map((_, i) => (
              <div key={i} className="px-6 py-4 flex gap-4 animate-pulse">
                <div className="h-4 w-12 bg-slate-100 rounded" />
                <div className="h-4 w-24 bg-slate-100 rounded" />
                <div className="h-4 flex-1 bg-slate-100 rounded" />
                <div className="h-4 w-16 bg-slate-100 rounded" />
                <div className="h-4 w-28 bg-slate-100 rounded" />
              </div>
            ))}
          </div>
        )}

        {isError && !isLoading && (
          <div className="flex flex-col items-center justify-center py-16 text-center gap-3">
            <div className="p-3 bg-rose-50 rounded-full text-rose-500">
              <AlertCircle className="w-8 h-8" />
            </div>
            <p className="text-sm font-semibold text-slate-700">Failed to load indents</p>
            <button
              onClick={() => refetch()}
              className="text-xs text-cyan-600 hover:underline flex items-center gap-1"
            >
              <RefreshCw className="w-3 h-3" /> Retry
            </button>
          </div>
        )}

        {!isLoading && !isError && indents.length === 0 && (
          <div className="flex flex-col items-center justify-center py-20 gap-3 text-slate-400">
            <ListTodo className="w-10 h-10" />
            <p className="text-sm font-medium">No indents found</p>
            <Link
              href="/dashboard/purchase/indents/new"
              className="text-xs text-cyan-600 hover:underline"
            >
              Create your first indent →
            </Link>
          </div>
        )}

        {!isLoading && !isError && indents.length > 0 && (
          <table className="w-full text-sm">
            <thead>
              <tr className="bg-slate-50 border-b border-slate-200">
                {['#', 'Indent ID', 'Items', 'Total Qty', 'Created By', 'Date', 'Actions'].map(h => (
                  <th
                    key={h}
                    className="px-5 py-3 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap"
                  >
                    {h}
                  </th>
                ))}
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {indents.map((indent, idx) => (
                <tr key={indent.indent_id} className="hover:bg-slate-50/60 transition-colors">
                  <td className="px-5 py-4 text-xs text-slate-400 font-medium">{idx + 1}</td>
                  <td className="px-5 py-4">
                    <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-cyan-50 text-cyan-700 font-bold text-xs border border-cyan-100">
                      <ListTodo className="w-3 h-3" />#{indent.indent_id}
                    </span>
                  </td>
                  <td className="px-5 py-4 max-w-xs">
                    <ItemsDropdown items={indent.items} />
                  </td>
                  <td className="px-5 py-4 text-center">
                    <span className="font-bold text-rose-600 text-sm">{indent.total_qty}</span>
                  </td>
                  <td className="px-5 py-4">
                    <div className="flex items-center gap-1.5 text-xs text-slate-600">
                      <User className="w-3 h-3 text-slate-400" />
                      {indent.created_by || '—'}
                    </div>
                  </td>
                  <td className="px-5 py-4">
                    <div className="flex items-center gap-1.5 text-xs text-slate-600">
                      <Calendar className="w-3 h-3 text-slate-400" />
                      {formatDate(indent.added_time)}
                    </div>
                  </td>
                  <td className="px-5 py-4">
                    <div className="flex items-center gap-2">
                      <Link
                        href={`/dashboard/purchase/indents/${indent.indent_id}`}
                        target="_blank"
                        title="View / Print Indent"
                        className="p-1.5 rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-100 transition-colors"
                      >
                        <FileText className="w-4 h-4" />
                      </Link>
                      <Link
                        href={`/dashboard/purchase/orders?indent_id=${indent.indent_id}`}
                        title="Create Purchase Order"
                        className="p-1.5 rounded-lg bg-cyan-50 text-cyan-600 hover:bg-cyan-100 transition-colors"
                      >
                        <ShoppingBag className="w-4 h-4" />
                      </Link>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </div>

      {/* Footer count */}
      {!isLoading && !isError && indents.length > 0 && (
        <div className="flex items-center gap-2 text-xs text-slate-400">
          <Clock className="w-3 h-3" />
          Showing {indents.length} indent{indents.length !== 1 ? 's' : ''}
        </div>
      )}
    </div>
  );
}
