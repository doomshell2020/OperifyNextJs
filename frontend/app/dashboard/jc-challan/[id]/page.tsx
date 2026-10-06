'use client';

import React, { useRef, useEffect } from 'react';
import { openModulePdf } from '@/services/pdf.service';
import { useQuery } from '@tanstack/react-query';
import apiClient from '../../../../services/apiClient';
import { useParams, useRouter, useSearchParams } from 'next/navigation';
import { ArrowLeft, Printer, Loader, AlertCircle } from 'lucide-react';
import Link from 'next/link';
import { useAuth } from '@/contexts/AuthContext';
import { useJcAccess } from '@/components/jobChallan/useJcAccess';

export default function ViewJobChallan() {
  const params       = useParams();
  const router       = useRouter();
  const searchParams = useSearchParams();
  const printRef     = useRef<HTMLDivElement>(null);
  const { user } = useAuth();
  const { can, loading: permissionsLoading, permissionError, retryPermissions } = useJcAccess();
  const senderDb = searchParams.get('sender_db');
  const senderQuery = senderDb ? '?sender_db=' + encodeURIComponent(senderDb) : '';

  const { data, isLoading, isError } = useQuery({
    queryKey: ['jobChallan', user?.db, params.id, senderDb],
    enabled: can('jobchallan','view'),
    queryFn: async () => {
      const res = await apiClient.get('/job-challan/' + params.id + senderQuery);
      return res.data.data;
    }
  });

  const handlePrint = () => { void openModulePdf(`/job-challan/${encodeURIComponent(String(params.id))}/pdf${senderQuery}`); };
  useEffect(() => {
    if (searchParams.get('pdf') === '1') {
      window.location.replace(`/dashboard/jc-challan/${encodeURIComponent(String(params.id))}/pdf${senderQuery}`);
    }
  }, [params.id, searchParams, senderQuery]);

  if (permissionsLoading) return <p className="p-6">Loading permissions...</p>;
  if (permissionError) return <div className="p-6 space-y-3" role="alert"><p>Unable to check JC access. Please try again.</p><button className="text-cyan-700 underline" onClick={() => void retryPermissions()}>Try again</button></div>;
  if (!can('jobchallan','view')) return <p className="p-6" role="alert">You do not have permission to view this JC.</p>;

  if (isLoading) return (
    <div className="flex flex-col items-center justify-center min-h-[60vh] gap-3">
      <Loader className="w-10 h-10 animate-spin text-cyan-600" />
      <span className="text-slate-500">Loading...</span>
    </div>
  );

  if (isError || !data) return (
    <div className="flex flex-col items-center justify-center min-h-[60vh] gap-3 text-red-500">
      <AlertCircle className="w-10 h-10" />
      <span>Job Challan not found.</span>
      <button onClick={() => router.back()} className="mt-2 text-sm text-slate-600 underline">Go Back</button>
    </div>
  );

  const challanData = data?.challan || {};
  const siteDetails = data?.site_details || {};

  const rawItems = (challanData.job_challan_items || []).filter((i: any) => i.return_type !== 'Semi-Finished Product');
  const sfpItem  = (challanData.job_challan_items || []).find((i: any) => i.return_type === 'Semi-Finished Product');

  return (
    <>
      {/* Print-only styling */}
      <style>{`@media print { .no-print { display: none !important; } body { font-size: 12px; } }`}</style>

      <main className="max-w-4xl mx-auto px-6 py-8 font-sans">
        {/* Top Bar */}
        <div className="no-print flex justify-between items-center mb-6">
          <button onClick={() => router.back()} className="flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-md text-sm font-medium transition">
            <ArrowLeft className="w-4 h-4" /> Back
          </button>
          {can('jobchallan','viewpdf') && <button onClick={handlePrint} className="flex items-center gap-1.5 px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-md text-sm font-medium transition shadow-sm">
            <Printer className="w-4 h-4" /> Print / PDF
          </button>}
        </div>

        {/* Printable Content */}
        <div ref={printRef} className="bg-white border border-slate-200 rounded-xl p-8 shadow-sm">
          {/* Title */}
          <div className="text-center mb-6">
            <h2 className="text-xl font-bold text-slate-800 uppercase">{siteDetails.company_name || 'COMPANY NAME'}</h2>
            <p className="text-slate-600 text-xs mt-1">{siteDetails.address || siteDetails.address1 || 'Address'}</p>
            <p className="text-slate-600 text-xs mb-3">GSTIN: <span className="font-semibold">{siteDetails.gst_no || 'N/A'}</span></p>
            <h3 className="text-lg font-extrabold text-slate-800 uppercase tracking-widest border-t border-slate-200 pt-3">Job Challan</h3>
            <p className="text-slate-400 text-sm mt-1">Dispatch Challan</p>
          </div>

          {/* Header Info */}
          <div className="grid grid-cols-2 gap-x-8 gap-y-2 mb-6 text-sm">
            <div className="flex justify-between border-b border-slate-100 py-1.5">
              <span className="text-slate-500 font-semibold">JC No.</span>
              <span className="font-bold text-slate-800">{challanData.challan_no}</span>
            </div>
            <div className="flex justify-between border-b border-slate-100 py-1.5">
              <span className="text-slate-500 font-semibold">Date</span>
              <span className="font-bold text-slate-800">{challanData.jc_date}</span>
            </div>
            <div className="flex justify-between border-b border-slate-100 py-1.5">
              <span className="text-slate-500 font-semibold">Sub Contractor</span>
              <span className="font-bold text-slate-800">{challanData.vendor?.name || '—'}</span>
            </div>
            <div className="flex justify-between border-b border-slate-100 py-1.5">
              <span className="text-slate-500 font-semibold">GST No.</span>
              <span className="text-slate-800">{challanData.gst_no || '—'}</span>
            </div>
            <div className="flex justify-between border-b border-slate-100 py-1.5">
              <span className="text-slate-500 font-semibold">Process Type</span>
              <span className="text-slate-800">{challanData.processing_type}</span>
            </div>
            <div className="flex justify-between border-b border-slate-100 py-1.5">
              <span className="text-slate-500 font-semibold">Status</span>
              <span className="font-semibold">{challanData.status}</span>
            </div>
            {challanData.estimated_values > 0 && (
              <div className="flex justify-between border-b border-slate-100 py-1.5">
                <span className="text-slate-500 font-semibold">Estimated Value</span>
                <span className="text-slate-800">₹{Number(challanData.estimated_values).toFixed(2)}</span>
              </div>
            )}
            {challanData.expected_days > 0 && (
              <div className="flex justify-between border-b border-slate-100 py-1.5">
                <span className="text-slate-500 font-semibold">Expected Days</span>
                <span className="text-slate-800">{challanData.expected_days} Day{challanData.expected_days > 1 ? 's' : ''}</span>
              </div>
            )}
            {challanData.work_description && (
              <div className="col-span-2 flex justify-between border-b border-slate-100 py-1.5">
                <span className="text-slate-500 font-semibold">Work Description</span>
                <span className="text-slate-800 text-right max-w-xs">{challanData.work_description}</span>
              </div>
            )}
          </div>

          {/* Item Table */}
          {rawItems.length > 0 && (
            <>
              <h4 className="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 mt-4">Items Dispatched</h4>
              <table className="w-full text-sm border-collapse border border-slate-200 mb-6">
                <thead>
                  <tr className="bg-slate-800 text-white text-xs uppercase">
                    <th className="p-2 text-left font-semibold">#</th>
                    <th className="p-2 text-left font-semibold">Item Name</th>
                    <th className="p-2 text-center font-semibold">HSN/SAC</th>
                    <th className="p-2 text-center font-semibold">Qty</th>
                    <th className="p-2 text-right font-semibold">Rate</th>
                    <th className="p-2 text-right font-semibold">Tax %</th>
                    <th className="p-2 text-right font-semibold">Tax Amt</th>
                    <th className="p-2 text-right font-semibold">Total</th>
                  </tr>
                </thead>
                <tbody>
                  {rawItems.map((item: any, idx: number) => (
                    <tr key={item.id} className="border-b border-slate-100 hover:bg-slate-50">
                      <td className="p-2 text-slate-500">{idx + 1}</td>
                      <td className="p-2 font-medium">{item.item?.item_name || `Item #${item.item_id}`}</td>
                      <td className="p-2 text-center text-slate-500">{item.hsn_code || '—'}</td>
                      <td className="p-2 text-center">{item.quantity}</td>
                      <td className="p-2 text-right">{Number(item.rate).toFixed(2)}</td>
                      <td className="p-2 text-right">{item.tax_rate}%</td>
                      <td className="p-2 text-right">{Number(item.tax_amount).toFixed(2)}</td>
                      <td className="p-2 text-right font-semibold">{Number(item.total).toFixed(2)}</td>
                    </tr>
                  ))}
                </tbody>
                <tfoot>
                  <tr className="bg-slate-50 border-t-2 border-slate-300 font-bold">
                    <td colSpan={5} className="p-2 text-right text-xs uppercase text-slate-500">Totals</td>
                    <td></td>
                    <td className="p-2 text-right">{Number(challanData.gst_amount).toFixed(2)}</td>
                    <td className="p-2 text-right text-cyan-700">₹{Number(challanData.final_amount).toFixed(2)}</td>
                  </tr>
                </tfoot>
              </table>
            </>
          )}

          {/* Semi-Finished Product */}
          {sfpItem && (
            <div className="border border-pink-200 rounded-lg p-4 mb-6 bg-pink-50">
              <h4 className="text-xs font-bold text-pink-700 uppercase tracking-wider mb-2">Semi-Finished Product</h4>
              <div className="flex gap-8 text-sm">
                <div>
                  <span className="text-slate-500">Product: </span>
                  <span className="font-semibold">{sfpItem.item?.item_name || `Item #${sfpItem.item_id}`}</span>
                </div>
                <div>
                  <span className="text-slate-500">Quantity: </span>
                  <span className="font-semibold">{sfpItem.quantity}</span>
                </div>
              </div>
            </div>
          )}

          {/* Signature row */}
          <section className="mt-6 space-y-3">
            <div className="flex justify-between items-center"><h4 className="font-semibold">Quantity Tracking</h4>
              {!senderDb && can('jobchallan','itemreceived') && !['Completed','Cancelled','Deleted'].includes(challanData.status) && <Link className="text-cyan-700 underline" href={`/dashboard/jc-challan/${params.id}/receive`}>Receive Returned Items</Link>}
            </div>
            <table className="w-full text-sm"><thead><tr>{['Item','Dispatch','Received','Pending','Consumed at Subcontractor','Subcontractor Balance'].map(h => <th key={h} className="p-2 text-left">{h}</th>)}</tr></thead><tbody>
              {Array.from(new Set<number>((challanData.job_challan_items || []).map((item: {item_id:number}) => Number(item.item_id)))).map(itemId => {
                const rows = challanData.job_challan_items.filter((item: {item_id:number}) => Number(item.item_id) === itemId);
                return <tr className="border-t" key={itemId}><td className="p-2">{rows[0].item?.item_name}</td><td className="p-2">{rows.reduce((sum:number,item:{quantity:number}) => sum + Number(item.quantity),0)}</td><td className="p-2">{rows[0].received_qty}</td><td className="p-2">{rows[0].pending_qty}</td><td className="p-2">{data.tracking?.[itemId]?.consumed ?? '-'}</td><td className="p-2">{data.tracking?.[itemId]?.balance ?? '-'}</td></tr>;
              })}
            </tbody></table>
            <h4 className="font-semibold">Receive History</h4>
            <table className="w-full text-sm"><thead><tr>{['Date','Item','Received Quantity','Vehicle','Remarks','Return Challan'].map(h => <th key={h} className="p-2 text-left">{h}</th>)}</tr></thead><tbody>{(data.history || []).map((row: {id:number;receive_date:string;item_name:string;received_qty:number;vehicle_no:string;remarks:string}) => <tr className="border-t" key={row.id}><td className="p-2">{row.receive_date}</td><td className="p-2">{row.item_name}</td><td className="p-2">{row.received_qty}</td><td className="p-2">{row.vehicle_no}</td><td className="p-2">{row.remarks}</td><td className="p-2">{can('jobchallan','viewreturnpdf') && <Link className="text-cyan-700" href={`/dashboard/jc-receive/${row.id}/pdf${senderQuery}`}>RC-{row.id} PDF</Link>}</td></tr>)}</tbody></table>
            {!data.history?.length && <p className="text-sm text-slate-500">No items received yet.</p>}
          </section>
          <div className="grid grid-cols-3 gap-8 mt-12 text-xs text-slate-400 text-center">
            <div className="border-t border-slate-300 pt-2">Prepared By</div>
            <div className="border-t border-slate-300 pt-2">Authorized By</div>
            <div className="border-t border-slate-300 pt-2">Received By</div>
          </div>
        </div>
      </main>
    </>
  );
}
