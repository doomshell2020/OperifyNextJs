"use client";

import React, { useState, useEffect } from 'react';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import purchaseOrderService, { PurchaseOrderListItem } from '../../services/purchaseOrder.service';
import { useAuth } from '../../contexts/AuthContext';
import { Loader, X } from 'lucide-react';
import toast from 'react-hot-toast';

interface ClosePurchaseOrderModalProps {
  po: PurchaseOrderListItem;
  onClose: () => void;
}

export function ClosePurchaseOrderModal({ po, onClose }: ClosePurchaseOrderModalProps) {
  const queryClient = useQueryClient();
  const { user } = useAuth();
  const [closedBy, setClosedBy] = useState(user?.user_name || '');
  const [closeRemarks, setCloseRemarks] = useState('');
  const [errorMsg, setErrorMsg] = useState('');

  useEffect(() => {
    if (user?.user_name && !closedBy) {
      setClosedBy(user.user_name);
    }
  }, [user, closedBy]);

  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape') onClose();
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [onClose]);

  const closeMutation = useMutation({
    mutationFn: (data: { closed_by: string; close_remarks: string }) =>
      purchaseOrderService.closePurchaseOrder(po.id, data),
    onSuccess: (res: any) => {
      toast.success(res?.message || `Purchase Order (${po.po_number}) closed without GRN successfully.`);
      queryClient.invalidateQueries({ queryKey: ['purchase-orders'] });
      queryClient.invalidateQueries({ queryKey: ['purchase-order-details', po.id] });
      onClose();
    },
    onError: (err: any) => {
      const msg = err?.response?.data?.message || err?.message || 'Failed to close Purchase Order';
      setErrorMsg(msg);
      toast.error(msg);
    }
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const trimmedClosedBy = closedBy.trim();
    const trimmedRemarks = closeRemarks.trim();

    if (!trimmedClosedBy || !trimmedRemarks) {
      setErrorMsg('Both Closed By and Close Remarks are mandatory fields.');
      toast.error('Both Closed By and Close Remarks are mandatory fields.');
      return;
    }

    closeMutation.mutate({
      closed_by: trimmedClosedBy,
      close_remarks: trimmedRemarks
    });
  };

  return (
    <div
      className="fixed inset-0 z-[10000] flex justify-center items-start overflow-y-auto bg-black/50 pt-10 pb-10"
      onClick={(e) => {
        if (e.target === e.currentTarget) onClose();
      }}
    >
      <div
        className="bg-white rounded-lg shadow-xl relative overflow-hidden w-[95%] max-w-[800px] border border-slate-200"
        role="dialog"
        aria-modal="true"
        aria-labelledby="closePoModalLabel"
      >
        {/* Modal Header */}
        <div
          className="px-6 py-4 flex justify-between items-center"
          style={{ backgroundColor: '#3399CC', color: 'white' }}
        >
          <h4
            id="closePoModalLabel"
            className="text-lg font-semibold text-white m-0"
            style={{ color: 'white', fontWeight: 600 }}
          >
            Close Purchase Order
          </h4>
          <button
            type="button"
            onClick={onClose}
            className="text-white hover:opacity-80 transition p-1 focus:outline-none"
            aria-label="Close"
            style={{ color: 'white' }}
          >
            <X className="w-5 h-5 text-white" />
          </button>
        </div>

        {/* Modal Body */}
        <div className="p-6">
          <form onSubmit={handleSubmit} id="closePoForm">
            {errorMsg && (
              <div className="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded text-sm">
                {errorMsg}
              </div>
            )}

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
              <div>
                <label className="block text-sm font-semibold text-slate-700 mb-1">
                  PO ID
                </label>
                <input
                  type="text"
                  id="close_po_number"
                  readOnly
                  value={po.po_number}
                  className="w-full px-3 py-2 border border-slate-300 rounded text-sm text-slate-800 font-bold focus:outline-none cursor-not-allowed"
                  style={{ backgroundColor: '#f9f9f9', fontWeight: 'bold' }}
                />
              </div>
              <div>
                <label className="block text-sm font-semibold text-slate-700 mb-1">
                  Vendor
                </label>
                <input
                  type="text"
                  id="close_po_vendor"
                  readOnly
                  value={po.vendor_name || ''}
                  className="w-full px-3 py-2 border border-slate-300 rounded text-sm text-slate-800 focus:outline-none cursor-not-allowed"
                  style={{ backgroundColor: '#f9f9f9' }}
                />
              </div>
            </div>

            <div className="mb-4">
              <label className="block text-sm font-semibold text-slate-700 mb-1">
                Closed By <span className="text-red-500">*</span>
              </label>
              <input
                type="text"
                name="closed_by"
                id="close_po_closed_by"
                value={closedBy}
                onChange={(e) => {
                  setClosedBy(e.target.value);
                  if (errorMsg) setErrorMsg('');
                }}
                placeholder="Enter Name of Person Closing PO"
                required
                className="w-full px-3 py-2 border border-slate-300 rounded text-sm text-slate-800 focus:border-[#3399CC] focus:ring-1 focus:ring-[#3399CC] focus:outline-none"
              />
            </div>

            <div className="mb-4">
              <label className="block text-sm font-semibold text-slate-700 mb-1">
                Close Remarks <span className="text-red-500">*</span>
              </label>
              <textarea
                name="close_remarks"
                id="close_po_remarks"
                value={closeRemarks}
                onChange={(e) => {
                  setCloseRemarks(e.target.value);
                  if (errorMsg) setErrorMsg('');
                }}
                placeholder="Enter Remarks"
                rows={3}
                required
                className="w-full px-3 py-2 border border-slate-300 rounded text-sm text-slate-800 focus:border-[#3399CC] focus:ring-1 focus:ring-[#3399CC] focus:outline-none"
              />
            </div>

            {/* Modal Footer / Actions */}
            <div className="pt-2">
              <button
                type="submit"
                disabled={closeMutation.isPending}
                title="Submit"
                className="btn btn-info px-4 py-2 text-white font-medium text-sm rounded shadow-sm hover:opacity-90 disabled:opacity-50 transition flex items-center justify-center gap-2"
                style={{
                  backgroundColor: '#3399CC',
                  color: '#fff',
                  minWidth: '90px',
                  margin: '10px 0px'
                }}
              >
                {closeMutation.isPending && (
                  <Loader className="w-4 h-4 animate-spin text-white" />
                )}
                Submit
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}
