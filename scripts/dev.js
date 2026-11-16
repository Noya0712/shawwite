/**
 * ============================================================================
 * SHAWWITE FRUITS — LARAVEL 11 DEVELOPMENT SERVER (`npm run dev`)
 * File: scripts/dev.js
 * ============================================================================
 * Menjalankan server Laravel 11 (`php artisan serve`) yang terhubung langsung
 * ke database MySQL `shawwite` tanpa mengubah tampilan website sedikit pun.
 */

const { spawn } = require('child_process');
const path = require('path');
const fs = require('fs');
const net = require('net');

const ROOT_DIR = path.resolve(__dirname, '..');
const PUBLIC_DIR = path.join(ROOT_DIR, 'public');
const DEFAULT_PORT = parseInt(process.env.PORT || '3000', 10);

const XAMPP_PHP = 'C:\\xampp\\php\\php.exe';
const PHP_BIN = fs.existsSync(XAMPP_PHP) ? XAMPP_PHP : 'php';

// Sinkronisasi otomatis assets/ & logo.png ke dalam public/ agar Laravel melayani aset terbaru
try {
  const srcLogo = path.join(ROOT_DIR, 'logo.png');
  const destLogo = path.join(PUBLIC_DIR, 'logo.png');
  if (fs.existsSync(srcLogo)) {
    fs.copyFileSync(srcLogo, destLogo);
  }
  const srcAssets = path.join(ROOT_DIR, 'assets');
  const destAssets = path.join(PUBLIC_DIR, 'assets');
  if (fs.existsSync(srcAssets)) {
    fs.cpSync(srcAssets, destAssets, { recursive: true, force: true });
  }
} catch (e) {}

function findAvailablePort(startPort) {
  return new Promise((resolve) => {
    const server = net.createServer();
    server.listen(startPort, '127.0.0.1', () => {
      server.once('close', () => resolve(startPort));
      server.close();
    });
    server.on('error', () => {
      resolve(findAvailablePort(startPort + 1));
    });
  });
}

async function startLaravelServer() {
  const port = await findAvailablePort(DEFAULT_PORT);
  const host = 'localhost';
  const url = `http://${host}:${port}`;

  console.log('\n\x1b[36m======================================================================\x1b[0m');
  console.log('\x1b[1m\x1b[32m  🍎 SHAWWITE FRUITS — LARAVEL 11 FRAMEWORK SERVER\x1b[0m');
  console.log('\x1b[36m======================================================================\x1b[0m');
  console.log(`  \x1b[32m➜\x1b[0m  \x1b[1mKatalog Utama :\x1b[0m \x1b[36m${url}/\x1b[0m`);
  console.log(`  \x1b[32m➜\x1b[0m  \x1b[1mHalaman Login :\x1b[0m \x1b[36m${url}/login\x1b[0m`);
  console.log(`  \x1b[32m➜\x1b[0m  \x1b[1mHalaman Daftar:\x1b[0m \x1b[36m${url}/register\x1b[0m`);
  console.log(`  \x1b[32m➜\x1b[0m  \x1b[1mFramework     :\x1b[0m \x1b[35mLaravel 11.22 (Blade + MySQL shawwite)\x1b[0m`);
  console.log('\x1b[36m======================================================================\x1b[0m');
  console.log('  \x1b[90mTekan Ctrl+C untuk menghentikan server Laravel.\x1b[0m\n');

  const laravelProcess = spawn(PHP_BIN, ['artisan', 'serve', `--host=${host}`, `--port=${port}`], {
    cwd: ROOT_DIR,
    stdio: 'inherit'
  });

  laravelProcess.on('error', (err) => {
    console.error('\x1b[31mGagal menjalankan Laravel server:\x1b[0m', err.message);
    process.exit(1);
  });

  process.on('SIGINT', () => {
    laravelProcess.kill('SIGINT');
    process.exit(0);
  });
}

startLaravelServer();
