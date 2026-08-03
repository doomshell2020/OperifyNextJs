'use client';

import React, { useState, useEffect, useRef } from 'react';
import { useRouter } from 'next/navigation';
import { designsheetService } from '../../../../services/designsheet.service';
import { toast } from 'react-hot-toast';
import { Save, Plus, Trash2, ArrowLeft, Search, Loader2, Package, FileText } from 'lucide-react';
import Link from 'next/link';

function ContractAutocomplete({
  value,
  onChange,
  onSelect,
}: {
  value: string;
  onChange: (v: string) => void;
  onSelect: (item: any) => void;
}) {
  const [results, setResults] = useState<any[]>([]);
  const [open, setOpen] = useState(false);
  const [loading, setLoading] = useState(false);
  const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);
  const wrapperRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    function handleOutside(e: MouseEvent) {
      if (wrapperRef.current && !wrapperRef.current.contains(e.target as Node)) {
        setOpen(false);
      }
    }
    document.addEventListener('mousedown', handleOutside);
    return () => document.removeEventListener('mousedown', handleOutside);
  }, []);

  const handleChange = (v: string) => {
    onChange(v);
    if (debounceRef.current) clearTimeout(debounceRef.current);
    if (!v.trim()) {
      setResults([]);
      setOpen(false);
      return;
    }
    setLoading(true);
    debounceRef.current = setTimeout(async () => {
      try {
        const res = await designsheetService.searchContracts(v.trim());
        setResults(res.contracts || []);
        setOpen(true);
      } catch {
        setResults([]);
      } finally {
        setLoading(false);
      }
    }, 280);
  };

  const handleSelect = (item: any) => {
    onSelect(item);
    setOpen(false);
    setResults([]);
  };

  return (
    <div className="relative" ref={wrapperRef}>
      <div className="relative">
        <Search className="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
        <input
          type="text"
          value={value}
          onChange={e => handleChange(e.target.value)}
          placeholder="Enter Contract Name"
          autoComplete="off"
          className="w-full px-3 py-2 pl-9 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:border-cyan-500 outline-none"
          required
        />
        {loading && <Loader2 className="absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 animate-spin" />}
      </div>
      {open && results.length > 0 && (
        <ul className="absolute z-50 top-full left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-52 overflow-y-auto">
          {results.map(item => (
            <li key={item.id}>
              <button
                type="button"
                onMouseDown={() => handleSelect(item)}
                className="w-full text-left px-3 py-2 text-xs text-slate-800 hover:bg-cyan-50 transition-colors flex items-center gap-2"
              >
                <FileText className="w-3.5 h-3.5 text-slate-400 shrink-0" />
                {item.title}({item.workorder})
              </button>
            </li>
          ))}
        </ul>
      )}
      {open && !loading && results.length === 0 && value.trim() && (
        <div className="absolute z-50 top-full left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-lg px-3 py-2 text-xs text-slate-500">
          No Record Found
        </div>
      )}
    </div>
  );
}

