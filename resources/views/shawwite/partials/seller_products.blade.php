      <!-- ====================================================================
           VIEW 2: DASHBOARD SELLER — KELOLA PRODUK, CHART MONITORING & KOMENTAR
           File: views/seller_products.php
           ==================================================================== -->
      <main id="viewDashboard" class="view-section" aria-label="Dashboard Seller">

        <!-- Sub-Navigation Bar Seller -->
        <div class="neu-card" style="padding: 16px 22px; margin-bottom: 22px; display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap;">
          <div>
            <h2 style="font-family: var(--font-serif); font-size: 1.35rem; font-weight: 800; color: var(--text-primary);">
              Dashboard Seller &amp; Monitoring Toko
            </h2>
            <p style="font-size: 0.84rem; color: var(--text-muted);">
              Pantau perkembangan produk, grafik pemesanan, komentar pembeli, dan kelola inventaris buah Anda.
            </p>
          </div>
          <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="button" id="btnSellerTabCrud" class="btn btn-primary" style="padding: 9px 18px; font-size: 0.84rem;" onclick="window.switchSellerSubTab('all')">
              📊 Semua Panel
            </button>
            <button type="button" id="btnSellerTabChart" class="btn btn-secondary" style="padding: 9px 18px; font-size: 0.84rem;" onclick="window.switchSellerSubTab('monitoring')">
              📈 Chart &amp; Komentar Produk
            </button>
            <button type="button" id="btnSellerTabForm" class="btn btn-secondary" style="padding: 9px 18px; font-size: 0.84rem;" onclick="window.switchSellerSubTab('crud')">
              🍎 Kelola Produk
            </button>
            <button type="button" class="btn btn-secondary" style="padding: 9px 18px; font-size: 0.84rem;" onclick="window.openProfileModal()" title="Lihat Profil Toko &amp; Edit Alamat/WA/Logo">
              🏪 Edit Profil Toko
            </button>
            <button type="button" class="neu-pill-btn" style="padding: 9px 18px; font-size: 0.84rem; color: var(--azure-accent);" onclick="window.triggerNavAction('catalog')">
              🛍️ Tampilan Dashboard User →
            </button>
          </div>
        </div>

        <!-- Metrics Cards Seller -->
        <div class="dashboard-grid">
          <div class="stat-card neu-card">
            <div class="stat-icon">🍎</div>
            <div>
              <h3 style="font-size: 0.84rem; color: var(--text-muted); font-weight: 600;">Total Produk Katalog</h3>
              <p id="statTotalProducts" style="font-family: var(--font-serif); font-size: 1.95rem; font-weight: 800; color: var(--text-primary);">0</p>
            </div>
          </div>

          <div class="stat-card neu-card">
            <div class="stat-icon">📦</div>
            <div>
              <h3 style="font-size: 0.84rem; color: var(--text-muted); font-weight: 600;">Total Stok Tersedia</h3>
              <p id="statTotalStock" style="font-family: var(--font-serif); font-size: 1.95rem; font-weight: 800; color: var(--text-primary);">0</p>
            </div>
          </div>

          <div class="stat-card neu-card">
            <div class="stat-icon">⭐</div>
            <div>
              <h3 style="font-size: 0.84rem; color: var(--text-muted); font-weight: 600;">Total Komentar &amp; Rating</h3>
              <p id="statSellerTotalReviews" style="font-family: var(--font-serif); font-size: 1.95rem; font-weight: 800; color: var(--text-primary);">0</p>
            </div>
          </div>

          <div class="stat-card neu-card">
            <div class="stat-icon">💬</div>
            <div>
              <h3 style="font-size: 0.84rem; color: var(--text-muted); font-weight: 600;">Total Pesanan Masuk</h3>
              <p id="statSellerTotalInquiries" style="font-family: var(--font-serif); font-size: 1.95rem; font-weight: 800; color: var(--text-primary);">0</p>
            </div>
          </div>
        </div>

        <!-- ==================================================================
             BAGIAN 1: CHART PERKEMBANGAN, MONITORING & KOMENTAR PRODUK SELLER
             ================================================================== -->
        <section id="sellerMonitoringSection" style="margin-bottom: 26px;">
          <div style="display: grid; grid-template-columns: 1.35fr 1fr; gap: 24px; margin-bottom: 24px;" class="responsive-two-col">

            <!-- Grafik Perkembangan Stok, Pesanan & Ulasan per Produk -->
            <div class="neu-card" style="padding: 24px;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 8px;">
                <div>
                  <h3 style="font-family: var(--font-serif); font-size: 1.28rem; font-weight: 700;">
                    📈 Grafik Perkembangan &amp; Monitoring Produk
                  </h3>
                  <p style="font-size: 0.82rem; color: var(--text-muted);">
                    Perbandingan Stok Tersedia, Jumlah Pack Dipesan, dan Ulasan Pembeli per produk
                  </p>
                </div>
              </div>
              <div style="position: relative; height: 290px; width: 100%;">
                <canvas id="sellerProductChart"></canvas>
              </div>
            </div>

            <!-- Grafik Estimasi Omzet & Rating Produk -->
            <div class="neu-card" style="padding: 24px;">
              <div style="margin-bottom: 14px;">
                <h3 style="font-family: var(--font-serif); font-size: 1.28rem; font-weight: 700;">
                  💰 Distribusi Omzet &amp; Peminat Produk
                </h3>
                <p style="font-size: 0.82rem; color: var(--text-muted);">
                  Kontribusi nilai pesanan (Rp) dari produk yang diminati pembeli
                </p>
              </div>
              <div style="position: relative; height: 290px; width: 100%; display: flex; align-items: center; justify-content: center;">
                <canvas id="sellerRevenueChart"></canvas>
              </div>
            </div>

          </div>

          <!-- Daftar Komentar & Rating Pembeli pada Postingan Produk Seller -->
          <div class="neu-card" style="padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
              <div>
                <h3 style="font-family: var(--font-serif); font-size: 1.32rem; font-weight: 700;">
                  💬 Komentar &amp; Rating Pembeli pada Produk Anda
                </h3>
                <p style="font-size: 0.84rem; color: var(--text-muted);">
                  Pantau langsung ulasan dan masukan dari pembeli di setiap postingan produk.
                </p>
              </div>
              <span id="statTotalCategories" style="display:none;">0</span>
            </div>

            <div id="sellerReviewsList" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
              <!-- Di-render dinamis oleh assets/js/app.js -->
            </div>
          </div>
        </section>

        <!-- ==================================================================
             BAGIAN 2: FORM TAMBAH/EDIT PRODUK & TABEL INVENTARIS SELLER
             ================================================================== -->
        <section id="sellerCrudSection" class="dashboard-panels">

          <!-- Left Panel: Form Tambah/Edit & Pengaturan WA Seller -->
          <div class="neu-card" style="padding: 26px;">
            <h2 id="formTitle" style="font-family: var(--font-serif); font-size: 1.4rem; font-weight: 700; margin-bottom: 18px;">
              Tambah Produk Baru
            </h2>

            <form id="productForm">
              <input type="hidden" id="inputId" />

              <div class="form-group">
                <label class="form-label" for="inputNama">Nama Buah</label>
                <input type="text" id="inputNama" class="form-input" required minlength="2" maxlength="80" placeholder="Mis. Jeruk Dekopon Manis" />
              </div>

              <div class="form-group">
                <label class="form-label" for="inputKategori">Kategori</label>
                <select id="inputKategori" class="form-select" required>
                  <option value="Buah Lokal">Buah Lokal</option>
                  <option value="Buah Impor">Buah Impor</option>
                  <option value="Buah Tropis">Buah Tropis</option>
                  <option value="Buah Beri">Buah Beri</option>
                  <option value="Paket Parcel">Paket Parcel</option>
                </select>
              </div>

              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="form-group">
                  <label class="form-label" for="inputHarga">Harga (Rp)</label>
                  <input type="number" id="inputHarga" class="form-input" required min="0" step="1000" placeholder="35000" />
                </div>

                <div class="form-group">
                  <label class="form-label" for="inputStok">Stok Fisik</label>
                  <input type="number" id="inputStok" class="form-input" required min="0" step="1" placeholder="20" />
                </div>
              </div>

              <div class="form-group">
                <label class="form-label" for="inputDeskripsi">Deskripsi Kesegaran</label>
                <textarea id="inputDeskripsi" class="form-input" required maxlength="250" rows="3" placeholder="Ceritakan cita rasa, tekstur, dan keunggulan buah..."></textarea>
              </div>

              <div class="form-group">
                <label class="form-label" for="inputImage">Foto Produk (Opsional)</label>
                <input type="file" id="inputImage" class="form-input" accept="image/*" style="padding: 9px 12px;" />
                <small style="color: var(--text-muted); font-size: 0.76rem; display: block; margin-top: 4px;">
                  Dikompresi otomatis via Canvas &amp; disimpan ke MySQL.
                </small>
              </div>

              <div class="form-group" style="display: flex; align-items: center; gap: 10px; padding: 6px 0;">
                <input type="checkbox" id="inputUnggulan" style="width: 18px; height: 18px; accent-color: var(--navy-primary);" />
                <label for="inputUnggulan" class="form-label" style="margin: 0; cursor: pointer;">
                  ★ Jadikan Produk Unggulan (Tampil di Hero Banner)
                </label>
              </div>

              <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Simpan Produk</button>
                <button type="button" id="btnCancelEdit" class="btn btn-secondary" style="display: none;">Batal</button>
              </div>
            </form>

            <hr style="border: 0; border-top: 1.5px solid var(--border-divider); margin: 28px 0 22px;" />

            <h3 style="font-family: var(--font-serif); font-size: 1.18rem; font-weight: 700; margin-bottom: 12px;">
              Pengaturan WhatsApp Seller
            </h3>
            <div class="form-group">
              <label class="form-label" for="inputWaNumber">Nomor WhatsApp Penerima Pesanan</label>
              <input type="text" id="inputWaNumber" class="form-input" placeholder="628123456789" />
            </div>
            <button type="button" id="btnSaveSettings" class="btn btn-secondary" style="width: 100%;">
              Simpan Nomor WhatsApp
            </button>
          </div>

          <!-- Right Panel: Tabel Inventaris Produk (MySQL) -->
          <div class="neu-card" style="padding: 26px; overflow: hidden;">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 18px;">
              <div>
                <h2 style="font-family: var(--font-serif); font-size: 1.4rem; font-weight: 700;">
                  Daftar Inventaris Buah (Database MySQL)
                </h2>
                <p style="font-size: 0.84rem; color: var(--text-muted);">
                  Kelola stok, harga, rating, dan status unggulan produk secara real-time.
                </p>
              </div>
              <button type="button" id="btnResetData" class="btn btn-danger" style="font-size: 0.8rem; padding: 8px 16px;">
                Reset Data Dummy
              </button>
            </div>

            <div style="overflow-x: auto;">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok &amp; Rating</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody id="tableBody">
                  <!-- Baris tabel di-render via assets/js/app.js -->
                </tbody>
              </table>
            </div>
          </div>

        </section>
      </main>
