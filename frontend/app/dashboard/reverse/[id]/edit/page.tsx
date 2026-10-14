"use client";
import { useParams } from 'next/navigation';
import { ReverseIndentForm } from '@/components/dashboard/ReverseIndentForm';
export default function EditReversePage() {
  const { id } = useParams<{ id: string }>();
  return <ReverseIndentForm reverseId={id} />;
}
