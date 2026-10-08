'use client';
import {DatePicker} from '@/components/ui/DatePicker';

import {LegacyPageHeader} from '@/components/ui/LegacyPageHeader';
import {useListLocation} from '@/components/ui/useListLocation';
import {formatContractDate} from '@/utils/dateFormatter';
import React, { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import apiClient from '../../../services/apiClient';
import Link from 'next/link';
import { useRouter } from 'next/navigation';
import { Box, RefreshCw, Plus, X, Trash2, Eye, FileText, Loader, AlertCircle } from 'lucide-react';
import toast from 'react-hot-toast';
import { useJcAccess, errorMessage } from '@/components/jobChallan/useJcAccess';
import { useAuth } from '@/contexts/AuthContext';
import { ListPagination } from '@/components/ui/ListPagination';

export default function JobChallanList() {
  const router = useRouter();
  const { can, loading: permissionsLoading } = useJcAccess();
  const { user } = useAuth();
  const queryClient = useQueryClient();
  const [page, setPage] = useState(1);
  const [filters, setFilters] = useState({
    fromDate: '', toDate: '', vendorId: '', status: '', challanNo: ''
  });
  const [applied, setApplied] = useState({ ...filters });

  const locationReady=useListLocation(applied,page,['fromDate','toDate','vendorId','status','challanNo'],(next,nextPage)=>{setFilters(next);setApplied(next);setPage(nextPage);});
  const { data, isLoading, isError, refetch } = useQuery({
    queryKey: ['jobChallans', user?.db, page, applied],
    enabled: locationReady && can('jobchallan','index'),
    queryFn: async () => {
      const res = await apiClient.get('/job-challan', {
        params: { page, limit: 50, ...applied }
      });
      return res.data.data;
    },
  });

  const { data: vendorData } = useQuery({
    queryKey: ['jcVendors', user?.db],
    enabled: can('jobchallan','index'),
    queryFn: async () => {
      const res = await apiClient.get('/job-challan/vendors');
      return res.data.data;
    }
  });

  const deleteMutation = useMutation({
    mutationFn: async (id: number) => {
      await apiClient.delete('/job-challan/' + id);
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['jobChallans'] });
      toast.success('Job Challan deleted successfully');
    },
    onError: (err: any) => {
      toast.error(errorMessage(err));
    }
  });

  const handleFilterChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    setFilters(prev => ({ ...prev, [e.target.name]: e.target.value }));
  };

  const handleSearch = () => {
    setApplied({ ...filters });
    setPage(1);
  };

  const resetFilters = () => {
    const empty = { fromDate: '', toDate: '', vendorId: '', status: '', challanNo: '' };
    setFilters(empty);
    setApplied(empty);
    setPage(1);
  };

  const handleDelete = (id: number) => {
    if (confirm('Are you sure you want to delete this Job Challan? This action cannot be undone.')) {
      deleteMutation.mutate(id);
    }
  };

  const statusBadge=(status:string)=><span className="legacy-status">{status || 'Pending'}</span>;

  if (permissionsLoading) return <p className="p-6">Loading permissions...</p>;
  if (!can('jobchallan','index')) return <p className="p-6" role="alert">You do not have permission to view JCs.</p>;
  return (
    <main className="max-w-7xl w-full mx-auto px-6 py-8 space-y-6 select-none font-sans">
      <LegacyPageHeader title="Job Challan Report"/>
      {can('jobchallan','add') && <Link className="legacy-button" href="/dashboard/jc-challan/create">+ Add Job Challan</Link>}

      {/* Filters */}
      <div className="legacy-filter-row">
        <div>
          <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">From Date</label>
          <DatePicker  name="fromDate" value={filters.fromDate} onChange={handleFilterChange}
            className="w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none transition" />
        </div>
        <div>
          <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">To Date</label>
          <DatePicker  name="toDate" value={filters.toDate} onChange={handleFilterChange}
            className="w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none transition" />
        </div>
        <div>
          <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Vendor</label>
          <select name="vendorId" value={filters.vendorId} onChange={handleFilterChange}
            className="w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 outline-none">
            <option value="">All Vendors</option>
            {vendorData?.map((v: any) => <option key={v.id} value={v.id}>{v.name}</option>)}
          </select>
        </div>
        <div>
          <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Status</label>
          <select name="status" value={filters.status} onChange={handleFilterChange}
            className="w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 outline-none">
            <option value="">All Statuses</option>
            <option value="Created">Created</option><option value="Pending">Pending</option><option value="Partially Returned">Partially Returned</option><option value="Completed">Completed</option>
            <option value="Received">Received</option>
            <option value="Cancelled">Cancelled</option>
          </select>
        </div>
        <div>
          <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Challan No</label>
          <input type="text" name="challanNo" value={filters.challanNo} onChange={handleFilterChange}
            className="w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none transition" placeholder="Enter Challan No" />
        </div>
        <div className="flex gap-2">
          <button onClick={handleSearch} className="flex-1 bg-cyan-600 hover:bg-cyan-700 text-white rounded-md p-2 flex items-center justify-center font-medium shadow-sm transition text-sm">
            Search
          </button>
          <button onClick={resetFilters} className="bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-md p-2 flex items-center justify-center transition" title="Reset Filters">
            Reset
          </button>
        </div>
      </div>

      {/* Table */}
      <div className="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden relative min-h-[300px]">
        {isLoading && (
          <div className="absolute inset-0 bg-white/60 backdrop-blur-sm z-10 flex flex-col items-center justify-center">
            <Loader className="w-8 h-8 animate-spin text-cyan-600 mb-2" />
            <span className="text-sm font-medium text-slate-600">Loading job challans...</span>
          </div>
        )}
        {isError && (
          <div className="absolute inset-0 bg-white z-10 flex flex-col items-center justify-center text-red-500">
            <AlertCircle className="w-10 h-10 mb-2 opacity-50" />
            <span className="text-sm font-semibold">Error loading job challans</span>
          </div>
        )}

        <div className="overflow-x-auto">
          <table className="w-full text-left text-sm border-collapse">
            <thead>
              <tr className="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase text-xs tracking-wider">
                <th className="p-4 font-semibold w-10">#</th>
                <th className="p-4 font-semibold w-32">Challan No</th>
                <th className="p-4 font-semibold w-32">Date</th>
                <th className="p-4 font-semibold min-w-[180px]">Vendor</th>
                <th className="p-4 font-semibold w-32">Vehicle No</th>
                <th className="p-4 font-semibold w-28">Status</th>
                <th className="p-4 font-semibold text-right w-36">Amount (₹)</th>
                <th className="p-4 font-semibold text-center w-36">Actions</th>
              </tr>
            </thead>
            <tbody>
              {data?.items && data.items.length > 0 ? (
                data.items.map((item: any, idx: number) => (
                  <tr key={item.id} className="border-b border-slate-100 hover:bg-slate-50 transition">
                    <td className="p-4 text-slate-500">{((page - 1) * 50) + idx + 1}</td>
                    <td className="p-4 font-medium text-cyan-700">{item.challan_no}</td>
                    <td className="p-4 text-slate-600">{formatContractDate(item.jc_date)}</td>
                    <td className="p-4 font-medium text-blue-600 truncate max-w-[200px]">{item.vendor?.name || 'N/A'}</td>
                    <td className="p-4 text-slate-500">{item.vehicle_no || '—'}</td>
                    <td className="p-4">{statusBadge(item.status)}</td>
                    <td className="p-4 text-center">
                      <div className="flex justify-center gap-1">
                        {can('jobchallan','view') && <button onClick={() => router.push(`/dashboard/jc-challan/${item.id}`)}
                          className="p-1.5 text-slate-400 hover:text-cyan-600 hover:bg-cyan-50 rounded transition" title="View">
                          <Eye className="w-4 h-4" />
                        </button>}
                        {can('jobchallan','viewpdf') && <button onClick={() => router.push(`/dashboard/jc-challan/${item.id}/pdf`)}
                          className="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded transition" title="PDF">
                          <FileText className="w-4 h-4" />
                        </button>}
                        {can('jobchallan','delete') && <button onClick={() => handleDelete(item.id)} disabled={deleteMutation.isPending}
                          className="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded transition disabled:opacity-40" title="Delete">
                          <Trash2 className="w-4 h-4" />
                        </button>}
                      </div>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={7} className="p-10 text-center text-slate-400 italic">
                    No job challans found matching your filters.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>

        <ListPagination page={page} limit={50} total={data?.total || 0} onPageChange={setPage} busy={isLoading} />
      </div>
    </main>
  );
}
