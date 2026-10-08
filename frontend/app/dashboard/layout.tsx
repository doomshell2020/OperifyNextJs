'use client';

import React, { useState } from 'react';
import { DashboardSidebar, DashboardTopbar } from '../../components/dashboard/DashboardHeader';
import './legacy.css';

export default function DashboardLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <div className="legacy-erp min-h-screen text-[#333] flex flex-col print:block print:bg-white print:min-h-0">

      {/* Top Header with Navigation */}
      <div className="sticky top-0 z-30 print:hidden shadow-sm">
        <DashboardTopbar />
      </div>

      {/* Main area: scrollable content */}
      <div className="flex-1 flex flex-col min-w-0 print:block">
        <main className="legacy-content flex-1 overflow-auto print:p-0 print:overflow-visible print:block">
          <div className="max-w-[100%] mx-auto bg-transparent">
             {children}
          </div>
        </main>
      </div>

    </div>
  );
}
