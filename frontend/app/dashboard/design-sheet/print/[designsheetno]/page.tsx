'use client';
import { useParams } from 'next/navigation';
import ModulePdfPreview from '@/components/ModulePdfPreview';

export default function PrintDesignSheetPage() {
  const {designsheetno} = useParams();
  return <ModulePdfPreview path={`/designsheets/view/${encodeURIComponent(String(designsheetno))}/pdf`} />;
}
