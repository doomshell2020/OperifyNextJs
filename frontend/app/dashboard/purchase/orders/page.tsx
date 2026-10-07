'use client';

import { useListLocation } from '@/components/ui/useListLocation';
import { ListPagination, LEGACY_LIST_LIMIT } from '@/components/ui/ListPagination';
import { createPortal } from 'react-dom';
import React, { useState, useEffect } from 'react';
import { useQuery, useQueryClient, useMutation } from '@tanstack/react-query';
import indentService from '@/services/indent.service';
import purchaseOrderService from '../../../../services/purchaseOrder.service';
import apiClient from '../../../../services/apiClient';
import { openPurchaseOrderPdf } from '../../../../services/purchaseOrderPdf.service';
import { PurchaseOrderDetailsModal } from '../../../../components/dashboard/PurchaseOrderDetailsModal';
import { PurchaseOrderFormModal } from '../../../../components/dashboard/PurchaseOrderFormModal';
import { DeliveryNoteModal } from '../../../../components/dashboard/DeliveryNoteModal';
import { VendorDetailsModal } from '../../../../components/dashboard/VendorDetailsModal';
import { PrintPurchaseOrder } from '../../../../components/dashboard/PrintPurchaseOrder';
import { Loader, AlertCircle, RefreshCw, MoreVertical, Search, FileText, X, Edit, Trash2, Box, Printer, Plus } from 'lucide-react';
import toast from 'react-hot-toast';
import { useRouter } from 'next/navigation';
import { DatePicker } from '../../../../components/ui/DatePicker';
import { formatContractDate } from '../../../../utils/dateFormatter';
import { usePermission } from '../../../../contexts/PermissionContext';

