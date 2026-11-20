#!/usr/bin/env node
const { execSync } = require('child_process');
const path = require('path');
const fs = require('fs');
const os = require('os');

const rootDir = path.resolve(__dirname, '..');
const frontendDir = path.join(rootDir, 'frontend');

const homeDir = os.homedir();
const possibleKeyPaths = [
  path.join(homeDir, '.ssh', 'idsprime-superadmin-node.pem'),
  'D:\\PPK\\idsprime-superadmin-node.pem',
];

const keyPath = possibleKeyPaths.find((p) => fs.existsSync(p));
if (!keyPath) {
  console.error('❌ Could not find idsprime-superadmin-node.pem key file.');
  process.exit(1);
}

const SSH_HOST = process.env.SSH_HOST || '13.206.225.226';
const SSH_USER = process.env.SSH_USER || 'ubuntu';

console.log('====================================================');
console.log('🚀 Starting Local 1-Click Frontend Build & Deploy');
console.log('====================================================');
console.log(`Server: ${SSH_USER}@${SSH_HOST}`);
console.log(`Key:    ${keyPath}`);

try {
  // 1. Build frontend locally
  console.log('\n📦 Step 1: Building Next.js frontend locally...');
  execSync('npm run build', {
    cwd: frontendDir,
    stdio: 'inherit',
    env: {
      ...process.env,
      NEXT_PUBLIC_API_URL: process.env.NEXT_PUBLIC_API_URL || 'https://api.operify.in',
      NEXT_PUBLIC_APP_URL: process.env.NEXT_PUBLIC_APP_URL || 'https://new.operify.in',
    },
  });

  // 2. Compress .next
  console.log('\n🗜️  Step 2: Compressing pre-built bundle...');
  const bundlePath = path.join(frontendDir, 'next-bundle.tar.gz');
  if (fs.existsSync(bundlePath)) {
    fs.unlinkSync(bundlePath);
  }

  execSync('tar --exclude=.next/cache --exclude=.next/dev -czf next-bundle.tar.gz .next', {
    cwd: frontendDir,
    stdio: 'inherit',
  });

  // 3. Upload to server
  console.log('\n📤 Step 3: Transferring bundle to server...');
  execSync(
    `scp -i "${keyPath}" -o StrictHostKeyChecking=accept-new "${bundlePath}" ${SSH_USER}@${SSH_HOST}:/tmp/operify-frontend-next.tar.gz`,
    { stdio: 'inherit' }
  );

  // 4. Run remote deploy script
  console.log('\n⚡ Step 4: Activating new build on server...');
  const deployScript = path.join(rootDir, 'deploy', 'deploy-frontend.sh');
  execSync(
    `ssh -i "${keyPath}" -o StrictHostKeyChecking=accept-new ${SSH_USER}@${SSH_HOST} "bash -s" < "${deployScript}"`,
    { stdio: 'inherit' }
  );

  // 5. Cleanup local archive
  if (fs.existsSync(bundlePath)) {
    fs.unlinkSync(bundlePath);
  }

  console.log('\n🎉 SUCCESS: Frontend deployed cleanly with ZERO server load!');
} catch (err) {
  console.error('\n❌ Deployment failed:', err.message);
  process.exit(1);
}
