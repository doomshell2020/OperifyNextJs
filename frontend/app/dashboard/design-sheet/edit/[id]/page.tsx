'use client';

import React, { useState, useEffect, useRef } from 'react';
import { useRouter, useParams } from 'next/navigation';
import { designsheetService } from '../../../../../services/designsheet.service';
import { toast } from 'react-hot-toast';
import { Save, Plus, Trash2, ArrowLeft, Download, Search, Package, Loader2 } from 'lucide-react';
import Link from 'next/link';
import { formatQty, formatAmt } from '@/utils/formatters';

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
        setResults(res.items);
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
        {loading && (
          <Loader2 className="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 animate-spin" />
        )}
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
          No items found for &ldquo;{value}&rdquo;
        </div>
      )}
    </div>
  );
}

export default function EditDesignSheetPage() {
  const router = useRouter();
  const { id } = useParams() as { id: string };
  const [formData, setFormData] = useState({
    contract_id: '', designsheetno: '', item_id: '', quantity: '', datefrom: '', contract_title: '', product_name: ''
  });
  const [file, setFile] = useState<File | null>(null);
  const [revisions, setRevisions] = useState<File[]>(Array(5).fill(null));
  const [existingFiles, setExistingFiles] = useState<{main: string, r: string[]}>({ main: '', r: []});
  
  const [existingDetails, setExistingDetails] = useState<any[]>([]);
  const [newDetails, setNewDetails] = useState<any[]>([]);
  
  useEffect(() => {
     if (id) fetchSheet();
  }, [id]);

  const fetchSheet = async () => {
      try {
          const res = await designsheetService.getDesignSheetById(id);
          const d = res.desheet;
          setFormData({
              contract_id: d.contract_id || '',
              designsheetno: d.designsheetno || '',
              item_id: d.item_id || '',
              quantity: d.quantity || '',
              datefrom: d.datefrom ? new Date(d.datefrom).toISOString().split('T')[0] : '',
              contract_title: d.contract_title || '',
              product_name: d.product_name || '',
          });
          setExistingFiles({
              main: d.design_sheet,
              r: [d.r1, d.r2, d.r3, d.r4, d.r5]
          });
          setExistingDetails(res.product || []);
      } catch (e) {
          toast.error('Failed to load Design Sheet');
      }
  };

  

  const deleteExistingDetail = async (idx: number, detailId: string) => {
      if (confirm('Delete this item?')) {
          try {
              await designsheetService.deleteDetail(detailId);
              setExistingDetails(existingDetails.filter((_, i) => i !== idx));
              toast.success('Item deleted');
          } catch (e) { toast.error('Error deleting item'); }
      }
  };

  const handleExistingDetailChange = (index: number, field: string, value: string | boolean) => {
      const updated = [...existingDetails];
      if (field === 'is_group') {
          updated[index].is_group = value ? '1' : '0';
      } else {
          updated[index][field] = value;
      }
      setExistingDetails(updated);
  };
  
  const addNewDetailRow = () => {
     setNewDetails([...newDetails, { pitemname: '', item_name: '', km_item_qty: '', pitemquantity: '', unit_name: '', is_group: '0' }]);
  };

  const removeNewDetailRow = (index: number) => {
     setNewDetails(newDetails.filter((_, i) => i !== index));
  };

  const handleNewDetailChange = async (index: number, field: string, value: string, itemData?: any) => {
     const nd = [...newDetails];
     nd[index][field] = value;
     if (field === 'item_name' && itemData) {
         nd[index].pitemname = itemData.id;
         try {
             const res = await designsheetService.getIndentItems(itemData.id);
             if (res.itemname) nd[index].unit_name = res.itemname.unit_name || '';
         } catch (e) {}
     }
     
     if ((field === 'km_item_qty' || field === 'item_name') && nd[index].km_item_qty && formData.quantity) {
         nd[index].pitemquantity = formatQty(parseFloat(nd[index].km_item_qty) * parseFloat(formData.quantity));
     }
     setNewDetails(nd);
  };

  const handleSubmit = async (e: React.FormEvent) => {
      e.preventDefault();
      try {
          const form = new FormData();
          Object.entries(formData).forEach(([k, v]) => form.append(k, v));
          if (file) form.append('design_sheet', file);
          revisions.forEach((r, i) => { if (r) form.append(`r${i+1}`, r); });
          
          existingDetails.forEach((d) => {
              form.append('pitemname11', d.item_id);
              form.append('is_group11', d.is_group);
          });
          
          newDetails.forEach((d) => {
              form.append('pitemname', d.pitemname);
              form.append('km_item_qty', d.km_item_qty);
              form.append('pitemquantity', d.pitemquantity);
              form.append('unit_name', d.unit_name);
              form.append('is_group', d.is_group);
          });
          
          await designsheetService.updateDesignSheet(id, form);
          toast.success('Design Sheet updated successfully!');
          router.push('/dashboard/design-sheet');
      } catch (e: any) {
          toast.error(e.response?.data?.message || 'Error updating design sheet');
      }
  };

  return (
    <main className="max-w-7xl w-full mx-auto px-6 py-8 space-y-6">
      <div className="flex items-center gap-4">
        <Link href="/dashboard/design-sheet" className="p-2 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-600 transition">
          <ArrowLeft className="w-5 h-5" />
        </Link>
        <h1 className="text-xl font-extrabold text-slate-900">Edit Production Sheet</h1>
      </div>
      
      <form onSubmit={handleSubmit} className="bg-white border border-slate-200 rounded-xl p-6 space-y-6 shadow-sm">
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <div>
              <label className="text-xs font-bold text-slate-500 block mb-2">Design Sheet No<span className="text-rose-500">*</span></label>
              <input type="text" readOnly value={formData.designsheetno} className="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-sm text-slate-500 cursor-not-allowed" />
            </div>
            <div>
              <label className="text-xs font-bold text-slate-500 block mb-2">Contract Name<span className="text-rose-500">*</span></label>
              <input type="text" readOnly value={formData.contract_title} className="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-sm text-slate-500 cursor-not-allowed" />
            </div>
            <div>
              <label className="text-xs font-bold text-slate-500 block mb-2">Finished Product<span className="text-rose-500">*</span></label>
              <input type="text" readOnly value={formData.product_name} className="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-sm text-slate-500 cursor-not-allowed" />
            </div>
            <div>
              <label className="text-xs font-bold text-slate-500 block mb-2">Quantity(in KM)<span className="text-rose-500">*</span></label>
              <input type="text" readOnly value={formData.quantity} className="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-sm text-slate-500 cursor-not-allowed" />
            </div>
            <div>
              <label className="text-xs font-bold text-slate-500 block mb-2">Date<span className="text-rose-500">*</span></label>
              <input type="date" required value={formData.datefrom} onChange={e => setFormData({ ...formData, datefrom: e.target.value })} className="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:border-cyan-500 outline-none" />
            </div>
            <div>
              <label className="text-xs font-bold text-slate-500 block mb-2">Upload Design Sheet</label>
              <input type="file" onChange={e => setFile(e.target.files?.[0] || null)} className="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100" />
              <p className="text-[10px] text-rose-500 font-bold mt-1">PDF, JPG, JPEG or PNG files only</p>
              {existingFiles.main && <a href={`/designsheet/${existingFiles.main}`} target="_blank" rel="noreferrer" className="text-xs text-blue-500 underline flex items-center gap-1 mt-1"><Download className="w-3 h-3"/> View existing</a>}
            </div>
            
            {Array(5).fill(0).map((_, i) => (
                <div key={i}>
                  <label className="text-xs font-bold text-slate-500 block mb-2">R{i+1}</label>
                  <input type="file" onChange={e => {
                      const newRevs = [...revisions];
                      newRevs[i] = e.target.files?.[0] || null;
                      setRevisions(newRevs);
                  }} className="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100" />
                  <p className="text-[10px] text-rose-500 font-bold mt-1">PDF, JPG, JPEG or PNG files only</p>
                  {existingFiles.r[i] && <a href={`/designsheet/${existingFiles.r[i]}`} target="_blank" rel="noreferrer" className="text-xs text-blue-500 underline flex items-center gap-1 mt-1"><Download className="w-3 h-3"/> View R{i+1}</a>}
                </div>
            ))}
        </div>
        
        <div className="pt-4 border-t border-slate-100">
           <div className="flex items-center justify-between mb-4">
              <h3 className="text-sm font-bold text-slate-800">Items</h3>
           </div>
           
           <table className="w-full text-left text-sm border border-slate-200 mb-4 bg-slate-50">
               <thead className="bg-[#c8c8c8] text-slate-800 font-bold text-[13px]">
                   <tr>
                       <th className="p-3 border-r border-slate-300 w-1/3">Item</th>
                       <th className="p-3 border-r border-slate-300 w-[5%]"></th>
                       <th className="p-3 border-r border-slate-300">Qty(per KM)</th>
                       <th className="p-3 border-r border-slate-300">Total Qty</th>
                       <th className="p-3 border-r border-slate-300">UOM</th>
                       <th className="p-3 text-center">Action</th>
                   </tr>
               </thead>
               <tbody className="divide-y divide-slate-200">
                   {existingDetails.map((row, idx) => (
                       <tr key={idx} className="bg-white hover:bg-slate-50 transition">
                           <td className="p-2 border-r border-slate-200">
                               <input type="text" readOnly value={row.item_name || ''} className="w-full p-2 border border-slate-200 rounded text-xs bg-white text-slate-700 outline-none" />
                           </td>
                           <td className="p-2 border-r border-slate-200 text-center">
                               <input type="checkbox" checked={row.is_group === '1'} disabled className="w-4 h-4 text-slate-400 bg-slate-100 border-slate-300 rounded cursor-not-allowed" />
                           </td>
                           <td className="p-2 border-r border-slate-200">
                               <input type="number" step="any" value={row.km_item_qty} readOnly className="w-full p-2 border border-slate-200 rounded text-xs bg-slate-100 text-slate-500 outline-none cursor-not-allowed" />
                           </td>
                           <td className="p-2 border-r border-slate-200">
                               <input type="text" readOnly value={row.item_qty} className="w-full p-2 border border-slate-200 rounded text-xs bg-slate-100 text-slate-500 outline-none cursor-not-allowed" />
                           </td>
                           <td className="p-2 border-r border-slate-200">
                               <input type="text" readOnly value={row.uom} className="w-full p-2 border border-slate-200 rounded text-xs bg-slate-100 text-slate-500 outline-none cursor-not-allowed" />
                           </td>
                           <td className="p-2 text-center">
                               <button type="button" onClick={() => deleteExistingDetail(idx, row.id)} className="p-1.5 text-rose-500 hover:bg-rose-50 rounded transition"><Trash2 className="w-4 h-4 mx-auto" /></button>
                           </td>
                       </tr>
                   ))}
                   {newDetails.map((row, idx) => (
                       <tr key={idx} className="bg-emerald-50/20 hover:bg-emerald-50 transition">
                           <td className="p-2 border-r border-slate-200">
                               <ItemAutocomplete 
                                 value={row.item_name} 
                                 onChange={v => handleNewDetailChange(idx, 'item_name', v)} 
                                 onSelect={item => handleNewDetailChange(idx, 'item_name', item.item_name, item)} 
                               />
                           </td>
                           <td className="p-2 border-r border-slate-200 text-center">
                               <input type="checkbox" checked={row.is_group === '1'} onChange={e => handleNewDetailChange(idx, 'is_group', e.target.checked ? '1' : '0')} className="w-4 h-4 text-emerald-600 rounded focus:ring-emerald-500" />
                           </td>
                           <td className="p-2 border-r border-slate-200">
                               <input type="number" step="any" value={row.km_item_qty} onChange={e => handleNewDetailChange(idx, 'km_item_qty', e.target.value)} className="w-full p-2 border border-emerald-200 rounded text-xs bg-white text-slate-900 outline-none focus:border-emerald-500" required />
                           </td>
                           <td className="p-2 border-r border-slate-200">
                               <input type="text" readOnly value={row.pitemquantity} className="w-full p-2 border border-slate-200 rounded text-xs bg-slate-100 text-slate-500 outline-none" />
                           </td>
                           <td className="p-2 border-r border-slate-200">
                               <input type="text" readOnly value={row.unit_name} className="w-full p-2 border border-slate-200 rounded text-xs bg-slate-100 text-slate-500 outline-none" />
                           </td>
                           <td className="p-2 text-center">
                              <button type="button" onClick={() => removeNewDetailRow(idx)} className="p-1.5 text-rose-500 hover:bg-rose-50 rounded"><Trash2 className="w-4 h-4 mx-auto" /></button>
                           </td>
                       </tr>
                   ))}
               </tbody>
           </table>
           
           <button type="button" onClick={addNewDetailRow} className="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg text-xs font-semibold mt-2">
              <Plus className="w-3.5 h-3.5" /> Add New Row
           </button>
        </div>
        
        <div className="flex justify-end pt-4 border-t border-slate-100">
           <button type="submit" className="flex items-center gap-2 px-6 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white rounded-lg text-sm font-semibold shadow-sm transition">
              <Save className="w-4 h-4" /> Save Changes
           </button>
        </div>
      </form>
    </main>
  );
}
