'use client';
import {DatePicker} from '@/components/ui/DatePicker';
import {LegacyPageHeader} from '@/components/ui/LegacyPageHeader';
import {useState} from 'react';
import {useRouter,useSearchParams} from 'next/navigation';
import Link from 'next/link';
import {useQuery} from '@tanstack/react-query';
import toast from 'react-hot-toast';
import apiClient from '@/services/apiClient';
import {useAuth} from '@/contexts/AuthContext';
import {buttonClass,inputClass,useJcAccess,errorMessage,localDate} from './useJcAccess';
import SubcontractorModal from './SubcontractorModal';
interface Item {item_id:string|number;item_name?:string;category?:string;unit_name?:string;original_qty?:number;received_qty?:number;pending_qty?:number;receive_qty:string;rate?:string;tax_rate?:string;remarks?:string;}
interface Options {manual_supported:boolean;eligible:{key:string;challan_no:string;sender_name:string}[];products:{id:number;item_name:string;unit_name:string}[];vendors:{id:number;name:string;gst_no:string}[];}
export default function ReceiveForm({returnId}:{returnId?:string}) {
  const router=useRouter(),search=useSearchParams(),{user}=useAuth(),{can,loading}=useJcAccess();
  const [mode,setMode]=useState(returnId?'Return':search.get('challan_id')?'In JC':'Others');
  const [form,setForm]=useState<Record<string,string>>({challan_id:search.get('challan_id') || '',manual_jc_type:'In JC',challan_date:localDate(),receive_date:localDate()});
  const [manualItems,setItems]=useState<Item[]>([{item_id:'',receive_qty:''}]),[busy,setBusy]=useState(false);
  const [edits,setEdits]=useState<Record<number,Partial<Item>>>({});
  const options=useQuery<Options>({queryKey:['receiveOptions',user?.db],enabled:!returnId && can('jobchallan','receiveadd'),queryFn:async()=>(await apiClient.get('/jc-receive/options')).data.data});
  const detailsEnabled=mode!=='Others' && !!(form.challan_id || returnId) && !!user && can('jobchallan',returnId?'itemreceived':'receiveadd');
  const details=useQuery<{from_company:string;items:Item[]}>({queryKey:['receivePending',user?.db,returnId,form.challan_id],enabled:detailsEnabled,queryFn:async()=>(await apiClient.get('/jc-receive/details',{params:{challan_id:returnId?returnId+'|'+user?.db:form.challan_id}})).data.data});
  const loadingItems=detailsEnabled && details.isFetching;
  const loadError=detailsEnabled && details.isError ? errorMessage(details.error):'';
  const fromCompany=details.data?.from_company || '';
  const items=mode==='Others'?manualItems:(details.data?.items || []).filter(i=>Number(i.pending_qty)>0).map((item,index)=>({...item,receive_qty:'',...edits[index]}));
  const taxes=useQuery<{id:number;tax:number}[]>({queryKey:['jcReturnTaxes',user?.db],enabled:!!returnId && can('jobchallan','itemreceived'),queryFn:async()=>(await apiClient.get('/job-challan/tax-master',{params:{context:'receive'}})).data.data});
  const update=(key:string,value:string)=>setForm(prev=>({...prev,[key]:value}));
  const updateItem=(index:number,key:keyof Item,value:string)=>{if(mode==='Others')setItems(prev=>prev.map((item,i)=>i===index?{...item,[key]:value}:item));else setEdits(prev=>({...prev,[index]:{...prev[index],[key]:value}}));};
  const allowed=can('jobchallan',returnId?'itemreceived':'receiveadd');
  if(loading) return <p className="p-6">Loading permissions...</p>;
  if(!allowed) return <p className="p-6" role="alert">You do not have permission to receive this JC.</p>;
  return <main className="max-w-7xl mx-auto px-6 py-8 space-y-5"><LegacyPageHeader title="JC Receive"/><form className="legacy-form space-y-5" onSubmit={async e=>{
    e.preventDefault();if(busy || loadingItems || loadError)return;
    const selected=items.filter(item=>item.item_id && Number(item.receive_qty)>0);
    if(!selected.length){toast.error('Enter a receive quantity for at least one item.');return;}
    if(mode!=='Others' && selected.some(item=>Number(item.receive_qty)>Number(item.pending_qty))){toast.error('Receive quantity cannot exceed pending quantity.');return;}
    setBusy(true);try{await apiClient.post(returnId?`/job-challan/${returnId}/receive`:'/jc-receive',{...form,jc_type:mode==='Others'?'Others':'In JC',items:selected,manual_items:selected});toast.success('JC received successfully');router.push(returnId?`/dashboard/jc-challan/${returnId}`:'/dashboard/jc-receive');}catch(error){toast.error(errorMessage(error));}finally{setBusy(false);}
  }}>
    {mode==='Others' && options.data && !options.data.manual_supported && <p role="alert" className="text-amber-700">Manual receipts are not yet enabled for this company. Select In JC to receive a linked challan.</p>}
    <section className="bg-white border rounded-xl p-5 space-y-4">{!returnId && <div className="flex gap-6">{['In JC','Others'].map(value=><label key={value}><input type="radio" name="mode" checked={mode===value} onChange={()=>{setMode(value);setItems([{item_id:'',receive_qty:''}]);setEdits({});}}/> {value}</label>)}</div>}
      {mode==='In JC' && <><label className="block text-sm">Job Challan<select required className={inputClass} value={form.challan_id} onChange={e=>{update('challan_id',e.target.value);setEdits({});}}><option value="">Select Job Challan</option>{options.data?.eligible.map(jc=><option key={jc.key} value={jc.key}>{jc.challan_no} ({jc.sender_name})</option>)}</select></label><p className="text-sm">From Company: {fromCompany || '-'}</p></>}
      <div className="grid grid-cols-2 md:grid-cols-3 gap-4">{mode==='Others' && <>
        <label className="text-sm">JC Type<select className={inputClass} value={form.manual_jc_type} onChange={e=>update('manual_jc_type',e.target.value)}><option>In JC</option><option>Others</option></select></label>
        <label className="text-sm">Challan No.<input required className={inputClass} value={form.challan_no || ''} onChange={e=>update('challan_no',e.target.value)}/></label>
        <label className="text-sm">Reference JC No. (optional)<input className={inputClass} value={form.reference_jc_no || ''} onChange={e=>update('reference_jc_no',e.target.value)}/></label>
        <label className="text-sm">Challan Date<DatePicker required  className={inputClass} value={form.challan_date} onChange={e=>update('challan_date',e.target.value)}/></label>
        {form.manual_jc_type==='In JC'?<div><label className="text-sm">From Company<select required className={inputClass} value={form.from_company || ''} onChange={e=>update('from_company',e.target.value)}><option value="">Select Company</option>{options.data?.vendors.map(v=><option key={v.id} value={v.id}>{v.name}</option>)}</select></label><SubcontractorModal onAdded={v=>{void options.refetch();update('from_company',String(v.id));}}/></div>:<label className="text-sm">Company/Party Name<input required className={inputClass} value={form.other_company_name || ''} onChange={e=>update('other_company_name',e.target.value)}/></label>}
        <label className="text-sm">GST No.<input readOnly className={inputClass+' bg-slate-50'} value={options.data?.vendors.find(v=>String(v.id)===form.from_company)?.gst_no || ''}/></label>
      </>}
      {returnId && <label className="text-sm">Receive Date<DatePicker required  className={inputClass} value={form.receive_date} onChange={e=>update('receive_date',e.target.value)}/></label>}
      <label className="text-sm">Vehicle No.<input className={inputClass} value={form.vehicle_no || ''} onChange={e=>update('vehicle_no',e.target.value)}/></label></div>
    </section>
    {(loadError || options.isError) && <p role="alert" className="text-red-600">{loadError || errorMessage(options.error)}</p>}
    <section className="bg-white border rounded-xl p-5 overflow-x-auto"><table className="w-full text-sm"><thead><tr>{(mode==='Others'?['Item','Receive Quantity','Unit','']:returnId?['Item','Dispatch','Received','Pending','Receive Qty','Rate','Tax %','Tax Amount','Total Amount','Remarks']:['Item','Category','Dispatch','Received','Pending','Receive Qty','Unit']).map((label,i)=><th className="p-2 text-left" key={i}>{label}</th>)}</tr></thead><tbody>{items.map((item,index)=><tr className="border-t" key={index}>{mode==='Others'?<><td className="p-2"><select required className={inputClass} value={item.item_id} onChange={e=>updateItem(index,'item_id',e.target.value)}><option value="">Select Item</option>{options.data?.products.map(p=><option key={p.id} value={p.id}>{p.item_name}</option>)}</select></td><td className="p-2"><input type="number" step="0.01" min="0" className={inputClass} value={item.receive_qty} onChange={e=>updateItem(index,'receive_qty',e.target.value)}/></td><td className="p-2">{options.data?.products.find(p=>String(p.id)===String(item.item_id))?.unit_name || 'Unit'}</td><td className="p-2"><button type="button" onClick={()=>setItems(items.filter((_,i)=>i!==index))}>Remove</button></td></>:<><td className="p-2">{item.item_name}</td>{!returnId && <td className="p-2">{item.category}</td>}<td className="p-2">{item.original_qty}</td><td className="p-2">{item.received_qty}</td><td className="p-2">{item.pending_qty}</td><td className="p-2"><input aria-label={`Receive quantity for ${item.item_name}`} type="number" min="0" step="0.01" max={item.pending_qty} className={inputClass} value={item.receive_qty} onChange={e=>updateItem(index,'receive_qty',e.target.value)}/></td>{returnId?<><td className="p-2"><input type="number" min="0" step="0.01" className={inputClass} value={item.rate || ''} onChange={e=>updateItem(index,'rate',e.target.value)}/></td><td className="p-2"><select className={inputClass} value={item.tax_rate || ''} onChange={e=>updateItem(index,'tax_rate',e.target.value)}><option value="">-- Tax --</option>{taxes.data?.map(t=><option key={t.id} value={String(t.tax)}>{t.tax}%</option>)}</select></td><td className="p-2">{(Number(item.receive_qty || 0)*Number(item.rate || 0)*Number(item.tax_rate || 0)/100).toFixed(2)}</td><td className="p-2">{(Number(item.receive_qty || 0)*Number(item.rate || 0)*(1+Number(item.tax_rate || 0)/100)).toFixed(2)}</td><td className="p-2"><input className={inputClass} value={item.remarks || ''} onChange={e=>updateItem(index,'remarks',e.target.value)}/></td></>:<td className="p-2">{item.unit_name || 'KG'}</td>}</>}</tr>)}</tbody></table>{loadingItems && <p>Loading pending quantities...</p>}{mode==='Others' && <button type="button" className="text-cyan-700 mt-3" onClick={()=>setItems([...items,{item_id:'',receive_qty:''}])}>+ Add Item</button>}</section>
    <div className="flex gap-4"><button className={buttonClass} disabled={busy || loadingItems || !!loadError || options.isError || (mode==='Others' && !options.data?.manual_supported)}>{busy?'Saving...':'Save Receive'}</button><Link href={returnId?`/dashboard/jc-challan/${returnId}`:'/dashboard/jc-receive'}>Cancel</Link></div>
  </form></main>;
}
