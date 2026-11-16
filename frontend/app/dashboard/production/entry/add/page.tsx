'use client';
import {useState} from 'react';
import Link from 'next/link';
import {useRouter} from 'next/navigation';
import {useQuery,useQueryClient} from '@tanstack/react-query';
import {format} from 'date-fns';
import {useAuth} from '@/contexts/AuthContext';
import apiClient from '@/services/apiClient';
import {LegacyPageHeader} from '@/components/ui/LegacyPageHeader';
import {DatePicker} from '@/components/ui/DatePicker';
import {ContractInput,Order,inputClass,errorMessage,qty} from '@/components/productionOrder/shared';
import {formatContractDate as date} from '@/utils/dateFormatter';
import '../production-orders.css';
type Product={id:number;product_id:number;item_name:string;quantity:string;pendingqty:number;unit_name:string};
type Line={plannedqty:string;startdate:string;enddate:string};
const empty:Line={plannedqty:'',startdate:'',enddate:''};
const days=(line:Line)=>line.startdate && line.enddate ? (Date.parse(line.enddate)-Date.parse(line.startdate))/86400000+1 : '';
export default function AddProductionOrders(){
  const {user}=useAuth(),router=useRouter(),client=useQueryClient();
  const [contractname,setName]=useState(''),[contract_id,setContract]=useState(''),[productId,setProduct]=useState(''),[issuedate,setDate]=useState(()=>format(new Date(),'yyyy-MM-dd')),[lines,setLines]=useState<Record<string,Line>>({}),[busy,setBusy]=useState(false),[error,setError]=useState('');
  const form=useQuery<{po_id:string;products:Product[];orders:Order[]}>({queryKey:['production-order-form',user?.db,contract_id],queryFn:async()=>(await apiClient.get('/production-orders/form',{params:{contract_id}})).data.data});
  const products=form.data?.products.filter(p=>String(p.product_id)===productId) || [];
  function update(id:number,key:keyof Line,value:string){setLines(old=>({...old,[id]:{...(old[id] || empty),[key]:value}}));}
  async function submit(e:React.FormEvent){e.preventDefault();setError('');if(!contract_id){setError('Your entered Contract does not exist.');return;}const items=products.filter(p=>lines[p.id]?.plannedqty!==undefined && lines[p.id].plannedqty!=='').map(p=>({bom_id:p.id,item_id:p.product_id,...lines[p.id]}));if(!items.length){setError('Please enter a planned quantity for at least one product.');return;}setBusy(true);try{await apiClient.post('/production-orders',{po_id:form.data?.po_id,issuedate,contract_id,items});await client.invalidateQueries({queryKey:['production-orders']});await client.invalidateQueries({queryKey:['production-order-form']});await client.invalidateQueries({queryKey:['contract-details']});router.push('/dashboard/production/entry');}catch(e){setError(errorMessage(e));}finally{setBusy(false);}}
  return <main className="production-orders-create space-y-4"><LegacyPageHeader title="Production Orders"/><form onSubmit={e=>void submit(e)} className="bg-white border p-4"><div className="production-order-create-grid">
    <label>Production Order No. *<input className={inputClass} readOnly required value={form.data?.po_id || ''}/></label>
    <label>Date<DatePicker className={inputClass} required value={issuedate} onChange={e=>setDate(e.target.value)}/></label>
    <label>Contract Name *<ContractInput required selectedId={contract_id} value={contractname} onChange={(name,id)=>{setName(name);setContract(id);setProduct('');setLines({});}}/></label>
    <label>Product *<select className={inputClass} required disabled={!contract_id || form.isFetching} value={productId} onChange={e=>{setProduct(e.target.value);setLines({});}}><option value="">-- Select Product--</option>{Array.from(new Map(form.data?.products.map(p=>[p.product_id,p]) || []).values()).map(p=><option key={p.product_id} value={p.product_id}>{p.item_name}</option>)}</select></label>
  </div>
  {form.isFetching && <p role="status">Loading...</p>}{(error || form.isError) && <p role="alert" className="text-red-700 mb-4">{error || errorMessage(form.error)}</p>}
  {!!products.length && <div className="production-order-results mb-4"><table className="production-order-table production-order-form-table"><thead><tr>{['Product','Order Qty','Pending Qty','Planned Qty','UOM','Start Date','End Date','Total Days'].map(h=><th key={h}>{h}</th>)}</tr></thead><tbody>{products.map(p=>{const line=lines[p.id] || empty;return <tr key={p.id}><td>{p.item_name}</td><td>{qty(p.quantity)}</td><td>{qty(p.pendingqty)}</td><td><input aria-label={'Planned Qty for '+p.item_name} className={inputClass} type="number" min="0" max={p.pendingqty} step="0.01" value={line.plannedqty} onChange={e=>update(p.id,'plannedqty',e.target.value)}/></td><td>{p.unit_name}</td><td><DatePicker aria-label={'Start Date for '+p.item_name} className={inputClass} required={line.plannedqty!==''} value={line.startdate} onChange={e=>update(p.id,'startdate',e.target.value)}/></td><td><DatePicker aria-label={'End Date for '+p.item_name} className={inputClass} required={line.plannedqty!==''} min={line.startdate || undefined} value={line.enddate} onChange={e=>update(p.id,'enddate',e.target.value)}/></td><td><input aria-label={'Total Days for '+p.item_name} className={inputClass} readOnly value={days(line)}/></td></tr>;})}</tbody></table></div>}
  <div className="flex justify-between"><Link className="legacy-button" href="/dashboard/production/entry">Back</Link><button className="legacy-button" disabled={busy || form.isFetching || form.isError}>{busy?'Saving...':'Submit'}</button></div>
  </form>
  {contract_id && !!form.data?.orders.length && <div className="production-order-results bg-white border p-3"><table className="production-order-table"><thead><tr>{['Production Order No.','Issue Date','Product','Planned Qty','Start Date','End Date','Total Days'].map(h=><th key={h}>{h}</th>)}</tr></thead><tbody>{form.data.orders.map(o=><tr key={o.id}><td>{o.po_id}</td><td>{date(o.issuedate)}</td><td>{o.item_name}</td><td>{qty(o.plannedqty)}</td><td>{date(o.startdate)}</td><td>{date(o.enddate)}</td><td>{o.totaldays}</td></tr>)}</tbody></table></div>}
  </main>;
}
