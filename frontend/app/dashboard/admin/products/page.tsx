'use client';

import { useState } from 'react';
import Link from 'next/link';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { settingsService, Product } from '@/services/settings.service';
import { Search, X, ToggleLeft, ToggleRight, Eye, Edit, Trash2 } from 'lucide-react';

import { ListPagination } from '@/components/ui/ListPagination';
import { usePermission } from '@/contexts/PermissionContext';
import { useListLocation } from '@/components/ui/useListLocation';

function formatAmt(n: number | null) {
  if (!n) return '—';
  return '₹' + Number(n).toLocaleString('en-IN', { maximumFractionDigits: 2 });
}

function TypeBadge({ type }: { type: string }) {
  const colors: Record<string, string> = {
    RawMaterial: 'bg-blue-100 text-blue-700',
    FinishedProduct: 'bg-violet-100 text-violet-700',
    SemiFinished: 'bg-amber-100 text-amber-700',
  };
  return <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ${colors[type] || 'bg-slate-100 text-slate-600'}`}>{type || '—'}</span>;
}

function DetailModal({ product, onClose }: { product: Product; onClose: () => void }) {
  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4" onClick={onClose}>
      <div className="absolute inset-0 bg-black/40 backdrop-blur-sm" />
      <div className="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto" onClick={e => e.stopPropagation()}>
        <div className="sticky top-0 bg-white border-b border-slate-100 px-6 py-4 flex items-center justify-between rounded-t-2xl">
          <h2 className="text-base font-bold text-slate-800">{product.item_name}</h2>
          <button onClick={onClose} className="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500 transition-colors"><X className="w-4 h-4" /></button>
        </div>
        <div className="p-6 space-y-4">
          <div className="grid grid-cols-2 gap-3">
            {[
              { label: 'Item Type', value: <TypeBadge type={product.itemtype} /> },
              { label: 'Category', value: product.category_name },
              { label: 'UOM', value: product.uom_name },
              { label: 'Tax %', value: product.tax_percentage ? `${product.tax_percentage}%` : '—' },
              { label: 'Cost Price', value: formatAmt(product.cost_price) },
              { label: 'Sale Price', value: formatAmt(product.sale_price) },
              { label: 'Min Order Qty', value: product.min_order_qty ?? '—' },
              { label: 'Status', value: product.status === 'Y' ? <span className="text-emerald-600 font-semibold">Active</span> : <span className="text-slate-400 font-semibold">Inactive</span> },
            ].map(({ label, value }) => (
              <div key={label} className="bg-slate-50 rounded-xl p-3">
                <p className="text-xs text-slate-500 font-medium mb-0.5">{label}</p>
                <p className="text-sm font-semibold text-slate-800">{value}</p>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}

export default function ProductsPage() {
  const qc = useQueryClient();
  const {hasPermission}=usePermission();
  const [page,setPage]=useState(1);
  const [actionError,setActionError]=useState('');
  const [exporting,setExporting]=useState(false);
  const [search, setSearch] = useState('');
  const [categoryFilter, setCategoryFilter] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [typeFilter, setTypeFilter] = useState('');
  const [locationFilter,setLocationFilter]=useState('');
  const [companyFilter,setCompanyFilter]=useState('');
  const [selected, setSelected] = useState<Product | null>(null);
  const locationReady=useListLocation({search,category_id:categoryFilter,status:statusFilter,itemtype:typeFilter,location_name:locationFilter,cname:companyFilter},page,['search','category_id','status','itemtype','location_name','cname'],(filters,nextPage)=>{
    setSearch(filters.search);setCategoryFilter(filters.category_id);setStatusFilter(filters.status);setTypeFilter(filters.itemtype);setLocationFilter(filters.location_name);setCompanyFilter(filters.cname);setPage(nextPage);
  });

  const { data: categories } = useQuery({ queryKey: ['product-cats'], queryFn: () => settingsService.getProductCategoryList() });
  const {data:masters}=useQuery({queryKey:['product-form-masters'],queryFn:()=>settingsService.getProductFormData()});

  const { data: result, isLoading,error:listError } = useQuery({
    enabled:locationReady,
    queryKey: ['products', search, categoryFilter, statusFilter, typeFilter,locationFilter,companyFilter, page],
    queryFn: () => settingsService.getProductsPage({
      page,limit:50,search_mode:search || categoryFilter || statusFilter || typeFilter || locationFilter || companyFilter ? 1 : undefined,
      location_name:locationFilter || undefined,cname:companyFilter || undefined,
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
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-slate-800">Products / Items</h1>
          <p className="text-sm text-slate-500 mt-0.5">View and manage all items in the system</p>
        </div>
        <div className="flex items-center gap-3">
          <button disabled={exporting} onClick={async()=>{
            setExporting(true);setActionError('');
            try{await settingsService.exportProducts({search,category_id:categoryFilter,status:statusFilter,itemtype:typeFilter,location_name:locationFilter,cname:companyFilter,search_mode:search || categoryFilter || statusFilter || typeFilter || locationFilter || companyFilter ? 1 : undefined});}
            catch(error:any){setActionError(error.response?.data?.message || 'Unable to export products');}
            finally{setExporting(false);}
          }} className="px-3 py-1.5 border rounded-lg text-sm">{exporting?'Exporting…':'Export Excel'}</button>
          <span className="bg-slate-100 px-3 py-1.5 rounded-lg text-sm font-medium text-slate-500">{result?.total ?? 0} items</span>
          {hasPermission('legacy:admin/additem/add') && <Link href="/dashboard/admin/products/add" className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            + Add Product
          </Link>}
        </div>
      </div>

      {actionError && <p role="alert" className="text-red-600">{actionError}</p>}
      {listError && <p role="alert" className="text-red-600">{(listError as any)?.response?.data?.message || 'Unable to load products'}</p>}
      {/* Filters */}
      <div className="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
        <div className="flex flex-wrap items-center gap-3">
          <div className="flex items-center gap-2 flex-1 min-w-[200px] bg-slate-50 border border-slate-200 rounded-lg px-3 py-2">
            <Search className="w-4 h-4 text-slate-400 shrink-0" />
            <input className="flex-1 text-sm bg-transparent focus:outline-none" placeholder="Search items..." value={search} onChange={e => {setSearch(e.target.value);setPage(1);}} />
            {search && <button onClick={() => {setSearch('');setPage(1);}}><X className="w-3.5 h-3.5 text-slate-400" /></button>}
          </div>
          <select className="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" value={categoryFilter} onChange={e => {setCategoryFilter(e.target.value);setPage(1);}}>
            <option value="">All Categories</option>
            {categories?.map(c => <option key={c.id} value={c.id}>{c.category_name}</option>)}
          </select>
          <select className="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" value={typeFilter} onChange={e => {setTypeFilter(e.target.value);setPage(1);}}>
            <option value="">All Types</option>
            <option value="RawMaterial">Raw Material</option>
            <option value="FinishedProduct">Finished Product</option>
            <option value="Semi-Finished Product">Semi Finished</option>
          </select>
          <select className="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" value={statusFilter} onChange={e => {setStatusFilter(e.target.value);setPage(1);}}>
            <option value="">All Status</option>
            <option value="Y">Active</option>
            <option value="N">Inactive</option>
          </select>
          <select aria-label="Location filter" className="border rounded-lg px-3 py-2 text-sm" value={locationFilter} onChange={e=>{setLocationFilter(e.target.value);setPage(1);}}>
            <option value="">All Locations</option>{masters?.locations.map(row=><option key={row.id} value={row.id}>{row.location_name}</option>)}
          </select>
          <select aria-label="Company filter" className="border rounded-lg px-3 py-2 text-sm" value={companyFilter} onChange={e=>{setCompanyFilter(e.target.value);setPage(1);}}>
            <option value="">All Companies</option>{masters?.companies.map(row=><option key={row.id} value={row.id}>{row.cname}</option>)}
          </select>
        </div>
      </div>

      {/* Table */}
      <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="min-w-full">
            <thead>
              <tr className="bg-slate-50 border-b border-slate-200">
                {['#','Unique Id', 'Item Name', 'Category', 'Type', 'UOM', 'Tax', 'Cost Price', 'Sale Price','Current Stock', 'Status', 'Action'].map(h => (
                  <th key={h} className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">{h}</th>
                ))}
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {isLoading ? (
                <tr><td colSpan={12} className="py-10 text-center"><div className="flex items-center justify-center gap-2 text-slate-400"><div className="w-5 h-5 border-2 border-blue-400 border-t-transparent rounded-full animate-spin" />Loading...</div></td></tr>
              ) : !data?.length ? (
                <tr><td colSpan={12} className="py-10 text-center text-slate-400 text-sm">No items found.</td></tr>
              ) : data.map((row, i) => (
                <tr key={row.id} className="hover:bg-slate-50 transition-colors">
                  <td className="px-4 py-2.5 text-slate-400 text-sm">{(page-1)*50+i+1}</td>
                  <td className="px-4 py-2.5 text-sm">{row.id}</td>
                  <td className="px-4 py-2.5 text-sm font-medium text-slate-800 max-w-[200px] truncate">{row.item_name}</td>
                  <td className="px-4 py-2.5 text-sm text-slate-500">{row.category_name || '—'}</td>
                  <td className="px-4 py-2.5"><TypeBadge type={row.itemtype} /></td>
                  <td className="px-4 py-2.5 text-sm text-slate-500">{row.uom_name || '—'}</td>
                  <td className="px-4 py-2.5 text-sm text-slate-600">{row.tax_percentage ? `${row.tax_percentage}%` : '—'}</td>
                  <td className="px-4 py-2.5 text-sm text-slate-700">{formatAmt(row.cost_price)}</td>
                  <td className="px-4 py-2.5 text-sm font-semibold text-slate-800">{formatAmt(row.sale_price)}</td>
                  <td className="px-4 py-2.5 text-sm">{Number(row.current_stock || 0).toFixed(2)}</td>
                  <td className="px-4 py-2.5">
                    <button onClick={() => toggle.mutate({ id: row.id, status: row.status === 'Y' ? 'N' : 'Y' })} className="flex items-center gap-1.5 text-xs font-medium">
                      {row.status === 'Y'
                        ? <><ToggleRight className="w-5 h-5 text-emerald-500" /><span className="text-emerald-600">Active</span></>
                        : <><ToggleLeft className="w-5 h-5 text-slate-400" /><span className="text-slate-400">Inactive</span></>
                      }
                    </button>
                  </td>
                  <td className="px-4 py-2.5 flex items-center gap-1">
                    <button onClick={() => setSelected(row)} className="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View"><Eye className="w-4 h-4" /></button>
                    {hasPermission('legacy:admin/additem/edit') && <Link href={`/dashboard/admin/products/edit/${row.id}`} className="p-1.5 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors" title="Edit"><Edit className="w-4 h-4" /></Link>}
                    {hasPermission('legacy:admin/additem/delete') && <button title="Delete" className="p-1.5 text-rose-500" onClick={()=>{if(window.confirm(`Delete ${row.item_name}?`)) del.mutate(row.id);}}><Trash2 className="w-4 h-4" /></button>}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
        <ListPagination page={page} limit={50} total={result?.total || 0} busy={isLoading} onPageChange={setPage} />
      </div>

      {selected && <DetailModal product={selected} onClose={() => setSelected(null)} />}
    </div>
  );
}
