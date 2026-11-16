      <!-- ====================================================================
           VIEW 1: KATALOG PEMBELI (SHAWWITE FRUITS — TEMA BUAH SEGAR)
           File: views/catalog.php
           ==================================================================== -->
      <main id="viewCatalog" class="view-section active">

        <!-- A. HERO BENTO STAGE -->
        <section class="hero-bento-stage" aria-label="Panen Buah Unggulan Utama">

          <!-- LEFT: Sculpted Wavy Hero Showcase -->
          <div class="hero-wavy-wrapper">
            <div class="hero-art-canvas">
              <svg class="hero-wave-sculpt" viewBox="0 0 900 65" preserveAspectRatio="none">
                <path d="M0,0 L900,0 L900,28 C780,62 650,4 510,20 C360,38 220,64 90,26 C40,12 15,8 0,18 Z" fill="var(--surface-card)"/>
              </svg>

              <img 
                id="heroMainImg"
                src="https://images.unsplash.com/photo-1553279768-865429fa0078?auto=format&fit=crop&w=1200&q=85" 
                alt="Panen Buah Unggulan" 
                class="hero-bg-image"
                onerror="this.src='https://placehold.co/900x500/183E62/FFFFFF?text=ShawWite+Fruits'"
              />
              <div class="hero-gradient-veil"></div>

              <!-- Hero Top Text Content -->
              <div style="position: relative; z-index: 4; padding-top: 14px;">
                <span class="hero-kicker" id="heroKicker">FRESH ORCHARD HARVEST • PANEN BUAH UNGGULAN</span>
                <h2 class="hero-main-title" id="heroTitle">Mangga Harum Manis</h2>
                <p class="hero-sub-location" id="heroCategory">Buah Lokal — Rp 32.000</p>
                <p class="hero-desc-text" id="heroDesc">
                  Mangga pilihan matang pohon dengan rasa manis segar paripurna. Dipetik langsung dari kebun matahari pagi.
                </p>

                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                  <button type="button" id="heroWaBtn" class="hero-cta-glass">
                    <span>Pilih &amp; Beli Sekarang</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                      <line x1="5" y1="12" x2="19" y2="12"></line>
                      <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                  </button>
                  <button type="button" id="heroDetailBtn" class="hero-cta-glass" style="background: rgba(255,255,255,0.2); padding: 11px 18px;">
                    Detail &amp; Ulasan Buah
                  </button>
                </div>
              </div>

              <!-- Bottom-Left Circular Thumbnail Switchers + '+' Button -->
              <div class="hero-thumbs-row" id="heroThumbsContainer" aria-label="Pilih Buah Unggulan">
                <!-- Diinjeksi dinamis dari produk unggulan -->
              </div>
            </div>
          </div>

          <!-- RIGHT: Handwritten Callout + Organic Weather Blob + Upcoming Highlight Card -->
          <div class="hero-right-column">

            <div class="hero-top-cluster">
              <!-- Handwritten "Fresh Harvest 🍎" Callout -->
              <div class="handwritten-callout">
                <span>Panen</span>
                <span style="margin-left: 10px;">Segar!</span>
                <svg width="54" height="18" viewBox="0 0 60 20" fill="none" style="margin-top: 2px; margin-left: 12px;">
                  <path d="M2 12 C20 4, 40 4, 56 8" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                  <path d="M18 17 C28 13, 38 12, 48 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
              </div>

              <!-- Organic Kidney/Pebble Orchard Freshness Widget -->
              <div class="weather-blob-card" onclick="window.filterByQuickCategory('Buah Tropis')" title="Klik untuk melihat Buah Tropis Segar">
                <div class="weather-blob-inner">
                  <svg width="58" height="58" viewBox="0 0 64 64" style="position: absolute; top: 10px; right: 14px; opacity: 0.92; pointer-events: none;">
                    <circle cx="32" cy="32" r="14" fill="#FDE047"/>
                    <circle cx="32" cy="32" r="20" fill="none" stroke="#FEF08A" stroke-width="2" stroke-dasharray="4 4"/>
                  </svg>

                  <div style="position: relative; z-index: 2; margin-top: 12px;">
                    <div class="weather-temp">100%</div>
                    <div class="weather-label">Segar Alami</div>
                  </div>

                  <div class="weather-loc-pill" style="position: relative; z-index: 2;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="#183E62" stroke="none">
                      <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 0 1 0-5 2.5 2.5 0 0 1 0 5z"/>
                    </svg>
                    <span>Kebun Buah Nusantara</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Bottom-Right Card: Panen Minggu Ini -->
            <div class="neu-card upcoming-card">
              <div class="upcoming-header">
                <h3 class="upcoming-title">Rekomendasi Buah Hari Ini</h3>
                <button type="button" class="view-all-link" onclick="window.triggerNavAction('explore')">
                  Lihat Semua <span>→</span>
                </button>
              </div>

              <div class="upcoming-body" id="upcomingHighlightBody">
                <!-- Diinjeksi secara dinamis oleh JS dari produk unggulan ke-2 -->
              </div>
            </div>

          </div>
        </section>

        <!-- B. KATALOG BUAH SEGAR PILIHAN (MOLDED TRAY) -->
        <section id="catalogSectionAnchor" class="catalog-tray-section" aria-label="Katalog Buah Pilihan">
          <div class="section-header-row">
            <div>
              <h2 class="section-serif-title">Katalog Panen Buah Segar Pilihan</h2>
              <p class="section-subtitle">Koleksi buah lokal, impor, tropis, dan parcel segar berkualitas terbaik langsung dari petani &amp; seller.</p>
            </div>

            <!-- Filter & Sort Controls in Tactile Neumorphic Pills -->
            <div class="controls-bar">
              <select id="filterCategory" class="neu-select-pill" aria-label="Filter Kategori Buah">
                <option value="Semua">Semua Kategori</option>
                <option value="Buah Lokal">Buah Lokal</option>
                <option value="Buah Impor">Buah Impor</option>
                <option value="Buah Tropis">Buah Tropis</option>
                <option value="Buah Beri">Buah Beri</option>
                <option value="Paket Parcel">Paket Parcel</option>
              </select>

              <select id="sortOption" class="neu-select-pill" aria-label="Urutkan Buah">
                <option value="unggulan">Urutkan: Unggulan Teratas</option>
                <option value="hargaAsc">Harga: Rendah ke Tinggi</option>
                <option value="hargaDesc">Harga: Tinggi ke Rendah</option>
                <option value="namaAsc">Nama: A - Z</option>
                <option value="stokDesc">Stok: Terbanyak</option>
              </select>

              <button type="button" class="neu-pill-btn" style="padding: 8px 16px; font-size: 0.82rem; color: var(--azure-accent);" onclick="window.resetCatalogFilters()">
                Lihat Semua →
              </button>
            </div>
          </div>

          <!-- 4-Column Product Grid -->
          <div id="productGrid" class="product-grid">
            <!-- Kartu produk di-render via assets/js/app.js -->
          </div>

          <!-- Empty State -->
          <div id="emptyStateCatalog" style="display:none; text-align:center; padding: 44px 20px;" class="neu-inset-box">
            <h3 style="font-family: var(--font-serif); font-size: 1.45rem; margin-bottom: 8px;">
              Tidak ada buah yang ditemukan
            </h3>
            <p style="color: var(--text-secondary); font-size: 0.92rem; margin-bottom: 16px;">
              Coba ubah kata kunci pencarian atau pilih kategori buah lainnya.
            </p>
            <button type="button" class="btn btn-primary" onclick="window.resetCatalogFilters()">
              Tampilkan Semua Buah
            </button>
          </div>
        </section>

        <!-- C. BOTTOM BENTO SECTION (INSPIRASI BUAH SEGAR + AUDIO KEBUN | 4 KATEGORI BUAH + PETA PENGIRIMAN) -->
        <section class="bottom-bento-grid" aria-label="Inspirasi Nutrisi &amp; Kategori Buah">

          <!-- Left Column: Fresh Fruit Nutrition Quote + Orchard Ambient Audio Player -->
          <div style="display: flex; flex-direction: column; justify-content: space-between;">
            <div class="neu-card inspiration-card">
              <div class="inspiration-canvas">
                <img 
                  src="https://images.unsplash.com/photo-1619566636858-adf3ef46400b?auto=format&fit=crop&w=900&q=80" 
                  alt="Keranjang Buah Segar ShawWite"
                  onerror="this.src='https://placehold.co/700x300/2B6CB0/FFFFFF?text=ShawWite+Fruits'"
                />
                <div class="inspiration-veil"></div>

                <div style="position: relative; z-index: 2; max-width: 330px;">
                  <span style="font-family: var(--font-serif); font-size: 1.05rem; font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 8px;">
                    Freshness &amp; Nutrition
                  </span>
                  <blockquote style="font-family: var(--font-serif); font-size: 1.45rem; font-weight: 700; line-height: 1.24; color: var(--text-primary); margin-bottom: 10px;">
                    “Buah segar terbaik hari ini adalah investasi kesehatan esok hari.”
                  </blockquote>
                  <cite style="font-style: normal; font-size: 0.84rem; color: var(--text-muted); font-weight: 500;">
                    — ShawWite Fruits (Fresh Fruits, Healthy Life)
                  </cite>
                </div>
              </div>
            </div>

            <!-- Interactive Orchard Breeze Ambient Player Bar -->
            <div class="neu-card audio-player-pill">
              <div style="display: flex; align-items: center; gap: 14px; flex: 1; min-width: 0;">
                <img 
                  src="https://images.unsplash.com/photo-1519996529931-28324d5a630e?auto=format&fit=crop&w=200&q=80" 
                  alt="Suasana Kebun Buah" 
                  class="audio-cover"
                  onerror="this.src='https://placehold.co/100x100/183E62/FFFFFF?text=Fruits'"
                />
                <div style="flex: 1; min-width: 0;">
                  <div style="font-weight: 700; font-size: 0.96rem; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    Suasana Kebun Buah Pagi
                  </div>
                  <div id="audioStatusSub" style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 8px;">
                    Harmoni Alam &amp; Panen Segar
                  </div>
                  <div style="height: 5px; background: var(--surface-inset); border-radius: 999px; position: relative; cursor: pointer;" onclick="window.scrubAmbientTrack(event)">
                    <div id="audioProgressFill" style="width: 62%; height: 100%; background: var(--azure-accent); border-radius: 999px; position: relative;">
                      <span style="position: absolute; right: -5px; top: -3.5px; width: 12px; height: 12px; border-radius: 50%; background: var(--navy-primary); border: 2px solid #FFF; box-shadow: 0 2px 5px rgba(0,0,0,0.25);"></span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Playback Controls -->
              <div style="display: flex; align-items: center; gap: 12px;">
                <button type="button" onclick="window.skipAmbientTrack(-1)" style="background:none; border:none; color: var(--text-primary); cursor:pointer;" title="Sebelumnya">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><polygon points="19 20 9 12 19 4 19 20"/><line x1="5" y1="19" x2="5" y2="5" stroke="currentColor" stroke-width="2.5"/></svg>
                </button>
                <button type="button" id="btnAmbientPlay" class="audio-play-circle" onclick="window.toggleAmbientAudio()" title="Putar Suasana Kebun Buah">
                  <svg id="iconPlaySvg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <polygon points="6 4 20 12 6 20 6 4"/>
                  </svg>
                </button>
                <button type="button" onclick="window.skipAmbientTrack(1)" style="background:none; border:none; color: var(--text-primary); cursor:pointer;" title="Berikutnya">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 4 15 12 5 20 5 4"/><line x1="19" y1="5" x2="19" y2="19" stroke="currentColor" stroke-width="2.5"/></svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Right Column: 4 Category Squircle Pills + Peta Kebun & Kurir Buah -->
          <div style="display: flex; flex-direction: column; justify-content: space-between;">

            <div class="category-shortcuts-row">
              <button type="button" class="category-squircle-btn" data-cat-btn="Buah Lokal" onclick="window.filterByQuickCategory('Buah Lokal')">
                <div class="category-icon-well" style="background: #D9ECFF; color: #1E5A8A;">🥭</div>
                <span style="font-size: 0.82rem; font-weight: 700; color: var(--text-primary);">Buah Lokal</span>
              </button>

              <button type="button" class="category-squircle-btn" data-cat-btn="Buah Impor" onclick="window.filterByQuickCategory('Buah Impor')">
                <div class="category-icon-well" style="background: #E0F2FE; color: #0284C7;">🍎</div>
                <span style="font-size: 0.82rem; font-weight: 700; color: var(--text-primary);">Buah Impor</span>
              </button>

              <button type="button" class="category-squircle-btn" data-cat-btn="Buah Tropis" onclick="window.filterByQuickCategory('Buah Tropis')">
                <div class="category-icon-well" style="background: #FEF3C7; color: #D97706;">🍌</div>
                <span style="font-size: 0.82rem; font-weight: 700; color: var(--text-primary);">Buah Tropis</span>
              </button>

              <button type="button" class="category-squircle-btn" data-cat-btn="Buah Beri" onclick="window.filterByQuickCategory('Buah Beri')">
                <div class="category-icon-well" style="background: #DBEAFE; color: #1D4ED8;">🍓</div>
                <span style="font-size: 0.82rem; font-weight: 700; color: var(--text-primary);">Buah Beri</span>
              </button>
            </div>

            <!-- Peta Distribusi Kebun & Pengiriman Kurir Buah Segar -->
            <div class="neu-card explore-map-card">
              <div class="explore-map-inner">
                <div style="position: relative; z-index: 2; max-width: 230px;">
                  <h3 style="font-family: var(--font-serif); font-size: 1.35rem; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;">
                    Jangkauan Kebun &amp; Kurir
                  </h3>
                  <p style="font-size: 0.84rem; color: var(--text-secondary); line-height: 1.45; margin-bottom: 16px;">
                    Pengiriman buah segar langsung dari kebun mitra ke rumah Anda dengan jaminan kesegaran.
                  </p>
                  <button type="button" class="neu-pill-btn" style="padding: 9px 18px; font-size: 0.84rem;" onclick="window.openDeliveryMapInfo()">
                    <span>Info Pengiriman</span>
                    <span>→</span>
                  </button>
                </div>

                <div style="flex: 1; display: flex; justify-content: flex-end; align-items: center; position: relative; min-height: 135px;">
                  <svg viewBox="0 0 320 150" style="width: 100%; max-width: 280px; height: auto; filter: drop-shadow(0 6px 12px rgba(24,62,98,0.18));">
                    <path d="M25,35 Q55,15 85,35 T125,55 Q105,85 85,115 Q60,125 45,95 T25,35 Z" fill="#5D9AD1" opacity="0.72"/>
                    <path d="M145,25 Q195,10 245,30 T295,65 Q275,105 235,120 Q185,110 165,80 T145,25 Z" fill="#3B7BB5" opacity="0.78"/>
                    <path d="M230,105 Q260,98 285,118 Q270,138 240,132 Z" fill="#5D9AD1" opacity="0.75"/>
                    <path d="M75,78 Q145,38 205,62 T265,92" fill="none" stroke="#183E62" stroke-width="2" stroke-dasharray="4 4"/>
                    <text x="144" y="56" font-size="16">🍎</text>
                    <circle cx="75" cy="78" r="6" fill="#15803D" stroke="#FFFFFF" stroke-width="2"/>
                    <circle cx="205" cy="62" r="6" fill="#F59E0B" stroke="#FFFFFF" stroke-width="2"/>
                    <circle cx="265" cy="92" r="6" fill="#183E62" stroke="#FFFFFF" stroke-width="2"/>
                  </svg>
                </div>
              </div>
            </div>

          </div>
        </section>

      </main>
