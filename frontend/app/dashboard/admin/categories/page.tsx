'use client';

import { LegacyPageHeader } from '@/components/ui/LegacyPageHeader';
import { useLegacyActionAccess } from '@/components/ui/useLegacyActionAccess';
import { useListLocation } from '@/components/ui/useListLocation';
import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { settingsService, Category } from '@/services/settings.service';
import { Plus, Pencil, Trash2, CheckCircle2, XCircle, Printer } from 'lucide-react';

import { usePermission } from '@/contexts/PermissionContext';
import { ListPagination } from '@/components/ui/ListPagination';

type ModalMode = { type: 'add' } | { type: 'edit'; item: Category } | null;

export default function CategoriesPage() {
  const qc = useQueryClient();
  const {hasPermission}=usePermission();
  const canAction=useLegacyActionAccess();
  const [page,setPage]=useState(1);
  const [search, setSearch] = useState('');
  const [draftSearch,setDraftSearch]=useState('');
  const ready=useListLocation({category_name:search},page,['category_name'],(filters,nextPage)=>{setSearch(filters.category_name);setDraftSearch(filters.category_name);setPage(nextPage);});
  const [modal, setModal] = useState<ModalMode>(null);
  const [form, setForm] = useState({ category_name: '', description: '' });
  const [error, setError] = useState('');

  const { data, isLoading, error:listError } = useQuery<Category[]>({
    queryKey: ['categories', search], enabled:ready,
    queryFn: () => settingsService.getCategories(search),
  });

  const create = useMutation({
    mutationFn: () => settingsService.createCategory(form),
    onSuccess: () => { qc.invalidateQueries({ queryKey: ['categories'] }); setModal(null); setForm({ category_name: '', description: '' }); },
    onError: (e: any) => setError(e?.response?.data?.message || 'Failed to create'),
  });

  const update = useMutation({
    mutationFn: (id: number) => settingsService.updateCategory(id, form),
    onSuccess: () => { qc.invalidateQueries({ queryKey: ['categories'] }); setModal(null); },
    onError: (e: any) => setError(e?.response?.data?.message || 'Failed to update'),
  });

  const toggle = useMutation({
    mutationFn: ({ id, status }: { id: number; status: string }) => settingsService.toggleCategoryStatus(id, status),
    onSuccess: () => qc.invalidateQueries({ queryKey: ['categories'] }),
    onError: (e:any) => setError(e?.response?.data?.message || 'Failed to change category status'),
  });

  const del = useMutation({
    mutationFn: (id: number) => settingsService.deleteCategory(id),
    onSuccess: () => { qc.invalidateQueries({ queryKey: ['categories'] }); setModal(null); setPage(1); },
    onError: (e:any) => setError(e?.response?.data?.message || 'Failed to delete category'),
  });
  const printToggle = useMutation({
    mutationFn: ({id,is_print}:{id:number;is_print:string}) => settingsService.toggleCategoryPrintStatus(id,is_print),
    onSuccess: () => qc.invalidateQueries({queryKey:['categories']}),
    onError: (e:any) => setError(e?.response?.data?.message || 'Failed to change weekly report setting'),
  });

  const openEdit = (item: Category) => { setForm({ category_name: item.category_name, description: item.description || '' }); setError(''); setModal({ type: 'edit', item }); };
  const openAdd = () => { setForm({ category_name: '', description: '' }); setError(''); setModal({ type: 'add' }); };

  if (modal?.type === 'add' || modal?.type === 'edit') return <div>
    <LegacyPageHeader title="Item Category Manager" />
    <form className="bg-white border p-4 space-y-4" onSubmit={e=>{e.preventDefault();if(modal.type==='add')create.mutate();else update.mutate(modal.item.id);}}>
      {error && <p role="alert" className="text-red-600">{error}</p>}
      <label className="block">Item Category Name *<input required className="block w-full mt-1" placeholder="Enter Item category name" value={form.category_name} onChange={e=>setForm({...form,category_name:e.target.value})}/></label>
      <label className="block">Description *<textarea required rows={3} className="block w-full mt-1" placeholder="Description" value={form.description} onChange={e=>setForm({...form,description:e.target.value})}/></label>
      <div className="flex gap-2"><button type="submit" disabled={create.isPending || update.isPending} className="legacy-button">{create.isPending || update.isPending?'Saving...':'Submit'}</button><button type="button" className="legacy-button" onClick={()=>setModal(null)}>Cancel</button></div>
    </form>
  </div>;

  return <div className="space-y-4">
    <LegacyPageHeader title="Item Category Manager" />
    <form className="legacy-filter-row" onSubmit={e=>{e.preventDefault();setSearch(draftSearch.trim());setPage(1);}}>
      <label>Item Category Name<input placeholder="Enter Item Category Name" value={draftSearch} onChange={e=>setDraftSearch(e.target.value)} /></label>
      <div className="legacy-filter-actions"><button type="submit" className="legacy-button">Search</button><button type="button" className="legacy-button" onClick={()=>{setSearch('');setDraftSearch('');setPage(1);}}>Reset</button></div>
      {hasPermission('legacy:admin/itemcategory/add') && <button type="button" className="legacy-button ml-auto mb-1" onClick={openAdd}><Plus size={12}/>Add Item Category</button>}
    </form>
    {error && <p role="alert" className="text-red-600">{error}</p>}
    {listError && <p role="alert" className="text-red-600">Unable to load categories.</p>}
    <div className="bg-white border overflow-x-auto"><table className="w-full"><thead><tr><th style={{width:'10%'}}>S.No.</th><th style={{width:'40%'}}>Item Category Name</th><th style={{width:'40%'}}>Description</th><th style={{width:'10%'}}>Action</th></tr></thead><tbody>
      {isLoading ? <tr><td colSpan={4} className="text-center">Loading...</td></tr> : !data?.length ? <tr><td colSpan={4} className="text-center">No categories found.</td></tr> : data.slice((page-1)*50,page*50).map((row,i)=><tr key={row.id}>
        <td>{(page-1)*50+i+1}</td><td>{row.category_name}</td><td>{row.description || ''}</td><td><div className="flex items-center gap-3">
          {hasPermission('legacy:admin/itemcategory/edit') && <button aria-label={`Edit ${row.category_name}`} title="Edit" className="text-blue-500" onClick={()=>openEdit(row)}><Pencil size={16}/></button>}
          {canAction('itemcategory','status') && <button aria-label={`Change status of ${row.category_name}`} title={row.status==='Y'?'Active':'Inactive'} disabled={toggle.isPending} onClick={()=>toggle.mutate({id:row.id,status:row.status==='Y'?'N':'Y'})}>{row.status==='Y'?<CheckCircle2 size={16} className="text-green-500"/>:<XCircle size={16} className="text-red-600"/>}</button>}
          {hasPermission('legacy:admin/itemcategory/delete') && <button title="Delete" aria-label={`Delete ${row.category_name}`} className="text-red-600" onClick={()=>{if(window.confirm('Are you sure do you want to delete this Item Category'))del.mutate(row.id);}}><Trash2 size={16}/></button>}
          {canAction('itemcategory','printstatus') && <button title={row.is_print==='Y'?'Print Available':'Print Not Available'} aria-label={`Change weekly stock report inclusion for ${row.category_name}`} disabled={printToggle.isPending} onClick={()=>printToggle.mutate({id:row.id,is_print:row.is_print==='Y'?'N':'Y'})}><Printer size={16} className={row.is_print==='Y'?'text-green-500':'text-red-600'}/></button>}
        </div></td>
      </tr>)}
    </tbody></table><ListPagination page={page} limit={50} total={data?.length || 0} busy={isLoading} onPageChange={setPage}/></div>
  </div>;
}
