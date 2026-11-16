<?php
/**
 * ============================================================================
 * SHAWWITE FRUITS — HALAMAN REGISTER TEMA BUAH SEGAR
 * File: register.php
 * Komposisi tata letak mengikuti sampel register.png dengan tema Buah Segar ShawWite
 * ============================================================================
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create Account — Register | ShawWite Fruits</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/auth.css" />
</head>
<body class="auth-page-body">

  <!-- ========================================================================
       LOADING SCREEN DENGAN LOGO.PNG
       ======================================================================== -->
  <div id="loadingScreen" class="loading-screen">
    <div class="loading-card">
      <img src="logo.png" alt="ShawWite Fruits Logo" class="loading-logo-img" onerror="this.style.display='none'" />
      <div class="loading-bar-track">
        <div class="loading-bar-fill"></div>
      </div>
      <p style="font-size: 0.85rem; color: #4A637E; font-weight: 600;">
        Menyiapkan halaman pendaftaran...
      </p>
    </div>
  </div>

  <div id="toastContainer" class="toast-container"></div>

  <!-- ========================================================================
       AUTH SHELL (TATA LETAK SAMPEL REGISTER.PNG DENGAN TEMA BUAH SEGAR)
       ======================================================================== -->
  <main class="auth-shell" aria-label="Halaman Register ShawWite Fruits">

    <!-- Latar Ilustrasi Panen Buah Segar Pilihan -->
    <img
      src="https://images.unsplash.com/photo-1610832958506-aa56368176cf?auto=format&fit=crop&w=1400&q=85"
      alt="Koleksi Buah Segar ShawWite Fruits"
      class="auth-santorini-bg"
      onerror="this.style.display='none'"
    />
    <div class="auth-sky-gradient"></div>
    <div class="auth-diagonal-veil"></div>

    <!-- Dekorasi Daun & Jejak Kesegaran Kebun di Kanan Atas -->
    <svg class="auth-flight-decor" viewBox="0 0 250 110" fill="none" aria-hidden="true">
      <path d="M12,98 C55,42 125,28 195,28" stroke="#689BC9" stroke-width="1.8" stroke-dasharray="5 5" stroke-linecap="round" />
      <g transform="translate(194, 8)">
        <path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1-2.3A4.49 4.49 0 0 0 8 20C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8Z" fill="#15803D"/>
      </g>
    </svg>

    <!-- Gelombang Lengkung di Bagian Bawah Kiri -->
    <svg class="auth-bottom-wave" viewBox="0 0 650 90" preserveAspectRatio="none" aria-hidden="true">
      <path d="M0,45 C140,-10 310,15 470,65 C550,88 610,90 650,90 L0,90 Z" fill="rgba(255,255,255,0.65)" />
      <path d="M0,62 C150,15 320,38 490,78 C560,92 615,90 650,90 L0,90 Z" fill="#EBF2FA" />
    </svg>

    <!-- 1. AREA BRANDING & VISUAL BUAH SEGAR (SISI KIRI) -->
    <section class="brand-visual">
      <div class="brand-top-row">
        <a href="index.php" class="wander-logo-group" title="Kembali ke Katalog ShawWite Fruits" style="align-items: flex-start;">
          <div style="background: rgba(255,255,255,0.92); padding: 8px 16px; border-radius: 22px; box-shadow: 0 8px 22px rgba(10,40,78,0.22); border: 2px solid #FFFFFF;">
            <img src="logo.png" alt="ShawWite Fruits Logo" style="height: 62px; width: auto; object-fit: contain; display: block;" />
          </div>
        </a>
      </div>

      <h1 class="brand-headline">
        Fresh Fruits,<br />Healthy Life Story
      </h1>
      <p class="brand-description">
        Nikmati kesegaran buah pilihan terbaik yang dipetik langsung dari kebun untuk kesehatan keluarga Anda setiap hari.
      </p>
      <div class="brand-divider-line" aria-hidden="true"></div>
    </section>

    <!-- 2. PANEL REGISTER FROSTED GLASS (SISI KANAN) -->
    <section class="auth-panel-wrap">
      <div class="auth-panel">
        <h2 class="auth-heading">Create Account</h2>
        <p class="auth-subheading">Daftar akun untuk mulai berbelanja di ShawWite Fruits</p>

        <form id="registerPageForm" novalidate>
          <!-- Field 1: Full Name -->
          <div class="field-wrapper">
            <div class="auth-input-capsule" id="capsuleFullName">
              <span class="field-icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                  <circle cx="12" cy="7" r="4"/>
                </svg>
              </span>
              <input
                type="text"
                id="regFullName"
                name="full_name"
                placeholder="Full Name"
                aria-label="Full Name"
                autocomplete="name"
                required
              />
            </div>
            <div id="errorFullName" class="field-error-msg" role="alert"></div>
          </div>

          <!-- Field 2: Email Address -->
          <div class="field-wrapper">
            <div class="auth-input-capsule" id="capsuleRegEmail">
              <span class="field-icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect width="20" height="16" x="2" y="4" rx="3"/>
                  <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                </svg>
              </span>
              <input
                type="email"
                id="regEmail"
                name="email"
                placeholder="Email Address"
                aria-label="Email Address"
                autocomplete="email"
                required
              />
            </div>
            <div id="errorRegEmail" class="field-error-msg" role="alert"></div>
          </div>

          <!-- Field 3: Password -->
          <div class="field-wrapper">
            <div class="auth-input-capsule" id="capsuleRegPass">
              <span class="field-icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
              </span>
              <input
                type="password"
                id="regPassword"
                name="password"
                placeholder="Password"
                aria-label="Password"
                autocomplete="new-password"
                required
              />
              <button
                type="button"
                id="btnToggleRegPass"
                class="btn-eye-toggle"
                aria-label="Tampilkan atau sembunyikan password"
                title="Tampilkan / Sembunyikan Password"
              >
                <svg id="eyeIconRegPass" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                  <line x1="1" y1="1" x2="23" y2="23"/>
                </svg>
              </button>
            </div>
            <div id="errorRegPass" class="field-error-msg" role="alert"></div>
          </div>

          <!-- Field 4: Confirm Password -->
          <div class="field-wrapper" style="margin-bottom: 18px;">
            <div class="auth-input-capsule" id="capsuleRegConfirm">
              <span class="field-icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
              </span>
              <input
                type="password"
                id="regConfirmPassword"
                name="confirm_password"
                placeholder="Confirm Password"
                aria-label="Confirm Password"
                autocomplete="new-password"
                required
              />
              <button
                type="button"
                id="btnToggleRegConfirm"
                class="btn-eye-toggle"
                aria-label="Tampilkan atau sembunyikan konfirmasi password"
                title="Tampilkan / Sembunyikan Konfirmasi Password"
              >
                <svg id="eyeIconRegConfirm" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                  <line x1="1" y1="1" x2="23" y2="23"/>
                </svg>
              </button>
            </div>
            <div id="errorRegConfirm" class="field-error-msg" role="alert"></div>
          </div>

          <!-- Tombol Utama Register -->
          <button type="submit" id="btnSubmitRegister" class="auth-submit-btn">
            <span id="btnRegText">Register</span>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </button>
        </form>

        <!-- Pemisah Social Registration -->
        <div class="social-divider">
          <span>or register with</span>
        </div>

        <!-- Tombol Google, Apple, GitHub -->
        <div class="social-buttons-row">
          <button type="button" class="social-squircle-btn" onclick="window.location.href='login.php'" aria-label="Sign up with Google" title="Sign up with Google">
            <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">
              <path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.7l3.1-3.1C17.3 1.8 14.8 1 12 1 7.4 1 3.5 3.6 1.6 7.4l3.7 2.8C6.2 7.2 8.9 5 12 5z"/>
              <path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.5h6.5c-.3 1.5-1.1 2.8-2.4 3.6l3.7 2.9c2.2-2 3.7-5 3.7-8.7z"/>
              <path fill="#FBBC05" d="M5.3 14.8c-.2-.8-.4-1.6-.4-2.5s.2-1.7.4-2.5L1.6 7C.6 9 0 11.2 0 13.5s.6 4.5 1.6 6.5l3.7-2.9z"/>
              <path fill="#34A853" d="M12 24c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3.1 0-5.8-2.1-6.7-5l-3.7 2.9C3.5 21.4 7.4 24 12 24z"/>
            </svg>
          </button>

          <button type="button" class="social-squircle-btn" onclick="window.location.href='login.php'" aria-label="Sign up with Apple" title="Sign up with Apple">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="#111827" aria-hidden="true">
              <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
            </svg>
          </button>

          <button type="button" class="social-squircle-btn" onclick="window.location.href='login.php'" aria-label="Sign up with GitHub" title="Sign up with GitHub">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="#111827" aria-hidden="true">
              <path d="M12 1.27a11 11 0 00-3.48 21.46c.55.09.73-.24.73-.53v-1.85c-3.03.66-3.67-1.45-3.67-1.45-.5-1.26-1.21-1.6-1.21-1.6-.99-.68.07-.66.07-.66 1.09.08 1.67 1.12 1.67 1.12.97 1.66 2.54 1.18 3.16.9.1-.7.38-1.18.69-1.45-2.42-.27-4.96-1.21-4.96-5.4 0-1.19.42-2.17 1.12-2.93-.11-.28-.49-1.39.11-2.89 0 0 .92-.29 3 1.12a10.4 10.4 0 015.46 0c2.08-1.41 3-1.12 3-1.12.6 1.5.22 2.61.11 2.89.7.76 1.12 1.74 1.12 2.93 0 4.2-2.54 5.12-4.97 5.39.39.34.74 1.01.74 2.04v3.02c0 .29.19.63.74.53A11 11 0 0012 1.27z"/>
            </svg>
          </button>
        </div>

        <!-- Tautan Login -->
        <div class="auth-footer-prompt">
          Already have an account? <a href="login.php">Login</a>
        </div>
      </div>
    </section>

  </main>

  <script>
    const ACTIVE_USER_KEY = 'shawwite_active_user_id_v3';

    window.addEventListener('load', () => {
      setTimeout(() => {
        document.getElementById('loadingScreen').classList.add('hidden');
      }, 650);
    });

    function showToast(msg, icon = '✨') {
      const container = document.getElementById('toastContainer');
      const toast = document.createElement('div');
      toast.className = 'neu-toast';
      toast.innerHTML = `<span>${icon}</span><span>${msg}</span>`;
      container.appendChild(toast);
      setTimeout(() => toast.remove(), 3400);
    }

    function setFieldError(capsuleId, errorId, message) {
      const capsule = document.getElementById(capsuleId);
      const errEl = document.getElementById(errorId);
      if (message) {
        capsule.classList.add('invalid');
        errEl.textContent = message;
        errEl.classList.add('visible');
      } else {
        capsule.classList.remove('invalid');
        errEl.textContent = '';
        errEl.classList.remove('visible');
      }
    }

    function bindPasswordToggle(btnId, inputId, iconId) {
      document.getElementById(btnId).addEventListener('click', () => {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        icon.innerHTML = isHidden
          ? `<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>`
          : `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>`;
      });
    }

    bindPasswordToggle('btnToggleRegPass', 'regPassword', 'eyeIconRegPass');
    bindPasswordToggle('btnToggleRegConfirm', 'regConfirmPassword', 'eyeIconRegConfirm');

    document.getElementById('registerPageForm').addEventListener('submit', async (e) => {
      e.preventDefault();

      const fullName = document.getElementById('regFullName').value.trim();
      const email = document.getElementById('regEmail').value.trim();
      const password = document.getElementById('regPassword').value;
      const confirmPassword = document.getElementById('regConfirmPassword').value;

      setFieldError('capsuleFullName', 'errorFullName', '');
      setFieldError('capsuleRegEmail', 'errorRegEmail', '');
      setFieldError('capsuleRegPass', 'errorRegPass', '');
      setFieldError('capsuleRegConfirm', 'errorRegConfirm', '');

      let hasError = false;

      if (!fullName) {
        setFieldError('capsuleFullName', 'errorFullName', 'Full name is required.');
        hasError = true;
      }

      if (!email) {
        setFieldError('capsuleRegEmail', 'errorRegEmail', 'Email address is required.');
        hasError = true;
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        setFieldError('capsuleRegEmail', 'errorRegEmail', 'Enter a valid email address.');
        hasError = true;
      }

      if (!password) {
        setFieldError('capsuleRegPass', 'errorRegPass', 'Password is required.');
        hasError = true;
      } else if (password.length < 4) {
        setFieldError('capsuleRegPass', 'errorRegPass', 'Password does not meet the requirements.');
        hasError = true;
      }

      if (!confirmPassword) {
        setFieldError('capsuleRegConfirm', 'errorRegConfirm', 'Please confirm your password.');
        hasError = true;
      } else if (password && password !== confirmPassword) {
        setFieldError('capsuleRegConfirm', 'errorRegConfirm', 'Passwords do not match.');
        hasError = true;
      }

      if (hasError) return;

      const btn = document.getElementById('btnSubmitRegister');
      const btnText = document.getElementById('btnRegText');
      btn.disabled = true;
      btnText.textContent = 'Creating account...';

      try {
        const res = await fetch('api.php?action=register', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          credentials: 'include',
          body: JSON.stringify({
            full_name: fullName,
            email: email,
            password: password,
            confirm_password: confirmPassword
          })
        });
        const data = await res.json();

        if (!res.ok || !data.ok) {
          const field = data.field || 'email';
          if (field === 'full_name') setFieldError('capsuleFullName', 'errorFullName', data.message);
          else if (field === 'password') setFieldError('capsuleRegPass', 'errorRegPass', data.message);
          else if (field === 'confirm_password') setFieldError('capsuleRegConfirm', 'errorRegConfirm', data.message);
          else setFieldError('capsuleRegEmail', 'errorRegEmail', data.message || 'Unable to create your account right now.');

          btn.disabled = false;
          btnText.textContent = 'Register';
          return;
        }

        localStorage.setItem(ACTIVE_USER_KEY, String(data.currentUser.id));
        showToast(data.message || 'Account created successfully!', '🎉');

        setTimeout(() => {
          window.location.href = 'index.php';
        }, 450);
      } catch (err) {
        setFieldError('capsuleRegEmail', 'errorRegEmail', 'Unable to create your account right now. Please try again.');
        btn.disabled = false;
        btnText.textContent = 'Register';
      }
    });
  </script>
</body>
</html>
