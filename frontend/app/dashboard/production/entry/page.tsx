'use client';
import {useState} from 'react';
import Link from 'next/link';
import {useQuery,useQueryClient} from '@tanstack/react-query';
import {CircleCheck,FileSpreadsheet,Trash2} from 'lucide-react';
import apiClient from '@/services/apiClient';
import {useAuth} from '@/contexts/AuthContext';
import {LegacyPageHeader} from '@/components/ui/LegacyPageHeader';
import {DatePicker} from '@/components/ui/DatePicker';
import {ListPagination} from '@/components/ui/ListPagination';
import {useListLocation} from '@/components/ui/useListLocation';
import {useLegacyActionAccess} from '@/components/ui/useLegacyActionAccess';
import {ContractDetailsModal} from '@/components/dashboard/ContractDetailsModal';
import ProductionOrderDetails from '@/components/productionOrder/ProductionOrderDetails';
import {ContractInput,Order,inputClass,errorMessage,qty} from '@/components/productionOrder/shared';
import {formatContractDate as date} from '@/utils/dateFormatter';
import './production-orders.css';
const initial={contractname:'',contract_id:'',status:'',datefrom:'',dateto:'',sort:'',direction:''};
export default function ProductionOrdersPage(){
  const {user}=useAuth(),can=useLegacyActionAccess(),client=useQueryClient();
  const [draft,setDraft]=useState(initial),[filters,setFilters]=useState(initial),[page,setPage]=useState(1);
  const [po,setPo]=useState(''),[contract,setContract]=useState(0),[busy,setBusy]=useState(false),[exporting,setExporting]=useState(false),[notice,setNotice]=useState(''),[error,setError]=useState('');
  const ready=useListLocation(filters,page,Object.keys(initial),(next,p)=>{setFilters(next);setDraft(next);setPage(p);});
  const query=useQuery<{items:Order[];total:number;limit:number}>({queryKey:['production-orders',user?.db,filters,page],enabled:ready && can('production','productionorders'),queryFn:async()=>(await apiClient.get('/production-orders',{params:{...filters,page}})).data.data});
  async function action(row:Order,remove=false){
    if(!window.confirm(remove?'Are you sure do you want to delete this Production Order':'Are you sure do you want to '+(row.status==='C'?'Open':'close')+' this Production Order'))return;
    setBusy(true);setError('');setNotice('');
    try{const response=remove?await apiClient.delete('/production-orders/'+row.id):await apiClient.patch('/production-orders/'+row.id+'/status',{status:row.status==='C'?'O':'C'});setNotice(response.data.data.message);setPage(1);await client.invalidateQueries({queryKey:['production-orders']});await client.invalidateQueries({queryKey:['production-order-details']});await client.invalidateQueries({queryKey:['contract-details']});}
    catch(e){setError(errorMessage(e));}finally{setBusy(false);}
  }
  async function download(){setExporting(true);setError('');try{const response=await apiClient.get('/production-orders/export',{params:filters,responseType:'blob'});const url=URL.createObjectURL(response.data);const a=document.createElement('a');a.href=url;a.download=String(response.headers['content-disposition'] || '').match(/filename="?([^";]+)/)?.[1] || 'Production_order.xlsx';document.body.appendChild(a);a.click();a.remove();window.setTimeout(()=>URL.revokeObjectURL(url),60000);}catch(e){const blob=(e as {response?:{data?:Blob}}).response?.data;if(blob instanceof Blob){try{const body=JSON.parse(await blob.text());setError(body.error?.message || body.message || 'Unable to export.');}catch{setError('Unable to export. Please try again.');}}else setError(errorMessage(e));}finally{setExporting(false);}}
  return <main className="production-orders space-y-4"><LegacyPageHeader title="Production Orders"/><div className="bg-white border p-3">
    <form className="production-order-filters" onSubmit={e=>{e.preventDefault();setFilters({...draft});setPage(1);setError('');}}>
      <label>Contract Name<ContractInput selectedId={draft.contract_id} value={draft.contractname} onChange={(contractname,contract_id)=>setDraft({...draft,contractname,contract_id})}/></label>
      <label>Select Status<select className={inputClass} value={draft.status} onChange={e=>setDraft({...draft,status:e.target.value})}><option value="">-- Select Status--</option><option value="O">Pending</option><option value="C">Completed</option></select></label>
      <label>From<DatePicker className={inputClass} placeholder="Completed Date From" value={draft.datefrom} onChange={e=>setDraft({...draft,datefrom:e.target.value})}/></label>
      <label>To<DatePicker className={inputClass} placeholder="Completed Date To" value={draft.dateto} onChange={e=>setDraft({...draft,dateto:e.target.value})}/></label>
      <div className="flex gap-2 items-center"><button className="legacy-button" disabled={query.isFetching}>Search</button><button type="button" className="legacy-button" onClick={()=>{setDraft(initial);setFilters(initial);setPage(1);setError('');setNotice('');}}>Reset</button></div>
      <div className="flex gap-3 justify-end items-center"><button type="button" title="Export Excel" aria-label="Export Excel" disabled={exporting} onClick={()=>void download()}><FileSpreadsheet size={28}/>{exporting && <span>Exporting...</span>}</button>{can('production','addproductionorders') && <Link className="legacy-button" href="/dashboard/production/entry/add">+ Add</Link>}</div>
    </form>
    {notice && <p role="status" className="text-green-700 my-3">{notice}</p>}{(error || query.isError) && <p role="alert" className="text-red-700 my-3">{error || errorMessage(query.error)}</p>}
    <div className="production-order-results"><table className="production-order-table"><thead><tr>{['PO NO.','Date Created','Contract Name','Product','Start Date','End Date','Planned Qty','Prepared Qty','Action'].map(h=><th key={h}>{h}</th>)}</tr></thead><tbody>{query.data?.items.map(row=><tr key={row.id}>
      <td><button className="text-cyan-700 underline" onClick={()=>setPo(row.po_id)}>{row.po_id}</button></td><td>{date(row.issuedate)}</td><td>{can('production','viewcontractdetail')?<button className="text-cyan-700 text-left" onClick={()=>setContract(row.contract_id)}>{row.title}({row.workorder})</button>:<>{row.title}({row.workorder})</>}</td><td>{row.item_name}</td><td>{date(row.startdate)}</td><td>{date(row.enddate)}{row.status==='C' && <>/<br/>{date(row.complete_date)}</>}</td><td className="text-right">{qty(row.plannedqty)}</td><td className="text-right">{row.status==='C'?qty(row.plannedqty):'0.00'}</td><td><div className="flex gap-2">{can('production','status') && <button disabled={busy} aria-label={(row.status==='C'?'Open':'Close')+' Production Order '+row.po_id} title={row.status==='C'?'Open Production Order':'Close Production Order'} onClick={()=>void action(row)}><CircleCheck size={20} className={row.status==='C'?'text-red-600':'text-green-600'}/></button>}{can('production','delete') && row.status!=='C' && !Number(row.production_started) && <button disabled={busy} aria-label={'Delete Production Order '+row.po_id} title="Delete Production Order" onClick={()=>void action(row,true)}><Trash2 size={17} className="text-red-700"/></button>}</div></td>
    </tr>)}{!query.data?.items.length && <tr><td colSpan={9} className="text-center">{query.isLoading?'Loading...':'No Record Found'}</td></tr>}</tbody></table></div>
    <ListPagination page={page} limit={query.data?.limit || 50} total={query.data?.total || 0} onPageChange={setPage} busy={query.isFetching || busy}/>
  </div>{po && <ProductionOrderDetails poId={po} onClose={()=>setPo('')}/>}{!!contract && <ContractDetailsModal contractId={contract} onClose={()=>setContract(0)}/>}</main>;
}