export default function PurchaseOrdersPage() {
  const router = useRouter();
  const queryClient = useQueryClient();
  const { hasPermission } = usePermission();
  const [page, setPage] = useState(1);
  const [filters, setFilters] = useState({
    po_number: '',
    vendor_name: '', vendor_id: '',
    datefrom: '',
    dateto: '',
    status: '', search: '', type: 'po', item_id: '', item_name: ''
  });
  
  const [activeFilters, setActiveFilters] = useState(filters);
  const { data: productSuggestions = [] } = useQuery({
    queryKey: ['po-list-product-search', filters.item_name],
    queryFn: () => indentService.searchItems(filters.item_name),
    enabled: filters.item_name.length >= 2,
  });
  const { data: vendorSuggestions = [] } = useQuery({
    queryKey: ['po-list-vendor-search', filters.vendor_name],
    queryFn: async () => (await apiClient.get('/vendors/search', { params: { q: filters.vendor_name } })).data.data as { id: number; name: string }[],
    enabled: filters.vendor_name.length >= 2,
  });
  const productLabel = (item: { item_name: string; size_name?: string }) => item.item_name + (item.size_name ? ` (${item.size_name})` : '');
  const applyFilters = () => {
    const product = productSuggestions.find(item => productLabel(item) === filters.item_name);
    setActiveFilters({ ...filters, vendor_id: vendorSuggestions.find(vendor => vendor.name === filters.vendor_name)?.id.toString() || filters.vendor_id, item_id: product ? String(product.id) : filters.item_id, search: '1' });
    setPage(1);
  };
  // Modals state
  const [viewPoId, setViewPoId] = useState<number | null>(null);
  const [revisePoId, setRevisePoId] = useState<number | null>(null);
  const [deliveryPoId, setDeliveryPoId] = useState<number | null>(null);
  const [viewVendorId, setViewVendorId] = useState<number | null>(null);
  const [printPoId, setPrintPoId] = useState<number | null>(null);
  
  // Action Dropdown state
  const [dropdownPosition, setDropdownPosition] = useState({ top: 0, left: 0 });
  const [openDropdownId, setOpenDropdownId] = useState<number | null>(null);

  const locationReady = useListLocation(activeFilters, page, ['po_number', 'vendor_name', 'vendor_id', 'datefrom', 'dateto', 'status', 'search', 'type', 'item_id', 'item_name', 'sort', 'direction'], (next, nextPage) => { setActiveFilters(next); setFilters(next); setPage(nextPage); });
  const { data, isLoading, isError, refetch } = useQuery({
    queryKey: ['purchase-orders', { page, ...activeFilters }],
    enabled: locationReady,
    queryFn: () => purchaseOrderService.listPurchaseOrders({ page, limit: LEGACY_LIST_LIMIT, ...activeFilters }),
  });

  useEffect(() => { setOpenDropdownId(null); }, [page, activeFilters]);

  const deleteMutation = useMutation({
    mutationFn: (id: number) => purchaseOrderService.deletePurchaseOrder(id),
    onSuccess: () => {
      toast.success('Purchase Order deleted');
      queryClient.invalidateQueries({ queryKey: ['purchase-orders'] });
    },
    onError: () => toast.error('Failed to delete Purchase Order')
  });

  const handleFilterChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setFilters(prev => ({ ...prev, [name]: value, ...(name === 'vendor_name' ? { vendor_id: vendorSuggestions.find(vendor => vendor.name === value)?.id.toString() || '' } : {}), ...(name === 'item_name' ? { item_id: productSuggestions.find(item => productLabel(item) === value)?.id.toString() || '' } : {}) }));
  };

  const resetFilters = () => {
    setFilters({ po_number: '', vendor_name: '', vendor_id: '', datefrom: '', dateto: '', status: '', search: '', type: 'po', item_id: '', item_name: '' });
    setActiveFilters({ po_number: '', vendor_name: '', vendor_id: '', datefrom: '', dateto: '', status: '', search: '', type: 'po', item_id: '', item_name: '' });
    setPage(1);
  };

  const handleDelete = (id: number) => {
    if (confirm('Are you sure you want to delete this Purchase Order? This action cannot be undone.')) {
      deleteMutation.mutate(id);
    }
    setOpenDropdownId(null);
  };

  const canUsePurchaseOrder = hasPermission('purchaseorder:view') || hasPermission('legacy:admin/purchaseorder/index');
  const canAdd = hasPermission('purchaseorder:add') || hasPermission('legacy:admin/purchaseorder/add') || canUsePurchaseOrder;
  const canRevise = hasPermission('purchaseorder:revise') || hasPermission('legacy:admin/purchaseorder/revised') || canUsePurchaseOrder;
  const canDelete = hasPermission('purchaseorder:delete') || hasPermission('legacy:admin/purchaseorder/delete') || canUsePurchaseOrder;
  const canDeliveryNote = hasPermission('purchaseorder:deliverynote') || hasPermission('legacy:admin/purchaseorder/deliverynote') || canUsePurchaseOrder;
  const canPrint = hasPermission('purchaseorder:pdf') || hasPermission('legacy:admin/purchaseorder/view') || hasPermission('legacy:admin/purchaseorder/viewpodetailspdf') || hasPermission('legacy:admin/purchaseorder/printallpo') || canUsePurchaseOrder;

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
          {canAdd && (
            <button onClick={() => router.push('/dashboard/purchase/orders/add')} className="flex items-center gap-1.5 px-4 py-1.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-md text-sm font-medium transition shadow-sm">
              <Plus className="w-4 h-4" /> Generate PO
            </button>
          )}
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
          <input type="text" name="vendor_name" list="po-vendor-options" value={filters.vendor_name} onChange={handleFilterChange} className="w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none transition" placeholder="Vendor name" /><datalist id="po-vendor-options">{vendorSuggestions.map(vendor => <option key={vendor.id} value={vendor.name} />)}</datalist>
        </div>
        <div>
          <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Date From</label>
          <DatePicker dateFormat="dd-MM-yyyy" name="datefrom" value={filters.datefrom} onChange={handleFilterChange} className="w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 outline-none" />
        </div>
        <div>
          <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Date To</label>
          <DatePicker dateFormat="dd-MM-yyyy" name="dateto" value={filters.dateto} onChange={handleFilterChange} className="w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 outline-none" />
        </div>
        <div>
          <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Status</label>
          <select name="status" value={filters.status} onChange={handleFilterChange} className="w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 outline-none">
            <option value="">All</option>
            <option value="O">Open</option>
            <option value="C">Closed</option>
          </select>
        </div>
        <div><label className="block text-xs font-semibold text-slate-500 mb-1">Date Type</label><select name="type" value={filters.type} onChange={handleFilterChange} className="w-full border border-slate-200 rounded-md p-2 text-sm"><option value="po">PO Date</option><option value="deli">Delivery Schedule</option></select></div>
        <div><label className="block text-xs font-semibold text-slate-500 mb-1">Product Name</label><input name="item_name" list="po-product-options" value={filters.item_name} onChange={handleFilterChange} className="w-full border border-slate-200 rounded-md p-2 text-sm" /><datalist id="po-product-options">{productSuggestions.map(item => <option key={item.id} value={productLabel(item)} />)}</datalist></div>
        <div className="flex gap-2">
          <button onClick={applyFilters} className="flex-1 bg-cyan-600 hover:bg-cyan-700 text-white rounded-md p-2 flex items-center justify-center font-medium shadow-sm transition">
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
                      {po.display_po_number || po.po_number}
                    </td>
                    <td className="p-4 text-slate-600">{formatContractDate(po.po_date)}</td>
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
                    <td className="p-4 text-slate-600">{formatContractDate(po.delivery_date) || '-'}</td>
                    <td className="p-4 text-center relative">
                      <button 
                        onClick={event => {
                          const rect = event.currentTarget.getBoundingClientRect();
                          setDropdownPosition({ top: Math.max(8, Math.min(rect.bottom + 4, window.innerHeight - 220)), left: Math.max(8, Math.min(rect.right - 224, window.innerWidth - 232)) });
                          setOpenDropdownId(openDropdownId === po.id ? null : po.id);
                        }}
                        className="px-3 py-1.5 bg-red-700 hover:bg-red-800 text-white rounded-md transition text-xs font-bold inline-flex items-center gap-1"
                      >
                        Action <MoreVertical className="w-4 h-4" />
                      </button>

                      {openDropdownId === po.id && createPortal(
                        <div style={dropdownPosition} className="fixed bg-white border border-slate-200 shadow-xl rounded-md py-2 w-56 z-50 animate-in fade-in zoom-in-95 duration-100">
                          {po.is_latest_revision === 1 && canRevise && (
                            <button onClick={() => { setRevisePoId(po.id); setOpenDropdownId(null); }} className="w-full text-left px-4 py-1.5 text-sm font-bold text-slate-900 hover:bg-slate-50 hover:text-cyan-700">
                              Revise PO
                            </button>
                          )}
                          {po.is_latest_revision === 1 && canDelete && (
                            <button onClick={() => handleDelete(po.id)} className="w-full text-left px-4 py-1.5 text-sm font-bold text-red-600 hover:bg-red-50">
                              Delete
                            </button>
                          )}
                          {po.is_latest_revision === 1 && canDeliveryNote && (
                            <button onClick={() => { setDeliveryPoId(po.id); setOpenDropdownId(null); }} className="w-full text-left px-4 py-1.5 text-sm font-bold text-slate-900 hover:bg-slate-50 hover:text-indigo-700">
                              {Number(po.delivery_notes_count || 0) > 0 ? 'Edit Delivery Note' : 'Add Delivery Note'}
                            </button>
                          )}
                          {canPrint && (
                            <>
                              <button onClick={() => { void openPurchaseOrderPdf(po.id).catch(() => toast.error('Unable to open purchase order PDF. Please retry and allow popups.')); setOpenDropdownId(null); }} className="w-full text-left px-4 py-1.5 text-sm font-bold text-blue-700 hover:bg-blue-50">
                                Print PO {po.display_po_number || po.po_number}
                              </button>
                              {po.amendment_no > 0 && (
                                <button onClick={() => { void openPurchaseOrderPdf(po.id).catch(() => toast.error('Unable to open purchase order PDF. Please retry and allow popups.')); setOpenDropdownId(null); }} className="w-full text-left px-4 py-1.5 text-sm font-bold text-blue-700 hover:bg-blue-50">
                                  Print Revised PO
                                </button>
                              )}
                            </>
                          )}
                        </div>, document.body
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
        {data && <ListPagination page={data?.page || page} limit={LEGACY_LIST_LIMIT} total={data?.total || 0} onPageChange={setPage} busy={isLoading} />}
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
