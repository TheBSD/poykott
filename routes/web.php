<?php

use App\Http\Controllers\AlternativeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ResourceController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('load-more', [HomeController::class, 'loadMore']);
Route::get('search', [HomeController::class, 'search']);

Route::get('people', [PersonController::class, 'index'])->name('people');
Route::get('investors', [InvestorController::class, 'index'])->name('investors');
