"use client";

import React from 'react';
import { useQuery } from '@tanstack/react-query';
import purchaseOrderService from '../../services/purchaseOrder.service';
import { Loader, X, Printer } from 'lucide-react';
import { API_URL } from '../../services/apiClient';

interface PrintPurchaseOrderProps {
  poId: number;
  onClose: () => void;
}

function numberToWords(num: number): string {
  if (num === 0) return 'Zero Rupees Only';
  const a = ['','One ','Two ','Three ','Four ', 'Five ','Six ','Seven ','Eight ','Nine ','Ten ','Eleven ','Twelve ','Thirteen ','Fourteen ','Fifteen ','Sixteen ','Seventeen ','Eighteen ','Nineteen '];
  const b = ['', '', 'Twenty','Thirty','Forty','Fifty', 'Sixty','Seventy','Eighty','Ninety'];

  const numStr = Math.floor(num).toString();
  if (numStr.length > 9) return 'Overflow';
  const n = ('000000000' + numStr).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{1})(\d{2})$/);
  if (!n) return '';
  let str = '';
  str += (n[1] !== '00') ? (a[Number(n[1])] || b[Number(n[1][0])] + ' ' + a[Number(n[1][1])]) + 'Crore ' : '';
  str += (n[2] !== '00') ? (a[Number(n[2])] || b[Number(n[2][0])] + ' ' + a[Number(n[2][1])]) + 'Lakh ' : '';
  str += (n[3] !== '00') ? (a[Number(n[3])] || b[Number(n[3][0])] + ' ' + a[Number(n[3][1])]) + 'Thousand ' : '';
  str += (n[4] !== '0') ? (a[Number(n[4])] || b[Number(n[4][0])] + ' ' + a[Number(n[4][1])]) + 'Hundred ' : '';
  str += (n[5] !== '00') ? ((str !== '') ? '' : '') + (a[Number(n[5])] || b[Number(n[5][0])] + ' ' + a[Number(n[5][1])]) + 'Rupees ' : 'Rupees ';
  return (str.trim() + ' Only');
}

