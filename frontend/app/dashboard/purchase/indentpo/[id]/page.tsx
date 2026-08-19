"use client";

import { useEffect, useState } from "react";
import { useParams, useRouter } from "next/navigation";
import { indentpoService } from "../../../../../services/indentpo.service";
import { ArrowLeft, Printer, RefreshCw } from "lucide-react";
import Link from "next/link";
import { format } from "date-fns";
import { formatDate } from '../../../../../utils/dateFormatter';

export default function IndentPoDetailPage() {
  const params = useParams();
  const router = useRouter();
  const indentId = params.id as string;
  
  const [detail, setDetail] = useState<any>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    if (indentId) {
      fetchDetail();
    }
  }, [indentId]);

  const fetchDetail = async () => {
    setIsLoading(true);
    try {
      const data = await indentpoService.getIndentpoDetail(indentId);
      setDetail(data);
    } catch (err: any) {
      setError("Failed to load Indent PO details");
    } finally {
      setIsLoading(false);
    }
  };

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <RefreshCw className="w-8 h-8 text-blue-500 animate-spin" />
      </div>
    );
  }

  if (error || !detail) {
    return (
      <div className="flex flex-col items-center justify-center h-64 space-y-4">
        <p className="text-red-500 font-medium">{error || "Indent PO not found"}</p>
        <Link href="/dashboard/purchase/indentpo">
          <button className="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200">
            Go Back
          </button>
        </Link>
      </div>
    );
  }

  const handlePrint = () => {
    window.print();
  };

  return (
    <div className="max-w-5xl mx-auto space-y-6 pb-20">
      {/* Non-printable header */}
      <div className="flex items-center justify-between print:hidden">
        <div className="flex items-center gap-4">
          <Link href="/dashboard/purchase/indentpo">
            <button className="p-2 hover:bg-slate-100 rounded-full transition-colors text-slate-500">
              <ArrowLeft className="w-5 h-5" />
            </button>
          </Link>
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Indent PO Details</h1>
          </div>
        </div>
        <button
          onClick={handlePrint}
          className="inline-flex items-center justify-center rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-slate-800"
        >
          <Printer className="w-4 h-4 mr-2" />
          Print / PDF
        </button>
      </div>

      {/* Printable Area */}
      <div className="bg-white p-8 sm:p-12 border border-slate-200 shadow-sm print:shadow-none print:border-none print:p-0">
        {/* Header */}
        <div className="text-center mb-8 border-b-2 border-slate-800 pb-6">
          <h1 className="text-2xl font-bold text-slate-900 uppercase tracking-wider">Stock Issue / Indent PO</h1>
          <p className="text-slate-500 mt-1">Operify ERP System</p>
        </div>

        <div className="grid grid-cols-2 gap-8 mb-8">
          <div className="space-y-3">
            <div className="grid grid-cols-3">
              <span className="font-semibold text-slate-700">Indent No:</span>
              <span className="col-span-2 font-bold text-slate-900">{detail.indent_id}</span>
            </div>
            <div className="grid grid-cols-3">
              <span className="font-semibold text-slate-700">Issue Date:</span>
              <span className="col-span-2">{formatDate(detail.issue_date)}</span>
            </div>
            <div className="grid grid-cols-3">
              <span className="font-semibold text-slate-700">Issued To:</span>
              <span className="col-span-2 uppercase">{detail.issued_name}</span>
            </div>
          </div>
          
          <div className="space-y-3">
            <div className="grid grid-cols-3">
              <span className="font-semibold text-slate-700">Contract:</span>
              <span className="col-span-2">{detail.contract_name}</span>
            </div>
            <div className="grid grid-cols-3">
              <span className="font-semibold text-slate-700">Workorder:</span>
              <span className="col-span-2">{detail.workorder}</span>
            </div>
            <div className="grid grid-cols-3">
              <span className="font-semibold text-slate-700">Machine:</span>
              <span className="col-span-2">{detail.machine_name}</span>
            </div>
          </div>
        </div>

        <div className="mb-6 bg-slate-50 p-4 border border-slate-200 rounded-lg">
          <div className="flex">
            <span className="font-semibold text-slate-700 mr-2">Finished Product:</span>
            <span className="font-bold text-slate-900">{detail.product_name}</span>
          </div>
        </div>

        {/* Details Table */}
        <table className="w-full text-sm text-left border border-slate-300">
          <thead className="bg-slate-100 text-slate-800 border-b-2 border-slate-300">
            <tr>
              <th className="px-4 py-3 font-bold border-r border-slate-300">S.No</th>
              <th className="px-4 py-3 font-bold border-r border-slate-300 w-1/2">Raw Material</th>
              <th className="px-4 py-3 font-bold border-r border-slate-300 text-center">Unit</th>
              <th className="px-4 py-3 font-bold border-r border-slate-300 text-center">Design Qty</th>
              <th className="px-4 py-3 font-bold text-right bg-slate-200">Issued Qty</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-300">
            {detail.items?.map((item: any, idx: number) => (
              <tr key={idx}>
                <td className="px-4 py-3 border-r border-slate-300 text-center">{idx + 1}</td>
                <td className="px-4 py-3 border-r border-slate-300 font-medium">{item.raw_material_name}</td>
                <td className="px-4 py-3 border-r border-slate-300 text-center">{item.unit_name}</td>
                <td className="px-4 py-3 border-r border-slate-300 text-center">{item.design_qty}</td>
                <td className="px-4 py-3 text-right font-bold bg-slate-50">{item.quantity}</td>
              </tr>
            ))}
          </tbody>
        </table>

        {/* Signatures */}
        <div className="grid grid-cols-3 gap-8 mt-24 pt-8 border-t border-slate-300 text-center text-sm font-semibold text-slate-700">
          <div>Prepared By</div>
          <div>Store Manager</div>
          <div>Receiver Sign</div>
        </div>

        {/* Footer */}
        <div className="mt-8 text-center text-xs text-slate-400">
          Created By: {detail.created_by || 'System'} | Created At: {format(new Date(detail.created), "dd/MM/yyyy HH:mm")}
        </div>
      </div>
    </div>
  );
}
