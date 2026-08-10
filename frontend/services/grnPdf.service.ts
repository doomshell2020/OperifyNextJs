export const grnPdfService = {
  downloadGrnPdf: async (id: string | number) => {
    try {
      const token = localStorage.getItem('accessToken');
      const response = await fetch(`/api/purchase/grn/${id}/pdf`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/pdf',
          ...(token ? { 'Authorization': `Bearer ${token}` } : {})
        },
      });

      if (!response.ok) {
        throw new Error('Failed to generate PDF');
      }

      const blob = await response.blob();
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `GRN_Details_${id}.pdf`);
      document.body.appendChild(link);
      link.click();
      link.parentNode?.removeChild(link);
      window.URL.revokeObjectURL(url);
      
      return true;
    } catch (error) {
      console.error('Error downloading GRN PDF:', error);
      throw error;
    }
  },
};
