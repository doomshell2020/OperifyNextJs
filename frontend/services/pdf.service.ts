import apiClient from './apiClient';

/** Open the module's authenticated PDF in the browser's native PDF viewer. */
export async function openModulePdf(path: string): Promise<void> {
  const viewer = window.open('about:blank', '_blank');
  try {
    const response = await apiClient.get(path, { responseType: 'blob' });
    const url = URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
    if (viewer) viewer.location.replace(url);
    else window.location.assign(url);
    // Keep the object URL alive while the PDF viewer loads and supports saving/printing.
    window.setTimeout(() => URL.revokeObjectURL(url), 300_000);
  } catch {
    viewer?.close();
    alert('Unable to generate this PDF. Please try again.');
  }
}
