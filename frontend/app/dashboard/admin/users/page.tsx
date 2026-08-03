'use client';

import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { settingsService, AppUser } from '@/services/settings.service';
import { Search, X, ToggleLeft, ToggleRight, ShieldCheck, Shield } from 'lucide-react';
import { format } from 'date-fns';

function formatDate(d: string | null) {
  if (!d) return '—';
  try { return format(new Date(d), 'dd/MM/yyyy HH:mm'); } catch { return '—'; }
}

export default function UsersPage() {
  const qc = useQueryClient();
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('');

  const { data, isLoading } = useQuery<AppUser[]>({
    queryKey: ['app-users', search, statusFilter],
    queryFn: () => settingsService.getUsers({ search: search || undefined, is_status: statusFilter || undefined }),
  });

  const toggle = useMutation({
    mutationFn: ({ id, status }: { id: number; status: string }) => settingsService.toggleUserStatus(id, status),
    onSuccess: () => qc.invalidateQueries({ queryKey: ['app-users'] }),
  });

  const roleLabel = (role_id: number) => {
    if (role_id >= 100) return 'Admin';
    if (role_id >= 50) return 'Manager';
    return 'Staff';
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-slate-800">System Users</h1>
          <p className="text-sm text-slate-500 mt-0.5">View and manage ERP user accounts</p>
        </div>
        <span className="bg-slate-100 px-3 py-1.5 rounded-lg text-sm font-medium text-slate-500">{data?.length ?? 0} users</span>
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
                {['#', 'User', 'Mobile', 'Database', 'Role', 'Admin', 'Created', 'Last Login', 'Status'].map(h => (
                  <th key={h} className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">{h}</th>
                ))}
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {isLoading ? (
                <tr><td colSpan={9} className="py-10 text-center"><div className="flex items-center justify-center gap-2 text-slate-400"><div className="w-5 h-5 border-2 border-blue-400 border-t-transparent rounded-full animate-spin" />Loading...</div></td></tr>
              ) : !data?.length ? (
                <tr><td colSpan={9} className="py-10 text-center text-slate-400 text-sm">No users found.</td></tr>
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
                  <td className="px-4 py-2.5 text-sm font-mono text-slate-500">{row.db || '—'}</td>
                  <td className="px-4 py-2.5">
                    <span className="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">{roleLabel(row.role_id)}</span>
                  </td>
                  <td className="px-4 py-2.5">
                    {row.is_admin === 'Y'
                      ? <ShieldCheck className="w-4 h-4 text-violet-500" />
                      : <Shield className="w-4 h-4 text-slate-300" />
                    }
                  </td>
                  <td className="px-4 py-2.5 text-xs text-slate-500">{formatDate(row.created)}</td>
                  <td className="px-4 py-2.5 text-xs text-slate-500">{formatDate(row.last_login)}</td>
                  <td className="px-4 py-2.5">
                    <button onClick={() => toggle.mutate({ id: row.id, status: row.is_status === 'Y' ? 'N' : 'Y' })} className="flex items-center gap-1.5 text-xs font-medium">
                      {row.is_status === 'Y'
                        ? <><ToggleRight className="w-5 h-5 text-emerald-500" /><span className="text-emerald-600">Active</span></>
                        : <><ToggleLeft className="w-5 h-5 text-slate-400" /><span className="text-slate-400">Inactive</span></>
                      }
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
