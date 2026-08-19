'use client';

import React, { useState, useEffect } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { useAuth } from '../../contexts/AuthContext';
import {
  FolderClosed,
  CreditCard,
  FileText,
  Layers,
  FileSpreadsheet,
  ShoppingBag,
  ClipboardCheck,
  Truck,
  ListTodo,
  RefreshCw,
  Factory,
  Calendar,
  Wrench,
  Database,
  Archive,
  Receipt,
  Settings,
  FolderTree,
  Package,
  Users,
  Building,
  ChevronDown,
  ChevronRight,
  Globe,
  LogOut,
  LayoutDashboard,
  PanelLeftClose,
  PanelLeftOpen,
  Bell,
} from 'lucide-react';

interface NavItem {
  title: string;
  icon: React.ReactNode;
  path: string;
}

interface NavGroup {
  title: string;
  icon: React.ReactNode;
  children: NavItem[];
}

type NavEntry = NavItem | NavGroup;

function isGroup(entry: NavEntry): entry is NavGroup {
  return 'children' in entry;
}

const navEntries: NavEntry[] = [
  { title: 'Dashboard', icon: <LayoutDashboard className="w-4 h-4" />, path: '/dashboard' },
  { title: 'EMD', icon: <FolderClosed className="w-4 h-4" />, path: '/dashboard/emd' },
  { title: 'Payments', icon: <CreditCard className="w-4 h-4" />, path: '/dashboard/payments' },
  { title: 'Contract', icon: <FileText className="w-4 h-4" />, path: '/dashboard/contracts' },
  { title: 'Design Sheet', icon: <Layers className="w-4 h-4" />, path: '/dashboard/design-sheet' },
  { title: 'Quotation', icon: <FileSpreadsheet className="w-4 h-4" />, path: '/dashboard/quotations' },
  {
    title: 'Purchase',
    icon: <ShoppingBag className="w-4 h-4" />,
    children: [
      { title: 'PO', icon: <ShoppingBag className="w-4 h-4" />, path: '/dashboard/purchase/orders' },
      { title: 'GRN Inspection', icon: <ClipboardCheck className="w-4 h-4" />, path: '/dashboard/purchase/inspections' },
      { title: 'GRN', icon: <Truck className="w-4 h-4" />, path: '/dashboard/purchase/grn' },
      { title: 'Indent PO', icon: <FileSpreadsheet className="w-4 h-4" />, path: '/dashboard/purchase/indentpo' },
    ],
  },
  { title: 'Reverse', icon: <RefreshCw className="w-4 h-4" />, path: '/dashboard/reverse' },
  {
    title: 'Production',
    icon: <Factory className="w-4 h-4" />,
    children: [
      { title: 'Production Entry', icon: <Factory className="w-4 h-4" />, path: '/dashboard/production/entry' },
      { title: 'Daily Sheet', icon: <Calendar className="w-4 h-4" />, path: '/dashboard/production/sheet' },
    ],
  },
  { title: 'Maintenance', icon: <Wrench className="w-4 h-4" />, path: '/dashboard/maintenance/breakdowns' },
  {
    title: 'Inventory',
    icon: <Database className="w-4 h-4" />,
    children: [
      { title: 'Stock', icon: <Database className="w-4 h-4" />, path: '/dashboard/inventory/stock' },
      { title: 'Daily Stock', icon: <Archive className="w-4 h-4" />, path: '/dashboard/inventory/daily' },
    ],
  },
  { title: 'JC Challan', icon: <Receipt className="w-4 h-4" />, path: '/dashboard/jc-challan' },
  {
    title: 'Settings',
    icon: <Settings className="w-4 h-4" />,
    children: [
      { title: 'Categories', icon: <FolderTree className="w-4 h-4" />, path: '/dashboard/admin/categories' },
      { title: 'Products', icon: <Package className="w-4 h-4" />, path: '/dashboard/admin/products' },
      { title: 'Suppliers', icon: <Building className="w-4 h-4" />, path: '/dashboard/admin/suppliers' },
      { title: 'Users', icon: <Users className="w-4 h-4" />, path: '/dashboard/admin/users' },
    ],
  },
];

