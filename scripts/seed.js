/**
 * ============================================================================
 * SHAWWITE FRUITS — DATABASE SEEDER (`npm run db:seed`)
 * File: scripts/seed.js
 * ============================================================================
 */

const { execFileSync } = require('child_process');
const path = require('path');

const PHP_BIN = 'C:\\xampp\\php\\php.exe';
const rootDir = path.resolve(__dirname, '..');

const phpCode = `
require '${rootDir.replace(/\\/g, '/')}/config/shawwite_db.php';
$pdo = getDbConnection();
$sql = file_get_contents('${rootDir.replace(/\\/g, '/')}/database/shawwite.sql');
$pdo->exec($sql);
echo "✅ Database MySQL 'shawwite' berhasil di-seed ulang dengan data dummy!\n";
`;

execFileSync(PHP_BIN, ['-r', phpCode], { stdio: 'inherit' });
