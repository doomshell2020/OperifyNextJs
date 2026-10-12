'use client';
import Link from 'next/link';
import { useParams, useSearchParams } from 'next/navigation';
import { useQuery } from '@tanstack/react-query';
import { useAuth } from '@/contexts/AuthContext';
import { LegacyPageHeader } from '@/components/ui/LegacyPageHeader';
import { useJcAccess, errorMessage } from '@/components/jobChallan/useJcAccess';
import { formatContractDate } from '@/utils/dateFormatter';
import apiClient from '@/services/apiClient';

export default function ViewJobChallan() {
  const { id } = useParams<{ id: string }>();
  const search = useSearchParams();
  const { user } = useAuth();
  const { can, loading } = useJcAccess();
  const senderDb = search.get('sender_db');
  const query = useQuery({ queryKey: ['jobChallan', user?.db, id, senderDb], enabled: can('jobchallan', 'view'),
    queryFn: async () => (await apiClient.get(`/job-challan/${id}`, { params: { sender_db: senderDb || undefined } })).data.data });
  if (loading) return <p>Loading permissions...</p>;
  if (!can('jobchallan', 'view')) return <p role="alert">You do not have permission to view this JC.</p>;
  if (query.isLoading) return <p>Loading challan...</p>;
  if (query.isError) return <p role="alert">{errorMessage(query.error)}</p>;
  const jc = query.data?.challan;
  if (!jc) return null;
  const money = (value: unknown) => Number(value || 0).toFixed(2);
  return <main className="legacy-jc-page space-y-5"><LegacyPageHeader title="Job Challan Details" />
    <section className="legacy-form"><h2 className="legacy-box-heading">Challan Info</h2>
      <div className="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
        <div><b>Challan No</b><p>{jc.challan_no}</p></div><div><b>Date</b><p>{formatContractDate(jc.jc_date)}</p></div>
        <div><b>Vendor</b><p>{jc.vendor?.name}</p></div><div><b>Expected Days</b><p>{jc.expected_days} Days</p></div>
        <div><b>Status</b><p><span className="legacy-status">{jc.status}</span></p></div>
        <div className="md:col-span-3"><b>Work Description</b><p>{jc.work_description}</p></div>
      </div>
      <div className="overflow-x-auto"><table className="w-full"><thead className="jc-blue"><tr>
        {['#','Item Name','Quantity','Rate','Tax','Tax Amount','Total Amount'].map(h => <th key={h} className="p-2">{h}</th>)}
      </tr></thead><tbody>{jc.job_challan_items.map((item: any, index: number) => <tr key={item.id}>
        <td className="p-2">{index + 1}</td><td className="p-2">{item.item?.item_name || item.item_name}</td>
        <td className="p-2">{money(item.quantity)}</td><td className="p-2">{money(item.rate)}</td><td className="p-2">{item.tax_rate}%</td>
        <td className="p-2">{money(item.tax_amount)}</td><td className="p-2">{money(item.total_amount ?? item.total)}</td>
      </tr>)}</tbody><tfoot><tr><th colSpan={6} className="p-2 text-right">Total</th>
        <th className="p-2">{money(jc.job_challan_items.reduce((sum: number, item: any) => sum + Number(item.total_amount ?? item.total ?? 0), 0))}</th>
      </tr></tfoot></table></div>
      <Link href="/dashboard/jc-challan" className="legacy-button mt-5">Back</Link>
    </section>
  </main>;
}
