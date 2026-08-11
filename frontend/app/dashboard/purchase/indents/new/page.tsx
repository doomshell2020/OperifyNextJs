'use client';

import React, { useState, useEffect, useRef, useCallback } from 'react';
import { useRouter } from 'next/navigation';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import indentService, {
  ItemSearchResult,
  TempIndentItem,
} from '../../../../../services/indent.service';
import {
  ListTodo,
  Search,
  Plus,
  Trash2,
  CheckCircle,
  Eye,
  ArrowLeft,
  Package,
  AlertCircle,
  Loader2,
} from 'lucide-react';
import Link from 'next/link';
import { formatAmt } from '@/utils/formatters';

// ─── Item Autocomplete ────────────────────────────────────────────────────────

function ItemAutocomplete({
  value,
  onChange,
  onSelect,
  dbPool,
}: {
  value: string;
  onChange: (v: string) => void;
  onSelect: (item: ItemSearchResult) => void;
  dbPool?: unknown;
}) {
  const [results, setResults] = useState<ItemSearchResult[]>([]);
  const [open, setOpen] = useState(false);
  const [loading, setLoading] = useState(false);
  const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);
  const wrapperRef = useRef<HTMLDivElement>(null);

  // Close dropdown when clicking outside
  useEffect(() => {
    function handleOutside(e: MouseEvent) {
      if (wrapperRef.current && !wrapperRef.current.contains(e.target as Node)) {
        setOpen(false);
      }
    }
    document.addEventListener('mousedown', handleOutside);
    return () => document.removeEventListener('mousedown', handleOutside);
  }, []);

  const handleChange = (v: string) => {
    onChange(v);
    if (debounceRef.current) clearTimeout(debounceRef.current);
    if (!v.trim()) {
      setResults([]);
      setOpen(false);
      return;
    }
    setLoading(true);
    debounceRef.current = setTimeout(async () => {
      try {
        const items = await indentService.searchItems(v.trim());
        setResults(items);
        setOpen(true);
      } catch {
        setResults([]);
      } finally {
        setLoading(false);
      }
    }, 280);
  };

  const handleSelect = (item: ItemSearchResult) => {
    onSelect(item);
    setOpen(false);
    setResults([]);
  };

  const displayName = (item: ItemSearchResult) =>
    item.size_name && item.size_id !== 6
      ? `${item.item_name} (${item.size_name})`
      : item.item_name;

  return (
    <div className="relative" ref={wrapperRef}>
      <div className="relative">
        <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
        <input
          type="text"
          value={value}
          onChange={e => handleChange(e.target.value)}
          placeholder="Type item name…"
          autoComplete="off"
          className="w-full h-10 pl-9 pr-3 rounded-lg border border-slate-200 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500"
        />
        {loading && (
          <Loader2 className="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 animate-spin" />
        )}
      </div>
      {open && results.length > 0 && (
        <ul className="absolute z-50 top-full left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-52 overflow-y-auto">
          {results.map(item => (
            <li key={item.id}>
              <button
                type="button"
                onMouseDown={() => handleSelect(item)}
                className="w-full text-left px-4 py-2.5 text-sm text-slate-800 hover:bg-cyan-50 hover:text-cyan-700 transition-colors flex items-center gap-2"
              >
                <Package className="w-3.5 h-3.5 text-slate-400 shrink-0" />
                {displayName(item)}
              </button>
            </li>
          ))}
        </ul>
      )}
      {open && !loading && results.length === 0 && value.trim() && (
        <div className="absolute z-50 top-full left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-lg px-4 py-3 text-xs text-slate-500">
          No items found for &ldquo;{value}&rdquo;
        </div>
      )}
    </div>
  );
}

// ─── Cart Row ─────────────────────────────────────────────────────────────────

function CartRow({
  item,
  onRemove,
  isRemoving,
}: {
  item: TempIndentItem;
  onRemove: (id: number) => void;
  isRemoving: boolean;
}) {
  return (
    <tr className="hover:bg-slate-50/60 transition-colors group">
      <td className="px-5 py-3.5 text-sm font-semibold text-slate-800">
        {item.item_name}
        {item.size_name && (
          <span className="ml-1.5 text-xs text-slate-400">({item.size_name})</span>
        )}
      </td>
      <td className="px-5 py-3.5 text-xs text-slate-500">{item.unit_name || '—'}</td>
      <td className="px-5 py-3.5 text-center font-bold text-cyan-700">{item.quantity}</td>
      <td className="px-5 py-3.5 text-center text-xs text-slate-500">
        {item.sale_price ? `₹${formatAmt(item.sale_price)}` : '—'}
      </td>
      <td className="px-5 py-3.5 text-center">
        <button
          type="button"
          onClick={() => onRemove(item.id)}
          disabled={isRemoving}
          className="p-1.5 rounded-lg text-rose-400 hover:text-rose-600 hover:bg-rose-50 transition-colors disabled:opacity-40"
        >
          {isRemoving ? (
            <Loader2 className="w-4 h-4 animate-spin" />
          ) : (
            <Trash2 className="w-4 h-4" />
          )}
        </button>
      </td>
    </tr>
  );
}

