'use client';

import React, { useState, useRef, useEffect, useCallback } from 'react';
import apiClient from '../../../../services/apiClient';
import { useRouter } from 'next/navigation';
import { Box, Save, ArrowLeft, Plus, Trash2, Loader, Search } from 'lucide-react';
import toast from 'react-hot-toast';
import { useJcAccess, errorMessage, localDate } from '@/components/jobChallan/useJcAccess';
import SubcontractorModal from '@/components/jobChallan/SubcontractorModal';

// ─── Types ──────────────────────────────────────────────────────────────────
interface ItemRow {
  item_id: string;
  item_name: string;
  in_hand_qty: string;
  quantity: string;
  hsn_code: string;
  rate: string;
  tax_rate: string;
  tax_amount: string;
  amount: string;
}

const emptyItem = (): ItemRow => ({
  item_id: '', item_name: '', in_hand_qty: '0',
  quantity: '', hsn_code: '', rate: '',
  tax_rate: '', tax_amount: '0.00', amount: '0.00'
});

// ─── Item Search Dropdown ────────────────────────────────────────────────────
function ItemSearch({
  value, onSelect, processType, placeholder
}: {
  value: string;
  onSelect: (item: any) => void;
  processType: string;
  placeholder?: string;
}) {
  const [search, setSearch] = useState(value);
  const [results, setResults] = useState<any[]>([]);
  const [open, setOpen] = useState(false);
  const [loading, setLoading] = useState(false);
  const timer = useRef<any>(null);
  const wrapRef = useRef<HTMLDivElement>(null);

  useEffect(() => { setSearch(value); }, [value]);

  useEffect(() => {
    const handler = (e: MouseEvent) => {
      if (wrapRef.current && !wrapRef.current.contains(e.target as Node)) setOpen(false);
    };
    document.addEventListener('mousedown', handler);
    return () => document.removeEventListener('mousedown', handler);
  }, []);

  const doSearch = useCallback(async (q: string) => {
    if (!q.trim()) { setResults([]); setOpen(false); return; }
    setLoading(true);
    try {
      const res = await apiClient.get('/job-challan/search-items', {
        params: { search: q, process_type: processType }
      });
      setResults(res.data.data || []);
      setOpen(true);
    } catch { setResults([]); }
    setLoading(false);
  }, [processType]);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const v = e.target.value;
    setSearch(v);
    clearTimeout(timer.current);
    timer.current = setTimeout(() => doSearch(v), 250);
  };

  const handleSelect = (item: any) => {
    setSearch(item.item_name);
    setOpen(false);
    onSelect(item);
  };

  return (
    <div ref={wrapRef} className="relative">
      <div className="relative">
        <input
          type="text"
          value={search}
          onChange={handleChange}
          onFocus={() => search && setOpen(true)}
          placeholder={placeholder || 'Search item...'}
          className="w-full border border-slate-200 rounded-md p-2 pr-8 text-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none transition"
          autoComplete="off"
        />
        {loading
          ? <Loader className="absolute right-2 top-2.5 w-4 h-4 animate-spin text-slate-400" />
          : <Search className="absolute right-2 top-2.5 w-4 h-4 text-slate-300" />
        }
      </div>
      {open && (
        <ul className="absolute z-50 w-full bg-white border border-slate-200 rounded-md shadow-lg max-h-48 overflow-y-auto mt-0.5">
          {results.length > 0
            ? results.map((item: any) => (
              <li key={item.id}
                onClick={() => handleSelect(item)}
                className="px-3 py-2 text-sm cursor-pointer hover:bg-cyan-50 hover:text-cyan-700 border-b border-slate-50 last:border-0">
                {item.item_name}
              </li>
            ))
            : <li className="px-3 py-2 text-sm text-slate-400 italic">No items found</li>
          }
        </ul>
      )}
    </div>
  );
}

