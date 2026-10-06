'use client';

import { useListLocation } from '@/components/ui/useListLocation';
import { ListPagination, LEGACY_LIST_LIMIT } from '@/components/ui/ListPagination';
import React, { useState } from 'react';
import Link from 'next/link';
import { useQuery } from '@tanstack/react-query';
import apiClient from '@/services/apiClient';
import contractService, { ContractFilters } from '../../../services/contract.service';
import { ContractDetailsModal } from '../../../components/dashboard/ContractDetailsModal';
import { usePermission } from '../../../contexts/PermissionContext';
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
  Download,
  Home,
  Edit,
  Trash2
} from 'lucide-react';
import toast from 'react-hot-toast';
import { DatePicker } from '../../../components/ui/DatePicker';
import { formatContractDate } from '../../../utils/dateFormatter';

export default function ContractsPage() {
  const { hasPermission } = usePermission();
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

  const [page, setPage] = useState(1);
  const limit = LEGACY_LIST_LIMIT;

  // Fetch contracts
  const locationReady = useListLocation(activeFilters, page, ['contract_name', 'contract_id', 'vendor_name', 'vendor_id', 'cost', 'datefrom', 'dateto', 'sort', 'direction'], (next, nextPage) => { setActiveFilters(next); setFilters(next); setPage(nextPage); });
  const { data: paginatedData, isLoading, isError, refetch } = useQuery({
    queryKey: ['contracts', activeFilters, page],
    enabled: locationReady,
    queryFn: () => contractService.getContracts({ ...activeFilters, page, limit }),
    staleTime: 5 * 60 * 1000
  });

  const { data: contractSuggestions } = useQuery({
    queryKey: ['contract-list-search', filters.contract_name],
    queryFn: () => contractService.getContracts({ contract_name: filters.contract_name, limit: 20 }),
    enabled: (filters.contract_name || '').length >= 2,
  });
  const { data: vendorSuggestions = [] } = useQuery({
    queryKey: ['contract-vendor-search', filters.vendor_name],
    queryFn: async () => (await apiClient.get('/vendors/search', { params: { q: filters.vendor_name } })).data.data as { id: number; name: string }[],
    enabled: (filters.vendor_name || '').length >= 2,
  });
  const contracts = paginatedData?.data || [];
  const total = paginatedData?.total || 0;
  const totalPages = Math.ceil(total / limit);

  // Fetch selected contract details
  const { data: details, isLoading: detailsLoading, isError: detailsError } = useQuery({
    queryKey: ['contract-details', selectedContractId],
    queryFn: () => contractService.getDetails(selectedContractId!),
    enabled: selectedContractId !== null,
    staleTime: 5 * 60 * 1000
  });

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    const contract = contractSuggestions?.data.find(c => c.title === filters.contract_name);
    const vendor = vendorSuggestions.find(v => v.name === filters.vendor_name);
    setActiveFilters({ ...filters, contract_id: contract?.id || filters.contract_id, vendor_id: vendor?.id || filters.vendor_id });
    setPage(1);
  };

  const handleReset = () => {
    const empty = { contract_name: '', vendor_name: '', cost: '', datefrom: '', dateto: '' };
    setFilters(empty);
    setActiveFilters(empty);
    setPage(1);
  };

  const handleDelete = async (id: number) => {
    if (!confirm('Are you sure do you want to delete this Contract')) return;
    try {
      await contractService.deleteContract(id);
      toast.success('Contract deleted successfully');
      refetch();
    } catch (err: any) {
      toast.error(err?.response?.data?.message || 'Failed to delete contract');
    }
  };

  return (
    <div className="w-full px-4 py-4 space-y-4 font-sans select-none text-[#333]">
      
      {/* Header and Title */}
      <div className="flex items-center justify-between border-b border-[#e0e0e0] pb-2 mb-4">
        <h1 className="text-xl text-[#333] font-normal tracking-tight">
          Contracts Manager
        </h1>
        <div className="flex items-center gap-1.5 text-[#555] text-sm cursor-pointer hover:text-[#1683D8]">
          <Home className="w-4 h-4" />
          <span className="font-semibold text-xs">Home</span>
        </div>
      </div>

      {/* Search & Filter Form */}
      <form onSubmit={handleSearch} className="bg-white p-4 mb-4 space-y-3 border border-[#ccc] rounded-[3px]">
        <div className="flex flex-wrap items-end gap-4">
          <div className="w-48">
            <label className="text-xs font-bold text-[#555] block mb-1">
              Contract Name
            </label>
            <input
              type="text"
              list="contract-names-list"
              placeholder="Enter Contract Name"
              value={filters.contract_name || ''}
              onChange={(e) => setFilters({ ...filters, contract_name: e.target.value, contract_id: undefined })}
              className="w-full px-2 py-1.5 bg-white border border-[#ccc] rounded-[3px] text-xs text-[#333] placeholder-[#999] focus:outline-none focus:border-[#1683D8] h-8"
            />
            <datalist id="contract-names-list">
              {(contractSuggestions?.data || contracts).map(c => (
                <option key={c.id} value={c.title} />
              ))}
            </datalist>
          </div>

          <div className="w-48">
            <label className="text-xs font-bold text-[#555] block mb-1">
              Supplier Name
            </label>
            <input
              type="text"
              list="supplier-names-list"
              placeholder="Enter Supplier Name"
              value={filters.vendor_name || ''}
              onChange={(e) => setFilters({ ...filters, vendor_name: e.target.value, vendor_id: undefined })}
              className="w-full px-2 py-1.5 bg-white border border-[#ccc] rounded-[3px] text-xs text-[#333] placeholder-[#999] focus:outline-none focus:border-[#1683D8] h-8"
            />
            <datalist id="supplier-names-list">
              {vendorSuggestions.map(vendor => (
                <option key={vendor.id} value={vendor.name} />
              ))}
            </datalist>
          </div>

          <div className="w-32">
            <label className="text-xs font-bold text-[#555] block mb-1">
              Cost
            </label>
            <input
              type="text"
              placeholder="Enter Cost"
              value={filters.cost || ''}
              onChange={(e) => setFilters({ ...filters, cost: e.target.value })}
              className="w-full px-2 py-1.5 bg-white border border-[#ccc] rounded-[3px] text-xs text-[#333] placeholder-[#999] focus:outline-none focus:border-[#1683D8] h-8"
            />
          </div>

          <div className="w-36">
            <label className="text-xs font-bold text-[#555] block mb-1">
              Start Date
            </label>
            <DatePicker  
              dateFormat="dd-MM-yyyy"
              value={filters.datefrom || ''}
              onChange={(e) => setFilters({ ...filters, datefrom: e.target.value })}
              className="w-full px-2 py-1.5 bg-white border border-[#ccc] rounded-[3px] text-xs text-[#333] focus:outline-none focus:border-[#1683D8] h-8"
            />
          </div>

          <div className="w-36">
            <label className="text-xs font-bold text-[#555] block mb-1">
              End Date
            </label>
            <DatePicker  
              dateFormat="dd-MM-yyyy"
              value={filters.dateto || ''}
              onChange={(e) => setFilters({ ...filters, dateto: e.target.value })}
              className="w-full px-2 py-1.5 bg-white border border-[#ccc] rounded-[3px] text-xs text-[#333] focus:outline-none focus:border-[#1683D8] h-8"
            />
          </div>
        </div>

        <div className="flex items-center gap-2 mt-2">
          <button
            type="submit"
            className="px-4 py-1.5 bg-[#1683D8] hover:bg-[#2563eb] text-white rounded-[3px] text-xs font-semibold cursor-pointer h-8"
          >
            Search
          </button>
          <button
            type="button"
            onClick={handleReset}
            className="px-4 py-1.5 bg-[#1683D8] hover:bg-[#2563eb] text-white rounded-[3px] text-xs font-semibold cursor-pointer h-8"
          >
            Reset
          </button>
          <div className="flex-1"></div>
          {(hasPermission('contracts:add') || hasPermission('legacy:admin/contracts/add')) && (
            <a
              href="/dashboard/contracts/add"
              className="px-4 py-1.5 bg-[#1683D8] hover:bg-[#2563eb] text-white rounded-[3px] text-xs font-semibold cursor-pointer h-8 flex items-center justify-center"
            >
              + Add
            </a>
          )}
        </div>
      </form>

      {/* Contracts Data Table */}
      {isLoading ? (
        <div className="bg-white border border-[#ccc] rounded p-10 flex flex-col items-center justify-center text-[#999] gap-2">
          <Loader className="w-6 h-6 animate-spin text-[#1683D8]" />
          <span className="text-xs font-medium">Loading...</span>
        </div>
      ) : isError || !contracts ? (
        <div className="bg-white border border-[#ccc] rounded p-10 flex flex-col items-center justify-center text-rose-500 gap-2">
          <AlertCircle className="w-6 h-6 animate-bounce" />
          <span className="text-xs font-medium">Error loading data.</span>
        </div>
      ) : contracts.length === 0 ? (
        <div className="bg-white border border-[#ccc] rounded p-10 flex flex-col items-center justify-center text-[#999] gap-2 text-center">
          <span className="text-xs font-medium">No contracts found.</span>
        </div>
      ) : (
        <div className="bg-white border border-[#ccc] overflow-x-auto">
          <table className="w-full text-left border-collapse text-xs text-[#333]">
            <thead>
              <tr className="bg-[#333] text-white font-bold border-b border-[#ccc]">
                <th className="px-3 py-2 border-r border-[#444]">S.No.</th>
                <th className="px-3 py-2 border-r border-[#444]">Title</th>
                <th className="px-3 py-2 border-r border-[#444]">Supplier Name</th>
                <th className="px-3 py-2 border-r border-[#444] text-right">Cost</th>
                <th className="px-3 py-2 border-r border-[#444] text-center">Issue Date</th>
                <th className="px-3 py-2 border-r border-[#444] text-center">Start Date</th>
                <th className="px-3 py-2 border-r border-[#444] text-center">End Date</th>
                <th className="px-3 py-2 border-r border-[#444]">Description</th>
                <th className="px-3 py-2 text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              {contracts.map((c, idx) => (
                <tr key={c.id} className={idx % 2 === 0 ? 'bg-white' : 'bg-[#f9f9f9]'}>
                  <td className="px-3 py-2 border-r border-[#ccc] border-b">{idx + 1}.</td>
                  <td className="px-3 py-2 border-r border-[#ccc] border-b">
                    <button 
                      onClick={() => setSelectedContractId(c.id)}
                      className="text-[#1683D8] hover:underline text-left cursor-pointer"
                    >
                      {c.title}({c.workorder})
                    </button>
                  </td>
                  <td className="px-3 py-2 border-r border-[#ccc] border-b">{c.vendor_name}</td>
                  <td className="px-3 py-2 border-r border-[#ccc] border-b text-right">
                    {Number(c.cost).toLocaleString('en-IN')}
                  </td>
                  <td className="px-3 py-2 border-r border-[#ccc] border-b text-center">{formatContractDate(c.issuedate)}</td>
                  <td className="px-3 py-2 border-r border-[#ccc] border-b text-center">{formatContractDate(c.contract_start_date)}</td>
                  <td className="px-3 py-2 border-r border-[#ccc] border-b text-center">{formatContractDate(c.contract_end_date)}</td>
                  <td className="px-3 py-2 border-r border-[#ccc] border-b max-w-[150px] truncate" title={c.description}>
                    {c.description || ''}
                  </td>
                  <td className="px-3 py-2 border-b text-center">
                    <div className="flex items-center justify-center gap-2">
                      {(c.designsheet_count || 0) === 0 && (hasPermission('contracts:edit') || hasPermission('legacy:admin/contracts/edit')) && (
                        <Link href={`/dashboard/contracts/edit/${c.id}`} className="text-[#1683D8] hover:text-blue-800" title="Edit">
                          <Edit className="w-4 h-4" />
                        </Link>
                      )}
                      {(c.designsheet_count || 0) === 0 && (hasPermission('contracts:delete') || hasPermission('legacy:admin/contracts/delete')) && (
                        <button onClick={() => handleDelete(c.id)} className="text-rose-600 hover:text-rose-800" title="Delete">
                          <Trash2 className="w-4 h-4" />
                        </button>
                      )}
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
                        className="text-emerald-600 hover:text-emerald-800 cursor-pointer"
                        title="Download PDF"
                      >
                        <Download className="w-4 h-4 stroke-[2.5px]" />
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
          

        </div>
      )}

      {!isLoading && !isError && <ListPagination page={page} limit={limit} total={total} onPageChange={setPage} />}

      {/* Contract Details Dialog Modal Overlay */}
      {selectedContractId !== null && (
        <ContractDetailsModal
          contractId={selectedContractId}
          onClose={() => setSelectedContractId(null)}
        />
      )}

    </div>
  );
}

