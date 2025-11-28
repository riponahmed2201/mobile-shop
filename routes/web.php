<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard.index');
})->name('dashboard');

// Inventory Routes (accessible without auth for now)
Route::resource('brands', App\Http\Controllers\Inventory\BrandController::class);
Route::resource('categories', App\Http\Controllers\Inventory\CategoryController::class);
Route::resource('products', App\Http\Controllers\Inventory\ProductController::class);

// Customer Routes (accessible without auth for now)
Route::resource('customers', App\Http\Controllers\Customer\CustomerController::class);

// Auth Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LogoutController::class, 'logout'])->name('logout');

// Protected Routes (commented out for now - uncomment when auth is ready)
// Route::middleware('auth')->group(function () {
//     Route::get('password-change', [PasswordChangeController::class, 'passwordChange'])->name('passwordChange');
//     Route::put('password/update', [PasswordChangeController::class, 'updatePassword'])->name('password.update');
//     Route::get('profile', [ProfileController::class, 'index'])->name('profile');
//     Route::put('profile/update', [ProfileController::class, 'update'])->name('profile.update');
//     Route::resource('customers', CustomerController::class);
// });
