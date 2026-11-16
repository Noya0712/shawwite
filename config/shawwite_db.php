<?php
/**
 * Konfigurasi Koneksi Database MySQL — ShawWite Fruits Production
 * Database: shawwite
 */

declare(strict_types=1);

if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: (getenv('MYSQLHOST') ?: '127.0.0.1'));
if (!defined('DB_PORT')) define('DB_PORT', getenv('DB_PORT') ?: (getenv('MYSQLPORT') ?: '3306'));
if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_DATABASE') ?: (getenv('MYSQLDATABASE') ?: 'shawwite'));
if (!defined('DB_USER')) define('DB_USER', getenv('DB_USERNAME') ?: (getenv('MYSQLUSER') ?: 'root'));
if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASSWORD') ?: (getenv('MYSQLPASSWORD') ?: ''));

if (!function_exists('getDbConnection')) {
    function getDbConnection(): PDO {
        static $pdo = null;
        if ($pdo !== null) {
            return $pdo;
        }

        $host = getenv('DB_HOST') ?: (getenv('MYSQLHOST') ?: DB_HOST);
        $port = getenv('DB_PORT') ?: (getenv('MYSQLPORT') ?: DB_PORT);
        $name = getenv('DB_DATABASE') ?: (getenv('MYSQLDATABASE') ?: DB_NAME);
        $user = getenv('DB_USERNAME') ?: (getenv('MYSQLUSER') ?: DB_USER);
        $pass = getenv('DB_PASSWORD') ?: (getenv('MYSQLPASSWORD') ?: DB_PASS);

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name);
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            // Jika di localhost dan database belum ada, coba buat
            try {
                $rootDsn = sprintf('mysql:host=%s;port=%s;charset=utf8mb4', $host, $port);
                $rootPdo = new PDO($rootDsn, $user, $pass, $options);
                $sqlFile = __DIR__ . '/../database/shawwite.sql';
                if (file_exists($sqlFile)) {
                    $sql = file_get_contents($sqlFile);
                    $rootPdo->exec($sql);
                    $pdo = new PDO($dsn, $user, $pass, $options);
                } else {
                    throw $e;
                }
            } catch (Throwable $e2) {
                throw $e;
            }
        }

        return $pdo;
    }
}

return [
    'host'     => DB_HOST,
    'port'     => DB_PORT,
    'database' => DB_NAME,
    'username' => DB_USER,
    'password' => DB_PASS,
];
