'use client';
import {useEffect,useRef} from 'react';
import {useQuery} from '@tanstack/react-query';
import {useAuth} from '@/contexts/AuthContext';
import {LegacyAutocompleteInput} from '@/components/ui/LegacyAutocompleteInput';
import apiClient from '@/services/apiClient';
export const inputClass='w-full border px-2 py-1 text-sm bg-white';
export type Order={id:number;po_id:string;issuedate:string;contract_id:number;item_id:number;title:string;workorder:string;item_name:string;startdate:string;enddate:string;complete_date:string;plannedqty:string;prepared_qty:number;production_started:number;status:'O'|'C';totaldays:string};
export type Detail={order:Order;quantity:number;prepared_qty:number;pending_qty:number;processes:{id:number;process_name:string;startdate:string;enddate:string;prepared_qty:number}[];materials:{id:number;item_name:string;required_qty:number;available_qty:number}[]};
export const qty=(value:unknown)=>Number(value || 0).toFixed(2);
export function errorMessage(error:unknown):string {
  const e=error as {response?:{data?:{error?:{message?:string};message?:string}};message?:string};
  return e?.response?.data?.error?.message || e?.response?.data?.message || e?.message || 'An error occurred. Please try again.';
}
export function ContractInput({value,onChange,required=false,selectedId=''}:{value:string;selectedId?:string;onChange:(name:string,id:string)=>void;required?:boolean}) {
  const {user}=useAuth();
  const contracts=useQuery<{id:number;title:string;workorder:string}[]>({queryKey:['production-order-contracts',user?.db,value],enabled:!!value && !selectedId,queryFn:async({signal})=>(await apiClient.get('/production-orders/contracts',{params:{fetch:value},signal})).data.data});
  return <><LegacyAutocompleteInput aria-label="Contract Name" required={required} className={inputClass} list="production-order-contracts" placeholder="Enter Contract Name" value={value} onChange={e=>{const name=e.target.value;const row=contracts.data?.find(c=>c.title+'('+c.workorder+')'===name);onChange(name,row?String(row.id):'');}}/><datalist id="production-order-contracts">{contracts.data?.map(c=><option key={c.id} value={c.title+'('+c.workorder+')'}/>)}</datalist>{contracts.isError && <p role="alert">{errorMessage(contracts.error)}</p>}{value && !selectedId && contracts.isSuccess && !contracts.data?.length && <small>No Record Found</small>}</>;
}
export function Dialog({title,onClose,children}:{title:string;onClose:()=>void;children:React.ReactNode}) {
  const ref=useRef<HTMLDialogElement>(null);
  useEffect(()=>{const dialog=ref.current;dialog?.showModal();return ()=>dialog?.close();},[]);
  return <dialog ref={ref} onCancel={e=>{e.preventDefault();onClose();}} className="production-order-dialog" aria-label={title} onClick={e=>{if(e.target===e.currentTarget)onClose();}}><div className="flex items-center justify-between mb-4"><h2 className="font-bold">{title}</h2><button aria-label="Close details" onClick={onClose} className="px-3 py-1">×</button></div>{children}</dialog>;
}
