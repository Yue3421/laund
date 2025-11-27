// routes/web.php

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanController;

// Default Breeze routes are assumed to be present for auth (login, logout, register)

// Protected routes for dashboard and features
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $role = Auth::user()->role;
        if ($role === 'admin') {
            return view('admin.dashboard');
        } elseif ($role === 'kasir') {
            return view('kasir.dashboard');
        } elseif ($role === 'owner') {
            return view('owner.dashboard');
        }
        abort(403);
    })->name('dashboard');

    // CRUD Outlet (only admin)
    Route::resource('outlets', OutletController::class);

    // Registrasi Pelanggan / CRUD Member (admin and kasir)
    Route::resource('members', MemberController::class);

    // CRUD Pengguna (only admin)
    Route::resource('users', UserController::class);

    // CRUD Paket (only admin)
    Route::resource('pakets', PaketController::class);

    // Entri Transaksi / CRUD Transaksi (admin and kasir)
    Route::resource('transaksis', TransaksiController::class);

    // Generate Laporan (admin, kasir, owner)
    Route::get('/laporans', [LaporanController::class, 'index'])->name('laporans.index');
});