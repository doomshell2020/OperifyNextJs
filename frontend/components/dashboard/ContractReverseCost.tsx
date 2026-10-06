'use client';
import {useState} from 'react';
import {useQuery} from '@tanstack/react-query';
import apiClient from '@/services/apiClient';
import {GrnDetailsModal} from '@/components/GrnDetailsModal';
import {formatContractDate} from '@/utils/dateFormatter';
import {formatAmt,formatQty} from '@/utils/formatters';

export function ContractReverseCost({contractId,onClose}:{contractId:number;onClose:()=>void}) {
  const [grn,setGrn]=useState<number|null>(null);
  const {data,isLoading,isError}=useQuery({queryKey:['contract-reverse-cost',contractId],queryFn:async()=>(await apiClient.get(`/contracts/${contractId}/reverse-cost`)).data.data});
  return <div className="fixed inset-0 z-[10001] bg-slate-900/40 flex items-center justify-center p-4"><div className="bg-white rounded-xl p-6 max-w-4xl w-full max-h-[90vh] overflow-auto">
    <div className="flex justify-between mb-4"><h2 className="font-bold">Contract Reverse Expenditure</h2><button onClick={onClose}>Close</button></div>
    {isLoading ? <p>Loading reverse expenditure...</p> : isError ? <p role="alert">Unable to load reverse expenditure.</p> : <>
      <p className="mb-4">{data?.contract.title} · Work Order {data?.contract.workorder}</p>
      <table className="w-full border-collapse text-sm"><thead><tr>{['Reverse Id','Date','Product','Quantity','Reference GRN','GRN Date','Rate','Cost'].map(label=><th className="border p-2" key={label}>{label}</th>)}</tr></thead>
        <tbody>{data?.items.map((row:any)=><tr key={row.id}>
          <td className="border p-2">{row.reverse_id}</td><td className="border p-2">{formatContractDate(row.created)}</td><td className="border p-2">{row.item_name}</td>
          <td className="border p-2 text-right">{formatQty(row.quantity)}</td><td className="border p-2">{row.goods_id ? <button className="text-cyan-700 underline" onClick={()=>setGrn(Number(row.goods_id))}>{row.goods_id}</button> : ''}</td>
          <td className="border p-2">{formatContractDate(row.grn_date)}</td><td className="border p-2 text-right">{formatAmt(row.rate)}</td><td className="border p-2 text-right">{formatAmt(row.cost)}</td>
        </tr>)}<tr><th className="border p-2 text-right" colSpan={7}>Total</th><td className="border p-2 text-right">{formatAmt(data?.total)}</td></tr></tbody>
      </table>
    </>}
    {grn && <GrnDetailsModal id={grn} isOpen onClose={()=>setGrn(null)}/>}
  </div></div>;
}
