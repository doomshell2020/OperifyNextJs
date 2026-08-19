'use client';

import React, { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import contractService, { ContractFilters } from '../../../services/contract.service';
import { ContractDetailsModal } from '../../../components/dashboard/ContractDetailsModal';
import { 
  FileText, 
  Search, 
  RefreshCw, 
  Eye, 
  Loader, 
  AlertCircle, 
  Calendar, 
  DollarSign, 
  Briefcase,
  X,
  Printer,
  Download
} from 'lucide-react';
import { StatusBadge } from '../../../components/dashboard/StatusBadge';
import toast from 'react-hot-toast';
import { DatePicker } from '../../../components/ui/DatePicker';
import { formatDate } from '../../../utils/dateFormatter';

export default function ContractsPage() {
  // Filters state
  const [filters, setFilters] = useState<ContractFilters>({
    contract_name: '',
    vendor_name: '',
    cost: '',
    datefrom: '',
    dateto: ''
  });

  const [activeFilters, setActiveFilters] = useState<ContractFilters>({});
  const [selectedContractId, setSelectedContractId] = useState<number | null>(null);

  // Fetch contracts
  const { data: contracts, isLoading, isError, refetch } = useQuery({
    queryKey: ['contracts', activeFilters],
    queryFn: () => contractService.getContracts(activeFilters),
    staleTime: 5 * 60 * 1000
  });

  // Fetch selected contract details
  const { data: details, isLoading: detailsLoading, isError: detailsError } = useQuery({
    queryKey: ['contract-details', selectedContractId],
    queryFn: () => contractService.getDetails(selectedContractId!),
    enabled: selectedContractId !== null,
    staleTime: 5 * 60 * 1000
  });

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    setActiveFilters({ ...filters });
  };

  const handleReset = () => {
    const empty = { contract_name: '', vendor_name: '', cost: '', datefrom: '', dateto: '' };
    setFilters(empty);
    setActiveFilters(empty);
  };

  
  return (
    <main className="max-w-7xl w-full mx-auto px-6 py-8 space-y-6 select-none font-sans">
      
      {/* Top Tenant Context Filters */}
{/* Header and Title */}
      <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 className="text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
            <FileText className="w-5 h-5 text-cyan-600" />
            Contracts Management
          </h1>
          <p className="text-xs text-slate-500 font-medium mt-0.5">
            View, search, and audit service contracts and associated finished products.
          </p>
        </div>
        <button
          onClick={() => refetch()}
          className="flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 transition cursor-pointer self-start md:self-auto"
        >
          <RefreshCw className="w-3.5 h-3.5" />
          Refresh Table
        </button>
      </div>

      {/* Search & Filter Form Card */}
      <form onSubmit={handleSearch} className="bg-white border border-slate-200/80 rounded-xl p-5 shadow-sm space-y-4">
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
          <div>
            <label className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">
              Contract Name
            </label>
            <div className="relative">
              <Search className="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
              <input
                type="text"
                list="contract-names-list"
                placeholder="Enter Contract Name"
                value={filters.contract_name || ''}
                onChange={(e) => setFilters({ ...filters, contract_name: e.target.value })}
                className="w-full pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-cyan-500 transition"
              />
              <datalist id="contract-names-list">
                {contracts && contracts.map(c => (
                  <option key={c.id} value={c.title} />
                ))}
              </datalist>
            </div>
          </div>

          <div>
            <label className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">
              Supplier Name
            </label>
            <div className="relative">
              <Briefcase className="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
              <input
                type="text"
                list="supplier-names-list"
                placeholder="Enter Supplier Name"
                value={filters.vendor_name || ''}
                onChange={(e) => setFilters({ ...filters, vendor_name: e.target.value })}
                className="w-full pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-cyan-500 transition"
              />
              <datalist id="supplier-names-list">
                {contracts && Array.from(new Set(contracts.map(c => c.vendor_name))).filter(Boolean).map(vendor => (
                  <option key={vendor} value={vendor} />
                ))}
              </datalist>
            </div>
          </div>

          <div>
            <label className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">
              Cost
            </label>
            <div className="relative">
              <DollarSign className="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
              <input
                type="text"
                placeholder="Enter Cost"
                value={filters.cost || ''}
                onChange={(e) => setFilters({ ...filters, cost: e.target.value })}
                className="w-full pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-cyan-500 transition"
              />
            </div>
          </div>

          <div>
            <label className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">
              Start Date
            </label>
            <DatePicker  
              value={filters.datefrom || ''}
              onChange={(e) => setFilters({ ...filters, datefrom: e.target.value })}
              className="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:outline-none focus:border-cyan-500 transition"
            />
          </div>

          <div>
            <label className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">
              End Date
            </label>
            <DatePicker  
              value={filters.dateto || ''}
              onChange={(e) => setFilters({ ...filters, dateto: e.target.value })}
              className="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:outline-none focus:border-cyan-500 transition"
            />
          </div>
        </div>

        <div className="flex items-center gap-3 pt-2">
          <button
            type="submit"
            className="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-xs font-semibold transition cursor-pointer"
          >
            Search
          </button>
          <button
            type="button"
            onClick={handleReset}
            className="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-xs font-semibold transition cursor-pointer"
          >
            Reset
          </button>
          <div className="flex-1"></div>
          <a
            href="/dashboard/contracts/add"
            className="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-xs font-semibold shadow-sm transition cursor-pointer"
          >
            + Add
          </a>
        </div>
      </form>

      {/* Contracts Data Table */}
      {isLoading ? (
        <div className="bg-white border border-slate-200 rounded-xl p-16 flex flex-col items-center justify-center text-slate-400 gap-2">
          <Loader className="w-8 h-8 animate-spin text-cyan-600" />
          <span className="text-xs font-medium">Fetching contract records...</span>
        </div>
      ) : isError || !contracts ? (
        <div className="bg-white border border-slate-200 rounded-xl p-16 flex flex-col items-center justify-center text-rose-500 gap-2">
          <AlertCircle className="w-8 h-8 animate-bounce" />
          <span className="text-xs font-medium">Failed to sync contract records.</span>
        </div>
      ) : contracts.length === 0 ? (
        <div className="bg-white border border-slate-200 rounded-xl p-16 flex flex-col items-center justify-center text-slate-400 gap-2 text-center">
          <Briefcase className="w-8 h-8" />
          <span className="text-xs font-medium">No contracts found matching the active search criteria.</span>
        </div>
      ) : (
        <div className="bg-white border border-slate-200 rounded-xl overflow-x-auto shadow-sm">
          <table className="w-full min-w-max text-left border-collapse text-xs font-medium text-slate-600">
            <thead>
              <tr className="bg-[#333] text-white text-[10px] font-bold uppercase tracking-wider border-b border-slate-200">
                <th className="px-6 py-3">S.No.</th>
                <th className="px-6 py-3">Title</th>
                <th className="px-6 py-3">Supplier Name</th>
                <th className="px-6 py-3 text-right">Cost</th>
                <th className="px-6 py-3 text-center">Issue Date</th>
                <th className="px-6 py-3 text-center">Start Date</th>
                <th className="px-6 py-3 text-center">End Date</th>
                <th className="px-6 py-3">Description</th>
                <th className="px-6 py-3 text-center w-24">Action</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-200 border-x border-b border-slate-200">
              {contracts.map((c, idx) => (
                <tr key={c.id} className="hover:bg-slate-50/50 transition">
                  <td className="px-6 py-4 font-semibold text-slate-700">{idx + 1}.</td>
                  <td className="px-6 py-4">
                    <button 
                      onClick={() => setSelectedContractId(c.id)}
                      className="font-bold text-blue-600 hover:underline text-left cursor-pointer"
                    >
                      {c.title}({c.workorder})
                    </button>
                  </td>
                  <td className="px-6 py-4 font-semibold text-slate-700">{c.vendor_name}</td>
                  <td className="px-6 py-4 text-right font-semibold text-slate-700">
                    {Number(c.cost).toLocaleString('en-IN')}
                  </td>
                  <td className="px-6 py-4 text-center font-semibold text-slate-700">{formatDate(c.issuedate)}</td>
                  <td className="px-6 py-4 text-center font-semibold text-slate-700">{formatDate(c.contract_start_date)}</td>
                  <td className="px-6 py-4 text-center font-semibold text-slate-700">{formatDate(c.contract_end_date)}</td>
                  <td className="px-6 py-4 text-slate-600 max-w-[150px] truncate" title={c.description}>
                    {c.description || ''}
                  </td>
                  <td className="px-6 py-4 text-center">
                    <button
                      onClick={async () => {
                        try {
                          toast.loading('Generating PDF...', { id: 'pdf-toast' });
                          await contractService.downloadPDF(c.id);
                          toast.success('PDF downloaded!', { id: 'pdf-toast' });
                        } catch (err) {
                          console.error('Failed to download PDF:', err);
                          toast.error('Failed to download PDF', { id: 'pdf-toast' });
                        }
                      }}
                      className="inline-flex items-center justify-center p-1.5 text-emerald-600 hover:bg-emerald-50 rounded transition cursor-pointer"
                      title="Download PDF"
                    >
                      <Download className="w-4 h-4 stroke-[2.5px]" />
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {/* Contract Details Dialog Modal Overlay */}
      {selectedContractId !== null && (
        <ContractDetailsModal
          contractId={selectedContractId}
          onClose={() => setSelectedContractId(null)}
        />
      )}

    </main>
  );
}
