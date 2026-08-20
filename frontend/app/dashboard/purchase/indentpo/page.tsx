"use client";

import { useState, useEffect } from "react";
import { format } from "date-fns";
import { indentpoService, Indentpo } from "../../../../services/indentpo.service";
import { Search, Plus, Printer, RefreshCw, Eye } from "lucide-react";
import Link from "next/link";
import { ContractDetailsModal } from "../../../../components/dashboard/ContractDetailsModal";
import { formatQty } from "@/utils/formatters";
import { formatDate } from '../../../../utils/dateFormatter';

export default function IndentPoListPage() {
  const [indents, setIndents] = useState<Indentpo[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState("");
  const [selectedIndentId, setSelectedIndentId] = useState<number | null>(null);
  const [selectedContractId, setSelectedContractId] = useState<number | null>(null);

  useEffect(() => {
    fetchIndents();
  }, []);

  const fetchIndents = async () => {
    setIsLoading(true);
    try {
      const data = await indentpoService.listIndentpo();
      setIndents(data);
    } catch (error) {
      alert("Failed to load Indent POs");
    } finally {
      setIsLoading(false);
    }
  };

  const filteredIndents = indents.filter((indent) => {
    const searchLower = searchTerm.toLowerCase();
    return (
      indent.indent_id?.toLowerCase().includes(searchLower) ||
      indent.contract_name?.toLowerCase().includes(searchLower) ||
      indent.product_name?.toLowerCase().includes(searchLower) ||
      indent.machine_name?.toLowerCase().includes(searchLower) ||
      indent.issued_name?.toLowerCase().includes(searchLower)
    );
  });

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold tracking-tight text-slate-900">Indent PO / Stock Issue</h1>
          <p className="text-sm text-slate-500 mt-1">Manage production issues and raw materials.</p>
        </div>
        <div className="flex gap-3">
          <Button onClick={fetchIndents} variant="outline" disabled={isLoading}>
            <RefreshCw className={`w-4 h-4 mr-2 ${isLoading ? 'animate-spin' : ''}`} />
            Refresh
          </Button>
          <Link href="/dashboard/purchase/indentpo/new">
            <Button>
              <Plus className="mr-2 h-4 w-4" />
              Create Indent PO
            </Button>
          </Link>
        </div>
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div className="p-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
          <h2 className="text-lg font-semibold text-slate-800">All Issued Indents</h2>
          <div className="relative w-72">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
            <input
              type="text"
              placeholder="Search indents..."
              className="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
            />
          </div>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full text-sm text-left">
            <thead className="bg-slate-50 text-slate-500 border-b border-slate-200">
              <tr>
                <th className="px-6 py-4 font-medium">Indent No</th>
                <th className="px-6 py-4 font-medium">Issue Date</th>
                <th className="px-6 py-4 font-medium">Contract</th>
                <th className="px-6 py-4 font-medium">Product</th>
                <th className="px-6 py-4 font-medium">Machine</th>
                <th className="px-6 py-4 font-medium">Issued To</th>
                <th className="px-6 py-4 font-medium text-right">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-200">
              {isLoading ? (
                <tr>
                  <td colSpan={7} className="text-center py-8 text-slate-500">
                    <div className="flex items-center justify-center gap-2">
                      <RefreshCw className="w-4 h-4 animate-spin" />
                      Loading...
                    </div>
                  </td>
                </tr>
              ) : filteredIndents.length === 0 ? (
                <tr>
                  <td colSpan={7} className="text-center py-12 text-slate-500">
                    <div className="flex flex-col items-center justify-center">
                      <Search className="w-8 h-8 text-slate-300 mb-3" />
                      <p>No indents found matching your criteria</p>
                    </div>
                  </td>
                </tr>
              ) : (
                filteredIndents.map((indent) => (
                  <tr key={indent.id} className="hover:bg-slate-50 transition-colors">
                    <td className="px-6 py-4 font-medium text-blue-600 cursor-pointer hover:underline" onClick={() => setSelectedIndentId(indent.id)}>
                      {indent.indent_id}
                    </td>
                    <td className="px-6 py-4 text-slate-600">
                      {formatDate(indent.issue_date)}
                    </td>
                    <td className="px-6 py-4">
                      <div 
                        className="text-blue-600 font-medium cursor-pointer hover:underline"
                        onClick={() => setSelectedContractId(indent.contract_id)}
                      >
                        {indent.contract_name}
                      </div>
                      <div className="text-xs text-slate-500 mt-1">{indent.workorder}</div>
                    </td>
                    <td className="px-6 py-4 text-slate-700">{indent.product_name}</td>
                    <td className="px-6 py-4 text-slate-700">{indent.machine_name}</td>
                    <td className="px-6 py-4 text-slate-700">{indent.issued_name}</td>
                    <td className="px-6 py-4 text-right">
                      <Link href={`/dashboard/purchase/indentpo/${indent.indent_id}`}>
                        <button className="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View / Print">
                          <Printer className="h-4 w-4" />
                        </button>
                      </Link>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>

      {selectedIndentId && (
        <IndentDetailsModal 
          id={selectedIndentId} 
          onClose={() => setSelectedIndentId(null)} 
        />
      )}

      {selectedContractId && (
        <ContractDetailsModal
          contractId={selectedContractId}
          onClose={() => setSelectedContractId(null)}
        />
      )}
    </div>
  );
}

// Simple button component to replace shadcn/ui Button
function Button({ children, onClick, variant = 'primary', disabled, className = '' }: any) {
  const baseStyles = "inline-flex items-center justify-center rounded-lg text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none px-4 py-2";
  const variants = {
    primary: "bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500",
    outline: "border border-slate-300 bg-transparent hover:bg-slate-50 text-slate-700 focus:ring-slate-500",
    ghost: "bg-transparent hover:bg-slate-100 text-slate-700",
  };
  
  return (
    <button 
      onClick={onClick}
      disabled={disabled}
      className={`${baseStyles} ${variants[variant as keyof typeof variants]} ${className}`}
    >
      {children}
    </button>
  );
}

function IndentDetailsModal({ id, onClose }: { id: number, onClose: () => void }) {
  const [details, setDetails] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    indentpoService.getIndentPoDetails(id).then(data => {
      setDetails(data);
      setLoading(false);
    }).catch(err => {
      alert("Failed to load details");
      onClose();
    });
  }, [id]);

  const handlePrint = () => {
    const printContent = document.getElementById('printable-indent-modal');
    if (printContent) {
      const originalContents = document.body.innerHTML;
      document.body.innerHTML = printContent.innerHTML;
      window.print();
      document.body.innerHTML = originalContents;
      window.location.reload();
    }
  };

  return (
    <div className="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
      <div className="bg-white rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col">
        {loading ? (
          <div className="p-12 flex justify-center"><RefreshCw className="w-8 h-8 animate-spin text-blue-600" /></div>
        ) : details ? (
          <>
            <div className="flex justify-between items-center p-4 border-b">
              <h2 className="text-xl font-bold">Indent Details</h2>
              <div className="flex gap-2">
                <Button onClick={handlePrint} variant="primary">
                  <Printer className="w-4 h-4 mr-2" />
                  Print
                </Button>
                <button onClick={onClose} className="p-2 hover:bg-slate-100 rounded-full">✕</button>
              </div>
            </div>
            
            <div className="p-6 overflow-y-auto" id="printable-indent-modal">
              <div className="grid grid-cols-2 gap-4 mb-8 text-sm">
                <div>
                  <div className="font-semibold text-slate-500">Indent Id :- <span className="font-normal text-slate-900">{details.header.indent_id}</span></div>
                  <div className="font-semibold text-slate-500 mt-2">Product :- <span className="font-normal text-slate-900">{details.header.product_name}</span></div>
                  <div className="font-semibold text-slate-500 mt-2">Created By :- <span className="font-normal text-slate-900 capitalize">{details.header.created_by || '-'}</span></div>
                  <div className="font-semibold text-slate-500 mt-2">Issue Date :- <span className="font-normal text-slate-900">{formatDate(details.header.issue_date)}</span></div>
                </div>
                <div>
                  <div className="font-semibold text-slate-500">Contract name :- <span className="font-normal text-slate-900">{details.header.contract_name}({details.header.workorder})</span></div>
                  <div className="font-semibold text-slate-500 mt-2">Machine Name :- <span className="font-normal text-slate-900">{details.header.machine_name}</span></div>
                  <div className="font-semibold text-slate-500 mt-2">Issue By :- <span className="font-normal text-slate-900">{details.header.issued_name}</span></div>
                </div>
              </div>
              
              <h3 className="text-center font-bold text-lg mb-4">Raw Material</h3>
              <table className="w-full text-sm text-left border">
                <thead className="bg-slate-50 border-b">
                  <tr>
                    <th className="px-4 py-2 border-r w-16">S.No.</th>
                    <th className="px-4 py-2 border-r">Item</th>
                    <th className="px-4 py-2 border-r text-right w-32">Issue Qty</th>
                    <th className="px-4 py-2 w-24">UOM</th>
                  </tr>
                </thead>
                <tbody>
                  {details.items?.map((item: any, idx: number) => (
                    <tr key={idx} className="border-b last:border-0">
                      <td className="px-4 py-2 border-r">{idx + 1}.</td>
                      <td className="px-4 py-2 border-r font-medium">{item.item_name}</td>
                      <td className="px-4 py-2 border-r text-right">{item.issue_qty ? formatQty(item.issue_qty) : 0}</td>
                      <td className="px-4 py-2">{item.uom}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </>
        ) : null}
      </div>
    </div>
  );
}
