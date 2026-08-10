import React, { useState, useRef, useEffect } from 'react';
import { Search } from 'lucide-react';
import { Product } from '@/services/settings.service';

interface ProductSearchSelectProps {
  products: Product[];
  value: string;
  onChange: (productId: string) => void;
  error?: string;
}

export function ProductSearchSelect({ products, value, onChange, error }: ProductSearchSelectProps) {
  const [isOpen, setIsOpen] = useState(false);
  const [searchTerm, setSearchTerm] = useState('');
  const wrapperRef = useRef<HTMLDivElement>(null);

  const selectedProduct = products.find(p => p.id.toString() === value);

  useEffect(() => {
    if (selectedProduct) {
      setSearchTerm(selectedProduct.item_name);
    } else {
      setSearchTerm('');
    }
  }, [value, selectedProduct]);

  useEffect(() => {
    function handleClickOutside(event: MouseEvent) {
      if (wrapperRef.current && !wrapperRef.current.contains(event.target as Node)) {
        setIsOpen(false);
        if (selectedProduct) {
          setSearchTerm(selectedProduct.item_name);
        } else {
          setSearchTerm('');
        }
      }
    }
    document.addEventListener("mousedown", handleClickOutside);
    return () => document.removeEventListener("mousedown", handleClickOutside);
  }, [selectedProduct]);

  const filteredProducts = products.filter(p => p.item_name.toLowerCase().includes(searchTerm.toLowerCase()));

  return (
    <div className="relative w-full" ref={wrapperRef}>
      <div className="relative">
        <input 
          type="text"
          className={`w-full h-[40px] border rounded-md pl-3 pr-8 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white shadow-sm ${error ? 'border-red-300 focus:ring-red-500' : 'border-gray-300'}`}
          placeholder="Select product..."
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
        <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
          <Search className="h-3 w-3" />
        </div>
      </div>
      
      {isOpen && (
        <div className="absolute top-full left-0 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg z-50 flex flex-col overflow-hidden max-h-60">
          <div className="overflow-y-auto py-1">
            {filteredProducts.length > 0 ? (
              filteredProducts.map(p => (
                <div 
                  key={p.id} 
                  className={`px-3 py-2 hover:bg-blue-50 cursor-pointer text-xs transition-colors ${value === p.id.toString() ? 'bg-blue-50/50 font-semibold text-blue-700' : 'text-gray-700'}`}
                  onClick={() => {
                    onChange(p.id.toString());
                    setSearchTerm(p.item_name);
                    setIsOpen(false);
                  }}
                >
                  {p.item_name}
                </div>
              ))
            ) : (
              <div className="px-3 py-2 text-xs text-gray-500 text-center italic">No products found</div>
            )}
          </div>
        </div>
      )}
    </div>
  );
}
