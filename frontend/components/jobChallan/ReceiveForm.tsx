'use client';
import { formatQty } from '@/utils/formatters';
import {useState} from 'react';
import {useRouter,useSearchParams} from 'next/navigation';
import {useQuery,useQueryClient} from '@tanstack/react-query';
import {Plus,Trash2} from 'lucide-react';
import toast from 'react-hot-toast';
import {DatePicker} from '@/components/ui/DatePicker';
import {LegacyPageHeader} from '@/components/ui/LegacyPageHeader';
import {formatContractDate} from '@/utils/dateFormatter';
import {useAuth} from '@/contexts/AuthContext';
import apiClient from '@/services/apiClient';
import {inputClass,useJcAccess,errorMessage} from './useJcAccess';
import SubcontractorModal from './SubcontractorModal';
import ReceiveSelect from './ReceiveSelect';
import ReturnForm from './ReturnForm';

interface Item {item_id:string|number;item_name?:string;category?:string;unit_name?:string;original_qty?:number;received_qty?:number;pending_qty?:number;receive_qty:string;}
interface Options {manual_supported:boolean;eligible:{key:string;challan_no:string;sender_name:string}[];products:{id:number;item_name:string;unit_name:string}[];vendors:{id:number;name:string;gst_no:string}[];}
export default function ReceiveForm({returnId,onClose}:{returnId?:string;onClose?:()=>void}) {
  return returnId?<ReturnForm id={returnId} onClose={onClose}/>:<ReceiveAddForm/>;
}

