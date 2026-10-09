'use client';
import { ReactNode } from 'react';
import { usePathname } from 'next/navigation';
import { useLegacyActionAccess } from './useLegacyActionAccess';

// Map existing Next pages to the PHP operation they execute. This uses the
// existing session grants; opening a form URL never creates a permission.
export function LegacyRouteAccess({ children }: { children: ReactNode }) {
  const pathname = usePathname();
  const can = useLegacyActionAccess();
  let controller = '', action = '';
  const modules: [string, string][] = [
    ['/dashboard/contracts', 'contracts'], ['/dashboard/design-sheet', 'designsheet'],
    ['/dashboard/purchase/orders', 'purchaseorder'], ['/dashboard/purchase/indentpo', 'indentpo'],
    ['/dashboard/purchase/indents', 'indent'], ['/dashboard/reverse', 'reverseindent'],
    ['/dashboard/admin/products', 'additem'], ['/dashboard/admin/categories', 'itemcategory'],
    ['/dashboard/admin/suppliers', 'vendors'], ['/dashboard/admin/roles', 'roles'],
    ['/dashboard/emd', 'emd'], ['/dashboard/payments', 'paymentmanager'],
    ['/dashboard/quotations', 'quotation'],
  ];
  const module = modules.find(([base]) => pathname === base || pathname.startsWith(base + '/'));
  if (module) {
    const [base, name] = module;
    controller = name;
    const suffix = pathname.slice(base.length);
    action = !suffix ? 'index' : /\/(add|new)$/.test(suffix) ? 'add'
      : /\/edit(\/|$)/.test(suffix) ? 'edit'
      : name === 'designsheet' ? 'viewdesignsheet'
      : name === 'indentpo' ? 'viewindentpodetail'
      : name === 'indent' ? 'view'
      : name === 'reverseindent' ? 'viewreverseindent' : 'index';
  } else if (pathname.startsWith('/dashboard/purchase/inspections')) {
    controller = pathname.includes('/print-po/') ? 'purchaseorder' : 'goodsreceived';
    action = pathname.includes('/print-po/') ? 'view' : pathname.endsWith('/add') ? 'add_inspection_grn' : 'grninspection';
  } else if (pathname.startsWith('/dashboard/purchase/grn')) {
    controller = 'goodsreceived';
    action = pathname.endsWith('/add') ? 'add' : pathname.includes('/view/') ? 'viewgrndetail' : 'index';
  } else if (pathname.startsWith('/dashboard/inventory/')) {
    controller = 'stockregister'; action = pathname.endsWith('/daily') ? 'dailystock' : 'index';
  } else if (pathname.startsWith('/dashboard/production/')) {
    controller = 'production'; action = pathname.includes('/viewcontractdetailspdf/') ? 'viewcontractdetailspdf'
      : pathname.endsWith('/entry') ? 'productionorders' : 'index';
  } else if (pathname.startsWith('/dashboard/maintenance/')) {
    controller = 'maintenance'; action = 'index';
  }
  // JC/Receive/Gate Pass pages already check the individual operation using
  // useJcAccess. Dashboard and own-profile pages require authentication only.
  if (controller && (!can(controller, action) || (action === 'add_inspection_grn' && !can('goodsreceived', 'add')))) {
    return <p role="alert" className="p-4">You do not have permission to access this page.</p>;
  }
  return children;
}
