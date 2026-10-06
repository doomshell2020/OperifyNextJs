'use client';
import {useEffect,useState} from 'react';
import {useParams,useRouter} from 'next/navigation';
import Link from 'next/link';
import {indentpoService} from '@/services/indentpo.service';
export default function EditIndentPage(){
 const {id}=useParams<{id:string}>(),router=useRouter();
 const [data,setData]=useState<any>(null),[error,setError]=useState(''),[saving,setSaving]=useState(false);
 const [machineName,setMachineName]=useState(''),[machines,setMachines]=useState<{id:number;machine_name:string}[]>([]);
 useEffect(()=>{indentpoService.getEditData(id).then(value=>{setData({...value,issue_date:String(value.issue_date||'').slice(0,10)});setMachineName(value.machine_name||'');setMachines([{id:value.machine_id,machine_name:value.machine_name}]);}).catch(e=>setError(e.response?.data?.message || 'Unable to load indent'));},[id]);
 async function save(event:React.FormEvent){
  event.preventDefault();const machine=machines.find(row=>row.machine_name===machineName);if(!machine){setError('Select a valid machine');return;}
  setSaving(true);setError('');try{await indentpoService.update(id,{...data,machine_id:machine.id});router.push('/dashboard/purchase/indentpo');}catch(e:any){setError(e.response?.data?.error?.message || e.response?.data?.message || 'Unable to update indent');}finally{setSaving(false);}
 }
 if(!data)return <p role="status">{error || 'Loading indent…'}</p>;
 return <div className="space-y-5"><h1 className="text-2xl font-bold">Edit Indent {id}</h1><p>{data.contract_name} ({data.workorder}) · {data.product_name}</p>
  {error && <p role="alert" className="text-red-600">{error}</p>}
  <form onSubmit={save} className="bg-white border rounded-xl p-5 space-y-5">
   <div className="grid grid-cols-3 gap-4">
    <label>Machine<input required list="indent-machines" value={machineName} onChange={async e=>{const value=e.target.value;setMachineName(value);if(value.trim())setMachines(await indentpoService.searchMachines(value));}} className="block border rounded p-2 w-full"/><datalist id="indent-machines">{machines.map(row=><option key={row.id} value={row.machine_name}/>)}</datalist></label>
    <label>Issued By<input required value={data.issued_name||''} onChange={e=>setData({...data,issued_name:e.target.value})} className="block border rounded p-2 w-full"/></label>
    <label>Issue Date<input required type="date" value={data.issue_date} onChange={e=>setData({...data,issue_date:e.target.value})} className="block border rounded p-2 w-full"/></label>
   </div>
   <table className="w-full text-sm"><thead><tr><th className="text-left p-2">Raw Material</th><th className="text-left p-2">Issued Quantity</th><th className="text-left p-2">UOM</th></tr></thead><tbody>{data.items.map((item:any,index:number)=><tr key={item.id}><td className="p-2">{item.raw_material_name}</td><td className="p-2"><input required type="number" min="0" step="any" value={item.quantity} onChange={e=>setData({...data,items:data.items.map((row:any,i:number)=>i===index?{...row,quantity:e.target.value}:row)})} className="border rounded p-2"/></td><td className="p-2">{item.unit_name}</td></tr>)}</tbody></table>
   <div className="flex gap-4"><button disabled={saving} className="bg-blue-600 text-white rounded px-5 py-2">{saving?'Saving…':'Update Indent'}</button><Link href="/dashboard/purchase/indentpo" className="px-5 py-2">Cancel</Link></div>
  </form>
 </div>;
}
