'use client';
import {useAuth} from '@/contexts/AuthContext';
import {usePermission} from '@/contexts/PermissionContext';
export function useJcAccess() {
  const {user,loading:authLoading}=useAuth();
  const {hasPermission}=usePermission();
  return {loading:authLoading,permissionError:false, retryPermissions:async()=>{},can:(controller:string,action:string)=>{
    if (!user) return false;
    const key=`legacy:admin/${controller}/${action}`.toLowerCase();
    return hasPermission(key);
  }};
}
export const inputClass='w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 outline-none';
export const buttonClass='px-4 py-2 rounded-md bg-cyan-600 text-white text-sm font-semibold disabled:opacity-50';
export const errorMessage=(error:unknown)=>{
  const e=error as {response?:{data?:{message?:string;error?:{message?:string}}}};
  return e.response?.data?.message || e.response?.data?.error?.message || 'Unable to complete the request';
};
export const localDate=()=>new Intl.DateTimeFormat('en-CA',{timeZone:'Asia/Kolkata',year:'numeric',month:'2-digit',day:'2-digit'}).format(new Date());
