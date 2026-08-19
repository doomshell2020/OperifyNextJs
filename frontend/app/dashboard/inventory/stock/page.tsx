'use client';

import React, { useState, useEffect } from 'react';
import { useAuth } from '../../../../contexts/AuthContext';
import { RefreshCw, Search, List, AlertCircle } from 'lucide-react';
import { settingsService, Product, Category } from '../../../../services/settings.service';
import { stockRegisterService, StockRecord } from '../../../../services/stockRegister.service';
import { ProductSearchSelect } from '../../../../components/ProductSearchSelect';
import { StockDetailModal } from './StockDetailModal';
import { DatePicker } from '../../../../components/ui/DatePicker';

export default function StockRegisterPage() {
  const { user } = useAuth();
  
  const [products, setProducts] = useState<Product[]>([]);
  const [categories, setCategories] = useState<Category[]>([]);
  
  const [selectedProduct, setSelectedProduct] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('');
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
        category_id: selectedCategory
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
    setSelectedCategory('');
    setDateFrom('');
    setDateTo('');
    setError('');
    setRecords(null);
  };

  return (
    <main className="max-w-7xl w-full mx-auto px-6 py-8 space-y-6 select-none font-sans">
      
      {/* Breadcrumb */}
      <div className="flex items-center text-sm text-slate-500 space-x-2">
        <span className="cursor-pointer hover:text-cyan-600">Home</span>
        <span>&gt;</span>
        <span className="font-semibold text-slate-700">Goods Received Manager</span>
      </div>

      <div className="bg-white border border-slate-200/80 rounded-xl p-8 shadow-sm flex flex-col space-y-6">
        <div className="flex items-center gap-3">
          <div className="p-3 bg-cyan-50 text-cyan-600 rounded-lg">
            <List className="w-6 h-6" />
          </div>
          <div>
            <h1 className="text-lg font-extrabold text-slate-900 tracking-tight">
              Stock Register
            </h1>
          </div>
        </div>

        <div className="h-px bg-slate-100"></div>

        {/* Filters */}
        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label className="block text-xs font-semibold text-slate-600 mb-1">Product</label>
            <ProductSearchSelect 
              products={products}
              value={selectedProduct}
              onChange={setSelectedProduct}
            />
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-600 mb-1">Category</label>
            <select
              value={selectedCategory}
              onChange={(e) => setSelectedCategory(e.target.value)}
              className="w-full text-sm border border-slate-300 rounded-md p-2 outline-none focus:border-cyan-500"
            >
              <option value="">Select Category</option>
              {categories.map(c => (
                <option key={c.id} value={c.id}>{c.category_name}</option>
              ))}
            </select>
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-600 mb-1">Date From <span className="text-red-500">*</span></label>
            <DatePicker  
              value={dateFrom}
              onChange={(e) => setDateFrom(e.target.value)}
              className="w-full text-sm border border-slate-300 rounded-md p-2 outline-none focus:border-cyan-500"
            />
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-600 mb-1">Date To <span className="text-red-500">*</span></label>
            <DatePicker  
              value={dateTo}
              onChange={(e) => setDateTo(e.target.value)}
              className="w-full text-sm border border-slate-300 rounded-md p-2 outline-none focus:border-cyan-500"
            />
          </div>
        </div>
        
        {/* Actions */}
        <div className="flex justify-end gap-3 mt-4">
          <button 
            onClick={handleReset}
            className="flex items-center gap-1.5 px-4 py-2 bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-lg text-sm font-semibold transition"
          >
            <RefreshCw className="w-4 h-4" /> Reset
          </button>
          <button 
            onClick={handleSearch}
            className="flex items-center gap-1.5 px-4 py-2 bg-cyan-600 text-white hover:bg-cyan-700 rounded-lg text-sm font-semibold shadow-md transition"
          >
            <Search className="w-4 h-4" /> Search
          </button>
          <button 
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
                  category_id: selectedCategory
                });
              } catch(e) {
                setError('Failed to export Excel');
              }
            }}
            className="flex items-center gap-1.5 px-4 py-2 bg-green-600 text-white hover:bg-green-700 rounded-lg text-sm font-semibold shadow-md transition"
          >
            <List className="w-4 h-4" /> Export Summary Excel
          </button>
        </div>

        {error && (
          <div className="p-3 bg-red-50 border border-red-200 text-red-600 rounded-md text-sm flex items-center gap-2">
            <AlertCircle className="w-4 h-4" /> {error}
          </div>
        )}
        
        {/* Results Table */}
        <div className="mt-8 overflow-x-auto rounded-lg border border-slate-200">
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
                      {parseFloat(r.received_stock.toString()) > 0 ? (
                        <button 
                          onClick={() => openModal('received', r.date_range, r.item_id, r.item_name)}
                          className="hover:underline text-left"
                        >
                          {r.received_stock}
                        </button>
                      ) : (
                        r.received_stock
                      )}
                    </td>
                    <td className="p-3 text-amber-600">
                      {parseFloat(r.dispatched_stock.toString()) > 0 ? (
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
                    Select a date range and click Search to view stock register.
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
