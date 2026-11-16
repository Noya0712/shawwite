/**
 * SHAWWITE FRUITS — SANTORINI NEUMORPHIC PRODUCTION FRONTEND LOGIC
 * File: assets/js/app.js
 * Terhubung ke: api.php (MySQL Database `shawwite`)
 *
 * Alur Utama:
 * 1. Pengunjung (Guest) dapat melihat tampilan Dashboard User / Katalog & membaca ulasan.
 *    Saat akan membeli atau memberi komentar, pengunjung diarahkan ke halaman login.php.
 * 2. Setelah Login (Data tersimpan di MySQL):
 *    - Pembeli : Memilih produk, menghubungi Seller via WA, serta memberi Rating & Komentar di tiap postingan Seller.
 *    - Seller  : Mengelola produk, serta melihat Chart Perkembangan, Monitoring & Komentar pada produk yang mereka jual.
 *    - Admin   : Mengelola User, bisa melihat tampilan Dashboard User maupun menggantinya ke tampilan Chart Perkembangan & Monitoring.
 */

(() => {
  /* ==========================================================================
     1. KONFIGURASI API MYSQL & STATE APLIKASI
     ========================================================================== */
  const SETTINGS_KEY = 'shawwite_fruits_settings_v3';
  const ACTIVE_USER_KEY = 'shawwite_active_user_id_v3';

  const API_URL = (window.location.protocol === 'file:' || window.location.port === '5500')
    ? 'http://localhost/shawwite/api.php'
    : 'api.php';

  const state = {
    currentUser: null,
    demoAccounts: [],
    products: [],
    users: [],
    inquiries: [],
    allReviews: [],
    analytics: null,
    settings: { waNumber: '6281234567890', theme: 'light' },
    currentMode: 'catalog',
    sellerSubTab: 'all',
    adminSubTab: 'all',
    searchQuery: '',
    filterCategory: 'Semua',
    filterUserRole: 'Semua',
    sortBy: 'unggulan',
    onlyUnggulan: false,
    activeHeroId: 'prd-01',
    audioPlaying: false,
    audioTrackIdx: 0,
    selectedQty: 1,
    selectedRating: 5
  };

  let sellerProductChartInstance = null;
  let sellerRevenueChartInstance = null;
  let adminCategoryChartInstance = null;
  let adminUserRoleChartInstance = null;

  const AMBIENT_TRACKS = [
    { title: 'Suasana Kebun Buah Pagi', sub: 'Harmoni Alam & Panen Segar', progress: 62 },
    { title: 'Angin Kebun Jeruk & Apel', sub: 'Akustik Relaksasi Kebun', progress: 38 },
    { title: 'Panen Segar Banyuwangi', sub: 'Suara Alam & Burung Pagi', progress: 78 }
  ];

  const escapeHTML = (str) => {
    const div = document.createElement('div');
    div.textContent = str || '';
    return div.innerHTML;
  };

  const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(number || 0);
  };

  const renderStars = (rating) => {
    const r = Math.round(Number(rating) || 5);
    return '★'.repeat(r) + '☆'.repeat(Math.max(0, 5 - r));
  };

  /* ==========================================================================
     2. HELPER REQUEST KE API MYSQL (`api.php`)
     ========================================================================== */
  async function apiRequest(action, method = 'GET', payload = null) {
    const storedUserId = localStorage.getItem(ACTIVE_USER_KEY);
    const headers = { 'Content-Type': 'application/json' };
    if (storedUserId) {
      headers['X-User-Id'] = storedUserId;
    }

    const options = {
      method,
      headers,
      credentials: 'include'
    };

    if (payload && method !== 'GET') {
      options.body = JSON.stringify(payload);
    }

    const response = await fetch(`${API_URL}?action=${encodeURIComponent(action)}`, options);
    const data = await response.json();
    if (!response.ok || !data.ok) {
      const err = new Error(data.message || 'Terjadi kesalahan pada server.');
      err.requireLogin = Boolean(data.requireLogin);
      throw err;
    }
    return data;
  }

  /* ==========================================================================
     3. CUSTOM TOAST & CONFIRMATION MODAL
     ========================================================================== */
  function showToast(message, icon = '✨') {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = 'neu-toast';
    toast.innerHTML = `<span style="font-size:1.2rem;">${icon}</span><span>${escapeHTML(message)}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(8px)';
      toast.style.transition = 'all 0.25s ease';
      setTimeout(() => toast.remove(), 260);
    }, 3200);
  }

  function showConfirmModal(title, message, onConfirm) {
    const modal = document.getElementById('confirmModal');
    document.getElementById('confirmTitle').textContent = title;
    document.getElementById('confirmMessage').textContent = message;
    modal.classList.add('active');

    const btnOk = document.getElementById('btnConfirmOk');
    const btnCancel = document.getElementById('btnConfirmCancel');

    const cleanup = () => {
      modal.classList.remove('active');
      btnOk.onclick = null;
      btnCancel.onclick = null;
    };

    btnOk.onclick = () => {
      cleanup();
      onConfirm();
    };
    btnCancel.onclick = cleanup;
  }

  /* ==========================================================================
     4. MANAJEMEN TEMA (LIGHT / DARK) & ROLE UI
     ========================================================================== */
  function applyTheme(themeName) {
    document.documentElement.setAttribute('data-theme', themeName);
    const label = document.getElementById('themeLabelText');
    if (label) {
      label.textContent = themeName === 'dark' ? 'Siang' : 'Malam';
    }
  }

  function loadLocalTheme() {
    try {
      const stored = localStorage.getItem(SETTINGS_KEY);
      if (stored) {
        const parsed = JSON.parse(stored);
        if (parsed.theme) state.settings.theme = parsed.theme;
      }
    } catch (e) {}
    applyTheme(state.settings.theme);
  }

  function saveLocalTheme() {
    localStorage.setItem(SETTINGS_KEY, JSON.stringify({ theme: state.settings.theme }));
  }

  /**
   * Mengatur tampilan navigasi, topbar, dan tombol switcher sesuai Role User:
   * - Guest (belum login): hanya bisa melihat tampilan dashboard user (katalog).
   * - Pembeli: melihat katalog, membeli & memberi rating/komentar.
   * - Seller: melihat katalog, mengelola produk, melihat chart perkembangan & komentar produknya.
   * - Admin: melihat seperti dashboard user, mengganti ke tampilan chart monitoring, dan mengelola user.
   */
  function applyRolePermissionsUI() {
    const user = state.currentUser;
    const role = user ? user.role : 'guest';

    const btnSellerDash = document.getElementById('btnModeDashboard');
    const btnAdminUsers = document.getElementById('btnModeUsers');
    const greetingName = document.getElementById('greetingNameText');
    const greetingSub = document.getElementById('greetingSubText');
    const roleBadge = document.getElementById('headerRoleBadge');
    const activeAccountLabel = document.getElementById('activeAccountLabel');
    const avatarImg = document.getElementById('headerAvatarImg');
    const guestAuthButtons = document.getElementById('guestAuthButtons');
    const loggedInUserCluster = document.getElementById('loggedInUserCluster');
    const roleViewSwitcherBar = document.getElementById('roleViewSwitcherBar');
    const sidebarLoginText = document.getElementById('sidebarLoginText');
    const modalCurrentUserName = document.getElementById('modalCurrentUserName');
    const btnLogoutModal = document.getElementById('btnLogoutModal');

    if (btnSellerDash) {
      btnSellerDash.style.display = (role === 'seller') ? 'flex' : 'none';
    }
    if (btnAdminUsers) {
      btnAdminUsers.style.display = (role === 'admin') ? 'flex' : 'none';
    }

    if (guestAuthButtons && loggedInUserCluster) {
      guestAuthButtons.style.display = user ? 'none' : 'flex';
      loggedInUserCluster.style.display = user ? 'flex' : 'none';
    }

    if (greetingName) {
      greetingName.textContent = user ? `Halo, ${user.nama}` : 'Halo, Pecinta Buah Segar';
    }

    if (roleBadge) {
      if (!user) {
        roleBadge.className = 'role-badge role-pembeli';
        roleBadge.textContent = '👀 PENGUNJUNG (BELUM LOGIN)';
      } else {
        roleBadge.className = `role-badge role-${role}`;
        const roleLabels = {
          admin: '👑 ADMIN • MONITORING & USER',
          seller: '🏪 SELLER • PRODUK & MONITORING',
          pembeli: '🛍️ PEMBELI TERDAFTAR'
        };
        roleBadge.textContent = roleLabels[role] || 'PEMBELI';
      }
    }

    if (greetingSub) {
      if (!user) {
        greetingSub.textContent = 'Anda sedang melihat tampilan Dashboard User. Silakan Login saat akan melakukan pembelian atau memberi komentar.';
      } else if (role === 'admin') {
        greetingSub.textContent = 'Anda dapat melihat tampilan Dashboard User atau menggantinya ke tampilan Chart Perkembangan, Monitoring & Kelola User.';
      } else if (role === 'seller') {
        greetingSub.textContent = 'Kelola produk yang Anda jual, pantau grafik perkembangan, dan lihat komentar/rating dari pembeli.';
      } else {
        greetingSub.textContent = 'Pilih buah segar terbaik, hubungi Seller untuk membeli, dan berikan rating/komentar pada produk.';
      }
    }

    if (activeAccountLabel && user) {
      activeAccountLabel.textContent = `${user.username} (${role.toUpperCase()})`;
    }

    if (sidebarLoginText) {
      sidebarLoginText.textContent = user ? 'Akun' : 'Login';
    }

    const btnNavProfile = document.getElementById('btnNavProfile');
    const navProfileText = document.getElementById('navProfileText');
    if (btnNavProfile) {
      btnNavProfile.style.display = user ? 'flex' : 'none';
      if (navProfileText && user) {
        navProfileText.textContent = (role === 'seller') ? 'Toko' : 'Profil';
      }
    }

    if (modalCurrentUserName) {
      modalCurrentUserName.textContent = user
        ? `${user.nama} (@${user.username} — ${role.toUpperCase()})`
        : 'Belum Login (Mode Pengunjung)';
    }

    if (btnLogoutModal) {
      btnLogoutModal.style.display = user ? 'inline-flex' : 'none';
    }

    if (avatarImg && user) {
      const defaultAv = (user.jenis_kelamin === 'Perempuan')
        ? 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=200&q=80'
        : 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=200&q=80';
      avatarImg.src = user.avatar_url || defaultAv;
    }

    // Render Bar Switcher Cepat untuk Admin & Seller di bawah sapaan
    if (roleViewSwitcherBar) {
      if (role === 'admin') {
        roleViewSwitcherBar.style.display = 'flex';
        roleViewSwitcherBar.innerHTML = `
          <button type="button" class="neu-pill-btn" style="padding: 7px 15px; font-size: 0.78rem; ${state.currentMode === 'catalog' ? 'background: var(--navy-primary); color: #FFF;' : ''}" onclick="window.triggerNavAction('catalog')">
            🛍️ Tampilan Dashboard User
          </button>
          <button type="button" class="neu-pill-btn" style="padding: 7px 15px; font-size: 0.78rem; ${(state.currentMode === 'users' && state.adminSubTab === 'chart') ? 'background: var(--navy-primary); color: #FFF;' : ''}" onclick="window.openAdminChartView()">
            📊 Tampilan Chart &amp; Monitoring
          </button>
          <button type="button" class="neu-pill-btn" style="padding: 7px 15px; font-size: 0.78rem; ${(state.currentMode === 'users' && state.adminSubTab !== 'chart') ? 'background: var(--navy-primary); color: #FFF;' : ''}" onclick="window.openAdminUsersView()">
            👥 Kelola User (${state.users.length})
          </button>
        `;
      } else if (role === 'seller') {
        roleViewSwitcherBar.style.display = 'flex';
        roleViewSwitcherBar.innerHTML = `
          <button type="button" class="neu-pill-btn" style="padding: 7px 15px; font-size: 0.78rem; ${state.currentMode === 'catalog' ? 'background: var(--navy-primary); color: #FFF;' : ''}" onclick="window.triggerNavAction('catalog')">
            🛍️ Tampilan Dashboard User
          </button>
          <button type="button" class="neu-pill-btn" style="padding: 7px 15px; font-size: 0.78rem; ${(state.currentMode === 'dashboard' && state.sellerSubTab === 'monitoring') ? 'background: var(--navy-primary); color: #FFF;' : ''}" onclick="window.openSellerMonitoringView()">
            📈 Chart &amp; Komentar Produk
          </button>
          <button type="button" class="neu-pill-btn" style="padding: 7px 15px; font-size: 0.78rem; ${(state.currentMode === 'dashboard' && state.sellerSubTab !== 'monitoring') ? 'background: var(--navy-primary); color: #FFF;' : ''}" onclick="window.openSellerCrudView()">
            🍎 Kelola Produk Seller
          </button>
        `;
      } else {
        roleViewSwitcherBar.style.display = 'none';
      }
    }

    if ((role === 'guest' || role === 'pembeli') && state.currentMode !== 'catalog') {
      switchMode('catalog');
    }
  }

  /* ==========================================================================
     5. KOMPRESI GAMBAR KE CANVAS
     ========================================================================== */
  function compressImage(file) {
    return new Promise((resolve) => {
      const reader = new FileReader();
      reader.onload = (event) => {
        const img = new Image();
        img.onload = () => {
          const canvas = document.createElement('canvas');
          const MAX_WIDTH = 600;
          const MAX_HEIGHT = 600;
          let width = img.width;
          let height = img.height;

          if (width > height) {
            if (width > MAX_WIDTH) {
              height *= MAX_WIDTH / width;
              width = MAX_WIDTH;
            }
          } else {
            if (height > MAX_HEIGHT) {
              width *= MAX_HEIGHT / height;
              height = MAX_HEIGHT;
            }
          }
          canvas.width = width;
          canvas.height = height;
          const ctx = canvas.getContext('2d');
          ctx.drawImage(img, 0, 0, width, height);
          resolve(canvas.toDataURL('image/jpeg', 0.8));
        };
        img.src = event.target.result;
      };
      reader.readAsDataURL(file);
    });
  }

  /* ==========================================================================
     6. RENDER HERO BENTO STAGE & UPCOMING HIGHLIGHT CARD
     ========================================================================== */
  function renderHeroShowcase() {
    if (!state.products.length) return;

    const featuredList = state.products.filter(p => p.unggulan);
    const pool = featuredList.length > 0 ? featuredList : state.products;

    let activeProduct = pool.find(p => p.id === state.activeHeroId) || pool[0];
    state.activeHeroId = activeProduct.id;

    const heroImg = document.getElementById('heroMainImg');
    const heroTitle = document.getElementById('heroTitle');
    const heroCat = document.getElementById('heroCategory');
    const heroDesc = document.getElementById('heroDesc');
    const heroWaBtn = document.getElementById('heroWaBtn');
    const heroDetailBtn = document.getElementById('heroDetailBtn');

    if (heroImg) heroImg.src = activeProduct.imageUrl;
    if (heroTitle) heroTitle.textContent = activeProduct.nama;
    if (heroCat) heroCat.textContent = `${activeProduct.kategori} — ${formatRupiah(activeProduct.harga)}`;
    if (heroDesc) heroDesc.textContent = activeProduct.deskripsi;

    if (heroWaBtn) {
      heroWaBtn.onclick = () => window.handleBuyButtonClick(activeProduct.id);
    }
    if (heroDetailBtn) {
      heroDetailBtn.onclick = () => window.openDetailModal(activeProduct.id);
    }

    const role = state.currentUser ? state.currentUser.role : 'guest';

    const thumbsContainer = document.getElementById('heroThumbsContainer');
    if (thumbsContainer) {
      const topFour = pool.slice(0, 4);
      const plusAction = role === 'seller'
        ? `window.triggerNavAction('dashboard')`
        : (role === 'admin' ? `window.triggerNavAction('users')` : `window.triggerNavAction('explore')`);
      const plusTitle = role === 'seller'
        ? 'Tambah / Kelola Produk (Seller)'
        : (role === 'admin' ? 'Chart Monitoring & Kelola User (Admin)' : 'Jelajahi Semua Produk');

      thumbsContainer.innerHTML = topFour.map(item => `
        <button type="button" 
          class="hero-thumb-circle ${item.id === activeProduct.id ? 'active' : ''}" 
          onclick="window.selectHeroProduct('${item.id}')"
          title="${escapeHTML(item.nama)}">
          <img src="${escapeHTML(item.imageUrl)}" alt="${escapeHTML(item.nama)}" onerror="this.src='https://placehold.co/100x100/183E62/FFFFFF?text=Fruit'" />
        </button>
      `).join('') + `
        <button type="button" class="hero-thumb-plus" onclick="${plusAction}" title="${plusTitle}">
          +
        </button>
      `;
    }

    const upcomingBody = document.getElementById('upcomingHighlightBody');
    if (upcomingBody) {
      const secondHighlight = pool.find(p => p.id !== activeProduct.id) || activeProduct;
      upcomingBody.innerHTML = `
        <img 
          src="${escapeHTML(secondHighlight.imageUrl)}" 
          alt="${escapeHTML(secondHighlight.nama)}" 
          class="upcoming-thumb painterly-img"
          onerror="this.src='https://placehold.co/150x150/183E62/FFFFFF?text=Segar'"
        />
        <div style="flex: 1; min-width: 0;">
          <h4 style="font-weight: 700; font-size: 1rem; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 4px;">
            ${escapeHTML(secondHighlight.nama)}
          </h4>
          <div style="font-size: 0.78rem; color: var(--text-secondary); display: flex; align-items: center; gap: 6px; margin-bottom: 3px;">
            <span>🏪</span>
            <span>${escapeHTML(secondHighlight.seller_nama || 'Kebun ShawWite')} • ★ ${Number(secondHighlight.rating || 4.9).toFixed(1)}</span>
          </div>
          <div style="font-size: 0.78rem; color: var(--navy-primary); font-weight: 700; display: flex; align-items: center; gap: 6px;">
            <span>📦</span>
            <span>${secondHighlight.stok > 0 ? `${secondHighlight.stok} Pack Tersedia` : 'Stok Habis'} • ${formatRupiah(secondHighlight.harga)}</span>
          </div>
        </div>
        <button type="button" class="circle-arrow-btn" onclick="window.openDetailModal('${secondHighlight.id}')" title="Lihat Detail &amp; Komentar">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </button>
      `;
    }
  }

  /* ==========================================================================
     7. RENDER LOGIC (KATALOG BUAH PILIHAN)
     ========================================================================== */
  function getProcessedProducts() {
    let result = [...state.products];

    if (state.onlyUnggulan) {
      result = result.filter(p => p.unggulan);
    }

    if (state.filterCategory !== 'Semua') {
      result = result.filter(p => p.kategori === state.filterCategory);
    }

    if (state.searchQuery) {
      const q = state.searchQuery.toLowerCase();
      result = result.filter(p =>
        p.nama.toLowerCase().includes(q) ||
        p.deskripsi.toLowerCase().includes(q) ||
        p.kategori.toLowerCase().includes(q) ||
        (p.seller_nama && p.seller_nama.toLowerCase().includes(q))
      );
    }

    result.sort((a, b) => {
      switch (state.sortBy) {
        case 'unggulan': return (a.unggulan === b.unggulan) ? 0 : a.unggulan ? -1 : 1;
        case 'hargaAsc': return a.harga - b.harga;
        case 'hargaDesc': return b.harga - a.harga;
        case 'namaAsc': return a.nama.localeCompare(b.nama);
        case 'stokDesc': return b.stok - a.stok;
        default: return 0;
      }
    });

    return result;
  }

  function renderCatalog() {
    renderHeroShowcase();

    const grid = document.getElementById('productGrid');
    const emptyState = document.getElementById('emptyStateCatalog');
    if (!grid || !emptyState) return;

    const items = getProcessedProducts();

    document.querySelectorAll('[data-cat-btn]').forEach(btn => {
      btn.classList.toggle('active', btn.getAttribute('data-cat-btn') === state.filterCategory);
    });

    grid.innerHTML = '';

    if (items.length === 0) {
      grid.style.display = 'none';
      emptyState.style.display = 'block';
      return;
    }

    grid.style.display = 'grid';
    emptyState.style.display = 'none';

    items.forEach((product, idx) => {
      const card = document.createElement('article');
      card.className = 'product-card';

      const isOutOfStock = product.stok === 0;
      const ratingValue = product.rating ? Number(product.rating).toFixed(1) : '4.9';
      const reviewCount = product.review_count || (product.reviews ? product.reviews.length : 0);

      card.innerHTML = `
        <div class="product-img-wrapper" style="cursor: pointer;" onclick="window.openDetailModal('${product.id}')">
          ${product.unggulan ? `<span class="badge-unggulan">★ UNGGULAN</span>` : ''}
          <span class="badge-stok-pill ${isOutOfStock ? 'out' : ''}">
            ${isOutOfStock ? 'Habis' : `Stok: ${product.stok}`}
          </span>
          <img 
            src="${escapeHTML(product.imageUrl)}" 
            alt="${escapeHTML(product.nama)}" 
            class="product-img painterly-img" 
            loading="lazy"
            onerror="this.src='https://placehold.co/400x300/183E62/FFFFFF?text=ShawWite'"
          />
        </div>

        <div class="product-content">
          <h3 class="product-title" title="${escapeHTML(product.nama)}">${escapeHTML(product.nama)}</h3>
          <div class="product-category-sub">
            <span>${escapeHTML(product.kategori)}</span>
            <span class="product-price">${formatRupiah(product.harga)}</span>
          </div>
          <div style="font-size: 0.73rem; color: var(--azure-accent); font-weight: 600; display: flex; justify-content: space-between;">
            <span>🏪 ${escapeHTML(product.seller_nama || 'Kebun ShawWite')}</span>
            <span>💬 ${reviewCount} ulasan</span>
          </div>
          <p style="font-size: 0.78rem; color: var(--text-secondary); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
            ${escapeHTML(product.deskripsi)}
          </p>

          <div class="product-footer-row">
            <div style="display: flex; align-items: center; gap: 8px;">
              <span class="rating-pill" onclick="window.openDetailModal('${product.id}')" style="cursor: pointer;" title="Lihat Rating &amp; Komentar">
                <span style="color: #F59E0B;">★</span> ${ratingValue}
              </span>
              <button 
                type="button"
                onclick="window.handleBuyButtonClick('${product.id}')"
                class="btn-wa-mini"
                style="${isOutOfStock ? 'opacity: 0.45; background: #94A3B8;' : ''}">
                ${isOutOfStock ? 'Habis' : 'Pilih & Beli'}
              </button>
            </div>

            <button type="button" class="circle-arrow-btn" onclick="window.openDetailModal('${product.id}')" title="Detail &amp; Beri Komentar ${escapeHTML(product.nama)}">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
              </svg>
            </button>
          </div>
        </div>
      `;
      grid.appendChild(card);
    });
  }

  /* ==========================================================================
     8. RENDER LOGIC: DASHBOARD SELLER (PRODUK, CHART PERKEMBANGAN & KOMENTAR)
     ========================================================================== */
  function renderDashboard() {
    const tBody = document.getElementById('tableBody');
    if (!tBody) return;
    tBody.innerHTML = '';

    const currentSellerId = state.currentUser ? Number(state.currentUser.id) : 0;
    // Filter produk milik Seller yang sedang login (atau semua produk jika ingin dilihat)
    const sellerProducts = state.products.filter(p => Number(p.seller_id) === currentSellerId);
    const displayProducts = sellerProducts.length > 0 ? sellerProducts : state.products;

    let totalStok = 0;
    let totalReviewsCount = 0;
    let totalInquiriesCount = 0;
    const categories = new Set();

    displayProducts.forEach(p => {
      totalStok += p.stok;
      totalReviewsCount += (p.review_count || 0);
      totalInquiriesCount += (p.inquiry_count || 0);
      categories.add(p.kategori);
    });

    state.products.forEach(p => {
      const isMine = Number(p.seller_id) === currentSellerId;
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td style="font-weight: 700; color: var(--text-primary);">
          <div style="display: flex; align-items: center; gap: 10px;">
            <img src="${escapeHTML(p.imageUrl)}" alt="" style="width: 38px; height: 38px; border-radius: 10px; object-fit: cover;" onerror="this.src='https://placehold.co/60x60/183E62/FFF?text=SW'" />
            <div>
              <div>${p.unggulan ? '<span style="color:#F59E0B;">★</span> ' : ''}${escapeHTML(p.nama)}</div>
              <div style="font-size: 0.72rem; color: ${isMine ? 'var(--leaf-green)' : 'var(--text-muted)'}; font-weight: 600;">
                🏪 ${escapeHTML(p.seller_nama || 'ShawWite')} ${isMine ? '(Produk Anda)' : ''}
              </div>
            </div>
          </div>
        </td>
        <td>
          <span style="background: var(--surface-inset); padding: 4px 10px; border-radius: 999px; font-size: 0.78rem; font-weight: 600;">
            ${escapeHTML(p.kategori)}
          </span>
        </td>
        <td style="font-weight: 700; color: var(--navy-primary);">${formatRupiah(p.harga)}</td>
        <td>
          <div style="font-weight: 600; color: ${p.stok === 0 ? 'var(--danger)' : 'var(--leaf-green)'};">
            ${p.stok === 0 ? 'Habis (0)' : `${p.stok} pack`}
          </div>
          <div style="font-size: 0.73rem; color: #D97706; font-weight: 700;">
            ★ ${Number(p.rating || 4.9).toFixed(1)} (${p.review_count || 0} komentar)
          </div>
        </td>
        <td>
          <div style="display: flex; gap: 10px;">
            <button type="button" style="background:none; border:none; color: var(--azure-accent); font-weight:700; cursor:pointer; font-size:0.84rem;" onclick="window.editProduct('${p.id}')">Edit</button>
            <button type="button" style="background:none; border:none; color: var(--danger); font-weight:700; cursor:pointer; font-size:0.84rem;" onclick="window.deleteProduct('${p.id}')">Hapus</button>
          </div>
        </td>
      `;
      tBody.appendChild(tr);
    });

    document.getElementById('statTotalProducts').textContent = displayProducts.length;
    document.getElementById('statTotalStock').textContent = totalStok;
    const revEl = document.getElementById('statSellerTotalReviews');
    if (revEl) revEl.textContent = totalReviewsCount;
    const inqEl = document.getElementById('statSellerTotalInquiries');
    if (inqEl) inqEl.textContent = totalInquiriesCount;

    renderSellerMonitoringCharts(displayProducts);
    renderSellerReviewsFeed(currentSellerId);
  }

  function renderSellerMonitoringCharts(sellerProducts) {
    if (typeof window.Chart === 'undefined') return;

    const ctxProd = document.getElementById('sellerProductChart');
    const ctxRev = document.getElementById('sellerRevenueChart');
    if (!ctxProd || !ctxRev) return;

    const labels = sellerProducts.map(p => p.nama.length > 16 ? p.nama.slice(0, 16) + '…' : p.nama);
    const stokData = sellerProducts.map(p => p.stok);
    const orderedData = sellerProducts.map(p => p.total_qty_ordered || 0);
    const reviewData = sellerProducts.map(p => p.review_count || 0);
    const omzetData = sellerProducts.map(p => p.total_omzet || (p.harga * 2));

    if (sellerProductChartInstance) sellerProductChartInstance.destroy();
    sellerProductChartInstance = new window.Chart(ctxProd, {
      type: 'bar',
      data: {
        labels,
        datasets: [
          {
            label: 'Stok Tersedia (Pack)',
            data: stokData,
            backgroundColor: 'rgba(33, 106, 168, 0.78)',
            borderColor: '#183E62',
            borderWidth: 1.5,
            borderRadius: 8
          },
          {
            label: 'Terjual / Dipesan (Pack)',
            data: orderedData,
            backgroundColor: 'rgba(21, 128, 61, 0.82)',
            borderColor: '#15803D',
            borderWidth: 1.5,
            borderRadius: 8
          },
          {
            label: 'Jumlah Komentar',
            data: reviewData,
            backgroundColor: 'rgba(245, 158, 11, 0.85)',
            borderColor: '#D97706',
            borderWidth: 1.5,
            borderRadius: 8
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom' }
        },
        scales: {
          y: { beginAtZero: true }
        }
      }
    });

    if (sellerRevenueChartInstance) sellerRevenueChartInstance.destroy();
    sellerRevenueChartInstance = new window.Chart(ctxRev, {
      type: 'doughnut',
      data: {
        labels,
        datasets: [{
          data: omzetData,
          backgroundColor: [
            '#183E62', '#2B6CB0', '#F59E0B', '#15803D', '#63B3ED', '#D97706', '#3B82F6', '#10B981'
          ],
          borderWidth: 2,
          borderColor: '#F7F4EE'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom' }
        }
      }
    });
  }

  function renderSellerReviewsFeed(sellerId) {
    const container = document.getElementById('sellerReviewsList');
    if (!container) return;

    let reviews = [];
    state.products.forEach(p => {
      if (!sellerId || Number(p.seller_id) === Number(sellerId) || state.products.filter(x => Number(x.seller_id) === Number(sellerId)).length === 0) {
        (p.reviews || []).forEach(r => {
          reviews.push({
            ...r,
            nama_produk: p.nama,
            gambar_produk: p.imageUrl,
            kode_produk: p.id,
            nama_seller: p.seller_nama
          });
        });
      }
    });

    reviews.sort((a, b) => b.id - a.id);

    if (reviews.length === 0) {
      container.innerHTML = `
        <div class="neu-inset-box" style="padding: 20px; text-align: center; grid-column: 1 / -1; color: var(--text-muted);">
          Belum ada komentar atau rating pada produk Anda.
        </div>
      `;
      return;
    }

    container.innerHTML = reviews.map(r => `
      <div class="review-item-card">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
          <div style="display: flex; align-items: center; gap: 10px;">
            <img src="${escapeHTML(r.avatar_user)}" alt="" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;" onerror="this.src='https://placehold.co/50x50/183E62/FFF?text=U'" />
            <div>
              <div style="font-weight: 700; font-size: 0.88rem; color: var(--text-primary);">${escapeHTML(r.nama_user)}</div>
              <div style="font-size: 0.72rem; color: var(--azure-accent); font-weight: 700;">🍎 Produk: ${escapeHTML(r.nama_produk)}</div>
            </div>
          </div>
          <span style="color: #F59E0B; font-weight: 800; font-size: 0.9rem;">${renderStars(r.rating)}</span>
        </div>
        <p style="font-size: 0.84rem; color: var(--text-secondary); line-height: 1.45;">
          “${escapeHTML(r.komentar)}”
        </p>
        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.72rem; color: var(--text-muted);">
          <span>📅 ${escapeHTML(r.created_at || '')}</span>
          <button type="button" style="background:none; border:none; color:var(--azure-accent); font-weight:700; cursor:pointer;" onclick="window.openDetailModal('${r.kode_produk}')">
            Lihat Postingan →
          </button>
        </div>
      </div>
    `).join('');
  }

  /* ==========================================================================
     9. RENDER LOGIC: DASHBOARD ADMIN (CHART MONITORING & KELOLA USER)
     ========================================================================== */
  function renderAdminUsers() {
    const tBody = document.getElementById('userTableBody');
    if (!tBody) return;
    tBody.innerHTML = '';

    const totalUsers = state.users.length;
    const totalSellersAndAdmins = state.users.filter(u => u.role === 'seller' || u.role === 'admin').length;
    const totalBuyers = state.users.filter(u => u.role === 'pembeli').length;

    let totalReviewsAll = 0;
    let totalInquiriesAll = 0;
    state.products.forEach(p => {
      totalReviewsAll += (p.review_count || 0);
      totalInquiriesAll += (p.inquiry_count || 0);
    });

    document.getElementById('statTotalUsers').textContent = totalUsers;
    document.getElementById('statTotalSellers').textContent = totalSellersAndAdmins;
    document.getElementById('statTotalBuyers').textContent = totalBuyers;
    const actEl = document.getElementById('statAdminTotalActivity');
    if (actEl) actEl.textContent = `${totalReviewsAll} Ulasan • ${totalInquiriesAll} Order`;

    let filtered = [...state.users];
    if (state.filterUserRole !== 'Semua') {
      filtered = filtered.filter(u => u.role === state.filterUserRole);
    }

    filtered.forEach(u => {
      const isSelf = state.currentUser && Number(state.currentUser.id) === Number(u.id);
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>
          <div style="display: flex; align-items: center; gap: 10px;">
            <img src="${escapeHTML(u.avatar_url || '')}" alt="" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover;" onerror="this.src='https://placehold.co/60x60/183E62/FFF?text=U'" />
            <div>
              <div style="font-weight: 700; color: var(--text-primary);">
                ${escapeHTML(u.nama)} ${isSelf ? '<span style="font-size:0.7rem; color:var(--azure-accent);">(Anda)</span>' : ''}
              </div>
              <div style="font-size: 0.75rem; color: var(--text-muted);">
                @${escapeHTML(u.username)} • ${escapeHTML(u.email)}
              </div>
            </div>
          </div>
        </td>
        <td>
          <span class="role-badge role-${escapeHTML(u.role)}">
            ${escapeHTML(u.role.toUpperCase())}
          </span>
        </td>
        <td style="font-size: 0.84rem; font-weight: 600; color: var(--text-secondary);">
          ${escapeHTML(u.no_wa || '-')}
        </td>
        <td>
          <span style="padding: 4px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: 700; background: ${u.status === 'aktif' ? 'rgba(21,128,61,0.14)' : 'rgba(220,38,38,0.14)'}; color: ${u.status === 'aktif' ? 'var(--leaf-green)' : 'var(--danger)'};">
            ${u.status === 'aktif' ? '● Aktif' : '○ Nonaktif'}
          </span>
        </td>
        <td>
          <div style="display: flex; gap: 10px;">
            <button type="button" style="background:none; border:none; color: var(--azure-accent); font-weight:700; cursor:pointer; font-size:0.84rem;" onclick="window.editUser(${u.id})">Edit</button>
            ${!isSelf ? `<button type="button" style="background:none; border:none; color: var(--danger); font-weight:700; cursor:pointer; font-size:0.84rem;" onclick="window.deleteUser(${u.id})">Hapus</button>` : ''}
          </div>
        </td>
      `;
      tBody.appendChild(tr);
    });

    renderAdminMonitoringCharts();
    renderAdminReviewsFeed();
  }

  function renderAdminMonitoringCharts() {
    if (typeof window.Chart === 'undefined') return;

    const ctxCat = document.getElementById('adminCategoryChart');
    const ctxRole = document.getElementById('adminUserRoleChart');
    if (!ctxCat || !ctxRole) return;

    const catMap = {};
    state.products.forEach(p => {
      if (!catMap[p.kategori]) {
        catMap[p.kategori] = { stok: 0, ordered: 0, reviews: 0 };
      }
      catMap[p.kategori].stok += p.stok;
      catMap[p.kategori].ordered += (p.total_qty_ordered || 0);
      catMap[p.kategori].reviews += (p.review_count || 0);
    });

    const catLabels = Object.keys(catMap);
    const catStok = catLabels.map(k => catMap[k].stok);
    const catOrdered = catLabels.map(k => catMap[k].ordered);
    const catReviews = catLabels.map(k => catMap[k].reviews);

    if (adminCategoryChartInstance) adminCategoryChartInstance.destroy();
    adminCategoryChartInstance = new window.Chart(ctxCat, {
      type: 'bar',
      data: {
        labels: catLabels,
        datasets: [
          {
            label: 'Total Stok Kategori',
            data: catStok,
            backgroundColor: 'rgba(24, 62, 98, 0.82)',
            borderRadius: 8
          },
          {
            label: 'Pack Dipesan Pembeli',
            data: catOrdered,
            backgroundColor: 'rgba(21, 128, 61, 0.82)',
            borderRadius: 8
          },
          {
            label: 'Jumlah Komentar & Rating',
            data: catReviews,
            backgroundColor: 'rgba(245, 158, 11, 0.88)',
            borderRadius: 8
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom' }
        },
        scales: {
          y: { beginAtZero: true }
        }
      }
    });

    const adminCount = state.users.filter(u => u.role === 'admin').length || 2;
    const sellerCount = state.users.filter(u => u.role === 'seller').length || 2;
    const buyerCount = state.users.filter(u => u.role === 'pembeli').length || 4;

    if (adminUserRoleChartInstance) adminUserRoleChartInstance.destroy();
    adminUserRoleChartInstance = new window.Chart(ctxRole, {
      type: 'doughnut',
      data: {
        labels: ['Admin', 'Seller (Penjual)', 'Pembeli'],
        datasets: [{
          data: [adminCount, sellerCount, buyerCount],
          backgroundColor: ['#F59E0B', '#15803D', '#216AA8'],
          borderWidth: 3,
          borderColor: '#F7F4EE'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom' }
        }
      }
    });
  }

  function renderAdminReviewsFeed() {
    const container = document.getElementById('adminReviewsList');
    if (!container) return;

    let reviews = [];
    state.products.forEach(p => {
      (p.reviews || []).forEach(r => {
        reviews.push({
          ...r,
          nama_produk: p.nama,
          kode_produk: p.id,
          nama_seller: p.seller_nama
        });
      });
    });

    reviews.sort((a, b) => b.id - a.id);

    container.innerHTML = reviews.slice(0, 8).map(r => `
      <div class="review-item-card">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
          <div style="display: flex; align-items: center; gap: 10px;">
            <img src="${escapeHTML(r.avatar_user)}" alt="" style="width: 34px; height: 34px; border-radius: 50%; object-fit: cover;" onerror="this.src='https://placehold.co/50x50/183E62/FFF?text=U'" />
            <div>
              <div style="font-weight: 700; font-size: 0.86rem; color: var(--text-primary);">${escapeHTML(r.nama_user)}</div>
              <div style="font-size: 0.72rem; color: var(--azure-accent); font-weight: 700;">
                🍎 ${escapeHTML(r.nama_produk)} (${escapeHTML(r.nama_seller)})
              </div>
            </div>
          </div>
          <span style="color: #F59E0B; font-weight: 800; font-size: 0.88rem;">${renderStars(r.rating)}</span>
        </div>
        <p style="font-size: 0.83rem; color: var(--text-secondary);">“${escapeHTML(r.komentar)}”</p>
      </div>
    `).join('');
  }

  /* ==========================================================================
     10. RENDER MODAL PILIH AKUN DEMO (3 ROLES)
     ========================================================================== */
  function renderDemoAccountsModal() {
    const container = document.getElementById('demoAccountsList');
    if (!container) return;

    const currentId = state.currentUser ? Number(state.currentUser.id) : 0;
    const roleDesc = {
      admin: 'Mengelola User & Chart Monitoring',
      seller: 'Mengelola Produk, Chart & Komentar',
      pembeli: 'Membeli Buah & Memberi Rating/Komentar'
    };

    container.innerHTML = state.demoAccounts.map(acc => {
      const isCurrent = Number(acc.id) === currentId;
      return `
        <button type="button" class="demo-role-card ${isCurrent ? 'active-account' : ''}" onclick="window.quickSwitchUser(${acc.id})">
          <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
            <img src="${escapeHTML(acc.avatar_url || '')}" alt="" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; flex-shrink: 0;" onerror="this.src='https://placehold.co/60x60/183E62/FFF?text=SW'" />
            <div style="min-width: 0;">
              <div style="font-weight: 700; font-size: 0.88rem; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                ${escapeHTML(acc.nama)}
              </div>
              <div style="font-size: 0.73rem; color: var(--text-secondary);">
                ${escapeHTML(roleDesc[acc.role] || '')}
              </div>
            </div>
          </div>
          <span class="role-badge role-${escapeHTML(acc.role)}" style="flex-shrink: 0;">
            ${escapeHTML(acc.role)}
          </span>
        </button>
      `;
    }).join('');
  }

  /* ==========================================================================
     11. EVENT LISTENERS & INTERAKSI CRUD MYSQL
     ========================================================================== */
  function bindEvents() {
    document.getElementById('btnModeCatalog').addEventListener('click', () => switchMode('catalog'));

    document.getElementById('btnModeDashboard').addEventListener('click', () => {
      if (!state.currentUser || state.currentUser.role !== 'seller') {
        showToast('Hanya Seller yang dapat mengakses halaman ini!', '🔒');
        return;
      }
      switchMode('dashboard');
    });

    document.getElementById('btnModeUsers').addEventListener('click', () => {
      if (!state.currentUser || state.currentUser.role !== 'admin') {
        showToast('Hanya Admin yang dapat mengakses halaman ini!', '🔒');
        return;
      }
      switchMode('users');
    });

    document.getElementById('btnThemeToggle').addEventListener('click', () => {
      state.settings.theme = state.settings.theme === 'light' ? 'dark' : 'light';
      saveLocalTheme();
      applyTheme(state.settings.theme);
      showToast(`Tema ${state.settings.theme === 'dark' ? 'Malam Aegean' : 'Siang Santorini'} diaktifkan`, '🎨');
    });

    document.getElementById('searchInput').addEventListener('input', (e) => {
      state.searchQuery = e.target.value;
      if (state.currentMode !== 'catalog') switchMode('catalog');
      renderCatalog();
    });

    document.getElementById('filterCategory').addEventListener('change', (e) => {
      state.filterCategory = e.target.value;
      state.onlyUnggulan = false;
      renderCatalog();
    });

    document.getElementById('sortOption').addEventListener('change', (e) => {
      state.sortBy = e.target.value;
      renderCatalog();
    });

    const filterUserRoleEl = document.getElementById('filterUserRole');
    if (filterUserRoleEl) {
      filterUserRoleEl.addEventListener('change', (e) => {
        state.filterUserRole = e.target.value;
        renderAdminUsers();
      });
    }

    // Seller: Simpan Nomor WhatsApp ke MySQL
    document.getElementById('btnSaveSettings').addEventListener('click', async () => {
      const val = document.getElementById('inputWaNumber').value.trim();
      if (!val) return;
      try {
        const res = await apiRequest('settings_save', 'POST', { waNumber: val });
        state.settings.waNumber = res.settings.waNumber;
        if (res.currentUser) state.currentUser = res.currentUser;
        if (res.products) state.products = res.products;
        renderCatalog();
        showToast(res.message, '✅');
      } catch (err) {
        showToast(err.message, '⚠️');
      }
    });

    // Seller: Tambah / Edit Produk ke MySQL
    document.getElementById('productForm').addEventListener('submit', async (e) => {
      e.preventDefault();

      const id = document.getElementById('inputId').value;
      const harga = parseInt(document.getElementById('inputHarga').value, 10);
      const stok = parseInt(document.getElementById('inputStok').value, 10);

      if (harga < 0 || stok < 0) {
        showToast('Harga dan Stok tidak boleh bernilai negatif!', '⚠️');
        return;
      }

      let newImageUrl = 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?auto=format&fit=crop&w=800&q=80';
      const fileInput = document.getElementById('inputImage');

      if (fileInput.files && fileInput.files[0]) {
        newImageUrl = await compressImage(fileInput.files[0]);
      } else if (id) {
        const existing = state.products.find(p => p.id === id);
        if (existing) newImageUrl = existing.imageUrl;
      }

      const productData = {
        id: id || '',
        nama: document.getElementById('inputNama').value.trim(),
        kategori: document.getElementById('inputKategori').value,
        harga: harga,
        stok: stok,
        deskripsi: document.getElementById('inputDeskripsi').value.trim(),
        imageUrl: newImageUrl,
        unggulan: document.getElementById('inputUnggulan').checked
      };

      try {
        const res = await apiRequest('product_save', 'POST', productData);
        state.products = res.products;
        resetForm();
        renderDashboard();
        renderCatalog();
        showToast(res.message, '🎉');
      } catch (err) {
        showToast(err.message, '⚠️');
      }
    });

    document.getElementById('btnCancelEdit').addEventListener('click', resetForm);

    document.getElementById('btnResetData').addEventListener('click', () => {
      showConfirmModal(
        'Reset Database ke Data Dummy Awal?',
        'Seluruh perubahan akan dikembalikan ke data dummy awal di database MySQL shawwite.',
        async () => {
          try {
            const res = await apiRequest('product_reset_dummy', 'POST');
            state.products = res.products;
            renderDashboard();
            renderCatalog();
            showToast(res.message, '🔄');
          } catch (err) {
            showToast(err.message, '⚠️');
          }
        }
      );
    });

    // Admin: Tambah / Edit User ke MySQL
    document.getElementById('userForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const payload = {
        id: document.getElementById('inputUserId').value,
        nama: document.getElementById('inputUserNama').value.trim(),
        username: document.getElementById('inputUserUsername').value.trim(),
        role: document.getElementById('inputUserRole').value,
        email: document.getElementById('inputUserEmail').value.trim(),
        no_wa: document.getElementById('inputUserWa').value.trim(),
        status: document.getElementById('inputUserStatus').value,
        alamat: document.getElementById('inputUserAlamat').value.trim(),
        password: document.getElementById('inputUserPassword').value
      };

      try {
        const res = await apiRequest('user_save', 'POST', payload);
        state.users = res.users;
        if (res.demoAccounts) state.demoAccounts = res.demoAccounts;
        if (res.products) state.products = res.products;
        resetUserForm();
        renderAdminUsers();
        renderDemoAccountsModal();
        renderCatalog();
        applyRolePermissionsUI();
        showToast(res.message, '✅');
      } catch (err) {
        showToast(err.message, '⚠️');
      }
    });

    document.getElementById('btnCancelUserEdit').addEventListener('click', resetUserForm);

    // Form Login di dalam Modal
    const loginFormEl = document.getElementById('loginForm');
    if (loginFormEl) {
      loginFormEl.addEventListener('submit', async (e) => {
        e.preventDefault();
        const identifier = document.getElementById('loginIdentifier').value.trim();
        const password = document.getElementById('loginPassword').value;

        try {
          const res = await apiRequest('login', 'POST', { identifier, password });
          loginFormEl.reset();
          handleAuthSuccess(res);
        } catch (err) {
          showToast(err.message, '⚠️');
        }
      });
    }

    // Form Register di dalam Modal
    const regFormEl = document.getElementById('registerForm');
    if (regFormEl) {
      regFormEl.addEventListener('submit', async (e) => {
        e.preventDefault();
        const payload = {
          nama: document.getElementById('regNama').value.trim(),
          username: document.getElementById('regUsername').value.trim(),
          email: document.getElementById('regEmail').value.trim(),
          no_wa: document.getElementById('regWa').value.trim(),
          alamat: document.getElementById('regAlamat').value.trim(),
          password: document.getElementById('regPassword').value
        };

        try {
          const res = await apiRequest('register', 'POST', payload);
          regFormEl.reset();
          handleAuthSuccess(res);
        } catch (err) {
          showToast(err.message, '⚠️');
        }
      });
    }
  }

  function handleAuthSuccess(res) {
    state.currentUser = res.currentUser;
    localStorage.setItem(ACTIVE_USER_KEY, String(res.currentUser.id));

    if (res.users) state.users = res.users;
    if (res.inquiries) state.inquiries = res.inquiries;
    if (res.allReviews) state.allReviews = res.allReviews;
    if (res.analytics) state.analytics = res.analytics;

    if (res.currentUser.role === 'seller' && res.currentUser.no_wa) {
      document.getElementById('inputWaNumber').value = res.currentUser.no_wa;
    }

    if (res.currentUser.role === 'admin') {
      switchMode('users');
    } else if (res.currentUser.role === 'seller') {
      switchMode('dashboard');
    } else {
      switchMode('catalog');
    }

    applyRolePermissionsUI();
    renderDemoAccountsModal();
    window.closeAuthModal();
    showToast(res.message || `Login berhasil sebagai ${res.currentUser.role.toUpperCase()}`, '🔑');
  }

  function clearSidebarActive() {
    ['btnModeCatalog', 'btnNavExplore', 'btnModeDashboard', 'btnModeUsers', 'btnNavSaved'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.classList.remove('active');
    });
  }

  function switchMode(mode) {
    const role = state.currentUser ? state.currentUser.role : 'guest';

    if (mode === 'dashboard' && role !== 'seller') {
      showToast('Halaman Kelola Produk khusus untuk user Seller!', '🔒');
      return;
    }
    if (mode === 'users' && role !== 'admin') {
      showToast('Halaman Admin khusus untuk user Admin!', '🔒');
      return;
    }

    state.currentMode = mode;
    clearSidebarActive();

    const btnCat = document.getElementById('btnModeCatalog');
    const btnDash = document.getElementById('btnModeDashboard');
    const btnUsers = document.getElementById('btnModeUsers');

    const viewCat = document.getElementById('viewCatalog');
    const viewDash = document.getElementById('viewDashboard');
    const viewUsers = document.getElementById('viewAdminUsers');

    viewCat.classList.remove('active');
    viewDash.classList.remove('active');
    viewUsers.classList.remove('active');

    if (mode === 'catalog') {
      btnCat.classList.add('active');
      viewCat.classList.add('active');
      renderCatalog();
    } else if (mode === 'dashboard') {
      btnDash.classList.add('active');
      viewDash.classList.add('active');
      renderDashboard();
    } else if (mode === 'users') {
      btnUsers.classList.add('active');
      viewUsers.classList.add('active');
      renderAdminUsers();
    }

    applyRolePermissionsUI();
  }

  function resetForm() {
    document.getElementById('productForm').reset();
    document.getElementById('inputId').value = '';
    document.getElementById('formTitle').textContent = 'Tambah Produk Baru';
    document.getElementById('btnCancelEdit').style.display = 'none';
    document.getElementById('inputImage').value = '';
  }

  function resetUserForm() {
    document.getElementById('userForm').reset();
    document.getElementById('inputUserId').value = '';
    document.getElementById('userFormTitle').textContent = 'Tambah User Baru';
    document.getElementById('btnCancelUserEdit').style.display = 'none';
    document.getElementById('passHintText').textContent = '(Kosongkan jika memakai default)';
  }

  /* ==========================================================================
     12. AMBIENT WEB AUDIO SYNTHESIZER
     ========================================================================== */
  let audioCtx = null;
  let ambientInterval = null;

  function playChimeNote() {
    try {
      if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
      if (audioCtx.state === 'suspended') audioCtx.resume();

      const freqs = [261.63, 329.63, 392.00, 493.88, 523.25, 659.25];
      const freq = freqs[Math.floor(Math.random() * freqs.length)];

      const osc = audioCtx.createOscillator();
      const gain = audioCtx.createGain();
      osc.type = 'sine';
      osc.frequency.setValueAtTime(freq, audioCtx.currentTime);

      gain.gain.setValueAtTime(0.001, audioCtx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.04, audioCtx.currentTime + 0.4);
      gain.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + 2.8);

      osc.connect(gain);
      gain.connect(audioCtx.destination);
      osc.start();
      osc.stop(audioCtx.currentTime + 2.9);
    } catch (e) {}
  }

  window.toggleAmbientAudio = () => {
    state.audioPlaying = !state.audioPlaying;
    const iconSvg = document.getElementById('iconPlaySvg');
    const subText = document.getElementById('audioStatusSub');
    const track = AMBIENT_TRACKS[state.audioTrackIdx];

    if (state.audioPlaying) {
      iconSvg.innerHTML = `<rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/>`;
      subText.textContent = `♫ Memutar: ${track.sub}`;
      playChimeNote();
      ambientInterval = setInterval(playChimeNote, 2400);
      showToast(`Memutar suasana "${track.title}"`, '🎵');
    } else {
      iconSvg.innerHTML = `<polygon points="6 4 20 12 6 20 6 4"/>`;
      subText.textContent = track.sub;
      if (ambientInterval) clearInterval(ambientInterval);
    }
  };

  window.skipAmbientTrack = (dir) => {
    state.audioTrackIdx = (state.audioTrackIdx + dir + AMBIENT_TRACKS.length) % AMBIENT_TRACKS.length;
    const track = AMBIENT_TRACKS[state.audioTrackIdx];
    document.getElementById('audioStatusSub').textContent = state.audioPlaying ? `♫ Memutar: ${track.sub}` : track.sub;
    document.getElementById('audioProgressFill').style.width = `${track.progress}%`;
    if (state.audioPlaying) playChimeNote();
  };

  window.scrubAmbientTrack = (e) => {
    const rect = e.currentTarget.getBoundingClientRect();
    const pct = Math.max(8, Math.min(96, Math.round(((e.clientX - rect.left) / rect.width) * 100)));
    document.getElementById('audioProgressFill').style.width = `${pct}%`;
  };

  /* ==========================================================================
     13. EKSPOS FUNGSI GLOBAL KE WINDOW (PEMBELIAN, RATING/KOMENTAR, SUB-TABS)
     ========================================================================== */
  window.selectHeroProduct = (id) => {
    state.activeHeroId = id;
    renderHeroShowcase();
  };

  window.handleSidebarAuthClick = () => {
    if (!state.currentUser) {
      window.location.href = 'login.php';
    } else {
      window.openProfileModal();
    }
  };

  /**
   * Modal Profil Pengguna & Edit Toko Seller
   */
  window.openProfileModal = () => {
    const user = state.currentUser;
    if (!user) {
      window.location.href = 'login.php';
      return;
    }

    const modal = document.getElementById('profileModal');
    if (!modal) return;

    const avatarPreview = document.getElementById('profModalAvatarPreview');
    const avatarInput = document.getElementById('profAvatarUrl');
    const roleBadge = document.getElementById('profRoleBadge');
    const title = document.getElementById('profModalTitle');
    const subtitle = document.getElementById('profModalSubtitle');
    const tabGeneralLabel = document.getElementById('tabProfGeneralLabel');
    const sellerFields = document.getElementById('sellerStoreFields');

    const defaultAv = (user.jenis_kelamin === 'Perempuan')
      ? 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=200&q=80'
      : 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=200&q=80';

    if (avatarPreview) avatarPreview.src = user.avatar_url || defaultAv;
    if (avatarInput) avatarInput.value = user.avatar_url || '';

    if (roleBadge) {
      roleBadge.className = `role-badge role-${user.role}`;
      roleBadge.textContent = user.role.toUpperCase();
    }

    if (user.role === 'seller') {
      if (title) title.textContent = 'Profil & Pengaturan Toko';
      if (subtitle) subtitle.textContent = 'Kelola detail lapak toko, kebun, kontak WA, dan foto profil Anda.';
      if (tabGeneralLabel) tabGeneralLabel.textContent = '🏪 Toko & Profil';
      if (sellerFields) {
        sellerFields.style.display = 'block';
        const namaTokoEl = document.getElementById('profNamaToko');
        const deskripsiTokoEl = document.getElementById('profDeskripsiToko');
        if (namaTokoEl) namaTokoEl.value = user.nama_toko || user.nama || '';
        if (deskripsiTokoEl) deskripsiTokoEl.value = user.deskripsi_toko || '';
      }
    } else {
      if (title) title.textContent = 'Profil Pengguna';
      if (subtitle) subtitle.textContent = 'Kelola identitas, jenis kelamin, foto profil, dan keamanan akun Anda.';
      if (tabGeneralLabel) tabGeneralLabel.textContent = '👤 Data Profil';
      if (sellerFields) sellerFields.style.display = 'none';
    }

    const namaInput = document.getElementById('profNama');
    if (namaInput) namaInput.value = user.nama || '';

    const waInput = document.getElementById('profNoWa');
    if (waInput) waInput.value = user.no_wa || '';

    const alamatInput = document.getElementById('profAlamat');
    if (alamatInput) alamatInput.value = user.alamat || '';

    // Gender radio selection
    const gender = user.jenis_kelamin || 'Laki-laki';
    const radioL = document.querySelector('input[name="prof_gender"][value="Laki-laki"]');
    const radioP = document.querySelector('input[name="prof_gender"][value="Perempuan"]');
    const pillL = document.getElementById('profGenderL');
    const pillP = document.getElementById('profGenderP');

    if (gender === 'Perempuan') {
      if (radioP) radioP.checked = true;
      if (pillP) pillP.classList.add('selected');
      if (pillL) pillL.classList.remove('selected');
    } else {
      if (radioL) radioL.checked = true;
      if (pillL) pillL.classList.add('selected');
      if (pillP) pillP.classList.remove('selected');
    }

    // Reset password inputs
    const pwdCurrent = document.getElementById('pwdCurrent');
    const pwdNew = document.getElementById('pwdNew');
    const pwdConfirm = document.getElementById('pwdConfirm');
    if (pwdCurrent) pwdCurrent.value = '';
    if (pwdNew) pwdNew.value = '';
    if (pwdConfirm) pwdConfirm.value = '';

    window.switchProfileTab('general');
    modal.classList.add('active');
  };

  window.closeProfileModal = () => {
    const modal = document.getElementById('profileModal');
    if (modal) modal.classList.remove('active');
  };

  window.switchProfileTab = (tab) => {
    const formProf = document.getElementById('profileForm');
    const formPwd = document.getElementById('passwordResetForm');
    const btnGen = document.getElementById('tabProfBtnGeneral');
    const btnSec = document.getElementById('tabProfBtnSecurity');
    if (!formProf || !formPwd) return;

    if (tab === 'general') {
      formProf.style.display = 'block';
      formPwd.style.display = 'none';
      if (btnGen) btnGen.className = 'btn btn-primary';
      if (btnSec) btnSec.className = 'btn btn-secondary';
    } else {
      formProf.style.display = 'none';
      formPwd.style.display = 'block';
      if (btnGen) btnGen.className = 'btn btn-secondary';
      if (btnSec) btnSec.className = 'btn btn-primary';
    }
  };

  window.pickPresetAvatar = (type) => {
    let url = 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=200&q=80';
    if (type === 'female') {
      url = 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=200&q=80';
    } else if (type === 'orchard') {
      url = 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=200&q=80';
    }
    const avatarInput = document.getElementById('profAvatarUrl');
    const avatarPreview = document.getElementById('profModalAvatarPreview');
    if (avatarInput) avatarInput.value = url;
    if (avatarPreview) avatarPreview.src = url;
  };

  window.openAuthModal = () => {
    if (state.currentUser) {
      window.openProfileModal();
      return;
    }
    renderDemoAccountsModal();
    document.getElementById('authModal').classList.add('active');
  };

  window.closeAuthModal = () => {
    document.getElementById('authModal').classList.remove('active');
  };

  window.logoutUser = async () => {
    try {
      const res = await apiRequest('logout', 'POST');
      localStorage.removeItem(ACTIVE_USER_KEY);
      state.currentUser = null;
      state.users = [];
      applyRolePermissionsUI();
      renderDemoAccountsModal();
      switchMode('catalog');
      window.closeAuthModal();
      showToast(res.message, '👋');
    } catch (err) {
      showToast(err.message, '⚠️');
    }
  };

  window.switchAuthTab = (tab) => {
    const loginForm = document.getElementById('loginForm');
    const regForm = document.getElementById('registerForm');
    const btnLogin = document.getElementById('tabBtnLogin');
    const btnReg = document.getElementById('tabBtnRegister');

    if (tab === 'login') {
      loginForm.style.display = 'block';
      regForm.style.display = 'none';
      btnLogin.className = 'btn btn-primary';
      btnReg.className = 'btn btn-secondary';
    } else {
      loginForm.style.display = 'none';
      regForm.style.display = 'block';
      btnLogin.className = 'btn btn-secondary';
      btnReg.className = 'btn btn-primary';
    }
  };

  window.quickSwitchUser = async (userId) => {
    try {
      const res = await apiRequest('login', 'POST', { quick_user_id: userId });
      handleAuthSuccess(res);
    } catch (err) {
      showToast(err.message, '⚠️');
    }
  };

  // Sub-Tab Switcher untuk Admin (Chart Monitoring vs Kelola User)
  window.switchAdminSubTab = (sub) => {
    state.adminSubTab = sub;
    const monSec = document.getElementById('adminMonitoringSection');
    const crudSec = document.getElementById('adminCrudSection');
    const btnAll = document.getElementById('btnAdminTabAll');
    const btnChart = document.getElementById('btnAdminTabChart');
    const btnUsers = document.getElementById('btnAdminTabUsers');

    if (monSec) monSec.style.display = (sub === 'all' || sub === 'chart') ? 'block' : 'none';
    if (crudSec) crudSec.style.display = (sub === 'all' || sub === 'users') ? 'grid' : 'none';

    if (btnAll) btnAll.className = sub === 'all' ? 'btn btn-primary' : 'btn btn-secondary';
    if (btnChart) btnChart.className = sub === 'chart' ? 'btn btn-primary' : 'btn btn-secondary';
    if (btnUsers) btnUsers.className = sub === 'users' ? 'btn btn-primary' : 'btn btn-secondary';

    if (sub === 'all' || sub === 'chart') {
      renderAdminMonitoringCharts();
    }
    applyRolePermissionsUI();
  };

  window.openAdminChartView = () => {
    switchMode('users');
    window.switchAdminSubTab('chart');
  };

  window.openAdminUsersView = () => {
    switchMode('users');
    window.switchAdminSubTab('users');
  };

  // Sub-Tab Switcher untuk Seller (Chart & Komentar vs Kelola Produk)
  window.switchSellerSubTab = (sub) => {
    state.sellerSubTab = sub;
    const monSec = document.getElementById('sellerMonitoringSection');
    const crudSec = document.getElementById('sellerCrudSection');
    const btnAll = document.getElementById('btnSellerTabCrud');
    const btnChart = document.getElementById('btnSellerTabChart');
    const btnForm = document.getElementById('btnSellerTabForm');

    if (monSec) monSec.style.display = (sub === 'all' || sub === 'monitoring') ? 'block' : 'none';
    if (crudSec) crudSec.style.display = (sub === 'all' || sub === 'crud') ? 'grid' : 'none';

    if (btnAll) btnAll.className = sub === 'all' ? 'btn btn-primary' : 'btn btn-secondary';
    if (btnChart) btnChart.className = sub === 'monitoring' ? 'btn btn-primary' : 'btn btn-secondary';
    if (btnForm) btnForm.className = sub === 'crud' ? 'btn btn-primary' : 'btn btn-secondary';

    if (sub === 'all' || sub === 'monitoring') {
      renderDashboard();
    }
    applyRolePermissionsUI();
  };

  window.openSellerMonitoringView = () => {
    switchMode('dashboard');
    window.switchSellerSubTab('monitoring');
  };

  window.openSellerCrudView = () => {
    switchMode('dashboard');
    window.switchSellerSubTab('crud');
  };

  window.triggerNavAction = (action) => {
    if (action === 'catalog') {
      state.onlyUnggulan = false;
      switchMode('catalog');
      renderCatalog();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    } else if (action === 'explore') {
      state.onlyUnggulan = false;
      switchMode('catalog');
      clearSidebarActive();
      document.getElementById('btnNavExplore').classList.add('active');
      renderCatalog();
      document.getElementById('catalogSectionAnchor').scrollIntoView({ behavior: 'smooth' });
    } else if (action === 'unggulan') {
      state.onlyUnggulan = !state.onlyUnggulan;
      switchMode('catalog');
      clearSidebarActive();
      document.getElementById(state.onlyUnggulan ? 'btnNavSaved' : 'btnModeCatalog').classList.add('active');
      renderCatalog();
      document.getElementById('catalogSectionAnchor').scrollIntoView({ behavior: 'smooth' });
      showToast(state.onlyUnggulan ? 'Menampilkan khusus Buah Unggulan ★' : 'Menampilkan seluruh katalog buah', '❤️');
    } else if (action === 'dashboard') {
      switchMode('dashboard');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    } else if (action === 'users') {
      switchMode('users');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  };

  window.filterByQuickCategory = (categoryName) => {
    switchMode('catalog');
    state.onlyUnggulan = false;
    state.filterCategory = state.filterCategory === categoryName ? 'Semua' : categoryName;
    document.getElementById('filterCategory').value = state.filterCategory;
    renderCatalog();
    document.getElementById('catalogSectionAnchor').scrollIntoView({ behavior: 'smooth' });
  };

  window.resetCatalogFilters = () => {
    state.searchQuery = '';
    state.filterCategory = 'Semua';
    state.onlyUnggulan = false;
    document.getElementById('searchInput').value = '';
    document.getElementById('filterCategory').value = 'Semua';
    renderCatalog();
  };

  window.openDeliveryMapInfo = () => {
    showToast('Pengiriman Instan & Kurir Dingin tersedia untuk seluruh area layanan!', '🗺️');
  };

  window.editProduct = (id) => {
    const p = state.products.find(x => x.id === id);
    if (!p) return;
    window.switchSellerSubTab('all');
    document.getElementById('inputId').value = p.id;
    document.getElementById('inputNama').value = p.nama;
    document.getElementById('inputKategori').value = p.kategori;
    document.getElementById('inputHarga').value = p.harga;
    document.getElementById('inputStok').value = p.stok;
    document.getElementById('inputDeskripsi').value = p.deskripsi;
    document.getElementById('inputUnggulan').checked = p.unggulan;

    document.getElementById('formTitle').textContent = 'Edit Produk: ' + p.nama;
    document.getElementById('btnCancelEdit').style.display = 'inline-flex';
    document.getElementById('sellerCrudSection').scrollIntoView({ behavior: 'smooth' });
  };

  window.deleteProduct = (id) => {
    const target = state.products.find(p => p.id === id);
    showConfirmModal(
      'Hapus Produk Buah?',
      `Apakah Anda yakin ingin menghapus "${target ? target.nama : 'produk ini'}" dari database MySQL?`,
      async () => {
        try {
          const res = await apiRequest('product_delete', 'POST', { id });
          state.products = res.products;
          renderDashboard();
          renderCatalog();
          showToast(res.message, '🗑️');
        } catch (err) {
          showToast(err.message, '⚠️');
        }
      }
    );
  };

  window.editUser = (id) => {
    const u = state.users.find(x => Number(x.id) === Number(id));
    if (!u) return;
    window.switchAdminSubTab('all');
    document.getElementById('inputUserId').value = u.id;
    document.getElementById('inputUserNama').value = u.nama;
    document.getElementById('inputUserUsername').value = u.username;
    document.getElementById('inputUserRole').value = u.role;
    document.getElementById('inputUserEmail').value = u.email;
    document.getElementById('inputUserWa').value = u.no_wa || '';
    document.getElementById('inputUserStatus').value = u.status || 'aktif';
    document.getElementById('inputUserAlamat').value = u.alamat || '';
    document.getElementById('inputUserPassword').value = '';

    document.getElementById('userFormTitle').textContent = 'Edit User: ' + u.nama;
    document.getElementById('passHintText').textContent = '(Kosongkan jika tidak ingin mengubah password)';
    document.getElementById('btnCancelUserEdit').style.display = 'inline-flex';
    document.getElementById('adminCrudSection').scrollIntoView({ behavior: 'smooth' });
  };

  window.deleteUser = (id) => {
    const target = state.users.find(u => Number(u.id) === Number(id));
    showConfirmModal(
      'Hapus Akun User?',
      `Apakah Anda yakin ingin menghapus user "${target ? target.nama : ''}" (@${target ? target.username : ''}) dari database MySQL?`,
      async () => {
        try {
          const res = await apiRequest('user_delete', 'POST', { id });
          state.users = res.users;
          if (res.demoAccounts) state.demoAccounts = res.demoAccounts;
          if (res.products) state.products = res.products;
          renderAdminUsers();
          renderDemoAccountsModal();
          renderCatalog();
          applyRolePermissionsUI();
          showToast(res.message, '🗑️');
        } catch (err) {
          showToast(err.message, '⚠️');
        }
      }
    );
  };

  /* ==========================================================================
     14. MODAL DETAIL PRODUK, PEMESANAN WA & RATING/KOMENTAR POSTINGAN SELLER
     ========================================================================== */
  window.openDetailModal = (id) => {
    const p = state.products.find(x => x.id === id);
    if (!p) return;

    state.selectedQty = 1;
    state.selectedRating = 5;
    const isOutOfStock = p.stok === 0;
    const maxQty = Math.max(1, p.stok);
    const reviews = p.reviews || [];
    const isLoggedIn = Boolean(state.currentUser);

    const reviewsHtml = reviews.length > 0
      ? reviews.map(r => `
          <div class="review-item-card">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
              <div style="display: flex; align-items: center; gap: 10px;">
                <img src="${escapeHTML(r.avatar_user)}" alt="" style="width: 34px; height: 34px; border-radius: 50%; object-fit: cover;" onerror="this.src='https://placehold.co/50x50/183E62/FFF?text=U'" />
                <div>
                  <div style="font-weight: 700; font-size: 0.86rem; color: var(--text-primary);">${escapeHTML(r.nama_user)}</div>
                  <div style="font-size: 0.7rem; color: var(--text-muted);">${escapeHTML(r.created_at || '')}</div>
                </div>
              </div>
              <span style="color: #F59E0B; font-weight: 800; font-size: 0.9rem;">${renderStars(r.rating)}</span>
            </div>
            <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.45;">
              ${escapeHTML(r.komentar)}
            </p>
          </div>
        `).join('')
      : `<div class="neu-inset-box" style="padding: 14px; text-align: center; font-size: 0.84rem; color: var(--text-muted);">Belum ada komentar untuk produk ini. Jadilah yang pertama memberi ulasan!</div>`;

    const modalBody = document.getElementById('modalBody');
    modalBody.innerHTML = `
      <div class="modal-body" style="padding: 0;">
        <div>
          <img src="${escapeHTML(p.imageUrl)}" alt="${escapeHTML(p.nama)}" class="modal-img painterly-img" onerror="this.src='https://placehold.co/500x400/183E62/FFFFFF?text=ShawWite'">
        </div>
        <div style="display:flex; flex-direction:column; gap: 10px;">
          <div>
            <span style="display:inline-block; padding:5px 12px; background:var(--surface-inset); border-radius:999px; font-size:0.78rem; font-weight:700; color:var(--text-secondary);">
              ${escapeHTML(p.kategori)}
            </span>
            ${p.unggulan ? `<span style="display:inline-block; padding:5px 12px; background:#FEF3C7; color:#92400E; border-radius:999px; font-size:0.78rem; font-weight:800; margin-left:6px;">★ UNGGULAN</span>` : ''}
            <span style="display:inline-block; padding:5px 12px; background:var(--surface-raised); border-radius:999px; font-size:0.78rem; font-weight:800; color:#D97706; margin-left:6px;">
              ★ ${Number(p.rating || 4.9).toFixed(1)} (${reviews.length} Ulasan)
            </span>
          </div>
          <h2 style="font-family: var(--font-serif); font-size: 1.85rem; font-weight: 800; line-height: 1.15; color: var(--text-primary);">
            ${escapeHTML(p.nama)}
          </h2>
          <div style="font-size: 1.5rem; color: var(--navy-primary); font-weight: 800;">
            ${formatRupiah(p.harga)} <span style="font-size: 0.84rem; font-weight: 500; color: var(--text-muted);">/ pack</span>
          </div>
          <div style="font-size: 0.84rem; color: var(--azure-accent); font-weight: 700;">
            🏪 Postingan Seller: ${escapeHTML(p.seller_nama || 'Kebun ShawWite Utama')}
          </div>
          <p style="color: var(--text-secondary); line-height: 1.55; font-size: 0.9rem;">
            ${escapeHTML(p.deskripsi)}
          </p>

          <!-- Panel Pemilihan Jumlah & Catatan Pembeli -->
          <div class="neu-inset-box" style="padding: 14px; margin-top: 4px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
              <span style="font-size: 0.84rem; font-weight: 700; color: var(--text-primary);">Pilih Jumlah (Max: ${p.stok}):</span>
              <div style="display: flex; align-items: center; gap: 10px;">
                <button type="button" class="neu-pill-btn" style="width: 32px; height: 32px;" onclick="window.adjustOrderQty(-1, ${p.harga}, ${maxQty})" ${isOutOfStock ? 'disabled' : ''}>−</button>
                <span id="modalOrderQty" style="font-weight: 800; font-size: 1.05rem; min-width: 24px; text-align: center;">1</span>
                <button type="button" class="neu-pill-btn" style="width: 32px; height: 32px;" onclick="window.adjustOrderQty(1, ${p.harga}, ${maxQty})" ${isOutOfStock ? 'disabled' : ''}>+</button>
              </div>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.88rem; margin-bottom: 10px;">
              <span style="color: var(--text-secondary);">Total Estimasi:</span>
              <strong id="modalOrderTotal" style="color: var(--navy-primary); font-size: 1.08rem;">${formatRupiah(p.harga)}</strong>
            </div>
            <input type="text" id="modalOrderNote" class="form-input" style="padding: 8px 12px; font-size: 0.84rem; background: var(--surface-raised);" placeholder="Catatan untuk Seller (mis. pilih yang matang pohon)..." ${isOutOfStock ? 'disabled' : ''} />
          </div>

          <div style="margin-top: auto; padding-top: 10px;">
            <button type="button" onclick="window.contactSellerToBuy('${p.id}')" 
               class="btn btn-primary" style="width: 100%; padding: 13px; font-size: 0.95rem; ${isOutOfStock ? 'pointer-events:none; opacity: 0.5;' : ''}">
               ${isOutOfStock
                 ? 'Maaf, Stok Sedang Habis'
                 : (isLoggedIn ? 'Hubungi Seller & Beli via WhatsApp →' : '🔒 Login untuk Membeli Produk Ini →')}
            </button>
          </div>
        </div>
      </div>

      <!-- ====================================================================
           SECTION RATING & KOMENTAR PADA POSTINGAN PRODUK SELLER
           ==================================================================== -->
      <div style="margin-top: 22px; padding-top: 20px; border-top: 1.5px solid var(--border-divider);">
        <h3 style="font-family: var(--font-serif); font-size: 1.3rem; font-weight: 700; color: var(--text-primary); margin-bottom: 12px;">
          ⭐ Rating &amp; Komentar Pembeli (${reviews.length})
        </h3>

        ${isLoggedIn ? `
          <form onsubmit="window.submitProductReview(event, '${p.id}')" class="neu-inset-box" style="padding: 16px; margin-bottom: 16px;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-bottom: 10px;">
              <span style="font-size: 0.86rem; font-weight: 700; color: var(--text-primary);">
                Beri Rating sebagai ${escapeHTML(state.currentUser.nama)}:
              </span>
              <div class="star-rating-picker" id="starPickerContainer">
                ${[1, 2, 3, 4, 5].map(num => `
                  <button type="button" class="star-pick-btn active" data-star="${num}" onclick="window.selectStarRating(${num})">★</button>
                `).join('')}
              </div>
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
              <input type="text" id="inputReviewComment" class="form-input" style="flex: 1; min-width: 200px; background: var(--surface-raised); padding: 10px 14px;" required placeholder="Tulis komentar atau pengalaman Anda membeli produk ini..." />
              <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">
                Kirim Komentar
              </button>
            </div>
          </form>
        ` : `
          <div class="neu-inset-box" style="padding: 14px 18px; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
            <span style="font-size: 0.86rem; color: var(--text-secondary);">
              Ingin memberikan rating atau komentar pada postingan produk ini?
            </span>
            <a href="login.php?reason=review&product=${encodeURIComponent(p.id)}" class="btn btn-secondary" style="padding: 7px 16px; font-size: 0.8rem;">
              🔑 Login untuk Berkomentar
            </a>
          </div>
        `}

        <div style="display: flex; flex-direction: column; gap: 10px; max-height: 240px; overflow-y: auto; padding-right: 4px;">
          ${reviewsHtml}
        </div>
      </div>
    `;
    document.getElementById('detailModal').classList.add('active');
  };

  window.selectStarRating = (rating) => {
    state.selectedRating = rating;
    document.querySelectorAll('#starPickerContainer .star-pick-btn').forEach(btn => {
      const starVal = parseInt(btn.getAttribute('data-star'), 10);
      btn.classList.toggle('active', starVal <= rating);
    });
  };

  window.submitProductReview = async (e, productId) => {
    e.preventDefault();
    if (!state.currentUser) {
      window.location.href = `login.php?reason=review&product=${encodeURIComponent(productId)}`;
      return;
    }

    const commentInput = document.getElementById('inputReviewComment');
    const komentar = commentInput ? commentInput.value.trim() : '';
    if (!komentar) return;

    try {
      const res = await apiRequest('review_save', 'POST', {
        product_id: productId,
        rating: state.selectedRating,
        komentar
      });
      state.products = res.products;
      if (res.allReviews) state.allReviews = res.allReviews;
      if (res.analytics) state.analytics = res.analytics;

      renderCatalog();
      renderDashboard();
      renderAdminUsers();
      window.openDetailModal(productId);
      showToast(res.message, '⭐');
    } catch (err) {
      if (err.requireLogin) {
        window.location.href = `login.php?reason=review&product=${encodeURIComponent(productId)}`;
        return;
      }
      showToast(err.message, '⚠️');
    }
  };

  window.adjustOrderQty = (delta, unitPrice, maxStok) => {
    state.selectedQty = Math.max(1, Math.min(maxStok, state.selectedQty + delta));
    const qtyEl = document.getElementById('modalOrderQty');
    const totalEl = document.getElementById('modalOrderTotal');
    if (qtyEl) qtyEl.textContent = String(state.selectedQty);
    if (totalEl) totalEl.textContent = formatRupiah(state.selectedQty * unitPrice);
  };

  window.contactSellerToBuy = async (productId) => {
    // Jika belum login, arahkan ke halaman login.php sesuai instruksi user
    if (!state.currentUser) {
      showToast('Mengalihkan ke halaman Login untuk melanjutkan pembelian...', '🔒');
      setTimeout(() => {
        window.location.href = `login.php?reason=buy&product=${encodeURIComponent(productId)}`;
      }, 350);
      return;
    }

    const noteEl = document.getElementById('modalOrderNote');
    const catatan = noteEl ? noteEl.value.trim() : '';
    try {
      const res = await apiRequest('create_inquiry', 'POST', {
        product_id: productId,
        qty: state.selectedQty,
        catatan
      });
      if (res.products) {
        state.products = res.products;
        renderCatalog();
        renderDashboard();
      }
      showToast(res.message, '📲');
      if (res.waUrl) {
        window.open(res.waUrl, '_blank', 'noopener');
      }
      window.closeModal();
    } catch (err) {
      if (err.requireLogin) {
        window.location.href = `login.php?reason=buy&product=${encodeURIComponent(productId)}`;
        return;
      }
      showToast(err.message, '⚠️');
    }
  };

  window.closeModal = () => {
    document.getElementById('detailModal').classList.remove('active');
  };

  document.getElementById('detailModal').addEventListener('click', (e) => {
    if (e.target.id === 'detailModal') window.closeModal();
  });

  const authModalEl = document.getElementById('authModal');
  if (authModalEl) {
    authModalEl.addEventListener('click', (e) => {
      if (e.target.id === 'authModal') window.closeAuthModal();
    });
  }

  const profileModalEl = document.getElementById('profileModal');
  if (profileModalEl) {
    profileModalEl.addEventListener('click', (e) => {
      if (e.target.id === 'profileModal') window.closeProfileModal();
    });
  }

  // Profile Form Submission Handler
  const profileForm = document.getElementById('profileForm');
  if (profileForm) {
    profileForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const nama = document.getElementById('profNama').value.trim();
      const jenisKelamin = document.querySelector('input[name="prof_gender"]:checked')?.value || 'Laki-laki';
      const noWa = document.getElementById('profNoWa').value.trim();
      const alamat = document.getElementById('profAlamat').value.trim();
      const avatarUrl = document.getElementById('profAvatarUrl').value.trim();
      const namaToko = document.getElementById('profNamaToko')?.value.trim() || '';
      const deskripsiToko = document.getElementById('profDeskripsiToko')?.value.trim() || '';

      const btn = document.getElementById('btnSaveProfile');
      if (btn) {
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';
      }

      try {
        const res = await apiRequest('profile_update', 'POST', {
          nama,
          jenis_kelamin: jenisKelamin,
          no_wa: noWa,
          alamat,
          avatar_url: avatarUrl,
          nama_toko: namaToko,
          deskripsi_toko: deskripsiToko
        });

        if (!res.ok) {
          showToast(res.message || 'Gagal memperbarui profil', '⚠️');
          return;
        }

        state.currentUser = res.currentUser;
        if (res.products) state.products = res.products;
        applyRolePermissionsUI();
        renderCatalog();
        renderDashboard();
        window.closeProfileModal();
        showToast(res.message || 'Profil berhasil diperbarui!', '✨');
      } catch (err) {
        showToast(err.message || 'Terjadi kesalahan sistem', '⚠️');
      } finally {
        if (btn) {
          btn.disabled = false;
          btn.textContent = '💾 Simpan Perubahan';
        }
      }
    });
  }

  // Gender selection toggle in profile modal
  document.querySelectorAll('input[name="prof_gender"]').forEach(radio => {
    radio.addEventListener('change', () => {
      const selected = document.querySelector('input[name="prof_gender"]:checked')?.value;
      const pillL = document.getElementById('profGenderL');
      const pillP = document.getElementById('profGenderP');
      if (pillL && pillP) {
        if (selected === 'Perempuan') {
          pillP.classList.add('selected');
          pillL.classList.remove('selected');
        } else {
          pillL.classList.add('selected');
          pillP.classList.remove('selected');
        }
      }
    });
  });

  // Avatar URL live preview in profile modal
  const profAvatarUrl = document.getElementById('profAvatarUrl');
  if (profAvatarUrl) {
    profAvatarUrl.addEventListener('input', () => {
      const preview = document.getElementById('profModalAvatarPreview');
      if (preview && profAvatarUrl.value.trim()) {
        preview.src = profAvatarUrl.value.trim();
      }
    });
  }

  // Password Reset Form Submission Handler
  const passwordResetForm = document.getElementById('passwordResetForm');
  if (passwordResetForm) {
    passwordResetForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const currentPassword = document.getElementById('pwdCurrent').value;
      const newPassword = document.getElementById('pwdNew').value;
      const confirmPassword = document.getElementById('pwdConfirm').value;

      if (newPassword.length < 4) {
        showToast('Password baru minimal 4 karakter!', '⚠️');
        return;
      }
      if (newPassword !== confirmPassword) {
        showToast('Konfirmasi password tidak cocok!', '⚠️');
        return;
      }

      const btn = document.getElementById('btnSavePassword');
      if (btn) {
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';
      }

      try {
        const res = await apiRequest('password_reset', 'POST', {
          current_password: currentPassword,
          new_password: newPassword,
          confirm_password: confirmPassword
        });

        if (!res.ok) {
          showToast(res.message || 'Gagal mereset password', '⚠️');
          return;
        }

        document.getElementById('pwdCurrent').value = '';
        document.getElementById('pwdNew').value = '';
        document.getElementById('pwdConfirm').value = '';
        window.closeProfileModal();
        showToast(res.message || 'Password berhasil diperbarui!', '🔒');
      } catch (err) {
        showToast(err.message || 'Gagal mereset password', '⚠️');
      } finally {
        if (btn) {
          btn.disabled = false;
          btn.textContent = '🔒 Perbarui Password';
        }
      }
    });
  }

  /* ==========================================================================
     15. INISIALISASI APLIKASI DARI DATABASE MYSQL (`shawwite`)
     ========================================================================== */
  async function init() {
    loadLocalTheme();
    bindEvents();

    try {
      const data = await apiRequest('bootstrap', 'GET');
      state.currentUser = data.currentUser;
      state.demoAccounts = data.demoAccounts || [];
      state.products = data.products || [];
      state.users = data.users || [];
      state.inquiries = data.inquiries || [];
      state.allReviews = data.allReviews || [];
      state.analytics = data.analytics || null;

      if (data.settings && data.settings.waNumber) {
        state.settings.waNumber = data.settings.waNumber;
      }

      if (state.currentUser) {
        localStorage.setItem(ACTIVE_USER_KEY, String(state.currentUser.id));
        document.getElementById('inputWaNumber').value = state.currentUser.no_wa || state.settings.waNumber;
      } else {
        localStorage.removeItem(ACTIVE_USER_KEY);
      }

      applyRolePermissionsUI();
      renderCatalog();
      renderDashboard();
      renderAdminUsers();
      renderDemoAccountsModal();

      const urlParams = new URLSearchParams(window.location.search);
      const requestedView = urlParams.get('view');
      const productParam = urlParams.get('product');

      if (requestedView === 'users' && state.currentUser && state.currentUser.role === 'admin') {
        switchMode('users');
      } else if (requestedView === 'dashboard' && state.currentUser && state.currentUser.role === 'seller') {
        switchMode('dashboard');
      }

      if (productParam) {
        window.openDetailModal(productParam);
      }
    } catch (error) {
      console.error('Gagal memuat data dari MySQL shawwite:', error);
      showToast('Pastikan Apache & MySQL di XAMPP aktif: ' + error.message, '⚠️');
    } finally {
      setTimeout(() => {
        document.getElementById('loadingScreen').classList.add('hidden');
      }, 650);
    }
  }

  window.addEventListener('load', init);
})();
