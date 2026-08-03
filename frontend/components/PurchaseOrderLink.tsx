'use client';

import React, { useState, useRef, useEffect } from 'react';
import { createPortal } from 'react-dom';
import { usePurchaseOrderHover } from '../hooks/usePurchaseOrderHover';
import { PurchaseOrderHoverCard } from './PurchaseOrderHoverCard';
import { PurchaseOrderDetailsModal } from './PurchaseOrderDetailsModal';

interface PurchaseOrderLinkProps {
  id: number | string;
  poNumber: string;
}

export const PurchaseOrderLink: React.FC<PurchaseOrderLinkProps> = ({ id, poNumber }) => {
  const [isHovered, setIsHovered] = useState(false);
  const [isOpen, setIsOpen] = useState(false);
  const [isDetailsOpen, setIsDetailsOpen] = useState(false);
  const [copied, setCopied] = useState(false);
  const [coords, setCoords] = useState<{ top: number; left: number }>({ top: 0, left: 0 });
  const [placement, setPlacement] = useState<'top' | 'bottom'>('bottom');
  
  const triggerRef = useRef<HTMLButtonElement>(null);
  const hoverCardRef = useRef<HTMLDivElement>(null);
  const closeTimeoutRef = useRef<NodeJS.Timeout | null>(null);

  // Trigger query hook only when hovered or open
  const { data, isLoading, isError } = usePurchaseOrderHover(id, isHovered || isOpen);

  // Calculate coordinates & placement on open
  const calculatePosition = () => {
    if (!triggerRef.current) return;
    
    const triggerRect = triggerRef.current.getBoundingClientRect();
    const scrollY = window.scrollY;
    const scrollX = window.scrollX;
    
    // Popup bounds
    const cardWidth = 340;
    const cardHeight = 360; // Max expected height
    const padding = 10;
    
    let top = triggerRect.bottom + scrollY + 4; // default: below
    let left = triggerRect.left + scrollX;
    let place: 'top' | 'bottom' = 'bottom';
    
    // Adjust right bounds (ensure it stays inside viewport horizontally)
    if (left + cardWidth > window.innerWidth) {
      left = window.innerWidth - cardWidth - padding;
    }
    if (left < padding) {
      left = padding;
    }
    
    // Adjust bottom bounds (ensure it stays inside viewport vertically)
    if (triggerRect.bottom + cardHeight > window.innerHeight) {
      top = triggerRect.top + scrollY - cardHeight - 4; // show above
      place = 'top';
    }
    
    setCoords({ top, left });
    setPlacement(place);
  };

  const handleMouseEnterTrigger = () => {
    if (closeTimeoutRef.current) {
      clearTimeout(closeTimeoutRef.current);
    }
    setIsHovered(true);
    setIsOpen(true);
    calculatePosition();
  };

  const handleMouseLeaveTrigger = () => {
    closeTimeoutRef.current = setTimeout(() => {
      setIsOpen(false);
      setIsHovered(false);
    }, 200); // 200ms grace period
  };

  const handleMouseEnterCard = () => {
    if (closeTimeoutRef.current) {
      clearTimeout(closeTimeoutRef.current);
    }
    setIsOpen(true);
  };

  const handleMouseLeaveCard = () => {
    closeTimeoutRef.current = setTimeout(() => {
      setIsOpen(false);
      setIsHovered(false);
    }, 200);
  };

  const handleCopy = (text: string) => {
    navigator.clipboard.writeText(text);
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };

  // Re-calculate position on window scroll or resize
  useEffect(() => {
    if (isOpen) {
      window.addEventListener('scroll', calculatePosition);
      window.addEventListener('resize', calculatePosition);
    }
    return () => {
      window.removeEventListener('scroll', calculatePosition);
      window.removeEventListener('resize', calculatePosition);
    };
  }, [isOpen]);

  // Clean timeouts on unmount
  useEffect(() => {
    return () => {
      if (closeTimeoutRef.current) {
        clearTimeout(closeTimeoutRef.current);
      }
    };
  }, []);

  return (
    <>
      <button
        ref={triggerRef}
        onMouseEnter={handleMouseEnterTrigger}
        onMouseLeave={handleMouseLeaveTrigger}
        onClick={() => setIsDetailsOpen(true)}
        className="text-cyan-600 hover:text-cyan-500 hover:underline font-bold tracking-tight text-left focus:outline-none cursor-pointer"
      >
        #{poNumber}
      </button>

      {/* Render the Hover Card inside Portal to overlay correctly at screen level */}
      {isOpen && !isDetailsOpen && typeof document !== 'undefined' && createPortal(
        <div
          ref={hoverCardRef}
          onMouseEnter={handleMouseEnterCard}
          onMouseLeave={handleMouseLeaveCard}
          style={{
            position: 'absolute',
            top: `${coords.top}px`,
            left: `${coords.left}px`,
            zIndex: 9999
          }}
          className={`animate-in fade-in zoom-in-95 duration-150 origin-${placement === 'top' ? 'bottom' : 'top'}`}
        >
          <PurchaseOrderHoverCard
            data={data}
            isLoading={isLoading}
            isError={isError}
            copied={copied}
            onCopy={handleCopy}
            onViewDetails={() => {
              setIsOpen(false);
              setIsDetailsOpen(true);
            }}
          />
        </div>,
        document.body
      )}

      {/* Render the detailed items modal overlay */}
      <PurchaseOrderDetailsModal
        id={id}
        isOpen={isDetailsOpen}
        onClose={() => setIsDetailsOpen(false)}
      />
    </>
  );
};
