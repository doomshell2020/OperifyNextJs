import React from 'react';

interface StatusBadgeProps {
  status: string;
}

export const StatusBadge: React.FC<StatusBadgeProps> = ({ status }) => {
  const normalized = status ? status.trim().toUpperCase() : 'UNKNOWN';

  // Return specific classes based on status string
  let classes = 'bg-slate-100 text-slate-800 border-slate-200';
  let label = status || 'Unknown';

  switch (normalized) {
    case 'Y':
    case 'YES':
    case 'ACTIVE':
    case 'APPROVED':
    case 'COMPLETE':
    case 'COMPLETED':
    case 'C': // For closed/completed in DB
      classes = 'bg-emerald-50 text-emerald-700 border-emerald-200';
      label = normalized === 'C' ? 'Closed' : label;
      break;
    case 'N':
    case 'NO':
    case 'INACTIVE':
    case 'PENDING':
    case 'O': // For open/pending in DB
      classes = 'bg-amber-50 text-amber-700 border-amber-200';
      label = normalized === 'O' ? 'Open' : label;
      break;
    case 'R':
    case 'REVISED':
      classes = 'bg-cyan-50 text-cyan-700 border-cyan-200';
      label = 'Revised';
      break;
    case 'ASSIGNED':
      classes = 'bg-indigo-50 text-indigo-700 border-indigo-200';
      break;
    case 'DRAFT':
      classes = 'bg-slate-100 text-slate-700 border-slate-200';
      break;
    default:
      break;
  }

  return (
    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border ${classes}`}>
      {label}
    </span>
  );
};
