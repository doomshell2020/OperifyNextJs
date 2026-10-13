'use client';
import {useParams} from 'next/navigation';
import ModulePdfPreview from '@/components/ModulePdfPreview';
import {useJcAccess} from '@/components/jobChallan/useJcAccess';
export default function Page(){
  const {id}=useParams(),{can,loading}=useJcAccess();
  if(loading)return <p>Loading permissions...</p>;
  if(!can('gatepasses','gatepasspdf'))return <p role="alert">You do not have permission to print this Gate Pass.</p>;
  return <ModulePdfPreview path={`/gatepass/${id}/pdf`}/>;
}
