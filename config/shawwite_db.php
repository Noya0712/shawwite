<?php
/**
 * Konfigurasi Koneksi Database MySQL — ShawWite Fruits Production
 * Database: shawwite
 */

declare(strict_types=1);

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'shawwite');
define('DB_USER', 'root');
define('DB_PASS', '');

function getDbConnection(): PDO {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        // Jika database belum terinisialisasi, coba buat dan jalankan file SQL
        $rootDsn = sprintf('mysql:host=%s;port=%s;charset=utf8mb4', DB_HOST, DB_PORT);
        $rootPdo = new PDO($rootDsn, DB_USER, DB_PASS, $options);
        $sqlFile = __DIR__ . '/../database/shawwite.sql';
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            $rootPdo->exec($sql);
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } else {
            throw $e;
        }
    }

    return $pdo;
}
