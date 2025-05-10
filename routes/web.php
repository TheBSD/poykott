<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\PersonController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => to_route('alternatives.index'))->name('home');
Route::get('alternatives', [HomeController::class, 'index'])->name('alternatives.index');
Route::get('alternative/{alternative:slug}', [HomeController::class, 'show'])->name('alternatives.show');
Route::get('companies', [CompanyController::class, 'index'])->name('companies.index');
Route::get('about', [HomeController::class, 'about'])->name('about');
Route::view('faqs', 'pages.faqs')->name('faqs');
Route::post('contact', [HomeController::class, 'contact'])->name('contact.store');
Route::view('contact', 'pages.contact')->name('contact.get');
Route::get('similar-sites', [HomeController::class, 'similarSites'])->name('similar-sites');
Route::get('companies/create', [CompanyController::class, 'create'])->name('companies.create');
Route::post('companies/store', [CompanyController::class, 'store'])->name('companies.store');

Route::get('companies/{company:slug}', [CompanyController::class, 'show'])->name('companies.show');
Route::get('companies/url/{companyUrl}', [CompanyController::class, 'redirectToSlug'])->name('companies.show.url');

Route::post('companies/{company:slug}/alternatives',
    [CompanyController::class, 'storeAlternative'])->name('companies.alternatives.store');

// Route::get('people', [PersonController::class, 'index'])->name('people');
Route::get('people/{person:slug}', [PersonController::class, 'show'])->name('people.show');

// Route::get('investors', [InvestorController::class, 'index'])->name('investors');
Route::get('investors/{investor:slug}', [InvestorController::class, 'show'])->name('investors.show');
