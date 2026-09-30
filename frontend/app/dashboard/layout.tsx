'use client';

import React, { useState } from 'react';
import { DashboardSidebar, DashboardTopbar } from '../../components/dashboard/DashboardHeader';

export default function DashboardLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <div className="min-h-screen bg-[#f1f3f6] text-[#333] flex flex-col font-sans print:block print:bg-white print:min-h-0">
      
      {/* Top Header with Navigation */}
      <div className="sticky top-0 z-30 print:hidden shadow-sm">
        <DashboardTopbar />
      </div>

      {/* Main area: scrollable content */}
      <div className="flex-1 flex flex-col min-w-0 print:block">
        <main className="flex-1 p-4 overflow-auto print:p-0 print:overflow-visible print:block">
          <div className="max-w-[100%] mx-auto bg-transparent">
             {children}
          </div>
        </main>
      </div>

    </div>
  );
}