export const DashboardSidebar: React.FC<{ collapsed: boolean }> = ({ collapsed }) => {
  const pathname = usePathname();
  const [logoUrl, setLogoUrl] = useState<string>('https://staging.operify.in/image/logo.png');
  const [openGroups, setOpenGroups] = useState<Record<string, boolean>>({
    Purchase: true,
    Production: false,
    Inventory: false,
    Settings: false,
  });

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

  const toggleGroup = (title: string) => {
    setOpenGroups(prev => ({ ...prev, [title]: !prev[title] }));
  };

  const isActive = (path: string) => pathname === path || pathname.startsWith(path + '/');
  const isGroupActive = (group: NavGroup) => group.children.some(c => isActive(c.path));

  return (
    <aside
      className={`h-full flex flex-col bg-white border-r border-slate-200 transition-all duration-300 ${
        collapsed ? 'w-[60px]' : 'w-[220px]'
      }`}
    >
      {/* Brand */}
      <div className={`flex items-center gap-2.5 px-3 py-4 border-b border-slate-100 shrink-0 ${collapsed ? 'justify-center' : ''}`}>
        <div className="h-8 w-8 flex items-center justify-center shrink-0">
          <img src={logoUrl} alt="Operify Logo" className="h-full w-full object-contain" />
        </div>
        {!collapsed && (
          <div className="flex flex-col">
            <span className="font-extrabold text-xs tracking-widest text-slate-900 uppercase leading-none">TIRUPATI</span>
            <span className="text-[8px] font-bold text-slate-400 uppercase tracking-widest leading-none mt-0.5">ERP Console</span>
          </div>
        )}
      </div>

      {/* Nav Items */}
      <nav className="flex-1 overflow-y-auto py-3 px-2 space-y-0.5">
        {navEntries.map((entry) => {
          if (isGroup(entry)) {
            const active = isGroupActive(entry);
            const open = openGroups[entry.title];
            return (
              <div key={entry.title}>
                <button
                  onClick={() => toggleGroup(entry.title)}
                  title={collapsed ? entry.title : undefined}
                  className={`w-full flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-left text-sm transition-colors ${
                    active
                      ? 'text-cyan-700 bg-cyan-50 font-semibold'
                      : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'
                  }`}
                >
                  <span className="shrink-0">{entry.icon}</span>
                  {!collapsed && (
                    <>
                      <span className="flex-1 font-medium text-xs">{entry.title}</span>
                      {open ? <ChevronDown className="w-3.5 h-3.5 text-slate-400" /> : <ChevronRight className="w-3.5 h-3.5 text-slate-400" />}
                    </>
                  )}
                </button>
                {!collapsed && open && (
                  <div className="mt-0.5 ml-3 pl-3 border-l-2 border-slate-100 space-y-0.5">
                    {entry.children.map(child => (
                      <Link
                        key={child.path}
                        href={child.path}
                        className={`flex items-center gap-2 px-2 py-1.5 rounded-lg text-xs transition-colors ${
                          isActive(child.path)
                            ? 'text-cyan-700 bg-cyan-50 font-semibold'
                            : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50'
                        }`}
                      >
                        <span className="shrink-0">{child.icon}</span>
                        <span>{child.title}</span>
                      </Link>
                    ))}
                  </div>
                )}
              </div>
            );
          }

          // Single item
          const active = isActive(entry.path);
          return (
            <Link
              key={entry.path}
              href={entry.path}
              title={collapsed ? entry.title : undefined}
              className={`flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm transition-colors ${
                active
                  ? 'text-cyan-700 bg-cyan-50 font-semibold'
                  : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'
              }`}
            >
              <span className="shrink-0">{entry.icon}</span>
              {!collapsed && <span className="font-medium text-xs">{entry.title}</span>}
            </Link>
          );
        })}
      </nav>
    </aside>
  );
};

export const DashboardTopbar: React.FC<{
  collapsed: boolean;
  onToggle: () => void;
}> = ({ collapsed, onToggle }) => {
  const { user, logout, switchCompany } = useAuth();
  const [profileOpen, setProfileOpen] = useState(false);

  const formatTenant = (dbName?: string) => {
    if (!dbName) return 'Central';
    if (dbName.includes('_')) {
      const parts = dbName.split('_');
      return parts[parts.length - 1].charAt(0).toUpperCase() + parts[parts.length - 1].slice(1);
    }
    return dbName.charAt(0).toUpperCase() + dbName.slice(1);
  };

  return (
    <header className="sticky top-0 z-40 bg-white border-b border-slate-200 h-14 flex items-center px-4 gap-4 select-none print:hidden">
      {/* Sidebar Toggle */}
      <button
        onClick={onToggle}
        className="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition-colors"
      >
        {collapsed ? <PanelLeftOpen className="w-5 h-5" /> : <PanelLeftClose className="w-5 h-5" />}
      </button>

      {/* Page title area — flex spacer */}
      <div className="flex-1" />

      {/* Right: DB Pill + Notifications + Profile */}
      <div className="flex items-center gap-3">
        {/* Tenant DB Switcher */}
        {user?.companies && user.companies.length > 1 ? (
          <select 
            value={user.db} 
            onChange={(e) => switchCompany(e.target.value)}
            className="flex items-center bg-slate-50 border border-slate-200 rounded-lg px-2 py-1 text-xs font-extrabold text-slate-700 tracking-wide outline-none cursor-pointer focus:ring-1 focus:ring-cyan-500"
          >
            {user.companies.map(c => (
              <option key={c.id} value={c.school_database}>
                {c.school_name.toUpperCase()}
              </option>
            ))}
          </select>
        ) : (
          <div className="flex items-center gap-1.5 bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 text-[10px] font-extrabold text-slate-700 tracking-wide">
            <Database className="w-3 h-3 text-slate-400" />
            {formatTenant(user?.db)}
          </div>
        )}

        {/* Notifications */}
        <button className="relative p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition-colors">
          <Bell className="w-5 h-5" />
        </button>

        {/* Profile */}
        <div className="relative">
          <button
            onClick={() => setProfileOpen(!profileOpen)}
            className="h-8 w-8 flex items-center justify-center rounded-lg bg-gradient-to-tr from-cyan-500 to-purple-600 text-white font-bold text-sm shadow-sm focus:outline-none cursor-pointer"
          >
            {user?.user_name ? user.user_name.charAt(0).toUpperCase() : 'U'}
          </button>
          {profileOpen && (
            <>
              <div className="fixed inset-0 z-40" onClick={() => setProfileOpen(false)} />
              <div className="absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-xl shadow-lg p-1.5 z-50">
                <div className="px-3 py-2 border-b border-slate-100 mb-1">
                  <p className="text-xs font-semibold text-slate-800">{user?.user_name}</p>
                  <p className="text-[10px] text-slate-400 truncate">{user?.email}</p>
                </div>
                
                <Link
                  href="/dashboard/admin/profile"
                  onClick={() => setProfileOpen(false)}
                  className="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-left text-xs text-slate-700 hover:bg-slate-50 transition cursor-pointer font-medium mb-1"
                >
                  <Settings className="w-4 h-4" />
                  Profile Settings
                </Link>

                <button
                  onClick={logout}
                  className="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-left text-xs text-rose-600 hover:bg-rose-50 transition cursor-pointer font-medium"
                >
                  <LogOut className="w-4 h-4" />
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
