'use client';
import { formatQty } from '@/utils/formatters';
import {useState} from 'react';
import {useQuery,useQueryClient} from '@tanstack/react-query';
import {useRouter} from 'next/navigation';
import toast from 'react-hot-toast';
import {useAuth} from '@/contexts/AuthContext';
import {DatePicker} from '@/components/ui/DatePicker';
import {ProductSearchSelect} from '@/components/ProductSearchSelect';
import apiClient from '@/services/apiClient';
import {useJcAccess,inputClass,buttonClass,errorMessage,localDate} from './useJcAccess';
type ReturnRow={item_id:string;receive_qty:string;receive_date:string;vehicle_no:string;rate:string;tax_rate:string};
const emptyRow=():ReturnRow=>({item_id:'',receive_qty:'',receive_date:localDate(),vehicle_no:'',rate:'',tax_rate:''});
type PendingItem={item_id:number;item_name:string;original_qty:number;pending_qty:number};
export default function ReturnForm({id,onClose}:{id:string;onClose?:()=>void}) {
  const {user}=useAuth(),{can,loading}=useJcAccess(),router=useRouter(),cache=useQueryClient();
  const [rows,setRows]=useState<ReturnRow[]>([emptyRow()]),[busy,setBusy]=useState(false);
  const allowed=can('jobchallan','itemreceived');
  const pending=useQuery<{items:PendingItem[]}>({queryKey:['receivePending',user?.db,id],enabled:allowed,
    queryFn:async()=>(await apiClient.get('/jc-receive/details',{params:{challan_id:id}})).data.data});
  const taxes=useQuery<{id:number;tax:number}[]>({queryKey:['jcReturnTaxes',user?.db],enabled:allowed,
    queryFn:async()=>(await apiClient.get('/job-challan/tax-master',{params:{context:'receive'}})).data.data});
  const items=(pending.data?.items || []).filter(item=>Number(item.pending_qty)>0);
  const update=(index:number,field:keyof ReturnRow,value:string)=>setRows(previous=>previous.map((row,i)=>i===index?{...row,[field]:value}:row));
  if(loading)return <p>Loading permissions...</p>;
  if(!allowed)return <p role="alert">You do not have permission to receive returned items.</p>;
  return <div className="jc-return-form"><div className="flex justify-between items-center p-3 bg-[#3399CC] text-white">
    <h2>Received Item Job Challan Entry</h2>{onClose && <button type="button" aria-label="Close" onClick={onClose}>×</button>}
  </div><form className="legacy-form p-4 space-y-4" onSubmit={async event=>{
    event.preventDefault();if(busy || pending.isFetching || pending.isError || taxes.isError)return;
    const selected=rows.filter(row=>row.item_id && Number(row.receive_qty)>0);
    if(!selected.length){toast.error('Enter a receive quantity for at least one item.');return;}
    const totals=new Map<string,number>();
    for(const row of selected){totals.set(row.item_id,(totals.get(row.item_id)||0)+Number(row.receive_qty));const item=items.find(item=>String(item.item_id)===row.item_id);
      if(!item || (totals.get(row.item_id) || 0)>Number(item.pending_qty)){toast.error('Receive quantity cannot exceed pending quantity.');return;}}
    setBusy(true);try{
      await apiClient.post(`/job-challan/${id}/receive`,{items:selected});
      await Promise.all(['jobChallans','jobChallan','jcTracking','jc-receive','receiveOptions','receivePending'].map(key=>cache.invalidateQueries({queryKey:[key]})));
      toast.success('All items received successfully');if(onClose)onClose();else router.push('/dashboard/jc-challan');
    }catch(error){toast.error(errorMessage(error));}finally{setBusy(false);}
  }}>
    {(pending.isError || taxes.isError) && <p role="alert" className="text-red-600">{errorMessage(pending.error || taxes.error)}</p>}
    {pending.isLoading && <p>Loading pending quantities...</p>}
    {!pending.isLoading && !items.length && <p>No items are pending for this challan.</p>}
    {rows.map((row,index)=>{const item=items.find(item=>String(item.item_id)===row.item_id),base=Number(row.receive_qty || 0)*Number(row.rate || 0),tax=base*Number(row.tax_rate || 0)/100;
      return <div className="grid grid-cols-2 md:grid-cols-6 gap-3 border-b pb-4" key={index}>
        <label className="md:col-span-2">Item Name<ProductSearchSelect products={items.map(item=>({id:item.item_id,item_name:`${item.item_name} (Pending: ${formatQty(item.pending_qty)})`}))} value={row.item_id} onChange={value=>update(index,'item_id',value)} />
          {item && <small className="text-cyan-700">JC Qty: {formatQty(item.original_qty)} | Pending: {formatQty(item.pending_qty)}</small>}</label>
        <label>Qty<input className={inputClass} type="number" min="0" step="0.01" max={item?.pending_qty} value={row.receive_qty} onChange={e=>update(index,'receive_qty',e.target.value)} /></label>
        <label>Received Date<DatePicker className={inputClass} value={row.receive_date} required={Number(row.receive_qty)>0} onChange={e=>update(index,'receive_date',e.target.value)} /></label>
        <label>Vehicle No.<input className={inputClass} value={row.vehicle_no} onChange={e=>update(index,'vehicle_no',e.target.value)} /></label>
        <label>Rate<input className={inputClass} type="number" min="0" step="0.01" value={row.rate} onChange={e=>update(index,'rate',e.target.value)} /></label>
        <label>GST %<select className={inputClass} value={row.tax_rate} onChange={e=>update(index,'tax_rate',e.target.value)}><option value="">-- Select Tax --</option>{taxes.data?.map(tax=><option value={tax.tax} key={tax.id}>{tax.tax}%</option>)}</select></label>
        <label>Tax Amt<input className={inputClass} readOnly value={tax.toFixed(2)} /></label><label>Total<input className={inputClass} readOnly value={(base+tax).toFixed(2)} /></label>
        <div className="flex items-end"><button type="button" className="text-red-600" onClick={()=>{if(rows.length>1)setRows(rows.filter((_,i)=>i!==index));else toast.error('At least one row is required');}}>Remove</button></div>
      </div>;})}
    <button type="button" className="legacy-button" onClick={()=>setRows([...rows,emptyRow()])}>+ Add More</button>
    <div className="legacy-form-footer"><button type="submit" className={buttonClass} disabled={busy || pending.isFetching || pending.isError || taxes.isError || !items.length}>{busy?'Saving...':'Save'}</button>
      <button type="button" onClick={()=>onClose?onClose():router.back()}>Close</button></div>
  </form></div>;
}
