'use client';

import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { settingsService, Category } from '@/services/settings.service';
import { Plus, Pencil, Trash2, ToggleLeft, ToggleRight, Search, X, CheckCircle2, XCircle } from 'lucide-react';

function Modal({ title, onClose, children }: { title: string; onClose: () => void; children: React.ReactNode }) {
  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4" onClick={onClose}>
      <div className="absolute inset-0 bg-black/40 backdrop-blur-sm" />
      <div className="relative bg-white rounded-2xl shadow-2xl w-full max-w-md" onClick={e => e.stopPropagation()}>
        <div className="flex items-center justify-between px-6 py-4 border-b border-slate-100">
          <h2 className="text-base font-bold text-slate-800">{title}</h2>
          <button onClick={onClose} className="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500 transition-colors"><X className="w-4 h-4" /></button>
        </div>
        <div className="p-6">{children}</div>
      </div>
    </div>
  );
}

type ModalMode = { type: 'add' } | { type: 'edit'; item: Category } | { type: 'delete'; item: Category } | null;

export default function CategoriesPage() {
  const qc = useQueryClient();
  const [search, setSearch] = useState('');
  const [modal, setModal] = useState<ModalMode>(null);
  const [form, setForm] = useState({ category_name: '', description: '' });
  const [error, setError] = useState('');

  const { data, isLoading } = useQuery<Category[]>({
    queryKey: ['categories', search],
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
  });

  const del = useMutation({
    mutationFn: (id: number) => settingsService.deleteCategory(id),
    onSuccess: () => { qc.invalidateQueries({ queryKey: ['categories'] }); setModal(null); },
  });

  const openEdit = (item: Category) => { setForm({ category_name: item.category_name, description: item.description || '' }); setError(''); setModal({ type: 'edit', item }); };
  const openAdd = () => { setForm({ category_name: '', description: '' }); setError(''); setModal({ type: 'add' }); };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-slate-800">Item Categories</h1>
          <p className="text-sm text-slate-500 mt-0.5">Manage product/item categories</p>
        </div>
        <button onClick={openAdd} className="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
          <Plus className="w-4 h-4" /> Add Category
        </button>
      </div>

      {/* Search */}
      <div className="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-3">
        <Search className="w-4 h-4 text-slate-400" />
        <input
          className="flex-1 text-sm focus:outline-none"
          placeholder="Search categories by name..."
          value={search}
          onChange={e => setSearch(e.target.value)}
        />
        {search && <button onClick={() => setSearch('')}><X className="w-4 h-4 text-slate-400 hover:text-slate-600" /></button>}
      </div>

      {/* Table */}
      <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <table className="min-w-full">
          <thead>
            <tr className="bg-slate-50 border-b border-slate-200">
              {['#', 'Category Name', 'Description', 'Status', 'Actions'].map(h => (
                <th key={h} className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">{h}</th>
              ))}
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100">
            {isLoading ? (
              <tr><td colSpan={5} className="py-10 text-center"><div className="flex items-center justify-center gap-2 text-slate-400"><div className="w-5 h-5 border-2 border-blue-400 border-t-transparent rounded-full animate-spin" />Loading...</div></td></tr>
            ) : !data?.length ? (
              <tr><td colSpan={5} className="py-10 text-center text-slate-400 text-sm">No categories found.</td></tr>
            ) : data.map((row, i) => (
              <tr key={row.id} className="hover:bg-slate-50 transition-colors">
                <td className="px-4 py-3 text-slate-400 text-sm">{i + 1}</td>
                <td className="px-4 py-3 font-semibold text-slate-800 text-sm">{row.category_name}</td>
                <td className="px-4 py-3 text-sm text-slate-500 max-w-[250px] truncate">{row.description || '—'}</td>
                <td className="px-4 py-3">
                  <button onClick={() => toggle.mutate({ id: row.id, status: row.status === 'Y' ? 'N' : 'Y' })} className="flex items-center gap-1.5 text-xs font-medium">
                    {row.status === 'Y'
                      ? <><ToggleRight className="w-5 h-5 text-emerald-500" /><span className="text-emerald-600">Active</span></>
                      : <><ToggleLeft className="w-5 h-5 text-slate-400" /><span className="text-slate-400">Inactive</span></>
                    }
                  </button>
                </td>
                <td className="px-4 py-3">
                  <div className="flex items-center gap-2">
                    <button onClick={() => openEdit(row)} className="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"><Pencil className="w-3.5 h-3.5" /></button>
                    <button onClick={() => setModal({ type: 'delete', item: row })} className="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors"><Trash2 className="w-3.5 h-3.5" /></button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Add/Edit Modal */}
      {(modal?.type === 'add' || modal?.type === 'edit') && (
        <Modal title={modal.type === 'add' ? 'Add Category' : 'Edit Category'} onClose={() => setModal(null)}>
          <div className="space-y-4">
            {error && <div className="text-sm text-rose-600 bg-rose-50 rounded-lg px-3 py-2">{error}</div>}
            <div>
              <label className="block text-xs font-semibold text-slate-600 mb-1.5">Category Name <span className="text-rose-500">*</span></label>
              <input
                className="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                placeholder="Enter category name"
                value={form.category_name}
                onChange={e => setForm(f => ({ ...f, category_name: e.target.value }))}
              />
            </div>
            <div>
              <label className="block text-xs font-semibold text-slate-600 mb-1.5">Description</label>
              <textarea
                rows={3}
                className="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none"
                placeholder="Optional description"
                value={form.description}
                onChange={e => setForm(f => ({ ...f, description: e.target.value }))}
              />
            </div>
            <div className="flex gap-3 pt-2">
              <button
                onClick={() => modal.type === 'add' ? create.mutate() : update.mutate(modal.item.id)}
                disabled={create.isPending || update.isPending}
                className="flex-1 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white text-sm font-semibold py-2 rounded-lg transition-colors"
              >
                {(create.isPending || update.isPending) ? 'Saving...' : modal.type === 'add' ? 'Add Category' : 'Update Category'}
              </button>
              <button onClick={() => setModal(null)} className="px-4 py-2 border border-slate-200 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-50 transition-colors">Cancel</button>
            </div>
          </div>
        </Modal>
      )}

      {/* Delete Confirm Modal */}
      {modal?.type === 'delete' && (
        <Modal title="Delete Category" onClose={() => setModal(null)}>
          <div className="space-y-4">
            <div className="flex gap-3 items-start bg-rose-50 border border-rose-200 rounded-xl p-4">
              <XCircle className="w-5 h-5 text-rose-500 mt-0.5 shrink-0" />
              <p className="text-sm text-rose-700">Are you sure you want to delete <strong>{modal.item.category_name}</strong>? This action cannot be undone.</p>
            </div>
            <div className="flex gap-3">
              <button onClick={() => del.mutate(modal.item.id)} disabled={del.isPending} className="flex-1 bg-rose-600 hover:bg-rose-700 disabled:opacity-60 text-white text-sm font-semibold py-2 rounded-lg transition-colors">
                {del.isPending ? 'Deleting...' : 'Yes, Delete'}
              </button>
              <button onClick={() => setModal(null)} className="px-4 py-2 border border-slate-200 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-50 transition-colors">Cancel</button>
            </div>
          </div>
        </Modal>
      )}
    </div>
  );
}
