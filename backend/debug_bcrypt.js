const bcrypt = require('bcryptjs');
async function run() {
  const password = 'Admin_2@25';
  const hash = '$2y$10$ZgUbZ0FkrproNEkSRoQ59etpsQBcPN0WvUsX/W7g6MfXPYalxyTUu';
  
  // PHP bcrypt uses $2y$, Node uses $2a$ - need to convert
  const nodeHash = hash.replace(/^\$2y\$/, '$2a$');
  
  const result = await bcrypt.compare(password, nodeHash);
  console.log('Password match (bcrypt):', result);
  console.log('Plaintext match:', password === 'Admin_2@25');
  process.exit(0);
}
run().catch(e => { console.error(e.message); process.exit(1); });
