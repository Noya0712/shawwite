    <!-- ======================================================================
         1. LEFT VERTICAL FLOATING PILL SIDEBAR (DENGAN LOGO.PNG & MENU LOGIN)
         File: includes/sidebar.php
         ====================================================================== -->
    <aside class="sidebar-rail" aria-label="Navigasi Utama">
      <div class="brand-emblem" onclick="window.triggerNavAction('catalog')" title="ShawWite Fruits Home">
        <div class="brand-wave-circle" style="overflow: hidden; padding: 3px; background: #FFFFFF;">
          <img src="logo.png" alt="ShawWite Logo" style="width: 100%; height: 100%; object-fit: contain;" onerror="this.style.display='none'" />
        </div>
        <span class="brand-emblem-text">ShawWite</span>
      </div>

      <nav class="sidebar-nav-list">
        <!-- Home / Dashboard User (Bisa dilihat semua orang) -->
        <button type="button" id="btnModeCatalog" class="nav-pill-item active" title="Tampilan Dashboard User &amp; Katalog">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            <polyline points="9 22 9 12 15 12 15 22"/>
          </svg>
          <span>Home</span>
        </button>

        <!-- Explore / Jelajah Katalog -->
        <button type="button" id="btnNavExplore" class="nav-pill-item" onclick="window.triggerNavAction('explore')" title="Jelajahi Katalog">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/>
          </svg>
          <span>Explore</span>
        </button>

        <!-- KHUSUS SELLER: Menu Kelola Produk & Monitoring Komentar/Chart -->
        <button type="button" id="btnModeDashboard" class="nav-pill-item" style="display: none;" title="Dashboard Seller: Kelola Produk, Chart &amp; Komentar">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/>
            <line x1="9" y1="3" x2="9" y2="18"/>
            <line x1="15" y1="6" x2="15" y2="21"/>
          </svg>
          <span>Seller</span>
        </button>

        <!-- KHUSUS ADMIN: Menu Kelola User & Chart Monitoring -->
        <button type="button" id="btnModeUsers" class="nav-pill-item" style="display: none;" title="Dashboard Admin: Chart Monitoring &amp; Kelola User">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
          <span>Admin</span>
        </button>

        <!-- Saved -> Filter Unggulan -->
        <button type="button" id="btnNavSaved" class="nav-pill-item" onclick="window.triggerNavAction('unggulan')" title="Tampilkan Buah Unggulan">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
          </svg>
          <span>Unggulan</span>
        </button>

        <!-- MENU LOGIN / KELUAR -->
        <button type="button" id="btnSidebarLogin" class="nav-pill-item" onclick="window.handleSidebarAuthClick()" title="Menu Login / Akun">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
            <polyline points="10 17 15 12 10 7"/>
            <line x1="15" y1="12" x2="3" y2="12"/>
          </svg>
          <span id="sidebarLoginText">Login</span>
        </button>

        <!-- Theme Switcher -->
        <button type="button" id="btnThemeToggle" class="nav-pill-item" title="Ganti Tema Siang / Malam">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
          </svg>
          <span id="themeLabelText">Malam</span>
        </button>
      </nav>

      <div style="font-size: 0.68rem; color: var(--text-muted); font-weight: 600; text-align: center;" class="hidden lg:block">
        v3.5
      </div>
    </aside>
