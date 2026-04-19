<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Product;

// --- Import Komponen Livewire ---
use App\Livewire\Login;
use App\Livewire\Dashboard;
use App\Livewire\PosTransaction;
use App\Livewire\TransactionHistory;
use App\Livewire\Settings;
use App\Livewire\Products;
use App\Livewire\ForgotPassword;
use App\Livewire\ResetPassword;
use App\Livewire\DebtManagement;
use App\Livewire\Reports;
use App\Livewire\StockOpname;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Root Redirect
Route::get('/', fn() => auth()->check() ? redirect()->route('dashboard') : redirect()->route('login'));

// 2. Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
});

// 3. Logout
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// 4. Authenticated Routes (Admin & Kasir)
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/transaksi', PosTransaction::class)->name('pos');
    Route::get('/riwayat', TransactionHistory::class)->name('history');
    Route::get('/hutang', DebtManagement::class)->name('debt.index');

    // --- RUTE CETAK BARCODE (Diletakkan di sini agar bisa diakses Admin/Kasir jika perlu) ---
    // Akses via: localhost:8000/admin/products/print-barcode/1
    Route::get('/admin/products/print-barcode/{id}', function ($id, Request $request) {
        $product = \App\Models\Product::find($id);

        if (!$product) {
            return "Gagal: Produk ID ($id) tidak ada di database.";
        }

        $qty = $request->query('qty', 1);
        return view('print-barcode', compact('product', 'qty'));
    })->name('print.barcode');

    // 5. Exclusive Admin Routes (Hanya Admin)
    Route::middleware(['is_admin'])->prefix('admin')->group(function () {
        Route::get('/produk', Products::class)->name('products');
        Route::get('/pengaturan', Settings::class)->name('settings');
        Route::get('/laporan', Reports::class)->name('reports');
        Route::get('/stok-opname', StockOpname::class)->name('stock.opname');
    });
});
