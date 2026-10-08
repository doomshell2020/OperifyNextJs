'use client';
import {LegacyPageHeader} from '@/components/ui/LegacyPageHeader';
import {useRef,useState} from 'react';
import {useQuery} from '@tanstack/react-query';
import {useRouter} from 'next/navigation';
import Link from 'next/link';
import toast from 'react-hot-toast';
import apiClient from '@/services/apiClient';
import {useAuth} from '@/contexts/AuthContext';
import {useJcAccess,inputClass,buttonClass,errorMessage,localDate} from './useJcAccess';
interface Item {item_id:number|string;item_name?:string;quantity:number|string;unit:string;description:string;remarks:string;jc_no?:string;}
interface Options {jcs:{id:number;challan_no:string}[];vendors:{id:number;name:string}[];products:{id:number;item_name:string;unit_name:string}[];multiple_jcs:boolean;}
interface Form {gatepass_type:string;jc_id:string[];date:string;return_date:string;sub_contractor_id:string;company_name:string;vehicle_no:string;remarks:string;}
const emptyItem=():Item=>({item_id:'',quantity:'',unit:'',description:'',remarks:''});
export default function GatepassForm({id}:{id?:string}) {
  const {user}=useAuth(),{can,loading}=useJcAccess();
  const allowed=can('gatepasses',id?'edit':'add');
  const detail=useQuery({queryKey:['gatepassEdit',user?.db,id],enabled:!!id && allowed,queryFn:async()=>(await apiClient.get('/gatepass/'+id)).data.data});
  if(loading || (id && detail.isLoading))return <p className="p-6">Loading...</p>;
  if(!allowed)return <p className="p-6" role="alert">You do not have permission for this action.</p>;
  if(detail.isError)return <p className="p-6 text-red-600" role="alert">{errorMessage(detail.error)}</p>;
  const g=detail.data?.gatepass;
  const initial=g?{gatepass_type:g.gatepass_type || 'JC Based',jc_id:String(g.jc_id || '').split(',').filter(Boolean),date:g.date || localDate(),return_date:g.return_date || '',sub_contractor_id:String(g.sub_contractor_id || ''),company_name:g.company_name || '',vehicle_no:g.vehicle_no || '',remarks:g.remarks || ''}:undefined;
  return <Editor key={String(user?.db)+':'+(id || 'new')} id={id} initial={initial} initialItems={g?.items}/>;
}
function Editor({id,initial,initialItems}:{id?:string;initial?:Form;initialItems?:Item[]}) {
  const router=useRouter(),{user}=useAuth(),{can,loading}=useJcAccess();
  const allowed=can('gatepasses',id?'edit':'add');
  const [form,setForm]=useState<Form>(initial || {gatepass_type:'JC Based',jc_id:[],date:localDate(),return_date:'',sub_contractor_id:'',company_name:'',vehicle_no:'',remarks:''});
  const [items,setItems]=useState<Item[]>(initialItems || []),[busy,setBusy]=useState(false),[loadingItems,setLoadingItems]=useState(false),[error,setError]=useState('');
  const options=useQuery<Options>({queryKey:['gatepassOptions',user?.db,id],enabled:allowed,queryFn:async()=>(await apiClient.get('/gatepass/options',{params:{edit_id:id}})).data.data});
  const requestVersion=useRef(0);
  const loadJcs=async(ids:string[])=>{
    const version=++requestVersion.current;setForm(prev=>({...prev,jc_id:ids}));setError('');
    if(!ids.length){setItems([]);setLoadingItems(false);return;}
    setLoadingItems(true);try{const data=(await apiClient.get('/gatepass/jc-data',{params:{jc_id:ids.join(','),edit_id:id}})).data.data;
      if(version!==requestVersion.current)return;setItems(data.items.map((item:Item)=>({...item,description:'',remarks:''})));setForm(prev=>({...prev,sub_contractor_id:String(data.sub_contractor_id || ''),vehicle_no:data.vehicle_no || prev.vehicle_no}));
    }catch(e){if(version===requestVersion.current){setError(errorMessage(e));setItems([]);}}finally{if(version===requestVersion.current)setLoadingItems(false);}
  };
  const field=<K extends keyof Form>(key:K,value:Form[K])=>setForm(prev=>({...prev,[key]:value}));
  const updateItem=(index:number,key:keyof Item,value:string)=>setItems(prev=>prev.map((row,i)=>i===index?{...row,[key]:value}:row));
  if(loading)return <p className="p-6">Loading...</p>;
  if(!allowed)return <p className="p-6" role="alert">You do not have permission for this action.</p>;
  if(options.isError)return <p className="p-6 text-red-600" role="alert">{errorMessage(options.error)}</p>;
  const misc=form.gatepass_type==='Miscellaneous';
  return <main className="max-w-7xl mx-auto p-6 space-y-5"><LegacyPageHeader title={id?'Edit Gate Pass':'Add Gate Pass'}/><form className="space-y-5" onSubmit={async e=>{e.preventDefault();if(busy || loadingItems || error)return;setBusy(true);try{if(id)await apiClient.put('/gatepass/'+id,{...form,items});else await apiClient.post('/gatepass',{...form,items});toast.success('Gate Pass saved');router.push('/dashboard/gatepass');}catch(e){toast.error(errorMessage(e));}finally{setBusy(false);}}}>
    <section className="bg-white border rounded-xl p-5 space-y-4"><div className="flex gap-6">{['JC Based','Miscellaneous'].map(type=><label key={type}><input type="radio" checked={form.gatepass_type===type} onChange={()=>{requestVersion.current++;setLoadingItems(false);field('gatepass_type',type);field('jc_id',[]);setItems(type==='Miscellaneous'?[emptyItem()]:[]);setError('');}}/> {type}</label>)}</div>
      {!misc && <label className="block text-sm">Job Challans<select multiple required className={inputClass} value={form.jc_id} onChange={e=>void loadJcs(Array.from(e.target.selectedOptions,option=>option.value))}>{options.data?.jcs.map(jc=><option value={jc.id} key={jc.id}>{jc.challan_no}</option>)}</select>{options.data && !options.data.multiple_jcs && <span className="text-amber-700 text-xs">Only one JC per Gate Pass is currently enabled for this company.</span>}</label>}
      <div className="grid grid-cols-2 md:grid-cols-3 gap-4"><label className="text-sm">Date<input required type="date" className={inputClass} value={form.date} onChange={e=>field('date',e.target.value)}/></label><label className="text-sm">Return Date<input type="date" className={inputClass} value={form.return_date} onChange={e=>field('return_date',e.target.value)}/></label>{misc?<label className="text-sm">Company Name<input required className={inputClass} value={form.company_name} onChange={e=>field('company_name',e.target.value)}/></label>:<label className="text-sm">Company<select required className={inputClass} value={form.sub_contractor_id} onChange={e=>field('sub_contractor_id',e.target.value)}><option value="">Select Company</option>{options.data?.vendors.map(v=><option value={v.id} key={v.id}>{v.name}</option>)}</select></label>}<label className="text-sm">Vehicle No.<input className={inputClass} value={form.vehicle_no} onChange={e=>field('vehicle_no',e.target.value)}/></label><label className="text-sm col-span-2">Remarks<textarea className={inputClass} value={form.remarks} onChange={e=>field('remarks',e.target.value)}/></label></div>
    </section>
    {error && <p role="alert" className="text-red-600">{error}</p>}
    <section className="bg-white border rounded-xl p-5 overflow-x-auto"><h2 className="font-semibold mb-3">Material Details</h2><table className="w-full text-sm"><thead><tr>{['Item / Description','Quantity','Unit','Remarks',''].map((title,i)=><th key={i} className="p-2 text-left">{title}</th>)}</tr></thead><tbody>{items.map((item,index)=><tr key={index} className="border-t"><td className="p-2">{misc?<select required className={inputClass} value={item.item_id} onChange={e=>{updateItem(index,'item_id',e.target.value);updateItem(index,'unit',options.data?.products.find(p=>String(p.id)===e.target.value)?.unit_name || 'KG');}}><option value="">Select Raw Material</option>{options.data?.products.map(p=><option value={p.id} key={p.id}>{p.item_name}</option>)}</select>:<><b>{item.item_name}</b>{item.jc_no && <p>JC No: {item.jc_no}</p>}<textarea className={inputClass} placeholder="Item Description / Calculations" value={item.description} onChange={e=>updateItem(index,'description',e.target.value)}/></>}</td><td className="p-2"><input required type="number" min="0.01" step="0.01" readOnly={!misc} className={inputClass} value={item.quantity} onChange={e=>updateItem(index,'quantity',e.target.value)}/></td><td className="p-2"><input readOnly className={inputClass} value={item.unit}/></td><td className="p-2"><input className={inputClass} value={item.remarks} onChange={e=>updateItem(index,'remarks',e.target.value)}/></td><td className="p-2">{misc && <button type="button" onClick={()=>setItems(items.filter((_,i)=>i!==index))}>Remove</button>}</td></tr>)}</tbody></table>{loadingItems && <p>Loading JC materials...</p>}{misc && <button type="button" className="text-cyan-700 mt-3" onClick={()=>setItems([...items,emptyItem()])}>+ Add Item</button>}</section>
    <div className="flex gap-4"><button className={buttonClass} disabled={busy || loadingItems || !!error || options.isLoading}>{busy?'Saving...':'Save Gate Pass'}</button><Link href="/dashboard/gatepass">Cancel</Link></div>
  </form></main>;
}
