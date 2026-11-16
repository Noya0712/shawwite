# ShawWite Fruits 🍏🍊🍇

> **Santorini Soft-Neumorphic Fruit Shop & Orchard Platform**  
> Platform belanja buah segar langsung dari kebun dengan arsitektur multi-role (Pembeli, Seller, Admin), dashboard pemantauan, ulasan interaktif, pemesanan WhatsApp, dan sistem manajemen profil/toko.

---

## 🌟 Fitur Utama

### 1. Registrasi Akun Fleksibel (Multi-Role)
- Calon pengguna dapat memilih peran saat mendaftar:
  - **🛍️ Pembeli (Buyer):** Jelajah katalog buah, hubungi seller untuk memesan via WhatsApp, dan berikan rating bintang 1–5 serta ulasan.
  - **🏪 Seller (Penjual / Pemilik Kebun):** Buka lapak toko buah, kelola stok dan harga produk, serta pantau performa penjualan dan ulasan pembeli.
- Pilihan **Jenis Kelamin** (👨 Laki-laki / 👩 Perempuan) dengan avatar adaptif otomatis.
- Field khusus Seller untuk menentukan **Nama Toko / Kebun** dan deskripsi lapak.

### 2. Manajemen Profil & Toko Lengkap
- **Untuk Pembeli:**
  - Ubah nama lengkap, jenis kelamin, nomor WhatsApp, alamat pengiriman.
  - Ganti foto avatar (dukungan URL custom atau preset instan).
  - Reset password aman (enkripsi Bcrypt).
- **Untuk Seller:**
  - Kelola profil identitas pemilik dan pengaturan toko/kebun.
  - Ubah Nama Toko, Deskripsi Toko, Alamat Kebun, dan Nomor WhatsApp toko.
  - Ganti logo/foto profil kebun.
  - Reset password akun seller.

### 3. Tampilan Dashboard & Monitoring (Sesuai Hak Akses)
- **Pengunjung (Guest):** Dapat melihat katalog buah dan ulasan, diarahkan login saat akan membeli.
- **Pembeli:** Bebas memilih buah, melihat detail deskripsi, melakukan pemesanan via WhatsApp, dan menuliskan ulasan/rating.
- **Seller:** Akses Dashboard Seller, manajemen CRUD produk buah, pemantauan grafik penjualan per kategori, dan monitoring ulasan pelanggan.
- **Admin:** Akses penuh monitoring analitik sistem, grafik distribusi peran user dan omzet, serta manajemen data pengguna (tambah, edit, aktifkan/nonaktifkan).

### 4. Desain Neumorphic Santorini
- Palet warna lembut terinspirasi dari arsitektur kubus putih Santorini (*Aegean Blue*, *Santorini Clay White*, *Emerald Green Accent*).
- Transisi halus, bayangan neumorphic ganda (*dual-shadows*), dan dukungan tema **Siang (Santorini Light)** & **Malam (Aegean Night)**.

---

## 🛠️ Teknologi & Stack

- **Backend:** PHP 8.2+ / Laravel 11 (Modular REST API `api.php` + Blade Templates)
- **Database:** MySQL / MariaDB (Database: `shawwite`, PDO connection)
- **Frontend:** Vanilla JavaScript (ES6+ modular state management), CSS Neumorphic kustom, Tailwind CSS CDN
- **Visualisasi Data:** Chart.js 4.4
- **Web Server:** Apache (XAMPP) atau Laravel Built-in Server (`php artisan serve`)

---

## 🚀 Panduan Instalasi & Menjalankan

### 1. Prasyarat
- PHP >= 8.2
- Composer
- Node.js & npm
- XAMPP / MySQL Server

### 2. Konfigurasi Database
1. Pastikan Apache & MySQL di XAMPP telah menyala (*Running*).
2. Buat database bernama `shawwite` atau import file skema database:
   ```bash
   mysql -u root < database/shawwite.sql
   ```
3. Konfigurasi kredensial koneksi database pada `.env` atau `config/shawwite_db.php`:
   - Host: `127.0.0.1`
   - Port: `3306`
   - Database: `shawwite`
   - Username: `root`
   - Password: `(kosong)`

### 3. Menjalankan Server Pengembangan
Jalankan server aplikasi menggunakan npm:
```bash
npm run dev
```
Aplikasi dapat dibuka melalui browser pada:
- **Laravel / Vite:** `http://127.0.0.1:8000`
- **XAMPP Apache:** `http://localhost/shawwite/`

---

## 👥 Akun Demo Bawaan

| Role | Username / Email | Password Default | Akses Fitur |
|---|---|---|---|
| **Admin** | `admin` / `admin@shawwite.com` | `admin123` | Monitoring Chart & Kelola Semua User |
| **Seller** | `seller` / `seller@shawwite.com` | `seller123` | Kelola Produk, Chart Toko & Review |
| **Pembeli** | `pembeli` / `pembeli@shawwite.com` | `pembeli123` | Beli Buah via WA & Beri Review |

---

## 📂 Struktur Direktori

```text
├── api.php                     # REST API Backend (Auth, Produk, Reviews, Profil, Reset Password)
├── login.php                   # Halaman Login Standar Neumorphic
├── register.php                # Halaman Registrasi Multi-Role (Pembeli vs Seller)
├── index.php                   # Entry Point Modular PHP
├── assets/
│   ├── css/                    # Stylesheet (style.css, auth.css)
│   └── js/                     # Logika Frontend (app.js)
├── config/                     # Konfigurasi Database & Laravel
├── database/                   # Skema MySQL (shawwite.sql)
├── includes/                   # Komponen Modular (Header, Sidebar, Topbar, Modal)
├── resources/                  # Template Laravel Blade & Aset
├── routes/                     # Definisi Route Laravel (web.php, api.php)
└── views/                      # Partial Views PHP (catalog, seller_products, admin_users)
```

---

## 📄 Lisensi
Dikembangkan untuk platform **ShawWite Fruits**. Hak cipta dilindungi.
