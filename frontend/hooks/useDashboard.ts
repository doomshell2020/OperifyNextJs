import { useQuery } from '@tanstack/react-query';
import dashboardService from '../services/dashboard.service';

export function useDashboard() {
  const summaryQuery = useQuery({
    queryKey: ['dashboard', 'summary'],
    queryFn: () => dashboardService.getSummary(),
    staleTime: 5 * 60 * 1000, // Cache for 5 minutes
  });

  const chartsQuery = useQuery({
    queryKey: ['dashboard', 'charts'],
    queryFn: () => dashboardService.getCharts(),
    staleTime: 5 * 60 * 1000,
  });

  const poQuery = useQuery({
    queryKey: ['dashboard', 'latest-po'],
    queryFn: () => dashboardService.getLatestPurchaseOrders(),
    staleTime: 2 * 60 * 1000,
  });

  const productionQuery = useQuery({
    queryKey: ['dashboard', 'latest-production'],
    queryFn: () => dashboardService.getLatestProduction(),
    staleTime: 2 * 60 * 1000,
  });

  const maintenanceQuery = useQuery({
    queryKey: ['dashboard', 'latest-maintenance'],
    queryFn: () => dashboardService.getLatestMaintenance(),
    staleTime: 2 * 60 * 1000,
  });

  const inspectionQuery = useQuery({
    queryKey: ['dashboard', 'latest-inspection'],
    queryFn: () => dashboardService.getLatestInspection(),
    staleTime: 2 * 60 * 1000,
  });

  const grnQuery = useQuery({
    queryKey: ['dashboard', 'latest-grn'],
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
      poQuery.refetch();
      productionQuery.refetch();
      maintenanceQuery.refetch();
      inspectionQuery.refetch();
      grnQuery.refetch();
    }
  };
}
