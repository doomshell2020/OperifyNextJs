'use client';

import React, { useState, useEffect } from 'react';
import { useQuery } from '@tanstack/react-query';
import { grnService } from '../../../../services/grn.service';
import { Loader, AlertCircle, RefreshCw, Search, X, Plus, FileSpreadsheet, Eye, FileText } from 'lucide-react';
import { useRouter } from 'next/navigation';
import { PurchaseOrderDetailsModal } from '../../../../components/PurchaseOrderDetailsModal';
import { GrnDetailsModal } from '../../../../components/GrnDetailsModal';
import { grnPdfService } from '../../../../services/grnPdf.service';
import apiClient from '@/services/apiClient';

export default function GrnIndexPage() {
  const router = useRouter();
  const [page, setPage] = useState(1);
  const [filters, setFilters] = useState({
    po_id: '',
    vendor_id: '',
    from_date: '',
    to_date: ''
  });
  
  const [vendorSearchText, setVendorSearchText] = useState('');
  const [vendorSuggestions, setVendorSuggestions] = useState<any[]>([]);
  const [showVendorDropdown, setShowVendorDropdown] = useState(false);

  const [selectedPoId, setSelectedPoId] = useState<string | null>(null);
  const [isPoModalOpen, setIsPoModalOpen] = useState(false);
  const [selectedGrnId, setSelectedGrnId] = useState<string | null>(null);
  const [isGrnModalOpen, setIsGrnModalOpen] = useState(false);
  const [downloadingPdf, setDownloadingPdf] = useState<string | null>(null);
  const [isExporting, setIsExporting] = useState(false);

  useEffect(() => {
    const timeoutId = setTimeout(async () => {
      if (vendorSearchText.trim().length >= 2) {
        try {
          const response = await apiClient.get(`/vendors/search?q=${vendorSearchText}`);
          if (response.data?.success) {
            setVendorSuggestions(response.data.data);
            setShowVendorDropdown(true);
          }
        } catch (err) {
          console.error('Vendor search error', err);
        }
      } else {
        setVendorSuggestions([]);
        setShowVendorDropdown(false);
      }
    }, 300);
    return () => clearTimeout(timeoutId);
  }, [vendorSearchText]);

  const handleVendorSelect = (vendor: any) => {
    setFilters(prev => ({ ...prev, vendor_id: vendor.id.toString() }));
    setVendorSearchText(vendor.name);
    setShowVendorDropdown(false);
    setPage(1);
  };

  const handleDownloadPdf = async (id: string) => {
    try {
      setDownloadingPdf(id);
      await grnPdfService.downloadGrnPdf(id);
    } catch (error) {
      console.error(error);
      alert('Failed to download PDF');
    } finally {
      setDownloadingPdf(null);
    }
  };

  const { data, isLoading, isError, refetch } = useQuery({
    queryKey: ['grn', { page, ...filters }],
    queryFn: () => grnService.getList({ page, limit: 10, ...filters }),
    placeholderData: (prev) => prev,
  });

  const handleFilterChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setFilters(prev => ({ ...prev, [e.target.name]: e.target.value }));
    setPage(1);
  };

  const resetFilters = () => {
    setFilters({ po_id: '', vendor_id: '', from_date: '', to_date: '' });
    setVendorSearchText('');
    setPage(1);
  };

  const handleExport = async () => {
    try {
      setIsExporting(true);
      const blob = await grnService.exportGrns(filters);
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      
      const dateStr = new Date().toLocaleDateString('en-GB').replace(/\//g, '-');
      link.setAttribute('download', `GRN_Summary-${dateStr}.xlsx`);
      
      document.body.appendChild(link);
      link.click();
      link.parentNode?.removeChild(link);
    } catch (error) {
      console.error('Error exporting GRNs:', error);
      alert('Failed to export GRNs. Please try again.');
    } finally {
      setIsExporting(false);
    }
  };

  return (
    <main className="max-w-7xl w-full mx-auto px-6 py-8 space-y-6 select-none font-sans">
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
            Goods Received Note (GRN)
          </h1>
          <p className="text-sm text-slate-500 font-medium mt-1">Manage Goods Received Notes and Stock In</p>
        </div>
        <div className="flex items-center gap-3">
          <button onClick={() => refetch()} className="flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-md text-sm font-medium transition">
            <RefreshCw className="w-4 h-4" /> Refresh
          </button>
          <button onClick={handleExport} disabled={isExporting} className={`flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-md text-sm font-medium transition border border-emerald-200 ${isExporting ? 'opacity-70 cursor-not-allowed' : ''}`}>
            {isExporting ? <Loader className="w-4 h-4 animate-spin" /> : <FileSpreadsheet className="w-4 h-4" />}
            {isExporting ? 'Exporting...' : 'Export Excel'}
          </button>
          <button onClick={() => router.push('/dashboard/purchase/grn/add')} className="flex items-center gap-1.5 px-4 py-1.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-md text-sm font-medium transition shadow-sm">
            <Plus className="w-4 h-4" /> Add GRN
          </button>
        </div>
      </div>

      <div className="bg-white border border-slate-200 p-4 rounded-xl shadow-sm grid grid-cols-2 md:grid-cols-5 gap-4 items-end">
        <div>
          <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">PO Number</label>
          <input type="text" name="po_id" value={filters.po_id} onChange={handleFilterChange} className="w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 outline-none transition" placeholder="PO-..." />
        </div>
        <div className="relative">
          <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Vendor</label>
          <input 
            type="text" 
            value={vendorSearchText} 
            onChange={(e) => {
              setVendorSearchText(e.target.value);
              if (e.target.value === '') {
                setFilters(prev => ({ ...prev, vendor_id: '' }));
              }
            }} 
            onFocus={() => { if (vendorSuggestions.length > 0) setShowVendorDropdown(true); }}
            onBlur={() => setTimeout(() => setShowVendorDropdown(false), 200)}
            className="w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 outline-none transition" 
            placeholder="Search Vendor..." 
          />
          {showVendorDropdown && (
            <ul className="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-md shadow-lg max-h-48 overflow-y-auto">
              {vendorSuggestions.map(v => (
                <li 
                  key={v.id} 
                  className="p-2 text-sm hover:bg-cyan-50 cursor-pointer"
                  onClick={() => handleVendorSelect(v)}
                >
                  {v.name}
                </li>
              ))}
              {vendorSuggestions.length === 0 && vendorSearchText.length >= 2 && (
                <li className="p-2 text-sm text-slate-500">No vendors found</li>
              )}
            </ul>
          )}
        </div>
        <div>
          <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Date From</label>
          <input type="date" name="from_date" value={filters.from_date} onChange={handleFilterChange} className="w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 outline-none" />
        </div>
        <div>
          <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Date To</label>
          <input type="date" name="to_date" value={filters.to_date} onChange={handleFilterChange} className="w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 outline-none" />
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
            <span className="text-sm font-medium text-slate-600">Loading GRNs...</span>
          </div>
        )}
        
        {isError && (
          <div className="absolute inset-0 bg-white z-10 flex flex-col items-center justify-center text-red-500">
            <AlertCircle className="w-10 h-10 mb-2 opacity-50" />
            <span className="text-sm font-semibold">Error loading GRNs</span>
          </div>
        )}

        <div className="overflow-x-auto">
          <table className="w-full text-left text-sm border-collapse">
            <thead>
              <tr className="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase text-xs tracking-wider">
                <th className="p-4 font-semibold">GRN No</th>
                <th className="p-4 font-semibold">PO Number</th>
                <th className="p-4 font-semibold">Inward Date</th>
                <th className="p-4 font-semibold">Bill No</th>
                <th className="p-4 font-semibold">Bill Date</th>
                <th className="p-4 font-semibold">Supplier</th>
                <th className="p-4 font-semibold text-right">Total Qty</th>
                <th className="p-4 font-semibold text-right">Total (₹)</th>
                <th className="p-4 font-semibold text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              {data?.data && data.data.length > 0 ? (
                data.data.map((grn: any) => (
                  <tr key={grn.id} className="border-b border-slate-100 hover:bg-slate-50 transition">
                    <td className="p-4 font-medium text-cyan-700 cursor-pointer hover:underline" onClick={() => { setSelectedGrnId(grn.id); setIsGrnModalOpen(true); }}>{grn.id}</td>
                    <td className="p-4 font-medium text-cyan-700 cursor-pointer hover:underline" onClick={() => { setSelectedPoId(grn.purchaseorder_id); setIsPoModalOpen(true); }}>{grn.purchaseorder_id}</td>
                    <td className="p-4 text-slate-600">{grn.inwarddate?.split('T')[0]}</td>
                    <td className="p-4 text-slate-600">{grn.bill_no}</td>
                    <td className="p-4 text-slate-600">{grn.bill_date?.split('T')[0]}</td>
                    <td className="p-4 text-slate-600">{grn.vendor_name}</td>
                    <td className="p-4 text-right font-medium">{grn.total_qty}</td>
                    <td className="p-4 text-right font-bold">{parseFloat(grn.total_amt).toLocaleString('en-IN')}</td>
                    <td className="p-4 text-center">
                      <div className="flex items-center justify-center space-x-3">
                        <button 
                          onClick={() => handleDownloadPdf(grn.id)} 
                          disabled={downloadingPdf === grn.id}
                          className="text-red-500 hover:text-red-700 transition disabled:opacity-50" 
                          title="Download GRN PDF"
                        >
                          {downloadingPdf === grn.id ? <Loader className="w-5 h-5 animate-spin" /> : <FileText className="w-5 h-5" />}
                        </button>
                        <button onClick={() => router.push(`/dashboard/purchase/grn/view/${grn.id}`)} className="text-green-600 hover:text-green-800 transition" title="View GRN">
                          <Eye className="w-5 h-5" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={9} className="p-8 text-center text-slate-500">
                    No GRNs found matching your filters.
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
                onClick={() => setPage(p => Math.min(data.pagination.totalPages, p + 1))}
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
          poNumber={selectedPoId}
          onClose={() => {
            setIsPoModalOpen(false);
            setSelectedPoId(null);
          }}
        />
      )}

      {isGrnModalOpen && selectedGrnId && (
        <GrnDetailsModal
          id={selectedGrnId}
          isOpen={isGrnModalOpen}
          onClose={() => {
            setIsGrnModalOpen(false);
            setSelectedGrnId(null);
          }}
        />
      )}
    </main>
  );
}
