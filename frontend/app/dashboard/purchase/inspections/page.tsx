'use client';

import React, { useState } from 'react';
import { useQuery, useQueryClient } from '@tanstack/react-query';
import grnInspectionService from '../../../../services/grnInspection.service';
import { Loader, AlertCircle, RefreshCw, Search, X, Plus, FileSpreadsheet, Eye } from 'lucide-react';
import { useRouter } from 'next/navigation';
import { PurchaseOrderDetailsModal } from '../../../../components/PurchaseOrderDetailsModal';

export default function GrnInspectionPage() {
  const router = useRouter();
  const [page, setPage] = useState(1);
  const [filters, setFilters] = useState({
    po_id: '',
    vendor_id: '',
    bill_no: ''
  });
  const [selectedPoId, setSelectedPoId] = useState<string | null>(null);
  const [isPoModalOpen, setIsPoModalOpen] = useState(false);

  const { data, isLoading, isError, refetch } = useQuery({
    queryKey: ['grn-inspection', { page, ...filters }],
    queryFn: () => grnInspectionService.listInspections({ page, limit: 10, ...filters }),
    placeholderData: (prev) => prev,
  });

  const handleFilterChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setFilters(prev => ({ ...prev, [e.target.name]: e.target.value }));
    setPage(1);
  };

  const resetFilters = () => {
    setFilters({ po_id: '', vendor_id: '', bill_no: '' });
    setPage(1);
  };

  const handleExport = () => {
    // Placeholder for Export Excel functionality based on current filters
    alert('Export to Excel feature triggered.');
  };

  return (
    <main className="max-w-7xl w-full mx-auto px-6 py-8 space-y-6 select-none font-sans">
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
            GRN Inspection
          </h1>
          <p className="text-sm text-slate-500 font-medium mt-1">Manage Goods Received Note Inspections</p>
        </div>
        <div className="flex items-center gap-3">
          <button onClick={() => refetch()} className="flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-md text-sm font-medium transition">
            <RefreshCw className="w-4 h-4" /> Refresh
          </button>
          <button onClick={handleExport} className="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-md text-sm font-medium transition border border-emerald-200">
            <FileSpreadsheet className="w-4 h-4" /> Export Excel
          </button>
          <button onClick={() => router.push('/dashboard/purchase/inspections/add')} className="flex items-center gap-1.5 px-4 py-1.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-md text-sm font-medium transition shadow-sm">
            <Plus className="w-4 h-4" /> Add Inspection
          </button>
        </div>
      </div>

      <div className="bg-white border border-slate-200 p-4 rounded-xl shadow-sm grid grid-cols-2 md:grid-cols-4 gap-4 items-end">
        <div>
          <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">PO Number</label>
          <input type="text" name="po_id" value={filters.po_id} onChange={handleFilterChange} className="w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 outline-none transition" placeholder="PO-..." />
        </div>
        <div>
          <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Vendor ID</label>
          <input type="text" name="vendor_id" value={filters.vendor_id} onChange={handleFilterChange} className="w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 outline-none transition" placeholder="Supplier ID..." />
        </div>
        <div>
          <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Bill No</label>
          <input type="text" name="bill_no" value={filters.bill_no} onChange={handleFilterChange} className="w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 outline-none" />
        </div>
        <div className="flex gap-2">
          <button onClick={() => refetch()} className="flex-1 bg-cyan-600 hover:bg-cyan-700 text-white rounded-md p-2 flex items-center justify-center font-medium shadow-sm transition">
            <Search className="w-4 h-4 mr-2" /> Search
          </button>
          <button onClick={resetFilters} className="bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-md p-2 flex items-center justify-center font-medium transition" title="Reset Filters">
            <X className="w-5 h-5" />
          </button>
        </div>
      </div>

      <div className="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden relative min-h-[400px]">
        {isLoading && (
          <div className="absolute inset-0 bg-white/50 backdrop-blur-sm z-10 flex flex-col items-center justify-center">
            <Loader className="w-8 h-8 animate-spin text-cyan-600 mb-2" />
            <span className="text-sm font-medium text-slate-600">Loading inspections...</span>
          </div>
        )}
        
        {isError && (
          <div className="absolute inset-0 bg-white z-10 flex flex-col items-center justify-center text-red-500">
            <AlertCircle className="w-10 h-10 mb-2 opacity-50" />
            <span className="text-sm font-semibold">Error loading GRN inspections</span>
          </div>
        )}

        <div className="overflow-x-auto">
          <table className="w-full text-left text-sm border-collapse">
            <thead>
              <tr className="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase text-xs tracking-wider">
                <th className="p-4 font-semibold">Inspection ID</th>
                <th className="p-4 font-semibold">PO Number</th>
                <th className="p-4 font-semibold">Inspection Inward</th>
                <th className="p-4 font-semibold">Bill No</th>
                <th className="p-4 font-semibold">Bill Date</th>
                <th className="p-4 font-semibold">Supplier</th>
                <th className="p-4 font-semibold text-right">Total Qty</th>
                <th className="p-4 font-semibold text-right">Total (₹)</th>
              </tr>
            </thead>
            <tbody>
              {data?.data && data.data.length > 0 ? (
                data.data.map((grn: any) => (
                  <tr key={grn.id} className="border-b border-slate-100 hover:bg-slate-50 transition">
                    <td className="p-4 font-medium text-slate-800">{grn.inspection_id}</td>
                    <td className="p-4 font-medium text-cyan-700 cursor-pointer hover:underline" onClick={() => { setSelectedPoId(grn.po_id); setIsPoModalOpen(true); }}>{grn.po_id}</td>
                    <td className="p-4 text-slate-600">{grn.inward_date?.split('T')[0]}</td>
                    <td className="p-4 text-slate-600">{grn.bill_no}</td>
                    <td className="p-4 text-slate-600">{grn.bill_date?.split('T')[0]}</td>
                    <td className="p-4 text-slate-600">{grn.supplier}</td>
                    <td className="p-4 text-right font-medium">{grn.total_qty}</td>
                    <td className="p-4 text-right font-bold">{parseFloat(grn.total_amt).toLocaleString('en-IN')}</td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={8} className="p-8 text-center text-slate-500">
                    No inspections found matching your filters.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>

        {data?.pagination && data.pagination.totalPages > 1 && (
          <div className="flex items-center justify-between p-4 border-t border-slate-200 bg-slate-50">
            <span className="text-sm text-slate-500">
              Showing page <span className="font-semibold text-slate-700">{data.pagination.page}</span> of <span className="font-semibold text-slate-700">{data.pagination.totalPages}</span>
            </span>
            <div className="flex gap-1">
              <button
                disabled={data.pagination.page === 1}
                onClick={() => setPage(p => Math.max(1, p - 1))}
                className="px-3 py-1.5 border border-slate-200 rounded-md text-sm font-medium text-slate-600 hover:bg-white disabled:opacity-50 transition"
              >
                Previous
              </button>
              <button
                disabled={data.pagination.page === data.pagination.totalPages}
                onClick={() => setPage(p => p + 1)}
                className="px-3 py-1.5 border border-slate-200 rounded-md text-sm font-medium text-slate-600 hover:bg-white disabled:opacity-50 transition"
              >
                Next
              </button>
            </div>
          </div>
        )}
      </div>

      {isPoModalOpen && selectedPoId && (
        <PurchaseOrderDetailsModal
          id={selectedPoId}
          isOpen={isPoModalOpen}
          onClose={() => setIsPoModalOpen(false)}
        />
      )}
    </main>
  );
}
