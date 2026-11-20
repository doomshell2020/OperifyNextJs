#!/usr/bin/env bash
set -Eeuo pipefail

APP_DIR="/var/www/OperifyNextJs"
BUNDLE_PATH="/tmp/operify-frontend-next.tar.gz"

echo "=========================================="
echo " Starting Zero-Downtime Frontend Deploy   "
echo "=========================================="

# 1. Update source code from Git
echo "--> Syncing source repository..."
cd "$APP_DIR"
git stash || true
git fetch origin main
git reset --hard origin/main
git stash pop || true

# 2. Check if pre-built bundle was transferred
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
else
  echo "--> No bundle found at $BUNDLE_PATH, proceeding with existing build."
fi

# 4. If package.json has changes, update production dependencies safely
if [ -f "$APP_DIR/frontend/package.json" ]; then
  echo "--> Ensuring frontend production packages are installed..."
  npm install --prefix "$APP_DIR/frontend" --omit=dev --no-audit --prefer-offline || true
fi

# 5. Reload PM2 frontend process
echo "--> Reloading operify-frontend via PM2..."
pm2 reload operify-frontend --update-env

# 6. If backend changes detected, reload backend too
if git diff --name-only HEAD~1 HEAD 2>/dev/null | grep -q '^backend/'; then
  echo "--> Backend changes detected, reloading operify-backend..."
  npm install --prefix "$APP_DIR/backend" --omit=dev --no-audit --prefer-offline || true
  pm2 reload operify-backend --update-env
fi

pm2 save

# 7. Verify health
echo "--> Running health check..."
sleep 2
if curl --fail --silent --max-time 10 "http://127.0.0.1:6001/login" >/dev/null; then
  echo "===> SUCCESS: Frontend deployment healthy & live on http://127.0.0.1:6001! <==="
else
  echo "===> WARNING: Health check failed! Inspect 'pm2 logs operify-frontend' <==="
  exit 1
fi
