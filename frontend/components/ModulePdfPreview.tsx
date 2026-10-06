'use client';
import { useEffect, useState } from 'react';
import apiClient from '@/services/apiClient';

export default function ModulePdfPreview({ path }: { path: string }) {
  const [result, setResult] = useState({ path: '', url: '', failed: false });
  const url = result.path === path ? result.url : '';
  const failed = result.path === path && result.failed;
  useEffect(() => {
    let active = true;
    let objectUrl = '';
    apiClient.get(path, { responseType:'blob' }).then(response => {
      if (!active) return;
      objectUrl = URL.createObjectURL(new Blob([response.data], {type:'application/pdf'}));
      setResult({ path, url: objectUrl, failed: false });
    }).catch(() => { if (active) setResult({ path, url: '', failed: true }); });
    return () => { active = false; if (objectUrl) URL.revokeObjectURL(objectUrl); };
  }, [path]);
  if (failed) return <p role="alert">Unable to load the PDF. Please refresh to try again.</p>;
  if (!url) return <p>Preparing PDF...</p>;
  return <div className="w-full"><a href={url} target="_blank" rel="noreferrer" className="inline-block mb-3 underline">Open PDF to print or save</a><iframe src={url} title="PDF preview" style={{width:'100%', height:'85vh',border:0}} /></div>;
}
