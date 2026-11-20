#!/usr/bin/env bash
set -Eeuo pipefail

APP_DIR="/var/www/OperifyNextJs"

echo "=========================================="
echo " Starting Zero-Downtime Backend Deploy    "
echo "=========================================="

echo "--> Syncing source repository..."
cd "$APP_DIR"
git stash || true
git fetch origin main
git reset --hard origin/main
git stash pop || true

echo "--> Checking backend dependencies..."
if [ -f "$APP_DIR/backend/package.json" ]; then
  PUPPETEER_SKIP_DOWNLOAD=true npm install --prefix "$APP_DIR/backend" --omit=dev --no-audit --prefer-offline || true
fi

echo "--> Reloading operify-backend via PM2..."
pm2 reload operify-backend --update-env
pm2 save

echo "--> Verifying backend health..."
sleep 2
if curl --fail --silent --max-time 10 "http://127.0.0.1:6002/health" >/dev/null; then
  echo "===> SUCCESS: Backend deployment healthy on http://127.0.0.1:6002! <==="
else
  echo "===> WARNING: Backend health check failed! Inspect 'pm2 logs operify-backend' <==="
  exit 1
fi
