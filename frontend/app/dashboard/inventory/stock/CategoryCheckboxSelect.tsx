'use client';

import { useEffect, useRef, useState } from 'react';
import type { Category } from '../../../../services/settings.service';

export function CategoryCheckboxSelect({ categories, value, onChange }: {
  categories: Category[];
  value: string[];
  onChange: (value: string[]) => void;
}) {
  const dropdown = useRef<HTMLDetailsElement>(null);
  const [search, setSearch] = useState('');
  const selected = categories.filter(category => value.includes(String(category.id)));
  const visible = categories.filter(category => category.category_name.toLowerCase().includes(search.toLowerCase()));

  useEffect(() => {
    const closeOutside = (event: PointerEvent) => {
      if (dropdown.current && !dropdown.current.contains(event.target as Node)) dropdown.current.open = false;
    };
    document.addEventListener('pointerdown', closeOutside);
    return () => document.removeEventListener('pointerdown', closeOutside);
  }, []);

  return (
    <details ref={dropdown} className="relative text-sm" onToggle={event => {
      if (!event.currentTarget.open) setSearch('');
    }} onKeyDown={event => {
      if (event.key === 'Escape' && dropdown.current) {
        dropdown.current.open = false;
        dropdown.current.querySelector('summary')?.focus();
      }
    }} onBlur={event => {
      if (event.relatedTarget && !event.currentTarget.contains(event.relatedTarget as Node)) event.currentTarget.open = false;
    }}>
      <summary id="stock-categories" aria-label="Select categories" className="cursor-pointer border border-slate-300 bg-white px-2 py-1.5 focus:outline-cyan-500">
        {selected.length ? `${selected.length} selected: ${selected.map(category => category.category_name).join(', ')}` : 'Select categories...'}
      </summary>
      <div className="absolute left-0 top-full z-30 mt-1 w-full min-w-56 border border-slate-300 bg-white shadow-lg">
        <div className="p-2 border-b border-slate-200">
          <input aria-label="Search categories" placeholder="Search categories..." value={search} onChange={event => setSearch(event.target.value)} className="w-full border border-slate-300 px-2 py-1 outline-none focus:border-cyan-500" />
          <button type="button" onClick={() => onChange([])} disabled={!value.length} className="mt-1 text-cyan-700 disabled:text-slate-400">Clear selection</button>
        </div>
        <fieldset className="max-h-64 overflow-y-auto p-1">
          <legend className="sr-only">Categories (Optional)</legend>
          {visible.map(category => {
            const id = String(category.id);
            return <label key={id} className="flex cursor-pointer items-center gap-2 px-2 py-1.5 hover:bg-slate-100">
              <input type="checkbox" checked={value.includes(id)} onChange={event => onChange(event.target.checked ? [...value, id] : value.filter(selectedId => selectedId !== id))} className="h-4 w-4 accent-cyan-600" />
              <span>{category.category_name}</span>
            </label>;
          })}
          {!visible.length && <p className="p-2 text-slate-500">No categories found.</p>}
        </fieldset>
      </div>
    </details>
  );
}
