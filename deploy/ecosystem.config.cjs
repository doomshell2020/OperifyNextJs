const path = require('node:path');
const root = path.resolve(__dirname, '..');

module.exports = {
  apps: [
    {
      name: process.env.PM2_BACKEND_NAME || 'operify-backend',
      cwd: path.join(root, 'backend'),
      script: 'src/server.js',
      env: { NODE_ENV: 'production' },
      kill_timeout: 10000,
    },
    {
      name: process.env.PM2_FRONTEND_NAME || 'operify-frontend',
      cwd: path.join(root, 'frontend'),
      script: 'node_modules/next/dist/bin/next',
      args: `start --hostname 127.0.0.1 --port ${process.env.FRONTEND_PORT || '3000'}`,
      env: { NODE_ENV: 'production' },
      kill_timeout: 10000,
    },
  ],
};
