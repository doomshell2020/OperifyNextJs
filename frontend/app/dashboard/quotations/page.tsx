'use client';

import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { quotationService, Quotation, QuotationDetail, QuotationFilters } from '@/services/quotation.service';
import { format } from 'date-fns';

function formatDate(d: string | null) {
  if (!d) return '—';
  try { return format(new Date(d), 'dd/MM/yyyy'); } catch { return '—'; }
}

function formatAmt(n: number | null) {
  if (!n) return '—';
  return new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 0 }).format(n);
}

function StatusBadge({ status, postatus }: { status: string; postatus: string }) {
  if (postatus === 'Y') return <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">PO Issued</span>;
  if (status === 'Y') return <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Active</span>;
  return <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">Inactive</span>;
}

function AwardBadge({ is_award }: { is_award: number }) {
  return (
    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ${is_award === 1 ? 'bg-violet-100 text-violet-700' : 'bg-slate-100 text-slate-500'}`}>
      {is_award === 1 ? 'Awarded' : 'Not Awarded'}
    </span>
  );
}

function DetailModal({ id, onClose }: { id: number; onClose: () => void }) {
  const { data, isLoading } = useQuery<QuotationDetail>({
    queryKey: ['quotation-detail', id],
    queryFn: () => quotationService.getDetail(id),
  });

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4" onClick={onClose}>
      <div className="absolute inset-0 bg-black/40 backdrop-blur-sm" />
      <div className="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[92vh] overflow-y-auto" onClick={e => e.stopPropagation()}>
        <div className="sticky top-0 bg-white border-b border-slate-100 px-6 py-4 flex items-center justify-between rounded-t-2xl">
          <div>
            <h2 className="text-lg font-bold text-slate-800">Quotation Detail</h2>
            {data && <p className="text-sm text-slate-500 mt-0.5">QID: {data.quotation_id}</p>}
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
              {/* Header info */}
              <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
                {[
                  { label: 'Quotation ID', value: data.quotation_id },
                  { label: 'Vendor', value: data.vendor_name },
                  { label: 'GST Number', value: data.gst_number },
                  { label: 'Contact', value: data.contact_no },
                  { label: 'Email', value: data.email },
                  { label: 'Contact Person', value: data.contact_person },
                  { label: 'Delivery Date', value: formatDate(data.delivery_date) },
                  { label: 'Acceptance Date', value: formatDate(data.acceptance_date) },
                  { label: 'Added On', value: formatDate(data.added_time) },
                ].map(({ label, value }) => (
                  <div key={label} className="bg-slate-50 rounded-xl p-3">
                    <p className="text-xs text-slate-500 font-medium mb-0.5">{label}</p>
                    <p className="text-sm font-semibold text-slate-800">{value || '—'}</p>
                  </div>
                ))}
              </div>

              {/* Terms */}
              <div className="grid grid-cols-3 gap-4">
                {[
                  { label: 'Payment Terms', value: data.payment_terms },
                  { label: 'Freight', value: data.freight },
                  { label: 'Transit Insurance', value: data.transit_insurance },
                ].map(({ label, value }) => (
                  <div key={label} className="bg-blue-50 rounded-xl p-3">
                    <p className="text-xs text-blue-500 font-medium mb-0.5">{label}</p>
                    <p className="text-sm font-semibold text-blue-900">{value || '—'}</p>
                  </div>
                ))}
              </div>

              {/* Totals */}
              <div className="grid grid-cols-3 gap-4">
                <div className="bg-slate-50 rounded-xl p-4 text-center">
                  <p className="text-xs text-slate-500 font-medium">Total Qty</p>
                  <p className="text-xl font-bold text-slate-800 mt-1">{data.total_qty ?? '—'}</p>
                </div>
                <div className="bg-amber-50 rounded-xl p-4 text-center">
                  <p className="text-xs text-amber-600 font-medium">Total Tax</p>
                  <p className="text-xl font-bold text-amber-700 mt-1">{formatAmt(data.total_tax)}</p>
                </div>
                <div className="bg-emerald-50 rounded-xl p-4 text-center">
                  <p className="text-xs text-emerald-600 font-medium">Total Amount</p>
                  <p className="text-xl font-bold text-emerald-700 mt-1">{formatAmt(data.total_amt)}</p>
                </div>
              </div>

              {/* Line Items */}
              {data.details && data.details.length > 0 && (
                <div>
                  <h3 className="text-sm font-bold text-slate-700 mb-3">Line Items ({data.details.length})</h3>
                  <div className="overflow-x-auto rounded-xl border border-slate-200">
                    <table className="min-w-full text-sm">
                      <thead className="bg-slate-50">
                        <tr>
                          {['#', 'Item Name', 'Qty', 'UOM', 'Unit Amount', 'Tax', 'Total'].map(h => (
                            <th key={h} className="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 uppercase">{h}</th>
                          ))}
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-slate-100">
                        {data.details.map((item, i) => (
                          <tr key={item.id} className="hover:bg-slate-50">
                            <td className="px-4 py-2.5 text-slate-500">{i + 1}</td>
                            <td className="px-4 py-2.5">
                              <p className="font-medium text-slate-800">{item.item_name}</p>
                              {item.item_description && <p className="text-xs text-slate-500 mt-0.5">{item.item_description}</p>}
                            </td>
                            <td className="px-4 py-2.5 font-semibold text-slate-700">{item.item_qty}</td>
                            <td className="px-4 py-2.5 text-slate-500">{item.uom_name || item.uom || '—'}</td>
                            <td className="px-4 py-2.5 text-slate-700">{formatAmt(item.item_amt)}</td>
                            <td className="px-4 py-2.5 text-amber-600">{formatAmt(item.item_tax_amt)}</td>
                            <td className="px-4 py-2.5 font-bold text-emerald-700">{formatAmt(item.item_total_amount)}</td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                </div>
              )}

              {data.remark && (
                <div className="bg-slate-50 rounded-xl p-4">
                  <p className="text-xs text-slate-500 font-medium mb-1">Remarks</p>
                  <p className="text-sm text-slate-700">{data.remark}</p>
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

export default function QuotationsPage() {
  const [filters, setFilters] = useState<QuotationFilters>({});
  const [applied, setApplied] = useState<QuotationFilters>({});
  const [selectedId, setSelectedId] = useState<number | null>(null);

  const { data: vendors } = useQuery({
    queryKey: ['quotation-vendors'],
    queryFn: () => quotationService.getVendors(),
  });

  const { data, isLoading } = useQuery<Quotation[]>({
    queryKey: ['quotations-list', applied],
    queryFn: () => quotationService.getList(applied),
  });

  const handleSearch = () => setApplied({ ...filters });
  const handleReset = () => { setFilters({}); setApplied({}); };

  return (
    <div className="space-y-6">
      {/* Page Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-slate-800">Quotations</h1>
          <p className="text-sm text-slate-500 mt-0.5">Manage vendor quotations and purchase proposals</p>
        </div>
        <span className="bg-slate-100 px-3 py-1.5 rounded-lg text-sm font-medium text-slate-500">{data?.length ?? 0} records</span>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <h2 className="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
          <svg className="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
          Search Filters
        </h2>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1.5">Quotation ID</label>
            <input
              className="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
              placeholder="Search quotation ID..."
              value={filters.quotation_id || ''}
              onChange={e => setFilters(f => ({ ...f, quotation_id: e.target.value }))}
            />
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1.5">Vendor</label>
            <select
              className="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
              value={filters.vendor_id || ''}
              onChange={e => setFilters(f => ({ ...f, vendor_id: e.target.value ? parseInt(e.target.value) : undefined }))}
            >
              <option value="">All Vendors</option>
              {vendors?.map(v => <option key={v.id} value={v.id}>{v.name}</option>)}
            </select>
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1.5">Award Status</label>
            <select
              className="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
              value={filters.is_award !== undefined ? String(filters.is_award) : ''}
              onChange={e => setFilters(f => ({ ...f, is_award: e.target.value !== '' ? parseInt(e.target.value) : undefined }))}
            >
              <option value="">All</option>
              <option value="1">Awarded</option>
              <option value="0">Not Awarded</option>
            </select>
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1.5">Status</label>
            <select
              className="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
              value={filters.status || ''}
              onChange={e => setFilters(f => ({ ...f, status: e.target.value }))}
            >
              <option value="">All</option>
              <option value="Y">Active</option>
              <option value="N">Inactive</option>
            </select>
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1.5">From Date</label>
            <input type="date" className="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" value={filters.datefrom || ''} onChange={e => setFilters(f => ({ ...f, datefrom: e.target.value }))} />
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1.5">To Date</label>
            <input type="date" className="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" value={filters.dateto || ''} onChange={e => setFilters(f => ({ ...f, dateto: e.target.value }))} />
          </div>
          <div className="flex items-end gap-2 md:col-span-2">
            <button onClick={handleSearch} className="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">Search</button>
            <button onClick={handleReset} className="px-4 py-2 border border-slate-200 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-50 transition-colors">Reset</button>
          </div>
        </div>
      </div>

      {/* Table */}
      <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="min-w-full">
            <thead>
              <tr className="bg-slate-50 border-b border-slate-200">
                {['#', 'QID', 'Vendor', 'Delivery Date', 'Total Qty', 'Total Tax', 'Total Amount', 'Award', 'Status', 'Action'].map(h => (
                  <th key={h} className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">{h}</th>
                ))}
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {isLoading ? (
                <tr><td colSpan={10} className="py-12 text-center"><div className="flex items-center justify-center gap-2 text-slate-400"><div className="w-5 h-5 border-2 border-blue-400 border-t-transparent rounded-full animate-spin" />Loading...</div></td></tr>
              ) : !data || data.length === 0 ? (
                <tr><td colSpan={10} className="py-12 text-center text-slate-400 text-sm">No quotation records found.</td></tr>
              ) : (
                data.map((row, i) => (
                  <tr key={row.id} className="hover:bg-slate-50 transition-colors">
                    <td className="px-4 py-3 text-slate-500 text-sm">{i + 1}</td>
                    <td className="px-4 py-3 font-mono text-sm font-semibold text-blue-600">{row.quotation_id}</td>
                    <td className="px-4 py-3 text-sm font-medium text-slate-800 max-w-[160px] truncate">{row.vendor_name || '—'}</td>
                    <td className="px-4 py-3 text-sm text-slate-600">{formatDate(row.delivery_date)}</td>
                    <td className="px-4 py-3 text-sm text-slate-700">{row.total_qty}</td>
                    <td className="px-4 py-3 text-sm text-amber-600 font-medium">{formatAmt(row.total_tax)}</td>
                    <td className="px-4 py-3 text-sm font-bold text-slate-800">{formatAmt(row.total_amt)}</td>
                    <td className="px-4 py-3"><AwardBadge is_award={row.is_award} /></td>
                    <td className="px-4 py-3"><StatusBadge status={row.status} postatus={row.postatus} /></td>
                    <td className="px-4 py-3">
                      <button onClick={() => setSelectedId(row.id)} className="text-xs font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
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

      {selectedId !== null && <DetailModal id={selectedId} onClose={() => setSelectedId(null)} />}
    </div>
  );
}
