'use client';

import { useState } from 'react';
import Link from 'next/link';
import { useQuery } from '@tanstack/react-query';
import { isAxiosError } from 'axios';
import { FileSpreadsheet, X } from 'lucide-react';
import { useAuth } from '@/contexts/AuthContext';
import { LegacyPageHeader } from '@/components/ui/LegacyPageHeader';
import { DatePicker } from '@/components/ui/DatePicker';
import { ListPagination, LEGACY_LIST_LIMIT } from '@/components/ui/ListPagination';
import {
  jcReportsService,
  RawMaterialTrackingFilters,
  RawMaterialTrackingGroup,
  ReceiptDetail
} from '@/services/jcReports.service';

const blankFilters: RawMaterialTrackingFilters = {
  from_date: '',
  to_date: '',
  challan_no: '',
  category_id: '',
  vendor_id: ''
};

function errorMessage(error: unknown): string {
  return isAxiosError(error)
    ? error.response?.data?.error?.message || error.message
    : 'Unable to load the raw material tracking report.';
}

function formatQty(value: number | null | undefined): string {
  if (value === null || value === undefined) return '0';
  const num = Number(value);
  return num.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 6 });
}

export default function RawMaterialTrackingReportPage() {
  const { user } = useAuth();
  return <RawMaterialTrackingReport key={`${user?.id}:${user?.db}`} company={user?.db || ''} />;
}

