'use client';
import {useQuery} from '@tanstack/react-query';
import Link from 'next/link';
import {useAuth} from '@/contexts/AuthContext';
import apiClient from '@/services/apiClient';
import {format} from 'date-fns';
import {formatContractDate as date} from '@/utils/dateFormatter';
import {openModulePdf} from '@/services/pdf.service';
import {Detail,Dialog,errorMessage,qty} from './shared';
const headingDate=(value:string)=>date(value)?format(new Date(value),'dd-MMM-yyyy'):'';
export default function ProductionOrderDetails({poId,onClose,apiBase='/production-orders',dailyLinks=false}:{poId:string;onClose:()=>void;apiBase?:string;dailyLinks?:boolean}) {
  const {user}=useAuth();
  const query=useQuery<Detail>({queryKey:['production-order-details',user?.db,poId,apiBase],queryFn:async()=>(await apiClient.get(apiBase+'/'+poId+'/details')).data.data});
  const data=query.data,o=data?.order;
  return <Dialog title="Production Order Details" onClose={onClose}>{query.isLoading?<p>Loading...</p>:query.isError?<p role="alert">{errorMessage(query.error)}</p>:data && o && <>
    <div className="flex justify-end mb-3"><button className="legacy-button" onClick={()=>void openModulePdf(apiBase+'/'+poId+'/pdf')}>Print</button></div>
    <div className="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4"><p><b>Production Order No.:-</b> {o.po_id}</p><p><b>Issue Date:-</b> {headingDate(o.issuedate)}</p><p><b>Product:-</b> {o.item_name}</p><p><b>Contract Name:-</b> {o.title}({o.workorder})</p><p><b>Quantity:-</b> {data.quantity} KM</p><p><b>Start Date:-</b> {headingDate(o.startdate)}</p><p><b>End Date:-</b> {headingDate(o.enddate)}</p></div>
    <div className="grid sm:grid-cols-3 gap-3 border p-3"><p><b>Planned Qty:-</b> {qty(o.plannedqty)} KM</p><p><b>Prepared Qty:-</b> {qty(data.prepared_qty)} KM</p><p><b>Pending Qty:-</b> {qty(data.pending_qty)} KM</p></div>
    <h3 className="text-center font-bold my-4">Process Details</h3><table className="production-order-table"><thead><tr>{['S.No.','Process Name','Start Date','End Date','Prepared Qty(KM)'].map(h=><th key={h}>{h}</th>)}</tr></thead><tbody>{data.processes.map((p,i)=><tr key={p.id}><td>{i+1}.</td><td>{dailyLinks?<Link href={'/dashboard/production/sheet?po_id='+poId+'&productprocess_id='+p.id}>{p.process_name}</Link>:p.process_name}</td><td>{date(p.startdate)}</td><td>{date(p.enddate)}</td><td className="text-right">{qty(p.prepared_qty)}</td></tr>)}{!data.processes.length && <tr><td colSpan={5}>Production Not Started Yet.</td></tr>}</tbody></table>
    <h3 className="text-center font-bold my-4">Raw Material</h3><table className="production-order-table"><thead><tr>{['S.No.','Item Name','Required Qty','Available Qty'].map(h=><th key={h}>{h}</th>)}</tr></thead><tbody>{data.materials.map((m,i)=><tr key={m.id}><td>{i+1}.</td><td>{m.item_name}</td><td className="text-right">{qty(m.required_qty)}</td><td className="text-right">{qty(m.available_qty)}</td></tr>)}{!data.materials.length && <tr><td colSpan={4}>No Record Found</td></tr>}</tbody></table>
  </>}</Dialog>;
}
