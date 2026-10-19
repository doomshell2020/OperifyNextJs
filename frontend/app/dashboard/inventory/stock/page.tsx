'use client';
import { formatDate } from '@/utils/dateFormatter';

import {useLegacyActionAccess} from '@/components/ui/useLegacyActionAccess';
import {LegacyPageHeader} from '@/components/ui/LegacyPageHeader';
import React, { useState, useEffect, useRef } from 'react';
import { CategoryCheckboxSelect } from './CategoryCheckboxSelect';
import Link from 'next/link';
import { RefreshCw, FileSpreadsheet, AlertCircle } from 'lucide-react';
import { stockRegisterService, StockRecord, StockRegisterOptions, StockRegisterFilters } from '../../../../services/stockRegister.service';
import { ProductSearchSelect } from '../../../../components/ProductSearchSelect';
import { StockDetailModal } from './StockDetailModal';
import { DatePicker } from '../../../../components/ui/DatePicker';

const formatQty = (value: string) => Number(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
const addDays = (value: string, days: number) => {
  if (!value) return '';
  const date = new Date(`${value}T00:00:00Z`);
  date.setUTCDate(date.getUTCDate() + days);
  return date.toISOString().slice(0, 10);
};
const errorMessage = (error: unknown, fallback: string) => {
  const response = (error as { response?: { data?: { error?: { message?: string }; message?: string } } })?.response?.data;
  return response?.error?.message || response?.message || fallback;
};

export default function StockRegisterPage() {
  const canAction=useLegacyActionAccess();
  const [products, setProducts] = useState<StockRegisterOptions['products']>([]);
  const [categories, setCategories] = useState<StockRegisterOptions['categories']>([]);

  const [selectedProduct, setSelectedProduct] = useState('');
  const [selectedCategories, setSelectedCategories] = useState<string[]>([]);
  const [dateFrom, setDateFrom] = useState('');
  const [dateTo, setDateTo] = useState('');

  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [records, setRecords] = useState<StockRecord[] | null>(null);
  const [appliedFilters, setAppliedFilters] = useState<StockRegisterFilters | null>(null);
  const [exporting, setExporting] = useState(false);
  const requestId = useRef(0);
  const showProductColumns = !!appliedFilters && !appliedFilters.product_id;
  const columnCount = showProductColumns ? 8 : 6;

  const [modalOpen, setModalOpen] = useState(false);
  const [modalType, setModalType] = useState<'received' | 'dispatched'>('received');
  const [modalDate, setModalDate] = useState('');
  const [modalProductId, setModalProductId] = useState('');
  const [modalProductName, setModalProductName] = useState('');

  const openModal = (type: 'received' | 'dispatched', date: string, productId: number, productName: string) => {
    setModalType(type);
    setModalDate(date);
    setModalProductId(productId.toString());
    setModalProductName(productName);
    setModalOpen(true);
  };

  useEffect(() => {
    let active = true;
    const pendingRequests = requestId;
    stockRegisterService.getRegisterOptions().then(options => {
      if (active) { setProducts(options.products); setCategories(options.categories); }
    }).catch(error => {
      if (active) setError(errorMessage(error, 'Unable to load Stock Register filters.'));
    });
    return () => { active = false; pendingRequests.current++; };
  }, []);

  const changeProduct = (id: string) => {
    setSelectedProduct(id);
    if (!id) setDateTo(dateFrom);
  };
  const changeDateFrom = (value: string) => {
    setDateFrom(value);
    if (!selectedProduct) setDateTo(value);
    else if (dateTo && (dateTo < value || dateTo > addDays(value, 31))) setDateTo('');
  };

  const handleSearch = async () => {
    if (!dateFrom || !dateTo) {
      setError('Date From and Date To are required.');
      return;
    }
    if (new Date(dateFrom) > new Date(dateTo)) {
      setError('Date From cannot be greater than Date To.');
      return;
    }
    if (!selectedProduct && dateFrom !== dateTo) {
      setError('Only a single date is allowed for the Consolidated Stock Register.');
      return;
    }
    if (!selectedProduct && !selectedCategories.length) {
      setError('Please choose at least one category to generate the Consolidated report.');
      return;
    }
    if (selectedProduct && dateTo > addDays(dateFrom, 31)) {
      setError('The maximum allowed date range for the Product-wise Stock Register is one month.');
      return;
    }

    setError('');
    setLoading(true);
    const id = ++requestId.current;
    const filters = { date_from: dateFrom, date_to: dateTo, product_id: selectedProduct, category_ids: [...selectedCategories] };

    try {
      const data = await stockRegisterService.getStockRegister(filters);
      if (id !== requestId.current) return;
      setRecords(data || []);
      setAppliedFilters(filters);
    } catch (err: unknown) {
      if (id === requestId.current) setError(errorMessage(err, 'An error occurred while fetching data.'));
    } finally {
      if (id === requestId.current) setLoading(false);
    }
  };

  const handleReset = () => {
    requestId.current++;
    setLoading(false);
    setAppliedFilters(null);
    setSelectedProduct('');
    setSelectedCategories([]);
    setDateFrom('');
    setDateTo('');
    setError('');
    setRecords(null);
  };

  return (
    <main className="stock-register-page max-w-7xl w-full mx-auto px-6 py-8 space-y-6 select-none font-sans">

      <LegacyPageHeader title="Stock Register" breadcrumb="Goods Received Manager"/>
      {canAction('stockregister','index') && <Link href="/dashboard/inventory/stock/monthly" className="legacy-button inline-flex">Month-wise Total Stock Report</Link>}
      <div className="stock-register-box bg-white border border-slate-200">
        {/* Filters */}
        <div className="stock-register-filters">
          <div>
            <label className="block text-xs font-semibold text-slate-600 mb-1">Product</label>
            <ProductSearchSelect
              products={products}
              placeholder="Enter Item Name (Optional)"
              value={selectedProduct}
              onChange={changeProduct}
            />
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-600 mb-1">Category (Optional)</label>
            <CategoryCheckboxSelect categories={categories} value={selectedCategories} onChange={setSelectedCategories} />
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-600 mb-1">Date From <span className="text-red-500">*</span></label>
            <DatePicker
              value={dateFrom}
              dateFormat="dd-MM-yy"
              placeholder="Date From"
              onChange={(e) => changeDateFrom(e.target.value)}
              className="w-full text-sm border border-slate-300 rounded-md p-2 outline-none focus:border-cyan-500"
            />
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-600 mb-1">Date To <span className="text-red-500">*</span></label>
            <DatePicker
              value={dateTo}
              dateFormat="dd-MM-yy"
              min={dateFrom || undefined}
              max={dateFrom ? (selectedProduct ? addDays(dateFrom, 31) : dateFrom) : undefined}
              readOnly={!selectedProduct}
              placeholder="Date To"
              onChange={(e) => setDateTo(e.target.value)}
              className="w-full text-sm border border-slate-300 rounded-md p-2 outline-none focus:border-cyan-500"
            />
          </div>
        {/* Actions */}
        <div className="stock-register-actions">
          <button
            onClick={handleSearch}
            disabled={loading || exporting}
            className="flex items-center gap-1.5 px-4 py-2 bg-cyan-600 text-white hover:bg-cyan-700 rounded-lg text-sm font-semibold shadow-md transition"
          >
            Search
          </button>
          <button onClick={handleReset} className="legacy-button">Reset</button>
          {appliedFilters && (canAction('stockregister','index') || canAction('stockregister','summaryexcel')) && <button
            type="button"
            title="Export Summary Excel"
            aria-label="Export Summary Excel"
            disabled={loading || exporting}
            onClick={async () => {
              setExporting(true);
              setError('');
              try {
                await stockRegisterService.exportExcel(appliedFilters);
              } catch(e: unknown) {
                setError(errorMessage(e, 'Excel export failed. Please try again.'));
              } finally { setExporting(false); }
            }}
            className="stock-excel-export"
          >
            {exporting ? <RefreshCw aria-hidden="true" size={28} className="animate-spin" /> : <FileSpreadsheet aria-hidden="true" size={28} />}
          </button>}
        </div>
        </div>

        {error && (
          <div className="p-3 bg-red-50 border border-red-200 text-red-600 rounded-md text-sm flex items-center gap-2">
            <AlertCircle className="w-4 h-4" /> {error}
          </div>
        )}

        {/* Results Table */}
        <div className="stock-register-results overflow-x-auto border border-slate-200">
          <table className={`w-full text-left text-sm text-slate-600 ${showProductColumns ? 'min-w-[900px]' : 'min-w-[660px]'}`}>
            <thead className="bg-slate-50 text-slate-800 text-xs uppercase font-bold border-b border-slate-200">
              <tr>
                <th className="p-3">S.No</th>
                <th className="p-3">Date</th>
                {showProductColumns && <><th className="p-3">Product Name</th><th className="p-3">Category</th></>}
                <th className="p-3">Opening Stock</th>
                <th className="p-3">Received Stock</th>
                <th className="p-3">Dispatched Stock</th>
                <th className="p-3">Closing Stock</th>
              </tr>
            </thead>
            <tbody>
              {loading ? (
                <tr>
                  <td colSpan={columnCount} className="p-8 text-center text-slate-500">
                    <RefreshCw className="w-6 h-6 animate-spin mx-auto mb-2 text-cyan-600" />
                    Fetching data...
                  </td>
                </tr>
              ) : records && records.length > 0 ? (
                records.map((r, i) => (
                  <tr key={i} className="border-b border-slate-100 hover:bg-slate-50/50 transition">
                    <td className="p-3">{i + 1}</td>
                    <td className="p-3 font-semibold text-slate-700">{formatDate(r.date_range)}</td>
                    {showProductColumns && <><td className="p-3">{r.item_name}</td><td className="p-3">{r.category_name}</td></>}
                    <td className="p-3 font-medium text-cyan-600">{formatQty(r.opening_stock)}</td>
                    <td className="p-3 text-green-600">
                      {parseFloat(r.received_stock.toString()) > 0 && (canAction('stockregister','index') || canAction('stockregister','receivedstock')) ? (
                        <Link
                          href={`/dashboard/inventory/stock/received/${encodeURIComponent(r.date_range)}/${r.item_id}`}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="hover:underline text-left"
                        >
                          {formatQty(r.received_stock)}
                        </Link>
                      ) : (
                        Number(r.received_stock) > 0 ? formatQty(r.received_stock) : '0'
                      )}
                    </td>
                    <td className="p-3 text-amber-600">
                      {parseFloat(r.dispatched_stock.toString()) > 0 && canAction('stockregister','dispatchedstock') ? (
                        <button
                          onClick={() => openModal('dispatched', r.date_range, r.item_id, r.item_name)}
                          className="hover:underline text-left"
                        >
                          {formatQty(r.dispatched_stock)}
                        </button>
                      ) : (
                        Number(r.dispatched_stock) > 0 ? formatQty(r.dispatched_stock) : '0'
                      )}
                    </td>
                    <td className="p-3 font-bold text-slate-900">{formatQty(r.closing_stock)}</td>
                  </tr>
                ))
              ) : records && records.length === 0 ? (
                <tr>
                  <td colSpan={columnCount} className="p-8 text-center text-slate-500 font-medium">
                    No Record Found
                  </td>
                </tr>
              ) : (
                <tr>
                  <td colSpan={columnCount} className="p-8 text-center text-slate-400">
                    No Record Found
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>

      </div>

      {modalOpen && (
        <StockDetailModal
          type={modalType}
          date={modalDate}
          productId={modalProductId}
          productName={modalProductName}
          onClose={() => setModalOpen(false)}
        />
      )}
    </main>
  );
}
