<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Customer\CustomerFeedbackController;
use App\Http\Controllers\Customer\CustomerGroupController;
use App\Http\Controllers\Customer\LoyaltyTransactionController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Inventory\BrandController;
use App\Http\Controllers\Inventory\CategoryController;
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Sales\EmiController;
use App\Http\Controllers\Sales\QuotationController;
use App\Http\Controllers\Sales\ReturnController;
use App\Http\Controllers\Sales\SaleController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LandingPageController;

// Public Routes
Route::get('/', [LandingPageController::class, 'index'])->name('landing');

Route::middleware('user')->group(function () {

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Inventory Routes (accessible without auth for now)
Route::resource('brands', BrandController::class);
Route::resource('categories', CategoryController::class);
Route::resource('products', ProductController::class);

// Customer Routes (accessible without auth for now)
Route::resource('customers', CustomerController::class);

// Customer Feedback Routes
Route::prefix('feedback')->name('feedback.')->group(function () {
    Route::get('/', [CustomerFeedbackController::class, 'index'])->name('index');
    Route::get('/create', [CustomerFeedbackController::class, 'create'])->name('create');
    Route::post('/', [CustomerFeedbackController::class, 'store'])->name('store');
    Route::get('/{feedback}', [CustomerFeedbackController::class, 'show'])->name('show');
    Route::post('/{feedback}/respond', [CustomerFeedbackController::class, 'respond'])->name('respond');
    Route::delete('/{feedback}', [CustomerFeedbackController::class, 'destroy'])->name('destroy');
});

// Loyalty Program Routes
Route::prefix('loyalty')->name('loyalty.')->group(function () {
    Route::get('/', [LoyaltyTransactionController::class, 'index'])->name('index');
    Route::get('/create', [LoyaltyTransactionController::class, 'create'])->name('create');
    Route::post('/', [LoyaltyTransactionController::class, 'store'])->name('store');
    Route::delete('/{loyaltyTransaction}', [LoyaltyTransactionController::class, 'destroy'])->name('destroy');
});

// Customer Groups Routes
Route::prefix('customer-groups')->name('customer-groups.')->group(function () {
    Route::get('/', [CustomerGroupController::class, 'index'])->name('index');
    Route::get('/create', [CustomerGroupController::class, 'create'])->name('create');
    Route::post('/', [CustomerGroupController::class, 'store'])->name('store');
    Route::get('/{customerGroup}/edit', [CustomerGroupController::class, 'edit'])->name('edit');
    Route::put('/{customerGroup}', [CustomerGroupController::class, 'update'])->name('update');
    Route::get('/{customerGroup}/members', [CustomerGroupController::class, 'members'])->name('members');
    Route::put('/{customerGroup}/members', [CustomerGroupController::class, 'updateMembers'])->name('update-members');
    Route::delete('/{customerGroup}', [CustomerGroupController::class, 'destroy'])->name('destroy');
});

// Sales & Orders Routes
    Route::get('sales/{sale}/items', [SaleController::class, 'getItems'])->name('sales.items');
    Route::resource('sales', SaleController::class);

Route::prefix('quotations')->name('quotations.')->group(function () {
    Route::get('/', [QuotationController::class, 'index'])->name('index');
    Route::get('/create', [QuotationController::class, 'create'])->name('create');
    Route::post('/', [QuotationController::class, 'store'])->name('store');
    Route::get('/{quotation}', [QuotationController::class, 'show'])->name('show');
    Route::get('/{quotation}/edit', [QuotationController::class, 'edit'])->name('edit');
    Route::put('/{quotation}', [QuotationController::class, 'update'])->name('update');
    Route::post('/{quotation}/convert', [QuotationController::class, 'convertToSale'])->name('convert');
    Route::delete('/{quotation}', [QuotationController::class, 'destroy'])->name('destroy');
});

Route::prefix('returns')->name('returns.')->group(function () {
    Route::get('/', [ReturnController::class, 'index'])->name('index');
    Route::get('/create', [ReturnController::class, 'create'])->name('create');
    Route::post('/', [ReturnController::class, 'store'])->name('store');
    Route::get('/{return}', [ReturnController::class, 'show'])->name('show');
    Route::post('/{return}/approve', [ReturnController::class, 'approve'])->name('approve');
    Route::post('/{return}/reject', [ReturnController::class, 'reject'])->name('reject');
    Route::post('/{return}/process', [ReturnController::class, 'process'])->name('process');
});

Route::prefix('emi')->name('emi.')->group(function () {
    Route::get('/', [EmiController::class, 'index'])->name('index');
    Route::get('/{emiPlan}', [EmiController::class, 'show'])->name('show');
    Route::post('/{emiPlan}/payment', [EmiController::class, 'recordPayment'])->name('record-payment');
});

});

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
