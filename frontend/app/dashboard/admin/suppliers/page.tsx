'use client';

import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { settingsService, Supplier } from '@/services/settings.service';
import { Plus, Pencil, Search, X, ToggleLeft, ToggleRight } from 'lucide-react';

function Modal({ title, onClose, children }: { title: string; onClose: () => void; children: React.ReactNode }) {
  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4" onClick={onClose}>
      <div className="absolute inset-0 bg-black/40 backdrop-blur-sm" />
      <div className="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[92vh] overflow-y-auto" onClick={e => e.stopPropagation()}>
        <div className="sticky top-0 bg-white flex items-center justify-between px-6 py-4 border-b border-slate-100 rounded-t-2xl">
          <h2 className="text-base font-bold text-slate-800">{title}</h2>
          <button onClick={onClose} className="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500 transition-colors"><X className="w-4 h-4" /></button>
        </div>
        <div className="p-6">{children}</div>
      </div>
    </div>
  );
}

const emptyForm: Partial<Supplier> = { name: '', address: '', contact_no: '', email: '', gst_number: '', pancard_number: '', tin_no: '', tds: '0', contact_person: '', type: 'Vendor', description: '' };

type ModalMode = { type: 'add' } | { type: 'edit'; item: Supplier } | null;

export default function SuppliersPage() {
  const qc = useQueryClient();
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('Y');
  const [typeFilter, setTypeFilter] = useState('');
  const [modal, setModal] = useState<ModalMode>(null);
  const [form, setForm] = useState<Partial<Supplier>>(emptyForm);
  const [error, setError] = useState('');

  const { data, isLoading } = useQuery<Supplier[]>({
    queryKey: ['suppliers', search, statusFilter, typeFilter],
    queryFn: () => settingsService.getSuppliers({ search: search || undefined, status: statusFilter || undefined, type: typeFilter || undefined }),
  });

  const create = useMutation({
    mutationFn: () => settingsService.createSupplier(form),
    onSuccess: () => { qc.invalidateQueries({ queryKey: ['suppliers'] }); setModal(null); },
    onError: (e: any) => setError(e?.response?.data?.message || 'Failed to create'),
  });

  const update = useMutation({
    mutationFn: (id: number) => settingsService.updateSupplier(id, form),
    onSuccess: () => { qc.invalidateQueries({ queryKey: ['suppliers'] }); setModal(null); },
    onError: (e: any) => setError(e?.response?.data?.message || 'Failed to update'),
  });

  const toggle = useMutation({
    mutationFn: ({ id, status }: { id: number; status: string }) => settingsService.toggleSupplierStatus(id, status),
    onSuccess: () => qc.invalidateQueries({ queryKey: ['suppliers'] }),
  });

  const openAdd = () => { setForm(emptyForm); setError(''); setModal({ type: 'add' }); };
  const openEdit = (item: Supplier) => { setForm({ ...item }); setError(''); setModal({ type: 'edit', item }); };
  const setF = (key: keyof Supplier, val: string) => setForm(f => ({ ...f, [key]: val }));

  const SupplierForm = () => (
    <div className="space-y-4">
      {error && <div className="text-sm text-rose-600 bg-rose-50 rounded-lg px-3 py-2">{error}</div>}
      <div className="grid grid-cols-2 gap-4">
        {([
          { key: 'name', label: 'Supplier / Vendor Name', required: true },
          { key: 'contact_person', label: 'Contact Person' },
          { key: 'contact_no', label: 'Phone / Mobile' },
          { key: 'email', label: 'Email' },
          { key: 'gst_number', label: 'GST Number' },
          { key: 'pancard_number', label: 'PAN Card' },
          { key: 'tin_no', label: 'TIN Number' },
          { key: 'tds', label: 'TDS %' },
        ] as { key: keyof Supplier; label: string; required?: boolean }[]).map(({ key, label, required }) => (
          <div key={key}>
            <label className="block text-xs font-semibold text-slate-600 mb-1.5">{label} {required && <span className="text-rose-500">*</span>}</label>
            <input
              className="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
              value={(form[key] as string) || ''}
              onChange={e => setF(key, e.target.value)}
              placeholder={label}
            />
          </div>
        ))}
        <div>
          <label className="block text-xs font-semibold text-slate-600 mb-1.5">Type</label>
          <select className="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" value={form.type || 'Vendor'} onChange={e => setF('type', e.target.value)}>
            <option value="Vendor">Vendor</option>
            <option value="Supplier">Supplier</option>
            <option value="Both">Both</option>
          </select>
        </div>
      </div>
      <div>
        <label className="block text-xs font-semibold text-slate-600 mb-1.5">Address</label>
        <textarea rows={2} className="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none" value={form.address || ''} onChange={e => setF('address', e.target.value)} placeholder="Full address..." />
      </div>
      <div>
        <label className="block text-xs font-semibold text-slate-600 mb-1.5">Description</label>
        <textarea rows={2} className="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none" value={form.description || ''} onChange={e => setF('description', e.target.value)} placeholder="Optional notes..." />
      </div>
      <div className="flex gap-3 pt-2">
        <button
          onClick={() => modal?.type === 'add' ? create.mutate() : update.mutate((modal as { type: 'edit'; item: Supplier }).item.id)}
          disabled={create.isPending || update.isPending}
          className="flex-1 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white text-sm font-semibold py-2 rounded-lg transition-colors"
        >
          {(create.isPending || update.isPending) ? 'Saving...' : modal?.type === 'add' ? 'Add Supplier' : 'Update Supplier'}
        </button>
        <button onClick={() => setModal(null)} className="px-4 py-2 border border-slate-200 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-50">Cancel</button>
      </div>
    </div>
  );

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-slate-800">Suppliers / Vendors</h1>
          <p className="text-sm text-slate-500 mt-0.5">Manage supplier and vendor master records</p>
        </div>
        <button onClick={openAdd} className="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
          <Plus className="w-4 h-4" /> Add Supplier
        </button>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap items-center gap-3">
        <div className="flex items-center gap-2 flex-1 min-w-[200px] bg-slate-50 border border-slate-200 rounded-lg px-3 py-2">
          <Search className="w-4 h-4 text-slate-400 shrink-0" />
          <input className="flex-1 text-sm bg-transparent focus:outline-none" placeholder="Search by name, contact, GST..." value={search} onChange={e => setSearch(e.target.value)} />
          {search && <button onClick={() => setSearch('')}><X className="w-3.5 h-3.5 text-slate-400" /></button>}
        </div>
        <select className="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" value={typeFilter} onChange={e => setTypeFilter(e.target.value)}>
          <option value="">All Types</option>
          <option value="Vendor">Vendor</option>
          <option value="Supplier">Supplier</option>
        </select>
        <select className="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" value={statusFilter} onChange={e => setStatusFilter(e.target.value)}>
          <option value="">All Status</option>
          <option value="Y">Active</option>
          <option value="N">Inactive</option>
        </select>
      </div>

      {/* Table */}
      <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="min-w-full">
            <thead>
              <tr className="bg-slate-50 border-b border-slate-200">
                {['#', 'Name', 'Contact Person', 'Phone', 'Email', 'GST Number', 'Type', 'Status', 'Actions'].map(h => (
                  <th key={h} className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">{h}</th>
                ))}
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {isLoading ? (
                <tr><td colSpan={9} className="py-10 text-center"><div className="flex items-center justify-center gap-2 text-slate-400"><div className="w-5 h-5 border-2 border-blue-400 border-t-transparent rounded-full animate-spin" />Loading...</div></td></tr>
              ) : !data?.length ? (
                <tr><td colSpan={9} className="py-10 text-center text-slate-400 text-sm">No suppliers found.</td></tr>
              ) : data.map((row, i) => (
                <tr key={row.id} className="hover:bg-slate-50 transition-colors">
                  <td className="px-4 py-2.5 text-slate-400 text-sm">{i + 1}</td>
                  <td className="px-4 py-2.5 font-semibold text-slate-800 text-sm max-w-[180px] truncate">{row.name}</td>
                  <td className="px-4 py-2.5 text-sm text-slate-600">{row.contact_person || '—'}</td>
                  <td className="px-4 py-2.5 text-sm text-slate-600">{row.contact_no || '—'}</td>
                  <td className="px-4 py-2.5 text-sm text-slate-500 max-w-[160px] truncate">{row.email || '—'}</td>
                  <td className="px-4 py-2.5 text-sm font-mono text-slate-600">{row.gst_number || '—'}</td>
                  <td className="px-4 py-2.5"><span className="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">{row.type}</span></td>
                  <td className="px-4 py-2.5">
                    <button onClick={() => toggle.mutate({ id: row.id, status: row.status === 'Y' ? 'N' : 'Y' })} className="flex items-center gap-1.5 text-xs font-medium">
                      {row.status === 'Y'
                        ? <><ToggleRight className="w-5 h-5 text-emerald-500" /><span className="text-emerald-600">Active</span></>
                        : <><ToggleLeft className="w-5 h-5 text-slate-400" /><span className="text-slate-400">Inactive</span></>
                      }
                    </button>
                  </td>
                  <td className="px-4 py-2.5">
                    <button onClick={() => openEdit(row)} className="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"><Pencil className="w-3.5 h-3.5" /></button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {modal && (
        <Modal title={modal.type === 'add' ? 'Add Supplier' : 'Edit Supplier'} onClose={() => setModal(null)}>
          <SupplierForm />
        </Modal>
      )}
    </div>
  );
}
