'use client';
import {useEffect,useState} from 'react';
import Link from 'next/link';
import {useRouter} from 'next/navigation';
import {useQuery,useQueryClient} from '@tanstack/react-query';
import toast from 'react-hot-toast';
import apiClient from '@/services/apiClient';
import {useAuth} from '@/contexts/AuthContext';
import {LegacyPageHeader} from '@/components/ui/LegacyPageHeader';
import {DatePicker} from '@/components/ui/DatePicker';
import {MaintenanceRow,MachineLookup,useMetadata,inputClass,errorMessage} from './shared';
import '@/app/dashboard/maintenance/breakdowns/maintenance.css';
const blank={datefrom:'',machines_id:'',machine_name:'',breakdown_type:'',total_time:'',assigned_to:'',shift_incharge:'',maintenance_incharge:'',production_head:'',remark:''};
export default function MaintenanceForm({id}:{id?:string}){
  const {user}=useAuth(),router=useRouter(),client=useQueryClient(),meta=useMetadata();
  const [form,setForm]=useState(blank),[error,setError]=useState(''),[busy,setBusy]=useState(false),[loaded,setLoaded]=useState('');
  const query=useQuery<MaintenanceRow>({queryKey:['maintenance-edit',user?.db,id],enabled:!!id,queryFn:async({signal})=>(await apiClient.get('/maintenance/'+id,{signal})).data.data});
  useEffect(()=>{const key=String(user?.db)+':'+String(id);if(loaded===key)return;if(id && query.data){const r=query.data;setForm({datefrom:r.datefrom?.slice(0,10) || '',machines_id:String(r.machine_id),machine_name:r.machine_name || '',breakdown_type:r.breakdown_type || '',total_time:r.total_time || '',assigned_to:r.assigned_to || '',shift_incharge:r.shift_incharge || '',maintenance_incharge:r.maintenance_incharge || '',production_head:r.production_head || '',remark:r.remark || ''});setLoaded(key);}else if(!id && meta.data){setForm({...blank,datefrom:meta.data.today});setLoaded(key);}},[query.data,meta.data,id,user?.db,loaded]);
  const field=(key:keyof typeof blank,value:string)=>setForm(f=>({...f,[key]:value}));
  const personnel=[['assigned_to','Assigned To','assigned'],['shift_incharge','Shift Incharge','shifts'],['maintenance_incharge','Maintenance Incharge','incharges'],['production_head','Production Head','heads']] as const;
  async function submit(){setError('');if(!form.machines_id){setError('Your entered machine does not exists.');return;}setBusy(true);try{const response=id?await apiClient.put('/maintenance/'+id,form):await apiClient.post('/maintenance',form);toast.success(response.data.data.message);for(const warning of response.data.data.warnings || [])toast(warning);await client.invalidateQueries({queryKey:['maintenance']});await client.invalidateQueries({queryKey:['maintenance-edit']});router.push('/dashboard/maintenance/breakdowns');}catch(e){setError(errorMessage(e));}finally{setBusy(false);}}
  if(query.isLoading || meta.isLoading)return <p>Loading...</p>;
  if(query.isError || meta.isError)return <p role="alert">{errorMessage(query.error || meta.error)}</p>;
  if(id && !meta.data?.actions.edit)return <p role="alert">You do not have permission to edit maintenance records.</p>;
  return <main className="maintenance-manager space-y-4"><LegacyPageHeader title="Maintenance Manager"/><form className="bg-white border p-5" onSubmit={e=>{e.preventDefault();void submit();}}><fieldset disabled={busy}><div className="maintenance-form">
    <label>Date<DatePicker className={inputClass} value={form.datefrom} onChange={e=>field('datefrom',e.target.value)}/></label>
    <label>Machine Name <span className="text-red-600">*</span><MachineLookup required value={form.machine_name} id={form.machines_id} onChange={(machine_name,machines_id)=>setForm(f=>({...f,machine_name,machines_id}))}/></label>
    <label>Type Of Breakdown <span className="text-red-600">*</span><input className={inputClass} required maxLength={255} placeholder="Enter Type Of Breakdown" value={form.breakdown_type} onChange={e=>field('breakdown_type',e.target.value)}/></label>
    <label>{id?'Total Time Takes':'Total Time Required (Hrs.)'} <span className="text-red-600">*</span><input className={inputClass} type="number" required value={form.total_time} onChange={e=>field('total_time',e.target.value)}/></label>
    {personnel.map(([key,label,group])=><label key={key}>{label} <span className="text-red-600">*</span>{id?<input className={inputClass} required maxLength={255} value={form[key]} onChange={e=>field(key,e.target.value)}/>:<select className={inputClass} required value={form[key]} onChange={e=>field(key,e.target.value)}><option value="">------select------</option>{meta.data?.[group].map(p=><option key={p.id} value={p.id}>{p.user_name}</option>)}</select>}</label>)}
    <label className="maintenance-remark">Remark <span className="text-red-600">*</span><textarea className={inputClass} required rows={4} placeholder="Enter Remark" value={form.remark} onChange={e=>field('remark',e.target.value)}/></label>
    </div>{error && <p role="alert" className="text-red-700 my-4">{error}</p>}<div className="flex justify-between border-t pt-4 mt-5"><Link href="/dashboard/maintenance/breakdowns" className="legacy-button">Back</Link><button className="legacy-button" disabled={busy}>{busy?'Saving...':id?'Update':'Add'}</button></div></fieldset></form></main>;
}
