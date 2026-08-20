"use client";

import { useState, useEffect } from "react";
import { format } from "date-fns";
import { reverseIndentService, ReverseIndent } from "../../../services/reverseIndent.service";
import { indentpoService } from "../../../services/indentpo.service";
import AsyncSelect from "react-select/async";
import { Search, Plus, Printer, RefreshCw, X, FileSpreadsheet } from "lucide-react";
import Link from "next/link";
import { ContractDetailsModal } from "../../../components/dashboard/ContractDetailsModal";
import { formatDate } from '../../../utils/dateFormatter';
import { DatePicker } from "../../../components/ui/DatePicker";

export default function ReverseIndentListPage() {
  const [indents, setIndents] = useState<ReverseIndent[]>([]);
  const [isLoading, setIsLoading] = useState(false);
  const [selectedIndentId, setSelectedIndentId] = useState<string | null>(null);
  const [selectedContractId, setSelectedContractId] = useState<number | null>(null);

  // Filters
  const [filters, setFilters] = useState({
    contract_id: null as number | null,
    item_id: null as number | null,
    machine_id: null as number | null,
    datefrom: "",
    dateto: ""
  });

  // For AsyncSelect values (to reset them)
  const [contractSelectValue, setContractSelectValue] = useState<any>(null);
  const [productSelectValue, setProductSelectValue] = useState<any>(null);
  const [machineSelectValue, setMachineSelectValue] = useState<any>(null);
  
  const [productOptions, setProductOptions] = useState<any[]>([]);

  useEffect(() => {
    fetchIndents();
  }, []);

  const fetchIndents = async () => {
    setIsLoading(true);
    try {
      const queryParams: any = {};
      if (filters.contract_id) queryParams.contract_id = filters.contract_id;
      if (filters.item_id) queryParams.item_id = filters.item_id;
      if (filters.machine_id) queryParams.machine_id = filters.machine_id;
      if (filters.datefrom) queryParams.datefrom = filters.datefrom;
      if (filters.dateto) queryParams.dateto = filters.dateto;

      const data = await reverseIndentService.listReverseIndents(queryParams);
      setIndents(data);
    } catch (error) {
      alert("Failed to load Reverse Indents");
    } finally {
      setIsLoading(false);
    }
  };

  const loadContracts = (inputValue: string) => {
    return indentpoService.searchContracts(inputValue).then(data => 
      data.map((c: any) => ({ label: `${c.title} (${c.workorder})`, value: c.id }))
    );
  };

  const loadMachines = (inputValue: string) => {
    return indentpoService.searchMachines(inputValue).then(data =>
      data.map((m: any) => ({ label: m.machine_name, value: m.id }))
    );
  };

  const handleContractChange = (selected: any) => {
    setContractSelectValue(selected);
    const cid = selected?.value || null;
    setFilters(prev => ({ ...prev, contract_id: cid, item_id: null }));
    setProductSelectValue(null);
    setProductOptions([]);
    
    if (cid) {
      indentpoService.getContractProducts(cid).then(data => {
        setProductOptions(data.map((p: any) => ({ label: p.item_name, value: p.id })));
      });
    }
  };

  const handleReset = () => {
    setFilters({
      contract_id: null,
      item_id: null,
      machine_id: null,
      datefrom: "",
      dateto: ""
    });
    setContractSelectValue(null);
    setProductSelectValue(null);
    setMachineSelectValue(null);
    setProductOptions([]);
    setTimeout(() => {
      // Small delay to ensure state updates before fetch
      fetchIndents(); // Wait actually I'll just rely on the user clicking search after reset, or I can fetch immediately.
    }, 10);
  };

  // We should fetch indents when handleReset triggers, but let's do it cleanly
  useEffect(() => {
    if (filters.contract_id === null && filters.item_id === null && filters.machine_id === null && filters.datefrom === "" && filters.dateto === "") {
      fetchIndents();
    }
  }, [filters]);

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold tracking-tight text-slate-900">Reverse Manager</h1>
        </div>
        <div className="flex gap-3">
          <Link href="/dashboard/reverse/add">
            <Button>
              <Plus className="mr-2 h-4 w-4" />
              Add
            </Button>
          </Link>
        </div>
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <div className="grid grid-cols-1 md:grid-cols-5 gap-4">
          <div className="space-y-1">
            <label className="text-xs font-semibold text-slate-500">Contract Name</label>
            <AsyncSelect 
              value={contractSelectValue}
              loadOptions={loadContracts}
              onChange={handleContractChange}
              placeholder="Enter Contract Name"
              className="text-sm"
            />
          </div>
          <div className="space-y-1">
            <label className="text-xs font-semibold text-slate-500">Product Name</label>
            <select
              className="w-full px-3 py-[9px] border border-slate-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white"
              value={filters.item_id || ""}
              onChange={(e) => {
                const val = e.target.value ? Number(e.target.value) : null;
                setFilters(prev => ({ ...prev, item_id: val }));
              }}
            >
              <option value="">Enter Product Name</option>
              {productOptions.map((p: any) => (
                <option key={p.value} value={p.value}>{p.label}</option>
              ))}
            </select>
          </div>
          <div className="space-y-1">
            <label className="text-xs font-semibold text-slate-500">Machine Name</label>
            <AsyncSelect 
              value={machineSelectValue}
              loadOptions={loadMachines}
              onChange={(selected: any) => {
                setMachineSelectValue(selected);
                setFilters({ ...filters, machine_id: selected?.value || null })
              }}
              placeholder="Enter Machine Name"
              className="text-sm"
            />
          </div>
          <div className="space-y-1">
            <label className="text-xs font-semibold text-slate-500">Start Date</label>
            <input 
              type="date"
              className="w-full px-3 py-2 border border-slate-300 rounded text-sm focus:outline-none"
              value={filters.datefrom}
              onChange={(e) => setFilters({...filters, datefrom: e.target.value})}
            />
          </div>
          <div className="space-y-1">
            <label className="text-xs font-semibold text-slate-500">End Date</label>
            <input 
              type="date"
              className="w-full px-3 py-2 border border-slate-300 rounded text-sm focus:outline-none"
              value={filters.dateto}
              onChange={(e) => setFilters({...filters, dateto: e.target.value})}
            />
          </div>
        </div>
        
        <div className="mt-4 flex gap-2">
          <Button onClick={fetchIndents} disabled={isLoading}>
            Search
          </Button>
          <Button variant="outline" onClick={handleReset}>
            Reset
          </Button>
          <Button variant="outline" className="ml-auto" title="Export Excel">
             <FileSpreadsheet className="w-5 h-5 text-green-600" />
          </Button>
        </div>
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-sm text-left">
            <thead className="bg-slate-800 text-white border-b border-slate-200">
              <tr>
                <th className="px-4 py-3 font-medium w-16">S No.</th>
                <th className="px-4 py-3 font-medium">Reverse Id</th>
                <th className="px-4 py-3 font-medium">Contract name</th>
                <th className="px-4 py-3 font-medium">Product</th>
                <th className="px-4 py-3 font-medium">Machine Name</th>
                <th className="px-4 py-3 font-medium">Received By</th>
                <th className="px-4 py-3 font-medium">Received Date</th>
                <th className="px-4 py-3 font-medium text-right">Action</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-200">
              {isLoading ? (
                <tr>
                  <td colSpan={8} className="text-center py-8 text-slate-500">
                    <div className="flex items-center justify-center gap-2">
                      <RefreshCw className="w-4 h-4 animate-spin" />
                      Loading...
                    </div>
                  </td>
                </tr>
              ) : indents.length === 0 ? (
                <tr>
                  <td colSpan={8} className="text-center py-12 text-slate-500">
                    No records found
                  </td>
                </tr>
              ) : (
                indents.map((indent, idx) => (
                  <tr key={indent.id} className="hover:bg-slate-50 transition-colors">
                    <td className="px-4 py-3 text-slate-600">{idx + 1}</td>
                    <td className="px-4 py-3 font-medium text-blue-600 cursor-pointer hover:underline" onClick={() => setSelectedIndentId(indent.reverse_id)}>
                      {indent.reverse_id}
                    </td>
                    <td className="px-4 py-3">
                      <div 
                        className="text-blue-600 font-medium cursor-pointer hover:underline uppercase"
                        onClick={() => setSelectedContractId(indent.contract_id)}
                      >
                        {indent.contract_name}({indent.workorder})
                      </div>
                    </td>
                    <td className="px-4 py-3 text-slate-700">{indent.product_name}</td>
                    <td className="px-4 py-3 text-slate-700">{indent.machine_name}</td>
                    <td className="px-4 py-3 text-slate-700 uppercase">{indent.received_name}</td>
                    <td className="px-4 py-3 text-slate-600">
                      {formatDate(indent.issue_date)}
                    </td>
                    <td className="px-4 py-3 text-right">
                      <Link href={`/dashboard/reverse/${indent.reverse_id}`}>
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
        <ReverseDetailsModal 
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

function ReverseDetailsModal({ id, onClose }: { id: string, onClose: () => void }) {
  const [details, setDetails] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    reverseIndentService.getReverseIndentDetails(id).then(data => {
      setDetails(data);
      setLoading(false);
    }).catch(err => {
      alert("Failed to load details");
      onClose();
    });
  }, [id]);

  if (loading) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div className="bg-white rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] flex flex-col">
        <div className="p-4 border-b border-slate-200 flex justify-between items-center">
          <h2 className="text-xl font-bold">Reverse Indent {details?.reverse_id}</h2>
          <div className="flex items-center gap-4">
            <button
              onClick={() => {
                const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:5000/api';
                window.open(`${API_URL}/reverse-indent/${id}/pdf?token=${localStorage.getItem('accessToken')}`, '_blank');
              }}
              className="flex items-center gap-1.5 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded font-bold shadow-sm transition cursor-pointer text-sm"
            >
              <Printer className="w-4 h-4" />
              Print
            </button>
            <button onClick={onClose} className="text-slate-500 hover:text-slate-700">&times;</button>
          </div>
        </div>
        <div className="p-6 overflow-y-auto space-y-6">
          <div className="grid grid-cols-2 gap-4 text-sm">
            <div><span className="font-semibold text-slate-500">Contract:</span> {details?.contract_name}</div>
            <div><span className="font-semibold text-slate-500">Product:</span> {details?.product_name}</div>
            <div><span className="font-semibold text-slate-500">Machine:</span> {details?.machine_name}</div>
            <div><span className="font-semibold text-slate-500">Received By:</span> {details?.received_name}</div>
            <div><span className="font-semibold text-slate-500">Issue Date:</span> {formatDate(details?.issue_date)}</div>
          </div>
          
          <div>
            <h3 className="font-semibold mb-3">Returned Items</h3>
            <table className="w-full text-sm border-collapse border border-slate-200">
              <thead className="bg-slate-50">
                <tr>
                  <th className="border border-slate-200 p-2 text-left">Item Name</th>
                  <th className="border border-slate-200 p-2 text-right">Received Qty</th>
                  <th className="border border-slate-200 p-2 text-left">UOM</th>
                </tr>
              </thead>
              <tbody>
                {details?.items?.map((item: any, idx: number) => (
                  <tr key={idx}>
                    <td className="border border-slate-200 p-2">{item.item_name}</td>
                    <td className="border border-slate-200 p-2 text-right">{item.quantity}</td>
                    <td className="border border-slate-200 p-2">{item.uom}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
}
