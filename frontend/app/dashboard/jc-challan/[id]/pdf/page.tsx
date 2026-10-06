'use client';
import { useParams, useSearchParams } from 'next/navigation';
import { Suspense } from 'react';
import ModulePdfPreview from '@/components/ModulePdfPreview';
function Preview() {
  const {id} = useParams();
  const search = useSearchParams();
  return <ModulePdfPreview path={`/job-challan/${encodeURIComponent(String(id))}/pdf${search.get('sender_db') ? '?sender_db=' + encodeURIComponent(search.get('sender_db')!) : ''}`} />;
}
export default function JobChallanPdfPage() { return <Suspense fallback={<p>Loading...</p>}><Preview /></Suspense>; }
