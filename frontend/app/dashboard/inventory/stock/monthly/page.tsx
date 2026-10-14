'use client';
import { formatQty } from '@/utils/formatters';

import { useState } from 'react';
import Link from 'next/link';
import { useQuery } from '@tanstack/react-query';
import { isAxiosError } from 'axios';
import { FileSpreadsheet } from 'lucide-react';
import { useAuth } from '@/contexts/AuthContext';
import { LegacyPageHeader } from '@/components/ui/LegacyPageHeader';
import { ListPagination, LEGACY_LIST_LIMIT } from '@/components/ui/ListPagination';
import { ProductSearchSelect } from '@/components/ProductSearchSelect';
import { monthlyStockService, MonthlyStockFilters } from '@/services/monthlyStock.service';
import { CategoryCheckboxSelect } from '../CategoryCheckboxSelect';

const months = Array.from({ length: 12 }, (_, index) => new Date(Date.UTC(2000,index,1)).toLocaleString('en-US',{month:'long',timeZone:'UTC'}));
function errorMessage(error: unknown) {
  return isAxiosError(error) ? error.response?.data?.error?.message || error.message : 'Unable to load the stock report.';
}
function monthLabel(value: string) {
  const [year,month] = value.split('-').map(Number);
  return `${months[month-1].slice(0,3)}(1-${new Date(Date.UTC(year,month,0)).getUTCDate()})`;
}

export default function MonthlyStockPage() {
  const { user } = useAuth();
  // Switching company discards the old company's filters, rows and pending UI.
  return <MonthlyStockReport key={`${user?.id}:${user?.db}`} company={user?.db || ''} />;
}