// ─── Main Component ──────────────────────────────────────────────────────────
export default function AddJobChallan() {
  const router = useRouter();
  const { can, loading: permissionsLoading } = useJcAccess();
  const [loading, setLoading] = useState(false);

  // Header fields
  const [challanNo, setChallanNo] = useState('');
  const [jcDate, setJcDate] = useState(localDate());
  const [vendorId, setVendorId] = useState('');
  const [gstNo, setGstNo] = useState('');
  const [estimatedValues, setEstimatedValues] = useState('');
  const [expectedDays, setExpectedDays] = useState('');
  const [workDescription, setWorkDescription] = useState('');
  const [processingType, setProcessingType] = useState<'Manufacturing' | 'In Progress'>('Manufacturing');

  // Line items
  const [items, setItems] = useState<ItemRow[]>([emptyItem()]);

  // Semi-finished product (In Progress only)
  const [sfpId, setSfpId] = useState('');
  const [sfpName, setSfpName] = useState('');
  const [sfpQty, setSfpQty] = useState('');
  const [sfpInHand, setSfpInHand] = useState('');
  const [sfpRate, setSfpRate] = useState('');
  const [sfpTax, setSfpTax] = useState('');
  const [sfpHsn, setSfpHsn] = useState('');

  // Dropdown data
  const [vendors, setVendors] = useState<any[]>([]);
  const [taxMaster, setTaxMaster] = useState<any[]>([]);

  // Load vendors and tax master on mount
  useEffect(() => {
    apiClient.get('/job-challan/vendors').then(r => setVendors(r.data.data || [])).catch(() => {});
    apiClient.get('/job-challan/tax-master').then(r => setTaxMaster(r.data.data || [])).catch(() => {});
  }, []);

  // Fetch GST when vendor changes
  useEffect(() => {
    if (!vendorId) { setGstNo(''); return; }
    apiClient.get('/job-challan/vendor-gst', { params: { vendor_id: vendorId } })
      .then(r => setGstNo(r.data.data?.gst_no || ''))
      .catch(() => {});
  }, [vendorId]);

  // Fetch in-hand stock + hsn + tax for a given item row
  const fetchItemStock = async (item_id: string, rowIdx: number, isSfp = false) => {
    if (!item_id) return;
    try {
      const res = await apiClient.get('/job-challan/item-stock', { params: { item_id } });
      const d = res.data.data;
      if (isSfp) {
        setSfpInHand(String(d.inhand_qty));
        setSfpHsn(d.hsn_code || '');
        setSfpTax(String(d.tax_rate || ''));
      } else {
        setItems(prev => {
          const upd = [...prev];
          upd[rowIdx] = {
            ...upd[rowIdx],
            in_hand_qty: String(d.inhand_qty),
            hsn_code: d.hsn_code || '',
            tax_rate: String(d.tax_rate || '')
          };
          return recalc(upd, rowIdx);
        });
      }
    } catch {}
  };

  // Auto-calculate tax and total for a row
  const recalc = (rows: ItemRow[], idx: number): ItemRow[] => {
    const row = rows[idx];
    const qty  = parseFloat(row.quantity) || 0;
    const rate = parseFloat(row.rate) || 0;
    const tax  = parseFloat(row.tax_rate) || 0;
    const amt  = qty * rate;
    const taxAmt = (amt * tax) / 100;
    rows[idx] = {
      ...row,
      tax_amount: taxAmt.toFixed(2),
      amount: (amt + taxAmt).toFixed(2)
    };
    return rows;
  };

  const updateItem = (idx: number, field: keyof ItemRow, val: string) => {
    setItems(prev => {
      const upd = [...prev];
      upd[idx] = { ...upd[idx], [field]: val };
      if (['quantity', 'rate', 'tax_rate'].includes(field)) {
        return recalc(upd, idx);
      }
      return upd;
    });
  };

  const addRow = () => setItems(prev => [...prev, emptyItem()]);

  const removeRow = (idx: number) => {
    setItems(prev => prev.length > 1 ? prev.filter((_, i) => i !== idx) : prev);
  };

  const handleItemSelect = (item: any, idx: number) => {
    setItems(prev => {
      const upd = [...prev];
      upd[idx] = { ...upd[idx], item_id: String(item.id), item_name: item.item_name };
      return upd;
    });
    fetchItemStock(String(item.id), idx, false);
  };

  const handleSfpSelect = (item: any) => {
    setSfpId(String(item.id));
    setSfpName(item.item_name);
    fetchItemStock(String(item.id), -1, true);
  };

  // Grand totals
  const sfpBase = processingType === 'In Progress' ? (Number(sfpQty) || 0) * (Number(sfpRate) || 0) : 0;
  const sfpTaxAmount = sfpBase * (Number(sfpTax) || 0) / 100;
  const grandTotal    = items.reduce((s, r) => s + (parseFloat(r.amount) || 0), 0) + sfpBase + sfpTaxAmount;
  const grandTaxTotal = items.reduce((s, r) => s + (parseFloat(r.tax_amount) || 0), 0) + sfpTaxAmount;
  const baseTotal     = grandTotal - grandTaxTotal;

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (loading) return;

    // Frontend guard: qty > in-hand
    for (const item of items) {
      if (!item.item_id) continue;
      const qty = parseFloat(item.quantity);
      const inh = parseFloat(item.in_hand_qty);
      if (qty > inh) {
        toast.error(`Quantity for "${item.item_name}" exceeds available stock (${inh})`);
        return;
      }
    }

    setLoading(true);
    try {
      await apiClient.post('/job-challan', {
        challan_no:               challanNo,
        jc_date:                  jcDate,
        sub_contractors_id:       vendorId,
        processing_type:          processingType,
        estimated_values:         estimatedValues,
        expected_days:            expectedDays,
        work_description:         workDescription,
        items: items
          .filter(i => i.item_id && parseFloat(i.quantity) > 0)
          .map(i => ({
            item_id:    i.item_id,
            quantity:   i.quantity,
            hsn_code:   i.hsn_code,
            rate:       i.rate,
            tax_rate:   i.tax_rate,
            tax_amount: i.tax_amount,
            amount:     i.amount,
          })),
        semi_finished_item_id:    sfpId || undefined,
        semi_finished_quantity:   sfpQty || undefined,
        semi_finished_rate:       sfpRate,
        semi_finished_tax_rate:   sfpTax,
        semi_finished_hsn_code:   sfpHsn,
      });
      toast.success('Job Challan created successfully!');
      router.push('/dashboard/jc-challan');
    } catch (err: any) {
      toast.error(errorMessage(err));
      setLoading(false);
    }
  };

  const labelCls = 'block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1';
  const inputCls = 'w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none transition';

  if (permissionsLoading) return <p className="p-6">Loading permissions...</p>;
  if (!can('jobchallan','add')) return <p className="p-6" role="alert">You do not have permission to add a JC.</p>;

  return (
    <main className="max-w-7xl w-full mx-auto px-6 py-8 space-y-6 font-sans">
      {/* Header */}
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
            <Box className="text-cyan-600" /> Add Job Challan
          </h1>
          <p className="text-sm text-slate-500 font-medium mt-1">Issue raw materials or goods to sub-contractors</p>
        </div>
        <button onClick={() => router.back()} className="flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-md text-sm font-medium transition">
          <ArrowLeft className="w-4 h-4" /> Back to List
        </button>
      </div>

      <form onSubmit={handleSubmit} className="space-y-6">
        {/* ── Basic Details ─────────────────────────────── */}
        <div className="bg-white border border-slate-200 p-6 rounded-xl shadow-sm">
          <h3 className="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">Basic Details</h3>
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            <div>
              <label className={labelCls}>JC No. <span className="text-red-500">*</span></label>
              <input required type="text" className={inputCls} placeholder="Numeric only"
                value={challanNo}
                onChange={e => setChallanNo(e.target.value.replace(/[^0-9]/g, ''))}
              />
            </div>
            <div>
              <label className={labelCls}>Date</label>
              <input required type="date" className={inputCls} value={jcDate} onChange={e => setJcDate(e.target.value)} />
            </div>
            <div>
              <label className={labelCls}>Sub Contractor</label>
              <select required className={inputCls} value={vendorId} onChange={e => setVendorId(e.target.value)}>
                <option value="">Select Sub Contractor</option>
                {vendors.map(v => <option key={v.id} value={v.id}>{v.name}</option>)}
              </select>
              <SubcontractorModal onAdded={v => { setVendors(prev => [...prev, v]); setVendorId(String(v.id)); }} />
            </div>
            <div>
              <label className={labelCls}>GST No.</label>
              <input type="text" className={inputCls + ' bg-slate-50'} value={gstNo} readOnly placeholder="Auto-filled from vendor" />
            </div>
            <div>
              <label className={labelCls}>Estimated Value</label>
              <input type="number" min="0" step="0.01" className={inputCls} value={estimatedValues}
                onChange={e => setEstimatedValues(e.target.value)} placeholder="0.00" />
            </div>
            <div>
              <label className={labelCls}>Expected Days</label>
              <input type="number" min="0" className={inputCls} value={expectedDays}
                onChange={e => setExpectedDays(e.target.value)} placeholder="0" />
            </div>
          </div>
          <div className="mt-4">
            <label className={labelCls}>Work Description</label>
            <textarea rows={2} className={inputCls} value={workDescription}
              onChange={e => setWorkDescription(e.target.value)} placeholder="Optional" />
          </div>

          {/* Process Type */}
          <div className="mt-4">
            <label className={labelCls}>Process Type</label>
            <div className="flex gap-6 mt-1">
              {(['Manufacturing', 'In Progress'] as const).map(pt => (
                <label key={pt} className="flex items-center gap-2 cursor-pointer text-sm font-medium text-slate-700">
                  <input type="radio" name="processing_type" value={pt}
                    checked={processingType === pt}
                    onChange={() => setProcessingType(pt)}
                    className="w-4 h-4 accent-cyan-600"
                  />
                  {pt}
                </label>
              ))}
            </div>
          </div>
        </div>

        {/* ── Raw Material Items ─────────────────────────── */}
        <div className="bg-white border border-slate-200 p-6 rounded-xl shadow-sm">
          <div className="flex justify-between items-center border-b pb-3 mb-4">
            <h3 className="text-sm font-bold text-slate-800 uppercase tracking-wider">Item Details</h3>
            <button type="button" onClick={addRow}
              className="flex items-center gap-1 text-cyan-600 hover:text-cyan-700 text-sm font-semibold px-3 py-1.5 border border-cyan-200 hover:border-cyan-400 rounded-md transition">
              <Plus className="w-4 h-4" /> Add Item
            </button>
          </div>

          <div className="overflow-x-auto">
            <table className="w-full text-sm border-collapse" style={{ minWidth: 900 }}>
              <thead>
                <tr className="bg-slate-800 text-white text-xs uppercase tracking-wider">
                  <th className="p-3 text-left font-semibold" style={{ minWidth: 220 }}>Item Name</th>
                  <th className="p-3 text-center font-semibold w-24">In Hand Qty</th>
                  <th className="p-3 text-center font-semibold w-24">Quantity</th>
                  <th className="p-3 text-center font-semibold w-24">HSN/SAC</th>
                  <th className="p-3 text-center font-semibold w-24">Rate</th>
                  <th className="p-3 text-center font-semibold w-24">Tax %</th>
                  <th className="p-3 text-center font-semibold w-28">Tax Amount</th>
                  <th className="p-3 text-center font-semibold w-28">Total Amount</th>
                  <th className="p-3 w-10"></th>
                </tr>
              </thead>
              <tbody>
                {items.map((row, idx) => (
                  <tr key={idx} className="border-b border-slate-100 hover:bg-slate-50">
                    <td className="p-2">
                      <ItemSearch
                        value={row.item_name}
                        processType="Manufacturing"
                        onSelect={item => handleItemSelect(item, idx)}
                        placeholder="Search item..."
                      />
                    </td>
                    <td className="p-2">
                      <input type="text" readOnly value={row.in_hand_qty}
                        className="w-full border border-slate-200 rounded-md p-2 text-sm text-center bg-slate-50 outline-none" />
                    </td>
                    <td className="p-2">
                      <input type="number" min="0.01" step="0.01" value={row.quantity}
                        onChange={e => updateItem(idx, 'quantity', e.target.value)}
                        className="w-full border border-slate-200 rounded-md p-2 text-sm text-center focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none"
                      />
                    </td>
                    <td className="p-2">
                      <input type="text" value={row.hsn_code}
                        onChange={e => updateItem(idx, 'hsn_code', e.target.value)}
                        className="w-full border border-slate-200 rounded-md p-2 text-sm text-center focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none"
                      />
                    </td>
                    <td className="p-2">
                      <input type="number" min="0" step="0.01" value={row.rate}
                        onChange={e => updateItem(idx, 'rate', e.target.value)}
                        className="w-full border border-slate-200 rounded-md p-2 text-sm text-center focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none"
                      />
                    </td>
                    <td className="p-2">
                      <select value={row.tax_rate} onChange={e => updateItem(idx, 'tax_rate', e.target.value)}
                        className="w-full border border-slate-200 rounded-md p-2 text-sm focus:border-cyan-500 outline-none">
                        <option value="">-- Tax --</option>
                        {taxMaster.map((t: any) => <option key={t.id} value={String(t.tax)}>{t.tax}%</option>)}
                      </select>
                    </td>
                    <td className="p-2">
                      <input type="text" readOnly value={row.tax_amount}
                        className="w-full border border-slate-200 rounded-md p-2 text-sm text-center bg-slate-50 outline-none" />
                    </td>
                    <td className="p-2">
                      <input type="text" readOnly value={row.amount}
                        className="w-full border border-slate-200 rounded-md p-2 text-sm text-center bg-slate-50 font-semibold outline-none" />
                    </td>
                    <td className="p-2 text-center">
                      {items.length > 1 && (
                        <button type="button" onClick={() => removeRow(idx)}
                          className="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded transition">
                          <Trash2 className="w-4 h-4" />
                        </button>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
              <tfoot>
                <tr className="bg-slate-50 border-t-2 border-slate-300">
                  <td colSpan={6} className="p-3 text-right text-xs font-bold text-slate-500 uppercase">Totals</td>
                  <td className="p-3 text-center text-sm font-bold text-slate-700">{grandTaxTotal.toFixed(2)}</td>
                  <td className="p-3 text-center text-sm font-bold text-cyan-700">{grandTotal.toFixed(2)}</td>
                  <td></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        {/* ── Semi-Finished Product (In Progress only) ─── */}
        {processingType === 'In Progress' && (
          <div className="bg-white border border-pink-200 p-6 rounded-xl shadow-sm">
            <h3 className="text-sm font-bold text-pink-700 uppercase tracking-wider mb-4 border-b border-pink-100 pb-2">
              Semi-Finished Product <span className="text-red-500">*</span>
            </h3>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
              <div>
                <label className={labelCls}>Product Name <span className="text-red-500">*</span></label>
                <ItemSearch
                  value={sfpName}
                  processType="Semi-Finished"
                  onSelect={handleSfpSelect}
                  placeholder="Search semi-finished product..."
                />
                <input type="hidden" value={sfpId} />
              </div>
              <div>
                <label className={labelCls}>Quantity <span className="text-red-500">*</span></label>
                <input type="number" min="0.01" step="0.01" className={inputCls}
                  value={sfpQty} onChange={e => setSfpQty(e.target.value)} placeholder="Enter quantity" />
              </div>
              <div>
                <label className={labelCls}>In Hand Qty</label>
                <input type="text" readOnly className={inputCls + ' bg-slate-50'} value={sfpInHand} placeholder="Fetched from server" />
              </div>
              <div><label className={labelCls}>HSN/SAC</label><input className={inputCls} value={sfpHsn} onChange={e => setSfpHsn(e.target.value)} /></div>
              <div><label className={labelCls}>Rate</label><input type="number" min="0" step="0.01" className={inputCls} value={sfpRate} onChange={e => setSfpRate(e.target.value)} /></div>
              <div><label className={labelCls}>Tax %</label><select className={inputCls} value={sfpTax} onChange={e => setSfpTax(e.target.value)}><option value="">-- Tax --</option>{taxMaster.map(t => <option key={t.id} value={String(t.tax)}>{t.tax}%</option>)}</select></div>
              <div><label className={labelCls}>Tax Amount</label><input readOnly className={inputCls} value={sfpTaxAmount.toFixed(2)} /></div>
              <div><label className={labelCls}>Total Amount</label><input readOnly className={inputCls} value={(sfpBase + sfpTaxAmount).toFixed(2)} /></div>
            </div>
          </div>
        )}

        {/* ── Buttons ───────────────────────────────────── */}
        <div className="flex items-center justify-end gap-3 pt-2">
          <button type="button" onClick={() => router.push('/dashboard/jc-challan')}
            className="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-semibold transition">
            Reset
          </button>
          <button type="submit" disabled={loading}
            className="flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-2.5 rounded-lg font-semibold shadow-md transition disabled:opacity-50">
            {loading ? <Loader className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
            {loading ? 'Saving...' : 'Save Job Challan'}
          </button>
        </div>
      </form>
    </main>
  );
}
