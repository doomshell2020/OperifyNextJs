'use client';

import React from 'react';
import { ArrowUpRight, ArrowDownRight, Activity } from 'lucide-react';
import { formatQty, formatAmt } from '@/utils/formatters';

interface SummaryCardProps {
  title: string;
  total: number;
  today: number;
  week: number;
  month: number;
  trend: {
    percentage: string;
    isUp: boolean;
    label: string;
  };
  sparkline: number[];
  icon: React.ReactNode;
  iconBg: string;
}

export const SummaryCard: React.FC<SummaryCardProps> = ({
  title,
  total,
  today,
  week,
  month,
  trend,
  sparkline = [],
  icon,
  iconBg
}) => {
  // SVG Sparkline Path Generator
  const generateSparklinePath = (data: number[]) => {
    if (!data || data.length < 2) return { path: '', area: '' };
    
    const width = 120;
    const height = 36;
    const padding = 2;
    
    const maxVal = Math.max(...data, 1);
    const minVal = Math.min(...data, 0);
    const range = maxVal - minVal;
    
    const points = data.map((val, index) => {
      const x = padding + (index / (data.length - 1)) * (width - padding * 2);
      // Invert Y because SVG coordinates start from top-left
      const y = padding + (1 - (val - minVal) / range) * (height - padding * 2);
      return { x, y };
    });
    
    const linePath = points.map((p, i) => `${i === 0 ? 'M' : 'L'} ${p.x.toFixed(1)} ${p.y.toFixed(1)}`).join(' ');
    const areaPath = `${linePath} L ${points[points.length - 1].x.toFixed(1)} ${height} L ${points[0].x.toFixed(1)} ${height} Z`;
    
    return { path: linePath, area: areaPath };
  };

  const { path: sparkPath, area: sparkArea } = generateSparklinePath(sparkline);

  return (
    <div className="bg-white border border-slate-200/80 rounded-xl p-6 shadow-sm hover:shadow-md transition duration-200 flex flex-col justify-between relative overflow-hidden group">
      
      {/* Top section: Icon and title */}
      <div className="flex items-center justify-between">
        <span className="text-xs font-bold text-slate-400 uppercase tracking-wider">{title}</span>
        <div className={`p-2.5 rounded-lg text-white ${iconBg} shadow-sm group-hover:scale-105 transition duration-200`}>
          {icon}
        </div>
      </div>

      {/* Middle section: Total and Sparkline */}
      <div className="mt-4 flex items-baseline justify-between">
        <div>
          <span className="text-3xl font-extrabold text-slate-900 tracking-tight">
            {title.toLowerCase().includes('amount') || title.toLowerCase().includes('value') || title.toLowerCase().includes('cost') || title.toLowerCase().includes('rate') ? formatAmt(total) : title.toLowerCase().includes('qty') || title.toLowerCase().includes('quantity') ? formatQty(total) : total.toLocaleString()}
          </span>
        </div>
        
        {/* Sparkline Visual */}
        {sparkPath && (
          <div className="h-9 w-28 shrink-0">
            <svg width="100%" height="100%" viewBox="0 0 120 36" className="overflow-visible">
              <defs>
                <linearGradient id={`gradient-${title}`} x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stopColor={trend.isUp ? '#10b981' : '#f43f5e'} stopOpacity="0.2" />
                  <stop offset="100%" stopColor={trend.isUp ? '#10b981' : '#f43f5e'} stopOpacity="0" />
                </linearGradient>
              </defs>
              <path
                d={sparkArea}
                fill={`url(#gradient-${title})`}
                stroke="none"
              />
              <path
                d={sparkPath}
                fill="none"
                stroke={trend.isUp ? '#10b981' : '#f43f5e'}
                strokeWidth="1.5"
                strokeLinecap="round"
                strokeLinejoin="round"
              />
            </svg>
          </div>
        )}
      </div>

      {/* Bottom section: Subtotals & Trends */}
      <div className="mt-4 pt-4 border-t border-slate-50 flex items-center justify-between text-xs font-medium text-slate-500">
        <div className="flex flex-col">
          <span className="text-slate-400 font-bold uppercase text-[9px] tracking-wider">Temporal Overview</span>
          <div className="flex items-center gap-2 mt-1">
            <span>Today: <strong className="text-slate-800">{today}</strong></span>
            <span className="w-1 h-1 bg-slate-300 rounded-full"></span>
            <span>Week: <strong className="text-slate-800">{week}</strong></span>
            <span className="w-1 h-1 bg-slate-300 rounded-full"></span>
            <span>Month: <strong className="text-slate-800">{month}</strong></span>
          </div>
        </div>

        {/* Trend Percentage badge */}
        <div className={`flex items-center gap-0.5 px-2 py-0.5 rounded-full font-semibold border ${
          trend.isUp 
            ? 'bg-emerald-50 text-emerald-700 border-emerald-100' 
            : 'bg-rose-50 text-rose-700 border-rose-100'
        }`}>
          {trend.isUp ? <ArrowUpRight className="w-3.5 h-3.5" /> : <ArrowDownRight className="w-3.5 h-3.5" />}
          <span>{trend.percentage}</span>
        </div>
      </div>

    </div>
  );
};
