'use client';

import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { emdService, EmdRecord, EmdDetail, EmdFilters } from '@/services/emd.service';
import { format } from 'date-fns';
import { DatePicker } from '../../../components/ui/DatePicker';
import { formatDate } from '../../../utils/dateFormatter';

function formatDate(d: string | null) {
  if (!d) return '—';
  try { return formatDate(d); } catch { return '—'; }
}

function formatAmt(n: number | null) {
  if (!n) return '—';
  return new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 0 }).format(n);
}

function StatusBadge({ status }: { status: string }) {
  const isReceived = status === 'Y';
  return (
    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ${isReceived ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'}`}>
      {isReceived ? 'Received' : 'Pending'}
    </span>
  );
}

function DetailModal({ id, onClose }: { id: number; onClose: () => void }) {
  const { data, isLoading } = useQuery<EmdDetail>({
    queryKey: ['emd-detail', id],
    queryFn: () => emdService.getDetail(id),
  });

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4" onClick={onClose}>
      <div className="absolute inset-0 bg-black/40 backdrop-blur-sm" />
      <div
        className="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto"
        onClick={e => e.stopPropagation()}
      >
        {/* Header */}
        <div className="sticky top-0 bg-white border-b border-slate-100 px-6 py-4 flex items-center justify-between rounded-t-2xl">
          <div>
            <h2 className="text-lg font-bold text-slate-800">EMD / Bank Guarantee Detail</h2>
            {data && <p className="text-sm text-slate-500 mt-0.5">BG No: {data.bankguaranteeno || '—'}</p>}
          </div>
          <button onClick={onClose} className="p-2 rounded-lg hover:bg-slate-100 text-slate-500 transition-colors">
            <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        <div className="p-6 space-y-6">
          {isLoading ? (
            <div className="flex items-center justify-center py-12">
              <div className="w-8 h-8 border-2 border-blue-500 border-t-transparent rounded-full animate-spin" />
            </div>
          ) : data ? (
            <>
              {/* Key Info Grid */}
              <div className="grid grid-cols-2 gap-4">
                {[
                  { label: 'BG For', value: data.bg_for },
                  { label: 'Favour Of', value: data.favour_of },
                  { label: 'Board / Client', value: data.board_name },
                  { label: 'PO Number', value: data.po_no },
                  { label: 'Amount', value: formatAmt(data.amount) },
                  { label: 'Currency', value: data.currency_type },
                  { label: 'Contact Person', value: data.contect_per },
                  { label: 'Status', value: <StatusBadge status={data.status} /> },
                ].map(({ label, value }) => (
                  <div key={label} className="bg-slate-50 rounded-xl p-3">
                    <p className="text-xs text-slate-500 font-medium mb-0.5">{label}</p>
                    <p className="text-sm font-semibold text-slate-800">{value || '—'}</p>
                  </div>
                ))}
              </div>

              {/* Dates */}
              <div className="bg-blue-50 rounded-xl p-4">
                <h3 className="text-sm font-bold text-blue-700 mb-3">Key Dates</h3>
                <div className="grid grid-cols-3 gap-3">
                  {[
                    { label: 'From Date', value: formatDate(data.datefrom) },
                    { label: 'Valid Upto', value: formatDate(data.validupto) },
                    { label: 'Extension Upto', value: formatDate(data.extenstionupto) },
                    { label: 'Claim Upto', value: formatDate(data.claim_upto) },
                    { label: 'Last Date', value: formatDate(data.lastdate) },
                    { label: 'Release Date', value: formatDate(data.relese_date) },
                  ].map(({ label, value }) => (
                    <div key={label}>
                      <p className="text-xs text-blue-500 font-medium mb-0.5">{label}</p>
                      <p className="text-sm font-semibold text-blue-900">{value}</p>
                    </div>
                  ))}
                </div>
              </div>

              {/* Amount Received Summary */}
              <div className="grid grid-cols-3 gap-4">
                <div className="bg-emerald-50 rounded-xl p-4 text-center">
                  <p className="text-xs text-emerald-600 font-medium">Total BG Amount</p>
                  <p className="text-lg font-bold text-emerald-700 mt-1">{formatAmt(data.amount)}</p>
                </div>
                <div className="bg-blue-50 rounded-xl p-4 text-center">
                  <p className="text-xs text-blue-600 font-medium">Total Received</p>
                  <p className="text-lg font-bold text-blue-700 mt-1">{formatAmt(data.totalReceived)}</p>
                </div>
                <div className="bg-amber-50 rounded-xl p-4 text-center">
                  <p className="text-xs text-amber-600 font-medium">Remaining</p>
                  <p className="text-lg font-bold text-amber-700 mt-1">{formatAmt(data.remainingAmount)}</p>
                </div>
              </div>

              {/* Amount History */}
              {data.amounts && data.amounts.length > 0 && (
                <div>
                  <h3 className="text-sm font-bold text-slate-700 mb-3">Amount Received History</h3>
                  <div className="overflow-x-auto rounded-xl border border-slate-200">
                    <table className="min-w-full text-sm">
                      <thead className="bg-slate-50">
                        <tr>
                          {['#', 'Received Amount', 'Receive Date', 'Description'].map(h => (
                            <th key={h} className="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 uppercase">{h}</th>
                          ))}
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-slate-100">
                        {data.amounts.map((a, i) => (
                          <tr key={a.id} className="hover:bg-slate-50">
                            <td className="px-4 py-2.5 text-slate-500">{i + 1}</td>
                            <td className="px-4 py-2.5 font-semibold text-emerald-700">{formatAmt(a.recive_amount)}</td>
                            <td className="px-4 py-2.5 text-slate-600">{formatDate(a.recive_date)}</td>
                            <td className="px-4 py-2.5 text-slate-600">{a.description || '—'}</td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                </div>
              )}

              {/* Remarks */}
              {data.remarks && data.remarks.length > 0 && (
                <div>
                  <h3 className="text-sm font-bold text-slate-700 mb-3">Remarks History</h3>
                  <div className="space-y-2">
                    {data.remarks.map(r => (
                      <div key={r.id} className="bg-slate-50 rounded-lg p-3 flex gap-3">
                        <div className="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                          <svg className="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                          <p className="text-xs text-slate-500 font-medium">{r.remarked_by} · {formatDate(r.created)}</p>
                          <p className="text-sm text-slate-700 mt-0.5">{r.remark}</p>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              )}
            </>
          ) : (
            <p className="text-center text-slate-500 py-10">Record not found.</p>
          )}
        </div>
      </div>
    </div>
  );
}

export default function EmdPage() {
  const [filters, setFilters] = useState<EmdFilters>({ status: 'N' });
  const [applied, setApplied] = useState<EmdFilters>({ status: 'N' });
  const [selectedId, setSelectedId] = useState<number | null>(null);

  const { data, isLoading, refetch } = useQuery<EmdRecord[]>({
    queryKey: ['emd-list', applied],
    queryFn: () => emdService.getList(applied),
  });

  const handleSearch = () => { setApplied({ ...filters }); };
  const handleReset = () => { setFilters({ status: 'N' }); setApplied({ status: 'N' }); };

  return (
    <div className="space-y-6">
      {/* Page Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-slate-800">EMD / Bank Guarantee</h1>
          <p className="text-sm text-slate-500 mt-0.5">Manage Earnest Money Deposits and Bank Guarantee records</p>
        </div>
        <div className="flex items-center gap-2 text-sm text-slate-500">
          <span className="bg-slate-100 px-3 py-1.5 rounded-lg font-medium">{data?.length ?? 0} records</span>
        </div>
      </div>

      {/* Filters Card */}
      <div className="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <h2 className="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
          <svg className="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
          Search Filters
        </h2>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1.5">BG For</label>
            <input
              className="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
              placeholder="Search by BG for..."
              value={filters.bg_for || ''}
              onChange={e => setFilters(f => ({ ...f, bg_for: e.target.value }))}
            />
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1.5">BG Number</label>
            <input
              className="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
              placeholder="Bank guarantee no..."
              value={filters.bankguaranteeno || ''}
              onChange={e => setFilters(f => ({ ...f, bankguaranteeno: e.target.value }))}
            />
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1.5">Status</label>
            <select
              className="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
              value={filters.status || ''}
              onChange={e => setFilters(f => ({ ...f, status: e.target.value }))}
            >
              <option value="">All</option>
              <option value="N">Pending</option>
              <option value="Y">Received</option>
            </select>
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1.5">From Date</label>
            <DatePicker  
              className="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
              value={filters.datefrom || ''}
              onChange={e => setFilters(f => ({ ...f, datefrom: e.target.value }))}
            />
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1.5">To Date</label>
            <DatePicker  
              className="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
              value={filters.dateto || ''}
              onChange={e => setFilters(f => ({ ...f, dateto: e.target.value }))}
            />
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1.5">Due From</label>
            <DatePicker  
              className="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
              value={filters.due_from || ''}
              onChange={e => setFilters(f => ({ ...f, due_from: e.target.value }))}
            />
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1.5">Due To</label>
            <DatePicker  
              className="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
              value={filters.due_to || ''}
              onChange={e => setFilters(f => ({ ...f, due_to: e.target.value }))}
            />
          </div>
          <div className="flex items-end gap-2">
            <button
              onClick={handleSearch}
              className="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors"
            >
              Search
            </button>
            <button
              onClick={handleReset}
              className="px-4 py-2 border border-slate-200 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-50 transition-colors"
            >
              Reset
            </button>
          </div>
        </div>
      </div>

      {/* Table */}
      <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="min-w-full">
            <thead>
              <tr className="bg-slate-50 border-b border-slate-200">
                {['#', 'BG Number', 'BG For', 'Favour Of / Board', 'PO No', 'Amount', 'Valid Upto', 'Claim Upto', 'Status', 'Action'].map(h => (
                  <th key={h} className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">{h}</th>
                ))}
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {isLoading ? (
                <tr>
                  <td colSpan={10} className="py-12 text-center">
                    <div className="flex items-center justify-center gap-2 text-slate-400">
                      <div className="w-5 h-5 border-2 border-blue-400 border-t-transparent rounded-full animate-spin" />
                      Loading...
                    </div>
                  </td>
                </tr>
              ) : !data || data.length === 0 ? (
                <tr>
                  <td colSpan={10} className="py-12 text-center text-slate-400 text-sm">No EMD records found.</td>
                </tr>
              ) : (
                data.map((row, i) => (
                  <tr key={row.id} className="hover:bg-slate-50 transition-colors">
                    <td className="px-4 py-3 text-slate-500 text-sm">{i + 1}</td>
                    <td className="px-4 py-3">
                      <span className="text-sm font-mono font-semibold text-blue-600">{row.bankguaranteeno || '—'}</span>
                    </td>
                    <td className="px-4 py-3 text-sm text-slate-700 max-w-[140px] truncate">{row.bg_for || '—'}</td>
                    <td className="px-4 py-3 text-sm text-slate-700 max-w-[140px] truncate">{row.board_name || row.favour_of || '—'}</td>
                    <td className="px-4 py-3 text-sm text-slate-600">{row.po_no || '—'}</td>
                    <td className="px-4 py-3 text-sm font-semibold text-slate-800">{formatAmt(row.amount)}</td>
                    <td className="px-4 py-3 text-sm text-slate-600">{formatDate(row.validupto)}</td>
                    <td className="px-4 py-3 text-sm text-slate-600">{formatDate(row.claim_upto)}</td>
                    <td className="px-4 py-3"><StatusBadge status={row.status} /></td>
                    <td className="px-4 py-3">
                      <button
                        onClick={() => setSelectedId(row.id)}
                        className="text-xs font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors"
                      >
                        View
                      </button>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Detail Modal */}
      {selectedId !== null && (
        <DetailModal id={selectedId} onClose={() => setSelectedId(null)} />
      )}
    </div>
  );
}
