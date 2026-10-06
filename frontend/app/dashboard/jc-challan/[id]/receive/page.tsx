import {Suspense} from 'react';
import ReceiveForm from '@/components/jobChallan/ReceiveForm';
export default async function Page({params}:{params:Promise<{id:string}>}){const {id}=await params;return <Suspense fallback={<p>Loading...</p>}><ReceiveForm returnId={id}/></Suspense>;}