function MonthlyStockReport({ company }: { company: string }) {
  const currentYear = Number(new Intl.DateTimeFormat('en-US',{year:'numeric',timeZone:'Asia/Kolkata'}).format(new Date()));
  const [year,setYear] = useState(String(currentYear));
  const [month,setMonth] = useState('');
  const [product,setProduct] = useState('');
  const [categories,setCategories] = useState<string[]>([]);
  const [applied,setApplied] = useState<MonthlyStockFilters | null>(null);
  const [page,setPage] = useState(1);
  const pageSize = LEGACY_LIST_LIMIT;
  const [error,setError] = useState('');
  const [exporting,setExporting] = useState(false);
  const options = useQuery({ queryKey:['monthly-stock-options',company], queryFn:monthlyStockService.getOptions });
  const report = useQuery({ queryKey:['monthly-stock',company,applied,page,pageSize], enabled:applied !== null,
    queryFn:() => monthlyStockService.getReport(applied!,page,pageSize) });
  const search = () => {
    if (!/^\d{4}$/.test(year) || Number(year)<1000 || Number(year)>9998) { setError('Enter a valid year.'); return; }
    setError(''); setPage(1);
    const filters = { year:Number(year),month,product_id:product,category_ids:categories };
    if (JSON.stringify(applied) === JSON.stringify(filters) && page === 1) void report.refetch();
    else setApplied(filters);
  };
  const pagination = report.data?.pagination;
  const products = options.data?.products.filter(item => !categories.length || categories.includes(String(item.category_id))) || [];
  const headers = ['S.No.','Item ID','Month','Product Name','Category','Opening Stock','Received Stock','Dispatched Stock','Closing Stock'];
  const inputClass = 'w-full text-sm border border-slate-300 rounded-md p-2 bg-white';
  return <main className="stock-register-page max-w-7xl w-full mx-auto px-6 py-8 space-y-6 font-sans">
    <LegacyPageHeader title="Month-wise Total Stock Report" breadcrumb="Stock Register" />
    <Link href="/dashboard/inventory/stock" className="text-sm text-cyan-700 hover:underline">Back to Stock Register</Link>
    <div className="stock-register-box bg-white border border-slate-200">
      <form className="grid grid-cols-1 md:grid-cols-4 gap-4 p-4" onSubmit={event => { event.preventDefault(); search(); }}>
        <label className="text-xs font-semibold text-slate-600">Year
          <input aria-label="Year" type="number" min={1000} max={9998} required value={year} onChange={event => setYear(event.target.value)} className={inputClass} />
        </label>
        <label className="text-xs font-semibold text-slate-600">Month
          <select aria-label="Month" value={month} onChange={event => setMonth(event.target.value)} className={inputClass}>
            <option value="">All Months</option>{months.map((name,index) => <option key={name} value={index+1}>{name}</option>)}
          </select>
        </label>
        <div><label className="block text-xs font-semibold text-slate-600 mb-1">Category</label>
          <CategoryCheckboxSelect categories={options.data?.categories || []} value={categories} onChange={value => { setCategories(value); setProduct(''); }} />
        </div>
        <div><label className="block text-xs font-semibold text-slate-600 mb-1">Product</label>
          <ProductSearchSelect products={products} value={product} onChange={setProduct} placeholder="All Products" />
        </div>
        <div className="md:col-span-4 flex flex-wrap items-center gap-3">
          <button disabled={options.isPending || options.isError || report.isFetching || exporting} className="legacy-button" type="submit">{report.isFetching ? 'Loading…' : 'Search'}</button>
          <button type="button" disabled={exporting} className="legacy-button" onClick={() => { setYear(String(currentYear)); setMonth(''); setProduct(''); setCategories([]); setApplied(null); setPage(1); setError(''); }}>Reset</button>
          <button type="button" disabled={!applied || report.isFetching || report.isError || exporting} className="legacy-button flex items-center gap-2" onClick={async () => {
            if (!applied) return;
            setExporting(true); setError('');
            try { await monthlyStockService.exportExcel(applied); } catch (failure) { setError(errorMessage(failure)); } finally { setExporting(false); }
          }}><FileSpreadsheet size={18} />{exporting ? 'Exporting…' : 'Export Excel'}</button>
        </div>
      </form>
      {(error || options.error || report.error) && <p role="alert" className="p-3 text-red-600">{error || errorMessage(options.error || report.error)}</p>}
      {applied && <p className="px-4 pb-3 text-sm text-slate-600">
        {applied.month ? `${months[Number(applied.month)-1]} ${applied.year}: ` : `${applied.year}: `}
        Each product row shows total received and dispatched quantities for the entire month. Opening Stock is the balance before the month starts; Closing Stock is the balance after the month ends.
      </p>}
      <div className="overflow-x-auto border-t border-slate-200">
        <table className="w-full text-left text-sm text-slate-600">
          <thead className="bg-slate-50 text-slate-800 font-bold"><tr>{headers.map(header => <th key={header} className="p-3 whitespace-nowrap">{header}</th>)}</tr></thead>
          <tbody>{report.isFetching ? <tr><td colSpan={headers.length} className="p-8 text-center" role="status">Fetching data…</td></tr>
            : applied && report.data?.data.length ? report.data.data.map((row,index) => <tr key={`${row.month}:${row.item_id}`} className="border-t border-slate-100 hover:bg-slate-50">
              <td className="p-3">{(page-1)*pageSize+index+1}</td><td className="p-3">{row.item_id}</td><td className="p-3 whitespace-nowrap" title={row.month}>{monthLabel(row.month)}</td>
              <td className="p-3">{row.item_name}</td><td className="p-3">{row.category_name}</td>
              {[row.opening_stock,row.received_stock,row.dispatched_stock,row.closing_stock].map((value,column) => <td key={column} className="p-3 text-right tabular-nums">{formatQty(value)}</td>)}
            </tr>) : <tr><td colSpan={headers.length} className="p-8 text-center">{applied ? 'No Record Found' : 'Select filters and search to view monthly stock.'}</td></tr>}</tbody>
        </table>
      </div>
      {applied && pagination && !report.isError && <ListPagination page={pagination.page} limit={pageSize} total={pagination.total} onPageChange={setPage} busy={report.isFetching} />}
    </div>
  </main>;
}
