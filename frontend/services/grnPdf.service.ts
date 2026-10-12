import apiClient from './apiClient';

async function getPdf(id: string | number): Promise<Blob> {
  const response = await apiClient.get<Blob>(`/grn/${encodeURIComponent(id)}/pdf`, {
    responseType: 'blob', headers: { Accept: 'application/pdf' },
  });
  if (!response.data.type.includes('application/pdf')) throw new Error('The server did not return a GRN PDF.');
  return response.data;
}

export const grnPdfService = {
  openGrnPdf: async (id: string | number) => {
    const pdfWindow = window.open('about:blank', '_blank');
    if (!pdfWindow) throw new Error('Allow popups to open the GRN PDF.');
    pdfWindow.opener = null;
    pdfWindow.document.title = 'GRN PDF';
    pdfWindow.document.body.textContent = 'Generating GRN PDF…';
    try {
      const blob = await getPdf(id);
      if (pdfWindow.closed) return;
      const url = URL.createObjectURL(blob);
      pdfWindow.location.replace(url);
      const cleanup = window.setInterval(() => {
        if (pdfWindow.closed) { URL.revokeObjectURL(url); window.clearInterval(cleanup); }
      }, 10000);
    } catch (error) { pdfWindow.close(); throw error; }
  },
  downloadGrnPdf: async (id: string | number) => {
    try {
      const blob = await getPdf(id);
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `GRN_Details_${id}.pdf`);
      document.body.appendChild(link);
      link.click();
      link.parentNode?.removeChild(link);
      window.setTimeout(() => window.URL.revokeObjectURL(url), 60000);
      
      return true;
    } catch (error) {
      console.error('Error downloading GRN PDF:', error);
      throw error;
    }
  },
};
