'use client';
import { LegacyAutocompleteInput } from '@/components/ui/LegacyAutocompleteInput';

import { LegacyPageHeader } from '@/components/ui/LegacyPageHeader';
import { useLegacyActionAccess } from '@/components/ui/useLegacyActionAccess';
import { useListLocation } from '@/components/ui/useListLocation';
import { ListPagination, LEGACY_LIST_LIMIT } from '@/components/ui/ListPagination';
import React, { useState } from 'react';
import { useQuery, useQueryClient } from '@tanstack/react-query';
import apiClient from '@/services/apiClient';
import grnInspectionService from '../../../../services/grnInspection.service';
import { Loader, AlertCircle, RefreshCw, Search, X, Plus, FileSpreadsheet, Eye } from 'lucide-react';
import { useRouter } from 'next/navigation';
import { PurchaseOrderDetailsModal } from '../../../../components/PurchaseOrderDetailsModal';
import { DatePicker } from '@/components/ui/DatePicker';
import { formatContractDate } from '../../../../utils/dateFormatter';
import {usePermission} from '@/contexts/PermissionContext';

export default function GrnInspectionPage() {
  const {hasPermission}=usePermission();
  const canAction=useLegacyActionAccess();
  const router = useRouter();
  const [page, setPage] = useState(1);
  const [filters, setFilters] = useState({
    po_id: '',
    vendor_id: '', vendor_name:'',
    from_date: '', to_date: ''
  });
  const [activeFilters, setActiveFilters] = useState(filters);
  const {data:vendors=[]}=useQuery<{id:number;name:string}[]>({queryKey:['inspection-filter-vendors',filters.vendor_name],queryFn:async()=>(await apiClient.get('/vendors/search',{params:{q:filters.vendor_name}})).data.data,enabled:filters.vendor_name.length>=2});
  const applyFilters = () => { const vendor=vendors.find(row=>row.name===filters.vendor_name);setActiveFilters({ ...filters,vendor_id:vendor?String(vendor.id):filters.vendor_id }); setPage(1); };
  const [selectedPoId, setSelectedPoId] = useState<string | null>(null);
  const [isPoModalOpen, setIsPoModalOpen] = useState(false);

  const locationReady = useListLocation(activeFilters, page, ['po_id', 'vendor_id', 'vendor_name', 'from_date', 'to_date', 'sort', 'direction'], (next, nextPage) => { setActiveFilters(next); setFilters(next); setPage(nextPage); });
  const { data, isLoading, isError, refetch } = useQuery({
    queryKey: ['grn-inspection', { page, ...activeFilters }],
    enabled: locationReady,
    queryFn: () => grnInspectionService.listInspections({ page, limit: LEGACY_LIST_LIMIT, ...activeFilters }),
  });

  const handleFilterChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setFilters(prev => ({ ...prev, [e.target.name]: e.target.value }));
  };

  const resetFilters = () => {
    setFilters({ po_id: '', vendor_id: '', vendor_name:'', from_date: '', to_date: '' });
    setActiveFilters({ po_id: '', vendor_id: '', vendor_name:'', from_date: '', to_date: '' });
    setPage(1);
  };

  const handleExport = async () => {
    try {
      const blob = await grnInspectionService.exportInspections();
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url; link.download = 'GRN_Inspection.xlsx'; link.click();
      URL.revokeObjectURL(url);
    } catch { alert('Failed to export GRN inspections.'); }
  };

  return (
    <main className="max-w-7xl w-full mx-auto px-6 py-8 space-y-6 select-none font-sans">
      <LegacyPageHeader title="Inspection GRN" />

      <div className="legacy-filter-row">
        <div className="legacy-filter-field">
          <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">PO ID</label>
          <input type="text" name="po_id" value={filters.po_id} onChange={handleFilterChange} className="w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 outline-none transition" placeholder="Enter PO ID" />
        </div>
        <label>Vendor<LegacyAutocompleteInput list="inspection-vendor-options" placeholder="Enter Vendor Name" value={filters.vendor_name} onChange={e=>setFilters({...filters,vendor_name:e.target.value,vendor_id:''})}/><datalist id="inspection-vendor-options">{vendors.map(row=><option key={row.id} value={row.name}/>)}</datalist></label>
        <div className="legacy-filter-field"><label className="block text-xs font-semibold text-slate-500 mb-1">Date From</label><DatePicker name="from_date" value={filters.from_date} onChange={handleFilterChange} className="w-full border border-slate-200 rounded-md p-2 text-sm" /></div>
        <div className="legacy-filter-field"><label className="block text-xs font-semibold text-slate-500 mb-1">Date To</label><DatePicker name="to_date" value={filters.to_date} onChange={handleFilterChange} className="w-full border border-slate-200 rounded-md p-2 text-sm" /></div>
        <div className="legacy-filter-actions">
          <button onClick={applyFilters} className="flex-1 bg-cyan-600 hover:bg-cyan-700 text-white rounded-md p-2 flex items-center justify-center font-medium shadow-sm transition">
            <Search className="w-4 h-4 mr-2" /> Search
          </button>
          <button onClick={resetFilters} className="legacy-button" title="Reset Filters">
            Reset
          </button>
        </div>
        <div className="legacy-filter-actions ml-auto">
          {canAction('goodsreceived','grninspectionexcel') && <button type="button" onClick={handleExport} aria-label="Export Excel" title="Export Excel"><FileSpreadsheet size={28}/></button>}
          {hasPermission('legacy:admin/goodsreceived/add') && canAction('goodsreceived','add_inspection_grn') && <button type="button" className="legacy-button" onClick={()=>router.push('/dashboard/purchase/inspections/add')}><Plus size={12}/>Add</button>}
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
                <th className="p-4 font-semibold">Inspection No.</th>
                <th className="p-4 font-semibold">PO Id</th>
                <th className="p-4 font-semibold">Inspection Inward</th>
                <th className="p-4 font-semibold">Bill No.</th>
                <th className="p-4 font-semibold">Bill Date</th>
                <th className="p-4 font-semibold">Supplier</th>
                <th className="p-4 font-semibold text-right">Total Qty.</th>
                <th className="p-4 font-semibold text-right">Total Amount (INR)</th>
              </tr>
            </thead>
            <tbody>
              {data?.data && data.data.length > 0 ? (
                data.data.map((grn: any) => (
                  <tr key={grn.id} className="border-b border-slate-100 hover:bg-slate-50 transition">
                    <td className="p-4 font-medium text-slate-800">{grn.inspection_id}</td>
                    <td className="p-4 font-medium text-cyan-700 cursor-pointer hover:underline" onClick={() => { setSelectedPoId(grn.po_id); setIsPoModalOpen(true); }}>{grn.po_id}</td>
                    <td className="p-4 text-slate-600">{formatContractDate(grn.inward_date)}</td>
                    <td className="p-4 text-slate-600">{grn.bill_no}</td>
                    <td className="p-4 text-slate-600">{formatContractDate(grn.bill_date)}</td>
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

        {data && <ListPagination page={data?.pagination?.page || page} limit={LEGACY_LIST_LIMIT} total={data?.pagination?.total || 0} onPageChange={setPage} busy={isLoading} />}
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
