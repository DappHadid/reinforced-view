<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/rekomendasi', [RecommendationController::class, 'index']);
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');