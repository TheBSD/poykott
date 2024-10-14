<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::resource('companies', App\Http\Controllers\CompanyController::class)->except('edit', 'update', 'destroy');

Route::resource('resources', App\Http\Controllers\ResourceController::class)->only('index', 'create', 'store');

Route::resource('alternatives', App\Http\Controllers\AlternativeController::class)->only('create', 'store');
