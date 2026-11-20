#!/usr/bin/env bash
set -Eeuo pipefail

APP_DIR="/var/www/OperifyNextJs"
BUNDLE_PATH="/tmp/operify-frontend-next.tar.gz"

echo "=========================================="
echo " Starting Zero-Downtime Application Deploy"
echo "=========================================="

# 1. Update source code from Git safely
echo "--> Syncing source repository..."
cd "$APP_DIR"
git stash || true
git fetch origin main
git reset --hard origin/main
git stash pop || true

# 2. Check if pre-built frontend bundle was transferred
if [ -f "$BUNDLE_PATH" ]; then
  echo "--> Staging pre-built .next artifacts..."
  rm -rf "$APP_DIR/frontend/.next_staging"
  mkdir -p "$APP_DIR/frontend/.next_staging"
  tar -xzf "$BUNDLE_PATH" -C "$APP_DIR/frontend/.next_staging"

  # 3. Perform atomic directory swap
  echo "--> Performing atomic swap of .next directory..."
  rm -rf "$APP_DIR/frontend/.next_old"
  if [ -d "$APP_DIR/frontend/.next" ]; then
    mv "$APP_DIR/frontend/.next" "$APP_DIR/frontend/.next_old"
  fi
  mv "$APP_DIR/frontend/.next_staging/.next" "$APP_DIR/frontend/.next"
  rm -rf "$APP_DIR/frontend/.next_staging" "$APP_DIR/frontend/.next_old" "$BUNDLE_PATH"

  if [ -f "$APP_DIR/frontend/package.json" ]; then
    echo "--> Ensuring frontend production packages are installed..."
    npm install --prefix "$APP_DIR/frontend" --omit=dev --no-audit --prefer-offline || true
  fi

  echo "--> Reloading operify-frontend via PM2..."
  pm2 reload operify-frontend --update-env
fi

# 4. Check backend deployment
echo "--> Checking backend updates..."
if [ -f "$APP_DIR/backend/package.json" ]; then
  PUPPETEER_SKIP_DOWNLOAD=true npm install --prefix "$APP_DIR/backend" --omit=dev --no-audit --prefer-offline || true
fi

echo "--> Reloading operify-backend via PM2..."
pm2 reload operify-backend --update-env

pm2 save

# 5. Verify health of BOTH frontend and backend
echo "--> Running health checks..."
sleep 2

frontend_ok=false
if curl --fail --silent --max-time 10 "http://127.0.0.1:6001/login" >/dev/null; then
  echo "--> [OK] Frontend healthy on http://127.0.0.1:6001"
  frontend_ok=true
else
  echo "--> [WARN] Frontend health check failed!"
fi

backend_ok=false
if curl --fail --silent --max-time 10 "http://127.0.0.1:6002/health" >/dev/null; then
  echo "--> [OK] Backend healthy on http://127.0.0.1:6002"
  backend_ok=true
else
  echo "--> [WARN] Backend health check failed!"
fi

if [ "$frontend_ok" = true ] && [ "$backend_ok" = true ]; then
  echo "===> SUCCESS: Both Frontend and Backend are healthy & live! <==="
else
  echo "===> WARNING: Health check had warnings. Please inspect 'pm2 status'! <==="
fi
