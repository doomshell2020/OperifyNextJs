'use client';

import { useEffect, useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { designsheetService } from '@/services/designsheet.service';
import { Loader } from 'lucide-react';
import React from 'react';
import { useParams } from 'next/navigation';

export default function ContractDetailsPrintPage() {
  const { id } = useParams();
  const [mounted, setMounted] = useState(false);
  const contractId = typeof id === 'string' ? id : Array.isArray(id) ? id[0] : null;

  const { data: contractData, isLoading } = useQuery({
    queryKey: ['contract-details', contractId],
    queryFn: () => designsheetService.getContractDetails(contractId!),
    enabled: !!contractId,
    staleTime: 5 * 60 * 1000
  });

  useEffect(() => {
    setMounted(true);
  }, []);

  useEffect(() => {
    if (contractData && !isLoading) {
      setTimeout(() => {
        window.print();
      }, 500);
    }
  }, [contractData, isLoading]);

  if (!mounted) return null;

  const formatDate = (dateString: string) => {
    if (!dateString) return '-';
    const d = new Date(dateString);
    if (isNaN(d.getTime())) return dateString;
    return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }).replace(/ /g, '-');
  };

  if (isLoading) {
    return (
      <div className="flex h-screen w-full items-center justify-center">
        <Loader className="w-8 h-8 animate-spin text-cyan-600" />
      </div>
    );
  }

  if (!contractData || !contractData.contract) {
    return (
      <div className="flex h-screen w-full items-center justify-center text-slate-500">
        Contract details not found.
      </div>
    );
  }

  return (
    <div className="bg-white text-slate-900 w-full mx-auto p-8 max-w-5xl text-sm" style={{ printColorAdjust: 'exact', WebkitPrintColorAdjust: 'exact' }}>
      <h3 className="text-xl font-extrabold text-center mb-6">Contract Details</h3>

      <div className="grid grid-cols-2 gap-y-4 gap-x-8 mb-8 text-sm">
          <div><span className="font-bold">Work Order:- </span>{contractData.contract.workorder}</div>
          <div><span className="font-bold">Title:- </span>{contractData.contract.title}</div>
          <div><span className="font-bold">Issue Date:- </span>{formatDate(contractData.contract.issuedate)}</div>
          <div><span className="font-bold">Contract Start Date:- </span>{formatDate(contractData.contract.contract_start_date)}</div>
          <div><span className="font-bold">Contract End Date:- </span>{formatDate(contractData.contract.contract_end_date)}</div>
          <div><span className="font-bold">Supplier Name:- </span>{contractData.contract.supplier_name}</div>
          <div><span className="font-bold">Cost:- </span>{Number(contractData.contract.cost).toLocaleString()}</div>
          <div><span className="font-bold">Labour Cost:- </span>{contractData.contract.labour_cost}</div>
          <div><span className="font-bold">Operational Cost:- </span>{parseFloat(contractData.contract.operational_cost).toFixed(2)}</div>
      </div>

      <h3 className="text-base font-extrabold text-center mb-6">Finished Products</h3>

      {contractData.finishedProducts.map((fp: any, idx: number) => (
          <div key={idx} className="mb-8">
              <div className="border border-slate-300 overflow-hidden mb-2">
                  <table className="w-full text-left text-xs border-collapse">
                      <thead className="bg-slate-50 border-b border-slate-300 font-bold">
                          <tr>
                              <td className="px-3 py-2 border-r border-slate-300">Product:- {fp.item_name}</td>
                              <td className="px-3 py-2 border-r border-slate-300">Quantity:- {parseFloat(fp.quantity).toFixed(2)} KM</td>
                              <td className="px-3 py-2 border-r border-slate-300">Planned Qty:- {parseFloat(fp.planned_qty).toFixed(2)} KM</td>
                              <td className="px-3 py-2 border-r border-slate-300">Prepared Qty:- {parseFloat(fp.prepared_qty).toFixed(2)} KM</td>
                              <td className="px-3 py-2">Price:- {Number(fp.price).toLocaleString()}</td>
                          </tr>
                      </thead>
                  </table>
              </div>

              {fp.rawMaterials && fp.rawMaterials.length > 0 ? (
                  <div className="border border-slate-300 overflow-hidden mb-4">
                      <table className="w-full text-left text-xs border-collapse">
                          <thead className="bg-slate-50 font-bold text-center border-b border-slate-300">
                              <tr><th colSpan={5} className="py-2">Raw Material</th></tr>
                          </thead>
                          <thead className="bg-white font-bold border-b border-slate-300">
                              <tr>
                                  <th className="px-3 py-2 border-r border-slate-300 w-12">S.No.</th>
                                  <th className="px-3 py-2 border-r border-slate-300">Item Name</th>
                                  <th className="px-3 py-2 border-r border-slate-300 text-right w-32">Qty(As per Design)</th>
                                  <th className="px-3 py-2 border-r border-slate-300 text-right w-32">Issued Qty</th>
                                  <th className="px-3 py-2 text-right w-32">Pending Qty</th>
                              </tr>
                          </thead>
                          <tbody className="divide-y divide-slate-200">
                              {fp.rawMaterials.map((rm: any, rIdx: number) => (
                                  <React.Fragment key={rIdx}>
                                      <tr>
                                          <td className="px-3 py-2 border-r border-slate-300">{rIdx + 1}.</td>
                                          <td className="px-3 py-2 border-r border-slate-300 uppercase">{rm.display_name}</td>
                                          <td className="px-3 py-2 border-r border-slate-300 text-right">{parseFloat(rm.qty_as_per_design).toFixed(2)}</td>
                                          <td className="px-3 py-2 border-r border-slate-300 text-right">{parseFloat(rm.issued_qty).toFixed(2)}</td>
                                          <td className="px-3 py-2 text-right">{parseFloat(rm.pending_qty).toFixed(2)}</td>
                                      </tr>
                                      {rm.subItems && rm.subItems.map((subItem: any, sIdx: number) => (
                                          <tr key={`sub-${sIdx}`}>
                                              <td className="px-3 py-2 border-r border-slate-300"></td>
                                              <td className="px-3 py-2 border-r border-slate-300 uppercase">{subItem.item_name}</td>
                                              <td className="px-3 py-2 border-r border-slate-300 text-right"></td>
                                              <td className="px-3 py-2 border-r border-slate-300 text-right">{parseFloat(subItem.issued_qty).toFixed(2)}</td>
                                              <td className="px-3 py-2 text-right"></td>
                                          </tr>
                                      ))}
                                  </React.Fragment>
                              ))}
                          </tbody>
                      </table>
                  </div>
              ) : (
                  <div className="text-center py-2 text-sm border border-slate-300 font-medium mb-4">Production Not Started Yet.</div>
              )}
          </div>
      ))}

      <h3 className="text-base font-extrabold text-center mb-4 mt-6">Production Orders</h3>
      <div className="border border-slate-300 overflow-hidden mb-8">
          <table className="w-full text-left text-xs border-collapse">
              <thead className="bg-slate-50 font-bold border-b border-slate-300">
                  <tr>
                      <th className="px-3 py-2 border-r border-slate-300">PO No.</th>
                      <th className="px-3 py-2 border-r border-slate-300">Issue Date</th>
                      <th className="px-3 py-2 border-r border-slate-300">Product</th>
                      <th className="px-3 py-2 border-r border-slate-300 text-right">Planned Qty</th>
                      <th className="px-3 py-2 border-r border-slate-300 text-right">Prepared Qty</th>
                      <th className="px-3 py-2 border-r border-slate-300">Start Date</th>
                      <th className="px-3 py-2 border-r border-slate-300">End Date</th>
                      <th className="px-3 py-2">Status</th>
                  </tr>
              </thead>
              <tbody className="divide-y divide-slate-200">
                  {contractData.productionOrders.length > 0 ? contractData.productionOrders.map((po: any, pIdx: number) => (
                      <tr key={pIdx}>
                          <td className="px-3 py-2 border-r border-slate-300">{po.po_id}</td>
                          <td className="px-3 py-2 border-r border-slate-300">{formatDate(po.issuedate)}</td>
                          <td className="px-3 py-2 border-r border-slate-300 uppercase">{po.item_name}</td>
                          <td className="px-3 py-2 border-r border-slate-300 text-right">{parseFloat(po.plannedqty).toFixed(2)}</td>
                          <td className="px-3 py-2 border-r border-slate-300 text-right">{parseFloat(po.prepared_qty).toFixed(2)}</td>
                          <td className="px-3 py-2 border-r border-slate-300">{formatDate(po.startdate)}</td>
                          <td className="px-3 py-2 border-r border-slate-300">{formatDate(po.enddate)}</td>
                          <td className="px-3 py-2">{po.status === 'C' ? 'Close' : 'Open'}</td>
                      </tr>
                  )) : (
                      <tr>
                          <td colSpan={8} className="px-3 py-4 text-center font-medium">No Production Orders Found</td>
                      </tr>
                  )}
              </tbody>
          </table>
      </div>

      <h3 className="text-base font-extrabold text-center mb-4">Inspection Report</h3>
      <div className="border border-slate-300 overflow-hidden mb-6">
          <table className="w-full text-left text-xs border-collapse">
              <thead className="bg-slate-50 font-bold border-b border-slate-300">
                  <tr>
                      <th className="px-3 py-2 border-r border-slate-300 w-16">S.No</th>
                      <th className="px-3 py-2 border-r border-slate-300">Inspector Name</th>
                      <th className="px-3 py-2">Inspection Date</th>
                  </tr>
              </thead>
              <tbody className="divide-y divide-slate-200">
                  {contractData.inspectionReports.length > 0 ? contractData.inspectionReports.map((ir: any, irIdx: number) => (
                      <tr key={irIdx}>
                          <td className="px-3 py-2 border-r border-slate-300">{irIdx + 1}.</td>
                          <td className="px-3 py-2 border-r border-slate-300 uppercase">{ir.name}</td>
                          <td className="px-3 py-2">{formatDate(ir.inspection_date)}</td>
                      </tr>
                  )) : (
                      <tr>
                          <td colSpan={3} className="px-3 py-4 text-center font-medium">No Inspection Reports Found</td>
                      </tr>
                  )}
              </tbody>
          </table>
      </div>
    </div>
  );
}
