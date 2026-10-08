'use client';

import React from 'react';
import Link from 'next/link';
import { useDashboard } from '../../hooks/useDashboard';
import { openPurchaseOrderPdf } from '../../services/purchaseOrderPdf.service';
import { ChartItem } from '../../services/dashboard.service';
import { formatContractDate } from '../../utils/dateFormatter';
import toast from 'react-hot-toast';
import styles from './overview.module.css';

const date = (value?: string) => value ? formatContractDate(value) : '-';
const amount = (value: number) => Number(value || 0).toLocaleString('en-IN', { maximumFractionDigits: 2 });
const text = (value?: string | number) => value ?? '-';
function openPdf(id: number) {
  void openPurchaseOrderPdf(id).catch(() => toast.error('Unable to open purchase order PDF. Please retry and allow popups.'));
}

function Table({ title, headers, rows }: { title: string; headers: string[]; rows: React.ReactNode[][] }) {
  const widths = title.includes('Purchase Order') ? [7, 9, 23, 33, 8, 11, 9] : title.includes('Production Orders') ? [6, 10, 29, 29, 10, 10, 6] : title.includes('Maintenance') ? [10, 20, 15, 7, 12, 12, 12, 12] : undefined;
  return <section className={styles.section}>
    <h2>{title}</h2>
    <div className={styles.tableScroll}><table>
      <colgroup>{headers.map((h, i) => <col key={h} style={widths ? { width: `${widths[i]}%` } : undefined} />)}</colgroup>
      <thead><tr>{headers.map(h => <th key={h} scope="col">{h}</th>)}</tr></thead>
      <tbody>{rows.length ? rows.map((row, i) => <tr key={i}>{row.map((cell, j) => <td key={j} style={/Quantity|Amount|Planned Qty/.test(headers[j]) ? { textAlign: 'right' } : undefined}>{cell}</td>)}</tr>) :
        <tr><td colSpan={headers.length} className={styles.empty}>No records found</td></tr>}</tbody>
    </table></div>
  </section>;
}

function Pie({ title, data, colors }: { title: string; data: ChartItem[]; colors: string[] }) {
  const entries = data;
  const total = entries.reduce((sum, item) => sum + Number(item.value), 0);
  let angle = -Math.PI / 2;
  return <section className={styles.chart} aria-label={title}>
    <h2>{title}</h2>
    <div className={styles.legend}>{entries.map((item, i) => <span key={item.name}>
      <i style={{ background: colors[i % colors.length] }} />{item.name}
    </span>)}</div>
    <svg viewBox="0 0 140 140" role="img" aria-label={total ? entries.map(item => `${item.name}: ${item.value}`).join(', ') : 'No data found'}>
      {total ? entries.map((item, i) => {
        const start = angle;
        const sweep = Number(item.value) / total * Math.PI * 2;
        angle += sweep;
        if (sweep <= 0) return null;
        const label = `${item.name}: ${item.value}`;
        const fill = colors[i % colors.length];
        if (sweep >= Math.PI * 2 - 0.00001) return <circle key={item.name} cx="70" cy="70" r="62" fill={fill}><title>{label}</title></circle>;
        const path = `M70 70 L${70 + 62 * Math.cos(start)} ${70 + 62 * Math.sin(start)} A62 62 0 ${sweep > Math.PI ? 1 : 0} 1 ${70 + 62 * Math.cos(angle)} ${70 + 62 * Math.sin(angle)} Z`;
        return <path key={item.name} d={path} fill={fill} stroke="white" strokeWidth="2"><title>{label}</title></path>;
      }) : <circle cx="70" cy="70" r="62" fill="#e5e5e5"><title>No data found</title></circle>}
    </svg>
    {!total && <p>No data found</p>}
  </section>;
}

