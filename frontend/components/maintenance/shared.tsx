'use client';
import {useId} from 'react';
import {useQuery} from '@tanstack/react-query';
import apiClient from '@/services/apiClient';
import {useAuth} from '@/contexts/AuthContext';
import {LegacyAutocompleteInput} from '@/components/ui/LegacyAutocompleteInput';
export const inputClass='w-full border border-gray-300 rounded px-2 py-2 bg-white text-sm';
export const errorMessage=(e:unknown)=>(e as {response?:{data?:{error?:{message?:string}}};message?:string})?.response?.data?.error?.message || (e as Error)?.message || 'Unable to process request.';
export const cap=(s:unknown)=>String(s ?? '').toLowerCase().replace(/^./u,c=>c.toUpperCase());
export type MaintenanceRow={id:number;datefrom:string;machine_id:string;machine_name:string;breakdown_type:string;total_time:string;assigned_to:string;shift_incharge:string;maintenance_incharge:string;production_head:string;remark:string;maintenance_status:string;assigned_name:string;shift_name:string;incharge_name:string;production_name:string};
type Person={id:number;user_name:string};
export type Metadata={today:string;assigned:Person[];shifts:Person[];incharges:Person[];heads:Person[];actions:{edit:boolean;delete:boolean};schema:{can_assign:boolean;can_complete:boolean;missing_assignment:string[];missing_completion:string[]}};
export function useMetadata(){const {user}=useAuth();return useQuery<Metadata>({queryKey:['maintenance-meta',user?.db,user?.id],queryFn:async({signal})=>(await apiClient.get('/maintenance/metadata',{signal})).data.data});}
export function MachineLookup({value,id,onChange,required=false}:{value:string;id:string;onChange:(name:string,id:string)=>void;required?:boolean}){
  const uid=useId(),{user}=useAuth();
  const query=useQuery<{id:number;name:string}[]>({queryKey:['maintenance-machines',user?.db,value],enabled:!!value && !id,queryFn:async({signal})=>(await apiClient.get('/maintenance/machines',{params:{fetch:value},signal})).data.data});
  return <><LegacyAutocompleteInput className={inputClass} aria-label="Machine Name" value={value} required={required} list={uid} placeholder="Enter Machine Name" onChange={e=>{const name=e.target.value,row=query.data?.find(r=>r.name===name);onChange(name,row?String(row.id):'');}}/><datalist id={uid}>{query.data?.map(r=><option key={r.id} value={r.name}/>)}</datalist>{query.isError && <small role="alert">{errorMessage(query.error)}</small>}{query.isSuccess && value && !id && !query.data?.length && <small>No Record Found</small>}</>;
}
