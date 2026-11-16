<?php
/**
 * ============================================================================
 * SHAWWITE FRUITS — SANTORINI NEUMORPHIC EDITION (MODULAR ENTRY POINT)
 * File: index.php
 * ============================================================================
 * Struktur File Modular:
 * - config/database.php        : Koneksi PDO MySQL ke database `shawwite`
 * - api.php                    : Endpoint REST API (Auth, Produk, Users, Inquiries)
 * - login.php                  : Halaman Login Khusus Sesuai Role
 * - assets/css/style.css       : Stylesheet Santorini Soft-Neumorphic
 * - assets/js/app.js           : Logika Frontend & Role-Based Access Control
 * - includes/header.php        : Head HTML, Loading Screen, Modal Detail & Confirm
 * - includes/modal_login.php   : Modal Menu Login & Pilih Role (Admin/Seller/Pembeli)
 * - includes/sidebar.php       : Navigasi Sidebar Kiri (termasuk Menu Login)
 * - includes/topbar.php        : Header Sapaan, Search Bar, Role Badge & Avatar
 * - views/catalog.php          : Halaman Katalog Pembeli (Lihat, Pilih, Hubungi WA)
 * - views/seller_products.php  : Halaman Dashboard Seller (Kelola Produk)
 * - views/admin_users.php      : Halaman Dashboard Admin (Kelola User)
 * - includes/footer.php        : Pemanggil JS & Penutup HTML
 * ============================================================================
 */

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/modal_login.php';
?>

  <!-- MAIN SANTORINI NEUMORPHIC APP SHELL -->
  <div class="neu-shell">

    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

    <!-- 2. MAIN WORKSPACE CONTENT -->
    <div style="min-width: 0;">

      <?php
        require_once __DIR__ . '/includes/topbar.php';
        require_once __DIR__ . '/views/catalog.php';
        require_once __DIR__ . '/views/seller_products.php';
        require_once __DIR__ . '/views/admin_users.php';
      ?>

    </div>
  </div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
