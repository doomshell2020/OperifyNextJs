'use client';

import { useState } from 'react';
import Select from 'react-select';
import { useQuery } from '@tanstack/react-query';
import { isAxiosError } from 'axios';
import { FileSpreadsheet } from 'lucide-react';
import apiClient from '@/services/apiClient';
import { useAuth } from '@/contexts/AuthContext';
import { usePermission } from '@/contexts/PermissionContext';
import { LegacyPageHeader } from '@/components/ui/LegacyPageHeader';
import { DatePicker } from '@/components/ui/DatePicker';
import { ListPagination, LEGACY_LIST_LIMIT } from '@/components/ui/ListPagination';

const endpoint = '/reports/po-raw-material-pending';
const title = 'PO-Wise Raw Material Pending Order List';
const headers = ['PO NO', 'DATE', 'NAME OF PARTY', 'ITEMS', 'ORDER QTY', 'RECEIVED QTY', 'RATE IN Rs', 'BALANCE QTY'];
const blank = { item_id: '', vendor_id: '', datefrom: '', dateto: '' };
type Option = { id: number; name: string };
type Result = { data: { po_id: number; item_id: number; cells: string[] }[]; pagination: { page: number; limit: number; total: number } };
function message(error: unknown) {
  return isAxiosError(error) ? error.response?.data?.error?.message || error.message : 'Unable to load the report. Please try again.';
}
export default function Page() {
  const { user } = useAuth();
  const { hasPermission } = usePermission();
  if (!hasPermission('legacy:admin/purchaseorder/index') && !hasPermission('legacy:admin/purchaseorder/posummaryreport')) return <p role="alert" className="p-4">You do not have permission to access this report.</p>;
  return <Report key={`${user?.id}:${user?.db}`} company={user?.db || ''} />;
}
function Report({ company }: { company: string }) {
  const [filters, setFilters] = useState(blank);
  const [applied, setApplied] = useState<typeof blank | null>(null);
  const [page, setPage] = useState(1);
  const [generation, setGeneration] = useState(0);
  const [exporting, setExporting] = useState(false);
  const [error, setError] = useState('');
  const options = useQuery({ queryKey: ['po-pending-options', company], enabled: !!company,
    queryFn: async ({ signal }) => (await apiClient.get<{ data: { items: Option[]; suppliers: Option[] } }>(`${endpoint}/options`, { signal })).data.data });
  const report = useQuery({ queryKey: ['po-pending', company, applied, page, generation], enabled: !!company && applied !== null, retry: false,
    queryFn: async ({ signal }) => (await apiClient.get<Result>(endpoint, { params: { ...applied, page, limit: LEGACY_LIST_LIMIT }, signal })).data });
  const search = () => {
    setError(''); setApplied({ ...filters }); setPage(1); setGeneration(value => value + 1);
  };
  const exportExcel = async () => {
    if (!applied) return;
    setExporting(true); setError('');
    try {
      const response = await apiClient.get(`${endpoint}/export`, { params: applied, responseType: 'blob' });
      const url = URL.createObjectURL(response.data);
      const link = document.createElement('a'); link.href = url; link.download = 'PO_Raw_Material_Pending.xlsx'; link.click();
      setTimeout(() => URL.revokeObjectURL(url), 1000);
    } catch (failure) {
      if (isAxiosError(failure) && failure.response?.data instanceof Blob) {
        try { setError(JSON.parse(await failure.response.data.text()).error?.message || 'Unable to export report.'); }
        catch { setError('Unable to export report. Please try again.'); }
      } else setError(message(failure));
    } finally { setExporting(false); }
  };
  const rows = applied ? report.data?.data || [] : [];
  return <main className="max-w-7xl w-full mx-auto px-6 py-8 space-y-6 font-sans">
    <LegacyPageHeader title={title} breadcrumb="Report" />
    <form className="legacy-filter-row" onSubmit={event => { event.preventDefault(); search(); }}>
      <div className="legacy-filter-field"><label htmlFor="pending-item">Item Name</label>
        <Select<Option> inputId="pending-item" instanceId="pending-item" options={options.data?.items || []} isSearchable isClearable placeholder="All raw materials"
          getOptionLabel={item => item.name} getOptionValue={item => String(item.id)} isLoading={options.isFetching}
          value={options.data?.items.find(item => String(item.id) === filters.item_id) || null}
          onChange={item => setFilters({ ...filters, item_id: item ? String(item.id) : '' })} />
      </div>
      <div className="legacy-filter-field"><label htmlFor="pending-supplier">Supplier Name</label>
        <Select<Option> inputId="pending-supplier" instanceId="pending-supplier" options={options.data?.suppliers || []} isSearchable isClearable placeholder="All suppliers"
          getOptionLabel={item => item.name} getOptionValue={item => String(item.id)} isLoading={options.isFetching}
          value={options.data?.suppliers.find(item => String(item.id) === filters.vendor_id) || null}
          onChange={item => setFilters({ ...filters, vendor_id: item ? String(item.id) : '' })} />
      </div>
      <label>Date From<DatePicker aria-label="Date From" value={filters.datefrom} onChange={event => setFilters({ ...filters, datefrom: event.target.value })} /></label>
      <label>Date To<DatePicker aria-label="Date To" value={filters.dateto} onChange={event => setFilters({ ...filters, dateto: event.target.value })} /></label>
      <div className="legacy-filter-actions">
        <button type="submit" className="legacy-button" disabled={exporting}>Search</button>
        <button type="button" className="legacy-button" onClick={() => { setFilters(blank); setApplied(null); setPage(1); setError(''); }} disabled={exporting}>Reset</button>
      </div>
      <button type="button" className="legacy-button ml-auto" aria-label="Export Excel" title="Export Excel" disabled={!applied || report.isFetching || report.isError || exporting} onClick={() => void exportExcel()}><FileSpreadsheet size={20} aria-hidden="true" /></button>
    </form>
    {(error || options.error || (applied && report.error)) && <p role="alert" className="text-red-700">{error || message(options.error || report.error)}</p>}
    {exporting && <p role="status">Exporting…</p>}
    <div className="overflow-x-auto" aria-busy={!!applied && report.isFetching}>
      <table className="w-full min-w-[1100px] border-collapse text-sm"><thead><tr>{headers.map(header => <th key={header} scope="col" className="border border-gray-300 p-2 text-left whitespace-nowrap">{header}</th>)}</tr></thead>
        <tbody>{!applied ? <tr><td colSpan={8} className="border p-4 text-center">Click Search to view the report.</td></tr>
          : report.isFetching ? <tr><td colSpan={8} className="border p-4 text-center" role="status">Loading…</td></tr>
          : report.isError ? <tr><td colSpan={8} className="border p-4 text-center">Unable to load report. Check the error above and search again.</td></tr>
          : !rows.length ? <tr><td colSpan={8} className="border p-4 text-center">No matching records found.</td></tr>
          : rows.map((row, index) => <tr key={`${row.po_id}:${row.item_id}`} className={index % 2 ? 'bg-gray-50' : 'bg-white'}>{row.cells.map((value, column) => <td key={column} className={`border border-gray-300 p-2 ${column >= 4 ? 'text-right whitespace-nowrap' : column < 2 ? 'whitespace-nowrap' : ''}`}>{column < 3 && index > 0 && rows[index - 1].po_id === row.po_id ? '' : value}</td>)}</tr>)}</tbody>
      </table>
    </div>
    {applied && report.data && !report.isError && <ListPagination page={page} limit={LEGACY_LIST_LIMIT} total={report.data.pagination.total} onPageChange={setPage} busy={report.isFetching || exporting} />}
  </main>;
}
