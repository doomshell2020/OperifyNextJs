import React from 'react';
import { API_URL } from '../services/apiClient';

interface PurchaseOrderDetailsPrintProps {
  data: any;
  standalone?: boolean;
}

const formatDate = (dateStr?: string) => {
  if (!dateStr) return '';
  return new Date(dateStr).toLocaleDateString('en-IN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  }).replace(/\//g, '-');
};

export const PurchaseOrderDetailsPrint: React.FC<PurchaseOrderDetailsPrintProps> = ({ data, standalone }) => {
  if (!data) return null;

  const baseUrl = API_URL.replace('/api', '');

  return (
    <div className={`${standalone ? 'block' : 'hidden print:block'} w-full text-black bg-white text-xs`} style={{ fontFamily: 'Arial, sans-serif' }}>
      
      {/* Header Table */}
      <table className="w-full border-collapse border border-black mb-4">
        <tbody>
          <tr>
            <td className="p-3 border-r border-black w-1/2 align-bottom">
              {data.site_details?.logo ? (
                <img 
                  src={`${baseUrl}/public/uploads/logos/${data.site_details.logo}`} 
                  alt="Logo" 
                  className="h-14 object-contain mb-4 ml-6"
                  onError={(e) => { (e.target as HTMLImageElement).src = `${baseUrl}/public/uploads/logos/tirupati_tppl_logo.png`; }}
                />
              ) : null}
              <div className="font-bold text-sm uppercase">
                {data.site_details?.company_name || 'TIRUPATI PLASTOMATICS PVT. LTD.'}
              </div>
            </td>
            <td className="p-2 text-right text-[11px] align-top w-1/2">
              <div>{data.site_details?.address1 || 'B-141(A), Rd Number 9D, Vishwakarma Industrial Area, Jaipur,'}</div>
              <div>{data.site_details?.address2 || 'Rajasthan 302013'}</div>
              <div className="font-bold">Phone : {data.site_details?.phone || '9829287189'}</div>
              <div><span className="font-bold">Email : </span> <u>{data.site_details?.email || 'contact@tirupatiplastomatics.com'}</u></div>
              <div><span className="font-bold">Website : </span> <u>{data.site_details?.website || 'www.tirupatiplastomatics.com'}</u></div>
            </td>
          </tr>
          <tr>
            <td colSpan={2} className="p-1 font-bold text-sm border-t border-black text-center bg-gray-50 print:bg-transparent">
              Purchase Order Details
            </td>
          </tr>
          <tr>
            <td colSpan={2} className="p-0 border-t border-black">
              <table className="w-full text-[10px] font-bold">
                <tbody>
                  <tr>
                    <td className="p-1.5 border-b border-black w-1/2">Purchase Order No. :- {data.po.po_number}</td>
                    <td className="p-1.5 border-b border-black border-l w-1/2">Amendment No :- {data.po.amendment_no || '0'} (Date : {formatDate(data.po.amendment_date)})</td>
                  </tr>
                  <tr>
                    <td className="p-1.5 border-b border-black">Purchase Order Date :- {formatDate(data.po.po_date)}</td>
                    <td className="p-1.5 border-b border-black border-l">Delivery Date :- {formatDate(data.po.delivery_date)}</td>
                  </tr>
                  <tr>
                    <td className="p-1.5 border-b border-black">GSTIN NO. :- {data.po.gst_number || 'NA'}</td>
                    <td className="p-1.5 border-b border-black border-l">Vendor Name :- {data.po.vendor_name}</td>
                  </tr>
                  <tr>
                    <td colSpan={2} className="p-1.5 border-b border-black">Status :- {data.po.status}</td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
        </tbody>
      </table>

      {/* Products Section */}
      <div className="text-center font-bold text-sm mb-1 mt-4">Products</div>
      <table className="w-full border-collapse border border-black text-[10px] mb-6">
        <thead>
          <tr className="bg-gray-50 print:bg-transparent font-bold">
            <td className="border border-black p-1 text-center">No.</td>
            <td className="border border-black p-1">Item</td>
            <td className="border border-black p-1 text-right">Order Qty</td>
            <td className="border border-black p-1 text-right">Pending Qty</td>
            <td className="border border-black p-1 text-right">Rate.</td>
            <td className="border border-black p-1 text-right">Price (INR)</td>
            <td className="border border-black p-1 text-center">Tax</td>
            <td className="border border-black p-1 text-right">Tax Amt</td>
            <td className="border border-black p-1 text-right">Amount</td>
          </tr>
        </thead>
        <tbody>
          {data.items?.map((item: any, idx: number) => (
            <tr key={idx}>
              <td className="border border-black p-1 text-center">{idx + 1}</td>
              <td className="border border-black p-1">{item.item_name}</td>
              <td className="border border-black p-1 text-right">{Number(item.order_qty)} {item.uom}</td>
              <td className="border border-black p-1 text-right">{Number(item.pending_qty)} {item.uom}</td>
              <td className="border border-black p-1 text-right">{Number(item.rate)}</td>
              <td className="border border-black p-1 text-right">{Number(item.price).toLocaleString('en-IN')}</td>
              <td className="border border-black p-1 text-center">{item.tax_percentage}%</td>
              <td className="border border-black p-1 text-right">{Number(item.tax_amt).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
              <td className="border border-black p-1 text-right">{Number(item.amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
            </tr>
          ))}
          <tr>
            <td colSpan={8} className="border border-black p-1 font-bold text-right">Total Amount :</td>
            <td className="border border-black p-1 font-bold text-right">{Number(data.po.total_amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
          </tr>
        </tbody>
      </table>

      {/* Goods Received Note Section */}
      {data.grns && data.grns.length > 0 && (
        <>
          <div className="text-center font-bold text-sm mb-1 mt-6">Goods Received Note</div>
          {data.grns.map((grn: any, idx: number) => (
            <table key={idx} className="w-full border-collapse border border-black text-[10px] mb-4 break-inside-avoid">
              <thead>
                <tr className="font-bold">
                  <td colSpan={5} className="border border-black p-1">GRN No. :- {grn.grn_number || grn.id}</td>
                  <td colSpan={4} className="border border-black p-1">Bill No :- {grn.bill_no}</td>
                </tr>
                <tr className="font-bold">
                  <td colSpan={5} className="border border-black p-1">Inward Date :- {formatDate(grn.inward_date)}</td>
                  <td colSpan={4} className="border border-black p-1">Bill Date :- {formatDate(grn.bill_date)}</td>
                </tr>
                <tr className="bg-gray-50 print:bg-transparent font-bold">
                  <td className="border border-black p-1 text-center">No.</td>
                  <td className="border border-black p-1">Item</td>
                  <td className="border border-black p-1 text-right">Order Qty</td>
                  <td className="border border-black p-1 text-right">Received Qty</td>
                  <td className="border border-black p-1 text-right">Rate</td>
                  <td className="border border-black p-1 text-right">Price (INR)</td>
                  <td className="border border-black p-1 text-center">Tax</td>
                  <td className="border border-black p-1 text-right">Tax Amt</td>
                  <td className="border border-black p-1 text-right">Amount</td>
                </tr>
              </thead>
              <tbody>
                {grn.items?.map((item: any, i: number) => {
                  const poItem = data.items.find((pi: any) => pi.item_id === item.item_id);
                  const orderQty = poItem ? Number(poItem.order_qty) : 0;
                  
                  return (
                    <tr key={i}>
                      <td className="border border-black p-1 text-center">{i + 1}</td>
                      <td className="border border-black p-1">{item.item_name}</td>
                      <td className="border border-black p-1 text-right">{orderQty} {item.uom}</td>
                      <td className="border border-black p-1 text-right">{Number(item.item_qty)} {item.uom}.</td>
                      <td className="border border-black p-1 text-right">{Number(item.price)}</td>
                      <td className="border border-black p-1 text-right">{Number(item.rate).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                      <td className="border border-black p-1 text-center">{item.tax_percentage}%</td>
                      <td className="border border-black p-1 text-right">{Number(item.tax_amt).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                      <td className="border border-black p-1 text-right">{Number(item.amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                    </tr>
                  );
                })}
                <tr>
                  <td colSpan={8} className="border border-black p-1 font-bold text-right">Total Amount :</td>
                  <td className="border border-black p-1 font-bold text-right">{Number(grn.total_amt).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                </tr>
              </tbody>
            </table>
          ))}
        </>
      )}

    </div>
  );
};
