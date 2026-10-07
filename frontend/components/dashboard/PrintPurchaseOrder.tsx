"use client";

import React from 'react';
import { useQuery } from '@tanstack/react-query';
import purchaseOrderService from '../../services/purchaseOrder.service';
import { Loader } from 'lucide-react';
import { API_ASSET_URL } from '../../services/apiConfig';

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

function formatCurrency(amount: number | string | null | undefined) {
  const val = Number(amount || 0);
  if (Math.floor(val) === val) {
    return val.toLocaleString('en-IN', { maximumFractionDigits: 0 });
  }
  return val.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

export function PrintPurchaseOrder({ poId }: PrintPurchaseOrderProps) {
  const { data, isLoading, isError } = useQuery({
    queryKey: ['purchase-order-details', poId],
    queryFn: () => purchaseOrderService.getDetails(poId),
    enabled: !!poId,
  });

  if (!poId) return null;

  const baseUrl = API_ASSET_URL;
  const legacyLogo = data?.site_details?.small_logo || data?.site_details?.logo || 'd80960ce77aede66a5c3c8eef8dfafda.png';
  const officer = data?.officer || {};
  const paymentTerms = data?.payment_terms || [];
  const schedules = data?.schedules || [];

  return (
    <div data-pdf-ready={!!data && !isLoading && !isError} data-pdf-error={isError} className="bg-white w-full print:w-full print:max-w-none custom-scrollbar mx-auto">
        <style jsx global>{`
          @page {
            size: A4 portrait;
            margin: 0;
          }

          @media print {
            html,
            body {
              width: 210mm;
              margin: 0;
              padding: 0;
              background: #fff !important;
            }

            .legacy-po-print {
              width: 210mm;
              padding: 0;
              box-sizing: border-box;
              background: #fff !important;
            }

            .legacy-po-print table {
              border: 0 !important;
              border-collapse: collapse !important;
              border-spacing: 0;
              text-align: inherit !important;
            }

            .legacy-po-print table tbody tr,
            .legacy-po-print table tbody tr:nth-child(even),
            .legacy-po-print table tbody tr:hover {
              background-color: #fff !important;
              border: 0 !important;
            }

            .legacy-po-print table tbody tr td {
              border: 0 !important;
              box-sizing: border-box;
              color: #000 !important;
              font-size: 8px !important;
              padding: 0 !important;
            }

            .legacy-po-print [style*="font-size: 10px"] {
              font-size: 10px !important;
            }

            .legacy-po-print [style*="font-size: 14px"] {
              font-size: 14px !important;
            }

            .legacy-po-print [style*="padding-left: 4px"] {
              padding-left: 4px !important;
            }

            .legacy-po-print [style*="padding-right: 4px"] {
              padding-right: 4px !important;
            }

            .legacy-po-print [style*="line-height: 9px"] {
              line-height: 12px !important;
            }

            .legacy-po-print [style*="height: 12px"] {
              height: 12px !important;
            }

            .legacy-po-print [style*="border-top: 1px"] {
              border-top: 1px solid #000 !important;
            }

            .legacy-po-print [style*="border-right: 1px"] {
              border-right: 1px solid #000 !important;
            }

            .legacy-po-print [style*="border-bottom: 1px"] {
              border-bottom: 1px solid #000 !important;
            }

            .legacy-po-print [style*="border-left: 1px"] {
              border-left: 1px solid #000 !important;
            }

            .legacy-po-print br {
              line-height: 8px;
            }

            .legacy-po-page {
              width: 190mm;
              margin: 10mm auto 0;
              break-inside: avoid;
              page-break-inside: avoid;
            }

            .legacy-po-page.first {
              break-after: page;
              page-break-after: always;
            }
          }
        `}</style>
        {isLoading ? (
          <div className="flex justify-center items-center py-20 print:hidden">
            <Loader className="w-8 h-8 animate-spin text-blue-500" />
          </div>
        ) : data ? (
          <div className="print-content legacy-po-print text-black font-sans bg-white" style={{ fontFamily: 'Arial, Helvetica, sans-serif' }}>
            
            {/* PAGE 1: Purchase Order */}
            <div style={{ border: '1px solid #000', marginBottom: '20px' }} className="legacy-po-page first print:mb-0">
              <table width="100%" cellPadding="0" cellSpacing="0">
                <tbody>
                  <tr>
                    <td>
                      {/* Header Start */}
                      <table width="100%" style={{ padding: '1px 1px 0px 0px', minHeight: '122px' }} align="left" cellPadding="0" cellSpacing="0">
                        <tbody>
                          <tr>
                            <td style={{ textAlign: 'left', verticalAlign: 'top', paddingTop: '18px' }} width="50%">
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                              <img src={`${baseUrl}/public/uploads/logos/${legacyLogo}`} alt="LOGO" style={{ display: 'block', height: '62px', maxWidth: '150px', objectFit: 'contain', marginLeft: '20px' }} onError={(e) => { const img = e.target as HTMLImageElement; img.onerror = null; const fallback = `${baseUrl}/public/uploads/logos/d80960ce77aede66a5c3c8eef8dfafda.png`; if (img.src !== fallback) img.src = fallback; }} /><br />
                              <span style={{ display: 'block', color: '#000', fontSize: '10px', paddingLeft: '20px' }}><b>{data.site_details?.company_name || 'TIRUPATI PLASTOMATICS PVT. LTD.'}</b></span>
                            </td>
                            <td style={{ textAlign: 'right', fontSize: '10px', verticalAlign: 'top', paddingTop: '4px', paddingRight: '2px', lineHeight: '14px' }} width="50%" align="right">
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
                            <td width="100%" style={{ height: '15px', lineHeight: '18px', color: '#000', textAlign: 'center', borderTop: '1px solid #000', borderBottom: '1px solid #000', fontSize: '14px', fontWeight: 'bold' }}>Purchase Order</td>
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
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>TO</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>
                                      <strong style={{ fontWeight: 'bold', fontSize: '8px', textAlign: 'left' }}>{data.po.vendor_name}</strong><br />
                                      {data.po.vendor_address && data.po.vendor_address !== 'N/A' && <>{data.po.vendor_address.split('\n').map((line: string, i: number) => <React.Fragment key={i}>{line}<br/></React.Fragment>)}</>}
                                    </td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>GST No.</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>{data.po.gst_number}</td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>State</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>Rajasthan</td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>Phone No.</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>{data.po.vendor_phone}</td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>Email</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>{data.po.vendor_email}</td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                </tbody>
                              </table>
                            </td>

                            <td width="50%" style={{ verticalAlign: 'top' }}>
                              <table width="100%" cellPadding="0" cellSpacing="0">
                                <tbody>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>Purchase Order No.</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>{data.po.po_number}</td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>Purchase Order Date</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>{data.po.po_date ? data.po.po_date.split('T')[0].split('-').reverse().join('-') : '-'}</td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>Delivery Date</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>{data.po.delivery_date ? data.po.delivery_date.split('T')[0].split('-').reverse().join('-') : '-'}</td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>Amendment No</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>
                                      {data.po.amendment_no > 0 ? (
                                        <>{data.po.amendment_no}&nbsp;(<b>Date : </b>{data.po.amendment_date ? data.po.amendment_date.split('T')[0].split('-').reverse().join('-') : ''} )</>
                                      ) : '---'}
                                    </td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
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
                            <td width="100%" style={{ textAlign: 'center', fontSize: '8px', color: '#000', height: '12px', lineHeight: '9px', borderTop: '1px solid #000', borderBottom: '1px solid #000' }}>
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
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>Bill To</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>
                                      <strong style={{ fontWeight: 'bold', fontSize: '8px', textAlign: 'left' }}>{data.site_details?.company_name || 'TIRUPATI PLASTOMATICS PVT. LTD.'}</strong><br />
                                      {data.site_details?.address1 || ''} {data.site_details?.address2 || ''}
                                    </td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>GSTIN</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>{data.site_details?.gst_no || ''}</td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>PAN</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>{data.site_details?.pan_number || ''}</td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>State</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>Rajasthan</td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>Phone No.</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>{data.site_details?.phone || ''}</td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                </tbody>
                              </table>
                            </td>
                            <td width="50%" style={{ verticalAlign: 'top' }}>
                              <table width="100%" cellPadding="0" cellSpacing="0">
                                <tbody>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>Consignee Name</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}><b>{data.site_details?.company_name || 'TIRUPATI PLASTOMATICS PVT. LTD.'}</b></td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>And Address Details</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>{data.site_details?.address1 || ''} {data.site_details?.address2 || ''}</td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>Email</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>{data.site_details?.email || ''}</td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>GSTIN</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>{data.site_details?.gst_no || ''}</td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>PAN</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>{data.site_details?.pan_number || ''}</td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
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
                            <td width="4%" style={{ borderTop: '1px solid #000', borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', fontWeight: 'bold', textAlign: 'center' }}>S.No</td>
                            <td width="39%" style={{ borderTop: '1px solid #000', borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', fontWeight: 'bold', textAlign: 'left', paddingLeft: '4px' }}>ITEM</td>
                            <td width="9%" style={{ borderTop: '1px solid #000', borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', fontWeight: 'bold', textAlign: 'right', paddingRight: '4px' }}>QUANTITY</td>
                            <td width="9%" style={{ borderTop: '1px solid #000', borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', fontWeight: 'bold', textAlign: 'right', paddingRight: '4px' }}>UNIT PRICE</td>
                            <td width="10%" style={{ borderTop: '1px solid #000', borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', fontWeight: 'bold', textAlign: 'right', paddingRight: '4px' }}>PRICE</td>
                            <td width="8%" style={{ borderTop: '1px solid #000', borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', fontWeight: 'bold', textAlign: 'right', paddingRight: '4px' }}>GST(%)</td>
                            <td width="10%" style={{ borderTop: '1px solid #000', borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', fontWeight: 'bold', textAlign: 'right', paddingRight: '4px' }}>GST VALUE</td>
                            <td width="11%" style={{ borderTop: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', fontWeight: 'bold', textAlign: 'right', paddingRight: '4px' }}>TOTAL PRICE</td>
                          </tr>

                          {data.items.map((item, idx) => {
                            const qty = parseFloat(String(item.order_qty));
                            const rate = parseFloat(String(item.rate));
                            const taxPct = parseFloat(String(item.tax_percentage));
                            const basePrice = qty * rate;
                            const taxAmt = parseFloat(String(item.tax_amt)) || (basePrice * (taxPct / 100));
                            const total = parseFloat(String(item.amount)) || (basePrice + taxAmt);

                            return (
                              <tr key={idx}>
                                <td style={{ borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', textAlign: 'center' }}>{idx + 1}.</td>
                                <td style={{ borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', textAlign: 'left', paddingLeft: '4px' }}>{item.item_name}</td>
                                <td style={{ borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', textAlign: 'right', paddingRight: '4px' }}>{qty} {item.uom}</td>
                                <td style={{ borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', textAlign: 'right', paddingRight: '4px' }}>{formatCurrency(rate)}</td>
                                <td style={{ borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', textAlign: 'right', paddingRight: '4px' }}>{formatCurrency(basePrice)}</td>
                                <td style={{ borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', textAlign: 'right', paddingRight: '4px' }}>{taxPct}</td>
                                <td style={{ borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', textAlign: 'right', paddingRight: '4px' }}>{formatCurrency(taxAmt)}</td>
                                <td style={{ borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', textAlign: 'right', paddingRight: '4px' }}>{formatCurrency(total)}</td>
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
                                            <td width="18%" style={{ fontWeight: 'bold', textAlign: 'center', borderBottom: '1px solid #000', color: '#000', fontSize: '8px', height: '12px' }}>Amount<br/>(In Words)</td>
                                            <td width="82%" style={{ textAlign: 'left', color: '#000', borderBottom: '1px solid #000', fontSize: '8px', height: '12px' }}>
                                              {numberToWords(Number(data.po.total_amount))}
                                            </td>
                                          </tr>
                                        </tbody>
                                      </table>
                                    </td>
                                    <td width="29%">
                                      <table width="100%" cellPadding="0" cellSpacing="0">
                                        <tbody>
                                          <tr>
                                            <td width="62%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', paddingLeft: '4px' }}>Grand Total (INR)</td>
                                            <td width="38%" style={{ textAlign: 'right', color: '#000', fontSize: '8px', height: '12px', paddingRight: '4px' }}>
                                              {formatCurrency(Number(data.po.total_amount))}
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
                                    <td width="9%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', lineHeight: '9px', verticalAlign: 'top', padding: '4px 0 4px 4px' }}>Remarks</td>
                                    <td width="91%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', lineHeight: '9px', verticalAlign: 'top', padding: '4px 0 4px 10px' }}>
                                      {data.po.remark || (Number(data.po.freight || 0) > 0 ? `Freight inclusive: ₹${data.po.freight}` : 'Freight inclusive')}
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
                                    <td width="100%" style={{ borderTop: '1px solid #000', borderLeft: '1px solid #000', borderRight: '1px solid #000', borderBottom: '1px solid #000', height: '12px', lineHeight: '9px', fontWeight: 'bold', fontSize: '8px', paddingLeft: '4px' }}>
                                      Terms and Conditions
                                    </td>
                                  </tr>
                                </tbody>
                              </table>

                              <table width="100%" cellPadding="0" cellSpacing="0">
                                <tbody>
                                  {paymentTerms.map((term) => (
                                    <tr key={term.id || term.description}>
                                      <td width="1%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                      <td
                                        width="95%"
                                        style={{ fontSize: '8px', textAlign: 'left', height: '12px', lineHeight: '9px' }}
                                        dangerouslySetInnerHTML={{ __html: term.description || '' }}
                                      />
                                    </tr>
                                  ))}
                                  <tr>
                                    <td width="1%" style={{ paddingTop: '2px', paddingBottom: '2px', lineHeight: '9px' }}></td>
                                    <td width="95%" style={{ fontSize: '8px', textAlign: 'left', paddingLeft: '4px', paddingTop: '2px', paddingBottom: '2px', lineHeight: '9px' }}>
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
                                    <td width="50%" style={{ textAlign: 'right', borderTop: '1px solid #000', fontSize: '8px' }}>
                                      For : <b>{data.site_details?.company_name || 'TIRUPATI PLASTOMATICS PVT. LTD.'}</b><br /><br /><br />
                                      <b> {officer.name || ''} &nbsp; <br />
                                      {officer.mobile || ''} &nbsp; <br />
                                      {officer.designation || ''}&nbsp;</b> &nbsp;
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
                            <td width="100%" style={{ textAlign: 'center', fontSize: '8px', fontWeight: 'bold', color: '#000', padding: '2px 0' }}>
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
            <div style={{ border: '1px solid #000' }} className="legacy-po-page">
              <table width="100%" cellPadding="0" cellSpacing="0">
                <tbody>
                  <tr>
                    <td>
                      {/* Header Start */}
                      <table width="100%" style={{ padding: '1px 1px 0px 0px', minHeight: '122px' }} align="left" cellPadding="0" cellSpacing="0">
                        <tbody>
                          <tr>
                            <td style={{ textAlign: 'left', verticalAlign: 'top', paddingTop: '18px' }} width="50%">
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                              <img src={`${baseUrl}/public/uploads/logos/${legacyLogo}`} alt="LOGO" style={{ display: 'block', height: '62px', maxWidth: '150px', objectFit: 'contain', marginLeft: '20px' }} onError={(e) => { const img = e.target as HTMLImageElement; img.onerror = null; const fallback = `${baseUrl}/public/uploads/logos/d80960ce77aede66a5c3c8eef8dfafda.png`; if (img.src !== fallback) img.src = fallback; }} /><br />
                              <span style={{ display: 'block', color: '#000', fontSize: '10px', paddingLeft: '20px' }}><b>{data.site_details?.company_name || 'TIRUPATI PLASTOMATICS PVT. LTD.'}</b></span>
                            </td>
                            <td style={{ textAlign: 'right', fontSize: '10px', verticalAlign: 'top', paddingTop: '4px', paddingRight: '2px', lineHeight: '14px' }} width="50%" align="right">
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
                            <td width="100%" style={{ height: '15px', lineHeight: '18px', color: '#000', textAlign: 'center', borderTop: '1px solid #000', borderBottom: '1px solid #000', fontSize: '14px', fontWeight: 'bold' }}>Delivery Schedule</td>
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
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>TO</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>
                                      <strong style={{ fontWeight: 'bold', fontSize: '8px', textAlign: 'left' }}>{data.po.vendor_name}</strong><br />
                                      {data.po.vendor_address && data.po.vendor_address !== 'N/A' && <>{data.po.vendor_address.split('\n').map((line: string, i: number) => <React.Fragment key={i}>{line}<br/></React.Fragment>)}</>}
                                    </td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>GST No.</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>{data.po.gst_number}</td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>State</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>Rajasthan</td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>Phone No.</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>{data.po.vendor_phone}</td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="25%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>Email</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="66%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>{data.po.vendor_email}</td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                </tbody>
                              </table>
                            </td>

                            <td width="50%" style={{ verticalAlign: 'top' }}>
                              <table width="100%" cellPadding="0" cellSpacing="0">
                                <tbody>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>Purchase Order No.</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>{data.po.po_number}</td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>Purchase Order Date</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>{data.po.po_date ? data.po.po_date.split('T')[0].split('-').reverse().join('-') : '-'}</td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>Delivery Date</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>{data.po.delivery_date ? data.po.delivery_date.split('T')[0].split('-').reverse().join('-') : '-'}</td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                  </tr>
                                  <tr>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
                                    <td width="30%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>Amendment No</td>
                                    <td width="5%" style={{ textAlign: 'center', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>:</td>
                                    <td width="61%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>
                                      {data.po.amendment_no > 0 ? (
                                        <>{data.po.amendment_no}&nbsp;(<b>Date : </b>{data.po.amendment_date ? data.po.amendment_date.split('T')[0].split('-').reverse().join('-') : ''} )</>
                                      ) : '---'}
                                    </td>
                                    <td width="2%" style={{ height: '12px', lineHeight: '9px' }}></td>
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
                            <td width="30%" style={{ borderTop: '1px solid #000', borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', fontWeight: 'bold', textAlign: 'left', paddingLeft: '4px' }}>ITEM</td>
                            <td width="10%" style={{ borderTop: '1px solid #000', borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', fontWeight: 'bold', textAlign: 'center' }}>PO Qty</td>
                            {(() => {
                               const uniqueDates = Array.from(new Set(schedules.map((s) => s.delivery_date)));
                               if (uniqueDates.length === 0) {
                                 return (
                                   <>
                                     <td width="30%" style={{ borderTop: '1px solid #000', borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', textAlign: 'center', fontWeight: 'bold' }}>DATE</td>
                                     <td width="30%" style={{ borderTop: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', textAlign: 'center', fontWeight: 'bold' }}>QTY</td>
                                   </>
                                 );
                               }
                               const dateColWidth = 60 / (uniqueDates.length * 2);
                               return uniqueDates.map((date, i) => (
                                 <React.Fragment key={i}>
                                   <td width={`${dateColWidth}%`} style={{ borderTop: '1px solid #000', borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', textAlign: 'center', fontWeight: 'bold' }}>DATE</td>
                                   <td width={`${dateColWidth}%`} style={{ borderTop: '1px solid #000', borderRight: i === uniqueDates.length - 1 ? 'none' : '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', textAlign: 'center', fontWeight: 'bold' }}>QTY</td>
                                 </React.Fragment>
                               ));
                            })()}
                          </tr>

                          {data.items.map((item, idx) => {
                            const qty = parseFloat(String(item.order_qty));
                            const uniqueDates = Array.from(new Set(schedules.map((s) => s.delivery_date)));

                            return (
                              <tr key={idx}>
                                <td style={{ borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', textAlign: 'left', paddingLeft: '4px' }}>{item.item_name}</td>
                                <td style={{ borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', textAlign: 'center' }}>{qty}</td>
                                {(() => {
                                  if (uniqueDates.length === 0) {
                                    return (
                                      <>
                                        <td style={{ borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', textAlign: 'center' }}>{data.po.delivery_date ? data.po.delivery_date.split('T')[0].split('-').reverse().join('-') : '-'}</td>
                                        <td style={{ borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', textAlign: 'center' }}>{qty}</td>
                                      </>
                                    );
                                  }
                                  
                                  return uniqueDates.map((date, i) => {
                                    const scheduleItem = schedules.find((s) => s.delivery_date === date && s.item_id === item.item_id);
                                    const scheduleQty = scheduleItem ? scheduleItem.item_qty : '';
                                    return (
                                      <React.Fragment key={i}>
                                        <td style={{ borderRight: '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', textAlign: 'center' }}>{scheduleQty ? new Date(date).toLocaleDateString('en-GB').replace(/\//g, '-') : ''}</td>
                                        <td style={{ borderRight: i === uniqueDates.length - 1 ? 'none' : '1px solid #000', borderBottom: '1px solid #000', color: '#000', height: '12px', lineHeight: '9px', fontSize: '8px', textAlign: 'center' }}>{scheduleQty || ''}</td>
                                      </React.Fragment>
                                    )
                                  });
                                })()}
                              </tr>
                            );
                          })}
                        </tbody>
                      </table>

                      {/* Remarks */}
                      <table width="100%" cellPadding="3" cellSpacing="0" style={{ borderTop: '1px solid #000' }}>
                        <tbody>
                          <tr>
                            <td width="09%" style={{ fontWeight: 'bold', textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}> &nbsp;Remarks</td>
                            <td width="91%" style={{ textAlign: 'left', color: '#000', fontSize: '8px', height: '12px', lineHeight: '9px' }}>
                              {data.po.remark || (Number(data.po.freight || 0) > 0 ? `Freight inclusive: ₹${data.po.freight}` : 'Freight inclusive')}
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
                                    <td width="50%" style={{ textAlign: 'right', borderTop: '1px solid #000', fontSize: '8px' }}>
                                      For : <b>{data.site_details?.company_name || 'TIRUPATI PLASTOMATICS PVT. LTD.'}</b><br /><br /><br />
                                      <b> {officer.name || ''} &nbsp; <br />
                                      {officer.mobile || ''} &nbsp; <br />
                                      {officer.designation || ''} </b> &nbsp;
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
                            <td width="100%" style={{ textAlign: 'center', fontSize: '8px', fontWeight: 'bold', color: '#000', padding: '2px 0' }}>
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



