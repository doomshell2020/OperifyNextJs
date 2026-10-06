import GatepassForm from '@/components/jobChallan/GatepassForm';
export default async function Page({params}:{params:Promise<{id:string}>}){const {id}=await params;return <GatepassForm id={id}/>;}
