'use client';

import React, { useState, useEffect } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { useAuth } from '../../contexts/AuthContext';
import {
  FolderClosed, CreditCard, FileText, Layers, FileSpreadsheet,
  ShoppingBag, ClipboardCheck, Truck, RefreshCw, Factory,
  Calendar, Wrench, Database, Archive, Receipt, Settings,
  LogOut, LayoutDashboard, Bell, ChevronDown
} from 'lucide-react';

export const DashboardSidebar: React.FC<{ collapsed?: boolean }> = () => null;

export const DashboardTopbar: React.FC = () => {
  const pathname = usePathname();
  const { user, logout, switchCompany } = useAuth();
  const [profileOpen, setProfileOpen] = useState(false);
  const [logoUrl, setLogoUrl] = useState<string>('https://staging.operify.in/image/logo.png');

  useEffect(() => {
    import('../../services/apiClient').then(({ default: apiClient }) => {
      apiClient.get('/settings/logo')
        .then(res => {
          if (res.data.success && res.data.logoUrl) {
            if (res.data.logoUrl.startsWith('http')) {
              setLogoUrl(res.data.logoUrl);
            } else {
              setLogoUrl(`http://localhost:5000${res.data.logoUrl}`);
            }
          }
        })
        .catch(() => {});
    });
  }, []);

  const navItems = [
    { title: 'EMD', icon: <FolderClosed className="w-[18px] h-[18px]" />, path: '/dashboard/emd' },
    { title: 'Payments', icon: <CreditCard className="w-[18px] h-[18px]" />, path: '/dashboard/payments' },
    { title: 'Contract', icon: <FileText className="w-[18px] h-[18px]" />, path: '/dashboard/contracts' },
    { title: 'Design Sheet', icon: <Layers className="w-[18px] h-[18px]" />, path: '/dashboard/design-sheet' },
    { title: 'Quotation', icon: <FileSpreadsheet className="w-[18px] h-[18px]" />, path: '/dashboard/quotations' },
    { title: 'PO', icon: <ShoppingBag className="w-[18px] h-[18px]" />, path: '/dashboard/purchase/orders' },
    { title: 'GRN Inspection', icon: <ClipboardCheck className="w-[18px] h-[18px]" />, path: '/dashboard/purchase/inspections' },
    { title: 'GRN', icon: <Truck className="w-[18px] h-[18px]" />, path: '/dashboard/purchase/grn' },
    { title: 'Indents', icon: <FileSpreadsheet className="w-[18px] h-[18px]" />, path: '/dashboard/purchase/indentpo' },
    { title: 'Reverse', icon: <RefreshCw className="w-[18px] h-[18px]" />, path: '/dashboard/reverse' },
    { title: 'Production', icon: <Factory className="w-[18px] h-[18px]" />, path: '/dashboard/production/entry' },
    { title: 'Daily Sheet', icon: <Calendar className="w-[18px] h-[18px]" />, path: '/dashboard/production/sheet' },
    { title: 'Maintenance', icon: <Wrench className="w-[18px] h-[18px]" />, path: '/dashboard/maintenance/breakdowns' },
    { title: 'Stock', icon: <Database className="w-[18px] h-[18px]" />, path: '/dashboard/inventory/stock' },
    { title: 'Daily Stock', icon: <Archive className="w-[18px] h-[18px]" />, path: '/dashboard/inventory/daily' },
    { title: 'JC Challan', icon: <Receipt className="w-[18px] h-[18px]" />, path: '/dashboard/jc-challan' },
    { title: 'JC Receive', icon: <RefreshCw className="w-[18px] h-[18px]" />, path: '/dashboard/jc-receive' },
    { title: 'Gate Pass', icon: <Truck className="w-[18px] h-[18px]" />, path: '/dashboard/gatepass' }
  ];

  const settingsMenu = [
    { title: 'Categories', path: '/dashboard/admin/categories' },
    { title: 'Products', path: '/dashboard/admin/products' },
    { title: 'Suppliers', path: '/dashboard/admin/suppliers' },
    { title: 'Users', path: '/dashboard/admin/roles' }
  ];

  const [settingsOpen, setSettingsOpen] = useState(false);

  const formatTenant = (dbName?: string) => {
    if (!dbName) return 'Central';
    if (dbName.includes('_')) {
      const parts = dbName.split('_');
      return parts[parts.length - 1].charAt(0).toUpperCase() + parts[parts.length - 1].slice(1);
    }
    return dbName.charAt(0).toUpperCase() + dbName.slice(1);
  };

  return (
    <header className="bg-[#fff] border-b border-[#ddd] flex items-center justify-between px-1 select-none h-[66px] w-full shadow-[0_1px_2px_rgba(0,0,0,0.03)] z-50 relative">
      {/* 2. Logo Section */}
      <Link href="/dashboard" className="flex items-center gap-1.5 shrink-0 justify-start w-[110px] cursor-pointer hover:opacity-80 transition-opacity">
        <div className="h-[28px] w-[28px] flex items-center justify-center shrink-0">
          <img src={logoUrl} alt="Logo" className="h-full w-full object-contain" />
        </div>
        <div className="flex flex-col">
          <span className="font-extrabold text-[12px] tracking-widest text-[#222] uppercase leading-none">TIRUPATI</span>
        </div>
      </Link>

      {/* 3. & 8. Main Navigation */}
      <nav className="flex-1 min-w-0 flex items-center overflow-x-auto overflow-y-hidden no-scrollbar px-1 h-full">
        {navItems.map((item) => {
          const isActive = pathname === item.path || pathname.startsWith(item.path + '/');
          return (
            <Link
              key={item.title}
              href={item.path}
              className={`flex flex-col items-center justify-center px-[8px] h-[52px] gap-[3px] shrink-0 border-b-[2px] transition-colors ${
                isActive ? 'border-[#1683D8] text-[#1683D8]' : 'border-transparent text-[#222] hover:bg-[#f5f5f5]'
              }`}
            >
              {React.cloneElement(item.icon as React.ReactElement, {
                className: `w-[18px] h-[18px] ${isActive ? 'text-[#1683D8]' : 'text-[#555]'}`
              })}
              <span className="text-[11px] font-semibold text-center whitespace-nowrap leading-none">{item.title}</span>
            </Link>
          );
        })}
      </nav>

      {/* 7. Right Section */}
      <div className="flex items-center gap-[6px] shrink-0 ml-auto justify-end h-full relative">
        
        {/* 11. Settings Dropdown Moved out of Nav to avoid clipping */}
        <div className="relative flex items-center h-full shrink-0 mr-2">
          <button
            onClick={() => setSettingsOpen(!settingsOpen)}
            className={`flex flex-col items-center justify-center px-[8px] h-[52px] gap-[3px] border-b-[2px] transition-colors ${
              pathname.includes('/admin/') ? 'border-[#1683D8] text-[#1683D8]' : 'border-transparent text-[#222] hover:bg-[#f5f5f5]'
            }`}
          >
            <Settings className={`w-[18px] h-[18px] ${pathname.includes('/admin/') ? 'text-[#1683D8]' : 'text-[#555]'}`} />
            <span className="text-[11px] font-semibold text-center whitespace-nowrap leading-none">Settings</span>
          </button>
          
          {settingsOpen && (
            <>
              <div className="fixed inset-0 z-40" onClick={() => setSettingsOpen(false)} />
              <div className="absolute left-1/2 -translate-x-1/2 top-[55px] w-[130px] bg-white border border-[#ccc] shadow-[0_2px_8px_rgba(0,0,0,0.1)] rounded-sm py-1 z-50">
                {settingsMenu.map((sm) => (
                  <Link
                    key={sm.title}
                    href={sm.path}
                    onClick={() => setSettingsOpen(false)}
                    className="flex items-center px-3 py-1.5 text-[10px] text-[#222] hover:bg-[#f5f5f5] transition-colors font-medium"
                  >
                    <span className="w-3 h-3 mr-2 text-[#555] flex items-center justify-center">
                       {sm.title === 'Categories' && '▦'}
                       {sm.title === 'Products' && '📦'}
                       {sm.title === 'Suppliers' && '👤'}
                       {sm.title === 'Users' && '👥'}
                    </span>
                    {sm.title}
                  </Link>
                ))}
              </div>
            </>
          )}
        </div>

        {user?.companies && user.companies.length > 1 ? (
          <select 
            value={user.db} 
            onChange={(e) => switchCompany(e.target.value)}
            className="flex items-center bg-white border border-[#ccc] rounded-[2px] px-1 py-1 text-[10px] text-[#222] outline-none cursor-pointer hover:border-[#999] h-[26px] w-[140px]"
          >
            {user.companies.map(c => (
              <option key={c.id} value={c.school_database}>
                {c.school_name.toUpperCase()}
              </option>
            ))}
          </select>
        ) : (
          <div className="flex items-center gap-1 bg-white border border-[#ccc] rounded-[2px] px-1 py-1 text-[10px] text-[#222] h-[26px] w-[140px] overflow-hidden whitespace-nowrap text-ellipsis">
            <Database className="w-[10px] h-[10px] text-[#555] shrink-0" />
            <span className="truncate">{formatTenant(user?.db)}</span>
          </div>
        )}

        {/* Profile */}
        <div className="relative">
          <button
            onClick={() => setProfileOpen(!profileOpen)}
            className="flex items-center justify-center h-[34px] w-[34px] rounded-full bg-[#f1f3f6] text-[#222] font-bold text-[12px] hover:bg-[#e2e8f0] transition-colors focus:outline-none border border-[#ccc]"
          >
            {user?.user_name ? user.user_name.charAt(0).toUpperCase() : 'U'}
          </button>
          {profileOpen && (
            <>
              <div className="fixed inset-0 z-40" onClick={() => setProfileOpen(false)} />
              <div className="absolute right-0 top-full mt-1 w-[150px] bg-white border border-[#ccc] shadow-[0_2px_8px_rgba(0,0,0,0.1)] rounded-sm p-1 z-50">
                <div className="px-2 py-2 border-b border-[#eee] mb-1">
                  <p className="text-[10px] font-bold text-[#222] truncate">{user?.user_name}</p>
                  <p className="text-[9px] text-[#666] truncate">{user?.email}</p>
                </div>
                
                <Link
                  href="/dashboard/admin/profile"
                  onClick={() => setProfileOpen(false)}
                  className="w-full flex items-center gap-2 px-2 py-1.5 text-left text-[10px] text-[#222] hover:bg-[#f5f5f5] transition cursor-pointer font-medium mb-1"
                >
                  <Settings className="w-[12px] h-[12px] text-[#555]" />
                  Profile Settings
                </Link>

                <button
                  onClick={logout}
                  className="w-full flex items-center gap-2 px-2 py-1.5 text-left text-[10px] text-[#ff0000] hover:bg-[#ffeeee] transition cursor-pointer font-medium"
                >
                  <LogOut className="w-[12px] h-[12px] text-[#ff0000]" />
                  Logout
                </button>
              </div>
            </>
          )}
        </div>
      </div>
    </header>
  );
};
