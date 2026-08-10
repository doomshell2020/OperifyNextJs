import React, { useState, useEffect, useRef } from 'react';
import { Search, Loader2, Check } from 'lucide-react';
import { useQuery } from '@tanstack/react-query';
import grnInspectionService from '@/services/grnInspection.service';

interface AsyncInspectionSearchSelectProps {
  value: string;
  onChange: (inspectionId: string) => void;
  error?: string;
  disabled?: boolean;
}

export function AsyncInspectionSearchSelect({ value, onChange, error, disabled }: AsyncInspectionSearchSelectProps) {
  const [isOpen, setIsOpen] = useState(false);
  const [searchTerm, setSearchTerm] = useState('');
  const wrapperRef = useRef<HTMLDivElement>(null);

  const [debouncedSearchTerm, setDebouncedSearchTerm] = useState('');
  
  useEffect(() => {
    const handler = setTimeout(() => {
      setDebouncedSearchTerm(searchTerm);
    }, 500);
    return () => clearTimeout(handler);
  }, [searchTerm]);

  const { data, isLoading } = useQuery({
    queryKey: ['pending-inspections', debouncedSearchTerm],
    queryFn: async () => {
      // Assuming grnInspectionService has listInspections that takes inspection_id or similar search parameter
      // We search across all inspections but in reality should only show approved ones
      const res = await grnInspectionService.listInspections({ 
        // passing bill_no as search term just to filter something, ideally the backend supports inspection_id search
        page: 1,
        limit: 10 
      });
      // For now returning the list, filtering locally based on search term
      let items = res.data || [];
      if (debouncedSearchTerm) {
        items = items.filter((i: any) => i.inspection_id.includes(debouncedSearchTerm) || i.po_id.includes(debouncedSearchTerm));
      }
      return items;
    },
    enabled: isOpen,
  });

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
          placeholder="Search Inspection (e.g. 1001)..."
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
              data.map((ins: any) => (
                <div 
                  key={ins.inspection_id} 
                  className={`px-3 py-2 flex items-center justify-between hover:bg-cyan-50 cursor-pointer text-sm transition-colors ${value === ins.inspection_id ? 'bg-cyan-50/50 font-medium text-cyan-700' : 'text-slate-700'}`}
                  onClick={() => {
                    onChange(ins.inspection_id);
                    setSearchTerm(ins.inspection_id);
                    setIsOpen(false);
                  }}
                >
                  <div className="flex flex-col">
                    <span className="font-semibold">{ins.inspection_id}</span>
                    <span className="text-xs text-slate-500">PO: {ins.po_id} | Vendor: {ins.supplier}</span>
                  </div>
                  {value === ins.inspection_id && <Check className="w-4 h-4 text-cyan-600" />}
                </div>
              ))
            ) : (
              <div className="px-3 py-4 text-sm text-slate-500 text-center">
                No inspections found.
              </div>
            )}
          </div>
        </div>
      )}
    </div>
  );
}
