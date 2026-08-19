'use client';

import React, { useState, useEffect } from 'react';
import { UploadCloud, CheckCircle2, AlertCircle, Save } from 'lucide-react';
import apiClient from '../../../../services/apiClient';
import { DatePicker } from '../../../../components/ui/DatePicker';

export default function AdminProfilePage() {
  const [file, setFile] = useState<File | null>(null);
  const [preview, setPreview] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState('');
  const [error, setError] = useState('');
  
  const [profileData, setProfileData] = useState({
    first_name: '',
    last_name: '',
    phone: '',
    email: '',
    address1: '',
    address2: '',
    fax: '',
    website: '',
    status: 'Y',
    company_name: '',
    pan_number: '',
    gst_no: '',
    tin_date: '',
    account_number: '',
    ifsc: '',
    address: '',
    company_number: '',
    alias: ''
  });

  useEffect(() => {
    // Fetch current logo
    apiClient.get('/settings/logo')
      .then(res => {
        if (res.data.success && res.data.logoUrl) {
          if (res.data.logoUrl.startsWith('http')) {
            setPreview(res.data.logoUrl);
          } else {
            setPreview(`http://localhost:5000${res.data.logoUrl}`);
          }
        }
      })
      .catch(err => console.error('Failed to load logo:', err));

    // Fetch full profile data
    apiClient.get('/settings/profile')
      .then(res => {
        if (res.data.success && res.data.profile) {
          const p = res.data.profile;
          setProfileData({
            first_name: p.first_name || '',
            last_name: p.last_name || '',
            phone: p.phone || '',
            email: p.contact_email || p.email || '',
            address1: p.address1 || '',
            address2: p.address2 || '',
            fax: p.fax || '',
            website: p.website || '',
            status: p.status || 'Y',
            company_name: p.company_name || '',
            pan_number: p.pan_number || '',
            gst_no: p.gst_no || '',
            tin_date: p.tin_date ? new Date(p.tin_date).toISOString().split('T')[0] : '',
            account_number: p.account_number || '',
            ifsc: p.ifsc || '',
            address: p.address || '',
            company_number: p.affiliation_no || '', // mapped from affiliation_no
            alias: p.alias || ''
          });
        }
      })
      .catch(err => console.error('Failed to load profile details:', err));
  }, []);

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files.length > 0) {
      const selected = e.target.files[0];
      setFile(selected);
      setPreview(URL.createObjectURL(selected));
      setSuccess('');
      setError('');
    }
  };

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setProfileData(prev => ({ ...prev, [name]: value }));
  };

  const handleUploadAndSave = async () => {
    setLoading(true);
    setSuccess('');
    setError('');

    try {
      // 1. Save Text Profile Data
      await apiClient.put('/settings/profile', {
        ...profileData,
        contact_email: profileData.email // map frontend field to backend if needed
      });

      // 2. Upload Logo if selected
      if (file) {
        const formData = new FormData();
        formData.append('logo', file);
        await apiClient.post('/settings/upload-logo', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
      }

      setSuccess('Profile updated successfully!');
      
      // Reload page only if logo changed to update sidebar/cache
      if (file) {
        setTimeout(() => window.location.reload(), 1000);
      }
    } catch (err: any) {
      setError(err.response?.data?.message || 'An error occurred during update.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="max-w-7xl mx-auto py-6">
      <div className="mb-6 flex justify-between items-end">
        <div>
          <h1 className="text-2xl font-bold text-slate-900 tracking-tight">Profile Manager</h1>
          <p className="text-slate-500 mt-1 text-sm">Manage your company profile and application settings.</p>
        </div>
      </div>

      <div className="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
        <div className="p-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1">First Name</label>
              <input type="text" name="first_name" value={profileData.first_name} onChange={handleInputChange} className="w-full p-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition-all" />
            </div>
            
            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1">Last Name</label>
              <input type="text" name="last_name" value={profileData.last_name} onChange={handleInputChange} className="w-full p-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition-all" placeholder="Last Name" />
            </div>
            
            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1">Phone</label>
              <input type="text" name="phone" value={profileData.phone} onChange={handleInputChange} className="w-full p-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition-all" />
            </div>
            
            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1">Email</label>
              <input type="email" name="email" value={profileData.email} onChange={handleInputChange} className="w-full p-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition-all" />
            </div>
            
            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1">Address Line 1</label>
              <input type="text" name="address1" value={profileData.address1} onChange={handleInputChange} className="w-full p-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition-all" />
            </div>
            
            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1">Address Line 2</label>
              <input type="text" name="address2" value={profileData.address2} onChange={handleInputChange} className="w-full p-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition-all" placeholder="Address" />
            </div>
            
            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1">Fax</label>
              <input type="text" name="fax" value={profileData.fax} onChange={handleInputChange} className="w-full p-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition-all" placeholder="Fax" />
            </div>
            
            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1">Web Site</label>
              <input type="text" name="website" value={profileData.website} onChange={handleInputChange} className="w-full p-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition-all" />
            </div>

            {/* Logo and Stock Update */}
            <div className="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
              <div>
                <label className="block text-sm font-medium text-slate-700 mb-2">Small Logo</label>
                <div className="flex items-center gap-3">
                  <label className="flex items-center justify-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg cursor-pointer border border-slate-300 transition-colors">
                    Choose File
                    <input type="file" className="hidden" accept="image/*" onChange={handleFileChange} />
                  </label>
                  <span className="text-sm text-slate-500 truncate max-w-[200px]">
                    {file ? file.name : 'No file chosen'}
                  </span>
                </div>
                <p className="text-xs text-rose-500 mt-2 font-medium">Note:- Please select image size 100*100px</p>
                {preview && (
                  <div className="mt-3 w-16 h-16 rounded border border-slate-200 overflow-hidden bg-slate-50 flex items-center justify-center">
                    <img src={preview} alt="Logo Preview" className="max-w-full max-h-full object-contain" />
                  </div>
                )}
              </div>

              <div>
                <label className="block text-sm font-medium text-slate-700 mb-3">Stock Update</label>
                <div className="flex items-center gap-6">
                  <label className="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="status" value="Y" checked={profileData.status === 'Y'} onChange={handleInputChange} className="w-4 h-4 text-cyan-600 focus:ring-cyan-500 border-slate-300" />
                    <span className="text-sm text-slate-700">Y</span>
                  </label>
                  <label className="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="status" value="N" checked={profileData.status === 'N'} onChange={handleInputChange} className="w-4 h-4 text-cyan-600 focus:ring-cyan-500 border-slate-300" />
                    <span className="text-sm text-slate-700">N</span>
                  </label>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div className="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div className="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
          <h2 className="text-lg font-medium text-slate-800">Taxsection</h2>
        </div>
        <div className="p-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1">Company Name</label>
              <input type="text" name="company_name" value={profileData.company_name} onChange={handleInputChange} className="w-full p-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition-all" />
            </div>

            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1">Pan No</label>
              <input type="text" name="pan_number" value={profileData.pan_number} onChange={handleInputChange} className="w-full p-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition-all" />
            </div>

            <div className="md:col-start-2">
              <label className="block text-sm font-medium text-slate-700 mb-1">GST No</label>
              <input type="text" name="gst_no" value={profileData.gst_no} onChange={handleInputChange} className="w-full p-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition-all" />
            </div>

            <div className="md:col-start-1 md:row-start-3">
              <label className="block text-sm font-medium text-slate-700 mb-1">Tin Date</label>
              <DatePicker name="tin_date" value={profileData.tin_date} onChange={handleInputChange} className="w-full p-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition-all text-slate-700" />
            </div>

            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1">Account No</label>
              <input type="text" name="account_number" value={profileData.account_number} onChange={handleInputChange} className="w-full p-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition-all" />
            </div>

            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1">IFSC Code</label>
              <input type="text" name="ifsc" value={profileData.ifsc} onChange={handleInputChange} className="w-full p-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition-all" />
            </div>

            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1">Address</label>
              <input type="text" name="address" value={profileData.address} onChange={handleInputChange} className="w-full p-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition-all" placeholder="Address" />
            </div>

            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1">Company Number</label>
              <input type="text" name="company_number" value={profileData.company_number} onChange={handleInputChange} className="w-full p-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition-all" placeholder="Company Number" />
            </div>

            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1">Alias</label>
              <input type="text" name="alias" value={profileData.alias} onChange={handleInputChange} className="w-full p-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition-all" />
            </div>
            
          </div>
        </div>
      </div>

      {success && (
        <div className="mb-6 p-4 bg-emerald-50 text-emerald-700 text-sm rounded-xl flex items-start gap-2 border border-emerald-100 shadow-sm">
          <CheckCircle2 className="w-5 h-5 mt-0.5 shrink-0" />
          <p>{success}</p>
        </div>
      )}
      
      {error && (
        <div className="mb-6 p-4 bg-rose-50 text-rose-700 text-sm rounded-xl flex items-start gap-2 border border-rose-100 shadow-sm">
          <AlertCircle className="w-5 h-5 mt-0.5 shrink-0" />
          <p>{error}</p>
        </div>
      )}

      <div className="flex justify-between items-center mb-10">
        <button className="px-6 py-2.5 bg-cyan-500 hover:bg-cyan-600 text-white font-medium rounded-lg shadow-sm transition-colors">
          Cancel
        </button>
        <button
          onClick={handleUploadAndSave}
          disabled={loading}
          className="px-8 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg shadow-sm transition-colors flex items-center gap-2 disabled:opacity-50"
        >
          {loading ? 'Saving...' : 'Update'}
        </button>
      </div>

    </div>
  );
}