export default function DashboardPage() {
  const { summary, charts, latestPo, latestProduction, latestMaintenance, latestInspection, latestGrn, isLoading, isError, refetchAll } = useDashboard();
  if (isLoading) return <div className={styles.loading} role="status">Loading overview…</div>;
  if (isError) return <div className={styles.loading} role="alert">Unable to load the overview. <button onClick={refetchAll}>Retry</button></div>;
  const cards = [
    { label: 'Contract', stats: summary?.contracts, color: '#345969' },
    { label: 'Purchase Order', stats: summary?.purchaseOrders, color: '#cf850c' },
    { label: 'GRN', stats: summary?.grn, color: '#3c4009' },
    { label: 'Vendors', stats: summary?.vendors, color: '#2c5796' },
    { label: 'Maintenance', stats: summary?.maintenance, color: '#298451' },
  ];
  const year = new Date().getFullYear();
  return <div className={styles.overview}>
    <div className={styles.heading}><h1>Overview</h1><span><Link href="/dashboard">⌂ Home</Link> &gt; Overview</span></div>
    <main className={styles.panel}>
      <div className={styles.cards}>{cards.map(({ label, stats, color }) => <section key={label} style={{ background: color }}>
        <h2>{label}({stats?.total ?? 0})</h2>
        <div><p>Today - {stats?.today ?? 0}</p><p>This Week - {stats?.week ?? 0}</p><p>This Month - {stats?.month ?? 0}</p></div>
      </section>)}</div>
      <div className={styles.wideRow}>
        <Table title="Last Five Purchase Order Request" headers={['PO Id', 'Generated Date', 'Vendor', 'Contact/Email', 'Quantity', 'Total Amount (INR)', 'Delivery Date']} rows={(latestPo || []).map(r => [
          <button key={r.id} onClick={() => openPdf(r.id)} aria-label={`Open purchase order PDF ${r.po_no}`}>{r.po_no}{Number(r.is_revised) > 0 ? ` R-${r.is_revised}` : ''}</button>, date(r.date), r.vendor_name,
          <React.Fragment key="contact">{text(r.contact_no)}<br />{r.email}</React.Fragment>, text(r.total_qty), amount(r.amount), date(r.delivery_date)
        ])} />
        <Pie title="Total Purchase Orders" data={charts?.purchaseOrder || []} colors={['#198754', '#2c5796', '#e52e46']} />
      </div>
      <div className={styles.productionRow}>
        <Pie title="Total Production Orders" data={charts?.production || []} colors={['#207748', '#e52e46', '#e5e5e5']} />
        <Table title="Last Five Production Orders" headers={['PO No.', 'Date Created', 'Contract Name', 'Product', 'Start Date', 'End Date', 'Planned Qty']} rows={(latestProduction || []).map(r => [
          text(r.po_no), date(r.date), r.contract_id ? <Link key={r.id} href={`/dashboard/production/viewcontractdetailspdf/${r.contract_id}`}>{r.contract_name || r.contract_id}({r.contract_number || r.contract_id})</Link> : '-',
          text(r.product_name), date(r.start_date), date(r.end_date), text(r.plan_qty)
        ])} />
      </div>
      <div className={styles.wideRow}>
        <Table title="Last Five Maintenance Request" headers={['Date', 'Machine Name', 'Type Of Breakdown', 'Time(Hrs)', 'Assigned To', 'Shift Incharge', 'Maintenance Incharge', 'Production Head']} rows={(latestMaintenance || []).map(r => [
          date(r.date), r.machine_name, r.breakdown_type, text(r.total_time), text(r.assigned_to), text(r.shift_incharge), text(r.maintenance_incharge), text(r.production_head)
        ])} />
        <Pie title="Total Maintenance" data={charts?.maintenance || []} colors={['#c8174f', '#00adb1', '#345969']} />
      </div>
      <div className={styles.bottomRow}>
        <Table title="Last Five Inspection" headers={['S.No', 'Contract Name', 'Name', 'Inspection Date']} rows={(latestInspection || []).map((r, i) => [
          i + 1, r.contract_id ? <Link key={r.id} href={`/dashboard/production/viewcontractdetailspdf/${r.contract_id}`}>{r.contract_name || r.contract_id}({r.contract_number || r.contract_id})</Link> : text(r.work_order_no), r.name, date(r.date)
        ])} />
        <Table title="Last Five GRN(Goods Received) Request" headers={['GRN No.', 'PO Id', 'G.R.N. Inward Date', 'Bill Date', 'Supplier', 'Total Amount (INR)']} rows={(latestGrn || []).map(r => [
          <Link key={r.id} href={`/dashboard/purchase/grn/view/${r.id}`}>{r.id}</Link>,
          <Link key="po" href={`/dashboard/purchase/orders?po_number=${encodeURIComponent(r.po_no)}`}>{r.po_no}</Link>, date(r.date), date(r.bill_date), r.vendor_name, amount(r.amount)
        ])} />
      </div>
    </main>
    <footer>Copyright © {year}-{year + 1} <span>All Rights Reserved by Doomshell</span></footer>
  </div>;
}
