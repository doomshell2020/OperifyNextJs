"use client";

import { useLegacyActionAccess } from '@/components/ui/useLegacyActionAccess';
import {LegacyPageHeader} from '@/components/ui/LegacyPageHeader';
import { useState, useEffect, useRef } from "react";
import { reverseIndentService, ReverseIndent } from "../../../services/reverseIndent.service";
import AsyncSelect from "react-select/async";
import { Search, Plus, Printer, RefreshCw, X, FileSpreadsheet } from "lucide-react";
import Link from "next/link";
import { ContractDetailsModal } from "../../../components/dashboard/ContractDetailsModal";
import { formatDate, formatContractDate } from '../../../utils/dateFormatter';
import { DatePicker } from "../../../components/ui/DatePicker";
import { openModulePdf } from '@/services/pdf.service';
import { isLegacyToday } from '@/utils/legacyActionDate';
import { useRouter } from 'next/navigation';

export default function ReverseIndentListPage() {
  const canAction = useLegacyActionAccess();
  const router = useRouter();
  const [indents, setIndents] = useState<ReverseIndent[]>([]);
  const [isLoading, setIsLoading] = useState(false);
  const [selectedIndentId, setSelectedIndentId] = useState<string | null>(null);
  const [selectedContractId, setSelectedContractId] = useState<number | null>(null);
  const [page, setPage] = useState(1);
  const [total, setTotal] = useState(0);
  const appliedFilters = useRef<any>({});
  const fetchGeneration = useRef(0);

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
  
  useEffect(() => {
    const params=new URLSearchParams(window.location.search);
    const initial={contract_id:params.get('contract_id')?Number(params.get('contract_id')):null,
      item_id:params.get('item_id')?Number(params.get('item_id')):null,machine_id:params.get('machine_id')?Number(params.get('machine_id')):null,
      datefrom:params.get('datefrom') || '',dateto:params.get('dateto') || ''};
    setFilters(initial);
    for(const [kind,value,setValue] of [['contracts',initial.contract_id,setContractSelectValue],['products',initial.item_id,setProductSelectValue],['machines',initial.machine_id,setMachineSelectValue]] as const) {
      if(value) void reverseIndentService.lookup(kind).then(rows=>{const row=rows.find((r: any)=>Number(r.id)===value);if(row)setValue({value,label:kind==='contracts'?`${row.title} (${row.workorder})`:kind==='machines'?row.machine_name:row.item_name});}).catch(()=>{});
    }
    void fetchIndents(Number(params.get('page')) || 1,{...initial,date_mode:params.get('date_mode') || ''});
  }, []);

  const fetchIndents = async (nextPage = 1, selectedFilters: any = filters) => {
    const request=++fetchGeneration.current;
    setIsLoading(true);
    try {
      const queryParams: any = {page: nextPage, limit: 50};
      Object.entries(selectedFilters).forEach(([key,value])=>{if(value) queryParams[key]=value;});

      const data = await reverseIndentService.listReverseIndents(queryParams);
      if(request!==fetchGeneration.current) return;
      setIndents(data.data); setTotal(data.total); setPage(nextPage); appliedFilters.current=selectedFilters;
      const params=new URLSearchParams();
      Object.entries(queryParams).forEach(([key,value])=>{if(key!=='limit' && value)params.set(key,String(value));});
      router.replace(`/dashboard/reverse?${params}`,{scroll:false});
    } catch (error) {
      alert("Failed to load Reverse Indents");
    } finally {
      if(request===fetchGeneration.current) setIsLoading(false);
    }
  };

  const loadContracts = (inputValue: string) => {
    return reverseIndentService.lookup('contracts',inputValue).then(data =>
      data.map((c: any) => ({ label: `${c.title} (${c.workorder})`, value: c.id }))
    );
  };

  const loadMachines = (inputValue: string) => {
    return reverseIndentService.lookup('machines',inputValue).then(data =>
      data.map((m: any) => ({ label: m.machine_name, value: m.id }))
    );
  };

  const handleContractChange = (selected: any) => {
    setContractSelectValue(selected);
    const cid = selected?.value || null;
    setFilters(prev => ({ ...prev, contract_id: cid }));
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
    void fetchIndents(1, {});
  };

  const loadProducts = async (q: string) => (await reverseIndentService.lookup('products',q)).map((row: any)=>({label:row.item_name,value:row.id}));
  const deleteIndent = async (id: string) => {
    if(!confirm('Are you sure do you want to delete this Indent?')) return;
    try { await reverseIndentService.deleteReverseIndent(id); await fetchIndents(page,appliedFilters.current); }
    catch(e: any) {alert(e?.response?.data?.error?.message || 'Failed to delete Reverse Indent.');}
  };

  return (
    <div className="space-y-6">
<LegacyPageHeader title="Reverse Manager"/>
      <div className="legacy-page-actions"><div className="flex gap-3">
          {canAction('reverseindent','add') && <Link href="/dashboard/reverse/add">
            <Button>
              <Plus className="mr-2 h-4 w-4" />
              Add
            </Button>
          </Link>}
        </div></div>

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
            <AsyncSelect value={productSelectValue} loadOptions={loadProducts} isClearable
              onChange={(row: any)=>{setProductSelectValue(row);setFilters(old=>({...old,item_id:row?.value || null}));}}
              placeholder="Enter Product Name" className="text-sm" />
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
            <DatePicker 
              dateFormat="dd-MM-yyyy"
              name="datefrom"
              className="w-full px-3 py-2 border border-slate-300 rounded text-sm focus:outline-none"
              value={filters.datefrom}
              onChange={(e: any) => setFilters({...filters, datefrom: e.target.value})}
            />
          </div>
          <div className="space-y-1">
            <label className="text-xs font-semibold text-slate-500">End Date</label>
            <DatePicker 
              dateFormat="dd-MM-yyyy"
              name="dateto"
              className="w-full px-3 py-2 border border-slate-300 rounded text-sm focus:outline-none"
              value={filters.dateto}
              onChange={(e: any) => setFilters({...filters, dateto: e.target.value})}
            />
          </div>
        </div>
        
        <div className="mt-4 flex gap-2">
          <Button onClick={()=>void fetchIndents(1,{...filters,date_mode:'search'})} disabled={isLoading}>
            Search
          </Button>
          <Button variant="outline" onClick={handleReset}>
            Reset
          </Button>
          {canAction('reverseindent','excel') && <Button variant="outline" className="ml-auto" title="Export Excel" onClick={()=>void reverseIndentService.exportExcel(appliedFilters.current).catch(()=>alert('Failed to export Reverse Indents.'))}>
             <FileSpreadsheet className="w-5 h-5 text-green-600" />
          </Button>}
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
                    <td className="px-4 py-3 text-slate-600">{(page-1)*50 + idx + 1}</td>
                    <td className="px-4 py-3 font-medium text-blue-600 cursor-pointer hover:underline" onClick={() => { if(canAction('reverseindent','viewreverseindent')) setSelectedIndentId(indent.reverse_id); }}>
                      {indent.reverse_id}
                      {indent.status==='draft' && <span className="ml-2 text-xs text-amber-700">{indent.draft_stock_clean?'Draft':'Review required'}</span>}
                    </td>
                    <td className="px-4 py-3">
                      <div 
                        className="text-blue-600 font-medium cursor-pointer hover:underline uppercase"
                        onClick={() => { if(canAction('production','viewcontractdetail')) setSelectedContractId(indent.contract_id); }}
                      >
                        {indent.contract_name}({indent.workorder})
                      </div>
                    </td>
                    <td className="px-4 py-3 text-slate-700">{indent.product_name}</td>
                    <td className="px-4 py-3 text-slate-700">{indent.machine_name}</td>
                    <td className="px-4 py-3 text-slate-700 uppercase">{indent.received_name}</td>
                    <td className="px-4 py-3 text-slate-600">
                      {formatContractDate(indent.issue_date)}
                    </td>
                    <td className="px-4 py-3 text-right">
                      {isLegacyToday(indent.issue_date) && indent.status==='draft' && !!indent.draft_stock_clean && canAction('reverseindent','edit') && <Link className="text-green-700 mr-3" href={`/dashboard/reverse/${indent.reverse_id}/edit`}>Edit</Link>}
                      {isLegacyToday(indent.issue_date) && canAction('reverseindent','delete') && <button className="text-red-600 mr-3" onClick={()=>void deleteIndent(indent.reverse_id)}>Delete</button>}
                      {canAction('reverseindent','viewreverseindent') && <Link href={`/dashboard/reverse/${indent.reverse_id}`}>
                        <button className="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View / Print">
                          <Printer className="h-4 w-4" />
                        </button>
                      </Link>}
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>

      <div className="flex items-center justify-between p-4 border text-sm bg-white">
        <span>{total} records · Page {page} of {Math.max(1,Math.ceil(total/50))}</span>
        <div className="flex gap-3"><Button variant="outline" disabled={isLoading || page<=1} onClick={()=>void fetchIndents(page-1,appliedFilters.current)}>Previous</Button><Button variant="outline" disabled={isLoading || page*50>=total} onClick={()=>void fetchIndents(page+1,appliedFilters.current)}>Next</Button></div>
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
  const canAction = useLegacyActionAccess();
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
            {canAction('reverseindent','viewreverseindentpdf') && <button
              onClick={() => {
                void openModulePdf(`/reverse-indent/${encodeURIComponent(id)}/pdf`);
              }}
              className="flex items-center gap-1.5 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded font-bold shadow-sm transition cursor-pointer text-sm"
            >
              <Printer className="w-4 h-4" />
              Print
            </button>}
            <button onClick={onClose} className="text-slate-500 hover:text-slate-700">&times;</button>
          </div>
        </div>
        <div className="p-6 overflow-y-auto space-y-6">
          <div className="grid grid-cols-2 gap-4 text-sm">
            <div><span className="font-semibold text-slate-500">Contract:</span> {details?.contract_name}({details?.workorder})</div>
            <div><span className="font-semibold text-slate-500">Product:</span> {details?.product_name}</div>
            <div><span className="font-semibold text-slate-500">Machine:</span> {details?.machine_name}</div>
            <div><span className="font-semibold text-slate-500">Received By:</span> {details?.received_name}</div>
            <div><span className="font-semibold text-slate-500">Issue Date:</span> {formatContractDate(details?.issue_date)}</div>
            {details?.updated && <div><span className="font-semibold text-slate-500">Last Updated Date:</span> {formatContractDate(details.updated)}</div>}
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
                    <td className="border border-slate-200 p-2 text-right">{Number(item.quantity).toFixed(2)}</td>
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
