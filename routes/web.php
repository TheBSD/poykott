<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::resource('companies', App\Http\Controllers\CompanyController::class)->except('edit', 'update', 'destroy');

Route::resource('resources', App\Http\Controllers\ResourceController::class)->only('index', 'create', 'store');

Route::resource('alternatives', App\Http\Controllers\AlternativeController::class)->only('create', 'store');

Route::resource('investors', App\Http\Controllers\InvestorController::class)->only('index', 'store');
