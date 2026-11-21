'use client';

import React, { useState, useRef, useEffect, useMemo } from 'react';
import { Search } from 'lucide-react';

export interface Option {
  id: number;
  name: string;
}

interface PoMultiselectProps {
  id: string;
  name: string;
  containerId: string;
  options: Option[];
  selected: string[];
  onChange: (selected: string[]) => void;
  defaultLabel: string;
  searchPlaceholder: string;
  emptyText: string;
}

export function PoMultiselect({
  id,
  name,
  containerId,
  options,
  selected,
  onChange,
  defaultLabel,
  searchPlaceholder,
  emptyText
}: PoMultiselectProps) {
  const [isOpen, setIsOpen] = useState(false);
  const [search, setSearch] = useState('');
  const [highlightedIndex, setHighlightedIndex] = useState(-1);

  const containerRef = useRef<HTMLDivElement>(null);
  const toggleBtnRef = useRef<HTMLButtonElement>(null);
  const searchInputRef = useRef<HTMLInputElement>(null);
  const selectAllRef = useRef<HTMLInputElement>(null);

  const filtered = useMemo(() => {
    const q = search.trim().toLowerCase();
    if (!q) return options;
    return options.filter(opt => opt.name.toLowerCase().includes(q));
  }, [options, search]);

  // Click outside to close
  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (containerRef.current && !containerRef.current.contains(event.target as Node)) {
        setIsOpen(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  // Escape to close
  useEffect(() => {
    const handleKeyDownGlobal = (event: KeyboardEvent) => {
      if (event.key === 'Escape' && isOpen) {
        setIsOpen(false);
        toggleBtnRef.current?.focus();
      }
    };
    document.addEventListener('keydown', handleKeyDownGlobal);
    return () => document.removeEventListener('keydown', handleKeyDownGlobal);
  }, [isOpen]);

  // Focus search input when opened
  useEffect(() => {
    if (isOpen) {
      setHighlightedIndex(-1);
      setTimeout(() => {
        searchInputRef.current?.focus();
      }, 50);
    }
  }, [isOpen]);

  const toggleOption = (optId: string) => {
    if (selected.includes(optId)) {
      onChange(selected.filter(id => id !== optId));
    } else {
      onChange([...selected, optId]);
    }
  };

  const filteredSelectedCount = useMemo(() => {
    const selectedSet = new Set(selected);
    return filtered.filter(opt => selectedSet.has(String(opt.id))).length;
  }, [filtered, selected]);

  const isAllFilteredSelected = filtered.length > 0 && filteredSelectedCount === filtered.length;
  const isIndeterminate = filteredSelectedCount > 0 && filteredSelectedCount < filtered.length;

  useEffect(() => {
    if (selectAllRef.current) {
      selectAllRef.current.indeterminate = isIndeterminate;
    }
  }, [isIndeterminate]);

  const handleSelectAllChange = () => {
    const filteredIds = filtered.map(opt => String(opt.id));
    if (isAllFilteredSelected) {
      // Uncheck all filtered
      const removeSet = new Set(filteredIds);
      onChange(selected.filter(id => !removeSet.has(id)));
    } else {
      // Check all filtered
      const currentSet = new Set(selected);
      filteredIds.forEach(id => currentSet.add(id));
      onChange(Array.from(currentSet));
    }
  };

  const displayText = useMemo(() => {
    if (selected.length === 0) {
      return defaultLabel;
    }
    if (options.length > 0 && selected.length === options.length) {
      return `All Selected (${options.length})`;
    }
    if (selected.length === 1) {
      const match = options.find(o => String(o.id) === selected[0]);
      return match ? match.name : defaultLabel;
    }
    return `${selected.length} Selected`;
  }, [selected, options, defaultLabel]);

  const countText = useMemo(() => {
    if (search.trim()) {
      return `${filtered.length} found`;
    }
    return `${options.length} total`;
  }, [search, filtered.length, options.length]);

  const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      if (!isOpen) {
        setIsOpen(true);
        return;
      }
      setHighlightedIndex(prev => (prev < filtered.length - 1 ? prev + 1 : 0));
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      setHighlightedIndex(prev => (prev > 0 ? prev - 1 : filtered.length - 1));
    } else if (e.key === 'Enter') {
      e.preventDefault();
      if (highlightedIndex >= 0 && highlightedIndex < filtered.length) {
        toggleOption(String(filtered[highlightedIndex].id));
      } else if (filtered.length === 1) {
        toggleOption(String(filtered[0].id));
      }
    }
  };

  return (
    <div
      ref={containerRef}
      className={`po-multiselect ${isOpen ? 'open' : ''}`}
      id={containerId}
    >
      <button
        ref={toggleBtnRef}
        type="button"
        className="form-control po-multiselect-toggle"
        aria-haspopup="true"
        aria-expanded={isOpen}
        onClick={() => setIsOpen(prev => !prev)}
      >
        <span className="po-multiselect-text">{displayText}</span>
        <span className="po-arrow" />
      </button>

      <div
        className="po-multiselect-dropdown"
        style={{ display: isOpen ? 'block' : 'none' }}
      >
        <div className="po-multiselect-search-box">
          <Search className="po-search-icon" size={13} aria-hidden="true" />
          <input
            ref={searchInputRef}
            type="text"
            id={id}
            className="form-control input-sm po-multiselect-search"
            placeholder={searchPlaceholder}
            value={search}
            onChange={e => {
              setSearch(e.target.value);
              setHighlightedIndex(0);
            }}
            onFocus={() => {
              if (!isOpen) setIsOpen(true);
            }}
            onKeyDown={handleKeyDown}
            autoComplete="off"
          />
        </div>

        <div className="po-multiselect-header">
          <label className="select-all-label">
            <input
              ref={selectAllRef}
              type="checkbox"
              className="po-select-all"
              checked={isAllFilteredSelected}
              onChange={handleSelectAllChange}
            />
            <span>Select All</span>
          </label>
          <span className="po-multiselect-count">{countText}</span>
        </div>

        <div className="po-multiselect-items">
          {filtered.map((item, index) => {
            const strId = String(item.id);
            const isChecked = selected.includes(strId);
            const isHighlighted = highlightedIndex === index;
            return (
              <label
                key={strId}
                className={`po-multiselect-item ${isHighlighted ? 'is-highlighted' : ''}`}
                data-text={item.name.toLowerCase()}
                onMouseEnter={() => setHighlightedIndex(index)}
              >
                <input
                  type="checkbox"
                  name={`${name}[]`}
                  value={strId}
                  checked={isChecked}
                  onChange={() => toggleOption(strId)}
                />
                <span className="po-item-label">{item.name}</span>
              </label>
            );
          })}
        </div>

        {filtered.length === 0 && (
          <div className="po-multiselect-empty">{emptyText}</div>
        )}
      </div>
    </div>
  );
}
