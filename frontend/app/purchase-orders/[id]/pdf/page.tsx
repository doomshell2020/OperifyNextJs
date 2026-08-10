"use client";

import React, { useEffect, useState } from 'react';
import { useParams, useSearchParams } from 'next/navigation';
import { PrintPurchaseOrder } from '../../../../components/dashboard/PrintPurchaseOrder';

export default function PurchaseOrderPdfPage() {
  const params = useParams();
  const searchParams = useSearchParams();
  const id = params?.id as string;
  const token = searchParams?.get('token');
  const [isReady, setIsReady] = useState(false);

  useEffect(() => {
    if (token) {
      localStorage.setItem('accessToken', token);
    }
    // Small delay to ensure localStorage is ready before components mount
    setIsReady(true);
    
    const timer = setTimeout(() => {
      // Only call window.print() if we are NOT running inside Puppeteer headless mode
      // Puppeteer does not need window.print() to generate the PDF buffer
      if (!token) {
        window.print();
      }
    }, 1000);
    return () => clearTimeout(timer);
  }, [token]);

  if (!isReady) return null;

  return (
    <div className="min-h-screen bg-slate-100">
      <PrintPurchaseOrder poId={Number(id)} onClose={() => window.close()} />
    </div>
  );
}
