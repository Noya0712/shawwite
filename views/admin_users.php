      <!-- ====================================================================
           VIEW 3: DASHBOARD ADMIN — CHART MONITORING & KELOLA USER
           File: views/admin_users.php
           ==================================================================== -->
      <main id="viewAdminUsers" class="view-section" aria-label="Dashboard Admin">

        <!-- Sub-Navigation Bar Admin (Bisa ganti ke Dashboard User, Chart Monitoring, atau Kelola User) -->
        <div class="neu-card" style="padding: 16px 22px; margin-bottom: 22px; display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap;">
          <div>
            <h2 style="font-family: var(--font-serif); font-size: 1.35rem; font-weight: 800; color: var(--text-primary);">
              Dashboard Admin — Monitoring &amp; Manajemen Sistem
            </h2>
            <p style="font-size: 0.84rem; color: var(--text-muted);">
              Beralih antara tampilan Chart Monitoring perkembangan, Manajemen User, atau Tampilan Dashboard User.
            </p>
          </div>
          <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="button" id="btnAdminTabAll" class="btn btn-primary" style="padding: 9px 18px; font-size: 0.84rem;" onclick="window.switchAdminSubTab('all')">
              📊 Chart &amp; Kelola User
            </button>
            <button type="button" id="btnAdminTabChart" class="btn btn-secondary" style="padding: 9px 18px; font-size: 0.84rem;" onclick="window.switchAdminSubTab('chart')">
              📈 Fokus Chart Monitoring
            </button>
            <button type="button" id="btnAdminTabUsers" class="btn btn-secondary" style="padding: 9px 18px; font-size: 0.84rem;" onclick="window.switchAdminSubTab('users')">
              👥 Fokus Kelola User
            </button>
            <button type="button" class="neu-pill-btn" style="padding: 9px 18px; font-size: 0.84rem; color: var(--azure-accent);" onclick="window.triggerNavAction('catalog')">
              🛍️ Tampilan Dashboard User →
            </button>
          </div>
        </div>

        <!-- Metrics Cards Admin -->
        <div class="dashboard-grid">
          <div class="stat-card neu-card">
            <div class="stat-icon">👥</div>
            <div>
              <h3 style="font-size: 0.84rem; color: var(--text-muted); font-weight: 600;">Total Seluruh User</h3>
              <p id="statTotalUsers" style="font-family: var(--font-serif); font-size: 1.95rem; font-weight: 800; color: var(--text-primary);">0</p>
            </div>
          </div>

          <div class="stat-card neu-card">
            <div class="stat-icon">🏪</div>
            <div>
              <h3 style="font-size: 0.84rem; color: var(--text-muted); font-weight: 600;">Total Seller &amp; Admin</h3>
              <p id="statTotalSellers" style="font-family: var(--font-serif); font-size: 1.95rem; font-weight: 800; color: var(--text-primary);">0</p>
            </div>
          </div>

          <div class="stat-card neu-card">
            <div class="stat-icon">🛍️</div>
            <div>
              <h3 style="font-size: 0.84rem; color: var(--text-muted); font-weight: 600;">Total Pembeli Terdaftar</h3>
              <p id="statTotalBuyers" style="font-family: var(--font-serif); font-size: 1.95rem; font-weight: 800; color: var(--text-primary);">0</p>
            </div>
          </div>

          <div class="stat-card neu-card">
            <div class="stat-icon">⭐</div>
            <div>
              <h3 style="font-size: 0.84rem; color: var(--text-muted); font-weight: 600;">Total Ulasan &amp; Pesanan</h3>
              <p id="statAdminTotalActivity" style="font-family: var(--font-serif); font-size: 1.95rem; font-weight: 800; color: var(--text-primary);">0</p>
            </div>
          </div>
        </div>

        <!-- ==================================================================
             BAGIAN 1: CHART PERKEMBANGAN & MONITORING SISTEM (ADMIN)
             ================================================================== -->
        <section id="adminMonitoringSection" style="margin-bottom: 26px;">
          <div style="display: grid; grid-template-columns: 1.35fr 1fr; gap: 24px; margin-bottom: 24px;" class="responsive-two-col">

            <!-- Grafik Perkembangan Kategori, Stok & Pesanan -->
            <div class="neu-card" style="padding: 24px;">
              <div style="margin-bottom: 14px;">
                <h3 style="font-family: var(--font-serif); font-size: 1.28rem; font-weight: 700;">
                  📊 Grafik Perkembangan Katalog &amp; Aktivitas per Kategori
                </h3>
                <p style="font-size: 0.82rem; color: var(--text-muted);">
                  Monitoring stok buah, jumlah pack dipesan pembeli, dan jumlah komentar/ulasan per kategori
                </p>
              </div>
              <div style="position: relative; height: 290px; width: 100%;">
                <canvas id="adminCategoryChart"></canvas>
              </div>
            </div>

            <!-- Grafik Komposisi Role User -->
            <div class="neu-card" style="padding: 24px;">
              <div style="margin-bottom: 14px;">
                <h3 style="font-family: var(--font-serif); font-size: 1.28rem; font-weight: 700;">
                  👥 Komposisi Pengguna Berdasarkan Role
                </h3>
                <p style="font-size: 0.82rem; color: var(--text-muted);">
                  Proporsi akun Admin, Seller, dan Pembeli di database MySQL <code>shawwite</code>
                </p>
              </div>
              <div style="position: relative; height: 290px; width: 100%; display: flex; align-items: center; justify-content: center;">
                <canvas id="adminUserRoleChart"></canvas>
              </div>
            </div>

          </div>

          <!-- Monitoring Komentar & Aktivitas Pembeli Terbaru -->
          <div class="neu-card" style="padding: 24px;">
            <div style="margin-bottom: 14px;">
              <h3 style="font-family: var(--font-serif); font-size: 1.28rem; font-weight: 700;">
                💬 Monitoring Ulasan &amp; Rating Terbaru di Seluruh Seller
              </h3>
              <p style="font-size: 0.82rem; color: var(--text-muted);">
                Rekapitulasi komentar dan rating yang diberikan user pada produk-produk seller
              </p>
            </div>
            <div id="adminReviewsList" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
              <!-- Di-render dinamis oleh assets/js/app.js -->
            </div>
          </div>
        </section>

        <!-- ==================================================================
             BAGIAN 2: MANAJEMEN USER (CRUD TABEL USERS MYSQL)
             ================================================================== -->
        <section id="adminCrudSection" class="dashboard-panels">

          <!-- Left Panel: Form Tambah / Edit User -->
          <div class="neu-card" style="padding: 26px;">
            <h2 id="userFormTitle" style="font-family: var(--font-serif); font-size: 1.4rem; font-weight: 700; margin-bottom: 18px;">
              Tambah User Baru
            </h2>

            <form id="userForm">
              <input type="hidden" id="inputUserId" />

              <div class="form-group">
                <label class="form-label" for="inputUserNama">Nama Lengkap / Nama Toko</label>
                <input type="text" id="inputUserNama" class="form-input" required placeholder="Mis. Hendra Kusuma" />
              </div>

              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="form-group">
                  <label class="form-label" for="inputUserUsername">Username</label>
                  <input type="text" id="inputUserUsername" class="form-input" required placeholder="hendra_k" />
                </div>

                <div class="form-group">
                  <label class="form-label" for="inputUserRole">Jenis User (Role)</label>
                  <select id="inputUserRole" class="form-select" required>
                    <option value="pembeli">Pembeli</option>
                    <option value="seller">Seller (Penjual)</option>
                    <option value="admin">Admin</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="form-label" for="inputUserEmail">Alamat Email</label>
                <input type="email" id="inputUserEmail" class="form-input" required placeholder="hendra@shawwite.com" />
              </div>

              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="form-group">
                  <label class="form-label" for="inputUserWa">Nomor WhatsApp</label>
                  <input type="text" id="inputUserWa" class="form-input" required placeholder="6281234567890" />
                </div>

                <div class="form-group">
                  <label class="form-label" for="inputUserStatus">Status Akun</label>
                  <select id="inputUserStatus" class="form-select" required>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="form-label" for="inputUserAlamat">Alamat / Lokasi</label>
                <input type="text" id="inputUserAlamat" class="form-input" placeholder="Banyuwangi, Jawa Timur" />
              </div>

              <div class="form-group">
                <label class="form-label" for="inputUserPassword">Password <span id="passHintText" style="font-weight:400; color:var(--text-muted);">(Kosongkan jika memakai default)</span></label>
                <input type="password" id="inputUserPassword" class="form-input" placeholder="Ketik password baru..." />
              </div>

              <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Simpan User</button>
                <button type="button" id="btnCancelUserEdit" class="btn btn-secondary" style="display: none;">Batal</button>
              </div>
            </form>
          </div>

          <!-- Right Panel: Tabel Daftar User -->
          <div class="neu-card" style="padding: 26px; overflow: hidden;">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 18px;">
              <div>
                <h2 style="font-family: var(--font-serif); font-size: 1.4rem; font-weight: 700;">
                  Manajemen Pengguna Sistem (Admin)
                </h2>
                <p style="font-size: 0.84rem; color: var(--text-muted);">
                  Kelola hak akses Admin, Seller, dan Pembeli di database MySQL <code>shawwite</code>.
                </p>
              </div>

              <div style="display: flex; gap: 10px; align-items: center;">
                <select id="filterUserRole" class="neu-select-pill" aria-label="Filter Role User">
                  <option value="Semua">Semua Role</option>
                  <option value="admin">Admin</option>
                  <option value="seller">Seller</option>
                  <option value="pembeli">Pembeli</option>
                </select>
              </div>
            </div>

            <div style="overflow-x: auto;">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Pengguna</th>
                    <th>Role</th>
                    <th>Kontak WA</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody id="userTableBody">
                  <!-- Baris tabel user di-render via assets/js/app.js -->
                </tbody>
              </table>
            </div>
          </div>

        </section>
      </main>
