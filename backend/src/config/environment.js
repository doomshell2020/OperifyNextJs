const path = require('node:path');
require('dotenv').config({ path: path.join(__dirname, '../../.env') });

function required(name) {
  const value = process.env[name]?.trim();
  if (!value) throw new Error(`${name} must be set in the backend environment`);
  return value;
}

function httpUrl(name) {
  const value = required(name);
  const url = new URL(value);
  if (!['http:', 'https:'].includes(url.protocol)) {
    throw new Error(`${name} must be an HTTP or HTTPS URL`);
  }
  return value.replace(/\/+$/, '');
}

const PORT = Number(required('PORT'));
if (!Number.isInteger(PORT) || PORT < 1 || PORT > 65535) {
  throw new Error('PORT must be an integer between 1 and 65535');
}
const FRONTEND_URL = httpUrl('FRONTEND_URL');
const BACKEND_URL = httpUrl('BACKEND_URL');
const DB_HOST = required('DB_HOST');
const CORS_ORIGINS = [...new Set([
  new URL(FRONTEND_URL).origin,
  ...(process.env.CORS_ORIGINS || '').split(',').map(value => value.trim()).filter(Boolean).map(value => new URL(value).origin),
])];

module.exports = { PORT, FRONTEND_URL, BACKEND_URL, DB_HOST, CORS_ORIGINS };
