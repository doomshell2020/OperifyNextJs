'use client';

import React, { useState, useEffect } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { useAuth } from '../../contexts/AuthContext';
import { usePermission } from '../../contexts/PermissionContext';
import { useJcAccess } from '../jobChallan/useJcAccess';
import { DEFAULT_LOGO_URL, resolveApiAssetUrl } from '../../services/apiConfig';
import apiClient from '../../services/apiClient';
import {
  FolderClosed, CreditCard, FileText, Layers, FileSpreadsheet,
  ShoppingBag, ClipboardCheck, Truck, RefreshCw, Factory,
  Calendar, Wrench, Database, Archive, Receipt, Settings,
  LogOut, LayoutDashboard, Bell, ChevronDown
} from 'lucide-react';

export const DashboardSidebar: React.FC<{ collapsed?: boolean }> = () => null;

export const DashboardTopbar: React.FC = () => {
  const pathname = usePathname();
  const { user, loading, logout, switchCompany } = useAuth();
  const { hasPermission } = usePermission();
  const { can: canJc } = useJcAccess();
  const [profileOpen, setProfileOpen] = useState(false);
  const [logoUrl, setLogoUrl] = useState<string>(DEFAULT_LOGO_URL);
  const [companyError, setCompanyError] = useState<string | null>(null);

  useEffect(() => {
    if (!user?.id) return;
    let active = true;
    setLogoUrl(DEFAULT_LOGO_URL);
    const refreshLogo = () => {
      apiClient.get('/settings/logo')
        .then(res => {
          if (active && res.data.success && res.data.logoUrl) {
            setLogoUrl(resolveApiAssetUrl(res.data.logoUrl));
          }
        })
        .catch(() => {});
    };
    refreshLogo();
    window.addEventListener('operify:logo-updated', refreshLogo);
    return () => {
      active = false;
      window.removeEventListener('operify:logo-updated', refreshLogo);
    };
  }, [user?.id, user?.db]);

  const navItems = [
    { title: 'EMD', icon: <FolderClosed className="w-[18px] h-[18px]"/>, path: '/dashboard/emd' },
    { title: 'Payments', icon: <img src="/legacy/headericons/inspectionindex.png" alt="" width={33} height={26}/>, path: '/dashboard/payments' },
    { title: 'Contract', icon: <img src="/legacy/headericons/contractsindex.png" alt="" width={33} height={26}/>, path: '/dashboard/contracts' },
    { title: 'Design Sheet', icon: <img src="/legacy/headericons/designsheetindex.png" alt="" width={33} height={26}/>, path: '/dashboard/design-sheet' },
    { title: 'Quotation', icon: <img src="/legacy/headericons/quotationindex.png" alt="" width={33} height={26}/>, path: '/dashboard/quotations' },
    { title: 'PO', icon: <img src="/legacy/headericons/purchaseorderindex.png" alt="" width={33} height={26}/>, path: '/dashboard/purchase/orders' },
    { title: 'GRN Inspection', icon: <img src="/legacy/headericons/quotationindex.png" alt="" width={33} height={26}/>, path: '/dashboard/purchase/inspections' },
    { title: 'GRN', icon: <img src="/legacy/headericons/goodsreceivedindex.png" alt="" width={33} height={26}/>, path: '/dashboard/purchase/grn' },
    { title: 'Indents', icon: <img src="/legacy/headericons/indentpoindex.png" alt="" width={33} height={26}/>, path: '/dashboard/purchase/indentpo' },
    { title: 'Reverse', icon: <img src="/legacy/headericons/reverseindentindex.png" alt="" width={33} height={26}/>, path: '/dashboard/reverse' },
    { title: 'Production', icon: <img src="/legacy/headericons/productionproductionorders.png" alt="" width={33} height={26}/>, path: '/dashboard/production/entry' },
    { title: 'Daily Sheet', icon: <img src="/legacy/headericons/productionindex.png" alt="" width={33} height={26}/>, path: '/dashboard/production/sheet' },
    { title: 'Maintenance', icon: <img src="/legacy/headericons/maintenanceindex.png" alt="" width={33} height={26}/>, path: '/dashboard/maintenance/breakdowns' },
    { title: 'Stock', icon: <img src="/legacy/headericons/stockregisterindex.png" alt="" width={33} height={26}/>, path: '/dashboard/inventory/stock' },
    { title: 'Daily Stock', icon: <img src="/legacy/headericons/stockregisterdailystock.png" alt="" width={33} height={26}/>, path: '/dashboard/inventory/daily' }
  ];

  const jobWorkItems = [
    { title: 'JC Challan', path: '/dashboard/jc-challan', allowed: canJc('jobchallan', 'index') },
    { title: 'JC Receive', path: '/dashboard/jc-receive', allowed: canJc('jobchallan', 'receiveindex') },
    { title: 'Gate Pass', path: '/dashboard/gatepass', allowed: canJc('gatepasses', 'index') }
  ].filter(item => item.allowed);

  const settingsMenu = [
    { title: 'Categories', path: '/dashboard/admin/categories', allowed: hasPermission('legacy:admin/itemcategory/index') },
    { title: 'Products', path: '/dashboard/admin/products', allowed: hasPermission('legacy:admin/additem/index') },
    { title: 'Suppliers', path: '/dashboard/admin/suppliers', allowed: hasPermission('legacy:admin/vendors/index') },
    { title: 'Users', path: '/dashboard/admin/roles', allowed: hasPermission('legacy:admin/roles/index') },
    { title: 'Permission', path: '/admin/permission', allowed: Number(user?.role_id) === 101 }
  ].filter(item => !('allowed' in item) || item.allowed);

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
    <header className="bg-[#fff] border-b border-[#ddd] flex items-center justify-between px-1 select-none h-[58px] w-full shadow-[0_1px_2px_rgba(0,0,0,0.03)] z-50 relative">
      {/* 2. Logo Section */}
      <Link href="/dashboard" className="flex items-center gap-1.5 shrink-0 justify-start w-[115px] cursor-pointer hover:opacity-80 transition-opacity [&>*]:pointer-events-none">
        <div className="h-[28px] w-[28px] flex items-center justify-center shrink-0">
          <img src={logoUrl} alt="Logo" className="h-full w-full object-contain" />
        </div>
        <div className="flex flex-col">
          <span className="font-extrabold text-[12px] tracking-widest text-[#222] uppercase leading-none">TIRUPATI</span>
        </div>
      </Link>

      {/* 3. & 8. Main Navigation */}
      <nav className="flex-1 min-w-0 flex items-center overflow-x-auto overflow-y-hidden no-scrollbar px-1 h-full">
        {user?.role_id === 101 ? (
          <>
            <div className="flex-1"></div>
          </>
        ) : (
          navItems.filter(item => {
            if(item.title==="EMD") return hasPermission("legacy:admin/emd/index");
            if(item.title==="Payments") return hasPermission("legacy:admin/paymentmanager/index");
            if(item.title==="Contract") return hasPermission("contracts:view") || hasPermission("legacy:admin/contracts/index");
            if(item.title==="Design Sheet") return hasPermission("designsheet:view") || hasPermission("legacy:admin/designsheet/index");
            if(item.title==="Quotation") return hasPermission("legacy:admin/quotation/index");
            if(item.title==="PO") return hasPermission("purchaseorder:view") || hasPermission("legacy:admin/purchaseorder/index");
            if(item.title==="GRN Inspection") return hasPermission("legacy:admin/goodsreceived/grninspection");
            if(item.title==="GRN") return hasPermission("grn:view") || hasPermission("legacy:admin/goodsreceived/index");
            if(item.title==="Indents") return hasPermission("legacy:admin/indentpo/index");
            if(item.title==="Reverse") return hasPermission("legacy:admin/reverseindent/index");
            if(item.title==="Production") return hasPermission("production:view") || hasPermission("legacy:admin/production/productionorders");
            if(item.title==="Daily Sheet") return hasPermission("legacy:admin/production/index");
            if(item.title==="Maintenance") return hasPermission("legacy:admin/maintenance/index");
            if(item.title==="Stock") return hasPermission("stock:view") || hasPermission("legacy:admin/stockregister/index");
            if(item.title==="Daily Stock") return hasPermission("legacy:admin/stockregister/dailystock");

            // For others where we are not 100% sure of the exact CakePHP URL, we still show them by default,
            // but the user can add precise URL restrictions if needed.
            return true;
          }).map((item) => {
            const isActive = pathname === item.path || pathname.startsWith(item.path + '/');
            return (
              <Link
                key={item.title}
                href={item.path}
                className={`flex flex-col items-center justify-center px-[4px] h-[52px] gap-[3px] shrink-0 border-b-[2px] transition-colors [&>*]:pointer-events-none ${
                  isActive ? 'border-[#1683D8] text-[#1683D8]' : 'border-transparent text-[#222] hover:bg-[#f5f5f5]'
                }`}
              >
                {item.icon}
                <span className="text-[9px] font-normal text-center whitespace-nowrap leading-none pointer-events-none">{item.title}</span>
              </Link>
            );
          })
        )}
        {user?.role_id !== 101 && jobWorkItems.map(item => <Link key={item.path} href={item.path}
          className={`flex flex-col items-center justify-center px-1 h-[52px] gap-[3px] shrink-0 border-b-2 ${pathname.startsWith(item.path) ? 'border-[#1683D8] text-[#1683D8]' : 'border-transparent text-[#222] hover:bg-[#f5f5f5]'}`}>
          {item.title === "Gate Pass" ? <Receipt className="w-[22px] h-[22px]"/> : <img src={item.title === "JC Challan" ? "/legacy/headericons/cheque.png" : "/legacy/headericons/goodsreceivedindex.png"} alt="" width={33} height={26}/>}
          <span className="text-[9px] whitespace-nowrap">{item.title}</span>
        </Link>)}
      </nav>

      {/* 7. Right Section */}
      <div className="flex items-center gap-[6px] shrink-0 ml-auto justify-end h-full relative">
        {/* Superadmin specific links or Settings */}
        {user?.role_id === 101 ? (
          <>
            <Link href="/dashboard/admin/schools" className="flex flex-col items-center justify-center px-[4px] h-[52px] gap-[3px] border-b-[2px] border-transparent text-[#222] [&>*]:pointer-events-none hover:bg-[#f5f5f5]">
              <Database className="w-[18px] h-[18px] text-[#555]" />
              <span className="text-[9px] font-normal text-center whitespace-nowrap leading-none">Companies</span>
            </Link>
            <Link href="/dashboard/admin/template" className="flex flex-col items-center justify-center px-[4px] h-[52px] gap-[3px] border-b-[2px] border-transparent text-[#222] [&>*]:pointer-events-none hover:bg-[#f5f5f5]">
              <FileText className="w-[18px] h-[18px] text-[#555]" />
              <span className="text-[9px] font-normal text-center whitespace-nowrap leading-none">Template</span>
            </Link>
            <Link href="/admin/permission" className={`flex flex-col items-center justify-center px-[4px] h-[52px] gap-[3px] border-b-[2px] transition-colors [&>*]:pointer-events-none ${pathname.includes('/admin/permission') ? 'border-[#1683D8] text-[#1683D8]' : 'border-transparent text-[#222] hover:bg-[#f5f5f5]'}`}>
              <Settings className={`w-[18px] h-[18px] ${pathname.includes('/admin/permission') ? 'text-[#1683D8]' : 'text-[#555]'}`} />
              <span className="text-[9px] font-normal text-center whitespace-nowrap leading-none">Permission</span>
            </Link>
            <Link href="/dashboard/admin/demo" className="flex flex-col items-center justify-center px-[4px] h-[52px] gap-[3px] border-b-[2px] border-transparent text-[#222] [&>*]:pointer-events-none hover:bg-[#f5f5f5]">
              <FileSpreadsheet className="w-[18px] h-[18px] text-[#555]" />
              <span className="text-[9px] font-normal text-center whitespace-nowrap leading-none">Demo Request</span>
            </Link>
            <Link href="/dashboard/admin/spam" className="flex flex-col items-center justify-center px-[4px] h-[52px] gap-[3px] border-b-[2px] border-transparent text-[#222] [&>*]:pointer-events-none hover:bg-[#f5f5f5]">
              <Archive className="w-[18px] h-[18px] text-[#555]" />
              <span className="text-[9px] font-normal text-center whitespace-nowrap leading-none">Spam</span>
            </Link>
          </>
        ) : (
          <div className="relative flex items-center h-full shrink-0 mr-2">
            <button
              onClick={() => setSettingsOpen(!settingsOpen)}
              className={`flex flex-col items-center justify-center px-[4px] h-[52px] gap-[3px] border-b-[2px] transition-colors [&>*]:pointer-events-none ${
                pathname.includes('/admin/') ? 'border-[#1683D8] text-[#1683D8]' : 'border-transparent text-[#222] hover:bg-[#f5f5f5]'
              }`}
            >
              <Settings className={`w-[18px] h-[18px] ${pathname.includes('/admin/') ? 'text-[#1683D8]' : 'text-[#555]'}`} />
              <span className="text-[9px] font-normal text-center whitespace-nowrap leading-none">Settings</span>
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
                        {sm.title === 'Permission' && '🔐'}
                      </span>
                      {sm.title}
                    </Link>
                  ))}
                </div>
              </>
            )}
          </div>
        )}

        {user?.companies && (user.companies.length > 1 || (user.role_id === 105 && user.companies.length === 1)) ? (
          <select
            aria-label="Company"
            value={user.db}
            disabled={loading}
            onChange={(e) => {
              setCompanyError(null);
              void switchCompany(e.target.value).catch(error => setCompanyError(String(error)));
            }}
            className="flex items-center bg-white border border-[#ccc] rounded-[2px] px-1 py-1 text-[10px] text-[#222] outline-none cursor-pointer hover:border-[#999] h-[26px] w-[140px]"
          >
            {user.companies.map(c => (
              <option key={c.school_database} value={c.school_database}>
                {c.school_name}
              </option>
            ))}
          </select>
        ) : (
          <div className="flex items-center gap-1 bg-white border border-[#ccc] rounded-[2px] px-1 py-1 text-[10px] text-[#222] h-[26px] w-[140px] overflow-hidden whitespace-nowrap text-ellipsis">
            <Database className="w-[10px] h-[10px] text-[#555] shrink-0" />
            <span className="truncate">{formatTenant(user?.db)}</span>
          </div>
        )}
        {companyError && <span role="alert" className="text-red-700 text-xs">{companyError}</span>}

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


