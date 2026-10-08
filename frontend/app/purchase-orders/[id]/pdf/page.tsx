"use client";
import React, { useEffect, useState } from 'react';
import { useParams, useSearchParams } from 'next/navigation';
import { PrintPurchaseOrder } from '../../../../components/dashboard/PrintPurchaseOrder';
import purchaseOrderService, { PurchaseOrderDetailsData } from '../../../../services/purchaseOrder.service';

export default function PurchaseOrderPdfPage() {
  const params = useParams();
  const searchParams = useSearchParams();
  const id = params?.id as string;
  const token = searchParams?.get('token');
  const requestedMode = searchParams?.get('mode');
  const mode = requestedMode === 'revised' ? 'revised' : requestedMode === 'delivery' ? 'delivery' : 'current';
  const [documents, setDocuments] = useState<PurchaseOrderDetailsData[]>([]);
  const [error, setError] = useState(false);
  useEffect(() => {
    let active = true;
    setDocuments([]); setError(false);
    if (token) localStorage.setItem('accessToken', token);
    purchaseOrderService.getPrintData(id, mode).then(data => {
      if (active) { if (data.length) setDocuments(data); else setError(true); }
    }).catch(() => { if (active) setError(true); });
    return () => { active = false; };
  }, [id, token, mode]);
  useEffect(() => {
    if (token || !documents.length) return;
    let cancelled = false;
    document.fonts.ready.then(() => { if (!cancelled) window.print(); });
    return () => { cancelled = true; };
  }, [token, documents]);
  return <div className="min-h-screen bg-white" data-po-print-ready={documents.length > 0 && !error} data-po-print-error={error}>
    <style>{`.po-revision-document:not(:last-child) { break-after: page; page-break-after: always; }`}</style>
    {error ? <p role="alert">Unable to load this purchase order PDF.</p> : !documents.length ? <p>Loading purchase order...</p> : documents.map(data => <section className="po-revision-document" key={data.po.id}>
      <PrintPurchaseOrder poId={data.po.id} documentData={data} printMode={mode} onClose={() => window.close()}/>
    </section>)}
  </div>;
}
