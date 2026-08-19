'use client';

import React, { useState, useEffect } from 'react';
import { useAuth } from '../../../../contexts/AuthContext';
import { Layers, Search, Download, Loader2 } from 'lucide-react';
import { stockRegisterService, DailyStockRecord } from '../../../../services/stockRegister.service';
import toast from 'react-hot-toast';
import { DatePicker } from '../../../../components/ui/DatePicker';

export default function DailyStockPage() {
  const { user } = useAuth();
  
  const [categories, setCategories] = useState<{id: number, category_name: string}[]>([]);
  const [selectedCategories, setSelectedCategories] = useState<string[]>([]);
  const [date, setDate] = useState<string>(new Date().toISOString().split('T')[0]);
  
  const [data, setData] = useState<DailyStockRecord[]>([]);
  const [loading, setLoading] = useState(false);
  const [exporting, setExporting] = useState(false);

  useEffect(() => {
    fetchCategories();
    // Initially load for today
    fetchData(new Date().toISOString().split('T')[0], []);
  }, []);

  const fetchCategories = async () => {
    try {
      const cats = await stockRegisterService.getCategories();
      setCategories(cats);
    } catch (err: any) {
      toast.error('Failed to load categories');
    }
  };

  const fetchData = async (fetchDate: string, fetchCats: string[]) => {
    setLoading(true);
    try {
      const records = await stockRegisterService.getDailyStock({
        date: fetchDate,
        category_ids: fetchCats
      });
      setData(records);
    } catch (err: any) {
      toast.error('Failed to fetch daily stock data');
    } finally {
      setLoading(false);
    }
  };

  const handleSearch = () => {
    fetchData(date, selectedCategories);
  };

  const handleExport = async () => {
    setExporting(true);
    try {
      await stockRegisterService.exportDailyStockExcel({
        date,
        category_ids: selectedCategories
      });
      toast.success('Excel exported successfully');
    } catch (err: any) {
      toast.error('Failed to export Excel');
    } finally {
      setExporting(false);
    }
  };

  const handleCategoryChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    const options = e.target.options;
    const selected = [];
    for (let i = 0; i < options.length; i++) {
      if (options[i].selected) {
        selected.push(options[i].value);
      }
    }
    setSelectedCategories(selected);
  };

  return (
    <main className="max-w-7xl w-full mx-auto px-6 py-8 space-y-6 select-none font-sans">
      <div className="bg-white border border-slate-200/80 rounded-xl p-8 shadow-sm flex flex-col space-y-6">
        <div className="flex items-center gap-3">
          <div className="p-3 bg-cyan-50 text-cyan-600 rounded-lg">
            <Layers className="w-6 h-6" />
          </div>
          <div>
            <h1 className="text-lg font-extrabold text-slate-900 tracking-tight">
              Daily Stock Report
            </h1>
            <p className="text-xs text-slate-400 font-medium mt-0.5">
              View daily opening, received, issued, reversed, returned, and closing stock balances.
            </p>
          </div>
        </div>

        <div className="h-px bg-slate-100"></div>

        {/* Filters */}
        <div className="flex flex-col md:flex-row gap-4">
          <div className="flex flex-col gap-1.5 w-full md:w-1/3">
            <label className="text-xs font-semibold text-slate-600 uppercase tracking-wide">Date</label>
            <DatePicker  
              value={date}
              onChange={(e) => setDate(e.target.value)}
              className="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition-colors"
            />
          </div>
          
          <div className="flex flex-col gap-1.5 w-full md:w-1/3">
            <label className="text-xs font-semibold text-slate-600 uppercase tracking-wide">Categories</label>
            <select 
              multiple
              value={selectedCategories}
              onChange={handleCategoryChange}
              className="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition-colors h-[100px]"
            >
              <option value="All">All Categories</option>
              {categories.map(c => (
                <option key={c.id} value={String(c.id)}>{c.category_name}</option>
              ))}
            </select>
          </div>

          <div className="flex items-end gap-2 w-full md:w-1/3 pb-1">
            <button 
              onClick={handleSearch}
              disabled={loading}
              className="flex items-center gap-2 px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-sm font-semibold transition disabled:opacity-50"
            >
              {loading ? <Loader2 className="w-4 h-4 animate-spin" /> : <Search className="w-4 h-4" />}
              Search
            </button>
            <button 
              onClick={handleExport}
              disabled={exporting}
              className="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold transition disabled:opacity-50"
            >
              {exporting ? <Loader2 className="w-4 h-4 animate-spin" /> : <Download className="w-4 h-4" />}
              Export Excel
            </button>
          </div>
        </div>

        {/* Table */}
        <div className="border border-slate-200 rounded-lg overflow-x-auto">
          <table className="w-full text-left text-sm text-slate-600">
            <thead className="bg-slate-50 text-slate-800 text-xs uppercase font-semibold border-b border-slate-200">
              <tr>
                <th className="px-3 py-3 w-12 text-center">S.No</th>
                <th className="px-3 py-3 min-w-[220px]">Item Name</th>
                <th className="px-3 py-3 min-w-[120px]">Category</th>
                <th className="px-3 py-3 text-right w-24">Opening Stock</th>
                <th className="px-3 py-3 text-right w-24">Received Stock</th>
                <th className="px-3 py-3 text-right w-24">Issued Stock</th>
                <th className="px-3 py-3 text-right w-24">Reverse Stock</th>
                <th className="px-3 py-3 text-right w-24">Return Stock</th>
                <th className="px-3 py-3 text-right w-24 text-cyan-700">Closing Stock</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {data.length === 0 ? (
                <tr>
                  <td colSpan={9} className="px-4 py-8 text-center text-slate-500 italic">
                    {loading ? 'Loading...' : 'No stock records found for the selected criteria.'}
                  </td>
                </tr>
              ) : (
                data.map((row, index) => (
                  <tr key={row.item_id} className="hover:bg-slate-50/50 transition-colors">
                    <td className="px-3 py-3 text-center align-top">{index + 1}</td>
                    <td className="px-3 py-3 font-medium text-slate-800 break-words align-top">{row.item_name}</td>
                    <td className="px-3 py-3 break-words align-top">{row.category_name}</td>
                    <td className="px-3 py-3 text-right align-top">{row.opening_stock}</td>
                    <td className="px-3 py-3 text-right text-emerald-600 align-top">{row.received_stock}</td>
                    <td className="px-3 py-3 text-right text-rose-600 align-top">{row.issued_stock}</td>
                    <td className="px-3 py-3 text-right text-amber-600 align-top">{row.reverse_stock}</td>
                    <td className="px-3 py-3 text-right text-emerald-600 align-top">{row.return_stock}</td>
                    <td className="px-3 py-3 text-right font-bold text-cyan-700 align-top">{row.closing_stock}</td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>
    </main>
  );
}
