import apiClient from './apiClient';

export async function openPurchaseOrderPdf(id: number, mode: 'current' | 'revised' | 'delivery' = 'current'): Promise<void> {
  // Open during the click so browser popup protection permits the new tab.
  const pdfWindow = window.open('about:blank', '_blank');
  if (!pdfWindow) throw new Error('Allow popups to open the purchase order PDF.');
  pdfWindow.opener = null;
  pdfWindow.document.title = 'Purchase Order PDF';
  pdfWindow.document.body.textContent = 'Generating purchase order PDF…';

  try {
    const response = await apiClient.get<Blob>(`/purchase-orders/${id}/pdf`, {
      params: { mode },
      responseType: 'blob',
      headers: { Accept: 'application/pdf' },
    });
    if (!response.data.type.includes('application/pdf')) {
      throw new Error('The server returned an unexpected response instead of a PDF.');
    }
    if (pdfWindow.closed) return;
    const url = URL.createObjectURL(response.data);
    pdfWindow.location.replace(url);
    const cleanup = window.setInterval(() => {
      if (pdfWindow.closed) {
        URL.revokeObjectURL(url);
        window.clearInterval(cleanup);
      }
    }, 10000);
  } catch (error) {
    pdfWindow.close();
    throw error;
  }
}