function ItemAutocomplete({
  value,
  onChange,
  onSelect,
}: {
  value: string;
  onChange: (v: string) => void;
  onSelect: (item: any) => void;
}) {
  const [results, setResults] = useState<any[]>([]);
  const [open, setOpen] = useState(false);
  const [loading, setLoading] = useState(false);
  const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);
  const wrapperRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    function handleOutside(e: MouseEvent) {
      if (wrapperRef.current && !wrapperRef.current.contains(e.target as Node)) {
        setOpen(false);
      }
    }
    document.addEventListener('mousedown', handleOutside);
    return () => document.removeEventListener('mousedown', handleOutside);
  }, []);

  const handleChange = (v: string) => {
    onChange(v);
    if (debounceRef.current) clearTimeout(debounceRef.current);
    if (!v.trim()) {
      setResults([]);
      setOpen(false);
      return;
    }
    setLoading(true);
    debounceRef.current = setTimeout(async () => {
      try {
        const res = await designsheetService.searchItems(v.trim());
        setResults(res.items || []);
        setOpen(true);
      } catch {
        setResults([]);
      } finally {
        setLoading(false);
      }
    }, 280);
  };

  const handleSelect = (item: any) => {
    onSelect(item);
    setOpen(false);
    setResults([]);
  };

  return (
    <div className="relative" ref={wrapperRef}>
      <div className="relative">
        <Search className="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
        <input
          type="text"
          value={value}
          onChange={e => handleChange(e.target.value)}
          placeholder="Type item name…"
          autoComplete="off"
          className="w-full h-8 pl-8 pr-3 rounded border border-emerald-200 text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-emerald-500"
          required
        />
        {loading && <Loader2 className="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 animate-spin" />}
      </div>
      {open && results.length > 0 && (
        <ul className="absolute z-50 top-full left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-52 overflow-y-auto w-64">
          {results.map(item => (
            <li key={item.id}>
              <button
                type="button"
                onMouseDown={() => handleSelect(item)}
                className="w-full text-left px-3 py-2 text-xs text-slate-800 hover:bg-emerald-50 hover:text-emerald-700 transition-colors flex items-center gap-2"
              >
                <Package className="w-3.5 h-3.5 text-slate-400 shrink-0" />
                {item.item_name}
              </button>
            </li>
          ))}
        </ul>
      )}
      {open && !loading && results.length === 0 && value.trim() && (
        <div className="absolute z-50 top-full left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-lg px-3 py-2 text-xs text-slate-500 w-64">
          No items found for "{value}"
        </div>
      )}
    </div>
  );
}

