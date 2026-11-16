<?php

require_once base_path('app/Http/Controllers/ShawWiteController.php');

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShawWiteController;

/*
|--------------------------------------------------------------------------
| ShawWite Fruits — Laravel 11 Web & API Routes
|--------------------------------------------------------------------------
*/

// Halaman Utama Katalog / Dashboard User
Route::get('/', [ShawWiteController::class, 'index'])->name('home');
Route::get('/index.php', [ShawWiteController::class, 'index']);

// Halaman Login (Sesuai login.md & Tema Buah Segar)
Route::get('/login', [ShawWiteController::class, 'login'])->name('login');
Route::get('/login.php', [ShawWiteController::class, 'login']);

// Halaman Register (Sesuai register.md & Tema Buah Segar)
Route::get('/register', [ShawWiteController::class, 'register'])->name('register');
Route::get('/register.php', [ShawWiteController::class, 'register']);

// Endpoint REST API (MySQL Database `shawwite`)
Route::any('/api.php', [ShawWiteController::class, 'api']);
Route::any('/api', [ShawWiteController::class, 'api']);
