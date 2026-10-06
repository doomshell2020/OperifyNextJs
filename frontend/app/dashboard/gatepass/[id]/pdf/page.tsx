'use client';
import {useParams} from 'next/navigation';
import ModulePdfPreview from '@/components/ModulePdfPreview';
export default function Page(){const {id}=useParams();return <ModulePdfPreview path={`/gatepass/${id}/pdf`}/>;}
