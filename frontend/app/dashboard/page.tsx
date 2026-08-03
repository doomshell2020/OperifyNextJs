'use client';

import React from 'react';
import { useDashboard } from '../../hooks/useDashboard';
import { DashboardFilters } from '../../components/dashboard/DashboardFilters';
import { SummaryCard } from '../../components/dashboard/SummaryCard';
import { AnalyticsCard } from '../../components/dashboard/AnalyticsCard';
import { RecentPurchaseTable } from '../../components/dashboard/RecentPurchaseTable';
import { RecentProductionTable } from '../../components/dashboard/RecentProductionTable';
import { RecentMaintenanceTable } from '../../components/dashboard/RecentMaintenanceTable';
import { RecentInspectionTable } from '../../components/dashboard/RecentInspectionTable';
import { RecentGRNTable } from '../../components/dashboard/RecentGRNTable';
import { FileText, ShoppingBag, PackageCheck, Users, Wrench, RefreshCw, AlertCircle } from 'lucide-react';

export default function DashboardPage() {
  const {
    summary,
    charts,
    latestPo,
    latestProduction,
    latestMaintenance,
    latestInspection,
    latestGrn,
    isLoading,
    isError,
    refetchAll
  } = useDashboard();

  // Skeleton Loader for Cards and Tables
  if (isLoading) {
    return (
      <main className="max-w-7xl w-full mx-auto px-6 py-8 space-y-6">
        <div className="h-16 bg-white border border-slate-200 rounded-xl animate-pulse"></div>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
          {[...Array(5)].map((_, i) => (
            <div key={i} className="h-32 bg-white border border-slate-200 rounded-xl animate-pulse"></div>
          ))}
        </div>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {[...Array(3)].map((_, i) => (
            <div key={i} className="h-[360px] bg-white border border-slate-200 rounded-xl animate-pulse"></div>
          ))}
        </div>
        <div className="h-[320px] bg-white border border-slate-200 rounded-xl animate-pulse"></div>
      </main>
    );
  }

  if (isError) {
    return (
      <main className="max-w-lg w-full mx-auto px-6 py-20 flex flex-col items-center justify-center text-center">
        <div className="p-4 bg-rose-50 border border-rose-100 rounded-full text-rose-500 mb-4 animate-bounce">
          <AlertCircle className="w-10 h-10" />
        </div>
        <h2 className="text-xl font-bold text-slate-800">Connection Sync Failed</h2>
        <p className="text-sm text-slate-500 mt-2 max-w-md">
          There was a problem communicating with the tenant database instance. Check your network connection.
        </p>
        <button
          onClick={refetchAll}
          className="mt-6 flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-cyan-500 to-purple-600 hover:from-cyan-600 hover:to-purple-700 text-white rounded-lg text-xs font-semibold shadow-md transition cursor-pointer"
        >
          <RefreshCw className="w-3.5 h-3.5" />
          Retry Connection
        </button>
      </main>
    );
  }

  return (
    <>
      <main className="max-w-7xl w-full mx-auto px-6 py-8 space-y-6 select-none">
      
      {/* Connection Context Filters */}
        
        {/* Connection Context Filters */}
        <DashboardFilters />
        
        {/* Row 1: Summary Cards */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
          <SummaryCard
            title="Total Contracts"
            total={summary?.contracts.total || 0}
            today={summary?.contracts.today || 0}
            week={summary?.contracts.week || 0}
            month={summary?.contracts.month || 0}
            trend={summary?.contracts.trend || { percentage: '0%', isUp: true, label: '' }}
            sparkline={summary?.contracts.sparkline || []}
            icon={<FileText className="w-5 h-5" />}
            iconBg="bg-indigo-500"
          />

          <SummaryCard
            title="Purchase Orders"
            total={summary?.purchaseOrders.total || 0}
            today={summary?.purchaseOrders.today || 0}
            week={summary?.purchaseOrders.week || 0}
            month={summary?.purchaseOrders.month || 0}
            trend={summary?.purchaseOrders.trend || { percentage: '0%', isUp: true, label: '' }}
            sparkline={summary?.purchaseOrders.sparkline || []}
            icon={<ShoppingBag className="w-5 h-5" />}
            iconBg="bg-cyan-500"
          />

          <SummaryCard
            title="GRN Receipts"
            total={summary?.grn.total || 0}
            today={summary?.grn.today || 0}
            week={summary?.grn.week || 0}
            month={summary?.grn.month || 0}
            trend={summary?.grn.trend || { percentage: '0%', isUp: true, label: '' }}
            sparkline={summary?.grn.sparkline || []}
            icon={<PackageCheck className="w-5 h-5" />}
            iconBg="bg-amber-500"
          />

          <SummaryCard
            title="Total Vendors"
            total={summary?.vendors.total || 0}
            today={summary?.vendors.today || 0}
            week={summary?.vendors.week || 0}
            month={summary?.vendors.month || 0}
            trend={summary?.vendors.trend || { percentage: '0%', isUp: true, label: '' }}
            sparkline={summary?.vendors.sparkline || []}
            icon={<Users className="w-5 h-5" />}
            iconBg="bg-emerald-500"
          />

          <SummaryCard
            title="Maintenance"
            total={summary?.maintenance.total || 0}
            today={summary?.maintenance.today || 0}
            week={summary?.maintenance.week || 0}
            month={summary?.maintenance.month || 0}
            trend={summary?.maintenance.trend || { percentage: '0%', isUp: true, label: '' }}
            sparkline={summary?.maintenance.sparkline || []}
            icon={<Wrench className="w-5 h-5" />}
            iconBg="bg-rose-500"
          />
        </div>

        {/* Row 2: Status Analytics (Doughnut charts) */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <AnalyticsCard
            title="Purchase Order Status"
            subtitle="Current active orders distribution"
            data={charts?.purchaseOrder || []}
            colors={['#06b6d4', '#6366f1', '#f43f5e']}
          />

          <AnalyticsCard
            title="Production Status"
            subtitle="Shop floor release operations status"
            data={charts?.production || []}
            colors={['#fbbf24', '#10b981', '#cbd5e1']}
          />

          <AnalyticsCard
            title="Maintenance Status"
            subtitle="Machine breakdown assignments overview"
            data={charts?.maintenance || []}
            colors={['#ef4444', '#3b82f6', '#10b981']}
          />
        </div>

        {/* Row 3: Latest Purchase Orders Table */}
        <div className="w-full">
          <RecentPurchaseTable data={latestPo || []} />
        </div>

        {/* Row 4: Latest Production and Latest GRN Side-by-Side */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <RecentProductionTable data={latestProduction || []} />
          <RecentGRNTable data={latestGrn || []} />
        </div>

        {/* Row 5: Latest Maintenance and Latest Inspection Side-by-Side */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <RecentMaintenanceTable data={latestMaintenance || []} />
          <RecentInspectionTable data={latestInspection || []} />
        </div>

      </main>

      <footer className="border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-500 mt-10">
        Operify ERP Dashboard &copy; {new Date().getFullYear()}
      </footer>
    </>
  );
}
