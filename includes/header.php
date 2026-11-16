<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ShawWite Fruits — Katalog &amp; Dashboard (Production Edition)</title>

  <!-- Tailwind CSS CDN untuk utilitas responsif tambahan -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Chart.js CDN untuk Grafik Monitoring Perkembangan Admin & Seller -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

  <!-- Google Fonts: Playfair Display (Editorial Serif), Outfit (UI Sans), Caveat (Handwritten Callout) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&display=swap" rel="stylesheet">

  <!-- Stylesheet Utama Santorini Soft-Neumorphic (Terpisah di assets/css/style.css) -->
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>

  <!-- ========================================================================
       LOADING SCREEN DENGAN LOGO.PNG
       ======================================================================== -->
  <div id="loadingScreen" class="loading-screen">
    <div class="neu-card" style="padding: 38px 48px; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 16px; max-width: 420px;">
      <img
        src="logo.png"
        alt="ShawWite Fruits Logo"
        style="width: 185px; height: auto; object-fit: contain; filter: drop-shadow(0 10px 20px rgba(21, 128, 61, 0.22)); animation: pulse 2s infinite;"
        onerror="this.style.display='none'"
      />
      <div class="loader-spinner" style="width: 38px; height: 38px;"></div>
      <h2 style="font-family: var(--font-serif); font-size: 1.45rem; font-weight: 700; color: var(--text-primary);">
        Memuat ShawWite Fruits...
      </h2>
      <p style="font-size: 0.86rem; color: var(--text-muted);">
        Menyiapkan kesegaran panen buah pilihan terbaik untuk Anda
      </p>
    </div>
  </div>

  <!-- ========================================================================
       MODAL DETAIL PRODUK, PEMESANAN WA & ULASAN/RATING KOMENTAR
       ======================================================================== -->
  <div id="detailModal" class="modal-overlay" role="dialog" aria-modal="true">
    <div class="modal-content neu-card" style="max-width: 820px;">
      <button class="modal-close" onclick="window.closeModal()" aria-label="Tutup Detail">✕</button>
      <div id="modalBody" style="padding: 16px;">
        <!-- Diinjeksi secara dinamis oleh assets/js/app.js -->
      </div>
    </div>
  </div>

  <!-- ========================================================================
       MODAL KONFIRMASI KUSTOM (PENGGANTI CONFIRM BAWAAN)
       ======================================================================== -->
  <div id="confirmModal" class="modal-overlay" role="dialog" aria-modal="true">
    <div class="neu-card" style="max-width: 420px; width: 100%; padding: 28px; text-align: center;">
      <div style="width: 56px; height: 56px; border-radius: 50%; background: rgba(220,38,38,0.12); color: var(--danger); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin: 0 auto 14px;">
        ⚠️
      </div>
      <h3 id="confirmTitle" style="font-family: var(--font-serif); font-size: 1.35rem; font-weight: 700; margin-bottom: 8px;">
        Konfirmasi Tindakan
      </h3>
      <p id="confirmMessage" style="font-size: 0.92rem; color: var(--text-secondary); margin-bottom: 22px;">
        Apakah Anda yakin ingin melanjutkan?
      </p>
      <div style="display: flex; gap: 12px; justify-content: center;">
        <button type="button" id="btnConfirmCancel" class="btn btn-secondary" style="flex: 1;">Batal</button>
        <button type="button" id="btnConfirmOk" class="btn btn-danger" style="flex: 1;">Ya, Lanjutkan</button>
      </div>
    </div>
  </div>

  <!-- TOAST CONTAINER -->
  <div id="toastContainer" class="toast-container"></div>
