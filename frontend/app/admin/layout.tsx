'use client';

import React from 'react';
import { DashboardTopbar } from '../../components/dashboard/DashboardHeader';

export default function AdminLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <div className="min-h-screen bg-[#f1f3f6] text-[#333] flex flex-col font-sans">
      <div className="sticky top-0 z-30 shadow-sm">
        <DashboardTopbar />
      </div>
      <div className="flex-1 flex flex-col min-w-0">
        <main className="flex-1 overflow-auto">
          <div className="max-w-[100%] mx-auto bg-transparent">
             {children}
          </div>
        </main>
      </div>
    </div>
  );
}
