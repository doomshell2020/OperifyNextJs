import { useLegacyActionAccess } from '@/components/ui/useLegacyActionAccess';
import { useQuery } from '@tanstack/react-query';
import dashboardService from '../services/dashboard.service';
import { useAuth } from '../contexts/AuthContext';

export function useDashboard() {
  const can = useLegacyActionAccess();
  const { user, loading } = useAuth();
  const scope = [user?.db, user?.id];
  const enabled = !!user && !loading;
  const summaryQuery = useQuery({
    queryKey: ['dashboard', ...scope, 'summary'],
    enabled,
    queryFn: () => dashboardService.getSummary(),
    staleTime: 5 * 60 * 1000, // Cache for 5 minutes
  });

  const chartsQuery = useQuery({
    queryKey: ['dashboard', ...scope, 'charts'],
    enabled,
    queryFn: () => dashboardService.getCharts(),
    staleTime: 5 * 60 * 1000,
  });

  const poQuery = useQuery({
    queryKey: ['dashboard', ...scope, 'latest-po'],
    enabled: enabled && can('purchaseorder','index'),
    queryFn: () => dashboardService.getLatestPurchaseOrders(),
    staleTime: 2 * 60 * 1000,
  });

  const productionQuery = useQuery({
    queryKey: ['dashboard', ...scope, 'latest-production'],
    enabled: enabled && can('production','productionorders'),
    queryFn: () => dashboardService.getLatestProduction(),
    staleTime: 2 * 60 * 1000,
  });

  const maintenanceQuery = useQuery({
    queryKey: ['dashboard', ...scope, 'latest-maintenance'],
    enabled: enabled && can('maintenance','index'),
    queryFn: () => dashboardService.getLatestMaintenance(),
    staleTime: 2 * 60 * 1000,
  });

  const inspectionQuery = useQuery({
    queryKey: ['dashboard', ...scope, 'latest-inspection'],
    enabled: enabled && can('goodsreceived','grninspection'),
    queryFn: () => dashboardService.getLatestInspection(),
    staleTime: 2 * 60 * 1000,
  });

  const grnQuery = useQuery({
    queryKey: ['dashboard', ...scope, 'latest-grn'],
    enabled: enabled && can('goodsreceived','index'),
    queryFn: () => dashboardService.getLatestGrn(),
    staleTime: 2 * 60 * 1000,
  });

  return {
    summary: summaryQuery.data,
    charts: chartsQuery.data,
    latestPo: poQuery.data,
    latestProduction: productionQuery.data,
    latestMaintenance: maintenanceQuery.data,
    latestInspection: inspectionQuery.data,
    latestGrn: grnQuery.data,
    
    // Status indicators
    isLoading:
      summaryQuery.isLoading ||
      chartsQuery.isLoading ||
      poQuery.isLoading ||
      productionQuery.isLoading ||
      maintenanceQuery.isLoading ||
      inspectionQuery.isLoading ||
      grnQuery.isLoading,
      
    isError:
      summaryQuery.isError ||
      chartsQuery.isError ||
      poQuery.isError ||
      productionQuery.isError ||
      maintenanceQuery.isError ||
      inspectionQuery.isError ||
      grnQuery.isError,
      
    refetchAll: () => {
      summaryQuery.refetch();
      chartsQuery.refetch();
      if (can('purchaseorder','index')) poQuery.refetch();
      if (can('production','productionorders')) productionQuery.refetch();
      if (can('maintenance','index')) maintenanceQuery.refetch();
      if (can('goodsreceived','grninspection')) inspectionQuery.refetch();
      if (can('goodsreceived','index')) grnQuery.refetch();
    }
  };
}
