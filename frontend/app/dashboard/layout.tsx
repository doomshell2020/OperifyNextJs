'use client';

import React, { useState } from 'react';
import { DashboardSidebar, DashboardTopbar } from '../../components/dashboard/DashboardHeader';

export default function DashboardLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const [collapsed, setCollapsed] = useState(false);

  return (
    <div className="min-h-screen bg-slate-50 text-slate-900 flex font-sans">
      
      {/* Sidebar — fixed height, sticky */}
      <div className="sticky top-0 h-screen flex-shrink-0 z-30">
        <DashboardSidebar collapsed={collapsed} />
      </div>

      {/* Main area: topbar + scrollable content */}
      <div className="flex-1 flex flex-col min-w-0">
        <DashboardTopbar collapsed={collapsed} onToggle={() => setCollapsed(c => !c)} />

        {/* Page Content */}
        <main className="flex-1 p-6 overflow-auto">
          {children}
        </main>
      </div>

    </div>
  );
}
