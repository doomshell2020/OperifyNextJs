'use client';

import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { paymentService, Payment, PaymentFilters } from '@/services/payment.service';
import { format } from 'date-fns';

function formatDate(d: string | null) {
  if (!d) return '—';
  try { return format(new Date(d), 'dd/MM/yyyy'); } catch { return '—'; }
}

function formatAmt(n: number | null) {
  if (!n) return '—';
  return new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 0 }).format(n);
}

function StatusBadge({ status }: { status: string }) {
  const paid = status === 'Y';
  return (
    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ${paid ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'}`}>
      {paid ? 'Paid' : 'Pending'}
    </span>
  );
}

function DetailModal({ payment, onClose }: { payment: Payment; onClose: () => void }) {
  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4" onClick={onClose}>
      <div className="absolute inset-0 bg-black/40 backdrop-blur-sm" />
      <div className="relative bg-white rounded-2xl shadow-2xl w-full max-w-xl overflow-y-auto max-h-[90vh]" onClick={e => e.stopPropagation()}>
        <div className="sticky top-0 bg-white border-b border-slate-100 px-6 py-4 flex items-center justify-between rounded-t-2xl">
          <div>
            <h2 className="text-lg font-bold text-slate-800">Payment Detail</h2>
            <p className="text-sm text-slate-500 mt-0.5">Bill No: {payment.bill_no}</p>
          </div>
          <button onClick={onClose} className="p-2 rounded-lg hover:bg-slate-100 text-slate-500 transition-colors">
            <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div className="p-6 space-y-4">
          <div className="grid grid-cols-2 gap-4">
            {[
              { label: 'Vendor', value: payment.vendor_name },
              { label: 'Bill No', value: payment.bill_no },
              { label: 'Receipt No', value: payment.receipt_no },
              { label: 'Inward Date', value: formatDate(payment.inwarddate) },
              { label: 'Bill Date', value: formatDate(payment.bill_date) },
              { label: 'Pay Date', value: formatDate(payment.pay_date) },
              { label: 'GST Number', value: payment.gst_number },
              { label: 'Contact', value: payment.contact_no },
            ].map(({ label, value }) => (
              <div key={label} className="bg-slate-50 rounded-xl p-3">
                <p className="text-xs text-slate-500 font-medium mb-0.5">{label}</p>
                <p className="text-sm font-semibold text-slate-800">{value || '—'}</p>
              </div>
            ))}
          </div>
          {payment.remark && (
            <div className="bg-slate-50 rounded-xl p-3">
              <p className="text-xs text-slate-500 font-medium mb-0.5">Remark</p>
              <p className="text-sm text-slate-700">{payment.remark}</p>
            </div>
          )}
          <div className="bg-emerald-50 rounded-xl p-4 flex items-center justify-between">
            <span className="text-sm font-bold text-emerald-700">Total Amount</span>
            <span className="text-xl font-bold text-emerald-700">{formatAmt(payment.total_amt)}</span>
          </div>
          <div className="flex justify-center">
            <StatusBadge status={payment.status} />
          </div>
        </div>
      </div>
    </div>
  );
}

export default function PaymentsPage() {
  const [filters, setFilters] = useState<PaymentFilters>({});
  const [applied, setApplied] = useState<PaymentFilters>({});
  const [selectedPayment, setSelectedPayment] = useState<Payment | null>(null);

  const { data: vendors } = useQuery({
    queryKey: ['payment-vendors'],
    queryFn: () => paymentService.getVendors(),
  });

  const { data, isLoading } = useQuery<Payment[]>({
    queryKey: ['payments-list', applied],
    queryFn: () => paymentService.getList(applied),
  });

  const handleSearch = () => setApplied({ ...filters });
  const handleReset = () => { setFilters({}); setApplied({}); };

  return (
    <div className="space-y-6">
      {/* Page Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-slate-800">Payments</h1>
          <p className="text-sm text-slate-500 mt-0.5">Track vendor bill payments and receipts</p>
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
            <label className="block text-xs font-semibold text-slate-500 mb-1.5">Bill No</label>
            <input
              className="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
              placeholder="Search bill no..."
              value={filters.bill_no || ''}
              onChange={e => setFilters(f => ({ ...f, bill_no: e.target.value }))}
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
              <option value="Y">Paid</option>
              <option value="N">Pending</option>
            </select>
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1.5">From Date</label>
            <input
              type="date"
              className="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
              value={filters.datefrom || ''}
              onChange={e => setFilters(f => ({ ...f, datefrom: e.target.value }))}
            />
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1.5">To Date</label>
            <input
              type="date"
              className="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
              value={filters.dateto || ''}
              onChange={e => setFilters(f => ({ ...f, dateto: e.target.value }))}
            />
          </div>
          <div className="flex items-end gap-2 md:col-span-2">
            <button onClick={handleSearch} className="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
              Search
            </button>
            <button onClick={handleReset} className="px-4 py-2 border border-slate-200 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-50 transition-colors">
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
                {['#', 'Vendor', 'Bill No', 'Inward Date', 'Bill Date', 'Total Amount', 'Pay Date', 'Remark', 'Status', 'Action'].map(h => (
                  <th key={h} className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">{h}</th>
                ))}
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {isLoading ? (
                <tr><td colSpan={10} className="py-12 text-center"><div className="flex items-center justify-center gap-2 text-slate-400"><div className="w-5 h-5 border-2 border-blue-400 border-t-transparent rounded-full animate-spin" />Loading...</div></td></tr>
              ) : !data || data.length === 0 ? (
                <tr><td colSpan={10} className="py-12 text-center text-slate-400 text-sm">No payment records found.</td></tr>
              ) : (
                data.map((row, i) => (
                  <tr key={row.id} className="hover:bg-slate-50 transition-colors">
                    <td className="px-4 py-3 text-slate-500 text-sm">{i + 1}</td>
                    <td className="px-4 py-3 text-sm font-medium text-slate-800 max-w-[160px] truncate">{row.vendor_name || '—'}</td>
                    <td className="px-4 py-3 text-sm font-mono text-blue-600">{row.bill_no}</td>
                    <td className="px-4 py-3 text-sm text-slate-600">{formatDate(row.inwarddate)}</td>
                    <td className="px-4 py-3 text-sm text-slate-600">{formatDate(row.bill_date)}</td>
                    <td className="px-4 py-3 text-sm font-bold text-slate-800">{formatAmt(row.total_amt)}</td>
                    <td className="px-4 py-3 text-sm text-slate-600">{formatDate(row.pay_date)}</td>
                    <td className="px-4 py-3 text-sm text-slate-500 max-w-[120px] truncate">{row.remark || '—'}</td>
                    <td className="px-4 py-3"><StatusBadge status={row.status} /></td>
                    <td className="px-4 py-3">
                      <button
                        onClick={() => setSelectedPayment(row)}
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

      {selectedPayment && <DetailModal payment={selectedPayment} onClose={() => setSelectedPayment(null)} />}
    </div>
  );
}
