import { useQuery } from '@tanstack/react-query';
import purchaseOrderService from '../services/purchaseOrder.service';

export function usePurchaseOrderHover(id: number | string, enabled: boolean) {
  return useQuery({
    queryKey: ['purchase-order', 'hover', id],
    queryFn: () => purchaseOrderService.getHoverDetails(id),
    enabled: enabled && !!id,
    staleTime: 10 * 60 * 1000, // Cache for 10 minutes
    retry: 1
  });
}
