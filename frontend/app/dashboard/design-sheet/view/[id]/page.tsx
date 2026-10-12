'use client';

import {useParams} from 'next/navigation';
import {DesignSheetDetails} from '@/components/DesignSheetDetails';

export default function DesignSheetDetailsPage() {
  const {id} = useParams<{id: string}>();
  return <DesignSheetDetails id={id} />;
}
