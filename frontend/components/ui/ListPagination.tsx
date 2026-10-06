'use client';

// CakePHP AppController defaults to 50; the bundled Indent DataTables also defaults to 50.
export const LEGACY_LIST_LIMIT = 50;

export function ListPagination({ page, limit, total, onPageChange, busy = false }: {
  page: number; limit: number; total: number; onPageChange: (page: number) => void; busy?: boolean;
}) {
  const pages = Math.ceil(total / limit);
  const start = Math.max(1, Math.min(page - 2, pages - 4));
  const numbers = Array.from({ length: Math.min(5, pages) }, (_, i) => start + i);
  const buttonClass = 'px-3 py-1.5 border border-slate-200 rounded text-xs text-slate-600 hover:bg-slate-100 disabled:opacity-50 disabled:cursor-not-allowed';
  return <nav aria-label="List pagination" className="flex flex-wrap items-center justify-between gap-3 p-4 border-t border-slate-200 bg-slate-50">
    <span className="text-xs text-slate-600" aria-live="polite">Page {page} of {pages}, showing {total ? (page - 1) * limit + 1 : 0} to {Math.min(page * limit, total)} of {total} records</span>
    <div className="flex flex-wrap gap-1">
      <button className={buttonClass} disabled={busy || page <= 1} onClick={() => onPageChange(1)}>First</button>
      <button className={buttonClass} disabled={busy || page <= 1} onClick={() => onPageChange(page - 1)}>Previous</button>
      {numbers.map(number => <button key={number} className={buttonClass} aria-current={page === number ? 'page' : undefined} disabled={busy || page === number} onClick={() => onPageChange(number)}>{number}</button>)}
      <button className={buttonClass} disabled={busy || page >= pages} onClick={() => onPageChange(page + 1)}>Next</button>
      <button className={buttonClass} disabled={busy || page >= pages} onClick={() => onPageChange(pages)}>Last</button>
    </div>
  </nav>;
}
