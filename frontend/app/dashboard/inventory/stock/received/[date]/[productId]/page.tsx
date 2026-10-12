'use client';

import { useEffect, useState } from 'react';
import { useParams } from 'next/navigation';
import Link from 'next/link';
import { FileText } from 'lucide-react';
import { LegacyPageHeader } from '@/components/ui/LegacyPageHeader';
import { useLegacyActionAccess } from '@/components/ui/useLegacyActionAccess';
import { stockRegisterService, ReceivedStockDetail } from '@/services/stockRegister.service';
import { openPurchaseOrderPdf } from '@/services/purchaseOrderPdf.service';
import { formatContractDate } from '@/utils/dateFormatter';

export default function ReceivedStockPage() {
  const { date, productId } = useParams<{ date: string; productId: string }>();
  const can = useLegacyActionAccess();
  const [rows, setRows] = useState<ReceivedStockDetail[]>([]);
  const [productName, setProductName] = useState('');
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [search, setSearch] = useState('');
  const [page, setPage] = useState(1);

  useEffect(() => {
    let active = true;
    setLoading(true);
    setError('');
    setRows([]);
    setProductName('');
    setSearch('');
    setPage(1);
    stockRegisterService.getReceivedStockPage({ date, product_id: productId })
      .then(result => {
        if (active) { setRows(result.data); setProductName(result.product.item_name); }
      })
      .catch(err => {
        if (active) setError(err?.response?.data?.error?.message || 'Failed to fetch received stock details.');
      })
      .finally(() => { if (active) setLoading(false); });
    return () => { active = false; };
  }, [date, productId]);

  // PHP example14 uses a fixed ten-row page, search, and descending stock ID order.
  const filtered = rows.filter(row => [row.po_id, formatContractDate(row.po_date),
    Number(row.is_revised) > 0 ? `Revised-${row.is_revised}` : '', row.vendor_name,
    row.contact_no, row.email, formatContractDate(row.inwarddate), row.bill_no,
    formatContractDate(row.bill_date)].join(' ').toLowerCase().includes(search.toLowerCase()));
  const pageCount = Math.max(1, Math.ceil(filtered.length / 10));
  const currentPage = Math.min(page, pageCount);
  const offset = (currentPage - 1) * 10;

  const openPdf = async (id: number) => {
    try { await openPurchaseOrderPdf(id); }
    catch (err) { setError(err instanceof Error ? err.message : 'Failed to open purchase order PDF.'); }
  };

  return (
    <main className="max-w-7xl w-full mx-auto px-6 py-8 space-y-6">
      <LegacyPageHeader title={`Received Item Name-${productName}`} />
      <div className="flex justify-between text-sm">
        <Link href="/dashboard/inventory/stock" className="text-cyan-700 hover:underline">Back to Stock Register</Link>
        <span>Date: {formatContractDate(date)}</span>
      </div>
      <div className="bg-white border border-slate-200 p-4 space-y-4">
        {error && <p role="alert" className="text-red-600">{error}</p>}
        <div className="flex justify-end">
          <label className="text-sm">Search: <input type="search" value={search}
            onChange={event => { setSearch(event.target.value); setPage(1); }}
            className="ml-2 border border-slate-300 rounded px-2 py-1" /></label>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full text-left text-sm text-slate-700 border-collapse">
            <thead className="bg-slate-100"><tr>
              {['S.No.', 'PO ID / Generated Date', 'Vendor Detail', 'Inward Date', 'Bill No. / Bill Date'].map(title =>
                <th key={title} className="border border-slate-200 p-3">{title}</th>)}
            </tr></thead>
            <tbody>
              {loading ? <tr><td colSpan={5} className="p-6 text-center">Loading details...</td></tr> :
                filtered.length === 0 ? <tr><td colSpan={5} className="p-6 text-center">No records found.</td></tr> :
                filtered.slice(offset, offset + 10).map((row, index) => <tr key={row.id} className="odd:bg-slate-50">
                  <td className="border border-slate-200 p-3">{offset + index + 1}</td>
                  <td className="border border-slate-200 p-3">
                    {row.po_id || '-'} / {formatContractDate(row.po_date) || '-'}{' '}
                    {Number(row.is_revised) > 0 && <small className="text-red-600">Revised-{row.is_revised} </small>}
                    {row.purchaseorder_id && can('purchaseorder', 'view') &&
                      <button type="button" onClick={() => openPdf(Number(row.purchaseorder_id))}
                        title={`Open PO ${row.po_id} revision ${row.is_revised || 0} PDF`}
                        aria-label={`Open PO ${row.po_id} revision ${row.is_revised || 0} PDF`}
                        className="inline-flex align-middle text-red-600 hover:text-red-800"><FileText size={20} /></button>}
                  </td>
                  <td className="border border-slate-200 p-3">{row.vendor_name || '-'}<br />
                    <b>Contact No. </b>{row.contact_no || '-'}<br /><b>Email </b>{row.email || '-'}</td>
                  <td className="border border-slate-200 p-3">{formatContractDate(row.inwarddate) || '-'}</td>
                  <td className="border border-slate-200 p-3">{row.bill_no || '-'} / {formatContractDate(row.bill_date) || '-'}</td>
                </tr>)}
            </tbody>
          </table>
        </div>
        {!loading && <div className="flex justify-between items-center text-sm gap-4">
          <span>Showing {filtered.length ? offset + 1 : 0} to {Math.min(offset + 10, filtered.length)} of {filtered.length} entries
            {search && ` (filtered from ${rows.length} total entries)`}</span>
          <nav aria-label="Received stock pagination" className="flex items-center gap-3">
            <button disabled={currentPage === 1} onClick={() => setPage(currentPage - 1)} className="border px-3 py-1 disabled:opacity-40">Previous</button>
            <span>Page {currentPage} of {pageCount}</span>
            <button disabled={currentPage === pageCount} onClick={() => setPage(currentPage + 1)} className="border px-3 py-1 disabled:opacity-40">Next</button>
          </nav>
        </div>}
      </div>
    </main>
  );
}
