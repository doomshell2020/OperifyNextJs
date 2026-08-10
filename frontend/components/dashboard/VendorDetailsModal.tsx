"use client";

import React, { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import vendorService, { Vendor } from '../../services/vendor.service';
import { Loader, AlertCircle, X, Edit, Save, Check } from 'lucide-react';

interface VendorDetailsModalProps {
  vendorId: number;
  onClose: () => void;
}

export function VendorDetailsModal({ vendorId, onClose }: VendorDetailsModalProps) {
  const queryClient = useQueryClient();
  const [isEditing, setIsEditing] = useState(false);
  const [formData, setFormData] = useState<Partial<Vendor>>({});

  const { data: vendor, isLoading, isError } = useQuery({
    queryKey: ['vendor-details', vendorId],
    queryFn: () => vendorService.getDetails(vendorId),
    enabled: !!vendorId,
  });

  const updateMutation = useMutation({
    mutationFn: (data: Partial<Vendor>) => vendorService.updateVendor(vendorId, data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['vendor-details', vendorId] });
      queryClient.invalidateQueries({ queryKey: ['purchase-orders'] });
      setIsEditing(false);
    }
  });

  const handleEditClick = () => {
    if (vendor) {
      setFormData(vendor);
      setIsEditing(true);
    }
  };

  const handleSave = () => {
    updateMutation.mutate(formData);
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    setFormData(prev => ({
      ...prev,
      [e.target.name]: e.target.value
    }));
  };

  if (!vendorId) return null;

  return (
    <div className="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm animate-in fade-in duration-200">
      <div className="bg-white border border-slate-200 shadow-2xl rounded-2xl max-w-2xl w-full p-6 flex flex-col relative overflow-hidden animate-in zoom-in-95 duration-200 max-h-[90vh]">
        
        <div className="absolute top-4 right-4 z-10 flex gap-2">
          {!isEditing ? (
            <button
              onClick={handleEditClick}
              className="flex items-center gap-1.5 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded font-bold shadow-sm transition cursor-pointer text-sm"
            >
              <Edit className="w-4 h-4" />
              Edit
            </button>
          ) : (
            <button
              onClick={handleSave}
              disabled={updateMutation.isPending}
              className="flex items-center gap-1.5 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded font-bold shadow-sm transition cursor-pointer text-sm disabled:opacity-50"
            >
              {updateMutation.isPending ? <Loader className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
              Save
            </button>
          )}
          <button
            onClick={onClose}
            className="p-2 text-slate-400 hover:text-slate-700 bg-white border border-slate-200 rounded transition cursor-pointer shadow-sm"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        <h2 className="text-xl font-bold text-slate-800 mb-6">
          Vendor Details
        </h2>

        {isLoading && (
          <div className="flex flex-col items-center justify-center py-20 text-slate-500">
            <Loader className="w-8 h-8 animate-spin mb-4 text-blue-500" />
            <p>Loading vendor information...</p>
          </div>
        )}

        {isError && (
          <div className="flex flex-col items-center justify-center py-20 text-red-500 bg-red-50 rounded-xl">
            <AlertCircle className="w-10 h-10 mb-2" />
            <p className="font-semibold">Failed to load vendor</p>
          </div>
        )}

        {vendor && !isLoading && !isError && (
          <div className="flex-1 overflow-y-auto pr-2 custom-scrollbar">
            <div className="grid grid-cols-2 gap-4">
              <div className="col-span-2">
                <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Company Name</label>
                {isEditing ? (
                  <input type="text" name="name" value={formData.name || ''} onChange={handleChange} className="w-full border p-2 rounded" />
                ) : (
                  <p className="font-medium text-slate-900">{vendor.name}</p>
                )}
              </div>
              <div className="col-span-2">
                <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Address</label>
                {isEditing ? (
                  <textarea name="address" value={formData.address || ''} onChange={handleChange} className="w-full border p-2 rounded" rows={2} />
                ) : (
                  <p className="text-slate-700">{vendor.address || 'N/A'}</p>
                )}
              </div>
              <div>
                <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Contact Person</label>
                {isEditing ? (
                  <input type="text" name="contact_person" value={formData.contact_person || ''} onChange={handleChange} className="w-full border p-2 rounded" />
                ) : (
                  <p className="text-slate-700">{vendor.contact_person || 'N/A'}</p>
                )}
              </div>
              <div>
                <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Contact No</label>
                {isEditing ? (
                  <input type="text" name="contact_no" value={formData.contact_no || ''} onChange={handleChange} className="w-full border p-2 rounded" />
                ) : (
                  <p className="text-slate-700">{vendor.contact_no || 'N/A'}</p>
                )}
              </div>
              <div>
                <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Email</label>
                {isEditing ? (
                  <input type="email" name="email" value={formData.email || ''} onChange={handleChange} className="w-full border p-2 rounded" />
                ) : (
                  <p className="text-slate-700">{vendor.email || 'N/A'}</p>
                )}
              </div>
              <div>
                <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">GST Number</label>
                {isEditing ? (
                  <input type="text" name="gst_number" value={formData.gst_number || ''} onChange={handleChange} className="w-full border p-2 rounded" />
                ) : (
                  <p className="text-slate-700">{vendor.gst_number || 'N/A'}</p>
                )}
              </div>
              <div>
                <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">PAN Card</label>
                {isEditing ? (
                  <input type="text" name="pancard_number" value={formData.pancard_number || ''} onChange={handleChange} className="w-full border p-2 rounded" />
                ) : (
                  <p className="text-slate-700">{vendor.pancard_number || 'N/A'}</p>
                )}
              </div>
              <div>
                <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">TIN No</label>
                {isEditing ? (
                  <input type="text" name="tin_no" value={formData.tin_no || ''} onChange={handleChange} className="w-full border p-2 rounded" />
                ) : (
                  <p className="text-slate-700">{vendor.tin_no || 'N/A'}</p>
                )}
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
