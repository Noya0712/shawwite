      <!-- ====================================================================
           TOP HEADER BAR (GREETING + ROLE SWITCHER + SEARCH + LOGIN/LOGOUT)
           File: includes/topbar.php
           ==================================================================== -->
      <header class="top-header-bar">
        <div>
          <h1 class="greeting-title">
            <span id="greetingNameText">Halo, Pecinta Buah Segar</span>
            <span style="display:inline-block;">👋</span>
            <span id="headerRoleBadge" class="role-badge role-pembeli">PENGUNJUNG</span>
          </h1>
          <p class="greeting-subtitle" id="greetingSubText">
            Temukan buah segar pilihan terbaik dari kebun, lihat ulasan, dan login saat ingin membeli.
          </p>

          <!-- TOMBOL SWITCH TAMPILAN KHUSUS ADMIN & SELLER (Dashboard User vs Chart Monitoring vs Kelola) -->
          <div id="roleViewSwitcherBar" style="display: none; gap: 8px; flex-wrap: wrap; margin-top: 12px;">
            <!-- Diinjeksi dinamis oleh assets/js/app.js saat Admin atau Seller login -->
          </div>
        </div>

        <div class="header-right-cluster">
          <!-- Recessed Neumorphic Pill Search Bar -->
          <div class="search-well">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-secondary); flex-shrink: 0;">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" id="searchInput" placeholder="Cari buah segar (mis. Mangga, Apel, Jeruk)..." aria-label="Cari buah segar" />
          </div>

          <!-- Area Tombol Login / Register (Tampil saat belum login) -->
          <div id="guestAuthButtons" style="display: flex; align-items: center; gap: 10px;">
            <a href="login.php" class="btn btn-primary" style="height: 50px; padding: 0 20px; font-size: 0.86rem;">
              <span>🔑 Login</span>
            </a>
            <a href="register.php" class="neu-pill-btn" style="height: 50px; padding: 0 18px; font-size: 0.86rem;">
              <span>Register</span>
            </a>
          </div>

          <!-- Area User Aktif & Logout (Tampil saat sudah login) -->
          <div id="loggedInUserCluster" style="display: none; align-items: center; gap: 10px;">
            <button type="button" class="neu-pill-btn" style="padding: 8px 16px; height: 50px; font-size: 0.82rem;" onclick="window.openAuthModal()" title="Ganti Role / Info Akun">
              <span>👤</span>
              <span id="activeAccountLabel">Akun</span>
            </button>
            <button type="button" class="btn btn-danger" style="padding: 8px 16px; height: 50px; font-size: 0.82rem;" onclick="window.logoutUser()" title="Keluar dari Akun">
              Keluar
            </button>
            <div class="avatar-frame" onclick="window.openAuthModal()" title="Profil Pengguna">
              <img 
                id="headerAvatarImg"
                src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=200&q=80" 
                alt="Profil Pengguna ShawWite"
                style="width: 100%; height: 100%; object-fit: cover;"
                onerror="this.src='https://placehold.co/120x120/183E62/FFFFFF?text=SW'"
              />
            </div>
          </div>
        </div>
      </header>
