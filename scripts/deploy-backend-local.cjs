#!/usr/bin/env node
const { execSync } = require('child_process');
const path = require('path');
const fs = require('fs');
const os = require('os');

const rootDir = path.resolve(__dirname, '..');

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
console.log('🚀 Starting Local 1-Click Backend Deploy');
console.log('====================================================');
console.log(`Server: ${SSH_USER}@${SSH_HOST}`);
console.log(`Key:    ${keyPath}`);

try {
  // 1. Syntax check backend locally
  console.log('\n🔍 Step 1: Validating backend syntax locally...');
  execSync('node scripts/check-backend.cjs', {
    cwd: rootDir,
    stdio: 'inherit',
  });

  // 2. Run remote deploy script for backend
  console.log('\n⚡ Step 2: Syncing code and reloading operify-backend on server...');
  const deployScript = path.join(rootDir, 'deploy', 'deploy-backend.sh');
  execSync(
    `ssh -i "${keyPath}" -o StrictHostKeyChecking=accept-new ${SSH_USER}@${SSH_HOST} "bash -s" < "${deployScript}"`,
    { stdio: 'inherit' }
  );

  console.log('\n🎉 SUCCESS: Backend deployed cleanly with ZERO disruption to other projects!');
} catch (err) {
  console.error('\n❌ Backend deployment failed:', err.message);
  process.exit(1);
}
