-- ============================================================================
-- SHAWWITE FRUITS — SANTORINI NEUMORPHIC EDITION (PRODUCTION MYSQL SCHEMA)
-- Database: shawwite
-- Roles: admin (Kelola User & Monitoring Chart), seller (Kelola Produk, Chart & Komentar), pembeli (Katalog, Beli & Rating/Komentar)
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `shawwite` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `shawwite`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `product_reviews`;
DROP TABLE IF EXISTS `order_inquiries`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------------------------
-- 1. TABEL USERS (Admin, Seller, Pembeli)
-- ----------------------------------------------------------------------------
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'seller', 'pembeli') NOT NULL DEFAULT 'pembeli',
  `no_wa` VARCHAR(30) DEFAULT '6281234567890',
  `alamat` VARCHAR(255) DEFAULT 'Banyuwangi, Jawa Timur',
  `avatar_url` TEXT DEFAULT NULL,
  `status` ENUM('aktif', 'nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password Default Dummy:
-- admin123   -> $2y$10$X1tqP7SsEiV0d107E8O7qe/b5ukEy/v1KH9yMCdsp68v45DYk67HS
-- seller123  -> $2y$10$bDm2SRDWkY3yZoMWYI6mHuSzij//HhA7YU7eOPMNffZV5TG9BIcSe
-- pembeli123 -> $2y$10$YkNUX1Vj7qRasTF5d2ZFBuJadHv09kKk5GUaqlpoNBiZRPkSBS4sK

INSERT INTO `users` (`id`, `nama`, `username`, `email`, `password_hash`, `role`, `no_wa`, `alamat`, `avatar_url`, `status`, `created_at`) VALUES
(1, 'Budi Santoso (Admin Utama)', 'admin', 'admin@shawwite.com', '$2y$10$X1tqP7SsEiV0d107E8O7qe/b5ukEy/v1KH9yMCdsp68v45DYk67HS', 'admin', '6281133445566', 'Kantor Pusat ShawWite, Banyuwangi', 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80', 'aktif', '2026-06-10 08:30:00'),
(2, 'Nadia Putri (Admin Operasional)', 'nadia_admin', 'nadia@shawwite.com', '$2y$10$X1tqP7SsEiV0d107E8O7qe/b5ukEy/v1KH9yMCdsp68v45DYk67HS', 'admin', '6281122334455', 'Genteng, Banyuwangi', 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=200&q=80', 'aktif', '2026-07-14 09:15:00'),
(3, 'Kebun ShawWite Utama', 'seller', 'seller@shawwite.com', '$2y$10$bDm2SRDWkY3yZoMWYI6mHuSzij//HhA7YU7eOPMNffZV5TG9BIcSe', 'seller', '6281234567890', 'Kebun Nusantara, Banyuwangi', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80', 'aktif', '2026-08-01 10:00:00'),
(4, 'Hendi Banyuwangi Fresh', 'hendi_orchard', 'hendi@shawwite.com', '$2y$10$bDm2SRDWkY3yZoMWYI6mHuSzij//HhA7YU7eOPMNffZV5TG9BIcSe', 'seller', '6281398765432', 'Glagah Orchard, Banyuwangi', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=200&q=80', 'aktif', '2026-08-19 11:20:00'),
(5, 'Siti Aminah', 'pembeli', 'pembeli@shawwite.com', '$2y$10$YkNUX1Vj7qRasTF5d2ZFBuJadHv09kKk5GUaqlpoNBiZRPkSBS4sK', 'pembeli', '6285711223344', 'Jl. Ahmad Yani No. 45, Banyuwangi', 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=200&q=80', 'aktif', '2026-09-05 14:10:00'),
(6, 'Rizky Pratama', 'rizky_pratama', 'rizky@gmail.com', '$2y$10$YkNUX1Vj7qRasTF5d2ZFBuJadHv09kKk5GUaqlpoNBiZRPkSBS4sK', 'pembeli', '6281299887766', 'Rogojampi, Banyuwangi', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=200&q=80', 'aktif', '2026-10-12 16:45:00'),
(7, 'Dewi Lestari', 'dewi_lestari', 'dewi@gmail.com', '$2y$10$YkNUX1Vj7qRasTF5d2ZFBuJadHv09kKk5GUaqlpoNBiZRPkSBS4sK', 'pembeli', '6282133445566', 'Giri, Banyuwangi', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=200&q=80', 'aktif', '2026-11-02 13:05:00'),
(8, 'Fajar Nugroho', 'fajar_nugroho', 'fajar@gmail.com', '$2y$10$YkNUX1Vj7qRasTF5d2ZFBuJadHv09kKk5GUaqlpoNBiZRPkSBS4sK', 'pembeli', '6287855667788', 'Kalipuro, Banyuwangi', 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?auto=format&fit=crop&w=200&q=80', 'nonaktif', '2026-11-10 17:30:00');

-- ----------------------------------------------------------------------------
-- 2. TABEL PRODUCTS (Katalog Buah Segar)
-- ----------------------------------------------------------------------------
CREATE TABLE `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_produk` VARCHAR(40) NOT NULL UNIQUE,
  `seller_id` INT DEFAULT 3,
  `nama` VARCHAR(100) NOT NULL,
  `kategori` VARCHAR(60) NOT NULL,
  `harga` INT NOT NULL DEFAULT 0,
  `stok` INT NOT NULL DEFAULT 0,
  `deskripsi` TEXT NOT NULL,
  `image_url` LONGTEXT NOT NULL,
  `unggulan` TINYINT(1) NOT NULL DEFAULT 0,
  `rating` DECIMAL(2,1) NOT NULL DEFAULT 4.8,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_products_seller` FOREIGN KEY (`seller_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `kode_produk`, `seller_id`, `nama`, `kategori`, `harga`, `stok`, `deskripsi`, `image_url`, `unggulan`, `rating`) VALUES
(1, 'prd-01', 3, 'Mangga Harum Manis', 'Buah Lokal', 32000, 25, 'Mangga pilihan matang pohon dengan rasa manis segar paripurna. Cocok untuk rujak manis atau dimakan langsung.', 'https://images.unsplash.com/photo-1553279768-865429fa0078?auto=format&fit=crop&w=800&q=80', 1, 4.9),
(2, 'prd-02', 3, 'Apel Fuji Premium', 'Buah Impor', 48000, 18, 'Apel Fuji kemerahan dengan tekstur garing (crunchy) dan kaya air. Didatangkan langsung dari perkebunan terbaik.', 'https://images.unsplash.com/photo-1560806887-1e4cd0b6fac6?auto=format&fit=crop&w=800&q=80', 1, 4.9),
(3, 'prd-03', 3, 'Pisang Cavendish Sun', 'Buah Tropis', 24000, 30, 'Pisang berkualitas tinggi yang mulus, kuning merata, dan kaya potasium. Sangat cocok untuk sarapan.', 'https://images.unsplash.com/photo-1603833665858-e61d17a86224?auto=format&fit=crop&w=800&q=80', 0, 4.8),
(4, 'prd-04', 4, 'Jeruk Dekopon Manis', 'Buah Lokal', 45000, 12, 'Jeruk tanpa biji dengan ciri khas benjolan di atasnya. Bulirnya besar, sangat manis, dan mudah dikupas.', 'https://images.unsplash.com/photo-1582979512210-99b6a53386f9?auto=format&fit=crop&w=800&q=80', 1, 4.9),
(5, 'prd-05', 4, 'Anggur Red Globe', 'Buah Impor', 65000, 0, 'Anggur merah besar dengan biji. Rasanya sangat manis, dagingnya tebal dan memuaskan.', 'https://images.unsplash.com/photo-1596363505729-4190a9506133?auto=format&fit=crop&w=800&q=80', 0, 4.7),
(6, 'prd-06', 3, 'Semangka Merah Segar', 'Buah Tropis', 28000, 15, 'Potongan semangka merah segar kaya air. Pelepas dahaga yang sempurna di siang hari.', 'https://images.unsplash.com/photo-1589984662646-e7b2e4962f18?auto=format&fit=crop&w=800&q=80', 0, 4.8),
(7, 'prd-07', 4, 'Alpukat Mentega Super', 'Buah Lokal', 38000, 20, 'Alpukat dengan daging buah kuning, tebal, dan bertekstur pulen seperti mentega. Gurih alami.', 'https://images.unsplash.com/photo-1523049673857-eb18f1d7b578?auto=format&fit=crop&w=800&q=80', 1, 4.9),
(8, 'prd-08', 3, 'Stroberi Ciwidey', 'Buah Beri', 30000, 10, 'Stroberi segar dari dataran tinggi Ciwidey. Perpaduan rasa manis dan asam yang menyegarkan mata.', 'https://images.unsplash.com/photo-1464965911861-746a04b4bca6?auto=format&fit=crop&w=800&q=80', 0, 4.8),
(9, 'prd-09', 3, 'Parcel Santorini Royal', 'Paket Parcel', 185000, 8, 'Rangkaian parcel buah premium berisi Apel Fuji, Anggur, Jeruk Dekopon, dan kartu ucapan eksklusif bergaya Santorini.', 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?auto=format&fit=crop&w=800&q=80', 1, 5.0),
(10, 'prd-10', 4, 'Buah Naga Merah Organik', 'Buah Tropis', 26000, 22, 'Buah naga merah asli Banyuwangi yang dipetik matang pohon, manis segar dan kaya antioksidan alami.', 'https://images.unsplash.com/photo-1527325678964-54921661f888?auto=format&fit=crop&w=800&q=80', 0, 4.9);

-- ----------------------------------------------------------------------------
-- 3. TABEL PRODUCT REVIEWS (Rating & Komentar User pada Postingan Produk Seller)
-- ----------------------------------------------------------------------------
CREATE TABLE `product_reviews` (
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

INSERT INTO `product_reviews` (`product_id`, `user_id`, `nama_user`, `avatar_user`, `rating`, `komentar`, `created_at`) VALUES
(1, 5, 'Siti Aminah', 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=200&q=80', 5, 'Mangga Harum Manisnya benar-benar matang pohon dan wangi banget! Dagingnya tebal dan serat halus. Pasti langganan di Kebun ShawWite.', '2026-11-12 10:15:00'),
(1, 6, 'Rizky Pratama', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=200&q=80', 5, 'Pengiriman cepat dan mangganya manis segar tanpa ada yang memar. Mantap!', '2026-11-13 14:22:00'),
(2, 7, 'Dewi Lestari', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=200&q=80', 5, 'Apel Fuji-nya super crunchy dan manis berair. Anak-anak di rumah suka sekali.', '2026-11-13 18:40:00'),
(4, 5, 'Siti Aminah', 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=200&q=80', 5, 'Jeruk Dekopon dari Hendi Banyuwangi Fresh ukurannya besar-besar, manis segar dan tanpa biji!', '2026-11-14 09:05:00'),
(7, 6, 'Rizky Pratama', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=200&q=80', 5, 'Alpukat menteganya pulen kuning merata, dibuat jus maupun dimakan langsung enak banget.', '2026-11-14 20:10:00'),
(9, 7, 'Dewi Lestari', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=200&q=80', 5, 'Parcel Santorini Royal kemasannya sangat mewah dan buah di dalamnya segar semua untuk hantaran.', '2026-11-15 11:30:00'),
(10, 5, 'Siti Aminah', 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=200&q=80', 5, 'Buah naga organik asli Banyuwangi memang juara manisnya!', '2026-11-15 16:45:00');

-- ----------------------------------------------------------------------------
-- 4. TABEL SETTINGS (Pengaturan Toko & WhatsApp Default)
-- ----------------------------------------------------------------------------
CREATE TABLE `settings` (
  `setting_key` VARCHAR(50) PRIMARY KEY,
  `setting_value` TEXT NOT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('wa_number', '6281234567890'),
('store_name', 'ShawWite Fruits Santorini');

-- ----------------------------------------------------------------------------
-- 5. TABEL ORDER INQUIRIES (Riwayat Pilihan & Hubungi Seller oleh Pembeli)
-- ----------------------------------------------------------------------------
CREATE TABLE `order_inquiries` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `pembeli_id` INT DEFAULT NULL,
  `nama_pembeli` VARCHAR(100) NOT NULL,
  `no_wa_pembeli` VARCHAR(30) DEFAULT NULL,
  `product_id` INT NOT NULL,
  `seller_id` INT DEFAULT NULL,
  `qty` INT NOT NULL DEFAULT 1,
  `total_harga` INT NOT NULL DEFAULT 0,
  `catatan` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_inquiry_pembeli` FOREIGN KEY (`pembeli_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_inquiry_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `order_inquiries` (`pembeli_id`, `nama_pembeli`, `no_wa_pembeli`, `product_id`, `seller_id`, `qty`, `total_harga`, `catatan`, `created_at`) VALUES
(5, 'Siti Aminah', '6285711223344', 1, 3, 2, 64000, 'Pilih yang matang untuk dimakan sore ini ya kak', '2026-11-12 09:30:00'),
(6, 'Rizky Pratama', '6281299887766', 9, 3, 1, 185000, 'Tolong tambahkan pita biru untuk hantaran keluarga', '2026-11-13 13:15:00'),
(7, 'Dewi Lestari', '6282133445566', 4, 4, 3, 135000, 'Kirim ke Giri sebelum jam 4 sore', '2026-11-14 08:40:00'),
(5, 'Siti Aminah', '6285711223344', 7, 4, 2, 76000, 'Alpukat mentega yang siap santap 1, yang mengkal 1', '2026-11-15 10:20:00'),
(6, 'Rizky Pratama', '6281299887766', 2, 3, 2, 96000, 'Apel Fuji untuk bekal kantor', '2026-11-15 15:50:00');
