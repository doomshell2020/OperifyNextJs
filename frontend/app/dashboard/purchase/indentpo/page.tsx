"use client";
import { useLegacyActionAccess } from '@/components/ui/useLegacyActionAccess';
import { openModulePdf } from '@/services/pdf.service';
import { useCallback, useState, useEffect } from "react";
import { indentpoService, Indentpo } from "../../../../services/indentpo.service";
import { Search, Plus, Printer, RefreshCw, FileSpreadsheet, Home } from "lucide-react";
import Link from "next/link";
import { ContractDetailsModal } from "../../../../components/dashboard/ContractDetailsModal";
import { formatQty } from "@/utils/formatters";
import { formatContractDate } from '../../../../utils/dateFormatter';
import { ListPagination } from '@/components/ui/ListPagination';
import { DatePicker } from '@/components/ui/DatePicker';
import { usePermission } from '@/contexts/PermissionContext';
import { useListLocation } from '@/components/ui/useListLocation';
import styles from './page.module.css';
import { isLegacyToday } from '@/utils/legacyActionDate';

const emptyFilters = { contract_name: '', product_name: '', machine_name: '', date_from: '', date_to: '' };
const filterKeys = Object.keys(emptyFilters);

export default function IndentPoListPage() {
  const canAction=useLegacyActionAccess();
  const { hasPermission } = usePermission();
  const [page, setPage] = useState(1);
  const [total, setTotal] = useState(0);
  const [filters, setFilters] = useState(emptyFilters);
  const [draftFilters, setDraftFilters] = useState(emptyFilters);
  const [indents, setIndents] = useState<Indentpo[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState('');
  const [refreshKey, setRefreshKey] = useState(0);
  const [selectedIndentId, setSelectedIndentId] = useState<number | null>(null);
  const [selectedContractId, setSelectedContractId] = useState<number | null>(null);
  const ready = useListLocation(filters, page, filterKeys, (restored, nextPage) => {
    setFilters(restored);
    setDraftFilters(restored);
    setPage(nextPage);
  });
  const fetchIndents = useCallback(() => setRefreshKey(key => key + 1), []);

  useEffect(() => {
    if (!ready) return;
    let active = true;
    setIsLoading(true);
    setError('');
    indentpoService.listPage({ page, ...filters }).then(result => {
      if (!active) return;
      // A deletion can leave the current page beyond the last available page.
      const lastPage = Math.max(1, Math.ceil(result.total / 50));
      if (page > lastPage) {
        setPage(lastPage);
        return;
      }
      setIndents(result.data);
      setTotal(result.total);
    }).catch(() => {
      if (active) setError('Failed to load indents. Please try again.');
    }).finally(() => {
      if (active) setIsLoading(false);
    });
    return () => { active = false; };
  }, [ready, page, filters, refreshKey]);

  const updateDraft = (key: keyof typeof emptyFilters, value: string) => {
    setDraftFilters(current => ({ ...current, [key]: value }));
  };

  return (
    <div className="space-y-4">
      <div className="flex flex-wrap items-center justify-between gap-2">
        <h1 className="text-2xl text-slate-900">Indent Manager</h1>
        <nav aria-label="Breadcrumb" className="flex items-center gap-1 text-xs font-semibold text-slate-700">
          <Link href="/dashboard" className="inline-flex items-center gap-1 hover:text-blue-600"><Home className="h-3 w-3" />Home</Link>
          <span aria-hidden="true">&gt;</span><span>Indent Manager</span>
        </nav>
      </div>

      <div className="bg-white border border-slate-200 p-2.5">
        <form className="flex flex-wrap items-end gap-x-6 gap-y-3 pb-2.5 pt-4" onSubmit={event => {
          event.preventDefault();
          if (draftFilters.date_from && draftFilters.date_to && draftFilters.date_from > draftFilters.date_to) return;
          const next = { ...draftFilters, contract_name: draftFilters.contract_name.trim(), product_name: draftFilters.product_name.trim(), machine_name: draftFilters.machine_name.trim() };
          setDraftFilters(next);
          setFilters(next);
          setPage(1);
          fetchIndents();
        }}>
          <label className="flex min-w-0 flex-1 basis-[130px] flex-col gap-1">
            Contract Name
            <input className="h-8 w-full" placeholder="Enter Contract Name" value={draftFilters.contract_name} onChange={event => updateDraft('contract_name', event.target.value)} />
          </label>
          <label className="flex min-w-0 flex-1 basis-[130px] flex-col gap-1">
            Product Name
            <input className="h-8 w-full" placeholder="Enter Product Name" value={draftFilters.product_name} onChange={event => updateDraft('product_name', event.target.value)} />
          </label>
          <label className="flex min-w-0 flex-1 basis-[130px] flex-col gap-1">
            Machine Name
            <input className="h-8 w-full" placeholder="Enter Machine Name" value={draftFilters.machine_name} onChange={event => updateDraft('machine_name', event.target.value)} />
          </label>
          <label className="flex min-w-0 flex-1 basis-[130px] flex-col gap-1">
            Start Date
            <DatePicker aria-label="Start Date" placeholder="Start Date" name="date_from" dateFormat="dd-MM-yyyy" className="h-8 w-full" value={draftFilters.date_from} onChange={event => updateDraft('date_from', event.target.value)} />
          </label>
          <label className="flex min-w-0 flex-1 basis-[130px] flex-col gap-1">
            End Date
            <DatePicker aria-label="End Date" placeholder="End Date" name="date_to" dateFormat="dd-MM-yyyy" className="h-8 w-full" value={draftFilters.date_to} onChange={event => updateDraft('date_to', event.target.value)} />
          </label>
          <div className="mb-1 flex gap-1">
            <button type="submit" className={styles.primaryButton} disabled={!ready}>Search</button>
            <button type="button" className={styles.primaryButton} disabled={!ready} onClick={() => {
              setDraftFilters(emptyFilters);
              setFilters(emptyFilters);
              setPage(1);
              fetchIndents();
            }}>Reset</button>
          </div>
          <div className="mb-1 ml-auto flex items-center gap-3">
            {canAction('indentpo','indentpoexcel') && <button type="button" title="Export Excel" aria-label="Export Excel" className="p-1 text-slate-800 hover:text-blue-600" disabled={!ready} onClick={async () => {
              try { await indentpoService.exportExcel(filters); } catch { alert('Unable to export indents'); }
            }}><FileSpreadsheet className="h-7 w-7" /></button>}
            {hasPermission('legacy:admin/indentpo/add') && <Link href="/dashboard/purchase/indentpo/new" className={styles.primaryButton}><Plus className="h-3 w-3" />Add</Link>}
          </div>
        </form>
        {draftFilters.date_from && draftFilters.date_to && draftFilters.date_from > draftFilters.date_to && <p role="alert" className="mb-2 text-xs text-red-600">Start Date must be on or before End Date.</p>}
        {error && <div role="alert" className="mb-3 flex items-center gap-3 text-sm text-red-600">{error}<button type="button" className="underline" onClick={fetchIndents}>Retry</button></div>}
        <div className="overflow-x-auto">
          <table className={`${styles.indentTable} w-full text-left`}>
            <thead><tr>
              <th scope="col">S No.</th>
              <th scope="col">Indent Id</th>
              <th scope="col">Contract name</th>
              <th scope="col">Product</th>
              <th scope="col">Machine Name</th>
              <th scope="col">Issue By</th>
              <th scope="col">Issue Date</th>
              <th scope="col">Action</th>
            </tr></thead>
            <tbody>
              {isLoading ? <tr><td colSpan={8} className="text-center"><div className="flex items-center justify-center gap-2 py-6"><RefreshCw className="h-4 w-4 animate-spin" />Loading...</div></td></tr>
                : error ? <tr><td colSpan={8} className="text-center">Unable to load indents</td></tr>
                : indents.length === 0 ? <tr><td colSpan={8} className="text-center"><div className="flex items-center justify-center gap-2 py-6"><Search className="h-4 w-4" />No indents found matching your criteria</div></td></tr>
                : indents.map((indent, index) => <tr key={indent.id}>
                  <td>{(page - 1) * 50 + index + 1}</td>
                  <td><button type="button" className="font-semibold text-blue-500 hover:underline" disabled={!canAction('indentpo','viewindentpodetail')} onClick={() => setSelectedIndentId(indent.id)}>{indent.indent_id}</button></td>
                  <td><button type="button" className="text-left font-semibold text-blue-500 hover:underline" disabled={!indent.contract_id || !canAction('production','viewcontractdetail')} onClick={() => setSelectedContractId(indent.contract_id || null)}>{indent.contract_name}{indent.workorder ? `(${indent.workorder})` : ''}</button></td>
                  <td>{indent.product_name}</td>
                  <td>{indent.machine_name}</td>
                  <td>{indent.issued_name}</td>
                  <td className="whitespace-nowrap">{formatContractDate(indent.issue_date)}</td>
                  <td><div className="flex items-center gap-2 whitespace-nowrap">
                    {isLegacyToday(indent.issue_date) && hasPermission('legacy:admin/indentpo/edit') && <Link href={`/dashboard/purchase/indentpo/${indent.indent_id}/edit`} className="text-blue-600 hover:underline">Edit</Link>}
                    {isLegacyToday(indent.issue_date) && hasPermission('legacy:admin/indentpo/delete') && <button type="button" className="text-red-600 hover:underline" onClick={async () => {
                      if (!window.confirm(`Delete indent ${indent.indent_id}?`)) return;
                      try { await indentpoService.remove(String(indent.indent_id)); fetchIndents(); } catch { alert('Unable to delete indent'); }
                    }}>Delete</button>}

                  </div></td>
                </tr>)}
            </tbody>
          </table>
        </div>
        <ListPagination page={page} limit={50} total={total} busy={isLoading} onPageChange={setPage} />
      </div>
      {selectedIndentId && <IndentDetailsModal id={selectedIndentId} onClose={() => setSelectedIndentId(null)} />}
      {selectedContractId && <ContractDetailsModal contractId={selectedContractId} onClose={() => setSelectedContractId(null)} />}
    </div>
  );
}

// Simple button component to replace shadcn/ui Button
function Button({ children, onClick, variant = 'primary', disabled, className = '' }: any) {
  const baseStyles = "inline-flex items-center justify-center rounded-lg text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none px-4 py-2";
  const variants = {
    primary: "bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500",
    outline: "border border-slate-300 bg-transparent hover:bg-slate-50 text-slate-700 focus:ring-slate-500",
    ghost: "bg-transparent hover:bg-slate-100 text-slate-700",
  };

  return (
    <button
      onClick={onClick}
      disabled={disabled}
      className={`${baseStyles} ${variants[variant as keyof typeof variants]} ${className}`}
    >
      {children}
    </button>
  );
}

function IndentDetailsModal({ id, onClose }: { id: number, onClose: () => void }) {
  const canAction = useLegacyActionAccess();
  const [details, setDetails] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    indentpoService.getIndentPoDetails(id).then(data => {
      setDetails(data);
      setLoading(false);
    }).catch(err => {
      alert("Failed to load details");
      onClose();
    });
  }, [id]);

  const handlePrint = () => {
    if (!details?.header?.indent_id) return;
    void openModulePdf(`/indentpo/view-details/${encodeURIComponent(String(id))}/pdf`);
  };

  return (
    <div className="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
      <div className="bg-white rounded-xl shadow-xl w-full max-w-[900px] max-h-[90vh] flex flex-col">
        {loading ? (
          <div className="p-12 flex justify-center"><RefreshCw className="w-8 h-8 animate-spin text-blue-600" /></div>
        ) : details ? (
          <>
            <div className="flex justify-between items-center p-4 border-b">
              <h2 className="text-xl font-bold">Indent Details</h2>
              <div className="flex gap-2">
                {canAction('indentpo','viewindentpopdf') && <Button onClick={handlePrint} variant="primary">
                  <Printer className="w-4 h-4 mr-2" />
                  Print
                </Button>}
                <button onClick={onClose} className="p-2 hover:bg-slate-100 rounded-full">âœ•</button>
              </div>
            </div>

            <div className="p-6 overflow-y-auto" id="printable-indent-modal">
              <div className="grid grid-cols-2 gap-4 mb-8 text-sm">
                <div>
                  <div className="font-semibold text-slate-500">Indent Id :- <span className="font-normal text-slate-900">{details.header.indent_id}</span></div>
                  <div className="font-semibold text-slate-500 mt-2">Product :- <span className="font-normal text-slate-900">{details.header.product_name}</span></div>
                  <div className="font-semibold text-slate-500 mt-2">Created By :- <span className="font-normal text-slate-900 capitalize">{details.header.created_by || '-'}</span></div>
                  <div className="font-semibold text-slate-500 mt-2">Issue Date :- <span className="font-normal text-slate-900">{formatContractDate(details.header.issue_date)}</span></div>
                </div>
                <div>
                  <div className="font-semibold text-slate-500">Contract name :- <span className="font-normal text-slate-900">{details.header.contract_name}({details.header.workorder})</span></div>
                  <div className="font-semibold text-slate-500 mt-2">Machine Name :- <span className="font-normal text-slate-900">{details.header.machine_name}</span></div>
                  <div className="font-semibold text-slate-500 mt-2">Issue By :- <span className="font-normal text-slate-900">{details.header.issued_name}</span></div>
                </div>
              </div>

              <h3 className="text-center font-bold text-lg mb-4">Raw Material</h3>
              <table className="w-full text-sm text-left border">
                <thead className="bg-slate-50 border-b">
                  <tr>
                    <th className="px-4 py-2 border-r w-16">S.No.</th>
                    <th className="px-4 py-2 border-r">Item</th>
                    <th className="px-4 py-2 border-r text-right w-32">Issue Qty</th>
                    <th className="px-4 py-2 w-24">UOM</th>
                  </tr>
                </thead>
                <tbody>
                  {details.items?.map((item: any, idx: number) => (
                    <tr key={idx} className="border-b last:border-0">
                      <td className="px-4 py-2 border-r">{idx + 1}.</td>
                      <td className="px-4 py-2 border-r font-medium">{item.item_name}</td>
                      <td className="px-4 py-2 border-r text-right">{item.issue_qty ? formatQty(item.issue_qty) : 0}</td>
                      <td className="px-4 py-2">{item.uom}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </>
        ) : null}
      </div>
    </div>
  );
}

