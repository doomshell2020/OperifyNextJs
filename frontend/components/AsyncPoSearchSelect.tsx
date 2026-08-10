import React, { useState, useEffect, useRef } from 'react';
import { Search, Loader2, Check } from 'lucide-react';
import { useQuery } from '@tanstack/react-query';
import purchaseOrderService from '@/services/purchaseOrder.service';

interface AsyncPoSearchSelectProps {
  value: string;
  onChange: (poId: string) => void;
  error?: string;
  disabled?: boolean;
}

export function AsyncPoSearchSelect({ value, onChange, error, disabled }: AsyncPoSearchSelectProps) {
  const [isOpen, setIsOpen] = useState(false);
  const [searchTerm, setSearchTerm] = useState('');
  const wrapperRef = useRef<HTMLDivElement>(null);

  // Simple debounce inline for standalone reliability
  const [debouncedSearchTerm, setDebouncedSearchTerm] = useState('');
  
  useEffect(() => {
    const handler = setTimeout(() => {
      setDebouncedSearchTerm(searchTerm);
    }, 500);
    return () => clearTimeout(handler);
  }, [searchTerm]);

  const { data, isLoading } = useQuery({
    queryKey: ['pending-purchase-orders', debouncedSearchTerm],
    queryFn: async () => {
      const res = await purchaseOrderService.listPurchaseOrders({ 
        po_number: debouncedSearchTerm,
        status: 'O', // Open POs only
        limit: 10 
      });
      return res.items || [];
    },
    enabled: isOpen,
  });

  // When value changes from outside (e.g., reset form), sync local state
  useEffect(() => {
    if (!value) {
      setSearchTerm('');
    } else if (value !== searchTerm && !isOpen) {
      setSearchTerm(value);
    }
  }, [value, isOpen]);

  useEffect(() => {
    function handleClickOutside(event: MouseEvent) {
      if (wrapperRef.current && !wrapperRef.current.contains(event.target as Node)) {
        setIsOpen(false);
        // Reset search term to selected value if they click away
        if (value) {
          setSearchTerm(value);
        } else {
          setSearchTerm('');
        }
      }
    }
    document.addEventListener("mousedown", handleClickOutside);
    return () => document.removeEventListener("mousedown", handleClickOutside);
  }, [value]);

  return (
    <div className="relative w-full" ref={wrapperRef}>
      <div className="relative">
        <input 
          type="text"
          disabled={disabled}
          className={`w-full h-[40px] border rounded-md pl-3 pr-8 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 bg-white shadow-sm disabled:bg-slate-50 disabled:text-slate-500 disabled:cursor-not-allowed ${error ? 'border-red-500 focus:ring-red-500' : 'border-slate-300'}`}
          placeholder="Search PO (e.g. 2526-101)..."
          value={searchTerm}
          onChange={(e) => {
            setSearchTerm(e.target.value);
            setIsOpen(true);
            if (e.target.value === '') {
              onChange('');
            }
          }}
          onFocus={() => setIsOpen(true)}
        />
        <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
          {isLoading ? <Loader2 className="h-4 w-4 animate-spin text-cyan-600" /> : <Search className="h-4 w-4" />}
        </div>
      </div>
      
      {isOpen && (
        <div className="absolute top-full left-0 w-full mt-1 bg-white border border-slate-200 rounded-md shadow-lg z-50 flex flex-col overflow-hidden max-h-60">
          <div className="overflow-y-auto py-1">
            {isLoading && data === undefined ? (
              <div className="px-3 py-4 text-sm text-slate-500 flex justify-center items-center gap-2">
                <Loader2 className="h-4 w-4 animate-spin" /> Loading...
              </div>
            ) : data && data.length > 0 ? (
              data.map(po => (
                <div 
                  key={po.id} 
                  className={`px-3 py-2 flex items-center justify-between hover:bg-cyan-50 cursor-pointer text-sm transition-colors ${value === po.po_number ? 'bg-cyan-50/50 font-medium text-cyan-700' : 'text-slate-700'}`}
                  onClick={() => {
                    onChange(po.po_number);
                    setSearchTerm(po.po_number);
                    setIsOpen(false);
                  }}
                >
                  <div className="flex flex-col">
                    <span className="font-medium text-slate-900">{po.po_number}</span>
                    <span className="text-xs text-slate-500 truncate">{po.vendor_name}</span>
                  </div>
                  {value === po.po_number && <Check className="h-4 w-4 text-cyan-600 flex-shrink-0" />}
                </div>
              ))
            ) : (
              <div className="px-3 py-3 text-sm text-slate-500 text-center italic">No pending POs found</div>
            )}
          </div>
        </div>
      )}
    </div>
  );
}
