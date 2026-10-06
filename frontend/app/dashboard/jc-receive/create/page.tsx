import {Suspense} from 'react';
import ReceiveForm from '@/components/jobChallan/ReceiveForm';
export default function Page(){return <Suspense fallback={<p>Loading...</p>}><ReceiveForm/></Suspense>;}
