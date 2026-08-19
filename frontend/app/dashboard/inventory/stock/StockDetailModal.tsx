import { formatDate } from '../../../../utils/dateFormatter';
import React, { useEffect, useState } from 'react';
import { X, Loader2 } from 'lucide-react';
import { stockRegisterService } from '../../../../services/stockRegister.service';

interface Props {
  type: 'received' | 'dispatched';
  date: string;
  productId: string;
  productName: string;
  onClose: () => void;
}

export function StockDetailModal({ type, date, productId, productName, onClose }: Props) {
  const [loading, setLoading] = useState(true);
  const [data, setData] = useState<any[]>([]);
  const [error, setError] = useState('');

  useEffect(() => {
    fetchData();
  }, [type, date, productId]);

  const fetchData = async () => {
    try {
      setLoading(true);
      if (type === 'received') {
        const res = await stockRegisterService.getReceivedStockDetails({ date, product_id: productId });
        setData(res);
      } else {
        const res = await stockRegisterService.getDispatchedStockDetails({ date, product_id: productId });
        setData(res);
      }
    } catch (err: any) {
      setError(err?.response?.data?.message || 'Failed to fetch details');
    } finally {
      setLoading(false);
    }
  };

  const formatDate = (dateStr: string) => {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    return isNaN(d.getTime()) ? dateStr : `${String(d.getDate()).padStart(2, '0')}-${String(d.getMonth() + 1).padStart(2, '0')}-${d.getFullYear()}`;
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
      <div className="bg-white rounded-xl shadow-2xl w-full max-w-5xl flex flex-col overflow-hidden max-h-[90vh]">
        <div className="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
          <div>
            <h2 className="text-xl font-bold text-slate-800">
              {type === 'received' ? 'Received Item Name' : 'Dispatched Item Name'} - <span className="text-cyan-700">{productName}</span>
            </h2>
            <p className="text-sm text-slate-500 mt-1">Transaction Date: {formatDate(date)}</p>
          </div>
          <button onClick={onClose} className="p-2 hover:bg-slate-200 rounded-lg text-slate-500 transition">
            <X className="w-5 h-5" />
          </button>
        </div>

        <div className="p-6 overflow-y-auto">
          {loading ? (
            <div className="flex flex-col items-center justify-center py-12 text-slate-500">
              <Loader2 className="w-8 h-8 animate-spin text-cyan-600 mb-4" />
              <p>Loading details...</p>
            </div>
          ) : error ? (
            <div className="p-4 bg-red-50 text-red-600 rounded-lg border border-red-200">
              {error}
            </div>
          ) : (
            <table className="w-full text-left text-sm text-slate-600 border-collapse border border-slate-200">
              <thead className="bg-slate-100 text-slate-700 font-semibold">
                {type === 'received' ? (
                  <tr>
                    <th className="p-3 border border-slate-200">S.No.</th>
                    <th className="p-3 border border-slate-200">PO ID / Genrated Date</th>
                    <th className="p-3 border border-slate-200">Vendor Detail</th>
                    <th className="p-3 border border-slate-200">Inward Date</th>
                    <th className="p-3 border border-slate-200">Bill No. / Bill Date</th>
                    <th className="p-3 border border-slate-200">Received Qty</th>
                  </tr>
                ) : (
                  <tr>
                    <th className="p-3 border border-slate-200">S.No.</th>
                    <th className="p-3 border border-slate-200">Indent ID / JC ID</th>
                    <th className="p-3 border border-slate-200">Issue Quantity</th>
                    <th className="p-3 border border-slate-200">Issue Date</th>
                    <th className="p-3 border border-slate-200">Indent Status / JC Status</th>
                  </tr>
                )}
              </thead>
              <tbody>
                {data.length === 0 ? (
                  <tr>
                    <td colSpan={6} className="p-6 text-center text-slate-500">No records found for this date.</td>
                  </tr>
                ) : (
                  data.map((row, i) => (
                    <tr key={i} className="hover:bg-slate-50 border-b border-slate-200">
                      <td className="p-3 border border-slate-200">{i + 1}</td>
                      
                      {type === 'received' ? (
                        <>
                          <td className="p-3 border border-slate-200">
                            {row.po_id ? `${row.po_id} / ${formatDate(row.po_date)}` : 'N/A'}
                            {row.is_revised ? <span className="text-red-500 text-xs ml-1 block">Revised-{row.is_revised}</span> : null}
                          </td>
                          <td className="p-3 border border-slate-200">
                            {row.vendor_name ? (
                              <>
                                {row.vendor_name}<br/>
                                <b>Contact No.</b> {row.contact_no}<br/>
                                <b>Email</b> {row.email}
                              </>
                            ) : row.sc_name ? (
                              <>
                                {row.sc_name}<br/>
                                <b>Contact No.</b> {row.sc_mobile}<br/>
                                <b>Email</b> {row.sc_email}
                              </>
                            ) : '-'}
                          </td>
                          <td className="p-3 border border-slate-200">{formatDate(row.inwarddate || row.issue_date)}</td>
                          <td className="p-3 border border-slate-200">{row.bill_no ? `${row.bill_no} / ${formatDate(row.inwarddate)}` : '-'}</td>
                          <td className="p-3 border border-slate-200 font-medium text-cyan-700">{row.quantity}</td>
                        </>
                      ) : (
                        <>
                          <td className="p-3 border border-slate-200">{row.indent_id || '-'}</td>
                          <td className="p-3 border border-slate-200">
                            {row.quantity}<br/>
                            {row.subcontractor_name && (
                              <div className="mt-1 text-xs">
                                <b>Sub Contractors Name:</b> {row.subcontractor_name}<br/>
                                <b>Mobile No.:</b> {row.subcontractor_mobile}<br/>
                                <b>Email:</b> {row.subcontractor_email}
                              </div>
                            )}
                          </td>
                          <td className="p-3 border border-slate-200">{formatDate(row.issue_date || row.created)}</td>
                          <td className="p-3 border border-slate-200">
                            {row.indent_status === 'P' ? (
                              <span className="text-red-500 font-bold">Pending</span>
                            ) : row.indent_status === 'C' ? (
                              <span className="text-green-600">Completed</span>
                            ) : row.indent_status || 'Completed'}
                          </td>
                        </>
                      )}
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          )}
        </div>
        
        <div className="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-end">
          <button onClick={onClose} className="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg font-semibold transition">
            Close
          </button>
        </div>
      </div>
    </div>
  );
}
