'use client';

import React, { useState, useEffect } from 'react';
import { UploadCloud, CheckCircle2, AlertCircle } from 'lucide-react';
import apiClient from '../../../../services/apiClient';

export default function AdminProfilePage() {
  const [file, setFile] = useState<File | null>(null);
  const [preview, setPreview] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState('');
  const [error, setError] = useState('');
  
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

  const handleUpload = async () => {
    if (!file) return;
    setLoading(true);
    setSuccess('');
    setError('');

    const formData = new FormData();
    formData.append('logo', file);

    try {
      const response = await apiClient.post('/settings/upload-logo', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });

      if (response.data.success) {
        setSuccess('Logo updated successfully! The sidebar and PDFs will now use this logo.');
        window.location.reload(); // Reload to refresh sidebar state if not using global state
      } else {
        setError(response.data.message || 'Failed to upload logo');
      }
    } catch (err: any) {
      setError(err.response?.data?.message || 'An error occurred during upload.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="max-w-4xl mx-auto py-6">
      <div className="mb-8">
        <h1 className="text-2xl font-bold text-slate-900 tracking-tight">Admin Profile</h1>
        <p className="text-slate-500 mt-1 text-sm">Manage your company profile and application settings.</p>
      </div>

      <div className="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div className="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
          <h2 className="text-lg font-semibold text-slate-800">Company Logo</h2>
          <p className="text-slate-500 text-sm mt-0.5">This logo appears on the dashboard sidebar and all generated contract PDFs.</p>
        </div>
        
        <div className="p-6">
          <div className="flex flex-col md:flex-row gap-8 items-start">
            
            {/* Logo Preview Area */}
            <div className="w-full md:w-1/3">
              <p className="text-sm font-medium text-slate-700 mb-3">Current Logo Preview</p>
              <div className="aspect-[3/1] bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-center p-4">
                {preview ? (
                  <img src={preview} alt="Logo Preview" className="max-w-full max-h-full object-contain mix-blend-multiply" />
                ) : (
                  <span className="text-slate-400 text-sm">No logo</span>
                )}
              </div>
            </div>

            {/* Upload Area */}
            <div className="flex-1 w-full">
              <p className="text-sm font-medium text-slate-700 mb-3">Upload New Logo</p>
              
              <label className="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-300 border-dashed rounded-xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition-colors group">
                <div className="flex flex-col items-center justify-center pt-5 pb-6">
                  <UploadCloud className="w-8 h-8 text-slate-400 group-hover:text-cyan-500 mb-2 transition-colors" />
                  <p className="mb-1 text-sm text-slate-600"><span className="font-semibold text-cyan-600">Click to upload</span> or drag and drop</p>
                  <p className="text-xs text-slate-500">PNG, JPG or JPEG (Recommended: 300x100px)</p>
                </div>
                <input type="file" className="hidden" accept="image/png, image/jpeg, image/jpg" onChange={handleFileChange} />
              </label>

              {success && (
                <div className="mt-4 p-3 bg-emerald-50 text-emerald-700 text-sm rounded-lg flex items-start gap-2 border border-emerald-100">
                  <CheckCircle2 className="w-4 h-4 mt-0.5 shrink-0" />
                  <p>{success}</p>
                </div>
              )}
              
              {error && (
                <div className="mt-4 p-3 bg-rose-50 text-rose-700 text-sm rounded-lg flex items-start gap-2 border border-rose-100">
                  <AlertCircle className="w-4 h-4 mt-0.5 shrink-0" />
                  <p>{error}</p>
                </div>
              )}

              <div className="mt-6 flex justify-end">
                <button
                  onClick={handleUpload}
                  disabled={!file || loading}
                  className="bg-cyan-600 hover:bg-cyan-700 text-white font-medium py-2 px-6 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 shadow-sm"
                >
                  {loading ? 'Uploading...' : 'Save Logo'}
                </button>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  );
}
