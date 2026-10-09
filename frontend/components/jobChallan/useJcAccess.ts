'use client';
import {useQuery} from '@tanstack/react-query';
import {useAuth} from '@/contexts/AuthContext';
import {usePermission} from '@/contexts/PermissionContext';
import apiClient from '@/services/apiClient';
export function useJcAccess() {
  const {user,loading:authLoading}=useAuth();
  const {hasPermission}=usePermission();
  const query=useQuery<{configured:string[]}>({queryKey:['jcActionPermissions',user?.db,user?.id],enabled:!!user && !authLoading,queryFn:async()=>(await apiClient.get('/job-challan/permissions')).data.data});
  return {loading:authLoading || (!!user && query.isPending),permissionError:query.isError && !query.data, retryPermissions:query.refetch,can:(controller:string,action:string)=>{
    if (!user) return false;
    if (Number(user.role_id)===101) return true;
    const key=`legacy:admin/${controller}/${action}`.toLowerCase();
    return !!query.data && (!query.data.configured.includes(key) || hasPermission(key));
  }};
}
export const inputClass='w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 outline-none';
export const buttonClass='px-4 py-2 rounded-md bg-cyan-600 text-white text-sm font-semibold disabled:opacity-50';
export const errorMessage=(error:unknown)=>{
  const e=error as {response?:{data?:{message?:string;error?:{message?:string}}}};
  return e.response?.data?.message || e.response?.data?.error?.message || 'Unable to complete the request';
};
export const localDate=()=>new Intl.DateTimeFormat('en-CA',{timeZone:'Asia/Kolkata',year:'numeric',month:'2-digit',day:'2-digit'}).format(new Date());
