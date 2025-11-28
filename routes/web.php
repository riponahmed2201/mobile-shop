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

// Customer Feedback Routes
Route::prefix('feedback')->name('feedback.')->group(function () {
    Route::get('/', [App\Http\Controllers\Customer\CustomerFeedbackController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Customer\CustomerFeedbackController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Customer\CustomerFeedbackController::class, 'store'])->name('store');
    Route::get('/{feedback}', [App\Http\Controllers\Customer\CustomerFeedbackController::class, 'show'])->name('show');
    Route::post('/{feedback}/respond', [App\Http\Controllers\Customer\CustomerFeedbackController::class, 'respond'])->name('respond');
    Route::delete('/{feedback}', [App\Http\Controllers\Customer\CustomerFeedbackController::class, 'destroy'])->name('destroy');
});

// Loyalty Program Routes
Route::prefix('loyalty')->name('loyalty.')->group(function () {
    Route::get('/', [App\Http\Controllers\Customer\LoyaltyTransactionController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Customer\LoyaltyTransactionController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Customer\LoyaltyTransactionController::class, 'store'])->name('store');
    Route::delete('/{loyaltyTransaction}', [App\Http\Controllers\Customer\LoyaltyTransactionController::class, 'destroy'])->name('destroy');
});

// Customer Groups Routes
Route::prefix('customer-groups')->name('customer-groups.')->group(function () {
    Route::get('/', [App\Http\Controllers\Customer\CustomerGroupController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Customer\CustomerGroupController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Customer\CustomerGroupController::class, 'store'])->name('store');
    Route::get('/{customerGroup}/edit', [App\Http\Controllers\Customer\CustomerGroupController::class, 'edit'])->name('edit');
    Route::put('/{customerGroup}', [App\Http\Controllers\Customer\CustomerGroupController::class, 'update'])->name('update');
    Route::get('/{customerGroup}/members', [App\Http\Controllers\Customer\CustomerGroupController::class, 'members'])->name('members');
    Route::put('/{customerGroup}/members', [App\Http\Controllers\Customer\CustomerGroupController::class, 'updateMembers'])->name('update-members');
    Route::delete('/{customerGroup}', [App\Http\Controllers\Customer\CustomerGroupController::class, 'destroy'])->name('destroy');
});

// Sales & Orders Routes
Route::resource('sales', App\Http\Controllers\Sales\SaleController::class);

Route::prefix('quotations')->name('quotations.')->group(function () {
    Route::get('/', [App\Http\Controllers\Sales\QuotationController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Sales\QuotationController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Sales\QuotationController::class, 'store'])->name('store');
    Route::get('/{quotation}', [App\Http\Controllers\Sales\QuotationController::class, 'show'])->name('show');
    Route::get('/{quotation}/edit', [App\Http\Controllers\Sales\QuotationController::class, 'edit'])->name('edit');
    Route::put('/{quotation}', [App\Http\Controllers\Sales\QuotationController::class, 'update'])->name('update');
    Route::post('/{quotation}/convert', [App\Http\Controllers\Sales\QuotationController::class, 'convertToSale'])->name('convert');
    Route::delete('/{quotation}', [App\Http\Controllers\Sales\QuotationController::class, 'destroy'])->name('destroy');
});

Route::prefix('returns')->name('returns.')->group(function () {
    Route::get('/', [App\Http\Controllers\Sales\ReturnController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Sales\ReturnController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Sales\ReturnController::class, 'store'])->name('store');
    Route::get('/{return}', [App\Http\Controllers\Sales\ReturnController::class, 'show'])->name('show');
    Route::post('/{return}/approve', [App\Http\Controllers\Sales\ReturnController::class, 'approve'])->name('approve');
    Route::post('/{return}/reject', [App\Http\Controllers\Sales\ReturnController::class, 'reject'])->name('reject');
    Route::post('/{return}/process', [App\Http\Controllers\Sales\ReturnController::class, 'process'])->name('process');
});

Route::prefix('emi')->name('emi.')->group(function () {
    Route::get('/', [App\Http\Controllers\Sales\EmiController::class, 'index'])->name('index');
    Route::get('/{emiPlan}', [App\Http\Controllers\Sales\EmiController::class, 'show'])->name('show');
    Route::post('/{emiPlan}/payment', [App\Http\Controllers\Sales\EmiController::class, 'recordPayment'])->name('record-payment');
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
