'use client';

import React, { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import Link from 'next/link';
import { designsheetService, DesignSheetFilter } from '../../../services/designsheet.service';
import { DashboardFilters } from '../../../components/dashboard/DashboardFilters';
import { 
  FileText, Search, RefreshCw, Eye, Loader, AlertCircle, Briefcase, Plus, X, Edit, Trash2, Printer
} from 'lucide-react';
import { toast } from 'react-hot-toast';

export default function DesignSheetsPage() {
  const [filters, setFilters] = useState<DesignSheetFilter>({
    contract_id: '',
    datestart: '',
    dateto: ''
  });

  const [activeFilters, setActiveFilters] = useState<DesignSheetFilter>({});
  const [selectedSheetNo, setSelectedSheetNo] = useState<string | null>(null);
  const [selectedContractId, setSelectedContractId] = useState<string | null>(null);

  const { data, isLoading, isError, refetch } = useQuery({
    queryKey: ['designsheets', activeFilters],
    queryFn: () => designsheetService.getDesignSheets(activeFilters),
    staleTime: 5 * 60 * 1000
  });
  
  const designs = data?.data || [];

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

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    setActiveFilters({ ...filters });
  };

  const handleReset = () => {
    const empty = { contract_id: '', datestart: '', dateto: '' };
    setFilters(empty);
    setActiveFilters(empty);
  };

  const handleDelete = async (id: number) => {
    if (confirm('Are you sure you want to delete this Design Sheet?')) {
        try {
            await designsheetService.deleteDesignSheet(id);
            toast.success('Production Sheet deleted successfully');
            refetch();
        } catch (e) {
            toast.error('Failed to delete Design Sheet');
        }
    }
  };

  const formatDate = (dateStr?: string) => {
    if (!dateStr) return 'N/A';
    return new Date(dateStr).toLocaleDateString('en-IN', {
      day: '2-digit', month: '2-digit', year: 'numeric'
    });
  };

  return (
    <main className="max-w-7xl w-full mx-auto px-6 py-8 space-y-6 select-none font-sans">
      <DashboardFilters />

      <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 className="text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
            <FileText className="w-5 h-5 text-cyan-600" />
            Design Sheets Management
          </h1>
        </div>
        <div className="flex gap-2">
          <Link href="/dashboard/design-sheet/add" className="flex items-center gap-1.5 px-3 py-1.5 bg-cyan-600 hover:bg-cyan-500 border border-cyan-600 rounded-lg text-xs font-semibold text-white transition cursor-pointer self-start md:self-auto">
            <Plus className="w-3.5 h-3.5" /> Add Design Sheet
          </Link>
          <button onClick={() => refetch()} className="flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 transition cursor-pointer self-start md:self-auto">
            <RefreshCw className="w-3.5 h-3.5" /> Refresh
          </button>
        </div>
      </div>

      <form onSubmit={handleSearch} className="bg-white border border-slate-200/80 rounded-xl p-5 shadow-sm space-y-4">
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <div>
            <label className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Contract ID</label>
            <div className="relative">
              <Search className="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
              <input type="text" placeholder="Enter Contract ID" value={filters.contract_id || ''} onChange={(e) => setFilters({ ...filters, contract_id: e.target.value })} className="w-full pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-cyan-500 transition" />
            </div>
          </div>
          <div>
            <label className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Date From</label>
            <input type="date" value={filters.datestart || ''} onChange={(e) => setFilters({ ...filters, datestart: e.target.value })} className="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:outline-none focus:border-cyan-500 transition" />
          </div>
          <div>
            <label className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Date To</label>
            <input type="date" value={filters.dateto || ''} onChange={(e) => setFilters({ ...filters, dateto: e.target.value })} className="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:outline-none focus:border-cyan-500 transition" />
          </div>
        </div>
        <div className="flex items-center justify-end gap-3 pt-2">
          <button type="button" onClick={handleReset} className="px-4 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg text-xs font-semibold text-slate-600 transition cursor-pointer">Reset</button>
          <button type="submit" className="px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white rounded-lg text-xs font-semibold shadow-sm transition cursor-pointer">Search</button>
        </div>
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
        <div className="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
          <table className="w-full text-left border-collapse text-xs font-medium text-slate-600">
            <thead>
              <tr className="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200">
                <th className="px-6 py-3">S.No.</th>
                <th className="px-6 py-3">Design Sheet No.</th>
                <th className="px-6 py-3">Contract Name</th>
                <th className="px-6 py-3">Type Of Cable</th>
                <th className="px-6 py-3">Quantity(in KM)</th>
                <th className="px-6 py-3 text-center">Issue Date</th>
                <th className="px-6 py-3 text-center">Design Sheet</th>
                <th className="px-6 py-3 text-center">Action</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {designs.map((d: any, idx: number) => (
                <tr key={d.id} className="hover:bg-slate-50/50 transition">
                  <td className="px-6 py-4 font-bold text-slate-900">{idx + 1}</td>
                  <td className="px-6 py-4 font-bold text-slate-900">
                     <span className="text-cyan-600 cursor-pointer" onClick={() => setSelectedSheetNo(d.designsheetno)}>
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
                  <td className="px-6 py-4 font-bold text-slate-900">{d.quantity}</td>
                  <td className="px-6 py-4 text-center font-semibold">{formatDate(d.datefrom)}</td>
                  <td className="px-6 py-4 text-center">
                    {d.design_sheet ? (
                        <a href={`/designsheet/${d.design_sheet}`} target="_blank" rel="noreferrer" className="text-cyan-600 underline">
                            Download
                        </a>
                    ) : '-'}
                  </td>
                  <td className="px-6 py-4 text-center flex flex-wrap justify-center gap-2 w-48">
                    <Link href={`/dashboard/design-sheet/edit/${d.id}`} className="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg text-slate-700 transition cursor-pointer text-[10px] font-bold uppercase tracking-wider">
                      <Edit className="w-3.5 h-3.5" /> Edit
                    </Link>
                    <button onClick={() => handleDelete(d.id)} className="inline-flex items-center gap-1 px-2.5 py-1 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg text-rose-700 transition cursor-pointer text-[10px] font-bold uppercase tracking-wider">
                      <Trash2 className="w-3.5 h-3.5" /> Delete
                    </button>
                    <Link href={`/dashboard/design-sheet/print/${d.designsheetno}`} target="_blank" className="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg text-emerald-700 transition cursor-pointer text-[10px] font-bold uppercase tracking-wider">
                      <Printer className="w-3.5 h-3.5" /> Print
                    </Link>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

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
                        <Link href={`/dashboard/design-sheet/print/${detailsData.designsheet.designsheetno}`} target="_blank" className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs font-semibold shadow-sm transition">
                            <Printer className="w-3.5 h-3.5" /> Print
                        </Link>
                    </div>
                </div>

                <div className="grid grid-cols-2 gap-y-3 gap-x-8 mb-8 text-sm font-semibold text-slate-800">
                    <div>
                        <span className="text-slate-600">Design Sheet No:- </span>
                        {detailsData.designsheet.designsheetno}
                    </div>
                    <div>
                        <span className="text-slate-600">Issue Date:- </span>
                        {formatDate(detailsData.designsheet.datefrom)}
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
                        {detailsData.designsheet.quantity} KM
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
                          <td className="px-3 py-2 border-r border-slate-200 text-right font-medium">{parseFloat(item.km_item_qty).toFixed(2)}</td>
                          <td className="px-3 py-2 border-r border-slate-200 text-right font-medium">{parseFloat(item.item_qty).toFixed(2)}</td>
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
                        <Link href={`/dashboard/production/viewcontractdetailspdf/${selectedContractId}`} target="_blank" className="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded font-bold shadow-sm transition">
                            <Printer className="w-4 h-4" /> Print
                        </Link>
                    </div>
                </div>

                <div className="grid grid-cols-2 gap-y-4 gap-x-8 mb-8 text-sm text-slate-800">
                    <div><span className="font-bold text-slate-900">Work Order:- </span>{contractData.contract.workorder}</div>
                    <div><span className="font-bold text-slate-900">Title:- </span>{contractData.contract.title}</div>
                    <div><span className="font-bold text-slate-900">Issue Date:- </span>{formatDate(contractData.contract.issuedate)}</div>
                    <div><span className="font-bold text-slate-900">Contract Start Date:- </span>{formatDate(contractData.contract.contract_start_date)}</div>
                    <div><span className="font-bold text-slate-900">Contract End Date:- </span>{formatDate(contractData.contract.contract_end_date)}</div>
                    <div><span className="font-bold text-slate-900">Supplier Name:- </span>{contractData.contract.supplier_name}</div>
                    <div><span className="font-bold text-slate-900">Cost:- </span>{Number(contractData.contract.cost).toLocaleString()}</div>
                    <div><span className="font-bold text-slate-900">Labour Cost:- </span>{contractData.contract.labour_cost}</div>
                    <div><span className="font-bold text-slate-900">Operational Cost:- </span>{parseFloat(contractData.contract.operational_cost).toFixed(2)}</div>
                </div>

                <h3 className="text-base font-extrabold text-slate-900 text-center mb-6">Finished Products</h3>

                {contractData.finishedProducts.map((fp: any, idx: number) => (
                    <div key={idx} className="mb-10">
                        <div className="border border-slate-200 overflow-hidden mb-4">
                            <table className="w-full text-left text-xs border-collapse">
                                <thead className="bg-slate-50 border-b border-slate-200 font-bold text-slate-800">
                                    <tr>
                                        <td className="px-3 py-2 border-r border-slate-200"><span className="text-slate-500">Product:-</span> {fp.item_name}</td>
                                        <td className="px-3 py-2 border-r border-slate-200"><span className="text-slate-500">Quantity:-</span> {parseFloat(fp.quantity).toFixed(2)} KM</td>
                                        <td className="px-3 py-2 border-r border-slate-200"><span className="text-slate-500">Planned Qty:-</span> {parseFloat(fp.planned_qty).toFixed(2)} KM</td>
                                        <td className="px-3 py-2 border-r border-slate-200"><span className="text-slate-500">Prepared Qty:-</span> {parseFloat(fp.prepared_qty).toFixed(2)} KM</td>
                                        <td className="px-3 py-2"><span className="text-slate-500">Price:-</span> {Number(fp.price).toLocaleString()}</td>
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
                                                    <td className="px-3 py-2 border-r border-slate-200 text-right">{parseFloat(rm.qty_as_per_design).toFixed(2)}</td>
                                                    <td className="px-3 py-2 border-r border-slate-200 text-right">{parseFloat(rm.issued_qty).toFixed(2)}</td>
                                                    <td className="px-3 py-2 text-right">{parseFloat(rm.pending_qty).toFixed(2)}</td>
                                                </tr>
                                                {rm.subItems && rm.subItems.map((subItem: any, sIdx: number) => (
                                                    <tr key={`sub-${sIdx}`} className="bg-slate-50/50">
                                                        <td className="px-3 py-2 border-r border-slate-200"></td>
                                                        <td className="px-3 py-2 border-r border-slate-200 uppercase">{subItem.item_name}</td>
                                                        <td className="px-3 py-2 border-r border-slate-200 text-right"></td>
                                                        <td className="px-3 py-2 border-r border-slate-200 text-right">{parseFloat(subItem.issued_qty).toFixed(2)}</td>
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
                                    <td className="px-3 py-2 border-r border-slate-200">{formatDate(po.issuedate)}</td>
                                    <td className="px-3 py-2 border-r border-slate-200 uppercase">{po.item_name}</td>
                                    <td className="px-3 py-2 border-r border-slate-200 text-right">{parseFloat(po.plannedqty).toFixed(2)}</td>
                                    <td className="px-3 py-2 border-r border-slate-200 text-right">{parseFloat(po.prepared_qty).toFixed(2)}</td>
                                    <td className="px-3 py-2 border-r border-slate-200">{formatDate(po.startdate)}</td>
                                    <td className="px-3 py-2 border-r border-slate-200">{formatDate(po.enddate)}</td>
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
                                    <td className="px-3 py-2">{formatDate(ir.inspection_date)}</td>
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
