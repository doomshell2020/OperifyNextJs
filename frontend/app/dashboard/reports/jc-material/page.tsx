'use client';

import { useState } from 'react';
import Link from 'next/link';
import Select from 'react-select';
import { useQuery } from '@tanstack/react-query';
import { isAxiosError } from 'axios';
import { FileSpreadsheet } from 'lucide-react';
import { useAuth } from '@/contexts/AuthContext';
import { LegacyPageHeader } from '@/components/ui/LegacyPageHeader';
import { DatePicker } from '@/components/ui/DatePicker';
import { ListPagination, LEGACY_LIST_LIMIT } from '@/components/ui/ListPagination';
import {
  jcReportsService,
  JcMaterialFilters,
  JcMaterialRecord
} from '@/services/jcReports.service';

const blankFilters: JcMaterialFilters = {
  challan_no: '',
  vendor_id: '',
  item_id: '',
  from_date: '',
  to_date: '',
  balance: ''
};

const balanceOptions = [
  'Pending',
  'Partial',
  'Completed',
  'Excess',
  'Receipt only',
  'No material',
  'Reference receipts'
];

function errorMessage(error: unknown): string {
  return isAxiosError(error)
    ? error.response?.data?.error?.message || error.message
    : 'Unable to load the JC report.';
}

function formatQuantity(value: number | null | undefined): string {
  if (value === null || value === undefined) return 'Needs review';
  const formatted = Number(value).toLocaleString('en-US', {
    minimumFractionDigits: 3,
    maximumFractionDigits: 3
  });
  const sign = formatted.startsWith('-') ? '-' : '';
  const clean = formatted.replace(/^-/, '');
  return sign + clean.padStart(7, '0');
}

export default function JcMaterialReportPage() {
  const { user } = useAuth();
  return <JcMaterialReport key={`${user?.id}:${user?.db}`} company={user?.db || ''} />;
}

