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

  <!-- ========================================================================
       MODAL PROFIL SAYA & EDIT TOKO (PEMBELI, SELLER, ADMIN)
       ======================================================================== -->
  <div id="profileModal" class="modal-overlay" role="dialog" aria-modal="true">
    <div class="modal-content neu-card" style="max-width: 580px; padding: 28px;">
      <button class="modal-close" onclick="window.closeProfileModal()" aria-label="Tutup Profil">✕</button>

      <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 18px;">
        <div style="position: relative;">
          <img id="profModalAvatarPreview" src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=200&q=80" alt="Avatar" style="width: 68px; height: 68px; border-radius: 50%; object-fit: cover; border: 3px solid #FFFFFF; box-shadow: 0 6px 18px rgba(18,59,112,0.14);" onerror="this.src='https://placehold.co/120x120/183E62/FFFFFF?text=SW'" />
          <span id="profRoleBadge" class="role-badge role-pembeli" style="position: absolute; bottom: -6px; left: 50%; transform: translateX(-50%); font-size: 0.62rem; padding: 2px 7px; white-space: nowrap;">PEMBELI</span>
        </div>
        <div>
          <h2 id="profModalTitle" style="font-family: var(--font-serif); font-size: 1.45rem; font-weight: 800; color: var(--text-primary); margin: 0;">
            Profil Pengguna
          </h2>
          <p id="profModalSubtitle" style="font-size: 0.82rem; color: var(--text-secondary); margin: 2px 0 0;">
            Kelola identitas, jenis kelamin, foto profil, dan toko Anda.
          </p>
        </div>
      </div>

      <!-- Tab Switcher Profil: Edit Identitas / Toko vs Reset Password -->
      <div style="display: flex; gap: 10px; margin-bottom: 16px;">
        <button type="button" id="tabProfBtnGeneral" class="btn btn-primary" style="flex: 1; padding: 8px 14px; font-size: 0.82rem;" onclick="window.switchProfileTab('general')">
          <span id="tabProfGeneralLabel">👤 Data Profil</span>
        </button>
        <button type="button" id="tabProfBtnSecurity" class="btn btn-secondary" style="flex: 1; padding: 8px 14px; font-size: 0.82rem;" onclick="window.switchProfileTab('security')">
          🔒 Reset Password
        </button>
      </div>

      <!-- TAB 1: FORM DATA PROFIL & TOKO -->
      <form id="profileForm" style="display: block;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <!-- Nama Lengkap / Pemilik -->
          <div class="form-group" style="grid-column: span 2; margin-bottom: 8px;">
            <label class="form-label" for="profNama">Nama Lengkap</label>
            <input type="text" id="profNama" class="form-input" required placeholder="Nama lengkap Anda" />
          </div>

          <!-- Jenis Kelamin -->
          <div class="form-group" style="grid-column: span 2; margin-bottom: 8px;">
            <label class="form-label">Jenis Kelamin</label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
              <label class="gender-choice-pill" id="profGenderL" style="margin-bottom: 0;">
                <input type="radio" name="prof_gender" value="Laki-laki" />
                <span>👨 Laki-laki</span>
              </label>
              <label class="gender-choice-pill" id="profGenderP" style="margin-bottom: 0;">
                <input type="radio" name="prof_gender" value="Perempuan" />
                <span>👩 Perempuan</span>
              </label>
            </div>
          </div>

          <!-- KHUSUS SELLER: Nama Toko & Deskripsi Toko -->
          <div id="sellerStoreFields" style="display: none; grid-column: span 2;">
            <div style="background: rgba(33, 106, 168, 0.06); border: 1.5px dashed rgba(33, 106, 168, 0.3); border-radius: 16px; padding: 14px; margin-bottom: 6px;">
              <div style="font-size: 0.82rem; font-weight: 700; color: #183E62; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                <span>🏪</span> Pengaturan Toko &amp; Kebun Anda
              </div>
              <div class="form-group" style="margin-bottom: 10px;">
                <label class="form-label" for="profNamaToko">Nama Toko / Kebun Buah</label>
                <input type="text" id="profNamaToko" class="form-input" placeholder="Contoh: Kebun Buah Segar Nusantara" />
              </div>
              <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="profDeskripsiToko">Deskripsi Singkat Toko</label>
                <textarea id="profDeskripsiToko" class="form-input" rows="2" placeholder="Jelaskan jenis buah &amp; keunggulan kebun Anda..."></textarea>
              </div>
            </div>
          </div>

          <!-- Nomor WhatsApp -->
          <div class="form-group" style="margin-bottom: 8px;">
            <label class="form-label" for="profNoWa">Nomor WhatsApp</label>
            <input type="text" id="profNoWa" class="form-input" required placeholder="6281234567890" />
          </div>

          <!-- Alamat / Lokasi -->
          <div class="form-group" style="margin-bottom: 8px;">
            <label class="form-label" for="profAlamat">Alamat / Lokasi Kebun</label>
            <input type="text" id="profAlamat" class="form-input" required placeholder="Kabupaten / Kota" />
          </div>

          <!-- Foto Avatar / URL Gambar -->
          <div class="form-group" style="grid-column: span 2; margin-bottom: 8px;">
            <label class="form-label" for="profAvatarUrl">URL Foto Profil / Logo Toko</label>
            <input type="url" id="profAvatarUrl" class="form-input" placeholder="https://..." />
            <div style="display: flex; gap: 6px; margin-top: 8px; align-items: center; flex-wrap: wrap;">
              <span style="font-size: 0.72rem; color: var(--text-muted);">Preset:</span>
              <button type="button" class="neu-pill-btn" style="padding: 2px 8px; font-size: 0.72rem;" onclick="window.pickPresetAvatar('male')">👨 Pria</button>
              <button type="button" class="neu-pill-btn" style="padding: 2px 8px; font-size: 0.72rem;" onclick="window.pickPresetAvatar('female')">👩 Wanita</button>
              <button type="button" class="neu-pill-btn" style="padding: 2px 8px; font-size: 0.72rem;" onclick="window.pickPresetAvatar('orchard')">🍎 Kebun Buah</button>
            </div>
          </div>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 16px; justify-content: flex-end;">
          <button type="button" class="btn btn-secondary" onclick="window.closeProfileModal()">Tutup</button>
          <button type="submit" id="btnSaveProfile" class="btn btn-primary">
            💾 Simpan Perubahan
          </button>
        </div>
      </form>

      <!-- TAB 2: RESET PASSWORD -->
      <form id="passwordResetForm" style="display: none;">
        <div class="neu-inset-box" style="padding: 12px; margin-bottom: 14px; font-size: 0.82rem; color: var(--text-secondary);">
          Gunakan form ini untuk memperbarui password akun Anda secara aman.
        </div>
        <div class="form-group" style="margin-bottom: 10px;">
          <label class="form-label" for="pwdCurrent">Password Saat Ini (Opsional)</label>
          <input type="password" id="pwdCurrent" class="form-input" placeholder="Masukkan password lama Anda" />
        </div>
        <div class="form-group" style="margin-bottom: 10px;">
          <label class="form-label" for="pwdNew">Password Baru (Min. 4 karakter)</label>
          <input type="password" id="pwdNew" class="form-input" required minlength="4" placeholder="Password baru" />
        </div>
        <div class="form-group" style="margin-bottom: 14px;">
          <label class="form-label" for="pwdConfirm">Konfirmasi Password Baru</label>
          <input type="password" id="pwdConfirm" class="form-input" required minlength="4" placeholder="Ulangi password baru" />
        </div>
        <div style="display: flex; gap: 10px; margin-top: 16px; justify-content: flex-end;">
          <button type="button" class="btn btn-secondary" onclick="window.closeProfileModal()">Batal</button>
          <button type="submit" id="btnSavePassword" class="btn btn-primary">
            🔒 Perbarui Password
          </button>
        </div>
      </form>
    </div>
  </div>
