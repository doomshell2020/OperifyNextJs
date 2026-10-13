'use client';

import { useEffect, useRef } from 'react';
import type { Category } from '../../../../services/settings.service';

export function CategoryCheckboxSelect({ categories, value, onChange }: {
  categories: Pick<Category, 'id' | 'category_name'>[];
  value: string[];
  onChange: (value: string[]) => void;
}) {
  const dropdown = useRef<HTMLDetailsElement>(null);
  const selected = categories.filter(category => value.includes(String(category.id)));
  const allSelected = categories.length > 0 && selected.length === categories.length;

  useEffect(() => {
    const closeOutside = (event: PointerEvent) => {
      if (dropdown.current && !dropdown.current.contains(event.target as Node)) dropdown.current.open = false;
    };
    document.addEventListener('pointerdown', closeOutside);
    return () => document.removeEventListener('pointerdown', closeOutside);
  }, []);

  return (
    <details ref={dropdown} className="stock-category-dropdown" onKeyDown={event => {
      if (event.key === 'Escape' && dropdown.current) {
        dropdown.current.open = false;
        dropdown.current.querySelector('summary')?.focus();
      }
    }} onBlur={event => {
      if (event.relatedTarget && !event.currentTarget.contains(event.relatedTarget as Node)) event.currentTarget.open = false;
    }}>
      <summary id="stock-categories" aria-label="Select categories">
        {allSelected ? 'All Selected' : selected.length ? `${selected.length} Selected` : 'Select Category'}
        <span aria-hidden="true" className="stock-category-caret" />
      </summary>
      <div className="stock-category-list">
        <label className="stock-category-all">
          <input type="checkbox" checked={allSelected} onChange={event => onChange(event.target.checked ? categories.map(category => String(category.id)) : [])} />
          <strong>All Category</strong>
        </label>
        <hr />
        <fieldset>
          <legend className="sr-only">Categories (Optional)</legend>
          {categories.map(category => {
            const id = String(category.id);
            return <label key={id}>
              <input type="checkbox" checked={value.includes(id)} onChange={event => onChange(event.target.checked ? [...value, id] : value.filter(selectedId => selectedId !== id))} />
              <span>{category.category_name}</span>
            </label>;
          })}
          {!categories.length && <p>No categories found.</p>}
        </fieldset>
      </div>
    </details>
  );
}