function ReceiveAddForm() {
  const router=useRouter(),search=useSearchParams(),{user}=useAuth(),{can,loading}=useJcAccess(),cache=useQueryClient();
  // Add New Receive is the simple manual form. The row-level Receive link
  // retains the local JC workflow without exposing extra type switches.
  const linked=!!search.get('challan_id');
  const [form,setForm]=useState({challan_id:search.get('challan_id') || '',challan_no:'',reference_jc_no:'',challan_date:'',vehicle_no:'',from_company:''});
  const [manualItems,setItems]=useState<Item[]>([{item_id:'',receive_qty:''}]),[busy,setBusy]=useState(false);
  const [edits,setEdits]=useState<Record<string,string>>({});
  const allowed=can('jobchallan','receiveadd');
  const options=useQuery<Options>({queryKey:['receiveOptions',user?.db],enabled:allowed,
    queryFn:async()=>(await apiClient.get('/jc-receive/options')).data.data});
  const details=useQuery<{challan_no:string;jc_date:string;from_company:string;items:Item[]}>({queryKey:['receivePending',user?.db,form.challan_id],enabled:allowed && linked && !!form.challan_id,
    queryFn:async()=>(await apiClient.get('/jc-receive/details',{params:{challan_id:form.challan_id}})).data.data});
  const items=linked?(details.data?.items || []).filter(item=>Number(item.pending_qty)>0).map(item=>({...item,receive_qty:edits[String(item.item_id)] || ''})):manualItems;
  const update=(field:keyof typeof form,value:string)=>setForm(previous=>({...previous,[field]:value}));
  const updateItem=(index:number,field:keyof Item,value:string)=>setItems(previous=>previous.map((item,i)=>i===index?{...item,[field]:value}:item));
  if(loading)return <p className="p-6">Loading permissions...</p>;
  if(!allowed)return <p className="p-6" role="alert">You do not have permission to receive this JC.</p>;
  return <main className="legacy-jc-page space-y-5"><LegacyPageHeader title="JC Receive"/>
    <form className="legacy-form jc-simple-receive" onSubmit={async event=>{
      event.preventDefault();if(busy || options.isFetching || options.isError || (linked && (details.isFetching || details.isError)))return;
      if(!linked && (!form.challan_no.trim() || !form.challan_date || !form.from_company)){toast.error('Please fill all required Challan details.');return;}
      const selected=items.filter(item=>item.item_id && Number(item.receive_qty)>0);
      if(!selected.length){toast.error('Enter a receive quantity for at least one item.');return;}
      if(linked && selected.some(item=>Number(item.receive_qty)>Number(item.pending_qty))){toast.error('Receive quantity cannot exceed pending quantity.');return;}
      setBusy(true);try{
        await apiClient.post('/jc-receive',{...form,jc_type:linked?'In JC':'Others',manual_jc_type:'In JC',items:selected,manual_items:selected});
        await Promise.all(['jobChallans','jobChallan','jcTracking','jc-receive','receiveOptions','receivePending'].map(key=>cache.invalidateQueries({queryKey:[key]})));
        toast.success('JC received successfully');router.push('/dashboard/jc-receive');
      }catch(error){toast.error(errorMessage(error));}finally{setBusy(false);}
    }}>
      <h2 className="jc-receive-heading">Receive Add</h2>
      {!linked?<>
        <div className="jc-receive-header-fields">
          <label>Challan No <span className="text-red-600">*</span><input required className={inputClass} placeholder="Enter Challan No" value={form.challan_no} onChange={e=>update('challan_no',e.target.value)}/></label>
          <label>Reference JC No<input className={inputClass} placeholder="Optional" value={form.reference_jc_no} onChange={e=>update('reference_jc_no',e.target.value)}/></label>
          <label>Challan Date <span className="text-red-600">*</span><DatePicker required className={inputClass} placeholder="Select Date" value={form.challan_date} onChange={e=>update('challan_date',e.target.value)}/></label>
          <label>Vehicle No<input className={inputClass} placeholder="Enter Vehicle No" value={form.vehicle_no} onChange={e=>update('vehicle_no',e.target.value)}/></label>
          <div><div className="jc-receive-field-label">From Company / Party <span className="text-red-600">*</span> <SubcontractorModal compact onAdded={vendor=>{void options.refetch();update('from_company',String(vendor.id));}}/></div>
            <ReceiveSelect label="From Company / Party" options={options.data?.vendors || []} value={form.from_company} onChange={value=>update('from_company',value)} placeholder="[ Select Company/Party ]"/>
          </div>
        </div>
        <label className="jc-receive-gst">GST No.<input readOnly className={inputClass} value={options.data?.vendors.find(vendor=>String(vendor.id)===form.from_company)?.gst_no || ''}/></label>
      </>:<div className="jc-receive-local-fields">
        <label>Created JC<select required className={inputClass} value={form.challan_id} onChange={e=>{update('challan_id',e.target.value);setEdits({});}}><option value="">Select Job Challan</option>{options.data?.eligible.map(jc=><option key={jc.key} value={jc.key}>{jc.challan_no} ({jc.sender_name})</option>)}</select></label>
        <label>Vehicle No<input className={inputClass} value={form.vehicle_no} onChange={e=>update('vehicle_no',e.target.value)}/></label>
        {details.data && <div className="jc-receive-local-summary"><p>JC Number: {details.data.challan_no}</p><p>JC Date: {formatContractDate(details.data.jc_date)}</p><p>From Company: {details.data.from_company}</p></div>}
      </div>}
      {(options.isError || (linked && details.isError)) && <p role="alert" className="text-red-600">{errorMessage(options.error || details.error)}</p>}
      {!linked && options.data && !options.data.manual_supported && <p role="alert" className="text-red-600">Manual receipts are not enabled for this company.</p>}
      <div className="jc-receive-items overflow-x-auto"><table className="w-full">
        {!linked && <colgroup><col style={{width:'70%'}}/><col style={{width:'25%'}}/><col style={{width:'5%'}}/></colgroup>}
        <thead><tr>{(linked?['Product','Category','Original Qty','Received Qty','Pending Qty','Receive Qty']:['Product','Receive Qty','Action']).map(title=><th key={title}>{title}{!linked && title!=='Action' && <span className="text-red-600"> *</span>}</th>)}</tr></thead>
        <tbody>{items.map((item,index)=><tr key={linked?item.item_id:index}>{!linked?<>
          <td><ReceiveSelect label={`Product ${index+1}`} options={(options.data?.products || []).map(product=>({id:product.id,name:product.item_name}))} value={String(item.item_id)} onChange={value=>updateItem(index,'item_id',value)} placeholder="[ Select Product ]"/></td>
          <td><div className="jc-receive-quantity"><input type="number" min="0" step="0.01" className={inputClass} placeholder="0" aria-label={`Receive quantity ${index+1}`} value={item.receive_qty} onChange={e=>updateItem(index,'receive_qty',e.target.value)}/><b>{options.data?.products.find(product=>String(product.id)===String(item.item_id))?.unit_name || 'Unit'}</b></div></td>
          <td className="text-center"><button type="button" className="jc-receive-remove" title="Remove" aria-label={`Remove product ${index+1}`} onClick={()=>setItems(previous=>previous.length>1?previous.filter((_,i)=>i!==index):[{item_id:'',receive_qty:''}])}><Trash2 size={14}/></button></td>
        </>:<><td>{item.item_name}</td><td>{item.category}</td><td>{formatQty(item.original_qty)}</td><td>{formatQty(item.received_qty)}</td><td>{formatQty(item.pending_qty)}</td><td><input aria-label={`Receive quantity for ${item.item_name}`} className={inputClass} type="number" min="0" step="0.01" max={item.pending_qty} value={item.receive_qty} onChange={e=>setEdits(previous=>({...previous,[String(item.item_id)]:e.target.value}))}/></td></>}</tr>)}</tbody>
      </table></div>
      {(options.isLoading || (linked && details.isFetching)) && <p>Loading items...</p>}
      {!linked && <button type="button" className="jc-receive-add" onClick={()=>setItems(previous=>[...previous,{item_id:'',receive_qty:''}])}><Plus size={14}/> Add Item</button>}
      <div className="jc-receive-save-row"><button type="submit" className="jc-receive-save" disabled={busy || options.isFetching || options.isError || (linked?(details.isFetching || details.isError):!options.data?.manual_supported)}>{busy?'Saving...':'Save JC Receive'}</button></div>
    </form>
  </main>;
}
