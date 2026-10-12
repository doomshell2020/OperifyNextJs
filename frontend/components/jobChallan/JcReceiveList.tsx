'use client';
import { useState } from 'react';
import Link from 'next/link';
import { useQuery } from '@tanstack/react-query';
import { useAuth } from '@/contexts/AuthContext';
import { LegacyPageHeader } from '@/components/ui/LegacyPageHeader';
import { ListPagination } from '@/components/ui/ListPagination';
import { useListLocation } from '@/components/ui/useListLocation';
import { DatePicker } from '@/components/ui/DatePicker';
import { formatContractDate } from '@/utils/dateFormatter';
import { useJcAccess, errorMessage, inputClass } from './useJcAccess';
import apiClient from '@/services/apiClient';

export default function JcReceiveList() {
  const { user } = useAuth(), { can, loading } = useJcAccess();
  const [page,setPage] = useState(1);
  const [draft,setDraft] = useState({search:'',from_date:'',to_date:''}), [filters,setFilters] = useState(draft);
  const ready = useListLocation(filters,page,['search','from_date','to_date'],(next,nextPage)=>{setDraft(next);setFilters(next);setPage(nextPage);});
  const query = useQuery({ queryKey:['jc-receive',user?.db,page,filters],enabled:ready && can('jobchallan','receiveindex'),
    queryFn: async () => (await apiClient.get('/jc-receive',{params:{...filters,page,limit:50}})).data.data });
  if (loading) return <p>Loading permissions...</p>;
  if (!can('jobchallan','receiveindex')) return <p role="alert">You do not have permission to view JC Receive.</p>;
  return <main className="legacy-jc-page space-y-5"><LegacyPageHeader title="JC Receive List" />
    <section className="bg-white border"><div className="legacy-list-heading"><h2>Job Challan Receives</h2>
      {can('jobchallan','receiveadd') && <Link className="legacy-button" href="/dashboard/jc-receive/create">+ Add New Receive</Link>}</div>
      <form className="legacy-filter-row" onSubmit={event=>{event.preventDefault();setFilters({...draft});setPage(1);}}>
        <label>Search<input className={inputClass} value={draft.search} onChange={e=>setDraft({...draft,search:e.target.value})} /></label>
        <label>From Date<DatePicker className={inputClass} value={draft.from_date} onChange={e=>setDraft({...draft,from_date:e.target.value})} /></label>
        <label>To Date<DatePicker className={inputClass} value={draft.to_date} onChange={e=>setDraft({...draft,to_date:e.target.value})} /></label>
        <button className="legacy-button">Search</button><button type="button" className="legacy-button" onClick={()=>{const empty={search:'',from_date:'',to_date:''};setDraft(empty);setFilters(empty);setPage(1);}}>Reset</button>
      </form>
      {query.isError && <p role="alert" className="p-4 text-red-600">{errorMessage(query.error)}</p>}
      <div className="overflow-x-auto p-3"><table className="w-full text-left"><thead><tr>{['#','Challan No','Ref JC No','From Company','Vehicle No','JC Date','Item Name','Received Qty','Action'].map(h=><th className="p-2" key={h}>{h}</th>)}</tr></thead>
        <tbody>{query.data?.items.map((row:any,index:number)=><tr key={`${row.is_manual?'manual':'jc'}:${row.receive_id || row.challan_id}:${row.item_id}`}>
          <td className="p-2">{(page-1)*50+index+1}</td><td className="p-2">{row.is_manual?row.challan_no:<Link className="text-cyan-700" href={`/dashboard/jc-challan/${row.challan_id}/tracking`}>{row.challan_no}</Link>}</td>
          <td className="p-2">{row.reference_jc_no || ''}</td><td className="p-2">{row.sender_name}</td><td className="p-2">{row.vehicle_no}</td><td className="p-2">{formatContractDate(row.jc_date)}</td><td className="p-2">{row.item_name}</td><td className="p-2">{row.total_received}</td>
          <td className="p-2 space-x-2">{row.is_manual || Number(row.pending_qty)<=0 ? <span className="legacy-status">Completed</span> : can('jobchallan','receiveadd') && <Link className="legacy-button" href={`/dashboard/jc-receive/create?challan_id=${row.challan_id}`}>Receive</Link>}
            {!row.is_manual && can('jobchallan','viewpdf') && <Link className="text-red-700" href={`/dashboard/jc-challan/${row.challan_id}/pdf`} title="Download PDF" target="_blank" rel="noopener noreferrer">PDF</Link>}
          </td>
        </tr>)}{!query.data?.items.length && <tr><td colSpan={9} className="p-6 text-center">{query.isLoading?'Loading...':'No JCs available for receive.'}</td></tr>}</tbody>
      </table><ListPagination page={page} limit={50} total={query.data?.total || 0} onPageChange={setPage} busy={query.isFetching} /></div>
    </section>
  </main>;
}
