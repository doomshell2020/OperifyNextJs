'use client';
import { formatQty } from '@/utils/formatters';
import {useParams} from 'next/navigation';
import {useQuery} from '@tanstack/react-query';
import Link from 'next/link';
import apiClient from '@/services/apiClient';
import {useAuth} from '@/contexts/AuthContext';
import {LegacyPageHeader} from '@/components/ui/LegacyPageHeader';
import {formatContractDate} from '@/utils/dateFormatter';
import {useJcAccess,errorMessage} from '@/components/jobChallan/useJcAccess';
interface Item {id:number;item_id:number;item_name:string;description:string;quantity:number;unit:string;remarks:string;}
export default function Page(){
  const {id}=useParams(),{user}=useAuth(),{can,loading}=useJcAccess();
  const query=useQuery({queryKey:['gatepassDetail',user?.db,id],enabled:can('gatepasses','view'),queryFn:async()=>(await apiClient.get('/gatepass/'+id)).data.data});
  if(loading || query.isLoading)return <p className="p-6">Loading...</p>;
  if(!can('gatepasses','view'))return <p className="p-6">Access denied.</p>;
  if(query.isError)return <p role="alert">{errorMessage(query.error)}</p>;
  const g=query.data?.gatepass;if(!g)return null;
  const type=g.gatepass_type || 'JC Based';
  const singleJc=String(g.jc_id || '').split(',').filter(Boolean).length===1 && g.jcs.length===1?g.jcs[0].challan_no:'';
  const jcRef=type==='Miscellaneous'?'Miscellaneous':g.jcs.map((jc:{challan_no:string})=>jc.challan_no).join(', ') || g.jc_id;
  return <main className="legacy-gatepass space-y-5">
    <LegacyPageHeader title="Gate Pass Details" breadcrumb="Gate Passes / View"/>
    <section className="bg-white border rounded">
      <div className="legacy-list-heading"><h2>{g.gatepass_no}</h2><div className="flex gap-2">
        {can('gatepasses','gatepasspdf') && <Link className="gatepass-print-button" target="_blank" rel="noreferrer" href={`/dashboard/gatepass/${id}/pdf`}>Print PDF</Link>}
        {can('gatepasses','edit') && <Link className="legacy-button" href={`/dashboard/gatepass/${id}/edit`}>Edit</Link>}
      </div></div>
      <div className="p-3 overflow-x-auto"><table className="w-full gatepass-details"><tbody>
        <tr><th>Gate Pass No</th><td>{g.gatepass_no}</td><th>Date</th><td>{formatContractDate(g.date)}</td></tr>
        <tr><th>To Company</th><td>{g.vendor_name || g.company_name}</td><th>Vehicle No</th><td>{g.vehicle_no}</td></tr>
        <tr><th>Job Challan Ref</th><td>{jcRef}</td><th>Return Date</th><td>{formatContractDate(g.return_date)}</td></tr>
        <tr><th>Get Pass Type</th><td><span className="legacy-status">{type}</span></td><th>Status</th><td>{g.status}</td></tr>
        <tr><th>Remarks</th><td colSpan={3} className="whitespace-pre-line">{g.remarks}</td></tr>
      </tbody></table>
      <h3 className="mt-5 mb-3">Items</h3><table className="w-full text-sm"><thead><tr>{['S.No.','Item Code','Description of Item','Quantity','Unit','Remarks'].map(h=><th key={h}>{h}</th>)}</tr></thead>
        <tbody>{g.items.map((item:Item,index:number)=>{
          let description=item.description || '';
          if(singleJc && !description.includes('JC No'))description+=(description?'\n':'')+'JC No: '+singleJc;
          return <tr key={item.id}><td>{index+1}</td><td>{item.item_id}</td><td className="whitespace-pre-line"><strong>{item.item_name}</strong>{description && <p>{description}</p>}</td><td>{formatQty(item.quantity)}</td><td>{item.unit}</td><td>{item.remarks}</td></tr>;
        })}{!g.items.length && <tr><td colSpan={6} className="text-center">No items found.</td></tr>}</tbody>
      </table></div>
      <div className="gatepass-footer"><Link className="gatepass-default-button" href="/dashboard/gatepass">Back to List</Link></div>
    </section>
  </main>;
}
