'use client';
import {useState} from 'react';
import apiClient from '@/services/apiClient';
import toast from 'react-hot-toast';
import {buttonClass,inputClass,errorMessage,useJcAccess} from './useJcAccess';
export default function SubcontractorModal({onAdded}:{onAdded:(vendor:{id:number;name:string})=>void}) {
  const [open,setOpen]=useState(false),[busy,setBusy]=useState(false);
  const [form,setForm]=useState<Record<string,string>>({});
  const {can}=useJcAccess();
  if (!can('jobchallan','ajaxaddsubcontractor')) return null;
  return <><button type="button" className="text-cyan-700 text-sm underline" onClick={()=>setOpen(true)}>Add Sub Contractor</button>{open && <div className="fixed inset-0 bg-black/40 z-50 flex items-center justify-center"><div role="dialog" aria-modal="true" aria-label="Add Sub Contractor" className="bg-white rounded-xl p-6 w-full max-w-lg space-y-3"><h2 className="font-bold text-lg">Add Sub Contractor</h2>{[['name','Company/Party Name'],['contact_person','Contact Person'],['mobile','Contact Number'],['email','Email'],['pan_no','PAN No.'],['gst_no','GST No.'],['address','Address']].map(([key,label])=><label key={key} className="block text-sm">{label}<input className={inputClass} type={key==='email'?'email':'text'} value={form[key] || ''} onChange={e=>setForm({...form,[key]:e.target.value})}/></label>)}<div className="flex gap-3"><button type="button" disabled={busy} className={buttonClass} onClick={async()=>{if(!form.name?.trim()){toast.error('Company/Party Name is required');return;}setBusy(true);try{const response=await apiClient.post('/job-challan/vendors',form);onAdded(response.data.data);setOpen(false);setForm({});}catch(error){toast.error(errorMessage(error));}finally{setBusy(false);}}}>{busy?'Saving...':'Save'}</button><button type="button" onClick={()=>setOpen(false)}>Cancel</button></div></div></div>}</>;
}
