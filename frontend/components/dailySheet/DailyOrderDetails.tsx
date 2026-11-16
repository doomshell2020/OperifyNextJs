'use client';
import ProductionOrderDetails from '@/components/productionOrder/ProductionOrderDetails';
import '@/app/dashboard/production/entry/production-orders.css';
export default function DailyOrderDetails({poId,onClose}:{poId:string;onClose:()=>void}) {
  return <ProductionOrderDetails poId={poId} onClose={onClose} apiBase="/daily-sheets/orders" dailyLinks/>;
}