function JcMaterialReport({ company }: { company: string }) {
  const [filters, setFilters] = useState<JcMaterialFilters>(blankFilters);
  const [applied, setApplied] = useState<JcMaterialFilters | null>(blankFilters);
  const [page, setPage] = useState(1);
  const [error, setError] = useState('');
  const [exporting, setExporting] = useState(false);

  const options = useQuery({
    queryKey: ['jc-material-options', company],
    enabled: !!company,
    queryFn: jcReportsService.getJcMaterialOptions
  });

  const report = useQuery({
    queryKey: ['jc-material', company, applied, page],
    enabled: !!company && applied !== null,
    queryFn: () => jcReportsService.getJcMaterialReport(applied!, page, LEGACY_LIST_LIMIT)
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
      await jcReportsService.exportJcMaterialExcel(applied);
    } catch (failure) {
      setError(errorMessage(failure));
    } finally {
      setExporting(false);
    }
  };

  const rows: JcMaterialRecord[] = report.data?.data || [];
  const pagination = report.data?.pagination;
  const summary = report.data?.summary;

  const itemOptions = (options.data?.items || []).map(item => ({
    value: String(item.id),
    label: item.item_name
  }));

  const inputClass = 'w-full text-sm border border-slate-300 rounded-md p-2 bg-white h-[38px]';

  return (
    <main className="max-w-7xl w-full mx-auto px-6 py-8 space-y-6 font-sans">
      <LegacyPageHeader title="JC Material Report" breadcrumb="Report" />

      {/* Filter box */}
      <div className="bg-white border border-slate-200 rounded-md shadow-sm p-4">
        <form
          className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 items-end"
          onSubmit={e => {
            e.preventDefault();
            search();
          }}
        >
          <div>
            <label htmlFor="jc-number" className="block text-xs font-semibold text-slate-600 mb-1">
              JC No.
            </label>
            <input
              id="jc-number"
              type="text"
              placeholder="e.g. 993 or 974-992"
              className={inputClass}
              value={filters.challan_no}
              onChange={e => setFilters({ ...filters, challan_no: e.target.value })}
            />
          </div>

          <div>
            <label htmlFor="jc-supplier" className="block text-xs font-semibold text-slate-600 mb-1">
              Supplier / Company
            </label>
            <select
              id="jc-supplier"
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

          <div>
            <label htmlFor="jc-material" className="block text-xs font-semibold text-slate-600 mb-1">
              Raw Material
            </label>
            <Select
              inputId="jc-material"
              instanceId="jc-material"
              options={itemOptions}
              isSearchable
              isClearable
              placeholder="All"
              isLoading={options.isFetching}
              value={itemOptions.find(o => o.value === filters.item_id) || null}
              onChange={opt => setFilters({ ...filters, item_id: opt ? opt.value : '' })}
              styles={{
                control: base => ({
                  ...base,
                  minHeight: '38px',
                  height: '38px',
                  borderColor: '#cbd5e1',
                  borderRadius: '0.375rem',
                  fontSize: '0.875rem'
                }),
                valueContainer: base => ({
                  ...base,
                  height: '38px',
                  padding: '0 8px'
                }),
                indicatorsContainer: base => ({
                  ...base,
                  height: '38px'
                })
              }}
            />
          </div>

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
            <label htmlFor="jc-balance" className="block text-xs font-semibold text-slate-600 mb-1">
              Material Balance
            </label>
            <select
              id="jc-balance"
              className={inputClass}
              value={filters.balance}
              onChange={e => setFilters({ ...filters, balance: e.target.value })}
            >
              <option value="">All</option>
              {balanceOptions.map(status => (
                <option key={status} value={status}>
                  {status}
                </option>
              ))}
            </select>
          </div>

          <div className="lg:col-span-6 flex flex-wrap items-center gap-3 pt-2">
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
              title="Export Excel"
            >
              <FileSpreadsheet size={18} aria-hidden="true" />
              {exporting ? 'Exporting…' : 'Export Excel'}
            </button>
          </div>
        </form>
      </div>

      {(error || options.error || report.error) && (
        <p role="alert" className="p-3 text-red-600 bg-red-50 border border-red-200 rounded-md">
          {error || errorMessage(options.error || report.error)}
        </p>
      )}

      {/* Summary statistics */}
      {summary && (
        <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
          <div className="p-4 bg-white border border-slate-200 rounded-md shadow-xs">
            <p className="text-xs font-medium text-slate-500 uppercase tracking-wider">Total JCs</p>
            <p className="text-2xl font-bold text-slate-900 mt-1">{summary.jc_count}</p>
          </div>
          <div className="p-4 bg-white border border-slate-200 rounded-md shadow-xs">
            <p className="text-xs font-medium text-slate-500 uppercase tracking-wider">Pending JCs</p>
            <p className="text-2xl font-bold text-amber-700 mt-1">{summary.pending_count}</p>
          </div>
          {Object.values(summary.units || {}).map(u => (
            <div key={u.unit_name} className="p-4 bg-white border border-slate-200 rounded-md shadow-xs col-span-2">
              <p className="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Quantities ({u.unit_name})</p>
              <div className="flex gap-4 mt-1 text-sm">
                <div>Created: <span className="font-semibold">{formatQuantity(u.created)}</span></div>
                <div>Received: <span className="font-semibold">{formatQuantity(u.received)}</span></div>
                <div>Pending: <span className="font-bold text-amber-700">{formatQuantity(u.pending)}</span></div>
              </div>
            </div>
          ))}
        </div>
      )}

      {/* Data Table */}
      <div className="bg-white border border-slate-200 rounded-md shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full min-w-[950px] border-collapse text-sm" aria-label="JC-wise received and pending report">
            <thead>
              <tr className="bg-[#333] text-white">
                <th scope="col" className="p-3 text-left font-medium whitespace-nowrap">JC Number</th>
                <th scope="col" className="p-3 text-right font-medium whitespace-nowrap">Created Qty</th>
                <th scope="col" className="p-3 text-right font-medium whitespace-nowrap">Received Qty</th>
                <th scope="col" className="p-3 text-right font-medium whitespace-nowrap">Pending Qty</th>
                <th scope="col" className="p-3 text-left font-medium">JC Item Name</th>
                <th scope="col" className="p-3 text-left font-medium whitespace-nowrap">Unit</th>
                <th scope="col" className="p-3 text-left font-medium">Supplier / Company</th>
              </tr>
            </thead>
            <tbody>
              {report.isFetching ? (
                <tr>
                  <td colSpan={7} className="p-8 text-center text-slate-500" role="status">
                    Loading…
                  </td>
                </tr>
              ) : !rows.length ? (
                <tr>
                  <td colSpan={7} className="p-8 text-center text-slate-500">
                    No matching JC records found.
                  </td>
                </tr>
              ) : (
                rows.map((row, index) => (
                  <tr
                    key={row.challan_id}
                    className={`border-t border-slate-100 hover:bg-slate-50 ${
                      index % 2 === 0 ? 'bg-white' : 'bg-[#fcfdfd]'
                    }`}
                  >
                    <td className="p-3 whitespace-nowrap font-medium text-cyan-700 hover:underline">
                      <Link href={`/dashboard/jc-challan/${row.challan_id}`}>
                        {row.challan_no}
                      </Link>
                    </td>
                    <td className="p-3 text-right tabular-nums whitespace-nowrap">
                      {formatQuantity(row.issued_qty)}
                    </td>
                    <td className="p-3 text-right tabular-nums whitespace-nowrap">
                      {formatQuantity(row.received_qty)}
                    </td>
                    <td className="p-3 text-right tabular-nums whitespace-nowrap font-bold text-amber-900 bg-amber-50/50">
                      {formatQuantity(row.pending_qty)}
                    </td>
                    <td className="p-3 text-slate-800 break-words">
                      {row.jc_item_name}
                    </td>
                    <td className="p-3 whitespace-nowrap text-slate-700">
                      {row.unit_name}
                    </td>
                    <td className="p-3 text-slate-700">
                      {row.supplier}
                    </td>
                  </tr>
                ))
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
    </main>
  );
}
