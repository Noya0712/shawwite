<?php
/**
 * ShawWite Fruits — Production REST API (MySQL `shawwite`)
 * File: api.php
 *
 * Fitur:
 * - Pengunjung umum (Guest) dapat melihat tampilan katalog/dashboard & ulasan produk.
 * - Pembelian (create_inquiry) & Rating/Komentar (review_save) mewajibkan Login terlebih dahulu.
 * - Login & Register (sesuai login.md & register.md) tersimpan langsung di tabel `users` MySQL.
 * - Admin: Mengelola User + Monitoring Chart perkembangan & aktivitas.
 * - Seller: Mengelola Produk + Monitoring Chart perkembangan & komentar dari produk yang dijual.
 * - Pembeli: Melihat produk, memilih & menghubungi Seller via WA, serta memberi rating & komentar.
 */

declare(strict_types=1);

$currentSavePath = session_save_path();
if (!$currentSavePath || !is_writable($currentSavePath)) {
    session_save_path(sys_get_temp_dir());
}
@session_start();

$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: ' . ($origin !== 'null' ? $origin : '*'));
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-User-Id');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/config/shawwite_db.php';

function jsonResponse(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function getJsonBody(): array {
    $raw = file_get_contents('php://input');
    if (!$raw) {
        return $_POST ?: [];
    }
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : ($_POST ?: []);
}

try {
    $pdo = getDbConnection();
} catch (Throwable $e) {
    jsonResponse([
        'ok' => false,
        'message' => 'Gagal terhubung ke database MySQL shawwite: ' . $e->getMessage()
    ], 500);
}

/**
 * Pastikan tabel product_reviews tersedia (auto-migrate bila belum ada)
 */
function ensureReviewsTable(PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `product_reviews` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `product_id` INT NOT NULL,
          `user_id` INT NOT NULL,
          `nama_user` VARCHAR(100) NOT NULL,
          `avatar_user` TEXT DEFAULT NULL,
          `rating` TINYINT NOT NULL DEFAULT 5,
          `komentar` TEXT NOT NULL,
          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
          CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
}
ensureReviewsTable($pdo);

/**
 * Ambil user aktif dari PHP Session atau header X-User-Id.
 * TIDAK melakukan auto-login jika pengunjung belum login (agar saat klik beli diarahkan ke Login).
 */
function getCurrentUser(PDO $pdo): ?array {
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId && !empty($_SERVER['HTTP_X_USER_ID'])) {
        $userId = (int) $_SERVER['HTTP_X_USER_ID'];
    }
    if (!$userId) {
        return null;
    }

    $stmt = $pdo->prepare('SELECT id, nama, username, email, role, no_wa, alamat, avatar_url, status, created_at FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([(int) $userId]);
    $user = $stmt->fetch();
    if (!$user || $user['status'] !== 'aktif') {
        unset($_SESSION['user_id']);
        return null;
    }
    return $user;
}

/**
 * Ambil seluruh ulasan/komentar dikelompokkan per product_id
 */
function fetchReviewsByProduct(PDO $pdo): array {
    $sql = "
        SELECT 
            r.id,
            r.product_id,
            r.user_id,
            r.nama_user,
            r.avatar_user,
            r.rating,
            r.komentar,
            r.created_at,
            u.role AS role_user
        FROM product_reviews r
        LEFT JOIN users u ON r.user_id = u.id
        ORDER BY r.id DESC
    ";
    $rows = $pdo->query($sql)->fetchAll();
    $grouped = [];
    foreach ($rows as $row) {
        $pid = (int) $row['product_id'];
        if (!isset($grouped[$pid])) {
            $grouped[$pid] = [];
        }
        $grouped[$pid][] = [
            'id'          => (int) $row['id'],
            'product_id'  => $pid,
            'user_id'     => (int) $row['user_id'],
            'nama_user'   => $row['nama_user'],
            'avatar_user' => $row['avatar_user'] ?: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=200&q=80',
            'role_user'   => $row['role_user'] ?: 'pembeli',
            'rating'      => (int) $row['rating'],
            'komentar'    => $row['komentar'],
            'created_at'  => $row['created_at'],
        ];
    }
    return $grouped;
}

/**
 * Ambil seluruh ulasan beserta informasi produk & seller (untuk monitoring Seller & Admin)
 */
function fetchAllReviewsDetailed(PDO $pdo, ?int $sellerId = null): array {
    $sql = "
        SELECT 
            r.id,
            r.product_id,
            r.user_id,
            r.nama_user,
            r.avatar_user,
            r.rating,
            r.komentar,
            r.created_at,
            p.kode_produk,
            p.nama AS nama_produk,
            p.kategori AS kategori_produk,
            p.image_url AS gambar_produk,
            p.seller_id,
            COALESCE(s.nama, 'Kebun ShawWite Utama') AS nama_seller
        FROM product_reviews r
        INNER JOIN products p ON r.product_id = p.id
        LEFT JOIN users s ON p.seller_id = s.id
    ";
    $params = [];
    if ($sellerId !== null) {
        $sql .= " WHERE p.seller_id = ? ";
        $params[] = $sellerId;
    }
    $sql .= " ORDER BY r.id DESC LIMIT 50";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    foreach ($rows as &$r) {
        $r['id'] = (int) $r['id'];
        $r['product_id'] = (int) $r['product_id'];
        $r['seller_id'] = (int) $r['seller_id'];
        $r['rating'] = (int) $r['rating'];
    }
    return $rows;
}

/**
 * Ambil seluruh produk beserta informasi Seller, rata-rata rating, jumlah ulasan, & statistik pesanan
 */
function fetchAllProducts(PDO $pdo): array {
    $reviewsMap = fetchReviewsByProduct($pdo);

    $sql = "
        SELECT 
            p.id AS db_id,
            p.kode_produk,
            p.seller_id,
            p.nama,
            p.kategori,
            p.harga,
            p.stok,
            p.deskripsi,
            p.image_url,
            p.unggulan,
            p.rating,
            p.created_at,
            COALESCE(u.nama, 'Kebun ShawWite Utama') AS seller_nama,
            COALESCE(NULLIF(u.no_wa, ''), '6281234567890') AS seller_wa,
            COALESCE(u.alamat, 'Banyuwangi, ID') AS seller_alamat,
            (SELECT COUNT(*) FROM order_inquiries oi WHERE oi.product_id = p.id) AS inquiry_count,
            (SELECT COALESCE(SUM(oi.qty), 0) FROM order_inquiries oi WHERE oi.product_id = p.id) AS total_qty_ordered,
            (SELECT COALESCE(SUM(oi.total_harga), 0) FROM order_inquiries oi WHERE oi.product_id = p.id) AS total_omzet
        FROM products p
        LEFT JOIN users u ON p.seller_id = u.id
        ORDER BY p.unggulan DESC, p.id ASC
    ";
    $rows = $pdo->query($sql)->fetchAll();
    $result = [];
    foreach ($rows as $r) {
        $dbId = (int) $r['db_id'];
        $prodReviews = $reviewsMap[$dbId] ?? [];
        $reviewCount = count($prodReviews);
        $avgRating = (float) $r['rating'];
        if ($reviewCount > 0) {
            $sum = 0;
            foreach ($prodReviews as $rv) {
                $sum += $rv['rating'];
            }
            $avgRating = round($sum / $reviewCount, 1);
        }

        $result[] = [
            'id'                => $r['kode_produk'],
            'db_id'             => $dbId,
            'seller_id'         => $r['seller_id'] ? (int) $r['seller_id'] : 3,
            'nama'              => $r['nama'],
            'kategori'          => $r['kategori'],
            'harga'             => (int) $r['harga'],
            'stok'              => (int) $r['stok'],
            'deskripsi'         => $r['deskripsi'],
            'imageUrl'          => $r['image_url'],
            'unggulan'          => (bool) $r['unggulan'],
            'rating'            => $avgRating,
            'review_count'      => $reviewCount,
            'reviews'           => $prodReviews,
            'inquiry_count'     => (int) $r['inquiry_count'],
            'total_qty_ordered' => (int) $r['total_qty_ordered'],
            'total_omzet'       => (int) $r['total_omzet'],
            'seller_nama'       => $r['seller_nama'],
            'seller_wa'         => $r['seller_wa'],
            'seller_alamat'     => $r['seller_alamat'],
        ];
    }
    return $result;
}

/**
 * Ambil seluruh user untuk Admin
 */
function fetchAllUsers(PDO $pdo): array {
    $sql = "
        SELECT 
            u.id,
            u.nama,
            u.username,
            u.email,
            u.role,
            u.no_wa,
            u.alamat,
            u.avatar_url,
            u.status,
            u.created_at,
            (SELECT COUNT(*) FROM products p WHERE p.seller_id = u.id) AS total_produk,
            (SELECT COUNT(*) FROM product_reviews r WHERE r.user_id = u.id) AS total_ulasan,
            (SELECT COUNT(*) FROM order_inquiries oi WHERE oi.pembeli_id = u.id) AS total_pesanan
        FROM users u
        ORDER BY FIELD(u.role, 'admin', 'seller', 'pembeli'), u.id ASC
    ";
    $rows = $pdo->query($sql)->fetchAll();
    foreach ($rows as &$r) {
        $r['id'] = (int) $r['id'];
        $r['total_produk'] = (int) $r['total_produk'];
        $r['total_ulasan'] = (int) $r['total_ulasan'];
        $r['total_pesanan'] = (int) $r['total_pesanan'];
    }
    return $rows;
}

/**
 * Ambil pengaturan toko dari tabel settings
 */
function fetchSettings(PDO $pdo): array {
    $rows = $pdo->query("SELECT setting_key, setting_value FROM settings")->fetchAll();
    $map = [
        'waNumber'  => '6281234567890',
        'storeName' => 'ShawWite Fruits Santorini'
    ];
    foreach ($rows as $row) {
        if ($row['setting_key'] === 'wa_number') {
            $map['waNumber'] = $row['setting_value'];
        } elseif ($row['setting_key'] === 'store_name') {
            $map['storeName'] = $row['setting_value'];
        }
    }
    return $map;
}

/**
 * Ambil riwayat inquiry/pesanan terbaru
 */
function fetchInquiries(PDO $pdo, ?int $sellerId = null): array {
    $sql = "
        SELECT 
            oi.id,
            oi.pembeli_id,
            oi.nama_pembeli,
            oi.no_wa_pembeli,
            oi.product_id,
            oi.seller_id,
            oi.qty,
            oi.total_harga,
            oi.catatan,
            oi.created_at,
            p.nama AS nama_produk,
            p.kategori AS kategori_produk,
            p.image_url AS gambar_produk,
            COALESCE(s.nama, 'ShawWite Seller') AS nama_seller
        FROM order_inquiries oi
        INNER JOIN products p ON oi.product_id = p.id
        LEFT JOIN users s ON oi.seller_id = s.id
    ";
    $params = [];
    if ($sellerId !== null) {
        $sql .= " WHERE p.seller_id = ? ";
        $params[] = $sellerId;
    }
    $sql .= " ORDER BY oi.id DESC LIMIT 30";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Ambil data analitik & statistik untuk grafik (Chart Monitoring Admin & Seller)
 */
function fetchAnalytics(PDO $pdo): array {
    $roleCounts = [
        'admin' => 0,
        'seller' => 0,
        'pembeli' => 0,
        'aktif' => 0,
        'nonaktif' => 0,
        'total' => 0
    ];
    $uRows = $pdo->query("SELECT role, status, COUNT(*) AS cnt FROM users GROUP BY role, status")->fetchAll();
    foreach ($uRows as $r) {
        $c = (int) $r['cnt'];
        $roleCounts[$r['role']] = ($roleCounts[$r['role']] ?? 0) + $c;
        $roleCounts[$r['status']] = ($roleCounts[$r['status']] ?? 0) + $c;
        $roleCounts['total'] += $c;
    }

    $catSql = "
        SELECT 
            p.kategori,
            COUNT(DISTINCT p.id) AS total_produk,
            COALESCE(SUM(p.stok), 0) AS total_stok,
            (SELECT COUNT(*) FROM product_reviews r INNER JOIN products p2 ON r.product_id = p2.id WHERE p2.kategori = p.kategori) AS total_ulasan,
            (SELECT COALESCE(SUM(oi.qty), 0) FROM order_inquiries oi INNER JOIN products p3 ON oi.product_id = p3.id WHERE p3.kategori = p.kategori) AS total_terjual,
            (SELECT COALESCE(SUM(oi.total_harga), 0) FROM order_inquiries oi INNER JOIN products p4 ON oi.product_id = p4.id WHERE p4.kategori = p.kategori) AS total_omzet
        FROM products p
        GROUP BY p.kategori
        ORDER BY total_omzet DESC, total_stok DESC
    ";
    $categoryStats = $pdo->query($catSql)->fetchAll();

    return [
        'userRoleCounts' => $roleCounts,
        'categoryStats'  => $categoryStats
    ];
}

$action = $_GET['action'] ?? 'bootstrap';
$currentUser = getCurrentUser($pdo);

// ============================================================================
// BOOTSTRAP DATA AWAL
// ============================================================================
if ($action === 'bootstrap') {
    $demoAccounts = $pdo->query("SELECT id, nama, username, email, role, no_wa, avatar_url, status FROM users WHERE status = 'aktif' ORDER BY FIELD(role, 'pembeli', 'seller', 'admin'), id ASC")->fetchAll();

    jsonResponse([
        'ok'           => true,
        'currentUser'  => $currentUser,
        'demoAccounts' => $demoAccounts,
        'products'     => fetchAllProducts($pdo),
        'settings'     => fetchSettings($pdo),
        'users'        => ($currentUser && $currentUser['role'] === 'admin') ? fetchAllUsers($pdo) : [],
        'inquiries'    => ($currentUser && in_array($currentUser['role'], ['seller', 'admin'], true)) ? fetchInquiries($pdo) : [],
        'allReviews'   => ($currentUser && in_array($currentUser['role'], ['seller', 'admin'], true)) ? fetchAllReviewsDetailed($pdo) : [],
        'analytics'    => ($currentUser && in_array($currentUser['role'], ['seller', 'admin'], true)) ? fetchAnalytics($pdo) : null,
    ]);
}

// ============================================================================
// AUTHENTICATION: LOGIN, QUICK SWITCH ROLE, REGISTER, LOGOUT
// ============================================================================
if ($action === 'logout') {
    unset($_SESSION['user_id']);
    jsonResponse([
        'ok'      => true,
        'message' => 'Anda telah keluar (Logout). Sekarang Anda berada dalam mode Pengunjung.'
    ]);
}

if ($action === 'login') {
    $body = getJsonBody();

    // Mode 1: Quick Switch Role / Demo User
    if (!empty($body['quick_user_id'])) {
        $stmt = $pdo->prepare("SELECT id, nama, username, email, role, no_wa, alamat, avatar_url, status, created_at FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([(int) $body['quick_user_id']]);
        $user = $stmt->fetch();
        if (!$user) {
            jsonResponse(['ok' => false, 'message' => 'User tidak ditemukan.'], 404);
        }
        if ($user['status'] !== 'aktif') {
            jsonResponse(['ok' => false, 'message' => 'Akun user ini sedang nonaktif. Hubungi Admin.'], 403);
        }
        $_SESSION['user_id'] = (int) $user['id'];
        jsonResponse([
            'ok'          => true,
            'message'     => 'Berhasil masuk sebagai ' . $user['nama'] . ' (' . strtoupper($user['role']) . ')',
            'currentUser' => $user,
            'users'       => $user['role'] === 'admin' ? fetchAllUsers($pdo) : [],
            'inquiries'   => in_array($user['role'], ['seller', 'admin'], true) ? fetchInquiries($pdo) : [],
            'allReviews'  => in_array($user['role'], ['seller', 'admin'], true) ? fetchAllReviewsDetailed($pdo) : [],
            'analytics'   => in_array($user['role'], ['seller', 'admin'], true) ? fetchAnalytics($pdo) : null,
        ]);
    }

    // Mode 2: Login Standar dengan Email/Username & Password (sesuai login.md)
    $identifier = trim((string) ($body['identifier'] ?? $body['email'] ?? ''));
    $password   = (string) ($body['password'] ?? '');

    if ($identifier === '') {
        jsonResponse(['ok' => false, 'message' => 'Email address is required.'], 400);
    }
    if ($password === '') {
        jsonResponse(['ok' => false, 'message' => 'Password is required.'], 400);
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE LOWER(email) = LOWER(?) OR LOWER(username) = LOWER(?) LIMIT 1");
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        jsonResponse(['ok' => false, 'message' => 'Email or password is incorrect.'], 401);
    }

    if ($user['status'] !== 'aktif') {
        jsonResponse(['ok' => false, 'message' => 'Akun Anda sedang dinonaktifkan oleh Admin.'], 403);
    }

    unset($user['password_hash']);
    $_SESSION['user_id'] = (int) $user['id'];

    jsonResponse([
        'ok'          => true,
        'message'     => 'Welcome back, ' . $user['nama'] . '!',
        'currentUser' => $user,
        'users'       => $user['role'] === 'admin' ? fetchAllUsers($pdo) : [],
        'inquiries'   => in_array($user['role'], ['seller', 'admin'], true) ? fetchInquiries($pdo) : [],
        'allReviews'  => in_array($user['role'], ['seller', 'admin'], true) ? fetchAllReviewsDetailed($pdo) : [],
        'analytics'   => in_array($user['role'], ['seller', 'admin'], true) ? fetchAnalytics($pdo) : null,
    ]);
}

if ($action === 'register') {
    $body            = getJsonBody();
    $nama            = trim((string) ($body['full_name'] ?? $body['nama'] ?? ''));
    $email           = strtolower(trim((string) ($body['email'] ?? '')));
    $password        = (string) ($body['password'] ?? '');
    $confirmPassword = (string) ($body['confirm_password'] ?? $body['password'] ?? '');
    $username        = trim((string) ($body['username'] ?? ''));
    $noWa            = preg_replace('/[^0-9]/', '', (string) ($body['no_wa'] ?? '6281200000000'));
    $alamat          = trim((string) ($body['alamat'] ?? 'Banyuwangi, Jawa Timur'));

    if ($nama === '') {
        jsonResponse(['ok' => false, 'field' => 'full_name', 'message' => 'Full name is required.'], 400);
    }
    if ($email === '') {
        jsonResponse(['ok' => false, 'field' => 'email', 'message' => 'Email address is required.'], 400);
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(['ok' => false, 'field' => 'email', 'message' => 'Enter a valid email address.'], 400);
    }
    if ($password === '') {
        jsonResponse(['ok' => false, 'field' => 'password', 'message' => 'Password is required.'], 400);
    }
    if (strlen($password) < 4) {
        jsonResponse(['ok' => false, 'field' => 'password', 'message' => 'Password does not meet the requirements (min. 4 characters).'], 400);
    }
    if ($confirmPassword === '') {
        jsonResponse(['ok' => false, 'field' => 'confirm_password', 'message' => 'Please confirm your password.'], 400);
    }
    if ($password !== $confirmPassword) {
        jsonResponse(['ok' => false, 'field' => 'confirm_password', 'message' => 'Passwords do not match.'], 400);
    }

    // Buat username otomatis dari email jika tidak dikirim dari form 4-field Register
    if ($username === '') {
        $baseUser = preg_replace('/[^a-z0-9_]/', '', strtolower(explode('@', $email)[0]));
        if ($baseUser === '') {
            $baseUser = 'user';
        }
        $username = $baseUser;
        $counter = 1;
        while (true) {
            $chkU = $pdo->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
            $chkU->execute([$username]);
            if (!$chkU->fetch()) {
                break;
            }
            $counter++;
            $username = $baseUser . $counter;
        }
    }

    $check = $pdo->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?) OR LOWER(username) = LOWER(?) LIMIT 1");
    $check->execute([$email, $username]);
    if ($check->fetch()) {
        jsonResponse([
            'ok'      => false,
            'field'   => 'email',
            'message' => 'An account with this email may already exist. Please sign in or recover your account.'
        ], 409);
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $avatar = 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80';

    $ins = $pdo->prepare("INSERT INTO users (nama, username, email, password_hash, role, no_wa, alamat, avatar_url, status) VALUES (?, ?, ?, ?, 'pembeli', ?, ?, ?, 'aktif')");
    $ins->execute([$nama, $username, $email, $hash, $noWa, $alamat, $avatar]);
    $newId = (int) $pdo->lastInsertId();

    $_SESSION['user_id'] = $newId;
    $stmt = $pdo->prepare("SELECT id, nama, username, email, role, no_wa, alamat, avatar_url, status, created_at FROM users WHERE id = ?");
    $stmt->execute([$newId]);
    $newUser = $stmt->fetch();

    jsonResponse([
        'ok'          => true,
        'message'     => 'Akun berhasil dibuat dan tersimpan di database! Selamat datang, ' . $nama,
        'currentUser' => $newUser
    ]);
}

// ============================================================================
// ULASAN / RATING & KOMENTAR PRODUK (WAJIB LOGIN)
// ============================================================================
if ($action === 'review_save') {
    if (!$currentUser) {
        jsonResponse([
            'ok'           => false,
            'requireLogin' => true,
            'message'      => 'Silakan login terlebih dahulu untuk memberikan rating dan komentar.'
        ], 401);
    }

    $body     = getJsonBody();
    $kode     = trim((string) ($body['product_id'] ?? ''));
    $rating   = max(1, min(5, (int) ($body['rating'] ?? 5)));
    $komentar = trim((string) ($body['komentar'] ?? ''));

    if ($kode === '' || $komentar === '') {
        jsonResponse(['ok' => false, 'message' => 'Rating dan isi komentar wajib diisi.'], 400);
    }

    $stmt = $pdo->prepare("SELECT id, nama FROM products WHERE kode_produk = ? LIMIT 1");
    $stmt->execute([$kode]);
    $prod = $stmt->fetch();
    if (!$prod) {
        jsonResponse(['ok' => false, 'message' => 'Produk tidak ditemukan.'], 404);
    }

    $prodDbId = (int) $prod['id'];
    $ins = $pdo->prepare("INSERT INTO product_reviews (product_id, user_id, nama_user, avatar_user, rating, komentar) VALUES (?, ?, ?, ?, ?, ?)");
    $ins->execute([
        $prodDbId,
        (int) $currentUser['id'],
        $currentUser['nama'],
        $currentUser['avatar_url'] ?? 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=200&q=80',
        $rating,
        $komentar
    ]);

    // Update rata-rata rating di tabel products
    $upd = $pdo->prepare("UPDATE products SET rating = COALESCE((SELECT ROUND(AVG(rating), 1) FROM product_reviews WHERE product_id = ?), 5.0) WHERE id = ?");
    $upd->execute([$prodDbId, $prodDbId]);

    jsonResponse([
        'ok'         => true,
        'message'    => 'Terima kasih! Rating & komentar Anda untuk "' . $prod['nama'] . '" berhasil disimpan.',
        'products'   => fetchAllProducts($pdo),
        'allReviews' => in_array($currentUser['role'], ['seller', 'admin'], true) ? fetchAllReviewsDetailed($pdo) : [],
        'analytics'  => in_array($currentUser['role'], ['seller', 'admin'], true) ? fetchAnalytics($pdo) : null,
    ]);
}

// ============================================================================
// ADMIN ENDPOINTS: KELOLA USER (HANYA ROLE 'admin')
// ============================================================================
if (in_array($action, ['users_list', 'user_save', 'user_delete'], true)) {
    if (!$currentUser || $currentUser['role'] !== 'admin') {
        jsonResponse([
            'ok'      => false,
            'message' => 'Akses Ditolak: Hanya Admin yang memiliki izin mengelola data User.'
        ], 403);
    }

    if ($action === 'users_list') {
        jsonResponse([
            'ok'        => true,
            'users'     => fetchAllUsers($pdo),
            'analytics' => fetchAnalytics($pdo)
        ]);
    }

    if ($action === 'user_save') {
        $body     = getJsonBody();
        $id       = !empty($body['id']) ? (int) $body['id'] : 0;
        $nama     = trim((string) ($body['nama'] ?? ''));
        $username = trim((string) ($body['username'] ?? ''));
        $email    = trim((string) ($body['email'] ?? ''));
        $role     = (string) ($body['role'] ?? 'pembeli');
        $noWa     = preg_replace('/[^0-9]/', '', (string) ($body['no_wa'] ?? ''));
        $alamat   = trim((string) ($body['alamat'] ?? 'Banyuwangi, Jawa Timur'));
        $status   = (string) ($body['status'] ?? 'aktif');
        $password = (string) ($body['password'] ?? '');
        $avatar   = trim((string) ($body['avatar_url'] ?? ''));

        if ($nama === '' || $username === '' || $email === '') {
            jsonResponse(['ok' => false, 'message' => 'Nama, Username, dan Email wajib diisi.'], 400);
        }

        if (!in_array($role, ['admin', 'seller', 'pembeli'], true)) {
            jsonResponse(['ok' => false, 'message' => 'Role user tidak valid.'], 400);
        }

        if (!in_array($status, ['aktif', 'nonaktif'], true)) {
            $status = 'aktif';
        }

        $dup = $pdo->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ? LIMIT 1");
        $dup->execute([$username, $email, $id]);
        if ($dup->fetch()) {
            jsonResponse(['ok' => false, 'message' => 'Username atau Email sudah digunakan oleh user lain.'], 409);
        }

        if ($avatar === '') {
            $defaultAvatars = [
                'admin'   => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80',
                'seller'  => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80',
                'pembeli' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=200&q=80',
            ];
            $avatar = $defaultAvatars[$role];
        }

        if ($id > 0) {
            if ($id === (int) $currentUser['id'] && ($status !== 'aktif' || $role !== 'admin')) {
                jsonResponse(['ok' => false, 'message' => 'Anda tidak dapat menonaktifkan atau mengubah role akun Admin Anda sendiri yang sedang aktif.'], 400);
            }

            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $upd = $pdo->prepare("UPDATE users SET nama = ?, username = ?, email = ?, password_hash = ?, role = ?, no_wa = ?, alamat = ?, avatar_url = ?, status = ? WHERE id = ?");
                $upd->execute([$nama, $username, $email, $hash, $role, $noWa, $alamat, $avatar, $status, $id]);
            } else {
                $upd = $pdo->prepare("UPDATE users SET nama = ?, username = ?, email = ?, role = ?, no_wa = ?, alamat = ?, avatar_url = ?, status = ? WHERE id = ?");
                $upd->execute([$nama, $username, $email, $role, $noWa, $alamat, $avatar, $status, $id]);
            }
            $msg = "Data user \"$nama\" berhasil diperbarui!";
        } else {
            $rawPass = $password !== '' ? $password : ($role . '123');
            $hash = password_hash($rawPass, PASSWORD_BCRYPT);
            $ins = $pdo->prepare("INSERT INTO users (nama, username, email, password_hash, role, no_wa, alamat, avatar_url, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $ins->execute([$nama, $username, $email, $hash, $role, $noWa, $alamat, $avatar, $status]);
            $msg = "User baru \"$nama\" ($role) berhasil ditambahkan ke database!";
        }

        $demoAccounts = $pdo->query("SELECT id, nama, username, email, role, no_wa, avatar_url, status FROM users WHERE status = 'aktif' ORDER BY FIELD(role, 'pembeli', 'seller', 'admin'), id ASC")->fetchAll();

        jsonResponse([
            'ok'           => true,
            'message'      => $msg,
            'users'        => fetchAllUsers($pdo),
            'demoAccounts' => $demoAccounts,
            'products'     => fetchAllProducts($pdo),
            'analytics'    => fetchAnalytics($pdo),
        ]);
    }

    if ($action === 'user_delete') {
        $body = getJsonBody();
        $id   = (int) ($body['id'] ?? 0);

        if ($id <= 0) {
            jsonResponse(['ok' => false, 'message' => 'ID User tidak valid.'], 400);
        }

        if ($id === (int) $currentUser['id']) {
            jsonResponse(['ok' => false, 'message' => 'Anda tidak dapat menghapus akun Admin Anda sendiri yang sedang digunakan!'], 400);
        }

        $del = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $del->execute([$id]);

        $demoAccounts = $pdo->query("SELECT id, nama, username, email, role, no_wa, avatar_url, status FROM users WHERE status = 'aktif' ORDER BY FIELD(role, 'pembeli', 'seller', 'admin'), id ASC")->fetchAll();

        jsonResponse([
            'ok'           => true,
            'message'      => 'User berhasil dihapus dari database.',
            'users'        => fetchAllUsers($pdo),
            'demoAccounts' => $demoAccounts,
            'products'     => fetchAllProducts($pdo),
            'analytics'    => fetchAnalytics($pdo),
        ]);
    }
}

// ============================================================================
// SELLER ENDPOINTS: KELOLA PRODUK & PENGATURAN WA (HANYA ROLE 'seller')
// ============================================================================
if (in_array($action, ['product_save', 'product_delete', 'settings_save', 'product_reset_dummy'], true)) {
    if (!$currentUser || $currentUser['role'] !== 'seller') {
        jsonResponse([
            'ok'      => false,
            'message' => 'Akses Ditolak: Hanya Seller yang memiliki izin mengelola Produk Katalog.'
        ], 403);
    }

    if ($action === 'product_save') {
        $body      = getJsonBody();
        $kode      = trim((string) ($body['id'] ?? ''));
        $nama      = trim((string) ($body['nama'] ?? ''));
        $kategori  = trim((string) ($body['kategori'] ?? 'Buah Lokal'));
        $harga     = max(0, (int) ($body['harga'] ?? 0));
        $stok      = max(0, (int) ($body['stok'] ?? 0));
        $deskripsi = trim((string) ($body['deskripsi'] ?? ''));
        $imageUrl  = trim((string) ($body['imageUrl'] ?? ''));
        $unggulan  = !empty($body['unggulan']) ? 1 : 0;

        if ($nama === '' || $deskripsi === '') {
            jsonResponse(['ok' => false, 'message' => 'Nama buah dan deskripsi wajib diisi.'], 400);
        }

        if ($imageUrl === '') {
            $imageUrl = 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?auto=format&fit=crop&w=800&q=80';
        }

        $stmt = $pdo->prepare("SELECT id FROM products WHERE kode_produk = ? LIMIT 1");
        $stmt->execute([$kode]);
        $existing = $stmt->fetch();

        if ($existing) {
            $upd = $pdo->prepare("UPDATE products SET nama = ?, kategori = ?, harga = ?, stok = ?, deskripsi = ?, image_url = ?, unggulan = ? WHERE kode_produk = ?");
            $upd->execute([$nama, $kategori, $harga, $stok, $deskripsi, $imageUrl, $unggulan, $kode]);
            $msg = "Produk \"$nama\" berhasil diperbarui di database!";
        } else {
            $newKode = 'prd-' . time() . '-' . random_int(10, 99);
            $ins = $pdo->prepare("INSERT INTO products (kode_produk, seller_id, nama, kategori, harga, stok, deskripsi, image_url, unggulan, rating) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 5.0)");
            $ins->execute([$newKode, (int) $currentUser['id'], $nama, $kategori, $harga, $stok, $deskripsi, $imageUrl, $unggulan]);
            $msg = "Produk baru \"$nama\" berhasil ditambahkan ke katalog!";
        }

        jsonResponse([
            'ok'         => true,
            'message'    => $msg,
            'products'   => fetchAllProducts($pdo),
            'allReviews' => fetchAllReviewsDetailed($pdo),
            'analytics'  => fetchAnalytics($pdo),
        ]);
    }

    if ($action === 'product_delete') {
        $body = getJsonBody();
        $kode = trim((string) ($body['id'] ?? ''));

        if ($kode === '') {
            jsonResponse(['ok' => false, 'message' => 'Kode produk tidak valid.'], 400);
        }

        $del = $pdo->prepare("DELETE FROM products WHERE kode_produk = ?");
        $del->execute([$kode]);

        jsonResponse([
            'ok'         => true,
            'message'    => 'Produk berhasil dihapus dari database MySQL.',
            'products'   => fetchAllProducts($pdo),
            'allReviews' => fetchAllReviewsDetailed($pdo),
            'analytics'  => fetchAnalytics($pdo),
        ]);
    }

    if ($action === 'settings_save') {
        $body = getJsonBody();
        $waNumber = preg_replace('/[^0-9]/', '', (string) ($body['waNumber'] ?? ''));
        if ($waNumber === '') {
            jsonResponse(['ok' => false, 'message' => 'Nomor WhatsApp tidak valid.'], 400);
        }

        $updSeller = $pdo->prepare("UPDATE users SET no_wa = ? WHERE id = ?");
        $updSeller->execute([$waNumber, (int) $currentUser['id']]);

        $updSetting = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('wa_number', ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        $updSetting->execute([$waNumber]);

        $currentUser['no_wa'] = $waNumber;

        jsonResponse([
            'ok'          => true,
            'message'     => 'Nomor WhatsApp Seller & Toko berhasil diperbarui!',
            'settings'    => fetchSettings($pdo),
            'currentUser' => $currentUser,
            'products'    => fetchAllProducts($pdo)
        ]);
    }

    if ($action === 'product_reset_dummy') {
        $sqlFile = __DIR__ . '/database/shawwite.sql';
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            $pdo->exec($sql);
        }
        jsonResponse([
            'ok'         => true,
            'message'    => 'Data katalog berhasil di-reset ke data dummy awal MySQL!',
            'products'   => fetchAllProducts($pdo),
            'allReviews' => fetchAllReviewsDetailed($pdo),
            'analytics'  => fetchAnalytics($pdo),
        ]);
    }
}

// ============================================================================
// PEMBELI ENDPOINT: PILIH PRODUK & HUBUNGI SELLER VIA WHATSAPP (WAJIB LOGIN)
// ============================================================================
if ($action === 'create_inquiry') {
    if (!$currentUser) {
        jsonResponse([
            'ok'           => false,
            'requireLogin' => true,
            'message'      => 'Silakan login terlebih dahulu untuk melakukan pembelian produk.'
        ], 401);
    }

    $body    = getJsonBody();
    $kode    = trim((string) ($body['product_id'] ?? ''));
    $qty     = max(1, (int) ($body['qty'] ?? 1));
    $catatan = trim((string) ($body['catatan'] ?? ''));

    $stmt = $pdo->prepare("
        SELECT p.*, COALESCE(NULLIF(u.no_wa, ''), '6281234567890') AS seller_wa, COALESCE(u.nama, 'ShawWite Seller') AS seller_nama
        FROM products p
        LEFT JOIN users u ON p.seller_id = u.id
        WHERE p.kode_produk = ?
        LIMIT 1
    ");
    $stmt->execute([$kode]);
    $product = $stmt->fetch();

    if (!$product) {
        jsonResponse(['ok' => false, 'message' => 'Produk tidak ditemukan.'], 404);
    }

    if ((int) $product['stok'] <= 0) {
        jsonResponse(['ok' => false, 'message' => 'Maaf, stok produk ini sedang habis.'], 400);
    }

    $namaPembeli = $currentUser['nama'];
    $waPembeli   = $currentUser['no_wa'] ?? '';
    $pembeliId   = (int) $currentUser['id'];
    $totalHarga  = (int) $product['harga'] * $qty;

    $ins = $pdo->prepare("INSERT INTO order_inquiries (pembeli_id, nama_pembeli, no_wa_pembeli, product_id, seller_id, qty, total_harga, catatan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $ins->execute([
        $pembeliId,
        $namaPembeli,
        $waPembeli,
        (int) $product['id'],
        $product['seller_id'] ? (int) $product['seller_id'] : 3,
        $qty,
        $totalHarga,
        $catatan
    ]);

    $settings = fetchSettings($pdo);
    $targetWa = !empty($product['seller_wa']) ? $product['seller_wa'] : $settings['waNumber'];
    $hargaFmt = 'Rp ' . number_format((int) $product['harga'], 0, ',', '.');
    $totalFmt = 'Rp ' . number_format($totalHarga, 0, ',', '.');

    $waLines = [
        "Halo *{$product['seller_nama']}* (ShawWite Fruits) 👋",
        "Saya *{$namaPembeli}* ingin memesan buah segar:",
        "",
        "🍎 *Produk*: {$product['nama']} ({$product['kategori']})",
        "📦 *Jumlah*: {$qty} Pack",
        "💰 *Harga Satuan*: {$hargaFmt}",
        "🧾 *Total Estimasi*: {$totalFmt}",
    ];
    if ($catatan !== '') {
        $waLines[] = "📝 *Catatan*: {$catatan}";
    }
    $waLines[] = "";
    $waLines[] = "Mohon informasi ketersediaan dan pengirimannya. Terima kasih!";

    $waUrl = 'https://wa.me/' . rawurlencode($targetWa) . '?text=' . rawurlencode(implode("\n", $waLines));

    jsonResponse([
        'ok'       => true,
        'message'  => 'Pesanan tercatat! Membuka WhatsApp Seller...',
        'waUrl'    => $waUrl,
        'products' => fetchAllProducts($pdo)
    ]);
}

jsonResponse(['ok' => false, 'message' => 'Action API tidak dikenali.'], 400);
