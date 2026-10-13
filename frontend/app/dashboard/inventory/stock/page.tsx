'use client';

import {useLegacyActionAccess} from '@/components/ui/useLegacyActionAccess';
import {LegacyPageHeader} from '@/components/ui/LegacyPageHeader';
import React, { useState, useEffect } from 'react';
import { CategoryCheckboxSelect } from './CategoryCheckboxSelect';
import Link from 'next/link';
import { useAuth } from '../../../../contexts/AuthContext';
import { RefreshCw, FileSpreadsheet, AlertCircle } from 'lucide-react';
import { settingsService, Product, Category } from '../../../../services/settings.service';
import { stockRegisterService, StockRecord } from '../../../../services/stockRegister.service';
import { ProductSearchSelect } from '../../../../components/ProductSearchSelect';
import { StockDetailModal } from './StockDetailModal';
import { DatePicker } from '../../../../components/ui/DatePicker';

export default function StockRegisterPage() {
  const canAction=useLegacyActionAccess();
  const { user } = useAuth();

  const [products, setProducts] = useState<Product[]>([]);
  const [categories, setCategories] = useState<Category[]>([]);

  const [selectedProduct, setSelectedProduct] = useState('');
  const [selectedCategories, setSelectedCategories] = useState<string[]>([]);
  const [dateFrom, setDateFrom] = useState('');
  const [dateTo, setDateTo] = useState('');

  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [records, setRecords] = useState<StockRecord[] | null>(null);

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
    fetchProducts();
    fetchCategories();
  }, []);

  const fetchProducts = async () => {
    try {
      const data = await settingsService.getProducts();
      setProducts(data || []);
    } catch (e) {
      console.error('Error fetching products', e);
    }
  };

  const fetchCategories = async () => {
    try {
      const data = await settingsService.getCategories();
      setCategories(data || []);
    } catch (e) {
      console.error('Error fetching categories', e);
    }
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

    setError('');
    setLoading(true);

    try {
      const data = await stockRegisterService.getStockRegister({
        date_from: dateFrom,
        date_to: dateTo,
        product_id: selectedProduct,
        category_ids: selectedCategories
      });
      setRecords(data || []);
    } catch (err: any) {
      setError(err?.response?.data?.message || 'An error occurred while fetching data.');
    } finally {
      setLoading(false);
    }
  };

  const handleReset = () => {
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
              onChange={setSelectedProduct}
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
              placeholder="Date From"
              onChange={(e) => setDateFrom(e.target.value)}
              className="w-full text-sm border border-slate-300 rounded-md p-2 outline-none focus:border-cyan-500"
            />
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-600 mb-1">Date To <span className="text-red-500">*</span></label>
            <DatePicker
              value={dateTo}
              placeholder="Date To"
              onChange={(e) => setDateTo(e.target.value)}
              className="w-full text-sm border border-slate-300 rounded-md p-2 outline-none focus:border-cyan-500"
            />
          </div>
        {/* Actions */}
        <div className="stock-register-actions">
          <button
            onClick={handleSearch}
            className="flex items-center gap-1.5 px-4 py-2 bg-cyan-600 text-white hover:bg-cyan-700 rounded-lg text-sm font-semibold shadow-md transition"
          >
            Search
          </button>
          <button onClick={handleReset} className="legacy-button">Reset</button>
          {(canAction('stockregister','index') || canAction('stockregister','summaryexcel')) && <button
            type="button"
            title="Export Summary Excel"
            aria-label="Export Summary Excel"
            onClick={async () => {
              if (!dateFrom || !dateTo) {
                setError('Date From and Date To are required for export.');
                return;
              }
              try {
                await stockRegisterService.exportExcel({
                  date_from: dateFrom,
                  date_to: dateTo,
                  product_id: selectedProduct,
                  category_ids: selectedCategories
                });
              } catch(e) {
                setError('Excel export failed. Please check your Stock Register access or contact your administrator.');
              }
            }}
            className="stock-excel-export"
          >
            <FileSpreadsheet aria-hidden="true" size={28} />
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
          <table className="w-full text-left text-sm text-slate-600">
            <thead className="bg-slate-50 text-slate-800 text-xs uppercase font-bold border-b border-slate-200">
              <tr>
                <th className="p-3">S.No</th>
                <th className="p-3">Date</th>
                <th className="p-3">Opening Stock</th>
                <th className="p-3">Received Stock</th>
                <th className="p-3">Dispatched Stock</th>
                <th className="p-3">Closing Stock</th>
              </tr>
            </thead>
            <tbody>
              {loading ? (
                <tr>
                  <td colSpan={6} className="p-8 text-center text-slate-500">
                    <RefreshCw className="w-6 h-6 animate-spin mx-auto mb-2 text-cyan-600" />
                    Fetching data...
                  </td>
                </tr>
              ) : records && records.length > 0 ? (
                records.map((r, i) => (
                  <tr key={i} className="border-b border-slate-100 hover:bg-slate-50/50 transition">
                    <td className="p-3">{i + 1}</td>
                    <td className="p-3 font-semibold text-slate-700">{r.date_range}</td>
                    <td className="p-3 font-medium text-cyan-600">{r.opening_stock}</td>
                    <td className="p-3 text-green-600">
                      {parseFloat(r.received_stock.toString()) > 0 && (canAction('stockregister','index') || canAction('stockregister','receivedstock')) ? (
                        <Link
                          href={`/dashboard/inventory/stock/received/${encodeURIComponent(r.date_range)}/${r.item_id}`}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="hover:underline text-left"
                        >
                          {r.received_stock}
                        </Link>
                      ) : (
                        r.received_stock
                      )}
                    </td>
                    <td className="p-3 text-amber-600">
                      {parseFloat(r.dispatched_stock.toString()) > 0 && canAction('stockregister','dispatchedstock') ? (
                        <button
                          onClick={() => openModal('dispatched', r.date_range, r.item_id, r.item_name)}
                          className="hover:underline text-left"
                        >
                          {r.dispatched_stock}
                        </button>
                      ) : (
                        r.dispatched_stock
                      )}
                    </td>
                    <td className="p-3 font-bold text-slate-900">{r.closing_stock}</td>
                  </tr>
                ))
              ) : records && records.length === 0 ? (
                <tr>
                  <td colSpan={6} className="p-8 text-center text-slate-500 font-medium">
                    No Record Found
                  </td>
                </tr>
              ) : (
                <tr>
                  <td colSpan={6} className="p-8 text-center text-slate-400">
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