function formatCurrency(amount: any) {
  const val = parseFloat(amount || '0');
  if (Math.floor(val) === val) {
    return val.toLocaleString('en-IN', { maximumFractionDigits: 0 });
  }
  return val.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

export function PrintPurchaseOrder({ poId, onClose }: PrintPurchaseOrderProps) {
  const { data, isLoading } = useQuery({
    queryKey: ['purchase-order-details', poId],
    queryFn: () => purchaseOrderService.getDetails(poId),
    enabled: !!poId,
  });

  if (!poId) return null;

  const baseUrl = API_URL.replace('/api', '');

  return (
    <div className="bg-white w-full print:w-full print:max-w-none custom-scrollbar mx-auto">
        {isLoading ? (
          <div className="flex justify-center items-center py-20 print:hidden">
            <Loader className="w-8 h-8 animate-spin text-blue-500" />
          </div>
        ) : data ? (
          <div className="print-content text-black font-sans bg-white" style={{ fontFamily: 'Arial, Helvetica, sans-serif', padding: '20px' }}>
            
            {/* PAGE 1: Purchase Order */}
            <div style={{ border: '1px solid #000', marginBottom: '20px' }} className="print:break-after-page print:mb-0">
              <table width="100%" cellPadding="0" cellSpacing="0">
                <tbody>
                  <tr>
                    <td>
                      {/* Header Start */}
                      <table width="100%" style={{ padding: '1px 1px 0px 0px' }} align="left" cellPadding="0" cellSpacing="0">
                        <tbody>
                          <tr>
                            <td style={{ textAlign: 'left' }} width="50%">
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                              <img src={data.site_details?.logo ? `${baseUrl}/public/uploads/logos/${data.site_details.logo}` : ''} alt="LOGO" style={{ display: 'block', height: '62px', maxWidth: '150px', objectFit: 'contain' }} onError={(e) => { (e.target as HTMLImageElement).src = `${baseUrl}/public/uploads/logos/tirupati_tppl_logo.png`; }} /><br />
                              <span style={{ display: 'block', color: '#000', fontSize: '10px' }}>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>{data.site_details?.company_name || 'TIRUPATI PLASTOMATICS PVT. LTD.'}</b></span>
                            </td>
                            <td style={{ textAlign: 'right', fontSize: '10px' }} width="50%">
                              {data.site_details?.address1 || ''},<br /> {data.site_details?.address2 || ''}<br />
                              <b>Phone</b>: {data.site_details?.phone || ''}<br />
                              &nbsp;&nbsp;&nbsp;&nbsp;<b>Email</b>: <u>{data.site_details?.email || ''}</u><br />
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Website</b> :&nbsp;{data.site_details?.website || ''}
                            </td>
                          </tr>
                        </tbody>
                      </table>
                      <br /><hr style={{ borderColor: '#000', margin: 0 }} />

                      <table width="100%" cellPadding="0" cellSpacing="0">
                        <tbody>
                          <tr>
                            <td width="100%" style={{ height: '22px', lineHeight: '22px', color: '#000', textAlign: 'center', borderTop: '1px solid #000', borderBottom: '1px solid #000', fontSize: '16px', fontWeight: 'bold' }}>Purchase Order</td>
                          </tr>
                        </tbody>
                      </table>

                      <table width="100%" cellPadding="0" cellSpacing="0">
                        <tbody>
                          <tr>
                            <td width="50%" style={{ borderRight: '1px solid #000', verticalAlign: 'top' }}>
                              <table width="100%" cellPadding="0" cellSpacing="0">
                                <tbody>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>TO</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>
                                      <strong style={{ fontWeight: 'bold', fontSize: '10px', textAlign: 'left' }}>{data.po.vendor_name}</strong><br />
                                      {data.po.vendor_address !== 'N/A' && <>{data.po.vendor_address.split('\n').map((line: string, i: number) => <React.Fragment key={i}>{line}<br/></React.Fragment>)}</>}
                                    </td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>GST No.</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>{data.po.gst_number}</td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>State</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>Rajasthan</td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>Phone No.</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>{data.po.vendor_phone}</td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>Email</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>{data.po.vendor_email}</td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                </tbody>
                              </table>
                            </td>

                            <td width="50%" style={{ verticalAlign: 'top' }}>
                              <table width="100%" cellPadding="0" cellSpacing="0">
                                <tbody>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>Purchase Order No.</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>{data.po.po_number}</td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>Purchase Order Date</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>{data.po.po_date ? data.po.po_date.split('T')[0].split('-').reverse().join('-') : '-'}</td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>Delivery Date</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>{data.po.delivery_date ? data.po.delivery_date.split('T')[0].split('-').reverse().join('-') : '-'}</td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>Amendment No</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>
                                      {data.po.amendment_no > 0 ? (
                                        <>{data.po.amendment_no}&nbsp;(<b>Date : </b>{data.po.amendment_date ? data.po.amendment_date.split('T')[0].split('-').reverse().join('-') : ''} )</>
                                      ) : '---'}
                                    </td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                </tbody>
                              </table>
                            </td>
                          </tr>
                        </tbody>
                      </table>

                      <table width="100%" cellPadding="0" cellSpacing="0">
                        <tbody>
                          <tr>
                            <td width="100%" style={{ textAlign: 'center', fontSize: '10px', color: '#000', height: '20px', lineHeight: '20px', borderTop: '1px solid #000', borderBottom: '1px solid #000' }}>
                              Please Supply the undermentioned materials and send us your acceptance per return post.
                            </td>
                          </tr>
                        </tbody>
                      </table>

                      <table width="100%" cellPadding="0" cellSpacing="0">
                        <tbody>
                          <tr>
                            <td width="50%" style={{ borderRight: '1px solid #000', verticalAlign: 'top' }}>
                              <table width="100%" cellPadding="0" cellSpacing="0">
                                <tbody>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>Bill To</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>
                                      <strong style={{ fontWeight: 'bold', fontSize: '10px', textAlign: 'left' }}>{data.site_details?.company_name || 'TIRUPATI PLASTOMATICS PVT. LTD.'}</strong><br />
                                      {data.site_details?.address1 || ''} {data.site_details?.address2 || ''}
                                    </td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>GSTIN</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>{data.site_details?.gst_no || ''}</td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>PAN</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>{data.site_details?.pan_number || ''}</td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>State</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>Rajasthan</td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>Phone No.</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>{data.site_details?.phone || ''}</td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                </tbody>
                              </table>
                            </td>
                            <td width="50%" style={{ verticalAlign: 'top' }}>
                              <table width="100%" cellPadding="0" cellSpacing="0">
                                <tbody>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>Consignee Name</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}><b>{data.site_details?.company_name || 'TIRUPATI PLASTOMATICS PVT. LTD.'}</b></td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>And Address Details</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>{data.site_details?.address1 || ''} {data.site_details?.address2 || ''}</td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>Email</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>{data.site_details?.email || ''}</td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>GSTIN</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>{data.site_details?.gst_no || ''}</td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>PAN</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>{data.site_details?.pan_number || ''}</td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                </tbody>
                              </table>
                            </td>
                          </tr>
                        </tbody>
                      </table>

                      {/* Header End */}

                      {/* Items Table */}
                      <table width="100%" cellPadding="0" cellSpacing="0">
                        <tbody>
                          <tr>
                            <td width="4%" style={{ borderTop: '1px solid #000', borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '24px', lineHeight: '24px', fontSize: '10px', fontWeight: 'bold', textAlign: 'center' }}>S.No</td>
                            <td width="39%" style={{ borderTop: '1px solid #000', borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '24px', lineHeight: '24px', fontSize: '10px', fontWeight: 'bold', textAlign: 'left', paddingLeft: '4px' }}>ITEM</td>
                            <td width="9%" style={{ borderTop: '1px solid #000', borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '24px', lineHeight: '24px', fontSize: '10px', fontWeight: 'bold', textAlign: 'right', paddingRight: '4px' }}>QUANTITY</td>
                            <td width="9%" style={{ borderTop: '1px solid #000', borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '24px', lineHeight: '24px', fontSize: '10px', fontWeight: 'bold', textAlign: 'right', paddingRight: '4px' }}>UNIT PRICE</td>
                            <td width="10%" style={{ borderTop: '1px solid #000', borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '24px', lineHeight: '24px', fontSize: '10px', fontWeight: 'bold', textAlign: 'right', paddingRight: '4px' }}>PRICE</td>
                            <td width="8%" style={{ borderTop: '1px solid #000', borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '24px', lineHeight: '24px', fontSize: '10px', fontWeight: 'bold', textAlign: 'right', paddingRight: '4px' }}>GST(%)</td>
                            <td width="10%" style={{ borderTop: '1px solid #000', borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '24px', lineHeight: '24px', fontSize: '10px', fontWeight: 'bold', textAlign: 'right', paddingRight: '4px' }}>GST VALUE</td>
                            <td width="11%" style={{ borderTop: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '24px', lineHeight: '24px', fontSize: '10px', fontWeight: 'bold', textAlign: 'right', paddingRight: '4px' }}>TOTAL PRICE</td>
                          </tr>

                          {data.items.map((item, idx) => {
                            const qty = parseFloat(item.order_qty as any);
                            const rate = parseFloat(item.rate as any);
                            const taxPct = parseFloat(item.tax_percentage as any);
                            const basePrice = qty * rate;
                            const taxAmt = basePrice * (taxPct / 100);
                            const total = basePrice + taxAmt;

                            return (
                              <tr key={idx}>
                                <td style={{ borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '24px', lineHeight: '24px', fontSize: '10px', textAlign: 'center' }}>{idx + 1}.</td>
                                <td style={{ borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '24px', lineHeight: '24px', fontSize: '10px', textAlign: 'left', paddingLeft: '4px' }}>{item.item_name}</td>
                                <td style={{ borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '24px', lineHeight: '24px', fontSize: '10px', textAlign: 'right', paddingRight: '4px' }}>{qty} {item.uom}</td>
                                <td style={{ borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '24px', lineHeight: '24px', fontSize: '10px', textAlign: 'right', paddingRight: '4px' }}>{formatCurrency(rate)}</td>
                                <td style={{ borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '24px', lineHeight: '24px', fontSize: '10px', textAlign: 'right', paddingRight: '4px' }}>{formatCurrency(basePrice)}</td>
                                <td style={{ borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '24px', lineHeight: '24px', fontSize: '10px', textAlign: 'right', paddingRight: '4px' }}>{taxPct}</td>
                                <td style={{ borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '24px', lineHeight: '24px', fontSize: '10px', textAlign: 'right', paddingRight: '4px' }}>{formatCurrency(taxAmt)}</td>
                                <td style={{ borderBottom: '1px solid #000', color: '#000', height: '24px', lineHeight: '24px', fontSize: '10px', textAlign: 'right', paddingRight: '4px' }}>{formatCurrency(total)}</td>
                              </tr>
                            );
                          })}
                        </tbody>
                      </table>

                      {/* Amount Start */}
                      <table width="100%" cellPadding="0" cellSpacing="0">
                        <tbody>
                          <tr>
                            <td>
                              <table width="100%" cellPadding="0" cellSpacing="0">
                                <tbody>
                                  <tr>
                                    <td width="71%" style={{ borderRight: '1px solid #000' }}>
                                      <table width="100%" cellPadding="0" cellSpacing="0">
                                        <tbody>
                                          <tr>
                                            <td width="18%" style={{ fontWeight: 'bold', textAlign: 'center', borderBottom: '1px solid #000', color: '#000', fontSize: '10px', height: '20px' }}>Amount<br/>(In Words)</td>
                                            <td width="82%" style={{ textAlign: 'left', color: '#000', borderBottom: '1px solid #000', fontSize: '10px', height: '20px' }}>
                                              {numberToWords(parseFloat(data.po.total_amount))}
                                            </td>
                                          </tr>
                                        </tbody>
                                      </table>
                                    </td>
                                    <td width="29%">
                                      <table width="100%" cellPadding="0" cellSpacing="0">
                                        <tbody>
                                          <tr>
                                            <td width="62%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '20px', paddingLeft: '4px' }}>Grand Total (INR)</td>
                                            <td width="38%" style={{ textAlign: 'right', color: '#000', fontSize: '10px', height: '20px', paddingRight: '4px' }}>
                                              {formatCurrency(parseFloat(data.po.total_amount))}
                                            </td>
                                          </tr>
                                        </tbody>
                                      </table>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>

                              <table width="100%" cellPadding="0" cellSpacing="0" style={{ borderTop: '1px solid #000' }}>
                                <tbody>
                                  <tr>
                                    <td width="9%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', lineHeight: '14px', verticalAlign: 'top', padding: '4px 0 4px 4px' }}>Remarks</td>
                                    <td width="91%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', lineHeight: '14px', verticalAlign: 'top', padding: '4px 0 4px 10px' }}>
                                      {data.po.remark || (data.po.freight > 0 ? `Freight inclusive: ₹${data.po.freight}` : 'Freight inclusive')}
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                      <hr style={{ borderColor: '#000', margin: 0 }} />
                      
                      {/* Terms and Conditions */}
                      <table width="100%" cellPadding="0" cellSpacing="0">
                        <tbody>
                          <tr>
                            <td>
                              <table width="100%" cellPadding="3" cellSpacing="0" style={{ borderTop: '1px solid #000' }}>
                                <tbody>
                                  <tr>
                                    <td width="100%" style={{ borderTop: '1px solid #000', borderLeft: '1px solid #000', borderRight: '1px solid #000', borderBottom: '1px solid #000', height: '18px', lineHeight: '18px', fontWeight: 'bold', fontSize: '14px', paddingLeft: '4px' }}>
                                      Terms and Conditions
                                    </td>
                                  </tr>
                                </tbody>
                              </table>

                              <table width="100%" cellPadding="0" cellSpacing="0">
                                <tbody>
                                  <tr>
                                    <td width="1%" style={{ paddingTop: '2px', paddingBottom: '2px', lineHeight: '14px' }}></td>
                                    <td width="95%" style={{ fontSize: '10px', textAlign: 'left', paddingLeft: '4px', paddingTop: '2px', paddingBottom: '2px', lineHeight: '14px', fontWeight: 'bold' }}>Without E-Way Bill, we will not accept the material.</td>
                                  </tr>
                                  <tr>
                                    <td width="1%" style={{ paddingTop: '2px', paddingBottom: '2px', lineHeight: '14px' }}></td>
                                    <td width="95%" style={{ fontSize: '10px', textAlign: 'left', paddingLeft: '4px', paddingTop: '2px', paddingBottom: '2px', lineHeight: '14px', fontWeight: 'bold' }}>Note : 1% LD will be charged per week if material is not dispatched according to the schedule.</td>
                                  </tr>
                                  <tr>
                                    <td width="1%" style={{ paddingTop: '2px', paddingBottom: '2px', lineHeight: '14px' }}></td>
                                    <td width="95%" style={{ fontSize: '10px', textAlign: 'left', paddingLeft: '4px', paddingTop: '2px', paddingBottom: '2px', lineHeight: '14px' }}>Test Report of the goods must accompany Consignment.</td>
                                  </tr>
                                  <tr>
                                    <td width="1%" style={{ paddingTop: '2px', paddingBottom: '2px', lineHeight: '14px' }}></td>
                                    <td width="95%" style={{ fontSize: '10px', textAlign: 'left', paddingLeft: '4px', paddingTop: '2px', paddingBottom: '2px', lineHeight: '14px' }}>Price : The Price Mentioned above shall remain firm till full execution of order.</td>
                                  </tr>
                                  <tr>
                                    <td width="1%" style={{ paddingTop: '2px', paddingBottom: '2px', lineHeight: '14px' }}></td>
                                    <td width="95%" style={{ fontSize: '10px', textAlign: 'left', paddingLeft: '4px', paddingTop: '2px', paddingBottom: '2px', lineHeight: '14px' }}>Discount : Please allow us.......NA.......discount on with above prices.</td>
                                  </tr>
                                  <tr>
                                    <td width="1%" style={{ paddingTop: '2px', paddingBottom: '2px', lineHeight: '14px' }}></td>
                                    <td width="95%" style={{ fontSize: '10px', textAlign: 'left', paddingLeft: '4px', paddingTop: '2px', paddingBottom: '2px', lineHeight: '14px' }}>GST : Inclusive / Exclusive</td>
                                  </tr>
                                  <tr>
                                    <td width="1%" style={{ paddingTop: '2px', paddingBottom: '2px', lineHeight: '14px' }}></td>
                                    <td width="95%" style={{ fontSize: '10px', textAlign: 'left', paddingLeft: '4px', paddingTop: '2px', paddingBottom: '2px', lineHeight: '14px' }}>We need Transport / Duplicate Copy of Challan-cum-invoice TAX Document with the material / Bank</td>
                                  </tr>
                                  <tr>
                                    <td width="1%" style={{ paddingTop: '2px', paddingBottom: '2px', lineHeight: '14px' }}></td>
                                    <td width="95%" style={{ fontSize: '10px', textAlign: 'left', paddingLeft: '4px', paddingTop: '2px', paddingBottom: '2px', lineHeight: '14px' }}>Document to enable us to take credit of MODVAT for CED paid by you.</td>
                                  </tr>
                                  <tr>
                                    <td width="1%" style={{ paddingTop: '2px', paddingBottom: '2px', lineHeight: '14px' }}></td>
                                    <td width="95%" style={{ fontSize: '10px', textAlign: 'left', paddingLeft: '4px', paddingTop: '2px', paddingBottom: '2px', lineHeight: '14px' }}>It is therefore requested that Material should be dispatched directly by Excise Godown.</td>
                                  </tr>
                                  <tr>
                                    <td width="1%" style={{ paddingTop: '2px', paddingBottom: '2px', lineHeight: '14px' }}></td>
                                    <td width="95%" style={{ fontSize: '10px', textAlign: 'left', paddingLeft: '4px', paddingTop: '2px', paddingBottom: '2px', lineHeight: '14px' }}>Please note that Materials may not be accepted by us in the absence of such documents.</td>
                                  </tr>
                                  <tr>
                                    <td width="1%" style={{ paddingTop: '2px', paddingBottom: '2px', lineHeight: '14px' }}></td>
                                    <td width="95%" style={{ fontSize: '10px', textAlign: 'left', paddingLeft: '4px', paddingTop: '2px', paddingBottom: '2px', lineHeight: '14px' }}>Please confirm the above prior to dispatch.</td>
                                  </tr>
                                  <tr>
                                    <td width="1%" style={{ paddingTop: '2px', paddingBottom: '2px', lineHeight: '14px' }}></td>
                                    <td width="95%" style={{ fontSize: '10px', textAlign: 'left', paddingLeft: '4px', paddingTop: '2px', paddingBottom: '2px', lineHeight: '14px' }}>
                                      Please send the document through Courier Mode.<br />
                                      <b>Payment Terms - <br />
                                      (From date of material received.)<br />
                                      {data.po.payment_term || '90 DAYS'}</b><br />Through Your Banker as per RBI directive under intimation to us
                                    </td>
                                  </tr>
                                </tbody>
                              </table>

                              <table width="100%" cellPadding="0" cellSpacing="0">
                                <tbody>
                                  <tr>
                                    <td width="25%" style={{ textAlign: 'left', borderTop: '1px solid #000' }}>
                                      &nbsp;&nbsp; 
                                    </td>
                                    <td width="25%" style={{ textAlign: 'center', borderTop: '1px solid #000' }}></td>
                                    <td width="50%" style={{ textAlign: 'right', borderTop: '1px solid #000', fontSize: '10px' }}>
                                      For : <b>{data.site_details?.company_name || 'TIRUPATI PLASTOMATICS PVT. LTD.'}</b><br /><br /><br />
                                      <b> Vishal Agarwal &nbsp; <br />
                                      M +91-9772977766 &nbsp; <br />
                                      Purchase Officer&nbsp;</b> &nbsp;
                                    </td>
                                  </tr>
                                </tbody>
                              </table>

                            </td>
                          </tr>
                        </tbody>
                      </table>

                      <table width="100%" style={{ borderTop: '1px solid #000' }} cellPadding="0" cellSpacing="0">
                        <tbody>
                          <tr>
                            <td width="100%" style={{ textAlign: 'center', fontSize: '10px', fontWeight: 'bold', color: '#000', padding: '2px 0' }}>
                              Subject to Jaipur Jurisdiction
                            </td>
                          </tr>
                        </tbody>
                      </table>

                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            {/* PAGE 2: Delivery Schedule */}
            <div style={{ border: '1px solid #000' }}>
              <table width="100%" cellPadding="0" cellSpacing="0">
                <tbody>
                  <tr>
                    <td>
                      {/* Header Start */}
                      <table width="100%" style={{ padding: '1px 1px 0px 0px' }} align="left" cellPadding="0" cellSpacing="0">
                        <tbody>
                          <tr>
                            <td style={{ textAlign: 'left' }} width="50%">
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                              <img src={data.site_details?.logo ? `${baseUrl}/public/uploads/logos/${data.site_details.logo}` : ''} alt="LOGO" style={{ display: 'block', height: '62px', maxWidth: '150px', objectFit: 'contain' }} onError={(e) => { (e.target as HTMLImageElement).src = `${baseUrl}/public/uploads/logos/tirupati_tppl_logo.png`; }} /><br />
                              <span style={{ display: 'block', color: '#000', fontSize: '10px' }}>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>{data.site_details?.company_name || 'TIRUPATI PLASTOMATICS PVT. LTD.'}</b></span>
                            </td>
                            <td style={{ textAlign: 'right', fontSize: '10px' }} width="50%">
                              {data.site_details?.address1 || ''},<br /> {data.site_details?.address2 || ''}<br />
                              <b>Phone</b>: {data.site_details?.phone || ''}<br />
                              &nbsp;&nbsp;&nbsp;&nbsp;<b>Email</b>: <u>{data.site_details?.email || ''}</u><br />
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Website</b> :&nbsp;{data.site_details?.website || ''}
                            </td>
                          </tr>
                        </tbody>
                      </table>
                      <br /><hr style={{ borderColor: '#000', margin: 0 }} />

                      <table width="100%" cellPadding="0" cellSpacing="0">
                        <tbody>
                          <tr>
                            <td width="100%" style={{ height: '22px', lineHeight: '22px', color: '#000', textAlign: 'center', borderTop: '1px solid #000', borderBottom: '1px solid #000', fontSize: '16px', fontWeight: 'bold' }}>Delivery Schedule</td>
                          </tr>
                        </tbody>
                      </table>

                      <table width="100%" cellPadding="0" cellSpacing="0">
                        <tbody>
                          <tr>
                            <td width="50%" style={{ borderRight: '1px solid #000', verticalAlign: 'top' }}>
                              <table width="100%" cellPadding="0" cellSpacing="0">
                                <tbody>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>TO</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>
                                      <strong style={{ fontWeight: 'bold', fontSize: '10px', textAlign: 'left' }}>{data.po.vendor_name}</strong><br />
                                      {data.po.vendor_address !== 'N/A' && <>{data.po.vendor_address.split('\n').map((line: string, i: number) => <React.Fragment key={i}>{line}<br/></React.Fragment>)}</>}
                                    </td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>GST No.</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>{data.po.gst_number}</td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>State</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>Rajasthan</td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>Phone No.</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>{data.po.vendor_phone}</td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>Email</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>{data.po.vendor_email}</td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                </tbody>
                              </table>
                            </td>

                            <td width="50%" style={{ verticalAlign: 'top' }}>
                              <table width="100%" cellPadding="0" cellSpacing="0">
                                <tbody>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>Purchase Order No.</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>{data.po.po_number}</td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>Purchase Order Date</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>{data.po.po_date ? data.po.po_date.split('T')[0].split('-').reverse().join('-') : '-'}</td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>Delivery Date</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>{data.po.delivery_date ? data.po.delivery_date.split('T')[0].split('-').reverse().join('-') : '-'}</td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>Amendment No</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>
                                      {data.po.amendment_no > 0 ? (
                                        <>{data.po.amendment_no}&nbsp;(<b>Date : </b>{data.po.amendment_date ? data.po.amendment_date.split('T')[0].split('-').reverse().join('-') : ''} )</>
                                      ) : '---'}
                                    </td>
                                    <td width="2%" style={{ height: '24px', lineHeight: '24px' }}></td>
                                  </tr>
                                </tbody>
                              </table>
                            </td>
                          </tr>
                        </tbody>
                      </table>

                      {/* Header End */}

                      {/* Delivery Schedule Table */}
                      <table width="100%" cellPadding="0" cellSpacing="0">
                        <tbody>
                          <tr>
                            <td width="43%" style={{ borderTop: '1px solid #000', borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '24px', lineHeight: '24px', fontSize: '10px', fontWeight: 'bold', textAlign: 'left', paddingLeft: '4px' }}>ITEM</td>
                            <td width="17%" style={{ borderTop: '1px solid #000', borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '24px', lineHeight: '24px', fontSize: '10px', fontWeight: 'bold', textAlign: 'center' }}>PO Qty</td>
                            <td width="20%" style={{ borderTop: '1px solid #000', borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '24px', lineHeight: '24px', fontSize: '10px', textAlign: 'center', fontWeight: 'bold' }}>DATE</td>
                            <td width="20%" style={{ borderTop: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '24px', lineHeight: '24px', fontSize: '10px', textAlign: 'center', fontWeight: 'bold' }}>QTY</td>
                          </tr>

                          {data.items.map((item, idx) => {
                            const qty = parseFloat(item.order_qty as any);
                            return (
                              <tr key={idx}>
                                <td style={{ borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '24px', lineHeight: '24px', fontSize: '10px', textAlign: 'left', paddingLeft: '4px' }}>{item.item_name}</td>
                                <td style={{ borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '24px', lineHeight: '24px', fontSize: '10px', textAlign: 'center' }}>{qty}</td>
                                <td style={{ borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '24px', lineHeight: '24px', fontSize: '10px', textAlign: 'center' }}>{data.po.delivery_date ? data.po.delivery_date.split('T')[0].split('-').reverse().join('-') : '-'}</td>
                                <td style={{ borderBottom: '1px solid #000', color: '#000', height: '24px', lineHeight: '24px', fontSize: '10px', textAlign: 'center' }}>{qty}</td>
                              </tr>
                            );
                          })}
                        </tbody>
                      </table>

                      {/* Remarks */}
                      <table width="100%" cellPadding="3" cellSpacing="0" style={{ borderTop: '1px solid #000' }}>
                        <tbody>
                          <tr>
                            <td width="09%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}> &nbsp;Remarks</td>
                            <td width="91%" style={{ textAlign: 'left', color: '#000', fontSize: '10px', height: '24px', lineHeight: '24px' }}>
                              {data.po.remark || (data.po.freight > 0 ? `Freight inclusive: ₹${data.po.freight}` : 'Freight inclusive')}
                            </td>
                          </tr>
                        </tbody>
                      </table>

                      {/* Signatures */}
                      <table width="100%" cellPadding="0" cellSpacing="0">
                        <tbody>
                          <tr>
                            <td>
                              <table width="100%" cellPadding="0" cellSpacing="0">
                                <tbody>
                                  <tr>
                                    <td width="25%" style={{ textAlign: 'left', borderTop: '1px solid #000' }}>
                                      &nbsp;&nbsp; 
                                    </td>
                                    <td width="25%" style={{ textAlign: 'center', borderTop: '1px solid #000' }}></td>
                                    <td width="50%" style={{ textAlign: 'right', borderTop: '1px solid #000', fontSize: '10px' }}>
                                      For : <b>{data.site_details?.company_name || 'TIRUPATI PLASTOMATICS PVT. LTD.'}</b><br /><br /><br />
                                      <b> Vishal Agarwal &nbsp; <br />
                                      M +91-9772977766 &nbsp; <br />
                                      Purchase Officer </b> &nbsp;
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </td>
                          </tr>
                        </tbody>
                      </table>

                      <table width="100%" style={{ borderTop: '1px solid #000' }} cellPadding="0" cellSpacing="0">
                        <tbody>
                          <tr>
                            <td width="100%" style={{ textAlign: 'center', fontSize: '10px', fontWeight: 'bold', color: '#000', padding: '2px 0' }}>
                              Subject to Jaipur Jurisdiction
                            </td>
                          </tr>
                        </tbody>
                      </table>

                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            
          </div>
        ) : null}
      </div>
    );
  }
