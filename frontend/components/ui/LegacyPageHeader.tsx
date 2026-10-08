import Link from 'next/link';
import { Home } from 'lucide-react';

/** Matches CakePHP's content-header; actions belong in the manager's box-header. */
export function LegacyPageHeader({ title, breadcrumb = title }: { title: string; breadcrumb?: string }) {
  return <div className="legacy-page-header">
    <h1>{title}</h1>
    <nav aria-label="Breadcrumb">
      <Link href="/dashboard"><Home aria-hidden="true" size={12} />Home</Link>
      <span aria-hidden="true">&gt;</span><span>{breadcrumb}</span>
    </nav>
  </div>;
}
