'use client';
import {useId} from 'react';
import {useQuery} from '@tanstack/react-query';
import {useAuth} from '@/contexts/AuthContext';
import apiClient from '@/services/apiClient';
import {LegacyAutocompleteInput} from '@/components/ui/LegacyAutocompleteInput';
import {inputClass,errorMessage} from '@/components/productionOrder/shared';
export {inputClass,errorMessage};
export type DailyRow={id:number;production_date:string;machine_id:string;contract_id:string;item_id:string;po_id:string;productprocess_id:string;machine_name:string;title:string;workorder:string;item_name:string;process_name:string;plan_qty:string;production_shift_a:string;production_shift_b:string;reading8am:string;reading8pm:string;nextday8am:string;manpower_day:string;manpower_night:string;scrap:string;remark:string;total_production:number;total_manpower:number;reading_locked?:boolean};
export type Meta={today:string;has_completed:boolean;processes:{id:number;process_name:string}[];machines:{id:number;machine_name:string;status:string}[]};
export type Entry=Record<string,string>;
export const blank=():Entry=>({production_date:'',contract_id:'',contractname:'',item_id:'',po_id:'',productprocess_id:'',machine_id:'',machinename:'',plan_qty:'',production_shift_a:'',production_shift_b:'',reading8am:'',reading8pm:'',nextday8am:'',manpower_day:'',manpower_night:'',scrap:'',remark:'',completed:'N'});
export function Lookup({kind,value,id,onChange,required=false}:{kind:'contracts'|'products'|'machines';value:string;id:string;onChange:(name:string,id:string)=>void;required?:boolean}) {
  const uid=useId(),{user}=useAuth();
  const query=useQuery<{id:number;name:string}[]>({queryKey:['daily-lookup',user?.db,kind,value],enabled:!!value && !id,queryFn:async({signal})=>(await apiClient.get('/daily-sheets/lookup',{params:{kind,fetch:value},signal})).data.data});
  return <><LegacyAutocompleteInput aria-label={kind==='contracts'?'Contract Name':kind==='products'?'Product Name':'Machine Name'} className={inputClass} required={required} value={value} list={uid} placeholder={'Enter '+(kind==='contracts'?'Contract Name':kind==='products'?'Product Name':'Machine Name')} onChange={e=>{const name=e.target.value,row=query.data?.find(r=>r.name===name);onChange(name,row?String(row.id):'');}}/><datalist id={uid}>{query.data?.map(r=><option key={r.id} value={r.name}/>)}</datalist>{query.isError && <small role="alert">{errorMessage(query.error)}</small>}{query.isSuccess && !id && value && !query.data?.length && <small>No Record Found</small>}</>;
}
export function useMetadata() {const {user}=useAuth();return useQuery<Meta>({queryKey:['daily-metadata',user?.db],queryFn:async()=>(await apiClient.get('/daily-sheets/metadata')).data.data});}
export type Dependencies={products:{id:number;item_name:string}[];orders:{po_id:number}[];processes:{id:number;process_name:string}[];reading:string|null;progress:{productprocess_id:string;quantity:number}[];plannedqty:string|null};
export function useDependencies(row:Entry) {
  const {user}=useAuth();
  return useQuery<Dependencies>({
    queryKey:['daily-dependencies',user?.db,row.contract_id,row.item_id,row.po_id,row.machine_id],
    enabled:!!(row.contract_id || row.item_id || row.po_id || row.machine_id),
    // Changing PO or machine also refreshes these lookups. Retain options for
    // the same contract/item so controlled selects do not temporarily go blank.
    // Never retain options across a company or contract change.
    placeholderData:(previous,previousQuery)=>{
      const key=previousQuery?.queryKey;
      if(!previous || key?.[1]!==user?.db || key?.[2]!==row.contract_id)return undefined;
      const sameItem=key?.[3]===row.item_id,sameOrder=sameItem && key?.[4]===row.po_id;
      return {...previous,orders:sameItem?previous.orders:[],processes:sameItem?previous.processes:[],progress:sameOrder?previous.progress:[],plannedqty:sameOrder?previous.plannedqty:null,reading:key?.[5]===row.machine_id?previous.reading:null};
    },
    queryFn:async({signal})=>(await apiClient.get('/daily-sheets/dependencies',{params:{contract_id:row.contract_id,item_id:row.item_id,po_id:row.po_id,machine_id:row.machine_id},signal})).data.data,
  });
}
export const numericFields=[['plan_qty','Planned Quantity'],['reading8am','Reading(Current day)08.00AM'],['reading8pm','Reading(Current day)08.00PM'],['nextday8am','Reading(Next day)08.00AM'],['production_shift_a','Production (Shift A)'],['production_shift_b','Production (Shift B)'],['manpower_day','Manpower in Day'],['manpower_night','Manpower in Night']] as const;
