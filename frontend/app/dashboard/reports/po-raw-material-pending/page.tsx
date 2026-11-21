'use client';

import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { isAxiosError } from 'axios';
import { FileSpreadsheet } from 'lucide-react';
import apiClient from '@/services/apiClient';
import { useAuth } from '@/contexts/AuthContext';
import { usePermission } from '@/contexts/PermissionContext';
import { LegacyPageHeader } from '@/components/ui/LegacyPageHeader';
import { DatePicker } from '@/components/ui/DatePicker';
import { ListPagination, LEGACY_LIST_LIMIT } from '@/components/ui/ListPagination';
import { PoMultiselect, Option } from './PoMultiselect';
import './pending-order.css';

const endpoint = '/reports/po-raw-material-pending';
const title = 'PO-Wise Raw Material Pending Order List';
const headers = ['PO NO', 'DATE', 'NAME OF PARTY', 'ITEMS', 'ORDER QTY', 'RECEIVED QTY', 'RATE IN Rs', 'BALANCE QTY'];

interface Filters {
  item_id: string[];
  vendor_id: string[];
  postatus: string;
  datefrom: string;
  dateto: string;
}

const blank: Filters = { item_id: [], vendor_id: [], postatus: '', datefrom: '', dateto: '' };

type Result = {
  data: { po_id: number; item_id: number; cells: string[] }[];
  pagination: { page: number; limit: number; total: number };
};

function message(error: unknown) {
  return isAxiosError(error)
    ? error.response?.data?.error?.message || error.message
    : 'Unable to load the report. Please try again.';
}

export default function Page() {
  const { user } = useAuth();
  const { hasPermission } = usePermission();
  if (
    !hasPermission('legacy:admin/purchaseorder/index') &&
    !hasPermission('legacy:admin/purchaseorder/posummaryreport')
  ) {
    return (
      <p role="alert" className="p-4">
        You do not have permission to access this report.
      </p>
    );
  }
  return <Report key={`${user?.id}:${user?.db}`} company={user?.db || ''} />;
}

