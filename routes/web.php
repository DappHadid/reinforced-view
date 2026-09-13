<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RekomendasiController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\EvaluasiController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/rekomendasi', [RekomendasiController::class, 'index'])->name('rekomendasi');
Route::post('/rekomendasi/penilaian', [RekomendasiController::class, 'submitPenilaian'])->name('rekomendasi.penilaian');

Route::get('/dosen', [DosenController::class, 'index'])->name('dosen.index');
Route::get('/dosen/{sintaId}', [DosenController::class, 'show'])->name('dosen.show');

Route::get('/evaluasi', [EvaluasiController::class, 'index'])->name('evaluasi');


