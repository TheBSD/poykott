<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\PersonController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('load-more', [HomeController::class, 'loadMore']);
Route::get('search', [HomeController::class, 'search']);
Route::get('about', [HomeController::class, 'about'])->name('about');
Route::post('contact', [HomeController::class, 'contact'])->name('contact');

Route::get('companies/{company:slug}', [CompanyController::class, 'show'])->name('companies.show');
Route::post('companies/{company:slug}/alternatives', [CompanyController::class, 'storeAlternative'])->name('companies.alternatives.store');

Route::get('people', [PersonController::class, 'index'])->name('people');
Route::get('people/load-more', [PersonController::class, 'loadMore']);
Route::get('people/search', [PersonController::class, 'search']);
Route::get('people/{person:slug}', [PersonController::class, 'show'])->name('people.show');

Route::get('investors', [InvestorController::class, 'index'])->name('investors');
Route::get('investors/load-more', [InvestorController::class, 'loadMore']);
Route::get('investors/search', [InvestorController::class, 'search']);
Route::get('investors/{investor:slug}', [InvestorController::class, 'show'])->name('investors.show');
