const { readdirSync } = require('node:fs');
const { join } = require('node:path');
const { spawnSync } = require('node:child_process');

function check(directory) {
  for (const entry of readdirSync(directory, { withFileTypes: true })) {
    const path = join(directory, entry.name);
    if (entry.isDirectory()) check(path);
    else if (/\.(js|cjs)$/.test(entry.name)) {
      const result = spawnSync(process.execPath, ['--check', path], { stdio: 'inherit' });
      if (result.error || result.status !== 0) process.exit(result.status || 1);
    }
  }
}
check(join(__dirname, '..', 'backend', 'src'));
check(join(__dirname, '..', 'backend', 'scripts'));
console.log('Backend JavaScript syntax checks passed.');
