'use client';
import {useRef,useState} from 'react';
import Link from 'next/link';
import {useParams,useRouter} from 'next/navigation';
import {useQuery,useQueryClient} from '@tanstack/react-query';
import apiClient from '@/services/apiClient';
import {useAuth} from '@/contexts/AuthContext';
import {LegacyPageHeader} from '@/components/ui/LegacyPageHeader';
import {DatePicker} from '@/components/ui/DatePicker';
import {DailyRow,Entry,Meta,Lookup,blank,numericFields,useMetadata,useDependencies,inputClass,errorMessage} from './shared';
import '@/app/dashboard/production/sheet/daily-sheet.css';
const base='/dashboard/production/sheet';
function Select({label,value,onChange,options,required=false}:{label:string;value:string;onChange:(v:string)=>void;options:{value:string;label:string}[];required?:boolean}){
  return <label>{label}<select className={inputClass} aria-label={label} required={required} value={value} onChange={e=>onChange(e.target.value)}><option value="">-- Select --</option>{options.map(o=><option value={o.value} key={o.value}>{o.label}</option>)}</select></label>;
}
function Selection({row,onChange,meta,edit=false,datewise=false}:{row:Entry;onChange:(row:Entry)=>void;meta:Meta;edit?:boolean;datewise?:boolean}) {
  const deps=useDependencies(datewise?{...row,machine_id:''}:row);
  const [readingBusy,setReadingBusy]=useState(false),[error,setError]=useState('');
  const latest=useRef(row),selection=useRef(0);latest.current=row;
  async function machine(name:string,id:string){
    const request=++selection.current;
    latest.current={...row,machinename:name,machine_id:id,reading8am:'',reading8pm:'',nextday8am:'',reading_locked:''};
    onChange(latest.current);
    if(!id || datewise)return;
    setReadingBusy(true);setError('');
    try{const response=await apiClient.get('/daily-sheets/dependencies',{params:{machine_id:id}}),reading=response.data.data.reading;if(request===selection.current)onChange({...latest.current,reading8am:reading===null?'':String(reading),reading_locked:reading===null?'':'1'});}catch(e){if(request===selection.current)setError(errorMessage(e));}finally{if(request===selection.current)setReadingBusy(false);}
  }
  const processes=datewise?[...meta.processes].sort((a,b)=>String(a.process_name || '').localeCompare(String(b.process_name || ''))):deps.data?.processes || [];
  const identity=edit;
  return <>
    {datewise && (edit || row.fixed_machine) && <label>Machine Name<input className={inputClass} readOnly value={row.machinename}/></label>}
    <label>Contract Name{identity?<input className={inputClass} readOnly value={row.contractname}/>:<Lookup kind="contracts" required={!datewise || !!row.plan_qty} id={row.contract_id} value={row.contractname} onChange={(contractname,contract_id)=>onChange({...row,contractname,contract_id,item_id:'',po_id:'',productprocess_id:''})}/>}</label>
    {identity?<><label>Product Name<input className={inputClass} readOnly value={row.item_name}/></label><label>Production Order No.<input className={inputClass} readOnly value={row.po_id}/></label></>:<>
      <Select label="Product Name" required={!datewise || !!row.plan_qty} value={row.item_id} options={(deps.data?.products || []).map(p=>({value:String(p.id),label:p.item_name}))} onChange={item_id=>onChange({...row,item_id,po_id:'',productprocess_id:''})}/>
      <Select label="Production Order No." required={!datewise || !!row.plan_qty} value={row.po_id} options={[...(deps.data?.orders || []).map(p=>({value:String(p.po_id),label:String(p.po_id)})),...(edit && row.po_id && !deps.data?.orders.some(o=>String(o.po_id)===row.po_id)?[{value:row.po_id,label:row.po_id}]:[])]} onChange={po_id=>onChange({...row,po_id})}/>
    </>}
    {edit && !datewise?<label>Process Name<input className={inputClass} readOnly value={row.process_name}/></label>:<Select label="Process Name" value={row.productprocess_id} options={processes.map(p=>({value:String(p.id),label:p.process_name}))} onChange={productprocess_id=>{
      const progress=deps.data?.progress.find(p=>String(p.productprocess_id)===productprocess_id);
      if(!edit && !datewise && deps.data?.plannedqty!==null && progress && Number(progress.quantity)>=Number(deps.data?.plannedqty)){setError('This process is already completed.');onChange({...row,productprocess_id:''});return;}
      setError('');onChange({...row,productprocess_id});
    }}/>}
    {!datewise && <label>Machine Name{edit?<input className={inputClass} readOnly value={row.machinename}/>:<Lookup kind="machines" required value={row.machinename} id={row.machine_id} onChange={(name,id)=>void machine(name,id)}/>} {readingBusy && <small>Loading machine reading...</small>}</label>}
    {(error || deps.isError) && <p role="alert" className="text-red-700">{error || errorMessage(deps.error)}</p>}
  </>;
}
function Numbers({row,onChange,single=false,edit=false}:{row:Entry;onChange:(row:Entry)=>void;single?:boolean;edit?:boolean}) {
  const fields=single?[...numericFields.slice(1,4),numericFields[0],...numericFields.slice(4)]:numericFields;
  return <>{fields.map(([key,label])=><label key={key}>{label}<input className={inputClass} aria-label={label} type="number" min="0" step="any" value={row[key] || ''} required={single && key!=='plan_qty'} readOnly={key==='reading8am' && (!!row.reading_locked || single && edit)} onChange={e=>onChange({...row,[key]:e.target.value})}/></label>)}<label>Scrap (Shift A + B)<input className={inputClass} value={row.scrap || ''} maxLength={255} onChange={e=>onChange({...row,scrap:e.target.value})}/></label></>;
}
async function invalidate(client:ReturnType<typeof useQueryClient>){for(const key of ['daily-sheets','daily-entry','daily-datewise','daily-dependencies','daily-order-details','production-orders','production-order-details','contract-details'])await client.invalidateQueries({queryKey:[key]});}
function SingleEditor({initial,meta,id}:{initial:Entry;meta:Meta;id?:string}) {
  const [row,setRow]=useState(initial),[busy,setBusy]=useState(false),[error,setError]=useState('');
  const router=useRouter(),client=useQueryClient();
  async function submit(e:React.FormEvent){e.preventDefault();setBusy(true);setError('');try{if(id)await apiClient.put('/daily-sheets/'+id,row);else await apiClient.post('/daily-sheets',row);await invalidate(client);router.push(base);}catch(e){setError(errorMessage(e));}finally{setBusy(false);}}
  return <form onSubmit={e=>void submit(e)} className="bg-white border p-4"><fieldset disabled={busy}><div className="daily-entry"><label>Date<DatePicker className={inputClass} required value={row.production_date} onChange={e=>setRow({...row,production_date:e.target.value})}/></label><Selection row={row} onChange={setRow} meta={meta} edit={!!id}/><Numbers row={row} onChange={setRow} single edit={!!id}/>{!id && meta.has_completed && <fieldset><legend>Completed :</legend>{['Y','N'].map(v=><label key={v} className="inline-flex mr-4"><input type="radio" name="completed" value={v} checked={row.completed===v} onChange={()=>setRow({...row,completed:v})}/>{v==='Y'?'YES':'NO'}</label>)}</fieldset>}<label>Remark<textarea className={inputClass} value={row.remark || ''} onChange={e=>setRow({...row,remark:e.target.value})}/></label></div><div className="flex gap-3 mt-4"><button className="legacy-button">{busy?'Saving...':'Submit'}</button><Link className="legacy-button" href={base}>Back</Link></div></fieldset>{error && <p role="alert" className="text-red-700 my-3">{error}</p>}</form>;
}
export function SingleForm({editing=false}:{editing?:boolean}) {
  const params=useParams<{id:string}>(),id=editing?params.id:undefined,{user}=useAuth(),meta=useMetadata();
  const detail=useQuery<DailyRow>({queryKey:['daily-entry',user?.db,id],enabled:!!id,queryFn:async()=>(await apiClient.get('/daily-sheets/'+id)).data.data});
  const initial=detail.data?Object.fromEntries(Object.entries({...blank(),...detail.data,production_date:(detail.data.production_date || '').slice(0,10),contractname:detail.data.title+'('+detail.data.workorder+')',machinename:detail.data.machine_name}).map(([k,v])=>[k,v===null?'':String(v)])):{...blank(),production_date:meta.data?.today || ''};
  return <main className="daily-sheet space-y-4"><LegacyPageHeader title={id?'Edit Daily Sheet':'Add Daily Sheet'}/>{meta.isError || detail.isError?<p role="alert">{errorMessage(meta.error || detail.error)}</p>:!meta.data || id && !detail.data?<p>Loading...</p>:<SingleEditor key={user?.db+':'+(id || 'new')} initial={initial} meta={meta.data} id={id}/>}</main>;
}
function DatewiseEditor({initial,meta,date}:{initial:Entry[];meta:Meta;date?:string}) {
  const [rows,setRows]=useState(initial),[busy,setBusy]=useState(false),[error,setError]=useState('');
  const router=useRouter(),client=useQueryClient();
  const update=(i:number,row:Entry)=>setRows(old=>old.map((v,index)=>index===i?row:v));
  async function submit(e:React.FormEvent){e.preventDefault();setBusy(true);setError('');try{const items=rows.filter(r=>r.plan_qty!=='');if(date)await apiClient.put('/daily-sheets/datewise/'+date,{items});else await apiClient.post('/daily-sheets/datewise',{items});await invalidate(client);router.push(base);}catch(e){setError(errorMessage(e));}finally{setBusy(false);}}
  return <form className={"bg-white border p-3"+(!date?" daily-datewise-create":"")} onSubmit={e=>void submit(e)}><fieldset disabled={busy}>{!date && <div className="daily-machine-heading" aria-hidden="true">{["Machine Name","Contract Name","Product Name","Production Order No.","Process Name","Planned Quantity"].map(heading=><span key={heading}>{heading}</span>)}</div>}{rows.map((row,i)=><div key={i} className="daily-machine-row"><div className="daily-machine-grid">{!date && !row.fixed_machine?<label>Machine Name<Lookup kind="machines" id={row.machine_id} value={row.machinename} required={!!row.plan_qty} onChange={(machinename,machine_id)=>update(i,{...row,machinename,machine_id})}/></label>:null}<Selection row={row} onChange={next=>update(i,next)} meta={meta} datewise edit={!!date}/>{date?<><Numbers row={row} onChange={next=>update(i,next)}/><label>Remark<textarea className={inputClass} value={row.remark || ''} onChange={e=>update(i,{...row,remark:e.target.value})}/></label></>:<label>Planned Quantity<input className={inputClass} aria-label={'Planned Quantity '+row.machinename} type="number" min="0" step="any" value={row.plan_qty} onChange={e=>update(i,{...row,plan_qty:e.target.value})}/></label>}</div></div>)}{!rows.length && <p>No Record Found</p>}<div className="flex gap-3 mt-4">{!date && <button type="button" className="legacy-button" onClick={()=>setRows([...rows,blank()])}>+ Add More</button>}<button className="legacy-button">{busy?'Saving...':'Submit'}</button><Link className="legacy-button" href={base}>Back</Link></div></fieldset>{error && <p role="alert" className="text-red-700 my-3">{error}</p>}</form>;
}
export function DatewiseForm({editing=false}:{editing?:boolean}) {
  const params=useParams<{date:string}>(),date=editing?params.date:undefined,{user}=useAuth(),meta=useMetadata();
  const detail=useQuery<{date:string;items:DailyRow[]}>({queryKey:['daily-datewise',user?.db,date],enabled:!!date,queryFn:async()=>(await apiClient.get('/daily-sheets/datewise/'+date)).data.data});
  const initial=date?(detail.data?.items || []).map(row=>Object.fromEntries(Object.entries({...blank(),...row,contractname:row.title+'('+row.workorder+')',machinename:row.machine_name,reading_locked:row.reading_locked?'1':''}).map(([k,v])=>[k,v===null?'':String(v)]))):(meta.data?.machines || []).map(m=>({...blank(),machine_id:String(m.id),machinename:m.machine_name,fixed_machine:'1'}));
  return <main className="daily-sheet space-y-4"><LegacyPageHeader title={date?'Daily Sheet '+date.split('-').reverse().join('-'):'Add Date Wise Daily Sheet'}/>{!date && meta.data && <p>Date: {meta.data.today.split('-').reverse().join('-')}</p>}{meta.isError || detail.isError?<p role="alert">{errorMessage(meta.error || detail.error)}</p>:!meta.data || date && !detail.data?<p>Loading...</p>:<DatewiseEditor key={user?.db+':'+(date || 'new')} initial={initial} meta={meta.data} date={date}/>}</main>;
}
