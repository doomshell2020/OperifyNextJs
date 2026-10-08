'use client';

/* eslint-disable @typescript-eslint/no-explicit-any */

import { LegacyPageHeader } from '@/components/ui/LegacyPageHeader';
import { useLegacyActionAccess } from '@/components/ui/useLegacyActionAccess';
import { useListLocation } from '@/components/ui/useListLocation';
import { ListPagination, LEGACY_LIST_LIMIT } from '@/components/ui/ListPagination';
import React, { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import Link from 'next/link';
import { usePermission } from '@/contexts/PermissionContext';
import { designsheetService, DesignSheetFilter } from '../../../services/designsheet.service';
import {
  FileText, Search, RefreshCw, Loader, AlertCircle, Briefcase, Plus, Edit, Trash2, Printer
} from 'lucide-react';
import { toast } from 'react-hot-toast';
import { formatQty, formatAmt } from '@/utils/formatters';
import { DatePicker } from '../../../components/ui/DatePicker';
import { formatContractDate } from '../../../utils/dateFormatter';

export default function DesignSheetsPage() {
  const { hasPermission } = usePermission();
  const canAction = useLegacyActionAccess();
  const [filters, setFilters] = useState<DesignSheetFilter>({
    contract_id: '', contract_name: '',
    datestart: '',
    dateto: ''
  });

  const [activeFilters, setActiveFilters] = useState<DesignSheetFilter>({ page: 1, limit: LEGACY_LIST_LIMIT });
  const [selectedSheetNo, setSelectedSheetNo] = useState<string | null>(null);
  const [selectedContractId, setSelectedContractId] = useState<string | null>(null);

  const locationReady = useListLocation(activeFilters, activeFilters.page || 1, ['contract_id', 'contract_name', 'datestart', 'dateto', 'sort', 'direction'], (next, nextPage) => { setActiveFilters({ ...next, page: nextPage, limit: LEGACY_LIST_LIMIT }); setFilters(next); });
  const { data, isLoading, isError, refetch } = useQuery({
    queryKey: ['designsheets', activeFilters],
    enabled: locationReady,
    queryFn: () => designsheetService.getDesignSheets(activeFilters),
    staleTime: 5 * 60 * 1000
  });

  const designs = data?.data || [];
  const canAdd = hasPermission("designsheet:add") || hasPermission("legacy:admin/designsheet/add");
  const canEdit = hasPermission("designsheet:edit") || hasPermission("legacy:admin/designsheet/edit");
  const canDelete = hasPermission("designsheet:delete") || hasPermission("legacy:admin/designsheet/delete");

  const { data: detailsData, isLoading: detailsLoading } = useQuery({
    queryKey: ['designsheet-details', selectedSheetNo],
    queryFn: () => designsheetService.getDesignSheetForView(selectedSheetNo!),
    enabled: selectedSheetNo !== null,
    staleTime: 5 * 60 * 1000
  });

  const { data: contractData, isLoading: contractLoading } = useQuery({
    queryKey: ['contract-details', selectedContractId],
    queryFn: () => designsheetService.getContractDetails(selectedContractId!),
    enabled: selectedContractId !== null,
    staleTime: 5 * 60 * 1000
  });

  const { data: contractOptions = [] } = useQuery<{ id: number; title: string; workorder: string }[]>({
    queryKey: ['designsheet-filter-contracts', filters.contract_name],
    queryFn: async () => (await designsheetService.searchContracts(filters.contract_name || '')).contracts,
    enabled: (filters.contract_name || '').trim().length >= 2,
  });

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    const selected = contractOptions.find(row => `${row.title}(${row.workorder})` === filters.contract_name);
    setActiveFilters({ ...filters, contract_id: selected ? String(selected.id) : filters.contract_id, page: 1, limit: LEGACY_LIST_LIMIT });
  };

  const handleReset = () => {
    const empty = { contract_id: '', contract_name: '', datestart: '', dateto: '' };
    setFilters(empty);
    setActiveFilters({ page: 1, limit: LEGACY_LIST_LIMIT });
  };

  const handlePageChange = (newPage: number) => {
    setActiveFilters(prev => ({ ...prev, page: newPage }));
  };

  const handleDelete = async (id: number) => {
    if (confirm('Are you sure do you want to delete this Design Sheet')) {
        try {
            await designsheetService.deleteDesignSheet(id);
            toast.success('Production Sheet deleted successfully');
            refetch();
        } catch (e: unknown) {
            const response = (e as { response?: { data?: { message?: string } } })?.response;
            toast.error(response?.data?.message || 'Failed to delete Design Sheet');
        }
    }
  };


  return (
    <main className="max-w-7xl w-full mx-auto px-6 py-8 space-y-6 select-none font-sans">
      <LegacyPageHeader title="Design Sheet" />
      <form onSubmit={handleSearch} className="legacy-filter-row">
        <label>Contract Name
          <input aria-label="Contract Name" list="designsheet-contract-options" placeholder="Enter Contract Name" value={filters.contract_name || ''} onChange={e => setFilters({ ...filters, contract_name: e.target.value, contract_id: '' })} />
          <datalist id="designsheet-contract-options">{contractOptions.map(row => <option key={row.id} value={`${row.title}(${row.workorder})`} />)}</datalist>
        </label>
        <label>Start Date<DatePicker aria-label="Start Date" placeholder="Start Date" value={filters.datestart || ''} onChange={e => setFilters({ ...filters, datestart: e.target.value })} /></label>
        <label>End Date<DatePicker aria-label="End Date" placeholder="End Date" value={filters.dateto || ''} onChange={e => setFilters({ ...filters, dateto: e.target.value })} /></label>
        <div className="legacy-filter-actions"><button type="submit" className="legacy-button">Search</button><button type="button" onClick={handleReset} className="legacy-button">Reset</button></div>
        {canAdd && <Link href="/dashboard/design-sheet/add" className="legacy-button mb-1 ml-auto"><Plus size={12} />Add</Link>}
      </form>

      {isLoading ? (
        <div className="bg-white border border-slate-200 rounded-xl p-16 flex flex-col items-center justify-center text-slate-400 gap-2">
          <Loader className="w-8 h-8 animate-spin text-cyan-600" />
        </div>
      ) : isError ? (
        <div className="bg-white border border-slate-200 rounded-xl p-16 flex flex-col items-center justify-center text-rose-500 gap-2">
          <AlertCircle className="w-8 h-8 animate-bounce" />
          <span className="text-xs font-medium">Failed to sync records.</span>
        </div>
      ) : designs.length === 0 ? (
        <div className="bg-white border border-slate-200 rounded-xl p-16 flex flex-col items-center justify-center text-slate-400 gap-2 text-center">
          <Briefcase className="w-8 h-8" />
          <span className="text-xs font-medium">No design sheets found.</span>
        </div>
      ) : (
        <>
        <div className="bg-white border border-slate-200 rounded-xl overflow-x-auto shadow-sm">
          <table className="w-full text-left border-collapse text-xs font-medium text-slate-600">
            <thead>
              <tr className="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200">
                <th style={{width:'3%'}} className="px-6 py-3">S.No.</th>
                <th style={{width:'9%'}} className="px-6 py-3">Design Sheet No.</th>
                <th style={{width:'25%'}} className="px-6 py-3">Contract Name</th>
                <th style={{width:'35%'}} className="px-6 py-3">Type Of Cable</th>
                <th style={{width:'8%'}} className="px-6 py-3">Quantity(in KM)</th>
                <th style={{width:'7%'}} className="px-6 py-3 text-center">Issue Date</th>
                <th style={{width:'7%'}} className="px-6 py-3 text-center">Design Sheet</th>
                <th style={{width:'6%'}} className="px-6 py-3 text-center">Action</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {designs.length === 0 && <tr><td colSpan={8} className="p-8 text-center text-slate-500">No design sheets found matching your filters.</td></tr>}
              {designs.map((d: any, idx: number) => (
                <tr key={d.id} className="hover:bg-slate-50/50 transition">
                  <td className="px-6 py-4 font-bold text-slate-900">{((activeFilters.page || 1) - 1) * (activeFilters.limit || 50) + idx + 1}</td>
                  <td className="px-6 py-4 font-bold text-slate-900">
                     <span className="text-cyan-600 cursor-pointer" onClick={() => {if(canAction('designsheet','viewdesignsheet'))setSelectedSheetNo(d.designsheetno);}}>
                         {d.designsheetno}
                     </span>
                  </td>
                  <td className="px-4 py-3">
                        <button
                            onClick={() => setSelectedContractId(d.contract_id)}
                            className="text-cyan-600 hover:text-cyan-800 font-semibold hover:underline"
                        >
                            {d.contract_title ? `${d.contract_title} (${d.workorder})` : ''}
                        </button>
                  </td>
                  <td className="px-6 py-4 font-semibold text-slate-700">{d.item_name}</td>
                  <td className="px-6 py-4 font-bold text-slate-900">{formatQty(d.quantity)}</td>
                  <td className="px-6 py-4 text-center font-semibold">{formatContractDate(d.datefrom)}</td>
                  <td className="px-6 py-4 text-center">
                    {(d.design_sheet || [1, 2, 3, 4, 5].some(rev => d[`r${rev}`])) ? (
                        <span className="inline-flex items-center justify-center gap-2 flex-wrap">
                          {d.design_sheet && <a href={`/designsheet/${d.design_sheet}`} target="_blank" rel="noreferrer" className="text-cyan-600 underline">
                              Download
                          </a>}
                          {[1, 2, 3, 4, 5].map((rev) => d[`r${rev}`] ? (
                            <a key={rev} href={`/designsheet/${d[`r${rev}`]}`} target="_blank" rel="noreferrer" className="text-cyan-600 underline">
                              R{rev}
                            </a>
                          ) : null)}
                        </span>
                    ) : '-'}
                  </td>
                  <td className="px-6 py-4 flex items-center justify-center gap-3">
                    {canEdit && (
                      <Link href={`/dashboard/design-sheet/edit/${d.id}`} className="text-blue-500 hover:text-blue-700 transition" title="Edit">
                        <Edit className="w-4 h-4" />
                      </Link>
                    )}
                    {canDelete && Number(d.indentpo_count) === 0 && (
                      <button onClick={() => handleDelete(d.id)} className="text-rose-500 hover:text-rose-700 transition" title="Delete">
                        <Trash2 className="w-4 h-4" />
                      </button>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>

        </div>
        {/* Pagination UI */}

        </>
      )}

      {!isLoading && !isError && data && <ListPagination page={activeFilters.page || 1} limit={LEGACY_LIST_LIMIT} total={data.total || 0} onPageChange={handlePageChange} />}

      {selectedSheetNo !== null && (
        <div className="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm animate-in fade-in duration-200" onClick={() => setSelectedSheetNo(null)}>
          <div className="bg-white border border-slate-200 shadow-2xl rounded max-w-4xl w-full p-8 flex flex-col relative overflow-hidden animate-in zoom-in-95 duration-200 max-h-[90vh]" onClick={e => e.stopPropagation()}>
            {detailsLoading ? (
              <div className="flex-1 flex flex-col items-center justify-center py-20 text-slate-400 gap-2">
                <Loader className="w-8 h-8 animate-spin text-cyan-600" />
              </div>
            ) : detailsData && detailsData.designsheet ? (
              <div className="flex-1 flex flex-col overflow-y-auto pr-2">
                <div className="relative mb-6">
                    <h3 className="text-base font-extrabold text-slate-900 text-center">Design Sheet Details</h3>
                    <div className="absolute right-0 top-0">
                        {canAction("designsheet", "viewdesignsheet") && (
                          <Link href={`/dashboard/design-sheet/print/${detailsData.designsheet.designsheetno}`} target="_blank" className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs font-semibold shadow-sm transition">
                            <Printer className="w-3.5 h-3.5" /> Print
                        </Link>
                        )}
                    </div>
                </div>

                <div className="grid grid-cols-2 gap-y-3 gap-x-8 mb-8 text-sm font-semibold text-slate-800">
                    <div>
                        <span className="text-slate-600">Design Sheet No:- </span>
                        {detailsData.designsheet.designsheetno}
                    </div>
                    <div>
                        <span className="text-slate-600">Issue Date:- </span>
                        {formatContractDate(detailsData.designsheet.datefrom)}
                    </div>
                    <div>
                        <span className="text-slate-600">Contract:- </span>
                        {detailsData.designsheet.contract_no}
                    </div>
                    <div>
                        <span className="text-slate-600">Finished Product:- </span>
                        {detailsData.designsheet.item_name}
                    </div>
                    <div className="col-span-2">
                        <span className="text-slate-600">Quantity:- </span>
                        {formatQty(detailsData.designsheet.quantity)} KM
                    </div>
                </div>

                <h4 className="text-sm font-extrabold text-slate-900 text-center mb-4">Raw Material</h4>

                <div className="border border-slate-200 overflow-hidden">
                  <table className="w-full text-left border-collapse text-xs text-slate-700">
                    <thead>
                      <tr className="bg-slate-100 border-b border-slate-200">
                        <th className="px-3 py-2 border-r border-slate-200">S.No.</th>
                        <th className="px-3 py-2 border-r border-slate-200">Item Name</th>
                        <th className="px-3 py-2 border-r border-slate-200 text-right">Qty(Per KM)</th>
                        <th className="px-3 py-2 border-r border-slate-200 text-right">Total Qty</th>
                        <th className="px-3 py-2">UOM</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-200 bg-white">
                      {detailsData.designsheetdetails.map((item: any, idx: number) => (
                        <tr key={item.id} className="hover:bg-slate-50 transition">
                          <td className="px-3 py-2 border-r border-slate-200">{idx + 1}.</td>
                          <td className="px-3 py-2 border-r border-slate-200 uppercase">{item.item_name}</td>
                          <td className="px-3 py-2 border-r border-slate-200 text-right font-medium">{formatQty(item.km_item_qty)}</td>
                          <td className="px-3 py-2 border-r border-slate-200 text-right font-medium">{formatQty(item.item_qty)}</td>
                          <td className="px-3 py-2 uppercase">{item.uom}</td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>
            ) : null}
          </div>
        </div>
      )}

      {selectedContractId !== null && (
        <div className="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm animate-in fade-in duration-200" onClick={() => setSelectedContractId(null)}>
          <div className="bg-white border border-slate-200 shadow-2xl rounded max-w-6xl w-full p-8 flex flex-col relative overflow-hidden animate-in zoom-in-95 duration-200 max-h-[95vh]" onClick={e => e.stopPropagation()}>
            {contractLoading ? (
              <div className="flex-1 flex flex-col items-center justify-center py-20 text-slate-400 gap-2">
                <Loader className="w-8 h-8 animate-spin text-cyan-600" />
              </div>
            ) : contractData && contractData.contract ? (
              <div className="flex-1 flex flex-col overflow-y-auto pr-4 custom-scrollbar">
                <div className="relative mb-6">
                    <h3 className="text-lg font-extrabold text-slate-900 text-center">Contract Details</h3>
                    <div className="absolute right-0 top-0">
                        {hasPermission("contracts:pdf") && (
                          <Link href={`/dashboard/production/viewcontractdetailspdf/${selectedContractId}`} target="_blank" className="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded font-bold shadow-sm transition">
                            <Printer className="w-4 h-4" /> Print
                        </Link>
                        )}
                    </div>
                </div>

                <div className="grid grid-cols-2 gap-y-4 gap-x-8 mb-8 text-sm text-slate-800">
                    <div><span className="font-bold text-slate-900">Work Order:- </span>{contractData.contract.workorder}</div>
                    <div><span className="font-bold text-slate-900">Title:- </span>{contractData.contract.title}</div>
                    <div><span className="font-bold text-slate-900">Issue Date:- </span>{formatContractDate(contractData.contract.issuedate)}</div>
                    <div><span className="font-bold text-slate-900">Contract Start Date:- </span>{formatContractDate(contractData.contract.contract_start_date)}</div>
                    <div><span className="font-bold text-slate-900">Contract End Date:- </span>{formatContractDate(contractData.contract.contract_end_date)}</div>
                    <div><span className="font-bold text-slate-900">Supplier Name:- </span>{contractData.contract.supplier_name}</div>
                    <div><span className="font-bold text-slate-900">Cost:- </span>{formatAmt(contractData.contract.cost)}</div>
                    <div><span className="font-bold text-slate-900">Labour Cost:- </span>{formatAmt(contractData.contract.labour_cost)}</div>
                    <div><span className="font-bold text-slate-900">Operational Cost:- </span>{formatAmt(contractData.contract.operational_cost)}</div>
                </div>

                <h3 className="text-base font-extrabold text-slate-900 text-center mb-6">Finished Products</h3>

                {contractData.finishedProducts.map((fp: any, idx: number) => (
                    <div key={idx} className="mb-10">
                        <div className="border border-slate-200 overflow-hidden mb-4">
                            <table className="w-full text-left text-xs border-collapse">
                                <thead className="bg-slate-50 border-b border-slate-200 font-bold text-slate-800">
                                    <tr>
                                        <td className="px-3 py-2 border-r border-slate-200"><span className="text-slate-500">Product:-</span> {fp.item_name}</td>
                                        <td className="px-3 py-2 border-r border-slate-200"><span className="text-slate-500">Quantity:-</span> {formatQty(fp.quantity)} KM</td>
                                        <td className="px-3 py-2 border-r border-slate-200"><span className="text-slate-500">Planned Qty:-</span> {formatQty(fp.planned_qty)} KM</td>
                                        <td className="px-3 py-2 border-r border-slate-200"><span className="text-slate-500">Prepared Qty:-</span> {formatQty(fp.prepared_qty)} KM</td>
                                        <td className="px-3 py-2"><span className="text-slate-500">Price:-</span> {formatAmt(fp.price)}</td>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                        {fp.rawMaterials && fp.rawMaterials.length > 0 ? (
                            <div className="border border-slate-200 overflow-hidden">
                                <table className="w-full text-left text-xs border-collapse">
                                    <thead className="bg-slate-50 font-bold text-slate-900 text-center border-b border-slate-200">
                                        <tr><th colSpan={5} className="py-2">Raw Material</th></tr>
                                    </thead>
                                    <thead className="bg-white font-bold text-slate-800 border-b border-slate-200">
                                        <tr>
                                            <th className="px-3 py-2 border-r border-slate-200 w-16">S.No.</th>
                                            <th className="px-3 py-2 border-r border-slate-200">Item Name</th>
                                            <th className="px-3 py-2 border-r border-slate-200 text-right w-32">Qty(As per Design)</th>
                                            <th className="px-3 py-2 border-r border-slate-200 text-right w-32">Issued Qty</th>
                                            <th className="px-3 py-2 text-right w-32">Pending Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-slate-100 bg-white">
                                        {fp.rawMaterials.map((rm: any, rIdx: number) => (
                                            <React.Fragment key={rIdx}>
                                                <tr className="hover:bg-slate-50 transition">
                                                    <td className="px-3 py-2 border-r border-slate-200">{rIdx + 1}.</td>
                                                    <td className="px-3 py-2 border-r border-slate-200 uppercase">{rm.display_name}</td>
                                                    <td className="px-3 py-2 border-r border-slate-200 text-right">{formatQty(rm.qty_as_per_design)}</td>
                                                    <td className="px-3 py-2 border-r border-slate-200 text-right">{formatQty(rm.issued_qty)}</td>
                                                    <td className="px-3 py-2 text-right">{formatQty(rm.pending_qty)}</td>
                                                </tr>
                                                {rm.subItems && rm.subItems.map((subItem: any, sIdx: number) => (
                                                    <tr key={`sub-${sIdx}`} className="bg-slate-50/50">
                                                        <td className="px-3 py-2 border-r border-slate-200"></td>
                                                        <td className="px-3 py-2 border-r border-slate-200 uppercase">{subItem.item_name}</td>
                                                        <td className="px-3 py-2 border-r border-slate-200 text-right"></td>
                                                        <td className="px-3 py-2 border-r border-slate-200 text-right">{formatQty(subItem.issued_qty)}</td>
                                                        <td className="px-3 py-2 text-right"></td>
                                                    </tr>
                                                ))}
                                            </React.Fragment>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        ) : (
                            <div className="text-center py-4 text-sm text-slate-500 border border-slate-200 border-t-0 font-medium">Production Not Started Yet.</div>
                        )}
                    </div>
                ))}

                <h3 className="text-base font-extrabold text-slate-900 text-center mb-6 mt-4">Production Orders</h3>
                <div className="border border-slate-200 overflow-hidden mb-10">
                    <table className="w-full text-left text-xs border-collapse">
                        <thead className="bg-slate-50 font-bold text-slate-800 border-b border-slate-200">
                            <tr>
                                <th className="px-3 py-2 border-r border-slate-200">PO No.</th>
                                <th className="px-3 py-2 border-r border-slate-200">Issue Date</th>
                                <th className="px-3 py-2 border-r border-slate-200">Product</th>
                                <th className="px-3 py-2 border-r border-slate-200 text-right">Planned Qty</th>
                                <th className="px-3 py-2 border-r border-slate-200 text-right">Prepared Qty</th>
                                <th className="px-3 py-2 border-r border-slate-200">Start Date</th>
                                <th className="px-3 py-2 border-r border-slate-200">End Date</th>
                                <th className="px-3 py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100 bg-white">
                            {contractData.productionOrders.length > 0 ? contractData.productionOrders.map((po: any, pIdx: number) => (
                                <tr key={pIdx} className="hover:bg-slate-50 transition">
                                    <td className="px-3 py-2 border-r border-slate-200">{po.po_id}</td>
                                    <td className="px-3 py-2 border-r border-slate-200">{formatContractDate(po.issuedate)}</td>
                                    <td className="px-3 py-2 border-r border-slate-200 uppercase">{po.item_name}</td>
                                    <td className="px-3 py-2 border-r border-slate-200 text-right">{formatQty(po.plannedqty)}</td>
                                    <td className="px-3 py-2 border-r border-slate-200 text-right">{formatQty(po.prepared_qty)}</td>
                                    <td className="px-3 py-2 border-r border-slate-200">{formatContractDate(po.startdate)}</td>
                                    <td className="px-3 py-2 border-r border-slate-200">{formatContractDate(po.enddate)}</td>
                                    <td className="px-3 py-2">{po.status === 'C' ? 'Close' : 'Open'}</td>
                                </tr>
                            )) : (
                                <tr>
                                    <td colSpan={8} className="px-3 py-6 text-center text-slate-500 font-medium">No Production Orders Found</td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                <h3 className="text-base font-extrabold text-slate-900 text-center mb-6">Inspection Report</h3>
                <div className="border border-slate-200 overflow-hidden mb-6">
                    <table className="w-full text-left text-xs border-collapse">
                        <thead className="bg-slate-50 font-bold text-slate-800 border-b border-slate-200">
                            <tr>
                                <th className="px-3 py-2 border-r border-slate-200 w-16">S.No</th>
                                <th className="px-3 py-2 border-r border-slate-200">Inspector Name</th>
                                <th className="px-3 py-2">Inspection Date</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100 bg-white">
                            {contractData.inspectionReports.length > 0 ? contractData.inspectionReports.map((ir: any, irIdx: number) => (
                                <tr key={irIdx} className="hover:bg-slate-50 transition">
                                    <td className="px-3 py-2 border-r border-slate-200">{irIdx + 1}.</td>
                                    <td className="px-3 py-2 border-r border-slate-200 uppercase">{ir.name}</td>
                                    <td className="px-3 py-2">{formatContractDate(ir.inspection_date)}</td>
                                </tr>
                            )) : (
                                <tr>
                                    <td colSpan={3} className="px-3 py-6 text-center text-slate-500 font-medium">No Inspection Reports Found</td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

              </div>
            ) : null}
          </div>
        </div>
      )}
    </main>
  );
}
