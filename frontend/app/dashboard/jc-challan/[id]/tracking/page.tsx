'use client';
import Link from 'next/link';
import { useState } from 'react';
import ReturnForm from '@/components/jobChallan/ReturnForm';
import { useParams, useSearchParams } from 'next/navigation';
import { useQuery } from '@tanstack/react-query';
import { useAuth } from '@/contexts/AuthContext';
import { LegacyPageHeader } from '@/components/ui/LegacyPageHeader';
import { useJcAccess, errorMessage } from '@/components/jobChallan/useJcAccess';
import { formatContractDate } from '@/utils/dateFormatter';
import apiClient from '@/services/apiClient';

export default function JcTracking() {
  const { id } = useParams<{ id: string }>();
  const [returnOpen,setReturnOpen] = useState(false);
  const search = useSearchParams();
  const { user } = useAuth();
  const { can, loading } = useJcAccess();
  const allowed = can('jobchallan','index') || can('jobchallan','receiveindex') || can('jobchallan','jcinfo');
  const senderDb = search.get('sender_db');
  const query = useQuery({ queryKey: ['jcTracking',user?.db,id,senderDb],enabled: allowed,
    queryFn: async () => (await apiClient.get(`/job-challan/${id}/tracking`,{params:{sender_db:senderDb || undefined}})).data.data });
  if (loading) return <p>Loading permissions...</p>;
  if (!allowed) return <p role="alert">You do not have permission to view JC tracking.</p>;
  if (query.isLoading) return <p>Loading tracking...</p>;
  if (query.isError) return <p role="alert">{errorMessage(query.error)}</p>;
  if (!query.data) return null;
  const { challan: jc, history, tracking, returnedItems } = query.data;
  const grouped = new Map<number, { name: string; dispatched: number; received: number; pending: number }>();
  for (const item of jc.job_challan_items) {
    const key = Number(item.item_id), previous = grouped.get(key);
    grouped.set(key,{name:item.item?.item_name || item.item_name,dispatched:(previous?.dispatched || 0)+Number(item.quantity),received:Number(item.received_qty),pending:Number(item.pending_qty)});
  }
  return <main className="legacy-jc-page space-y-5"><LegacyPageHeader title="Job Challan Details & Tracking" />
    <section className="legacy-form"><h2 className="legacy-box-heading">Job Challan: {jc.challan_no}{jc.vehicle_no && ` | Dispatch Vehicle No: ${jc.vehicle_no}`}</h2>
      <h3 className="jc-section-heading">Item Receipts History</h3>
      <div className="overflow-x-auto"><table className="w-full"><thead className="jc-blue"><tr>{['#','Item Name','Receive Date','Received Qty','Vehicle No','Action'].map(h=><th className="p-2" key={h}>{h}</th>)}</tr></thead>
        <tbody>{Array.from(grouped).map(([itemId,item])=> <JcHistory key={itemId} id={itemId} name={item.name} dispatched={item.dispatched} history={history} canPrint={can('jobchallan','viewreturnpdf')} />)}</tbody>
      </table></div>
      <h3 className="jc-section-heading">Production & Return Tracking</h3>
      <div className="overflow-x-auto"><table className="w-full"><thead className="jc-green"><tr>{['Raw Material Item','Dispatched','Received','Consumed (Indents)','RM Balance'].map(h=><th className="p-2" key={h}>{h}</th>)}</tr></thead>
        <tbody>{Array.from(grouped).map(([itemId,item])=><tr key={itemId}><td className="p-2">{item.name}</td><td className="p-2">{item.dispatched}</td><td className="p-2">{item.received}</td><td className="p-2">{tracking[itemId]?.consumed || 0}</td><td className="p-2">{tracking[itemId]?.balance || 0}</td></tr>)}</tbody>
      </table></div>
      <h3 className="jc-section-heading">Returned Items (Finished Goods / Unused RM)</h3>
      <table className="w-full"><thead className="jc-red"><tr>{['Returned Item','Return Type','Production Output (If FG)','Returned Qty'].map(h=><th className="p-2" key={h}>{h}</th>)}</tr></thead>
        <tbody>{Object.entries(returnedItems || {}).map(([name,value]: [string, any])=><tr key={name}><td className="p-2">{name}</td><td className="p-2">{value.type}</td><td className="p-2">{value.output}</td><td className="p-2">{value.quantity}</td></tr>)}
          {!Object.keys(returnedItems || {}).length && <tr><td colSpan={4} className="p-4 text-center">No returns initiated yet</td></tr>}</tbody>
      </table>
      <div className="flex gap-3 mt-5"><Link className="legacy-button" href="/dashboard/jc-challan">Back</Link>
        {can('jobchallan','itemreceived') && !['Completed','Cancelled','Deleted'].includes(jc.status) && <button type="button" className="legacy-button" onClick={()=>setReturnOpen(true)}>Receive Returned Items</button>}
      </div>
    </section>
    {returnOpen && <div className="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">
      <div role="dialog" aria-modal="true" aria-label="Received Item Job Challan Entry" className="jc-return-dialog bg-white w-full max-w-[999px] max-h-[90vh] overflow-auto p-4">
        <ReturnForm id={id} onClose={()=>setReturnOpen(false)} />
      </div>
    </div>}
  </main>;
}

function JcHistory({id,name,dispatched,history,canPrint}: {id:number;name:string;dispatched:number;history:any[];canPrint:boolean}) {
  const receipts = history.filter(row=>Number(row.item_id)===id);
  return <><tr><td colSpan={6} className="p-2"><b>{name} (Dispatch: {dispatched})</b></td></tr>
    {receipts.map((row,index)=><tr key={row.id}><td className="p-2">{index+1}</td><td className="p-2">{row.item_name}</td><td className="p-2">{formatContractDate(row.receive_date)}</td><td className="p-2">{row.received_qty}</td><td className="p-2">{row.vehicle_no}</td><td className="p-2">{canPrint && <Link className="legacy-button" href={`/dashboard/jc-receive/${row.id}/pdf`} target="_blank" rel="noopener noreferrer">Print Return Challan</Link>}</td></tr>)}
    {!receipts.length && <tr><td colSpan={6} className="p-4 text-center">No Receive Data for this item</td></tr>}</>;
}