function Report({ company }: { company: string }) {
  const [filters, setFilters] = useState<Filters>(blank);
  const [applied, setApplied] = useState<Filters | null>(null);
  const [page, setPage] = useState(1);
  const [generation, setGeneration] = useState(0);
  const [exporting, setExporting] = useState(false);
  const [error, setError] = useState('');

  const options = useQuery({
    queryKey: ['po-pending-options', company],
    enabled: !!company,
    queryFn: async ({ signal }) =>
      (
        await apiClient.get<{ data: { items: Option[]; suppliers: Option[] } }>(
          `${endpoint}/options`,
          { signal }
        )
      ).data.data
  });

  const report = useQuery({
    queryKey: ['po-pending', company, applied, page, generation],
    enabled: !!company && applied !== null,
    retry: false,
    queryFn: async ({ signal }) => {
      const params: Record<string, string | number> = {
        page,
        limit: LEGACY_LIST_LIMIT
      };
      if (applied?.item_id.length) params.item_id = applied.item_id.join(',');
      if (applied?.vendor_id.length) params.vendor_id = applied.vendor_id.join(',');
      if (applied?.postatus) params.postatus = applied.postatus;
      if (applied?.datefrom) params.datefrom = applied.datefrom;
      if (applied?.dateto) params.dateto = applied.dateto;

      return (
        await apiClient.get<Result>(endpoint, {
          params,
          signal
        })
      ).data;
    }
  });

  const search = () => {
    setError('');
    setApplied({ ...filters });
    setPage(1);
    setGeneration(value => value + 1);
  };

  const exportExcel = async () => {
    if (!applied) return;
    setExporting(true);
    setError('');
    try {
      const params: Record<string, string> = {};
      if (applied.item_id.length) params.item_id = applied.item_id.join(',');
      if (applied.vendor_id.length) params.vendor_id = applied.vendor_id.join(',');
      if (applied.postatus) params.postatus = applied.postatus;
      if (applied.datefrom) params.datefrom = applied.datefrom;
      if (applied.dateto) params.dateto = applied.dateto;

      const response = await apiClient.get(`${endpoint}/export`, {
        params,
        responseType: 'blob'
      });
      const url = URL.createObjectURL(response.data);
      const link = document.createElement('a');
      link.href = url;
      link.download = 'PO_Raw_Material_Pending.xlsx';
      link.click();
      setTimeout(() => URL.revokeObjectURL(url), 1000);
    } catch (failure) {
      if (isAxiosError(failure) && failure.response?.data instanceof Blob) {
        try {
          setError(JSON.parse(await failure.response.data.text()).error?.message || 'Unable to export report.');
        } catch {
          setError('Unable to export report. Please try again.');
        }
      } else setError(message(failure));
    } finally {
      setExporting(false);
    }
  };

  const handleReset = () => {
    setFilters(blank);
    setApplied(null);
    setPage(1);
    setError('');
  };

  const rows = applied ? report.data?.data || [] : [];

  return (
    <main className="content-wrapper pending-order-page max-w-7xl w-full mx-auto px-6 py-8 space-y-6 font-sans">
      <LegacyPageHeader title={title} breadcrumb="Report" />

      <div className="box">
        <div className="box-header">
          <form
            id="pending-order-search"
            onSubmit={event => {
              event.preventDefault();
              search();
            }}
          >
            <input type="hidden" name="search" value="1" />
            <div className="form-group">
              <div className="row pending-order-filters">
                <div className="col po-multiselect-col">
                  <label className="control-label">Item Name</label>
                  <PoMultiselect
                    id="pending-item"
                    name="item_id"
                    containerId="pending-item-ms"
                    options={options.data?.items || []}
                    selected={filters.item_id}
                    onChange={selected => setFilters(prev => ({ ...prev, item_id: selected }))}
                    defaultLabel="All Raw Materials"
                    searchPlaceholder="Search Raw Materials..."
                    emptyText="No matching items found"
                  />
                </div>

                <div className="col po-multiselect-col">
                  <label className="control-label">Supplier Name</label>
                  <PoMultiselect
                    id="pending-supplier"
                    name="vendor_id"
                    containerId="pending-supplier-ms"
                    options={options.data?.suppliers || []}
                    selected={filters.vendor_id}
                    onChange={selected => setFilters(prev => ({ ...prev, vendor_id: selected }))}
                    defaultLabel="All Suppliers"
                    searchPlaceholder="Search Suppliers..."
                    emptyText="No matching suppliers found"
                  />
                </div>

                <div className="col">
                  <label htmlFor="pending-status" className="control-label">
                    PO Status
                  </label>
                  <select
                    className="form-control"
                    id="pending-status"
                    name="postatus"
                    value={filters.postatus}
                    onChange={e => setFilters(prev => ({ ...prev, postatus: e.target.value }))}
                  >
                    <option value="">All</option>
                    <option value="O">Open</option>
                    <option value="C">Close</option>
                  </select>
                </div>

                <div className="col">
                  <label htmlFor="pending-from" className="control-label">
                    Date From
                  </label>
                  <DatePicker
                    id="pending-from"
                    name="datefrom"
                    aria-label="Date From"
                    placeholder="Date From"
                    className="form-control"
                    value={filters.datefrom}
                    onChange={event => setFilters(prev => ({ ...prev, datefrom: event.target.value }))}
                  />
                </div>

                <div className="col">
                  <label htmlFor="pending-to" className="control-label">
                    Date To
                  </label>
                  <DatePicker
                    id="pending-to"
                    name="dateto"
                    aria-label="Date To"
                    placeholder="Date To"
                    className="form-control"
                    value={filters.dateto}
                    onChange={event => setFilters(prev => ({ ...prev, dateto: event.target.value }))}
                  />
                </div>

                <div className="pending-order-actions">
                  <button type="submit" className="btn btn4 btn_pdf myscl-btn date legacy-button" disabled={exporting}>
                    Search
                  </button>
                  <button
                    type="button"
                    id="pending-order-reset"
                    className="excelbtn btn legacy-button"
                    onClick={handleReset}
                    disabled={exporting}
                  >
                    Reset
                  </button>
                </div>

                <div className="col pending-order-export">
                  <button
                    type="button"
                    className="excelbtn btn pull-right"
                    aria-label="Export Excel"
                    title="Export Excel"
                    disabled={!applied || report.isFetching || report.isError || exporting}
                    onClick={() => void exportExcel()}
                  >
                    <FileSpreadsheet className="text-green-600" size={26} aria-hidden="true" />
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>

      {(error || options.error || (applied && report.error)) && (
        <p role="alert" className="text-red-700">
          {error || message(options.error || report.error)}
        </p>
      )}
      {exporting && <p role="status">Exporting…</p>}

      <div className="overflow-x-auto" aria-busy={!!applied && report.isFetching}>
        <table className="w-full min-w-[1100px] border-collapse text-sm pending-order-table">
          <thead>
            <tr>
              {headers.map(header => (
                <th key={header} scope="col" className="border border-gray-300 p-2 text-left whitespace-nowrap">
                  {header}
                </th>
              ))}
            </tr>
          </thead>
          <tbody>
            {!applied ? (
              <tr>
                <td colSpan={8} className="border p-4 text-center">
                  Click Search to view the report.
                </td>
              </tr>
            ) : report.isFetching ? (
              <tr>
                <td colSpan={8} className="border p-4 text-center" role="status">
                  Loading…
                </td>
              </tr>
            ) : report.isError ? (
              <tr>
                <td colSpan={8} className="border p-4 text-center">
                  Unable to load report. Check the error above and search again.
                </td>
              </tr>
            ) : !rows.length ? (
              <tr>
                <td colSpan={8} className="border p-4 text-center">
                  No matching records found.
                </td>
              </tr>
            ) : (
              rows.map((row, index) => (
                <tr key={`${row.po_id}:${row.item_id}`} className={index % 2 ? 'bg-gray-50' : 'bg-white'}>
                  {row.cells.map((value, column) => (
                    <td
                      key={column}
                      className={`border border-gray-300 p-2 ${
                        column >= 4 ? 'text-right whitespace-nowrap' : column < 2 ? 'whitespace-nowrap' : ''
                      }`}
                    >
                      {column < 3 && index > 0 && rows[index - 1].po_id === row.po_id ? '' : value}
                    </td>
                  ))}
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>

      {applied && report.data && !report.isError && (
        <ListPagination
          page={page}
          limit={LEGACY_LIST_LIMIT}
          total={report.data.pagination.total}
          onPageChange={setPage}
          busy={report.isFetching || exporting}
        />
      )}
    </main>
  );
}
