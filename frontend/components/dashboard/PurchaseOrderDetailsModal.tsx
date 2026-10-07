"use client";

import React, { useMemo } from 'react';
import { useQuery } from '@tanstack/react-query';
import purchaseOrderService from '../../services/purchaseOrder.service';
import { Loader, X } from 'lucide-react';
import { openPurchaseOrderPdf } from '../../services/purchaseOrderPdf.service';
import toast from 'react-hot-toast';

interface PurchaseOrderDetailsModalProps {
  poId: number;
  onClose: () => void;
}

function formatCurrency(amount: any) {
  const num = parseFloat(amount);
  if (isNaN(num)) return '0.00';
  if (num === Math.floor(num)) {
    return num.toLocaleString('en-IN', { maximumFractionDigits: 0 });
  }
  return num.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

export function PurchaseOrderDetailsModal({ poId, onClose }: PurchaseOrderDetailsModalProps) {
  const { data: details, isLoading } = useQuery({
    queryKey: ['purchase-order-details', poId],
    queryFn: () => purchaseOrderService.getDetails(poId),
    enabled: !!poId,
  });

  const uniqueSchedules = useMemo(() => {
    if (!details?.schedules) return [];
    return Array.from(new Set(details.schedules.map((s: any) => s.delivery_date)));
  }, [details]);

  if (!poId || isLoading || !details) {
    return (
      <div className="fixed inset-0 z-[10000] flex items-center justify-center bg-black/40">
        <Loader className="w-8 h-8 animate-spin text-white" />
      </div>
    );
  }

  const { po, items, grns, schedules } = details;

  let totalamaunt = 0;
  let taxstatus = 'Tax Excluded';

  const checkgrn = grns && grns.length > 0;
  const getDeliverydates = uniqueSchedules.length > 0;

  return (
    <div className="fixed inset-0 z-[10000] flex justify-center items-start overflow-y-auto bg-black/40 pt-10 pb-10 font-sans text-[14px]">
      <div className="bg-white rounded w-[95%] max-w-[1000px] shadow-lg relative p-4 pb-8">
        
        <button onClick={onClose} className="absolute top-2 right-2 text-gray-500 hover:text-gray-800 bg-gray-100 rounded p-1">
          <X className="w-5 h-5" />
        </button>

        <div className="tableContainer" style={{ border: '1px solid #ccc', padding: '15px' }}>
          
          <button type="button"
             onClick={() => { void openPurchaseOrderPdf(po.id).catch(() => toast.error('Unable to open purchase order PDF. Please retry and allow popups.')); }}
             className="float-right bg-[#00a65a] hover:bg-[#008d4c] text-white px-[20px] py-[6px] rounded text-[14px] flex items-center gap-1 mt-2"
             style={{ textDecoration: 'none' }}>
            <i className="fa fa-file-pdf-o"></i> Print
          </button>

          <div className="tableHeader">
            <p style={{ textAlign: 'center', fontSize: '15px', marginBottom: '5px' }}><b>Purchase Order Details</b></p>
            <table className="w-full mb-4">
              <tbody>
                <tr>
                  <td className="py-1"><b>Purchase Order No. :-</b> {po.po_number}</td>
                  <td className="py-1"><b>Amendment No :-</b> {po.amendment_no > 0 ? `${po.amendment_no} (Date : ${new Date(po.amendment_date).toLocaleDateString('en-GB').replace(/\//g, '-')})` : '---'}</td>
                </tr>
                <tr>
                  <td className="py-1"><b>Purchase Order Date :-</b> {new Date(po.po_date).toLocaleDateString('en-GB').replace(/\//g, '-')}</td>
                  <td className="py-1"><b>Delivery Date :-</b> {new Date(po.delivery_date).toLocaleDateString('en-GB').replace(/\//g, '-')}</td>
                </tr>
                <tr>
                  <td className="py-1"><b>GSTIN NO. :-</b> {po.gst_number}</td>
                  <td className="py-1"><b>Vendor Name :-</b> {po.vendor_name}</td>
                </tr>
                <tr>
                  <td className="py-1"><b>Status :-</b> {po.status}</td>
                </tr>
              </tbody>
            </table>
          </div>

          {/* po details */}
          <p style={{ textAlign: 'center', fontSize: '15px', marginBottom: '5px' }}><b>Products</b></p>
          <div className="table-responsive p-[10px]">
            <table className="w-full border-collapse border border-gray-300" cellPadding={3}>
              <thead className="bg-white text-[#333]">
                <tr>
                  <th className="border border-gray-300 w-[4%]">S.No.</th>
                  <th className="border border-gray-300 w-[35%]">Item</th>
                  <th className="border border-gray-300 w-[10%]">Order Qty.</th>
                  <th className="border border-gray-300 w-[10%]">Pending Qty.</th>
                  <th className="border border-gray-300 w-[7%]">Rate</th>
                  <th className="border border-gray-300 w-[10%]">Price (INR)</th>
                  <th className="border border-gray-300 w-[4%]">Tax</th>
                  <th className="border border-gray-300 w-[9%]">Tax Amt</th>
                  <th className="border border-gray-300 w-[10%]">Amount</th>
                </tr>
              </thead>
              <tbody>
                {items.map((item: any, idx: number) => {
                  const s = idx + 1;
                  const qty = checkgrn ? item.pending_qty : item.order_qty;
                  const price = item.order_qty * item.rate;
                  
                  if (price === Number(item.amount)) {
                    taxstatus = 'Tax Included';
                  } else {
                    taxstatus = 'Tax Excluded';
                  }

                  totalamaunt += Number(item.amount);

                  return (
                    <tr key={idx}>
                      <td className="border border-gray-300">{s}.</td>
                      <td className="border border-gray-300 capitalize">{item.item_name}</td>
                      <td className="border border-gray-300">{item.order_qty} {item.uom}</td>
                      <td className="border border-gray-300">{formatCurrency(qty)} {item.uom}</td>
                      <td className="border border-gray-300 text-right">{formatCurrency(item.rate)}</td>
                      <td className="border border-gray-300 text-right">{formatCurrency(price)}</td>
                      <td className="border border-gray-300">{item.tax_percentage}%</td>
                      <td className="border border-gray-300 text-right">{formatCurrency(item.tax_amt)}</td>
                      <td className="border border-gray-300 text-right">{formatCurrency(item.amount)}</td>
                    </tr>
                  )
                })}
                <tr>
                  <td colSpan={5} className="border border-gray-300 text-right"><b>{taxstatus}</b></td>
                  <td colSpan={4} className="border border-gray-300 text-right"><b>Total Amount : </b>{formatCurrency(totalamaunt)}</td>
                </tr>
              </tbody>
            </table>
          </div>

          {/* GRN details */}
          {checkgrn && (
            <>
              <p style={{ textAlign: 'center', fontSize: '15px', marginBottom: '5px' }}><b>Goods Received Note</b></p>
              {grns.map((grn: any, gIdx: number) => {
                let totalamaunt1 = 0;
                return (
                  <div key={gIdx} className="table-responsive p-[10px]">
                    <table className="w-full border-collapse border border-gray-300" cellPadding={3}>
                      <tbody>
                        <tr>
                          <td colSpan={4} className="border border-gray-300"><b>GRN No. :-</b> {grn.id}</td>
                          <td colSpan={5} className="border border-gray-300"><b>Bill No :-</b> {grn.bill_no}</td>
                        </tr>
                        <tr>
                          <td colSpan={4} className="border border-gray-300"><b>Inward Date :-</b> {new Date(grn.inward_date).toLocaleDateString('en-GB').replace(/\//g, '-')}</td>
                          <td colSpan={5} className="border border-gray-300"><b>Bill Date :-</b> {new Date(grn.bill_date).toLocaleDateString('en-GB').replace(/\//g, '-')}</td>
                        </tr>
                        <tr>
                          <th className="border border-gray-300 w-[4%]">S.No.</th>
                          <th className="border border-gray-300 w-[29.12%]">Item</th>
                          <th className="border border-gray-300 w-[9.76%]">Order Qty.</th>
                          <th className="border border-gray-300 w-[11.76%]">Received Qty.</th>
                          <th className="border border-gray-300 w-[9.6%]">Rate</th>
                          <th className="border border-gray-300 w-[11.96%]">Price (INR)</th>
                          <th className="border border-gray-300 w-[4.6%]">Tax</th>
                          <th className="border border-gray-300 w-[9.6%]">Tax Amt</th>
                          <th className="border border-gray-300 w-[9.6%]">Amount</th>
                        </tr>
                        {grn.items.map((gItem: any, iIdx: number) => {
                          const poItem = items.find((i: any) => i.item_id === gItem.item_id);
                          totalamaunt1 += Number(gItem.amount);
                          let gTaxStatus = 'Tax Excluded';
                          if (Number(gItem.price) === Number(gItem.amount)) {
                             gTaxStatus = 'Tax Included';
                          }
                          taxstatus = gTaxStatus;

                          return (
                            <tr key={iIdx}>
                              <td className="border border-gray-300">{iIdx + 1}.</td>
                              <td className="border border-gray-300 capitalize">{gItem.item_name}</td>
                              <td className="border border-gray-300">{poItem?.order_qty} {gItem.uom}</td>
                              <td className="border border-gray-300">{gItem.item_qty} {gItem.uom}</td>
                              <td className="border border-gray-300 text-right">{formatCurrency(gItem.rate)}</td>
                              <td className="border border-gray-300 text-right">{formatCurrency(gItem.price)}</td>
                              <td className="border border-gray-300">{gItem.tax_percentage}%</td>
                              <td className="border border-gray-300 text-right">{formatCurrency(gItem.tax_amt)}</td>
                              <td className="border border-gray-300 text-right">{formatCurrency(gItem.amount)}</td>
                            </tr>
                          )
                        })}
                        <tr>
                          <td colSpan={5} className="border border-gray-300 text-right"><b>{taxstatus}</b></td>
                          <td colSpan={4} className="border border-gray-300 text-right"><b>Total Amount : </b>{formatCurrency(totalamaunt1)}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                )
              })}
            </>
          )}

          {/* Delivery Schedule details */}
          {getDeliverydates && (
            <>
              <p style={{ textAlign: 'center', fontSize: '15px', marginBottom: '5px', marginTop: '10px' }}><b>Delivery Schedule</b></p>
              <div className="table-responsive p-[10px]">
                <table className="w-full border-collapse border border-gray-300" cellPadding={3}>
                  <thead>
                    <tr>
                      <th className="border border-gray-300">Item</th>
                      {uniqueSchedules.map((date: any, i: number) => (
                        <React.Fragment key={i}>
                          <th className="border border-gray-300">Date</th>
                          <th className="border border-gray-300">Qty</th>
                        </React.Fragment>
                      ))}
                    </tr>
                  </thead>
                  <tbody>
                    {items.map((item: any, iIdx: number) => (
                      <tr key={iIdx}>
                        <td className="border border-gray-300 capitalize">{item.item_name}</td>
                        {uniqueSchedules.map((date: any, i: number) => {
                          const scheduleItem = (schedules || []).find((s: any) => s.delivery_date === date && s.item_id === item.item_id);
                          const qty = scheduleItem ? scheduleItem.item_qty : 0;
                          return (
                            <React.Fragment key={i}>
                              <td className="border border-gray-300">{new Date(date).toLocaleDateString('en-GB').replace(/\//g, '-')}</td>
                              <td className="border border-gray-300">{qty} {item.uom}</td>
                            </React.Fragment>
                          )
                        })}
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </>
          )}

        </div>
      </div>
    </div>
  );
}
