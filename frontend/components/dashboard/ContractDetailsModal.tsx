"use client";

import React from 'react';
import { useQuery } from '@tanstack/react-query';
import contractService from '../../services/contract.service';
import { Loader, AlertCircle, X, Printer } from 'lucide-react';
import { formatQty, formatAmt } from '@/utils/formatters';
import { formatDate } from '../../utils/dateFormatter';

interface ContractDetailsModalProps {
  contractId: number;
  onClose: () => void;
}

export function ContractDetailsModal({ contractId, onClose }: ContractDetailsModalProps) {
  const { data: details, isLoading, isError } = useQuery({
    queryKey: ['contract-details', contractId],
    queryFn: () => contractService.getDetails(contractId),
    enabled: !!contractId,
    staleTime: 5 * 60 * 1000
  });

  
  return (
    <div className="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm animate-in fade-in duration-200">
      <div className="bg-white border border-slate-200 shadow-2xl rounded-2xl max-w-3xl w-full p-6 flex flex-col relative overflow-hidden animate-in zoom-in-95 duration-200 max-h-[90vh]">
        
        {/* Top Close Control */}
        <div className="absolute top-4 right-4 z-10 flex gap-2">
          {details && (
            <button
              onClick={() => {
                const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:5000/api';
                window.open(`${API_URL}/contracts/${contractId}/pdf?token=${localStorage.getItem('accessToken')}`, '_blank');
              }}
              className="flex items-center gap-1.5 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded font-bold shadow-sm transition cursor-pointer print:hidden text-sm"
            >
              <Printer className="w-4 h-4" />
              Print
            </button>
          )}
          <button
            onClick={onClose}
            className="p-2 text-slate-400 hover:text-slate-700 bg-white border border-slate-200 rounded transition cursor-pointer print:hidden shadow-sm"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        {/* Details Loader / Errors */}
        {isLoading ? (
          <div className="flex-1 flex flex-col items-center justify-center py-20 text-slate-400 gap-2">
            <Loader className="w-8 h-8 animate-spin text-cyan-600" />
            <span className="text-xs font-medium">Loading products data...</span>
          </div>
        ) : isError || !details ? (
          <div className="flex-1 flex flex-col items-center justify-center py-20 text-rose-500 gap-2">
            <AlertCircle className="w-8 h-8 animate-bounce" />
            <span className="text-xs font-medium">Failed to sync contract details.</span>
          </div>
        ) : (
          <div className="flex-1 flex flex-col overflow-y-auto space-y-6 select-text pr-2 print:p-0 print:overflow-visible">
            
            <h2 className="text-center font-bold text-lg text-black mt-2">Contract Details</h2>

            {/* Header Information Grid */}
            <div className="grid grid-cols-2 gap-x-8 gap-y-2 text-xs text-black font-semibold px-4">
              <div>
                <p>Work Order:- {details.contract.workorder}</p>
                <p>Title:- {details.contract.title}</p>
                <p>Contract Start Date:- {formatDate(details.contract.contract_start_date)}</p>
                <p>Supplier Name:- {details.contract.vendor_name}</p>
                <p>Labour Cost:- {formatAmt(details.contract.labour_cost)}</p>
              </div>
              <div>
                <p>Issue Date:- {formatDate(details.contract.issuedate)}</p>
                <p>Contract End Date:- {formatDate(details.contract.contract_end_date)}</p>
                <p>Cost:- {formatAmt(details.contract.cost)}</p>
                <p>Operational Cost:- {formatAmt(details.contract.operation_cost)}</p>
              </div>
            </div>

            <h3 className="text-center font-bold text-sm text-black mt-6">Finished Products</h3>

            {/* Finished Products List */}
            <div className="space-y-8">
              {details.items.map((item: any) => (
                <div key={item.id} className="space-y-2">
                  <div className="flex justify-between items-center text-xs text-black font-bold border-b border-gray-300 pb-1">
                    <span>Product:- {item.item_name}</span>
                    <span>Quantity:- {formatQty(item.quantity)} {item.uom.toUpperCase()}</span>
                    <span>Planned Qty:- {formatQty(item.planned_qty)} {item.uom.toUpperCase()}</span>
                    <span>Prepared Qty:- {formatQty(item.prepared_qty)} {item.uom.toUpperCase()}</span>
                    <span>Price:- {formatAmt(item.price)}</span>
                  </div>
                  
                  <p className="text-center text-xs text-gray-500 font-semibold italic">Production Not Started Yet.</p>
                  <h4 className="text-center font-bold text-xs text-black">Raw Material</h4>
                  
                  <table className="w-full text-left border-collapse text-xs text-black border border-gray-300">
                    <thead>
                      <tr className="border-b border-gray-300 font-bold">
                        <th className="px-2 py-1.5 border-r border-gray-300 w-12">S.No.</th>
                        <th className="px-2 py-1.5 border-r border-gray-300">Item Name</th>
                        <th className="px-2 py-1.5 border-r border-gray-300 text-right w-32">Qty(As per Design)</th>
                        <th className="px-2 py-1.5 border-r border-gray-300 text-right w-24">Issued Qty</th>
                        <th className="px-2 py-1.5 text-right w-24">Pending Qty</th>
                      </tr>
                    </thead>
                    <tbody>
                      {item.raw_materials?.length > 0 ? (
                        item.raw_materials.map((rm: any, idx: number) => (
                          <React.Fragment key={`${rm.id}-${idx}`}>
                            <tr className="border-b border-gray-200">
                              <td className="px-2 py-1.5 border-r border-gray-300">{idx + 1}.</td>
                              <td className="px-2 py-1.5 border-r border-gray-300">{rm.item_name}</td>
                              <td className="px-2 py-1.5 border-r border-gray-300 text-right">{formatQty(rm.as_per_design)}</td>
                              <td className="px-2 py-1.5 border-r border-gray-300 text-right">{formatQty(rm.total_issued)}</td>
                              <td className="px-2 py-1.5 text-right">{formatQty(rm.pending_qty)}</td>
                            </tr>
                            {rm.issued_items?.map((issued: any, iIdx: number) => (
                              <tr key={`${rm.id}-issued-${iIdx}`} className="border-b border-gray-200 text-gray-600 text-[11px]">
                                <td className="px-2 py-1.5 border-r border-gray-300"></td>
                                <td className="px-2 py-1.5 border-r border-gray-300">{issued.item_name}</td>
                                <td className="px-2 py-1.5 border-r border-gray-300 text-right"></td>
                                <td className="px-2 py-1.5 border-r border-gray-300 text-right">{formatQty(issued.issued_qty)}</td>
                                <td className="px-2 py-1.5 text-right"></td>
                              </tr>
                            ))}
                          </React.Fragment>
                        ))
                      ) : (
                        <tr>
                          <td colSpan={5} className="px-2 py-2 text-center text-gray-500">No Raw Materials Found</td>
                        </tr>
                      )}
                    </tbody>
                  </table>
                </div>
              ))}
            </div>

            {/* Production Orders */}
            <div className="mt-8">
              <h3 className="text-center font-bold text-sm text-black mb-2">Production Orders</h3>
              <table className="w-full text-left border-collapse text-xs text-black border border-gray-300">
                <thead>
                  <tr className="border-b border-gray-300 font-bold bg-gray-50">
                    <th className="px-2 py-1.5 border-r border-gray-300">PO No.</th>
                    <th className="px-2 py-1.5 border-r border-gray-300">Issue Date</th>
                    <th className="px-2 py-1.5 border-r border-gray-300">Product</th>
                    <th className="px-2 py-1.5 border-r border-gray-300 text-right">Planned Qty</th>
                    <th className="px-2 py-1.5 border-r border-gray-300 text-right">Prepared Qty</th>
                    <th className="px-2 py-1.5 border-r border-gray-300">Start Date</th>
                    <th className="px-2 py-1.5 border-r border-gray-300">End Date</th>
                    <th className="px-2 py-1.5">Status</th>
                  </tr>
                </thead>
                <tbody>
                  {details.productionOrders?.length > 0 ? (
                    details.productionOrders.map((po: any, idx: number) => (
                      <tr key={po.po_id || idx} className="border-b border-gray-200">
                        <td className="px-2 py-1.5 border-r border-gray-300">{po.po_id}</td>
                        <td className="px-2 py-1.5 border-r border-gray-300">{formatDate(po.issuedate)}</td>
                        <td className="px-2 py-1.5 border-r border-gray-300">{po.product_name}</td>
                        <td className="px-2 py-1.5 border-r border-gray-300 text-right">{formatQty(po.plannedqty)}</td>
                        <td className="px-2 py-1.5 border-r border-gray-300 text-right">{formatQty(po.prepared_qty)}</td>
                        <td className="px-2 py-1.5 border-r border-gray-300">{formatDate(po.startdate)}</td>
                        <td className="px-2 py-1.5 border-r border-gray-300">{formatDate(po.enddate)}</td>
                        <td className="px-2 py-1.5">{po.status === 'O' ? 'Open' : 'Closed'}</td>
                      </tr>
                    ))
                  ) : (
                    <tr>
                      <td colSpan={8} className="px-2 py-2 text-center text-gray-500">No Production Orders Found</td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>

            {/* Inspection Report */}
            <div className="mt-8 mb-4">
              <h3 className="text-center font-bold text-sm text-black mb-2">Inspection Report</h3>
              <table className="w-full text-left border-collapse text-xs text-black border border-gray-300">
                <thead>
                  <tr className="border-b border-gray-300 font-bold bg-gray-50">
                    <th className="px-2 py-1.5 border-r border-gray-300 w-16">S.No</th>
                    <th className="px-2 py-1.5 border-r border-gray-300">Inspector Name</th>
                    <th className="px-2 py-1.5">Inspection Date</th>
                  </tr>
                </thead>
                <tbody>
                  {details.inspectionReports?.length > 0 ? (
                    details.inspectionReports.map((ir: any, idx: number) => (
                      <tr key={ir.s_no || idx} className="border-b border-gray-200">
                        <td className="px-2 py-1.5 border-r border-gray-300">{idx + 1}.</td>
                        <td className="px-2 py-1.5 border-r border-gray-300">{ir.inspector_name}</td>
                        <td className="px-2 py-1.5">{formatDate(ir.inspection_date)}</td>
                      </tr>
                    ))
                  ) : (
                    <tr>
                      <td colSpan={3} className="px-2 py-2 text-center text-gray-500">No Inspection Reports Found</td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>

          </div>
        )}

      </div>
    </div>
  );
}
