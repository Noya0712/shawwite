{{-- ============================================================================
     SHAWWITE FRUITS — SANTORINI NEUMORPHIC EDITION (LARAVEL 11 BLADE VIEW)
     File: resources/views/shawwite/index.blade.php
     ============================================================================ --}}
@include('shawwite.layouts.header')
@include('shawwite.layouts.modal_login')

  <!-- MAIN SANTORINI NEUMORPHIC APP SHELL -->
  <div class="neu-shell">

    @include('shawwite.layouts.sidebar')

    <!-- 2. MAIN WORKSPACE CONTENT -->
    <div style="min-width: 0;">

      @include('shawwite.layouts.topbar')
      @include('shawwite.partials.catalog')
      @include('shawwite.partials.seller_products')
      @include('shawwite.partials.admin_users')

    </div>
  </div>

@include('shawwite.layouts.footer')
