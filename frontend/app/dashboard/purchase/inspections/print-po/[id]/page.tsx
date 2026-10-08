'use client';

import React from 'react';
import { PrintPurchaseOrder } from '../../../../../../components/dashboard/PrintPurchaseOrder';

export default function PrintPoPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = React.use(params);
  return <div className="bg-white min-h-screen">
    <div className="flex justify-end p-3 print:hidden">
      <button onClick={() => window.print()} className="px-4 py-2 bg-blue-600 text-white rounded cursor-pointer">Print purchase order</button>
    </div>
    <PrintPurchaseOrder poId={Number(id)} onClose={() => window.close()} />
  </div>;
}
