'use client';
import {DatePicker} from '@/components/ui/DatePicker';
import {LegacyPageHeader} from '@/components/ui/LegacyPageHeader';
import {useRef,useState} from 'react';
import {useQuery,useQueryClient} from '@tanstack/react-query';
import Select from 'react-select';
import {useRouter} from 'next/navigation';
import Link from 'next/link';
import toast from 'react-hot-toast';
import apiClient from '@/services/apiClient';
import {useAuth} from '@/contexts/AuthContext';
import {useJcAccess,inputClass,buttonClass,errorMessage,localDate} from './useJcAccess';
interface Item {id?:number;item_id:number|string;item_name?:string;quantity:number|string;unit:string;description:string;remarks:string;jc_no?:string;}
interface Options {jcs:{id:number;challan_no:string}[];vendors:{id:number;name:string}[];products:{id:number;item_name:string;unit_name:string}[];multiple_jcs:boolean;}
interface Form {gatepass_type:string;jc_id:string[];date:string;return_date:string;sub_contractor_id:string;company_name:string;vehicle_no:string;remarks:string;}
const emptyItem=():Item=>({item_id:'',quantity:'',unit:'',description:'',remarks:''});
export default function GatepassForm({id}:{id?:string}) {
  const {user}=useAuth(),{can,loading}=useJcAccess();
  const allowed=can('gatepasses',id?'edit':'add');
  const detail=useQuery({queryKey:['gatepassEdit',user?.db,id],enabled:!!id && allowed,queryFn:async()=>(await apiClient.get('/gatepass/'+id+'/edit-data')).data.data});
  if(loading || (id && detail.isLoading))return <p className="p-6">Loading...</p>;
  if(!allowed)return <p className="p-6" role="alert">You do not have permission for this action.</p>;
  if(detail.isError)return <p className="p-6 text-red-600" role="alert">{errorMessage(detail.error)}</p>;
  const g=detail.data?.gatepass;
  const initial=g?{gatepass_type:g.gatepass_type || 'JC Based',jc_id:String(g.jc_id || '').split(',').filter(Boolean),date:g.date || localDate(),return_date:g.return_date || '',sub_contractor_id:String(g.sub_contractor_id || ''),company_name:g.company_name || '',vehicle_no:g.vehicle_no || '',remarks:g.remarks || ''}:undefined;
  return <Editor key={String(user?.db)+':'+(id || 'new')} id={id} initial={initial} initialItems={g?.items} number={g?.gatepass_no} vendorName={g?.vendor_name}/>;
}
function Editor({id,initial,initialItems,number,vendorName}:{id?:string;initial?:Form;initialItems?:Item[];number?:string;vendorName?:string}) {
  const queryClient=useQueryClient();
  const router=useRouter(),{user}=useAuth(),{can,loading}=useJcAccess();
  const allowed=can('gatepasses',id?'edit':'add');
  const [form,setForm]=useState<Form>(initial || {gatepass_type:'JC Based',jc_id:[],date:localDate(),return_date:'',sub_contractor_id:'',company_name:'',vehicle_no:'',remarks:''});
  const [items,setItems]=useState<Item[]>(initialItems || []),[busy,setBusy]=useState(false),[loadingItems,setLoadingItems]=useState(false),[error,setError]=useState('');
  const options=useQuery<Options>({queryKey:['gatepassOptions',user?.db,id],enabled:allowed,queryFn:async()=>(await apiClient.get('/gatepass/options',{params:{edit_id:id}})).data.data});
  const requestVersion=useRef(0);
  const loadJcs=async(ids:string[])=>{
    const version=++requestVersion.current;setForm(prev=>({...prev,jc_id:ids}));setError('');
    if(!ids.length){setItems([]);setLoadingItems(false);setForm(prev=>({...prev,sub_contractor_id:'',vehicle_no:''}));return;}
    setLoadingItems(true);try{const data=(await apiClient.get('/gatepass/jc-data',{params:{jc_id:ids.join(','),edit_id:id}})).data.data;
      if(version!==requestVersion.current)return;setItems(data.items.map((item:Item)=>({...item,description:'',remarks:''})));setForm(prev=>({...prev,sub_contractor_id:String(data.sub_contractor_id || ''),vehicle_no:data.vehicle_no || ''}));
    }catch(e){if(version===requestVersion.current){setError(errorMessage(e));setItems([]);}}finally{if(version===requestVersion.current)setLoadingItems(false);}
  };
  const field=<K extends keyof Form>(key:K,value:Form[K])=>setForm(prev=>({...prev,[key]:value}));
  const updateItem=(index:number,key:keyof Item,value:string)=>setItems(prev=>prev.map((row,i)=>i===index?{...row,[key]:value}:row));
  if(loading)return <p className="p-6">Loading...</p>;
  if(!allowed)return <p className="p-6" role="alert">You do not have permission for this action.</p>;
  if(options.isError)return <p className="p-6 text-red-600" role="alert">{errorMessage(options.error)}</p>;
  const misc=form.gatepass_type==='Miscellaneous';
  const jcOptions=options.data?.jcs.map(jc=>({value:String(jc.id),label:jc.challan_no})) || [];
  const vendors=options.data?.vendors || [];
  return <main className="legacy-gatepass space-y-5">
    <LegacyPageHeader title={id?'Edit Gate Pass':'Add Gate Pass'}/>
    <form className="legacy-form legacy-gatepass-form space-y-5" onSubmit={async e=>{
      e.preventDefault();if(busy || loadingItems || error)return;
      if(misc && !items.length){toast.error('Please add at least one raw material item.');return;}
      setBusy(true);
      try{
        if(id)await apiClient.put('/gatepass/'+id,{...form,items});else await apiClient.post('/gatepass',{...form,items});
        await Promise.all(['gatepass','gatepassDetail','gatepassEdit','gatepassOptions'].map(key=>queryClient.invalidateQueries({queryKey:[key]})));
        toast.success(id?'The gate pass has been updated.':'The gate pass has been saved.');router.push('/dashboard/gatepass');
      }catch(e){toast.error(errorMessage(e));}finally{setBusy(false);}
    }}>
      <h2 className="legacy-box-heading">Gate Pass Details{number?` (${number})`:''}</h2>
      <fieldset className="space-y-2"><legend>Get Pass Type: *</legend><div className="flex gap-6">{['JC Based','Miscellaneous'].map(type=><label key={type}><input type="radio" name="gatepass_type" checked={form.gatepass_type===type} onChange={()=>{
        requestVersion.current++;setLoadingItems(false);setForm(prev=>({...prev,gatepass_type:type,jc_id:[],sub_contractor_id:'',company_name:''}));
        setItems(type==='Miscellaneous'?[emptyItem()]:[]);setError('');
      }}/> {type}</label>)}</div></fieldset>
      <hr/>
      {!misc && <div className="max-w-md"><label htmlFor="gatepass-jcs">{id?'Job Challan ID':'Select Job Challan'} *</label>
        <Select classNamePrefix="gatepass-select" inputId="gatepass-jcs" instanceId="gatepass-jcs" isMulti isSearchable required isDisabled={busy || options.isLoading}
          options={jcOptions} value={jcOptions.filter(jc=>form.jc_id.includes(jc.value))}
          onChange={selected=>void loadJcs(selected.map(jc=>jc.value))} placeholder="Select JC"
          isOptionDisabled={option=>!options.data?.multiple_jcs && form.jc_id.length>0 && !form.jc_id.includes(option.value)}/>
      </div>}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <label>Date *<DatePicker required className={inputClass} value={form.date} onChange={e=>field('date',e.target.value)}/></label>
        <label>Return Date<DatePicker className={inputClass} value={form.return_date} onChange={e=>field('return_date',e.target.value)}/></label>
        {misc?<label>To Company *<input required className={inputClass} value={form.company_name} placeholder="Enter Company Name" onChange={e=>field('company_name',e.target.value)}/></label>:
          <div><label htmlFor="gatepass-company">To Company *</label><Select classNamePrefix="gatepass-select" inputId="gatepass-company" instanceId="gatepass-company" required isSearchable
            options={[...vendors,...(form.sub_contractor_id && !vendors.some(v=>String(v.id)===form.sub_contractor_id)?[{id:Number(form.sub_contractor_id),name:vendorName || form.sub_contractor_id}]:[])].map(v=>({value:String(v.id),label:v.name}))}
            value={form.sub_contractor_id?{value:form.sub_contractor_id,label:vendors.find(v=>String(v.id)===form.sub_contractor_id)?.name || vendorName || form.sub_contractor_id}:null}
            onChange={v=>field('sub_contractor_id',v?.value || '')} placeholder="Select Company"/></div>}
        <label>Vehicle No.<input className={inputClass} value={form.vehicle_no} onChange={e=>field('vehicle_no',e.target.value)}/></label>
      </div>
      <label className="block">Remarks<textarea rows={2} className={inputClass} value={form.remarks} onChange={e=>field('remarks',e.target.value)}/></label>
      {error && <p role="alert" className="text-red-600">{error}</p>}
      <section className="overflow-x-auto"><h3 className="mb-3">{misc?'Raw Material Items':'Items'}</h3>
        <table className="w-full text-sm"><thead><tr>{(misc?['Raw Material *','Quantity *','Unit','Remarks','Action']:['Item','Quantity','Unit','Remarks']).map(title=><th key={title}>{title}</th>)}</tr></thead>
          <tbody>{items.map((item,index)=><tr key={item.id || index}>
            <td>{misc?<Select classNamePrefix="gatepass-select" inputId={`gatepass-material-${index}`} instanceId={`gatepass-material-${index}`} required isSearchable menuPortalTarget={typeof document!=='undefined'?document.body:undefined} styles={{menuPortal:base=>({...base,zIndex:9999})}} placeholder="Select Item"
              options={[...(options.data?.products || []),...(item.item_id && !options.data?.products.some(p=>String(p.id)===String(item.item_id))?[{id:Number(item.item_id),item_name:item.item_name || String(item.item_id),unit_name:item.unit}]:[])].map(p=>({value:String(p.id),label:p.item_name}))}
              value={item.item_id?{value:String(item.item_id),label:options.data?.products.find(p=>String(p.id)===String(item.item_id))?.item_name || item.item_name || String(item.item_id)}:null}
              onChange={p=>{updateItem(index,'item_id',p?.value || '');updateItem(index,'unit',options.data?.products.find(item=>String(item.id)===p?.value)?.unit_name || 'KG');}}/>:
              <><strong>{item.item_name || 'Unknown Item'}</strong><textarea className={inputClass} rows={3} placeholder="Item Description / Calculations (e.g. 1+2=3 KM)" value={item.description || ''} onChange={e=>updateItem(index,'description',e.target.value)}/></>}</td>
            <td><input required type="number" min={misc?'0.01':undefined} step="0.01" readOnly={!misc} className={inputClass} value={item.quantity} onChange={e=>updateItem(index,'quantity',e.target.value)}/></td>
            <td><input readOnly className={inputClass} value={item.unit || ''}/></td>
            <td><input className={inputClass} value={item.remarks || ''} onChange={e=>updateItem(index,'remarks',e.target.value)}/></td>
            {misc && <td><button type="button" className="gatepass-remove" aria-label={`Remove item ${index+1}`} onClick={()=>setItems(items.filter((_,i)=>i!==index))}>&times;</button></td>}
          </tr>)}{!items.length && <tr><td colSpan={misc?5:4} className="text-center text-slate-500">{loadingItems?'Loading JC materials...':misc?'No items found.':form.jc_id.length?'No items found for this Job Challan.':'Please select a Job Challan to load items.'}</td></tr>}</tbody>
        </table>{misc && <button type="button" className="gatepass-add-item mt-3" onClick={()=>setItems([...items,emptyItem()])}>+ Add Item</button>}
      </section>
      <div className="gatepass-footer"><button className={buttonClass} disabled={busy || loadingItems || !!error || options.isLoading}>{busy?'Saving...':'Save Gate Pass'}</button><Link className="gatepass-default-button" href="/dashboard/gatepass">Cancel</Link></div>
    </form>
  </main>;
}
