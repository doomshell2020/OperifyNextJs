'use client';
import {useEffect,useRef,useState} from 'react';
import Link from 'next/link';
import {useQuery,useQueryClient} from '@tanstack/react-query';
import {FileText,Pencil,Trash2} from 'lucide-react';
import apiClient from '@/services/apiClient';
import {useAuth} from '@/contexts/AuthContext';
import {LegacyPageHeader} from '@/components/ui/LegacyPageHeader';
import {DatePicker} from '@/components/ui/DatePicker';
import {ListPagination} from '@/components/ui/ListPagination';
import {useListLocation} from '@/components/ui/useListLocation';
import {useLegacyActionAccess} from '@/components/ui/useLegacyActionAccess';
import {MaintenanceRow,MachineLookup,useMetadata,inputClass,errorMessage,cap} from '@/components/maintenance/shared';
import {formatContractDate} from '@/utils/dateFormatter';
import './maintenance.css';
const initial={machine_name:'',machines_id:'',assigned_to:'',m_status:'',datefrom:'',dateto:''};
const statuses=['pending','assigned','completed'];
export default function MaintenancePage(){
  const {user}=useAuth(),can=useLegacyActionAccess(),client=useQueryClient(),meta=useMetadata();
  const [draft,setDraft]=useState(initial),[filters,setFilters]=useState(initial),[page,setPage]=useState(1),[busy,setBusy]=useState(false),[error,setError]=useState(''),[notice,setNotice]=useState(''),[complete,setComplete]=useState<MaintenanceRow|null>(null),[completion,setCompletion]=useState({completion_date:'',completion_time:'',remarks:''});
  const dialog=useRef<HTMLDialogElement>(null);
  useEffect(()=>{if(complete)dialog.current?.showModal();else dialog.current?.close();},[complete]);
  const ready=useListLocation(filters,page,Object.keys(initial),(f,p)=>{setFilters(f);setDraft(f);setPage(p);});
  const query=useQuery<{items:MaintenanceRow[];total:number;limit:number}>({queryKey:['maintenance',user?.db,filters,page],enabled:ready && can('maintenance','index'),queryFn:async({signal})=>(await apiClient.get('/maintenance',{params:{...filters,page},signal})).data.data});
  const refresh=async()=>{await client.invalidateQueries({queryKey:['maintenance']});};
  async function mutate(action:()=>Promise<{data:{data:{message:string}}}>,deleted=false){setBusy(true);setError('');try{const result=await action();setNotice(result.data.data.message);setComplete(null);if(deleted && query.data?.items.length===1 && page>1)setPage(page-1);await refresh();}catch(e){setError(errorMessage(e));}finally{setBusy(false);}}
  async function pdf(){setBusy(true);setError('');try{const response=await apiClient.get('/maintenance/pdf',{params:filters,responseType:'blob'}),url=URL.createObjectURL(response.data),a=document.createElement('a');a.href=url;a.download='MaintenanceDetails.pdf';a.click();setTimeout(()=>URL.revokeObjectURL(url),60000);}catch(e){const body=(e as {response?:{data?:Blob}})?.response?.data;if(body instanceof Blob){try{setError(JSON.parse(await body.text()).error?.message || 'Unable to export PDF.');}catch{setError('Unable to export PDF.');}}else setError(errorMessage(e));}finally{setBusy(false);}}
  function status(row:MaintenanceRow,value:string){setError('');if(value==='completed'){setCompletion({completion_date:new Date().toISOString().slice(0,10),completion_time:'',remarks:''});setComplete(row);}else void mutate(()=>apiClient.patch('/maintenance/'+row.id+'/status',{status:value}));}
  return <main className="maintenance-manager space-y-4"><LegacyPageHeader title="Maintenance Manager"/><section className="bg-white border p-3">
    <form className="maintenance-filters" onSubmit={e=>{e.preventDefault();setFilters({...draft});setPage(1);setError('');}}>
      <label>Machine Name<MachineLookup value={draft.machine_name} id={draft.machines_id} onChange={(machine_name,machines_id)=>setDraft({...draft,machine_name,machines_id})}/></label>
      <label>Assigned To<input className={inputClass} placeholder="Assigned To" value={draft.assigned_to} onChange={e=>setDraft({...draft,assigned_to:e.target.value})}/></label>
      <label>Status<select className={inputClass} value={draft.m_status} onChange={e=>setDraft({...draft,m_status:e.target.value})}><option value="">---- Select ----</option>{statuses.map(s=><option key={s} value={s}>{cap(s)}</option>)}</select></label>
      <label>Start Date<DatePicker className={inputClass} value={draft.datefrom} placeholder="Start Date" onChange={e=>setDraft({...draft,datefrom:e.target.value})}/></label>
      <label>End Date<DatePicker className={inputClass} value={draft.dateto} placeholder="End Date" onChange={e=>setDraft({...draft,dateto:e.target.value})}/></label>
      <div className="flex gap-2 items-end"><button className="legacy-button" disabled={busy || query.isFetching}>Search</button><button className="legacy-button" type="button" onClick={()=>{setDraft(initial);setFilters(initial);setPage(1);setError('');setNotice('');}}>Reset</button></div>
      <div className="flex gap-2 items-end justify-end">{can('maintenance','viewpdf') && <button type="button" title="Export PDF" aria-label="Export PDF" disabled={busy} onClick={()=>void pdf()}><FileText size={28}/></button>}{can('maintenance','add') && <Link href="/dashboard/maintenance/breakdowns/add" className="legacy-button">+ Add</Link>}</div>
    </form>
    {notice && <p role="status" className="my-3 text-green-700">{notice}</p>}{(error || query.isError || meta.isError) && <p role="alert" className="my-3 text-red-700">{error || errorMessage(query.error || meta.error)}</p>}
    <div className="maintenance-results"><table className="maintenance-table"><thead><tr>{['No.','Date','Machine Name','Type Of Breakdown','Time(Hrs)','Assigned To','Shift Incharge','Maintenance Incharge','Production Incharge','Remark','Action'].map(h=><th key={h}>{h}</th>)}</tr></thead><tbody>
    {query.data?.items.map((r,i)=><tr key={r.id}><td>{(page-1)*50+i+1}</td><td className="whitespace-nowrap">{formatContractDate(r.datefrom)}</td><td>{r.machine_name}</td><td>{cap(r.breakdown_type)}</td><td>{r.total_time}</td><td>{cap(r.assigned_name)}</td><td>{cap(r.shift_name)}</td><td>{cap(r.incharge_name)}</td><td>{cap(r.production_name)}</td><td>{cap(r.remark)}</td><td><div className="flex gap-2 mb-1">{can('maintenance','edit') && meta.data?.actions.edit && <Link aria-label={'Edit maintenance '+r.id} href={'/dashboard/maintenance/breakdowns/edit/'+r.id}><Pencil size={17}/></Link>}{can('maintenance','status') && meta.data?.actions.delete && <button aria-label={'Delete maintenance '+r.id} disabled={busy} onClick={()=>{if(window.confirm('Are you sure do you want to delete this Maintenance details'))void mutate(()=>apiClient.delete('/maintenance/'+r.id),true);}}><Trash2 size={17} className="text-red-700"/></button>}</div><select aria-label={'Maintenance status '+r.id} className={inputClass} value={r.maintenance_status} disabled={busy || !can('maintenance','maintenancestatus')} onChange={e=>status(r,e.target.value)}>{!statuses.includes(r.maintenance_status) && <option value={r.maintenance_status}>{cap(r.maintenance_status)}</option>}{statuses.map(s=><option key={s} value={s} disabled={s==='completed' && !can('maintenance','completemaintenance')}>{cap(s)}</option>)}</select></td></tr>)}
    {!query.data?.items.length && <tr><td colSpan={11} className="text-center">{query.isLoading?'Loading...':'No Record Found'}</td></tr>}
    </tbody></table></div><ListPagination page={page} limit={50} total={query.data?.total || 0} busy={busy || query.isFetching} onPageChange={setPage}/>
    </section>
    <dialog ref={dialog} className="maintenance-modal" onCancel={e=>{e.preventDefault();if(!busy)setComplete(null);}} onClose={()=>setComplete(null)}><form onSubmit={e=>{e.preventDefault();if(complete)void mutate(()=>apiClient.post('/maintenance/'+complete.id+'/complete',completion));}}><div className="flex justify-between border-b pb-3"><h2>Complete Maintenance</h2><button type="button" aria-label="Close" disabled={busy} onClick={()=>setComplete(null)}>×</button></div><label>Completion Date<input name="completion_date" className={inputClass} value={completion.completion_date} readOnly/></label><label>Completion Time<input name="completion_time" className={inputClass} placeholder="Enter completion time" value={completion.completion_time} onChange={e=>setCompletion({...completion,completion_time:e.target.value})}/></label><label>Remarks<textarea name="remarks" className={inputClass} required placeholder="Enter remarks" value={completion.remarks} onChange={e=>setCompletion({...completion,remarks:e.target.value})}/></label>{error && <p role="alert" className="text-red-700">{error}</p>}<div className="flex justify-between mt-4"><button className="legacy-button" type="submit" disabled={busy}>Submit</button><button className="legacy-button" type="button" disabled={busy} onClick={()=>setComplete(null)}>Close</button></div></form></dialog>
  </main>;
}
