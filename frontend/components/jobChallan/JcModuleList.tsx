'use client';
import {DatePicker} from '@/components/ui/DatePicker';
import {useState} from 'react';
import {LegacyPageHeader} from '@/components/ui/LegacyPageHeader';
import {useListLocation} from '@/components/ui/useListLocation';
import {formatContractDate} from '@/utils/dateFormatter';
import Link from 'next/link';
import {useQuery} from '@tanstack/react-query';
import {useAuth} from '@/contexts/AuthContext';
import apiClient from '@/services/apiClient';
import {ListPagination} from '@/components/ui/ListPagination';
import {useJcAccess,inputClass,buttonClass,errorMessage} from '@/components/jobChallan/useJcAccess';
interface Row {id:number;challan_id:number;challan_no:string;reference_jc_no:string;jc_date:string;gatepass_no:string;date:string;vendor_name:string;company_name:string;sender_name:string;sender_db:string;item_id:number;item_name:string;dispatch_qty:number;total_received:number;pending_qty:number;vehicle_no:string;status:string;is_manual:number;}
export default function JcModuleList({module}:{module:'receive'|'gatepass'}) {
  const gate=module==='gatepass',controller=gate?'gatepasses':'jobchallan',action=gate?'index':'receiveindex';
  const path=gate?'gatepass':'jc-receive';
  const {can,loading}=useJcAccess(),{user}=useAuth();
  const [page,setPage]=useState(1);
  const [draft,setDraft]=useState({from_date:'',to_date:'',sub_contractor_id:'',limit:gate?'20':'50'}),[filters,setFilters]=useState(draft);
  const locationReady=useListLocation(filters,page,['from_date','to_date','sub_contractor_id','limit'],(next,nextPage)=>{setDraft(next);setFilters(next);setPage(nextPage);});
  const limit=Math.min(100,Math.max(1,Number(filters.limit) || (gate?20:50)));
  const query=useQuery<{items:Row[];total:number}>({queryKey:[path,user?.db,page,limit,filters],enabled:locationReady && can(controller,action),queryFn:async()=>(await apiClient.get('/'+path,{params:{...filters,page,limit}})).data.data});
  const vendors=useQuery<{id:number;name:string}[]>({queryKey:['jcVendors',user?.db],enabled:can(controller,action),queryFn:async()=>(await apiClient.get('/job-challan/vendors')).data.data});
  if (loading) return <p className="p-6">Loading permissions...</p>;
  if (!can(controller,action)) return <p className="p-6" role="alert">You do not have permission to view this module.</p>;
  return <main className="space-y-5"><LegacyPageHeader title={gate?'Gate Passes':'JC Receive List'}/>
    <div className="bg-white border"><div className="legacy-list-heading"><h2>{gate?'Gate Pass List':'Job Challan Receives'}</h2>{can(controller,gate?'add':'receiveadd') && <Link className="legacy-button" href={`/dashboard/${path}/create`}>{gate?'Add Gate Pass':'+ Add New Receive'}</Link>}</div>
    {gate && <form className="legacy-filter-row" onSubmit={e=>{e.preventDefault();setFilters({...draft});setPage(1);}}>
      <label>Company<select className={inputClass} value={draft.sub_contractor_id} onChange={e=>setDraft({...draft,sub_contractor_id:e.target.value})}><option value="">All Companies</option>{vendors.data?.map(v=><option value={v.id} key={v.id}>{v.name}</option>)}</select></label>
      <label>From Date<DatePicker  className={inputClass} value={draft.from_date} onChange={e=>setDraft({...draft,from_date:e.target.value})}/></label>
      <label>To Date<DatePicker  className={inputClass} value={draft.to_date} onChange={e=>setDraft({...draft,to_date:e.target.value})}/></label>
      <label>Limit<select className={inputClass} value={draft.limit} onChange={e=>setDraft({...draft,limit:e.target.value})}>{[20,50,100,500].map(n=><option key={n}>{n}</option>)}</select></label><button className="legacy-button">Search</button>
    </form>}
    {query.isError && <p role="alert" className="text-red-600">{errorMessage(query.error)}</p>}
    <div className="bg-white border rounded-xl overflow-x-auto"><table className="w-full text-sm text-left"><thead className="bg-slate-50"><tr>{(gate?['ID','Gate Pass No','Date','Company','Vehicle No','Status','Actions']:['#','Challan No','Ref JC No','From Company','Vehicle No','JC Date','Item Name','Received Qty','Action']).map(h=><th className="p-3" key={h}>{h}</th>)}</tr></thead><tbody>{query.data?.items.map((row,i)=><tr className="border-t" key={`${row.sender_db || 'local'}:${row.id || row.challan_id}:${row.item_id || i}`}><td className="p-3">{(page-1)*limit+i+1}</td>{gate?<><td className="p-3">{row.gatepass_no}</td><td className="p-3">{formatContractDate(row.date)}</td><td className="p-3">{row.vendor_name || row.company_name}</td><td className="p-3">{row.vehicle_no}</td><td><span className="legacy-status">{row.status}</span></td><td className="p-3 space-x-3">{can(controller,'view') && <Link className="text-cyan-700" href={`/dashboard/gatepass/${row.id}`}>View</Link>}{can(controller,'edit') && <Link className="text-cyan-700" href={`/dashboard/gatepass/${row.id}/edit`}>Edit</Link>}{can(controller,'gatepasspdf') && <Link className="text-cyan-700" href={`/dashboard/gatepass/${row.id}/pdf`}>PDF</Link>}</td></>:<><td className="p-3">{row.is_manual?row.challan_no:<Link className="text-cyan-700" href={`/dashboard/jc-challan/${row.challan_id}?sender_db=${encodeURIComponent(row.sender_db)}`}>{row.challan_no}</Link>}</td><td className="p-3">{row.reference_jc_no || '-'}</td><td className="p-3">{row.sender_name}</td><td className="p-3">{row.vehicle_no}</td><td className="p-3">{formatContractDate(row.jc_date)}</td><td className="p-3">{row.item_name}</td><td className="p-3">{row.total_received}</td><td className="p-3 space-x-2">{(row.is_manual || Number(row.pending_qty)<=0) && <span className="legacy-status">Completed</span>}{!row.is_manual && <>{Number(row.pending_qty)>0 && can('jobchallan','receiveadd') && <Link className="text-cyan-700" href={`/dashboard/jc-receive/create?challan_id=${encodeURIComponent(row.challan_id+'|'+row.sender_db)}`}>Receive</Link>}{can('jobchallan','viewpdf') && <Link className="text-cyan-700" href={`/dashboard/jc-challan/${row.challan_id}/pdf?sender_db=${encodeURIComponent(row.sender_db)}`}>PDF</Link>}</>}</td></>}</tr>)}{(!query.data?.items.length) && <tr><td className="p-8 text-center text-slate-500" colSpan={gate?7:9}>{query.isLoading?'Loading...':'No records found.'}</td></tr>}</tbody></table><ListPagination page={page} limit={limit} total={query.data?.total || 0} onPageChange={setPage} busy={query.isFetching}/></div>
    </div>
  </main>;
}
