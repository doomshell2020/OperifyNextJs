'use client';
import {useState} from 'react';
import {createPortal} from 'react-dom';
import apiClient from '@/services/apiClient';
import toast from 'react-hot-toast';
import {buttonClass,inputClass,errorMessage,useJcAccess} from './useJcAccess';
export default function SubcontractorModal({onAdded,compact=false}:{onAdded:(vendor:{id:number;name:string})=>void;compact?:boolean}) {
  const [open,setOpen]=useState(false),[busy,setBusy]=useState(false);
  const [form,setForm]=useState<Record<string,string>>({});
  const {can}=useJcAccess();
  if (!can('jobchallan','ajaxaddsubcontractor')) return null;
  return <><button type="button" className={compact?'jc-receive-party-add':'text-cyan-700 text-sm underline'} title="Add Company/Party" aria-label="Add Company/Party" onClick={()=>setOpen(true)}>{compact?'+':'Add Company / Party'}</button>
    {open && createPortal(<div className="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
      <div role="dialog" aria-modal="true" aria-label="Add Company / Party" className="bg-white w-full max-w-[999px]">
        <div className="flex items-center justify-between p-3 bg-[#3399CC] text-white"><h2>Add Company / Party</h2><button type="button" aria-label="Close" onClick={()=>setOpen(false)}>×</button></div>
        <form className="p-5 grid grid-cols-1 md:grid-cols-3 gap-4" onSubmit={async event=>{
          event.preventDefault();event.stopPropagation();if(busy)return;
          if(!form.name?.trim()){toast.error('Company/Party Name is required');return;}
          setBusy(true);try{const response=await apiClient.post('/job-challan/vendors',form);onAdded(response.data.data);setOpen(false);setForm({});}
          catch(error){toast.error(errorMessage(error));}finally{setBusy(false);}
        }}>
          {[['name','Company/Party Name'],['contact_person','Contact Person'],['mobile','Mobile'],['email','Email'],['pan_no','PAN NO.'],['gst_no','GST No.']].map(([key,label])=><label key={key} className="block text-sm">{label}{key==='name' && ' *'}
            <input required={key==='name'} className={inputClass} type={key==='email'?'email':'text'} maxLength={key==='mobile'?15:['pan_no','gst_no'].includes(key)?20:undefined} value={form[key] || ''} onChange={event=>setForm({...form,[key]:event.target.value})}/></label>)}
          <label className="md:col-span-3">Address<textarea rows={2} className={inputClass} value={form.address || ''} onChange={event=>setForm({...form,address:event.target.value})}/></label>
          <div className="md:col-span-3"><button type="submit" disabled={busy} className={buttonClass}>{busy?'Saving...':'Submit'}</button></div>
        </form>
      </div>
    </div>,document.body)}
  </>;
}
