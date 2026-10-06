'use client';
import { useParams } from 'next/navigation';
import ModulePdfPreview from '@/components/ModulePdfPreview';
export default function JobChallanPdfPage() {
  const {id} = useParams();
  return <ModulePdfPreview path={`/job-challan/${encodeURIComponent(String(id))}/pdf`} />;
}
