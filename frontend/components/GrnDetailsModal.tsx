'use client';
import React from 'react';
import {useQuery} from '@tanstack/react-query';
import {grnService} from '../services/grn.service';
import {grnPdfService} from '../services/grnPdf.service';
import {useLegacyActionAccess} from './ui/useLegacyActionAccess';
import {X,Printer,Loader} from 'lucide-react';
import toast from 'react-hot-toast';
import {formatContractDate} from '../utils/dateFormatter';
import {ReceivingDetailsTable} from './ReceivingDetailsTable';

interface GrnDetailsModalProps {id:number|string;isOpen:boolean;onClose:()=>void;}
export const GrnDetailsModal:React.FC<GrnDetailsModalProps>=({id,isOpen,onClose})=>{
 const can=useLegacyActionAccess();
 const {data,isLoading,isError}=useQuery({queryKey:['grn-details',id],queryFn:()=>grnService.getDetails(String(id)),enabled:isOpen && !!id,staleTime:5*60*1000});
 if(!isOpen)return null;
 const detail=data?.data;
 return <div className="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-black/40"><div role="dialog" aria-modal="true" aria-labelledby="grn-details-title" className="receiving-details bg-white border border-gray-300 w-full max-w-[900px] max-h-[90vh] overflow-y-auto p-3">
  <div className="flex justify-between items-center mb-2"><h3 id="grn-details-title" className="font-bold">GRN Details</h3><div className="flex gap-2">{detail?.grn && (can('goodsreceived','index') || can('goodsreceived','view') || can('goodsreceived','viewgrndetail')) && <button className="legacy-button" onClick={()=>{void grnPdfService.openGrnPdf(id).catch(()=>toast.error('Unable to open GRN PDF. Please retry and allow popups.'));}}><Printer size={14}/> Print PDF</button>}<button aria-label="Close GRN details" onClick={onClose}><X size={18}/></button></div></div>
  {isLoading?<div className="p-8 text-center"><Loader className="inline animate-spin"/> Loading GRN details...</div>:isError || !detail?.grn?<p role="alert">Unable to load GRN details.</p>:<div className="border border-gray-300"><table className="receiving-metadata w-full"><tbody>
   <tr><td><b>GRN No. :-</b> {detail.grn.id}</td><td><b>PO No. :-</b> {detail.grn.purchaseorder_id}</td></tr>
   <tr><td><b>Inward Date :-</b> {formatContractDate(detail.grn.inwarddate)}</td><td><b>Bill Date :-</b> {formatContractDate(detail.grn.bill_date)}</td></tr>
   <tr><td><b>Bill No :-</b> {detail.grn.bill_no}</td><td><b>GSTIN NO. :-</b> {detail.grn.vendor_gstin}</td></tr>
   <tr><td colSpan={2}><b>Vendor Name :-</b> {detail.grn.vendor_name}</td></tr>
  </tbody></table><h4>Received Products</h4><ReceivingDetailsTable items={detail.items || []}/></div>}
 </div></div>;
};