// ─── Main Page ────────────────────────────────────────────────────────────────

export default function NewIndentPage() {
  const router = useRouter();
  const queryClient = useQueryClient();

  // Fetch next indent ID
  const { data: nextIdData, isLoading: idLoading } = useQuery({
    queryKey: ['indent-next-id'],
    queryFn: () => indentService.getNextIndentId(),
    staleTime: 0,
  });
  const indentId = nextIdData;

  // Temp items cart
  const { data: tempItems = [], refetch: refetchTemp } = useQuery({
    queryKey: ['indent-temp', indentId],
    queryFn: () => indentService.getTempItems(indentId!),
    enabled: !!indentId,
  });

  // Local add-item form state
  const [selectedItem, setSelectedItem] = useState<ItemSearchResult | null>(null);
  const [itemSearch, setItemSearch] = useState('');
  const [quantity, setQuantity] = useState('');
  const [addError, setAddError] = useState('');
  const [removingId, setRemovingId] = useState<number | null>(null);
  const [success, setSuccess] = useState(false);

  // Add item mutation
  const addMutation = useMutation({
    mutationFn: (data: { indent_id: number; item_id: number; size_id?: number | null; quantity: number }) =>
      indentService.addTempItem(data),
    onSuccess: () => {
      refetchTemp();
      setSelectedItem(null);
      setItemSearch('');
      setQuantity('');
      setAddError('');
    },
    onError: () => setAddError('Failed to add item. Please try again.'),
  });

  // Finalize mutation
  const finalizeMutation = useMutation({
    mutationFn: () => indentService.finalizeIndent(indentId!),
    onSuccess: () => {
      setSuccess(true);
      queryClient.invalidateQueries({ queryKey: ['indents'] });
      setTimeout(() => router.push('/dashboard/purchase/indents'), 1800);
    },
  });

  const handleSelectItem = useCallback((item: ItemSearchResult) => {
    setSelectedItem(item);
    const name = item.size_name && item.size_id !== 6
      ? `${item.item_name} (${item.size_name})`
      : item.item_name;
    setItemSearch(name);
  }, []);

  const handleAddItem = () => {
    if (!selectedItem) { setAddError('Please select an item from the list.'); return; }
    if (!quantity || isNaN(Number(quantity)) || Number(quantity) <= 0) {
      setAddError('Please enter a valid quantity.');
      return;
    }
    if (!indentId) return;
    setAddError('');
    addMutation.mutate({
      indent_id: indentId,
      item_id: selectedItem.id,
      size_id: selectedItem.size_id,
      quantity: Number(quantity),
    });
  };

  const handleRemoveItem = async (id: number) => {
    setRemovingId(id);
    try {
      await indentService.removeTempItem(id);
      refetchTemp();
    } finally {
      setRemovingId(null);
    }
  };

  const handlePreview = () => {
    if (indentId) window.open(`/dashboard/purchase/indents/${indentId}?preview=1`, '_blank');
  };

  if (success) {
    return (
      <div className="flex flex-col items-center justify-center min-h-[60vh] gap-4">
        <div className="p-4 bg-emerald-50 rounded-full text-emerald-500 animate-bounce">
          <CheckCircle className="w-12 h-12" />
        </div>
        <h2 className="text-xl font-bold text-slate-800">Indent Finalized!</h2>
        <p className="text-sm text-slate-500">Indent #{indentId} has been saved successfully.</p>
        <p className="text-xs text-slate-400">Redirecting to list…</p>
      </div>
    );
  }

  return (
    <div className="max-w-4xl mx-auto space-y-5">
      {/* Header */}
      <div className="flex items-center gap-3">
        <Link
          href="/dashboard/purchase/indents"
          className="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 transition-colors"
        >
          <ArrowLeft className="w-5 h-5" />
        </Link>
        <div className="h-9 w-9 rounded-lg bg-cyan-600 flex items-center justify-center shadow-sm">
          <ListTodo className="w-5 h-5 text-white" />
        </div>
        <div>
          <h1 className="text-lg font-bold text-slate-900 leading-none">
            New Indent
            {!idLoading && indentId && (
              <span className="ml-2 text-xs font-normal text-slate-400">ID: #{indentId}</span>
            )}
          </h1>
          <p className="text-xs text-slate-500 mt-0.5">Purchase Requisition</p>
        </div>
      </div>

      {/* Add Item Form */}
      <div className="bg-white border border-slate-200 rounded-xl p-5 shadow-sm space-y-4">
        <h2 className="text-sm font-semibold text-slate-700">Add Items</h2>

        <div className="flex flex-wrap gap-3 items-end">
          {/* Item search */}
          <div className="flex flex-col gap-1 flex-1 min-w-[220px]">
            <label className="text-[10px] font-semibold text-slate-500 uppercase tracking-wide">
              Select Item <span className="text-rose-500">*</span>
            </label>
            <ItemAutocomplete
              value={itemSearch}
              onChange={v => { setItemSearch(v); if (!v) setSelectedItem(null); }}
              onSelect={handleSelectItem}
            />
          </div>

          {/* Quantity */}
          <div className="flex flex-col gap-1 w-36">
            <label className="text-[10px] font-semibold text-slate-500 uppercase tracking-wide">
              Quantity <span className="text-rose-500">*</span>
            </label>
            <input
              type="number"
              min="1"
              value={quantity}
              onChange={e => setQuantity(e.target.value.replace(/[^0-9]/g, ''))}
              placeholder="Qty"
              className="h-10 px-3 rounded-lg border border-slate-200 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500"
            />
          </div>

          {/* Add button */}
          <button
            type="button"
            onClick={handleAddItem}
            disabled={addMutation.isPending}
            className="h-10 px-5 flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 disabled:opacity-60 text-white rounded-lg text-xs font-semibold transition-colors shadow-sm"
          >
            {addMutation.isPending ? (
              <Loader2 className="w-3.5 h-3.5 animate-spin" />
            ) : (
              <Plus className="w-3.5 h-3.5" />
            )}
            Add Item
          </button>
        </div>

        {addError && (
          <div className="flex items-center gap-2 text-xs text-rose-600 bg-rose-50 border border-rose-100 rounded-lg px-3 py-2">
            <AlertCircle className="w-3.5 h-3.5 shrink-0" />
            {addError}
          </div>
        )}
      </div>

      {/* Cart Table */}
      <div className="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <div className="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <span className="text-xs font-semibold text-slate-600 uppercase tracking-wide">
            Indent Items ({tempItems.length})
          </span>
        </div>

        {tempItems.length === 0 ? (
          <div className="flex flex-col items-center justify-center py-14 gap-2 text-slate-400">
            <Package className="w-8 h-8" />
            <p className="text-xs font-medium">No items added yet</p>
          </div>
        ) : (
          <table className="w-full text-sm">
            <thead>
              <tr className="bg-slate-50 border-b border-slate-200">
                <th className="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Item</th>
                <th className="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Unit</th>
                <th className="px-5 py-2.5 text-center text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Qty</th>
                <th className="px-5 py-2.5 text-center text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Rate</th>
                <th className="px-5 py-2.5 text-center text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Remove</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {tempItems.map(item => (
                <CartRow
                  key={item.id}
                  item={item}
                  onRemove={handleRemoveItem}
                  isRemoving={removingId === item.id}
                />
              ))}
            </tbody>
          </table>
        )}
      </div>

      {/* Action Buttons */}
      <div className="flex items-center gap-3 justify-end pb-6">
        <Link
          href="/dashboard/purchase/indents"
          className="h-10 px-5 flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition-colors"
        >
          <ArrowLeft className="w-3.5 h-3.5" />
          Back
        </Link>
        <button
          type="button"
          onClick={handlePreview}
          disabled={tempItems.length === 0}
          className="h-10 px-5 flex items-center gap-2 bg-slate-700 hover:bg-slate-800 disabled:opacity-50 text-white rounded-lg text-xs font-semibold transition-colors"
        >
          <Eye className="w-3.5 h-3.5" />
          Preview
        </button>
        <button
          type="button"
          onClick={() => finalizeMutation.mutate()}
          disabled={tempItems.length === 0 || finalizeMutation.isPending}
          className="h-10 px-6 flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 disabled:opacity-50 text-white rounded-lg text-xs font-semibold shadow-sm transition-all"
        >
          {finalizeMutation.isPending ? (
            <Loader2 className="w-3.5 h-3.5 animate-spin" />
          ) : (
            <CheckCircle className="w-3.5 h-3.5" />
          )}
          Save & Finalize
        </button>
      </div>

      {finalizeMutation.isError && (
        <div className="flex items-center gap-2 text-xs text-rose-600 bg-rose-50 border border-rose-100 rounded-lg px-3 py-2">
          <AlertCircle className="w-3.5 h-3.5 shrink-0" />
          Failed to finalize indent. Please try again.
        </div>
      )}
    </div>
  );
}
