'use client';

import React, { useEffect } from 'react';
import { useQuery } from '@tanstack/react-query';
import purchaseOrderService from '../../../../../../services/purchaseOrder.service';
import { PurchaseOrderDetailsPrint } from '../../../../../../components/PurchaseOrderDetailsPrint';
import { Loader } from 'lucide-react';

export default function PrintPoPage({ params }: { params: Promise<{ id: string }> }) {
  const resolvedParams = React.use(params);
  
  const { data, isLoading } = useQuery({
    queryKey: ['purchase-order-details', resolvedParams.id],
    queryFn: () => purchaseOrderService.getDetails(resolvedParams.id),
  });

  useEffect(() => {
    if (data) {
      setTimeout(() => {
        window.print();
      }, 500);
    }
  }, [data]);

  if (isLoading) {
    return (
      <div className="flex justify-center items-center h-screen bg-slate-50">
        <Loader className="animate-spin w-8 h-8 text-cyan-600" />
      </div>
    );
  }

  return (
    <div className="bg-white min-h-screen">
      <PurchaseOrderDetailsPrint data={data} standalone={true} />
    </div>
  );
}
