'use client';

// CakePHP AppController defaults to 50; the bundled Indent DataTables also defaults to 50.
export const LEGACY_LIST_LIMIT = 50;

export function ListPagination({ page, limit, total, onPageChange, busy = false }: {
  page: number; limit: number; total: number; onPageChange: (page: number) => void; busy?: boolean;
}) {
  const pages = Math.max(1, Math.ceil(total / limit));
  const current = Math.max(0, Math.min(limit, total - (page - 1) * limit));
  const start = Math.max(1, Math.min(page - 2, pages - 4));
  const numbers = Array.from({ length: Math.min(5, pages) }, (_, i) => start + i);
  const buttonClass = 'disabled:cursor-not-allowed';
  return <nav aria-label="List pagination" className="legacy-pagination">
    <span aria-live="polite">Page {page} of {pages}, showing {current} record(s) out of {total} total</span>
    <div className="flex flex-wrap gap-1">
      <button className={buttonClass} disabled={busy || page <= 1} onClick={() => onPageChange(1)}>&lt;&lt; First</button>
      <button className={buttonClass} disabled={busy || page <= 1} onClick={() => onPageChange(page - 1)}>&lt; Previous</button>
      {numbers.map(number => <button key={number} className={buttonClass} aria-current={page === number ? 'page' : undefined} disabled={busy || page === number} onClick={() => onPageChange(number)}>{number}</button>)}
      <button className={buttonClass} disabled={busy || page >= pages} onClick={() => onPageChange(page + 1)}>Next &gt;</button>
      <button className={buttonClass} disabled={busy || page >= pages} onClick={() => onPageChange(pages)}>Last &gt;&gt;</button>
    </div>
  </nav>;
}