function RawMaterialTrackingReport({ company }: { company: string }) {
  const [filters, setFilters] = useState<RawMaterialTrackingFilters>(blankFilters);
  const [applied, setApplied] = useState<RawMaterialTrackingFilters | null>(blankFilters);
  const [page, setPage] = useState(1);
  const [error, setError] = useState('');
  const [exporting, setExporting] = useState(false);
  const [activeReceiptsGroup, setActiveReceiptsGroup] = useState<RawMaterialTrackingGroup | null>(null);

  const options = useQuery({
    queryKey: ['raw-tracking-options', company],
    enabled: !!company,
    queryFn: jcReportsService.getRawTrackingOptions
  });

  const report = useQuery({
    queryKey: ['raw-material-tracking', company, applied, page],
    enabled: !!company && applied !== null,
    queryFn: () => jcReportsService.getRawTrackingReport(applied!, page, LEGACY_LIST_LIMIT)
  });

  const search = () => {
    setError('');
    setPage(1);
    setApplied({ ...filters });
  };

  const reset = () => {
    setFilters(blankFilters);
    setApplied(blankFilters);
    setPage(1);
    setError('');
  };

  const exportExcel = async () => {
    if (!applied) return;
    setExporting(true);
    setError('');
    try {
      await jcReportsService.exportRawTrackingExcel(applied);
    } catch (failure) {
      setError(errorMessage(failure));
    } finally {
      setExporting(false);
    }
  };

  const groups: RawMaterialTrackingGroup[] = report.data?.data || [];
  const pagination = report.data?.pagination;
  const summary = report.data?.summary;
  const unmatched = report.data?.unmatched || [];

  const inputClass = 'w-full text-sm border border-slate-300 rounded-md p-2 bg-white h-[38px]';

  return (
    <main className="max-w-7xl w-full mx-auto px-6 py-8 space-y-6 font-sans">
      <LegacyPageHeader title="Category-wise Raw Material Tracking Report" breadcrumb="Report" />

      {/* Filter box */}
      <div className="bg-white border border-slate-200 rounded-md shadow-sm p-4">
        <form
          className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 items-end"
          onSubmit={e => {
            e.preventDefault();
            search();
          }}
        >
          <div>
            <label className="block text-xs font-semibold text-slate-600 mb-1">
              From JC Date
            </label>
            <DatePicker
              aria-label="From JC Date"
              value={filters.from_date}
              className={inputClass}
              onChange={e => setFilters({ ...filters, from_date: e.target.value })}
            />
          </div>

          <div>
            <label className="block text-xs font-semibold text-slate-600 mb-1">
              To JC Date
            </label>
            <DatePicker
              aria-label="To JC Date"
              value={filters.to_date}
              className={inputClass}
              onChange={e => setFilters({ ...filters, to_date: e.target.value })}
            />
          </div>

          <div>
            <label htmlFor="tracking-jc-number" className="block text-xs font-semibold text-slate-600 mb-1">
              JC Number
            </label>
            <input
              id="tracking-jc-number"
              type="text"
              placeholder="e.g. 792 or 974-992"
              className={inputClass}
              value={filters.challan_no}
              onChange={e => setFilters({ ...filters, challan_no: e.target.value })}
            />
          </div>

          <div>
            <label htmlFor="tracking-category" className="block text-xs font-semibold text-slate-600 mb-1">
              Raw Material Category
            </label>
            <select
              id="tracking-category"
              className={inputClass}
              value={filters.category_id}
              onChange={e => setFilters({ ...filters, category_id: e.target.value })}
            >
              <option value="">All</option>
              {options.data?.categories.map(c => (
                <option key={c.id} value={c.id}>
                  {c.name}
                </option>
              ))}
            </select>
          </div>

          <div>
            <label htmlFor="tracking-supplier" className="block text-xs font-semibold text-slate-600 mb-1">
              Subcontractor / Company
            </label>
            <select
              id="tracking-supplier"
              className={inputClass}
              value={filters.vendor_id}
              onChange={e => setFilters({ ...filters, vendor_id: e.target.value })}
            >
              <option value="">All</option>
              {options.data?.suppliers.map(s => (
                <option key={s.id} value={s.id}>
                  {s.name}
                </option>
              ))}
            </select>
          </div>

          <div className="lg:col-span-5 flex flex-wrap items-center gap-3 pt-2">
            <button
              type="submit"
              disabled={report.isFetching || exporting}
              className="legacy-button"
            >
              {report.isFetching ? 'Loading…' : 'Search'}
            </button>
            <button
              type="button"
              disabled={exporting}
              className="legacy-button"
              onClick={reset}
            >
              Reset
            </button>
            <button
              type="button"
              disabled={!applied || report.isFetching || report.isError || exporting}
              className="legacy-button flex items-center gap-2 ml-auto"
              onClick={() => void exportExcel()}
              title="Download Excel"
            >
              <FileSpreadsheet size={18} aria-hidden="true" />
              {exporting ? 'Exporting…' : 'Download Excel'}
            </button>
          </div>
        </form>
      </div>

      {(error || options.error || report.error) && (
        <p role="alert" className="p-3 text-red-600 bg-red-50 border border-red-200 rounded-md">
          {error || errorMessage(options.error || report.error)}
        </p>
      )}

      {/* Explanatory notes */}
      <div className="bg-slate-50 border border-slate-200 rounded-md p-4 text-xs text-slate-600 space-y-1">
        <details>
          <summary className="font-semibold text-cyan-800 cursor-pointer hover:underline">
            How quantities are calculated
          </summary>
          <p className="mt-2 text-slate-700 leading-relaxed">
            Date filters use the JC date. Quantities include all receipts recorded against the selected JCs.
            The JC source has no recorded loss or conversion factor, so the loss column shows zero recorded loss.
            Unallocated receipts ({unmatched.length} record{unmatched.length === 1 ? '' : 's'}) are excluded from the balances.
          </p>
        </details>
      </div>

      {/* Unit summary cards */}
      {summary && Object.keys(summary).length > 0 && (
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          {Object.entries(summary).map(([key, u]) => (
            <div key={key} className="p-4 bg-white border border-slate-200 rounded-md shadow-xs space-y-1">
              <p className="text-xs font-semibold text-slate-500 uppercase tracking-wider">Unit: {u.unit}</p>
              <div className="text-sm space-y-0.5">
                <div className="flex justify-between">
                  <span className="text-slate-600">Total Sent:</span>
                  <span className="font-medium">{formatQty(u.sent)}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-slate-600">Total Received:</span>
                  <span className="font-medium">{formatQty(u.received)}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-slate-600">Pending:</span>
                  <span className="font-bold text-amber-800">{formatQty(u.pending)}</span>
                </div>
              </div>
            </div>
          ))}
        </div>
      )}

      {/* Data Table */}
      <div className="bg-white border border-slate-200 rounded-md shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full min-w-[1050px] border-collapse text-sm" aria-label="Category-wise raw material balances">
            <thead>
              <tr className="bg-[#333] text-white">
                <th scope="col" className="p-3 text-left font-medium">Raw Material Category</th>
                <th scope="col" className="p-3 text-left font-medium">Subcontractor / Company Name</th>
                <th scope="col" className="p-3 text-left font-medium whitespace-nowrap">Unit</th>
                <th scope="col" className="p-3 text-right font-medium whitespace-nowrap">Total Raw Material Sent Qty</th>
                <th scope="col" className="p-3 text-right font-medium whitespace-nowrap">Total Processed Material Received Qty</th>
                <th scope="col" className="p-3 text-right font-medium whitespace-nowrap">Total Wastage / Conversion Loss Qty</th>
                <th scope="col" className="p-3 text-right font-medium whitespace-nowrap">Pending Raw Material Qty</th>
                <th scope="col" className="p-3 text-center font-medium whitespace-nowrap">Total Number of JCs</th>
                <th scope="col" className="p-3 text-left font-medium min-w-[150px]">JC Numbers (Reference)</th>
              </tr>
            </thead>
            <tbody>
              {report.isFetching ? (
                <tr>
                  <td colSpan={9} className="p-8 text-center text-slate-500" role="status">
                    Loading…
                  </td>
                </tr>
              ) : !groups.length ? (
                <tr>
                  <td colSpan={9} className="p-8 text-center text-slate-500">
                    No matching raw material issues. Try a wider date range or clear the filters.
                  </td>
                </tr>
              ) : (
                groups.map((group, index) => {
                  const jcEntries = Object.entries(group.jcs || {});
                  const firstTwo = jcEntries.slice(0, 2);
                  const remaining = jcEntries.slice(2);

                  return (
                    <tr
                      key={index}
                      className={`border-t border-slate-100 hover:bg-slate-50 ${
                        index % 2 === 0 ? 'bg-white' : 'bg-[#fcfdfd]'
                      }`}
                    >
                      <td className="p-3 font-medium text-slate-900">{group.category}</td>
                      <td className="p-3 text-slate-800">{group.company}</td>
                      <td className="p-3 whitespace-nowrap text-slate-700">{group.unit}</td>
                      <td className="p-3 text-right tabular-nums whitespace-nowrap">{formatQty(group.sent)}</td>
                      <td className="p-3 text-right tabular-nums whitespace-nowrap">
                        {group.received > 0 ? (
                          <button
                            type="button"
                            className="text-cyan-700 hover:underline font-medium cursor-pointer"
                            onClick={() => setActiveReceiptsGroup(group)}
                            title="Click to view received material details"
                          >
                            {formatQty(group.received)}
                          </button>
                        ) : (
                          <span>0</span>
                        )}
                      </td>
                      <td className="p-3 text-right tabular-nums whitespace-nowrap text-slate-500">{formatQty(group.loss)}</td>
                      <td
                        className={`p-3 text-right tabular-nums whitespace-nowrap font-bold ${
                          group.pending > 0 ? 'bg-[#fff8e8] text-[#744508]' : 'text-slate-700'
                        }`}
                      >
                        {formatQty(group.pending)}
                      </td>
                      <td className="p-3 text-center tabular-nums">{group.count}</td>
                      <td className="p-3 text-xs">
                        {firstTwo.map(([id, number]) => (
                          <div key={id}>
                            <Link href={`/dashboard/jc-challan/${id}`} className="text-cyan-700 hover:underline">
                              {number}
                            </Link>
                          </div>
                        ))}
                        {remaining.length > 0 && (
                          <details className="mt-1">
                            <summary className="text-cyan-800 cursor-pointer hover:underline font-medium">
                              +{remaining.length} more
                            </summary>
                            <ul className="pl-3 mt-1 space-y-0.5 list-disc text-slate-600">
                              {remaining.map(([id, number]) => (
                                <li key={id}>
                                  <Link href={`/dashboard/jc-challan/${id}`} className="text-cyan-700 hover:underline">
                                    {number}
                                  </Link>
                                </li>
                              ))}
                            </ul>
                          </details>
                        )}
                      </td>
                    </tr>
                  );
                })
              )}
            </tbody>
          </table>
        </div>

        {pagination && pagination.total > 0 && (
          <div className="p-4 border-t border-slate-200">
            <ListPagination
              page={pagination.page}
              limit={LEGACY_LIST_LIMIT}
              total={pagination.total}
              onPageChange={setPage}
              busy={report.isFetching || exporting}
            />
          </div>
        )}
      </div>

      {/* Received Details Modal Dialog */}
      {activeReceiptsGroup && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
          <div className="bg-white rounded-lg shadow-xl border border-slate-300 w-full max-w-4xl max-h-[85vh] flex flex-col">
            <div className="flex items-center justify-between p-4 border-b border-slate-200">
              <h2 className="text-lg font-bold text-slate-900">Received Material Details</h2>
              <button
                type="button"
                className="p-1 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100"
                onClick={() => setActiveReceiptsGroup(null)}
                aria-label="Close"
              >
                <X size={20} />
              </button>
            </div>

            <div className="p-4 overflow-y-auto space-y-4">
              <div>
                <p className="font-semibold text-slate-900">
                  {activeReceiptsGroup.category} &mdash; {activeReceiptsGroup.company}
                </p>
                <p className="text-sm text-slate-600">
                  Received quantity <strong className="text-slate-900">{formatQty(activeReceiptsGroup.received)} {activeReceiptsGroup.unit}</strong>
                </p>
              </div>

              <div className="overflow-x-auto border border-slate-200 rounded-md">
                <table className="w-full border-collapse text-xs">
                  <thead>
                    <tr className="bg-[#333] text-white">
                      <th className="p-2 text-left font-medium">Receipt ID</th>
                      <th className="p-2 text-left font-medium">Received Date</th>
                      <th className="p-2 text-left font-medium">Receipt Challan</th>
                      <th className="p-2 text-left font-medium">JC Number</th>
                      <th className="p-2 text-left font-medium">JC Reference</th>
                      <th className="p-2 text-left font-medium">Received Item</th>
                      <th className="p-2 text-right font-medium">Receipt Qty</th>
                      <th className="p-2 text-right font-medium">Allocated Received Qty</th>
                      <th className="p-2 text-left font-medium">Unit</th>
                    </tr>
                  </thead>
                  <tbody>
                    {(!activeReceiptsGroup.receipts || !activeReceiptsGroup.receipts.length) ? (
                      <tr>
                        <td colSpan={9} className="p-4 text-center text-slate-500">
                          No received transactions for this balance.
                        </td>
                      </tr>
                    ) : (
                      activeReceiptsGroup.receipts.map(r => (
                        <tr key={`${r.id}:${r.jc_id}`} className="border-t border-slate-100 hover:bg-slate-50">
                          <td className="p-2">{r.id}</td>
                          <td className="p-2 whitespace-nowrap">{r.date || '-'}</td>
                          <td className="p-2">{r.receipt_no || '-'}</td>
                          <td className="p-2 whitespace-nowrap">
                            <Link href={`/dashboard/jc-challan/${r.jc_id}`} className="text-cyan-700 hover:underline">
                              {r.jc_number}
                            </Link>
                          </td>
                          <td className="p-2">{r.reference || '-'}</td>
                          <td className="p-2">{r.product}</td>
                          <td className="p-2 text-right tabular-nums">{formatQty(r.receipt_qty)}</td>
                          <td className="p-2 text-right tabular-nums font-semibold">{formatQty(r.quantity)}</td>
                          <td className="p-2">{r.unit}</td>
                        </tr>
                      ))
                    )}
                  </tbody>
                  <tfoot>
                    <tr className="bg-slate-50 font-bold border-t border-slate-200">
                      <td colSpan={7} className="p-2 text-slate-800">Total allocated to this category and company</td>
                      <td className="p-2 text-right tabular-nums text-slate-900">{formatQty(activeReceiptsGroup.received)}</td>
                      <td className="p-2 text-slate-900">{activeReceiptsGroup.unit}</td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>

            <div className="p-4 border-t border-slate-200 flex justify-end">
              <button
                type="button"
                className="legacy-button"
                onClick={() => setActiveReceiptsGroup(null)}
              >
                Close
              </button>
            </div>
          </div>
        </div>
      )}
    </main>
  );
}
