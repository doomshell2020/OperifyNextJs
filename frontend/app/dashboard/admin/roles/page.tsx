'use client';

import { useLegacyActionAccess } from '@/components/ui/useLegacyActionAccess';
import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { settingsService, AppUser } from '@/services/settings.service';
import { Search, X, ToggleLeft, ToggleRight, ShieldCheck, Shield, Plus, Edit2, Trash2 } from 'lucide-react';
import { formatDate } from '../../../../utils/dateFormatter';
import toast from 'react-hot-toast';

export default function UsersPage() {
  const canAction = useLegacyActionAccess();
  const qc = useQueryClient();
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingUser, setEditingUser] = useState<AppUser | null>(null);
  
  const [formData, setFormData] = useState({
    user_name: '',
    email: '',
    mobile: '',
    password: '',
    confirm_pass: '',
    role_id: '',
  });

  const { data, isLoading } = useQuery<AppUser[]>({
    queryKey: ['app-users', search, statusFilter],
    queryFn: () => settingsService.getUsers({ search: search || undefined, is_status: statusFilter || undefined }),
  });

  const { data: roles } = useQuery({
    queryKey: ['roles'],
    queryFn: () => settingsService.getRoles(),
  });

  const toggle = useMutation({
    mutationFn: ({ id, status }: { id: number; status: string }) => settingsService.toggleUserStatus(id, status),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['app-users'] });
      toast.success('Status updated');
    },
  });

  const deleteUser = useMutation({
    mutationFn: (id: number) => settingsService.deleteUser(id),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['app-users'] });
      toast.success('User deleted successfully');
    },
  });

  const saveUser = useMutation({
    mutationFn: async (data: Partial<AppUser> & { password?: string, confirm_pass?: string }) => {
      if (editingUser) {
        return settingsService.updateUser(editingUser.id, data);
      } else {
        return settingsService.createUser(data);
      }
    },
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['app-users'] });
      toast.success(editingUser ? 'User updated successfully' : 'User created successfully');
      closeModal();
    },
    onError: (error: unknown) => {
      const err = error as { response?: { data?: { message?: string } } };
      toast.error(err?.response?.data?.message || 'An error occurred');
    }
  });

  const handleEdit = (user: AppUser) => {
    setEditingUser(user);
    setFormData({
      user_name: user.user_name || '',
      email: user.email || '',
      mobile: user.mobile || '',
      password: '',
      confirm_pass: '',
      role_id: user.role_id?.toString() || '',
    });
    setIsModalOpen(true);
  };

  const closeModal = () => {
    setIsModalOpen(false);
    setEditingUser(null);
    setFormData({ user_name: '', email: '', mobile: '', password: '', confirm_pass: '', role_id: '' });
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (formData.password !== formData.confirm_pass) {
      toast.error('Passwords do not match');
      return;
    }
    if (!editingUser && !formData.password) {
      toast.error('Password is required for new users');
      return;
    }
    saveUser.mutate({ ...formData, role_id: Number(formData.role_id) });
  };

  const getRoleName = (roleId: number) => {
    const role = roles?.find(r => r.id === roleId);
    return role ? role.name : 'Unknown';
  };

  return (
    <div className="space-y-6 relative">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-slate-800">User Management</h1>
          <p className="text-sm text-slate-500 mt-0.5">Manage roles and users in the system</p>
        </div>
        {canAction('roles','add') && <button
          onClick={() => setIsModalOpen(true)}
          className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors"
        >
          <Plus className="w-4 h-4" /> Add User
        </button>}
      </div>

      {/* Filters */}
      <div className="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap items-center gap-3">
        <div className="flex items-center gap-2 flex-1 min-w-[200px] bg-slate-50 border border-slate-200 rounded-lg px-3 py-2">
          <Search className="w-4 h-4 text-slate-400 shrink-0" />
          <input className="flex-1 text-sm bg-transparent focus:outline-none" placeholder="Search by name, email, mobile..." value={search} onChange={e => setSearch(e.target.value)} />
          {search && <button onClick={() => setSearch('')}><X className="w-3.5 h-3.5 text-slate-400" /></button>}
        </div>
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
                {['#', 'User', 'Mobile', 'Role', 'Admin', 'Created', 'Status', 'Action'].map(h => (
                  <th key={h} className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">{h}</th>
                ))}
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {isLoading ? (
                <tr><td colSpan={8} className="py-10 text-center"><div className="flex items-center justify-center gap-2 text-slate-400"><div className="w-5 h-5 border-2 border-blue-400 border-t-transparent rounded-full animate-spin" />Loading...</div></td></tr>
              ) : !data?.length ? (
                <tr><td colSpan={8} className="py-10 text-center text-slate-400 text-sm">No users found.</td></tr>
              ) : data.map((row, i) => (
                <tr key={row.id} className="hover:bg-slate-50 transition-colors">
                  <td className="px-4 py-2.5 text-slate-400 text-sm">{i + 1}</td>
                  <td className="px-4 py-2.5">
                    <div className="flex items-center gap-2.5">
                      <div className="h-8 w-8 rounded-lg bg-gradient-to-tr from-cyan-500 to-purple-600 text-white font-bold text-xs flex items-center justify-center shrink-0">
                        {row.user_name?.charAt(0)?.toUpperCase() || 'U'}
                      </div>
                      <div>
                        <p className="text-sm font-semibold text-slate-800">{row.user_name}</p>
                        <p className="text-xs text-slate-400">{row.email}</p>
                      </div>
                    </div>
                  </td>
                  <td className="px-4 py-2.5 text-sm text-slate-600">{row.mobile || '—'}</td>
                  <td className="px-4 py-2.5">
                    <span className="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">{getRoleName(row.role_id)}</span>
                  </td>
                  <td className="px-4 py-2.5">
                    {row.is_admin === 'Y'
                      ? <ShieldCheck className="w-4 h-4 text-violet-500" />
                      : <Shield className="w-4 h-4 text-slate-300" />
                    }
                  </td>
                  <td className="px-4 py-2.5 text-xs text-slate-500">{formatDate(row.created)}</td>
                  <td className="px-4 py-2.5">
                    {canAction('roles','status') && <button onClick={() => toggle.mutate({ id: row.id, status: row.is_status === 'Y' ? 'N' : 'Y' })} className="flex items-center gap-1.5 text-xs font-medium">
                      {row.is_status === 'Y'
                        ? <><ToggleRight className="w-5 h-5 text-emerald-500" /><span className="text-emerald-600">Active</span></>
                        : <><ToggleLeft className="w-5 h-5 text-slate-400" /><span className="text-slate-400">Inactive</span></>
                      }
                    </button>}
                  </td>
                  <td className="px-4 py-2.5">
                    <div className="flex items-center gap-2">
                      {canAction('roles','add') && <button onClick={() => handleEdit(row)} className="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors" title="Edit">
                        <Edit2 className="w-4 h-4" />
                      </button>}
                      {canAction('roles','delete') && <button onClick={() => { if(confirm('Are you sure you want to delete this user?')) deleteUser.mutate(row.id); }} className="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors" title="Delete">
                        <Trash2 className="w-4 h-4" />
                      </button>}
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* Modal Slide-over Panel */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
          <div className="absolute inset-0 bg-slate-900/30 backdrop-blur-sm transition-opacity" onClick={closeModal} />
          <div className="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
            <div className="pointer-events-auto w-screen max-w-md transform transition-transform duration-300 ease-in-out">
              <div className="flex h-full flex-col bg-white shadow-xl">
                <div className="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                  <h2 className="text-lg font-semibold text-slate-800" id="slide-over-title">
                    {editingUser ? 'Edit User' : 'Add User'}
                  </h2>
                  <button onClick={closeModal} className="rounded-md text-slate-400 hover:text-slate-500 focus:outline-none">
                    <X className="h-5 w-5" />
                  </button>
                </div>
                <div className="flex-1 overflow-y-auto px-6 py-6">
                  <form id="user-form" onSubmit={handleSubmit} className="space-y-5">
                    <div>
                      <label className="block text-sm font-medium text-slate-700 mb-1">Select Role <span className="text-red-500">*</span></label>
                      <select 
                        required
                        value={formData.role_id}
                        onChange={e => setFormData({...formData, role_id: e.target.value})}
                        className="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                      >
                        <option value="">--Select Role--</option>
                        {roles?.filter((r: { id: number; name: string }) => ![1, 6, 101, 105].includes(r.id)).map((r: { id: number; name: string }) => (
                          <option key={r.id} value={r.id}>{r.name}</option>
                        ))}
                      </select>
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-slate-700 mb-1">Username <span className="text-red-500">*</span></label>
                      <input 
                        type="text" required
                        value={formData.user_name}
                        onChange={e => setFormData({...formData, user_name: e.target.value})}
                        className="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none" 
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-slate-700 mb-1">Email <span className="text-red-500">*</span></label>
                      <input 
                        type="email" required
                        value={formData.email}
                        onChange={e => setFormData({...formData, email: e.target.value})}
                        className="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none" 
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-slate-700 mb-1">Mobile <span className="text-red-500">*</span></label>
                      <input 
                        type="text" required maxLength={10} pattern="\d{10}"
                        value={formData.mobile}
                        onChange={e => setFormData({...formData, mobile: e.target.value.replace(/\D/g, '')})}
                        className="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none" 
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-slate-700 mb-1">Password {(!editingUser) && <span className="text-red-500">*</span>}</label>
                      <input 
                        type="password" 
                        required={!editingUser}
                        value={formData.password}
                        onChange={e => setFormData({...formData, password: e.target.value})}
                        className="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none" 
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-slate-700 mb-1">Confirm Password {(!editingUser) && <span className="text-red-500">*</span>}</label>
                      <input 
                        type="password" 
                        required={!editingUser}
                        value={formData.confirm_pass}
                        onChange={e => setFormData({...formData, confirm_pass: e.target.value})}
                        className="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none" 
                      />
                    </div>
                  </form>
                </div>
                <div className="flex-shrink-0 px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
                  <button onClick={closeModal} type="button" className="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                    Cancel
                  </button>
                  <button 
                    type="submit" form="user-form" 
                    disabled={saveUser.isPending}
                    className="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors"
                  >
                    {saveUser.isPending ? 'Saving...' : editingUser ? 'Update User' : 'Add User'}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

