'use client';

import { LegacyPageHeader } from '@/components/ui/LegacyPageHeader';
import { useLegacyActionAccess } from '@/components/ui/useLegacyActionAccess';
import { useState } from 'react';
import Link from 'next/link';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { settingsService, Product } from '@/services/settings.service';
import { Search, X, ToggleLeft, ToggleRight, Eye, Edit, Trash2, CheckCircle2, XCircle, FileSpreadsheet } from 'lucide-react';

import { ListPagination } from '@/components/ui/ListPagination';
import { usePermission } from '@/contexts/PermissionContext';
import { useListLocation } from '@/components/ui/useListLocation';

export default function ProductsPage() {
  const qc = useQueryClient();
  const {hasPermission}=usePermission();
  const canAction=useLegacyActionAccess();
  const [page,setPage]=useState(1);
  const [actionError,setActionError]=useState('');
  const [exporting,setExporting]=useState(false);
  const [search, setSearch] = useState('');
  const [categoryFilter, setCategoryFilter] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [typeFilter, setTypeFilter] = useState('');
  const [draft,setDraft]=useState({search:'',category_id:'',status:'Y',itemtype:''});
  const locationReady=useListLocation({search,category_id:categoryFilter,status:statusFilter,itemtype:typeFilter},page,['search','category_id','status','itemtype'],(filters,nextPage)=>{
    setSearch(filters.search);setCategoryFilter(filters.category_id);setStatusFilter(filters.status);setTypeFilter(filters.itemtype);setDraft({...filters,status:filters.status || 'Y'});setPage(nextPage);
  });

  const { data: categories } = useQuery({ queryKey: ['product-cats'], queryFn: () => settingsService.getProductCategoryList() });

  const { data: result, isLoading,error:listError } = useQuery({
    enabled:locationReady,
    queryKey: ['products', search, categoryFilter, statusFilter, typeFilter, page],
    queryFn: () => settingsService.getProductsPage({
      page,limit:50,search_mode:search || categoryFilter || statusFilter || typeFilter ? 1 : undefined,
      search: search || undefined,
      category_id: categoryFilter ? parseInt(categoryFilter) : undefined,
      status: statusFilter || undefined,
      itemtype: typeFilter || undefined,
    }),
  });

  const data=result?.data;
  const del=useMutation({mutationFn:(id:number)=>settingsService.deleteProduct(id),onSuccess:()=>qc.invalidateQueries({queryKey:['products']}),onError:(error:any)=>setActionError(error.response?.data?.error?.message || error.response?.data?.message || 'Unable to delete product')});
  const toggle = useMutation({
    mutationFn: ({ id, status }: { id: number; status: string }) => settingsService.toggleProductStatus(id, status),
    onSuccess: () => qc.invalidateQueries({ queryKey: ['products'] }),
  });

  return (
    <div className="space-y-6">
      <LegacyPageHeader title="Add Item Master" />
      {actionError && <p role="alert" className="text-red-600">{actionError}</p>}
      {listError && <p role="alert" className="text-red-600">{(listError as any)?.response?.data?.message || 'Unable to load products'}</p>}
      <form className="legacy-filter-row" onSubmit={e=>{e.preventDefault();setSearch(draft.search.trim());setCategoryFilter(draft.category_id);setStatusFilter(draft.status);setTypeFilter(draft.itemtype);setPage(1);}}>
        <label>Product<input placeholder="Enter Item Name" value={draft.search} onChange={e=>setDraft({...draft,search:e.target.value})}/></label>
        <label>Select Product Type<select value={draft.itemtype} onChange={e=>setDraft({...draft,itemtype:e.target.value})}><option value="">Select Product Type</option><option value="RawMaterial">RawMaterial</option><option value="Semi-Finished Product">Semi-Finished Product</option><option value="FinishedProduct">FinishedProduct</option></select></label>
        <label>Category<select value={draft.category_id} onChange={e=>setDraft({...draft,category_id:e.target.value})}><option value="">Select Category</option>{categories?.map(row=><option key={row.id} value={row.id}>{row.category_name}</option>)}</select></label>
        <label>Status<select value={draft.status} onChange={e=>setDraft({...draft,status:e.target.value})}><option value="Y">Active</option><option value="N">Deactive</option></select></label>
        <div className="legacy-filter-actions"><button type="submit" className="legacy-button">Search</button><button type="button" className="legacy-button" onClick={()=>{setSearch('');setCategoryFilter('');setStatusFilter('');setTypeFilter('');setDraft({search:'',category_id:'',status:'Y',itemtype:''});setPage(1);}}>Reset</button></div>
        <div className="legacy-filter-actions ml-auto">
          {canAction('additem','viewitemexcel') && <button type="button" aria-label="Export Excel" title="Export Excel" disabled={exporting} onClick={async()=>{setExporting(true);setActionError('');try{await settingsService.exportProducts({search,category_id:categoryFilter,status:statusFilter,itemtype:typeFilter,search_mode:search || categoryFilter || statusFilter || typeFilter ? 1 : undefined});}catch{setActionError('Unable to export products');}finally{setExporting(false);}}}><FileSpreadsheet size={28}/></button>}
          {hasPermission('legacy:admin/additem/add') && <Link href="/dashboard/admin/products/add" className="legacy-button">+ Add Item</Link>}
        </div>
      </form>

      {/* Table */}
      <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="min-w-full">
            <thead>
              <tr className="bg-slate-50 border-b border-slate-200">
                {['S.No.','Unique Id', 'Item Name', 'Category', 'Item Type', 'UOM', 'Current Stock', 'Action'].map(h => (
                  <th key={h} className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">{h}</th>
                ))}
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {isLoading ? (
                <tr><td colSpan={8} className="py-10 text-center"><div className="flex items-center justify-center gap-2 text-slate-400"><div className="w-5 h-5 border-2 border-blue-400 border-t-transparent rounded-full animate-spin" />Loading...</div></td></tr>
              ) : !data?.length ? (
                <tr><td colSpan={8} className="py-10 text-center text-slate-400 text-sm">No items found.</td></tr>
              ) : data.map((row, i) => (
                <tr key={row.id} className="hover:bg-slate-50 transition-colors">
                  <td className="px-4 py-2.5 text-slate-400 text-sm">{(page-1)*50+i+1}</td>
                  <td className="px-4 py-2.5 text-sm">{row.id}</td>
                  <td className="px-4 py-2.5 text-sm font-medium text-slate-800 ">{row.item_name}</td>
                  <td className="px-4 py-2.5 text-sm text-slate-500">{row.category_name || '—'}</td>
                  <td className="px-4 py-2.5">{row.itemtype}</td>
                  <td className="px-4 py-2.5 text-sm text-slate-500">{row.uom_name || '—'}</td>
                  <td>{Number(row.current_stock || 0).toFixed(2)}</td>
                  <td className="px-4 py-2.5 flex items-center gap-1">
                    {hasPermission('legacy:admin/additem/edit') && <Link href={`/dashboard/admin/products/edit/${row.id}`} className="p-1.5 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors" title="Edit"><Edit className="w-4 h-4" /></Link>}
                    {hasPermission('legacy:admin/additem/delete') && <button title="Delete" className="p-1.5 text-rose-500" onClick={()=>{if(window.confirm(`Delete ${row.item_name}?`)) del.mutate(row.id);}}><Trash2 className="w-4 h-4" /></button>}
                    {canAction('additem','status') && <button title={row.status==='Y'?'Active':'Inactive'} aria-label={`Change status of ${row.item_name}`} disabled={toggle.isPending} onClick={()=>toggle.mutate({id:row.id,status:row.status==='Y'?'N':'Y'})}>{row.status==='Y'?<CheckCircle2 size={16} className="text-green-500"/>:<XCircle size={16} className="text-red-600"/>}</button>}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
        <ListPagination page={page} limit={50} total={result?.total || 0} busy={isLoading} onPageChange={setPage} />
      </div>


    </div>
  );
}
