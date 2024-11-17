<?php

use App\Http\Controllers\AlternativeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\ResourceController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::resource('companies', CompanyController::class)->except('edit', 'update', 'destroy');

Route::resource('resources', ResourceController::class)->only('index', 'create', 'store');

Route::resource('alternatives', AlternativeController::class)->only('create', 'store');

Route::resource('investors', InvestorController::class)->only('index', 'store');
