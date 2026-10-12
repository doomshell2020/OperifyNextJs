import {Suspense} from 'react';
import ReturnForm from '@/components/jobChallan/ReturnForm';
export default async function Page({params}:{params:Promise<{id:string}>}){const {id}=await params;return <Suspense fallback={<p>Loading...</p>}><ReturnForm id={id}/></Suspense>;}
