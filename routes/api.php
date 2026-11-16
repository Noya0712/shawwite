<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShawWiteController;

Route::any('/', [ShawWiteController::class, 'api']);
