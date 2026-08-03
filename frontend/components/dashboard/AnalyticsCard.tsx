'use client';

import React, { useEffect, useState } from 'react';
import dynamic from 'next/dynamic';

// Dynamically load react-apexcharts to avoid Next.js SSR document undefined errors
const Chart = dynamic(() => import('react-apexcharts'), { ssr: false });

interface ChartItem {
  name: string;
  value: number;
}

interface AnalyticsCardProps {
  title: string;
  subtitle: string;
  data: ChartItem[];
  colors?: string[];
}

export const AnalyticsCard: React.FC<AnalyticsCardProps> = ({
  title,
  subtitle,
  data = [],
  colors = ['#6366f1', '#06b6d4', '#10b981', '#f43f5e']
}) => {
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setMounted(true);
  }, []);

  const total = data.reduce((acc, curr) => acc + Number(curr.value || 0), 0);
  const series = data.map(item => Number(item.value || 0));
  const labels = data.map(item => item.name);

  const chartOptions: any = {
    chart: {
      type: 'donut',
      animations: {
        enabled: true,
        easing: 'easeinout',
        speed: 800
      }
    },
    labels: labels,
    colors: colors,
    stroke: {
      show: true,
      width: 2,
      colors: ['#ffffff']
    },
    dataLabels: {
      enabled: false
    },
    legend: {
      position: 'bottom',
      fontSize: '11px',
      fontFamily: 'inherit',
      fontWeight: 500,
      labels: {
        colors: '#64748b'
      },
      markers: {
        width: 8,
        height: 8,
        radius: 4
      }
    },
    plotOptions: {
      pie: {
        donut: {
          size: '72%',
          labels: {
            show: true,
            name: {
              show: true,
              fontSize: '11px',
              fontFamily: 'inherit',
              fontWeight: 600,
              color: '#94a3b8',
              offsetY: -4
            },
            value: {
              show: true,
              fontSize: '20px',
              fontFamily: 'inherit',
              fontWeight: 800,
              color: '#0f172a',
              offsetY: 6,
              formatter: (val: string) => Number(val).toLocaleString()
            },
            total: {
              show: true,
              label: 'Total',
              color: '#94a3b8',
              fontSize: '11px',
              fontWeight: 600,
              formatter: () => total.toLocaleString()
            }
          }
        }
      }
    },
    tooltip: {
      enabled: true,
      y: {
        formatter: (val: number) => `${val} entries`
      }
    }
  };

  return (
    <div className="bg-white border border-slate-200/80 rounded-xl p-6 shadow-sm flex flex-col justify-between h-[360px] hover:shadow-md transition duration-200">
      
      {/* Title */}
      <div>
        <h3 className="text-xs font-bold text-slate-800 uppercase tracking-wider">{title}</h3>
        <p className="text-[10px] text-slate-400 font-medium mt-0.5">{subtitle}</p>
      </div>

      {/* Chart wrapper */}
      <div className="flex-1 flex items-center justify-center min-h-0 mt-4">
        {mounted && total > 0 ? (
          <div className="w-full max-w-[260px] mx-auto">
            <Chart
              options={chartOptions}
              series={series}
              type="donut"
              width="100%"
              height={220}
            />
          </div>
        ) : (
          <div className="flex flex-col items-center justify-center text-slate-400 gap-1.5 py-10">
            <div className="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center border border-slate-100">
              <span className="text-xs font-bold text-slate-300">0</span>
            </div>
            <p className="text-xs font-semibold text-slate-400">No data found</p>
          </div>
        )}
      </div>

    </div>
  );
};