export default function AddDesignSheetPage() {
  const router = useRouter();
  const [formData, setFormData] = useState({
    contract_id: '',
    designsheetno: '',
    item_id: '',
    quantity: '',
    datefrom: new Date().toISOString().split('T')[0],
  });
  
  const [contractName, setContractName] = useState('');
  const [file, setFile] = useState<File | null>(null);
  const [bomProducts, setBomProducts] = useState<any[]>([]);
  const [details, setDetails] = useState<any[]>([]);
  const [alreadyExists, setAlreadyExists] = useState(false);

  const handleContractSelect = async (contract: any) => {
    setContractName(`${contract.title}(${contract.workorder})`);
    setFormData(prev => ({ ...prev, contract_id: contract.id, item_id: '', quantity: '' }));
    setAlreadyExists(false);
    
    try {
        const res = await designsheetService.getBomFinishedProducts(contract.id);
        setBomProducts(res.products || []);
    } catch (e) {
        toast.error('Failed to fetch finished products');
        setBomProducts([]);
    }
  };

  const handleItemChange = async (e: React.ChangeEvent<HTMLSelectElement>) => {
     const val = e.target.value;
     setFormData({ ...formData, item_id: val, quantity: '' });
     setAlreadyExists(false);
     
     if (val && formData.contract_id) {
         try {
             const res = await designsheetService.checkDesignSheetItem(formData.contract_id, val);
             
             if (res.checkdesign) {
                 setAlreadyExists(true);
                 setFormData(prev => ({ ...prev, item_id: '', quantity: '' }));
             } else {
                 setFormData(prev => ({ ...prev, quantity: res.itemqty || '' }));
             }
         } catch (e) {
             toast.error('Error validating finished product');
         }
     }
  };

  const addDetailRow = () => {
     setDetails([...details, { pitemnameText: '', pitemname: '', km_item_qty: '', pitemquantity: '', unit_name: '', is_group: '0', uom: '' }]);
  };

  const removeDetailRow = (index: number) => {
     setDetails(details.filter((_, i) => i !== index));
  };

  const handleDetailChange = (index: number, field: string, value: string) => {
     const newDetails = [...details];
     newDetails[index][field] = value;
     
     // Recalculate quantity if km_item_qty changes
     if (field === 'km_item_qty') {
         if (value && formData.quantity) {
             newDetails[index].pitemquantity = (parseFloat(value) * parseFloat(formData.quantity)).toFixed(2);
         } else {
             newDetails[index].pitemquantity = '';
         }
     }
     setDetails(newDetails);
  };
  
  const handleItemSelect = async (index: number, item: any) => {
      const newDetails = [...details];
      newDetails[index].pitemnameText = item.item_name;
      newDetails[index].pitemname = item.id;
      
      try {
          const res = await designsheetService.getIndentItems(item.id);
          if (res.itemname) {
              newDetails[index].unit_name = res.itemname.unit_name || '';
          }
      } catch (e) {}
      
      setDetails(newDetails);
  };

  const handleSubmit = async (e: React.FormEvent) => {
      e.preventDefault();
      
      if (!formData.contract_id) {
          toast.error("Your entered Contract does not exist.");
          return;
      }
      if (!formData.item_id) {
          toast.error("Your entered Product does not exist.");
          return;
      }
      
      try {
          const form = new FormData();
          Object.entries(formData).forEach(([k, v]) => form.append(k, v));
          if (file) form.append('design_sheet', file);
          
          details.forEach((d) => {
              form.append('pitemname', d.pitemname);
              form.append('km_item_qty', d.km_item_qty);
              form.append('pitemquantity', d.pitemquantity);
              form.append('unit_name', d.unit_name);
              form.append('is_group', d.is_group);
          });
          
          await designsheetService.createDesignSheet(form);
          toast.success('Design Sheet added successfully!');
          router.push('/dashboard/design-sheet');
      } catch (e: any) {
          toast.error(e.response?.data?.message || 'Error adding design sheet');
      }
  };

  return (
    <main className="max-w-7xl w-full mx-auto px-6 py-8 space-y-6">
      <div className="flex items-center gap-4">
        <Link href="/dashboard/design-sheet" className="p-2 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-600 transition">
          <ArrowLeft className="w-5 h-5" />
        </Link>
        <h1 className="text-xl font-extrabold text-slate-900">Create New Design Sheet</h1>
      </div>
      
      <form onSubmit={handleSubmit} className="bg-white border border-slate-200 rounded-xl p-6 space-y-6 shadow-sm">
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <div>
              <label className="text-xs font-bold text-slate-500 uppercase block mb-2">Design Sheet No</label>
              <input type="text" value={formData.designsheetno} placeholder="Auto-generated if empty" onChange={e => setFormData({ ...formData, designsheetno: e.target.value })} className="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:border-cyan-500 outline-none" />
            </div>
            <div>
              <label className="text-xs font-bold text-slate-500 uppercase block mb-2">Contract Name <span className="text-rose-500">*</span></label>
              <ContractAutocomplete
                 value={contractName}
                 onChange={setContractName}
                 onSelect={handleContractSelect}
              />
            </div>
            <div>
              <label className="text-xs font-bold text-slate-500 uppercase block mb-2">Finished Product <span className="text-rose-500">*</span></label>
              <select required value={formData.item_id} onChange={handleItemChange} className="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:border-cyan-500 outline-none">
                 <option value="">-- Select Finished Product --</option>
                 {bomProducts.map(b => (
                     <option key={b.id} value={b.id}>{b.item_name}</option>
                 ))}
              </select>
              {alreadyExists && (
                 <div className="text-rose-500 text-xs mt-1 font-semibold">This Product Already Exits</div>
              )}
            </div>
            <div>
              <label className="text-xs font-bold text-slate-500 uppercase block mb-2">Quantity (in KM) <span className="text-rose-500">*</span></label>
              <input type="text" required readOnly value={formData.quantity} placeholder="Enter Quantity(in KM)" className="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-sm text-slate-500 cursor-not-allowed" />
            </div>
            <div>
              <label className="text-xs font-bold text-slate-500 uppercase block mb-2">Date <span className="text-rose-500">*</span></label>
              <input type="date" required value={formData.datefrom} onChange={e => setFormData({ ...formData, datefrom: e.target.value })} className="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:border-cyan-500 outline-none" />
            </div>
            <div>
              <label className="text-xs font-bold text-slate-500 uppercase block mb-2">Upload Design Sheet <span className="text-rose-500">*</span></label>
              <input type="file" required onChange={e => setFile(e.target.files?.[0] || null)} className="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100" />
              <div className="text-[10px] text-rose-500 font-semibold mt-1">PDF, JPG, JPEG or PNG files only</div>
            </div>
        </div>
        
        <div className="pt-4 border-t border-slate-100">
           <div className="flex items-center justify-between mb-4">
              <h3 className="text-sm font-bold text-slate-800">Items</h3>
              <button type="button" onClick={addDetailRow} className="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg text-xs font-semibold">
                 <Plus className="w-3.5 h-3.5" /> Add Row
              </button>
           </div>
           
           <div className="overflow-visible">
             <table className="w-full text-left text-sm border border-slate-200 rounded-lg">
                 <thead className="bg-slate-200 text-xs font-bold text-slate-800">
                     <tr>
                         <th className="p-3 w-1/3">Item</th>
                         <th className="p-3 w-10"></th>
                         <th className="p-3">Qty(per KM)</th>
                         <th className="p-3">Total Qty</th>
                         <th className="p-3">UOM</th>
                         <th className="p-3">Action</th>
                     </tr>
                 </thead>
                 <tbody className="divide-y divide-slate-200">
                     {details.map((row, idx) => (
                         <tr key={idx} className="bg-white hover:bg-slate-50">
                             <td className="p-2">
                               <ItemAutocomplete
                                   value={row.pitemnameText}
                                   onChange={(v) => handleDetailChange(idx, 'pitemnameText', v)}
                                   onSelect={(item) => handleItemSelect(idx, item)}
                               />
                             </td>
                             <td className="p-2 text-center">
                               <input type="checkbox" checked={row.is_group === '1'} onChange={e => handleDetailChange(idx, 'is_group', e.target.checked ? '1' : '0')} className="w-4 h-4 text-emerald-600 rounded focus:ring-emerald-500" />
                             </td>
                             <td className="p-2"><input type="number" step="any" value={row.km_item_qty} onChange={e => handleDetailChange(idx, 'km_item_qty', e.target.value)} className="w-full p-2 border border-slate-200 rounded text-xs focus:border-cyan-500 focus:outline-none" required /></td>
                             <td className="p-2"><input type="text" readOnly value={row.pitemquantity} className="w-full p-2 border border-slate-200 rounded text-xs bg-slate-100 outline-none" required /></td>
                             <td className="p-2"><input type="text" readOnly value={row.unit_name} className="w-full p-2 border border-slate-200 rounded text-xs bg-slate-100 outline-none" required /></td>
                             <td className="p-2 text-center">
                                <button type="button" onClick={() => removeDetailRow(idx)} className="p-1.5 text-rose-500 hover:bg-rose-50 rounded"><Trash2 className="w-4 h-4" /></button>
                             </td>
                         </tr>
                     ))}
                     {details.length === 0 && (
                         <tr>
                             <td colSpan={6} className="p-6 text-center text-sm text-slate-500">
                                 No items added. Click "Add Row" to begin.
                             </td>
                         </tr>
                     )}
                 </tbody>
             </table>
           </div>
        </div>
        
        <div className="flex justify-end pt-4 border-t border-slate-100">
           <button type="submit" className="flex items-center gap-2 px-6 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white rounded-lg text-sm font-semibold shadow-sm transition">
              <Save className="w-4 h-4" /> Add
           </button>
        </div>
      </form>
    </main>
  );
}
