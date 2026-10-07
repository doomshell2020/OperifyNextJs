#!/usr/bin/env bash
set -Eeuo pipefail

app_root="${1:?Application root is required}"
release_id="${2:?Release ID is required}"
export PM2_FRONTEND_NAME="${3:-operify-frontend}"
export PM2_BACKEND_NAME="${4:-operify-backend}"
export FRONTEND_PORT="${5:-3000}"
[[ "$app_root" =~ ^/[a-zA-Z0-9_./-]+$ && "$app_root" != / && "$app_root" != *..* ]] || exit 1
[[ "$release_id" =~ ^[a-f0-9]{40}-[0-9]+-[0-9]+$ ]] || exit 1
[[ "$FRONTEND_PORT" =~ ^[0-9]+$ ]] && (( FRONTEND_PORT > 0 && FRONTEND_PORT < 65536 )) || exit 1
for command in node npm pm2 curl flock; do command -v "$command" >/dev/null; done
node -e 'const [a,b] = process.versions.node.split(".").map(Number); if(a < 22 || (a === 22 && b < 12)) process.exit(1)'

mkdir -p "$app_root/releases" "$app_root/shared/public"
exec 9>"$app_root/deploy.lock"
flock -w 900 9
[[ -f "$app_root/shared/backend.env" && -f "$app_root/shared/frontend.env" ]] || {
  echo 'Configure shared/backend.env and shared/frontend.env before deploying.' >&2
  exit 1
}
[[ ! -e "$app_root/current" || -L "$app_root/current" ]] || {
  echo 'The current path must be a release symlink, not an existing directory.' >&2
  exit 1
}
release="$app_root/releases/$release_id"
mkdir "$release"
tar -xzf "$app_root/incoming/$release_id.tar.gz" -C "$release"
ln -s "$app_root/shared/backend.env" "$release/backend/.env"
ln -s "$app_root/shared/frontend.env" "$release/frontend/.env.production.local"
# Seed repository logos without overwriting persistent uploaded files.
cp -an "$release/backend/public/." "$app_root/shared/public/"
mv "$release/backend/public" "$release/backend/bundled-public"
ln -s "$app_root/shared/public" "$release/backend/public"

export NODE_ENV=production
# npm needs dev dependencies to compile TypeScript and Next.js.
npm ci --prefix "$release/backend" --include=dev
npm ci --prefix "$release/frontend" --include=dev
(cd "$release/backend" && node -e 'require("./src/config/environment"); const path=require("node:path"); const r=require("dotenv").config({path:path.resolve("../frontend/.env.production.local")}); for(const key of ["NEXT_PUBLIC_API_URL","NEXT_PUBLIC_APP_URL"]){const u=new URL(r.parsed?.[key] || "");if(!["http:","https:"].includes(u.protocol))throw new Error("Invalid "+key)}')
(cd "$release" && npm run check)
(cd "$release/backend" && npm run pdf:check)
backend_port=$(cd "$release/backend" && node -e 'const r=require("dotenv").config({path:".env"}); const p=Number(r.parsed?.PORT); if(!Number.isInteger(p)||p<1||p>65535)process.exit(1); process.stdout.write(String(p))')
previous=$(readlink -f "$app_root/current" || true)
rollback() {
  local code=$?
  trap - ERR
  echo 'Deployment failed after activation.' >&2
  if [[ -n "$previous" && -f "$previous/deploy/ecosystem.config.cjs" ]]; then
    ln -sfn "$previous" "$app_root/current"
    pm2 startOrReload "$previous/deploy/ecosystem.config.cjs" --update-env || true
    pm2 save || true
    echo "Restored previous release: $previous" >&2
  else
    echo 'No previous managed release exists; check PM2 logs before retrying.' >&2
  fi
  exit "$code"
}
trap rollback ERR
ln -sfn "$release" "$app_root/current"
pm2 startOrReload "$release/deploy/ecosystem.config.cjs" --update-env
healthy=false
for attempt in {1..30}; do
  if curl --fail --silent --max-time 5 "http://127.0.0.1:$backend_port/health" >/dev/null &&
     curl --fail --silent --max-time 5 "http://127.0.0.1:$FRONTEND_PORT/login" >/dev/null; then
    healthy=true
    break
  fi
  sleep 2
done
[[ "$healthy" == true ]]
pm2 save
trap - ERR
echo "Deployment healthy: $release_id"
