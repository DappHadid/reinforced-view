<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RekomendasiController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\EvaluasiController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ChangePinController;

// ── Autentikasi (publik) ─────────────────────────────────────────────────────
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ── Halaman Terproteksi ──────────────────────────────────────────────────────
Route::middleware('auth.reinforced')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/rekomendasi', [RekomendasiController::class, 'index'])->name('rekomendasi');
    Route::post('/rekomendasi/penilaian', [RekomendasiController::class, 'submitPenilaian'])->name('rekomendasi.penilaian');

    Route::get('/dosen', [DosenController::class, 'index'])->name('dosen.index');
    Route::get('/dosen/{sintaId}', [DosenController::class, 'show'])->name('dosen.show');
    Route::get('/dosen/{sintaId}/cetak-laporan', [DosenController::class, 'cetakLaporan'])->name('dosen.export_pdf');

    Route::get('/evaluasi', [EvaluasiController::class, 'index'])->name('evaluasi');
    Route::get('/evaluasi/target-detail', [EvaluasiController::class, 'getTargetDetail'])->name('evaluasi.target_detail');

    Route::post('/profil/ganti-pin', [ChangePinController::class, 'update'])->name('profil.ganti_pin');

});
