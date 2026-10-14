'use client';
import { formatQty } from '@/utils/formatters';
import React from 'react';
import { useQuery } from '@tanstack/react-query';
import purchaseOrderService from '../services/purchaseOrder.service';
import { openPurchaseOrderPdf } from '../services/purchaseOrderPdf.service';
import toast from 'react-hot-toast';
import { useLegacyActionAccess } from './ui/useLegacyActionAccess';
import { X, Printer, Loader } from 'lucide-react';
import { formatContractDate } from '../utils/dateFormatter';
import { ReceivingDetailsTable } from './ReceivingDetailsTable';

interface PurchaseOrderDetailsModalProps { id: number | string; isOpen: boolean; onClose: () => void; }
export const PurchaseOrderDetailsModal: React.FC<PurchaseOrderDetailsModalProps> = ({id,isOpen,onClose}) => {
  const can=useLegacyActionAccess();
  const {data,isLoading,isError}=useQuery({queryKey:['purchase-order','details',id],queryFn:()=>purchaseOrderService.getDetails(id),enabled:isOpen && !!id,staleTime:5*60*1000});
  if(!isOpen)return null;
  const canPrint=can('purchaseorder','viewpodetailspdf') || can('purchaseorder','viewpodetail') || can('purchaseorder','index') || can('goodsreceived','index') || can('goodsreceived','grninspection');
  const days=[...new Set(data?.schedules?.map(row=>row.delivery_date) || [])];
  return <div className="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-black/40">
    <div role="dialog" aria-modal="true" aria-labelledby="po-details-title" className="receiving-details bg-white border border-gray-300 w-full max-w-[900px] max-h-[90vh] overflow-y-auto p-3">
      <div className="flex items-center justify-between gap-3 mb-2"><h3 id="po-details-title" className="font-bold">Purchase Order Details</h3><div className="flex gap-2">
        {data && canPrint && <button className="legacy-button" onClick={()=>{void openPurchaseOrderPdf(Number(data.po.id),'details').catch(()=>toast.error('Unable to open purchase order PDF. Please retry and allow popups.'));}}><Printer size={14}/> Print PDF</button>}
        <button aria-label="Close PO details" onClick={onClose}><X size={18}/></button>
      </div></div>
      {isLoading ? <div className="p-8 text-center"><Loader className="animate-spin inline"/> Loading purchase order details...</div> : isError || !data ? <p role="alert">Unable to load purchase order details.</p> : <div className="border border-gray-300">
        <table className="receiving-metadata w-full"><tbody>
          <tr><td><b>Purchase Order No. :-</b> {data.po.po_number}</td><td><b>Amendment No :-</b> {data.po.amendment_no ? `${data.po.amendment_no} (Date : ${formatContractDate(data.po.amendment_date)})` : '---'}</td></tr>
          <tr><td><b>Purchase Order Date :-</b> {formatContractDate(data.po.po_date)}</td><td><b>Delivery Date :-</b> {formatContractDate(data.po.delivery_date)}</td></tr>
          <tr><td><b>GSTIN NO. :-</b> {data.po.gst_number}</td><td><b>Vendor Name :-</b> {data.po.vendor_name}</td></tr>
          <tr><td><b>Status :-</b> {data.po.status}</td><td/></tr>
        </tbody></table>
        <h4>Products</h4><ReceivingDetailsTable items={data.items} purchaseOrder/>
        {!!data.grns?.length && <><h4>Goods Received Note</h4>{data.grns.map(grn=><div key={grn.id} className="mb-3">
          <table className="receiving-metadata w-full"><tbody><tr><td><b>GRN No. :-</b> {grn.id}</td><td><b>Bill No :-</b> {grn.bill_no}</td></tr><tr><td><b>Inward Date :-</b> {formatContractDate(grn.inward_date)}</td><td><b>Bill Date :-</b> {formatContractDate(grn.bill_date)}</td></tr></tbody></table>
          <ReceivingDetailsTable items={grn.items}/>
        </div>)}</>}
        {!!days.length && <><h4>Delivery Schedule</h4><div className="overflow-x-auto p-2"><table className="w-full"><thead><tr><th>Item</th>{days.map(day=><React.Fragment key={day}><th>Date</th><th>Qty</th></React.Fragment>)}</tr></thead><tbody>{data.items.map(item=><tr key={item.id}><td>{item.item_name}</td>{days.map(day=>{const qty=data.schedules!.filter(row=>String(row.item_id)===String(item.item_id) && row.delivery_date===day).reduce((sum,row)=>sum+Number(row.item_qty || 0),0);return <React.Fragment key={day}><td>{qty?formatContractDate(day):''}</td><td>{qty?`${formatQty(qty)} ${item.uom || ''}`:''}</td></React.Fragment>;})}</tr>)}</tbody></table></div></>}
      </div>}
    </div>
  </div>;
};
