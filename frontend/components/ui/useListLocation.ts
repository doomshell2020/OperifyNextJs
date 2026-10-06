'use client';

import { useEffect, useRef, useState } from 'react';

/** Preserve Cake's query-string list state through reloads and browser navigation. */
export function useListLocation<T extends object>(filters: T, page: number, keys: string[], restore: (filters: T, page: number) => void) {
  const [ready, setReady] = useState(false);
  const initial = useRef({ filters, keys, restore });
  useEffect(() => {
    const read = () => {
      const params = new URLSearchParams(window.location.search);
      const next = { ...initial.current.filters } as Record<string, unknown>;
      initial.current.keys.forEach(key => { if (params.has(key)) next[key] = params.get(key) || ''; });
      const requested = Number(params.get('page') || 1);
      initial.current.restore(next as T, Number.isSafeInteger(requested) && requested > 0 ? requested : 1);
      setReady(true);
    };
    read();
    window.addEventListener('popstate', read);
    return () => window.removeEventListener('popstate', read);
  }, []);
  useEffect(() => {
    if (!ready) return;
    const params = new URLSearchParams();
    initial.current.keys.forEach(key => {
      const value = (filters as Record<string, unknown>)[key];
      if (value !== undefined && value !== null && value !== '' && !(key === 'type' && value === 'po')) params.set(key, String(value));
    });
    if (page > 1) params.set('page', String(page));
    const query = params.toString();
    const url = window.location.pathname + (query ? `?${query}` : '');
    if (url !== window.location.pathname + window.location.search) window.history.pushState(window.history.state, '', url);
  }, [filters, page, ready]);
  return ready;
}
