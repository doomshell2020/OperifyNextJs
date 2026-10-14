"use client";

import { useEffect, useRef, useState } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import AsyncSelect from 'react-select/async';
import { reverseIndentService as service } from '@/services/reverseIndent.service';
import { useLegacyActionAccess } from '@/components/ui/useLegacyActionAccess';
import { LegacyPageHeader } from '@/components/ui/LegacyPageHeader';
import { DatePicker } from '@/components/ui/DatePicker';
import { isLegacyToday } from '@/utils/legacyActionDate';

const today = () => new Intl.DateTimeFormat('en-CA', {timeZone:'Asia/Kolkata',year:'numeric',month:'2-digit',day:'2-digit'}).format(new Date());
const errorMessage = (e: any) => e?.response?.data?.error?.message || e?.response?.data?.message || e?.message || 'Unable to save Reverse Indent.';
const fieldClass = 'w-full border border-slate-300 rounded px-3 py-2 bg-white text-sm';

export function ReverseIndentForm({ reverseId }: { reverseId?: string }) {
  const router = useRouter();
  const canAction = useLegacyActionAccess();
  const edit = !!reverseId;
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState('');
  const [blocked, setBlocked] = useState(false);
  const [form, setForm] = useState<any>({reverse_id:'',issue_date:today(),contract_id:'',design_product_id:'',finishedproduct_id:'',machine_id:null,received_name:'',finished_qty:''});
  const [contract, setContract] = useState<any>(null);
  const [machine, setMachine] = useState<any>(null);
  const [products, setProducts] = useState<any[]>([]);
  const [semiProducts, setSemiProducts] = useState<any[]>([]);
  const [items, setItems] = useState<any[]>([]);
  const [pending, setPending] = useState<number | null>(null);
  const [productUom, setProductUom] = useState('');
  const generation = useRef(0);
  const setField = (name: string, value: any) => setForm((old: any)=>({...old,[name]:value}));

  useEffect(()=>{
    let alive=true;
    const load = async () => {
      try {
        if (reverseId) {
          const data = await service.getEditData(reverseId);
          if (!alive) return;
          setBlocked(data.status!=='draft' || !isLegacyToday(data.issue_date) || !data.draft_stock_clean);
          setForm({...data,issue_date:String(data.issue_date).slice(0,10),finished_qty:String(data.finished_qty),design_product_id:''});
          setContract({value:data.contract_id,label:`${data.contract_name} (${data.workorder})`});
          setMachine(data.machine_id ? {value:data.machine_id,label:data.machine_name} : null);
          setItems(data.raw_items.map((row: any)=>({...row,quantity:String(row.quantity),is_group:0})));
          setProductUom(data.product_uom || ''); setPending(data.pending_qty);
        } else {
          const number=await service.getNextReverseId();
          if (alive) setField('reverse_id',String(number));
        }
      } catch(e) { if(alive) {setError(errorMessage(e));setBlocked(true);} }
      finally { if(alive) setLoading(false); }
    };
    void load(); return ()=>{alive=false;};
  },[reverseId]);

  const selectContract = async (selected: any) => {
    const request=++generation.current;
    setContract(selected); setProducts([]); setSemiProducts([]); setItems([]); setProductUom(''); setPending(null);
    setForm((old: any)=>({...old,contract_id:selected?.value || '',design_product_id:'',finishedproduct_id:'',finished_qty:''}));
    if (!selected) return;
    try { const rows=await service.contractProducts(selected.value); if(request===generation.current) setProducts(rows); }
    catch(e) { if(request===generation.current) setError(errorMessage(e)); }
  };
  const selectProduct = async (value: string) => {
    const request=++generation.current;
    setItems([]); setSemiProducts([]); setProductUom('');setPending(null);setError('');
    setForm((old: any)=>({...old,design_product_id:value,finishedproduct_id:'',finished_qty:''}));
    if (!value) return;
    try {
      const data=await service.designSheet(form.contract_id,value);
      if(request!==generation.current) return;
      setSemiProducts(data.semi_products); setItems(data.items.map((row: any)=>({...row,quantity:''})));
    } catch(e) { if(request===generation.current) setError(errorMessage(e)); }
  };
  const addGroupItem = (group: any, value: string) => {
    const selected=group.group_items?.find((row: any)=>String(row.id)===value);
    if (!selected) return;
    if(items.some(row=>Number(row.is_group)!==1 && String(row.item_id)===value)) {setError('This Item Already added');return;}
    setError(''); setItems(old=>[...old,{...selected,item_id:selected.id,quantity:'',is_group:0,from_group:true}]);
  };
  const submit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault(); setError('');
    const action=(event.nativeEvent as SubmitEvent).submitter?.getAttribute('value') || 'finalize';
    if (!form.contract_id || !form.finishedproduct_id || !form.received_name.trim() || (!edit && !form.design_product_id)) {setError('Please fill all required fields.');return;}
    if(!Number.isFinite(Number(form.finished_qty)) || Number(form.finished_qty)<=0) {setError('Received Qty must be greater than 0.');return;}
    if(action==='finalize' && pending!=null && Number(form.finished_qty)>pending) {setError('Received Qty cannot exceed Design Sheet Qty.');return;}
    setSaving(true);
    try {
      const payload={...form,action,items:items.filter(row=>Number(row.is_group)!==1).map(row=>({item_id:row.item_id,quantity:row.quantity}))};
      if(reverseId) await service.updateReverseIndent(reverseId,payload); else await service.saveReverseIndent(payload);
      router.push('/dashboard/reverse');
    } catch(e) {setError(errorMessage(e));} finally {setSaving(false);}
  };
  const loadContracts = async (q: string) => (await service.lookup('contracts',q)).map((row: any)=>({value:row.id,label:`${row.title} (${row.workorder})`}));
  const loadMachines = async (q: string) => (await service.lookup('machines',q)).map((row: any)=>({value:row.id,label:row.machine_name}));
  if(loading) return <div>Loading Reverse Indent...</div>;
  if(!canAction('reverseindent',edit?'edit':'add')) return <div>You do not have permission to {edit?'edit':'add'} Reverse Indents.</div>;
  return <div className="space-y-5">
    <LegacyPageHeader title="Reverse Manager" />
    <div className="bg-white border border-slate-200 rounded p-5">
      <h2 className="font-semibold border-b pb-3 mb-4">{edit?'Edit Reverse id':'Generate Reverse id'} : I-{form.reverse_id}</h2>
      {error && <p role="alert" className="bg-red-50 text-red-700 p-3 mb-4">{error}</p>}
      {blocked && <p role="alert" className="text-red-700 mb-4">Only drafts issued today can be edited. Drafts containing posted stock require review.</p>}
      <form onSubmit={submit}>
        <fieldset disabled={saving || blocked} className="space-y-5">
          <div className="grid md:grid-cols-4 gap-4">
            <label className="text-sm">Reverse Id No. *<input className={fieldClass} value={form.reverse_id} readOnly /></label>
            <label className="text-sm">Issued Date *<DatePicker required max={today()} name="issue_date" dateFormat="dd-MM-yyyy" value={form.issue_date} onChange={(e: any)=>setField('issue_date',e.target.value)} className={fieldClass} /></label>
            <div className="text-sm"><label htmlFor="reverse-contract">Contract Name *</label><AsyncSelect inputId="reverse-contract" value={contract} loadOptions={loadContracts} defaultOptions isClearable isDisabled={edit || saving || blocked} onChange={selectContract} placeholder="Enter Contract Name" /></div>
            <label className="text-sm">Product *{edit?<input readOnly value={form.product_name || ''} className={fieldClass} />:<select id="reverse-design-product" required className={fieldClass} value={form.design_product_id} onChange={e=>void selectProduct(e.target.value)}><option value="">-- Select Product --</option>{products.map(row=><option key={row.id} value={row.id}>{row.item_name}</option>)}</select>}</label>
            <div className="text-sm"><label htmlFor="reverse-machine">Machine Name</label><AsyncSelect inputId="reverse-machine" value={machine} loadOptions={loadMachines} defaultOptions isClearable isDisabled={saving || blocked} onChange={(row: any)=>{setMachine(row);setField('machine_id',row?.value || null);}} placeholder="Enter Machine Name" /></div>
            <label className="text-sm">Received By *<input required maxLength={255} className={fieldClass} value={form.received_name} onChange={e=>setField('received_name',e.target.value)} placeholder="Enter Name" /></label>
          </div>
          {(edit || form.design_product_id) && <div className="overflow-x-auto"><table className="w-full border-collapse text-sm"><thead className="bg-slate-200"><tr><th className="p-2 text-left">Semi-Finished Product *</th><th>UOM</th><th>Pending Qty</th><th>Received Qty *</th></tr></thead><tbody><tr>
            <td className="border p-2">{edit?<input readOnly className={fieldClass} value={form.product_name || ''} />:<select id="reverse-semi-product" required className={fieldClass} value={form.finishedproduct_id} onChange={e=>{setField('finishedproduct_id',e.target.value);setProductUom(semiProducts.find(row=>String(row.id)===e.target.value)?.uom || '');}}><option value="">-- Select Semi-Finished Product --</option>{semiProducts.map(row=><option key={row.id} value={row.id}>{row.item_name}</option>)}</select>}</td>
            <td className="border p-2">{productUom}</td><td className="border p-2">{pending==null?'N/A':pending.toFixed(2)}</td>
            <td className="border p-2"><input required type="number" min="0.01" step="0.01" className={fieldClass} value={form.finished_qty} onChange={e=>setField('finished_qty',e.target.value)} /></td>
          </tr></tbody></table>
          <table className="w-full border-collapse text-sm mt-4"><thead className="bg-slate-200"><tr><th className="p-2 text-left w-[55%]">Raw Material</th><th className="p-2 text-left">Received Qty</th><th>UOM</th><th></th></tr></thead><tbody>
            {items.map((row,index)=><tr key={`${row.item_id}-${index}`}><td className="border p-2">{Number(row.is_group)===1?<select className={fieldClass} value="" onChange={e=>addGroupItem(row,e.target.value)}><option value="">-- {row.category_name || row.item_name} --</option>{row.group_items?.map((child: any)=><option key={child.id} value={child.id}>{child.item_name}</option>)}</select>:row.item_name}</td>
              <td className="border p-2">{Number(row.is_group)===1?'--':<input type="number" min="0" step="0.01" className={fieldClass} value={row.quantity} onChange={e=>setItems(old=>old.map((item,i)=>i===index?{...item,quantity:e.target.value}:item))} />}</td><td className="border p-2">{row.uom}</td><td className="border p-2">{row.from_group && <button type="button" onClick={()=>setItems(old=>old.filter((_,i)=>i!==index))}>Remove</button>}</td></tr>)}
          </tbody></table></div>}
          <div className="flex gap-3 justify-end"><button type="submit" value="draft" className="bg-amber-500 text-white px-4 py-2 rounded" disabled={saving || blocked}>Save as Draft</button><button type="submit" value="finalize" className="bg-green-600 text-white px-4 py-2 rounded" disabled={saving || blocked}>{saving?'Saving...':'Save & Finalize'}</button></div>
        </fieldset>
      </form>
      <Link href="/dashboard/reverse" className="inline-block border px-4 py-2 mt-4 rounded">Back</Link>
    </div>
  </div>;
}
