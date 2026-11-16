<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * ============================================================================
 * SHAWWITE FRUITS — LARAVEL 11 MAIN CONTROLLER
 * File: app/Http/Controllers/ShawWiteController.php
 * ============================================================================
 * Mengelola seluruh tampilan Blade (Katalog, Login, Register, Dashboard Admin & Seller)
 * serta endpoint REST API yang terhubung ke database MySQL `shawwite`.
 */
class ShawWiteController extends Controller
{
    /**
     * Halaman Utama Dashboard User / Katalog Buah (resources/views/shawwite/index.blade.php)
     */
    public function index()
    {
        return view('shawwite.index');
    }

    /**
     * Halaman Login (resources/views/shawwite/login.blade.php)
     */
    public function login()
    {
        return view('shawwite.login');
    }

    /**
     * Halaman Register (resources/views/shawwite/register.blade.php)
     */
    public function register()
    {
        return view('shawwite.register');
    }

    /**
     * Endpoint REST API untuk Katalog, Login, Register, Ulasan/Rating, CRUD Produk (Seller), & CRUD User (Admin)
     */
    public function api(Request $request)
    {
        require base_path('api.php');
    }
}
