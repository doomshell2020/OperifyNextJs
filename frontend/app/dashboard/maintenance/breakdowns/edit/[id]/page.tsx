import MaintenanceForm from '@/components/maintenance/MaintenanceForm';
export default async function Page({params}:{params:Promise<{id:string}>}){const {id}=await params;return <MaintenanceForm id={id}/>;}
