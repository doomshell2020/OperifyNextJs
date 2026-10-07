const { spawnSync } = require('node:child_process');
const { join } = require('node:path');
process.chdir(join(__dirname, '..'));

function run(command, args, capture = false) {
  const result = spawnSync(command, args, {
    stdio: capture ? ['ignore', 'pipe', 'inherit'] : 'inherit',
    encoding: 'utf8',
  });
  if (result.error) console.error(result.error.message);
  if (result.error || result.status !== 0) process.exit(result.status || 1);
  return result.stdout?.trim();
}

const message = process.argv.slice(2).join(' ').trim();
if (!message) {
  console.error('Usage: npm run ship -- "Describe your changes"');
  process.exit(1);
}
const branch = run('git', ['branch', '--show-current'], true);
if (!branch) {
  console.error('Switch to a branch before shipping.');
  process.exit(1);
}
run('git', ['remote', 'get-url', 'origin'], true);
run('git', ['fetch', 'origin']);
// Refuse to push from a branch behind its remote; never merge automatically.
const remote = spawnSync('git', ['rev-parse', '--verify', `refs/remotes/origin/${branch}`], { stdio: 'ignore' });
if (remote.status === 0) {
  const behind = run('git', ['rev-list', '--count', `HEAD..origin/${branch}`], true);
  if (Number(behind) > 0) {
    console.error('Remote changes exist. Pull and resolve them before shipping.');
    process.exit(1);
  }
}
const npmCli = process.env.npm_execpath;
if (!npmCli) {
  console.error('Run this helper with npm run ship.');
  process.exit(1);
}
run(process.execPath, [npmCli, 'run', 'check']);
run('git', ['add', '--all']);
if (run('git', ['diff', '--cached', '--name-only'], true)) {
  run('git', ['commit', '-m', message]);
}
run('git', ['push', '--set-upstream', 'origin', branch]);
console.log(`Pushed ${branch}. Follow the automatic checks in GitHub Actions.`);
