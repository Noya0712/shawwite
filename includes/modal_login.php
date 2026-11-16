  <!-- ========================================================================
       MODAL AKUN & LOGIN PENGGUNA
       File: includes/modal_login.php
       ======================================================================== -->
  <div id="authModal" class="modal-overlay" role="dialog" aria-modal="true">
    <div class="modal-content neu-card" style="max-width: 520px; padding: 28px;">
      <button class="modal-close" onclick="window.closeAuthModal()" aria-label="Tutup Menu Akun">✕</button>

      <div style="margin-bottom: 18px;">
        <span style="font-size: 0.75rem; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase; color: var(--azure-accent);">
          AKUN PENGGUNA • SHAWWITE FRUITS
        </span>
        <h2 style="font-family: var(--font-serif); font-size: 1.65rem; font-weight: 800; color: var(--text-primary); margin-top: 2px;">
          Informasi Sesi &amp; Login
        </h2>
        <p style="font-size: 0.88rem; color: var(--text-secondary);">
          Masuk dengan akun Anda untuk mengakses fitur sesuai hak akses (Admin, Seller, atau Pembeli).
        </p>
      </div>

      <!-- Status Akun yang Sedang Login + Tombol Logout & Halaman Login Penuh -->
      <div id="currentSessionBanner" class="neu-inset-box" style="padding: 14px 16px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
        <div style="font-size: 0.86rem; color: var(--text-secondary);">
          Status saat ini: <strong id="modalCurrentUserName" style="color: var(--text-primary);">Belum Login</strong>
        </div>
        <div style="display: flex; gap: 8px; align-items: center;">
          <a href="login.php" class="neu-pill-btn" style="padding: 6px 14px; font-size: 0.78rem;">
            Halaman Login ↗
          </a>
          <button type="button" id="btnLogoutModal" class="btn btn-danger" style="padding: 6px 14px; font-size: 0.78rem;" onclick="window.logoutUser()">
            Keluar (Logout)
          </button>
        </div>
      </div>

      <!-- Tab Switcher: Form Login vs Daftar Pembeli Baru -->
      <div style="display: flex; gap: 10px; margin-bottom: 16px;">
        <button type="button" id="tabBtnLogin" class="btn btn-primary" style="padding: 8px 18px; font-size: 0.84rem; flex: 1;" onclick="window.switchAuthTab('login')">
          🔑 Masuk (Login)
        </button>
        <button type="button" id="tabBtnRegister" class="btn btn-secondary" style="padding: 8px 18px; font-size: 0.84rem; flex: 1;" onclick="window.switchAuthTab('register')">
          ✨ Daftar Akun Baru
        </button>
      </div>

      <!-- Form Login Standar -->
      <form id="loginForm" style="display: block;">
        <div class="form-group">
          <label class="form-label" for="loginIdentifier">Email atau Username</label>
          <input type="text" id="loginIdentifier" class="form-input" required placeholder="Masukkan email atau username Anda" />
        </div>
        <div class="form-group">
          <label class="form-label" for="loginPassword">Password</label>
          <input type="password" id="loginPassword" class="form-input" required placeholder="Masukkan password Anda" />
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 6px;">
          Login →
        </button>
      </form>

      <!-- Form Registrasi Pembeli Baru -->
      <form id="registerForm" style="display: none;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div class="form-group" style="margin-bottom: 10px;">
            <label class="form-label" for="regNama">Nama Lengkap</label>
            <input type="text" id="regNama" class="form-input" required placeholder="Nama lengkap Anda" />
          </div>
          <div class="form-group" style="margin-bottom: 10px;">
            <label class="form-label" for="regUsername">Username</label>
            <input type="text" id="regUsername" class="form-input" required placeholder="username_anda" />
          </div>
          <div class="form-group" style="margin-bottom: 10px;">
            <label class="form-label" for="regEmail">Email</label>
            <input type="email" id="regEmail" class="form-input" required placeholder="nama@email.com" />
          </div>
          <div class="form-group" style="margin-bottom: 10px;">
            <label class="form-label" for="regWa">Nomor WhatsApp</label>
            <input type="text" id="regWa" class="form-input" required placeholder="628123456789" />
          </div>
          <div class="form-group" style="margin-bottom: 10px;">
            <label class="form-label" for="regAlamat">Alamat</label>
            <input type="text" id="regAlamat" class="form-input" required placeholder="Kota / Alamat pengiriman" />
          </div>
          <div class="form-group" style="margin-bottom: 10px;">
            <label class="form-label" for="regPassword">Password</label>
            <input type="password" id="regPassword" class="form-input" required minlength="4" placeholder="Minimal 4 karakter" />
          </div>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 8px;">
          Daftar Akun →
        </button>
      </form>

    </div>
  </div>
