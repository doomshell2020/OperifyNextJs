'use client';

import React, { useState, useEffect } from 'react';
import { useQuery, useQueryClient, useMutation } from '@tanstack/react-query';
import purchaseOrderService from '../../../../services/purchaseOrder.service';
import { API_URL } from '../../../../services/apiClient';
import { PurchaseOrderDetailsModal } from '../../../../components/dashboard/PurchaseOrderDetailsModal';
import { PurchaseOrderFormModal } from '../../../../components/dashboard/PurchaseOrderFormModal';
import { DeliveryNoteModal } from '../../../../components/dashboard/DeliveryNoteModal';
import { VendorDetailsModal } from '../../../../components/dashboard/VendorDetailsModal';
import { PrintPurchaseOrder } from '../../../../components/dashboard/PrintPurchaseOrder';
import { Loader, AlertCircle, RefreshCw, MoreVertical, Search, FileText, X, Edit, Trash2, Box, Printer, Plus } from 'lucide-react';
import toast from 'react-hot-toast';
import { useRouter } from 'next/navigation';
import { DatePicker } from '../../../../components/ui/DatePicker';

export default function PurchaseOrdersPage() {
  const router = useRouter();
  const queryClient = useQueryClient();
  const [page, setPage] = useState(1);
  const [filters, setFilters] = useState({
    po_number: '',
    vendor_name: '',
    datefrom: '',
    dateto: '',
    status: ''
  });
  
  // Modals state
  const [viewPoId, setViewPoId] = useState<number | null>(null);
  const [revisePoId, setRevisePoId] = useState<number | null>(null);
  const [deliveryPoId, setDeliveryPoId] = useState<number | null>(null);
  const [viewVendorId, setViewVendorId] = useState<number | null>(null);
  const [printPoId, setPrintPoId] = useState<number | null>(null);
  
  // Action Dropdown state
  const [openDropdownId, setOpenDropdownId] = useState<number | null>(null);

  const { data, isLoading, isError, refetch } = useQuery({
    queryKey: ['purchase-orders', { page, ...filters }],
    queryFn: () => purchaseOrderService.listPurchaseOrders({ page, limit: 10, ...filters }),
    placeholderData: (prev) => prev,
  });

  const deleteMutation = useMutation({
    mutationFn: (id: number) => purchaseOrderService.deletePurchaseOrder(id),
    onSuccess: () => {
      toast.success('Purchase Order deleted');
      queryClient.invalidateQueries({ queryKey: ['purchase-orders'] });
    },
    onError: () => toast.error('Failed to delete Purchase Order')
  });

  const handleFilterChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    setFilters(prev => ({ ...prev, [e.target.name]: e.target.value }));
    setPage(1);
  };

  const resetFilters = () => {
    setFilters({ po_number: '', vendor_name: '', datefrom: '', dateto: '', status: '' });
    setPage(1);
  };

  const handleDelete = (id: number) => {
    if (confirm('Are you sure you want to delete this Purchase Order? This action cannot be undone.')) {
      deleteMutation.mutate(id);
    }
    setOpenDropdownId(null);
  };

  return (
    <main className="max-w-7xl w-full mx-auto px-6 py-8 space-y-6 select-none font-sans">
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
            <Box className="text-cyan-600" /> Purchase Orders
          </h1>
          <p className="text-sm text-slate-500 font-medium mt-1">Manage purchase orders and delivery notes</p>
        </div>
        <div className="flex items-center gap-3">
          <button onClick={() => refetch()} className="flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-md text-sm font-medium transition">
            <RefreshCw className="w-4 h-4" /> Refresh
          </button>
          <button onClick={() => router.push('/dashboard/purchase/orders/add')} className="flex items-center gap-1.5 px-4 py-1.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-md text-sm font-medium transition shadow-sm">
            <Plus className="w-4 h-4" /> Generate PO
          </button>
        </div>
      </div>

      {/* Filters Form */}
      <div className="bg-white border border-slate-200 p-4 rounded-xl shadow-sm grid grid-cols-2 md:grid-cols-6 gap-4 items-end">
        <div>
          <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">PO Number</label>
          <input type="text" name="po_number" value={filters.po_number} onChange={handleFilterChange} className="w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none transition" placeholder="PO-123..." />
        </div>
        <div>
          <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Vendor</label>
          <input type="text" name="vendor_name" value={filters.vendor_name} onChange={handleFilterChange} className="w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none transition" placeholder="Vendor name" />
        </div>
        <div>
          <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Date From</label>
          <DatePicker name="datefrom" value={filters.datefrom} onChange={handleFilterChange} className="w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 outline-none" />
        </div>
        <div>
          <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Date To</label>
          <DatePicker name="dateto" value={filters.dateto} onChange={handleFilterChange} className="w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 outline-none" />
        </div>
        <div>
          <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Status</label>
          <select name="status" value={filters.status} onChange={handleFilterChange} className="w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 outline-none">
            <option value="">All</option>
            <option value="O">Open</option>
            <option value="C">Closed</option>
          </select>
        </div>
        <div className="flex gap-2">
          <button onClick={() => refetch()} className="flex-1 bg-cyan-600 hover:bg-cyan-700 text-white rounded-md p-2 flex items-center justify-center font-medium shadow-sm transition">
            Search
          </button>
          <button onClick={resetFilters} className="bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-md p-2 flex items-center justify-center font-medium transition" title="Reset Filters">
            <X className="w-5 h-5" />
          </button>
        </div>
      </div>

      {/* Table */}
      <div className="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden relative min-h-[400px]">
        {isLoading && (
          <div className="absolute inset-0 bg-white/50 backdrop-blur-sm z-10 flex flex-col items-center justify-center">
            <Loader className="w-8 h-8 animate-spin text-cyan-600 mb-2" />
            <span className="text-sm font-medium text-slate-600">Loading orders...</span>
          </div>
        )}
        
        {isError && (
          <div className="absolute inset-0 bg-white z-10 flex flex-col items-center justify-center text-red-500">
            <AlertCircle className="w-10 h-10 mb-2 opacity-50" />
            <span className="text-sm font-semibold">Error loading purchase orders</span>
          </div>
        )}

        <div className="overflow-x-auto custom-scrollbar">
          <table className="w-full text-left text-sm border-collapse">
            <thead>
              <tr className="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase text-xs tracking-wider">
                <th className="p-4 font-semibold w-24">PO ID</th>
                <th className="p-4 font-semibold w-28">PO Date</th>
                <th className="p-4 font-semibold min-w-[150px]">Vendor</th>
                <th className="p-4 font-semibold">Contact No.</th>
                <th className="p-4 font-semibold text-right">Order Qty</th>
                <th className="p-4 font-semibold text-right">Rec. Qty</th>
                <th className="p-4 font-semibold text-right">Total (₹)</th>
                <th className="p-4 font-semibold w-28">Delivery Date</th>
                <th className="p-4 font-semibold text-center w-20">Action</th>
              </tr>
            </thead>
            <tbody>
              {data?.items && data.items.length > 0 ? (
                data.items.map((po) => (
                  <tr key={po.id} className="border-b border-slate-100 hover:bg-slate-50 transition">
                    <td className="p-4 font-medium text-cyan-700 cursor-pointer hover:underline" onClick={() => setViewPoId(po.id)}>
                      {po.po_number}
                    </td>
                    <td className="p-4 text-slate-600">{po.po_date?.split('T')[0]}</td>
                    <td className="p-4 font-medium text-blue-600 cursor-pointer hover:underline truncate max-w-[200px]" onClick={() => setViewVendorId(po.vendor_id)}>
                      {po.vendor_name}
                    </td>
                    <td className="p-4 text-slate-600">{po.mobile}</td>
                    <td className="p-4 text-right text-slate-700 font-medium">{po.quantity}</td>
                    <td className="p-4 text-right text-slate-700 font-medium">
                      <span className={po.received_qty > 0 ? "text-green-600 font-semibold bg-green-50 px-2 py-0.5 rounded" : ""}>
                        {po.received_qty}
                      </span>
                    </td>
                    <td className="p-4 text-right text-slate-800 font-bold">
                      {po.amount.toLocaleString('en-IN', { minimumFractionDigits: 2 })}
                    </td>
                    <td className="p-4 text-slate-600">{po.delivery_date?.split('T')[0] || '-'}</td>
                    <td className="p-4 text-center relative">
                      <button 
                        onClick={() => setOpenDropdownId(openDropdownId === po.id ? null : po.id)}
                        className="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-200 rounded-md transition"
                      >
                        <MoreVertical className="w-5 h-5" />
                      </button>

                      {openDropdownId === po.id && (
                        <div className="absolute right-8 top-10 bg-white border border-slate-200 shadow-xl rounded-lg py-1 w-44 z-50 animate-in fade-in zoom-in-95 duration-100">
                          <button onClick={() => { setRevisePoId(po.id); setOpenDropdownId(null); }} className="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-cyan-600 flex items-center gap-2">
                            <Edit className="w-4 h-4" /> Revise PO
                          </button>
                          <button onClick={() => { setDeliveryPoId(po.id); setOpenDropdownId(null); }} className="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600 flex items-center gap-2">
                            <Box className="w-4 h-4" /> Delivery Note
                          </button>
                          <button onClick={() => { window.open(`${API_URL}/purchase-orders/${po.id}/pdf?token=${localStorage.getItem('accessToken')}`, '_blank'); setOpenDropdownId(null); }} className="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-slate-900 flex items-center gap-2">
                            <Printer className="w-4 h-4" /> Print PO
                          </button>
                          <div className="h-px bg-slate-100 my-1"></div>
                          <button onClick={() => handleDelete(po.id)} className="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                            <Trash2 className="w-4 h-4" /> Delete PO
                          </button>
                        </div>
                      )}
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={9} className="p-8 text-center text-slate-500">
                    No purchase orders found matching your filters.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>

        {/* Pagination */}
        {data?.totalPages > 1 && (
          <div className="flex items-center justify-between p-4 border-t border-slate-200 bg-slate-50">
            <span className="text-sm text-slate-500">
              Showing page <span className="font-semibold text-slate-700">{data.page}</span> of <span className="font-semibold text-slate-700">{data.totalPages}</span>
            </span>
            <div className="flex gap-1">
              <button
                disabled={data.page === 1}
                onClick={() => setPage(p => Math.max(1, p - 1))}
                className="px-3 py-1.5 border border-slate-200 rounded-md text-sm font-medium text-slate-600 hover:bg-white disabled:opacity-50 transition"
              >
                Previous
              </button>
              <button
                disabled={data.page === data.totalPages}
                onClick={() => setPage(p => p + 1)}
                className="px-3 py-1.5 border border-slate-200 rounded-md text-sm font-medium text-slate-600 hover:bg-white disabled:opacity-50 transition"
              >
                Next
              </button>
            </div>
          </div>
        )}
      </div>

      {/* Modals */}
      {viewPoId && <PurchaseOrderDetailsModal poId={viewPoId} onClose={() => setViewPoId(null)} />}
      {revisePoId && <PurchaseOrderFormModal poId={revisePoId} onClose={() => setRevisePoId(null)} />}
      {deliveryPoId && <DeliveryNoteModal poId={deliveryPoId} onClose={() => setDeliveryPoId(null)} />}
      {viewVendorId && <VendorDetailsModal vendorId={viewVendorId} onClose={() => setViewVendorId(null)} />}
      {/* printPoId modal removed because it opens in a new tab */}

      {/* Global Click outside to close dropdown */}
      {openDropdownId && (
        <div className="fixed inset-0 z-40" onClick={() => setOpenDropdownId(null)}></div>
      )}
    </main>
  );
}
