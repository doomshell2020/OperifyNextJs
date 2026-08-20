"use client";

import { useState, useEffect } from "react";
import { useParams, useRouter } from "next/navigation";
import { reverseIndentService } from "../../../../../services/reverseIndent.service";
import { Printer, ArrowLeft, Trash2, Download } from "lucide-react";
import Link from "next/link";
import { formatDate } from '../../../../../utils/dateFormatter';

export default function ViewReverseIndentPage() {
  const { id } = useParams();
  const router = useRouter();
  const [details, setDetails] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (id) {
      reverseIndentService.getReverseIndentDetails(id as string).then(data => {
        setDetails(data);
        setLoading(false);
      }).catch(err => {
        alert("Failed to load details");
        setLoading(false);
      });
    }
  }, [id]);

  const handlePrint = () => {
    window.print();
  };

  const handleDelete = async () => {
    if (confirm("Are you sure you want to delete this reverse indent? This will reverse the stock adjustments.")) {
      try {
        await reverseIndentService.deleteReverseIndent(id as string);
        router.push("/dashboard/reverse");
      } catch (e) {
        alert("Failed to delete reverse indent");
      }
    }
  };

  if (loading) return <div>Loading...</div>;
  if (!details) return <div>Not found.</div>;

  return (
    <div className="space-y-6 max-w-4xl mx-auto">
      <div className="flex items-center justify-between print:hidden">
        <div>
          <h1 className="text-2xl font-bold tracking-tight text-slate-900">View Reverse Indent</h1>
        </div>
        <div className="flex gap-3">
          <Link href="/dashboard/reverse">
            <button className="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200">
              <ArrowLeft className="w-4 h-4 mr-2" />
              Back
            </button>
          </Link>
          <button onClick={handlePrint} className="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <Printer className="w-4 h-4 mr-2" />
            Print
          </button>
          <button onClick={handleDelete} className="inline-flex items-center px-4 py-2 bg-red-50 text-red-600 border border-red-200 rounded-lg hover:bg-red-100">
            <Trash2 className="w-4 h-4 mr-2" />
            Delete
          </button>
        </div>
      </div>

      <div className="bg-white p-8 rounded-xl shadow-sm border border-slate-200 print:shadow-none print:border-none print:p-0">
        <div className="text-center mb-8 border-b border-slate-200 pb-4 print:border-b-2 print:border-black">
          <h2 className="text-2xl font-bold uppercase tracking-wider">Reverse Indent</h2>
          <p className="text-slate-500 mt-1">Return Note / Stock Reversal</p>
        </div>

        <div className="grid grid-cols-2 gap-8 mb-8">
          <div className="space-y-2">
            <div className="flex"><span className="w-32 font-semibold text-slate-500 print:text-black">Reverse ID:</span> <span>{details.reverse_id}</span></div>
            <div className="flex"><span className="w-32 font-semibold text-slate-500 print:text-black">Date:</span> <span>{formatDate(details.issue_date)}</span></div>
            <div className="flex"><span className="w-32 font-semibold text-slate-500 print:text-black">Contract:</span> <span>{details.contract_name}</span></div>
          </div>
          <div className="space-y-2">
            <div className="flex"><span className="w-32 font-semibold text-slate-500 print:text-black">Product:</span> <span>{details.product_name}</span></div>
            <div className="flex"><span className="w-32 font-semibold text-slate-500 print:text-black">Machine:</span> <span>{details.machine_name}</span></div>
            <div className="flex"><span className="w-32 font-semibold text-slate-500 print:text-black">Received By:</span> <span>{details.received_name}</span></div>
          </div>
        </div>

        <div className="mb-12">
          <table className="w-full text-left border-collapse border border-slate-200 print:border-black">
            <thead className="bg-slate-50 print:bg-white print:border-b-2 print:border-black">
              <tr>
                <th className="border border-slate-200 print:border-black p-3 font-semibold text-slate-800 print:text-black">S.No.</th>
                <th className="border border-slate-200 print:border-black p-3 font-semibold text-slate-800 print:text-black">Raw Material</th>
                <th className="border border-slate-200 print:border-black p-3 font-semibold text-slate-800 print:text-black text-right">Received Qty</th>
                <th className="border border-slate-200 print:border-black p-3 font-semibold text-slate-800 print:text-black">UOM</th>
              </tr>
            </thead>
            <tbody>
              {details.items?.map((item: any, idx: number) => (
                <tr key={idx}>
                  <td className="border border-slate-200 print:border-black p-3 w-16">{idx + 1}</td>
                  <td className="border border-slate-200 print:border-black p-3">{item.item_name}</td>
                  <td className="border border-slate-200 print:border-black p-3 text-right font-medium">{item.quantity}</td>
                  <td className="border border-slate-200 print:border-black p-3 w-24">{item.uom}</td>
                </tr>
              ))}
              {(!details.items || details.items.length === 0) && (
                <tr>
                  <td colSpan={4} className="border border-slate-200 p-4 text-center text-slate-500">No items found.</td>
                </tr>
              )}
            </tbody>
          </table>
        </div>

        <div className="grid grid-cols-3 gap-8 mt-24 text-center">
          <div>
            <div className="border-t border-slate-300 print:border-black pt-2 font-medium">Prepared By</div>
          </div>
          <div>
            <div className="border-t border-slate-300 print:border-black pt-2 font-medium">Store Incharge</div>
          </div>
          <div>
            <div className="border-t border-slate-300 print:border-black pt-2 font-medium">Authorized Signatory</div>
          </div>
        </div>
      </div>
    </div>
  );
}
