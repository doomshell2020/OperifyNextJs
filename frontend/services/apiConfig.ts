const configuredApiUrl = process.env.NEXT_PUBLIC_API_URL?.trim();

if (!configuredApiUrl) {
  throw new Error('NEXT_PUBLIC_API_URL must be set in the frontend environment');
}

// Accept a backend origin or the legacy origin ending in /api.
export const API_ASSET_URL = configuredApiUrl.replace(/\/+$/, '').replace(/\/api$/, '');
export const API_URL = `${API_ASSET_URL}/api`;
export const APP_URL = process.env.NEXT_PUBLIC_APP_URL?.trim().replace(/\/+$/, '');
export const DEFAULT_LOGO_URL = `${API_ASSET_URL}/public/uploads/logos/d80960ce77aede66a5c3c8eef8dfafda.png`;

export function resolveApiAssetUrl(path: string): string {
  if (/^https?:\/\//i.test(path)) return path;
  return `${API_ASSET_URL}/${path.replace(/^\/+/, '')}`;
}
