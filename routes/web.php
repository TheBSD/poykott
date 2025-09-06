<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\MailchimpRedirectionController;
use App\Http\Controllers\PersonController;
use Illuminate\Support\Facades\Route;

// Alternatives
Route::get('/', [HomeController::class, 'index'])->name('alternatives.index');
Route::permanentRedirect('alternatives', '/');

Route::get('alternative/{alternative:slug}', [HomeController::class, 'show'])->name('alternatives.show');

// Companies
Route::get('companies', [CompanyController::class, 'index'])->name('companies.index');
Route::get('companies/create', [CompanyController::class, 'create'])->name('companies.create');
Route::post('companies/store', [CompanyController::class, 'store'])->name('companies.store');

Route::get('companies/{company:slug}', [CompanyController::class, 'show'])->name('companies.show');
Route::get('company/{company:slug}', [CompanyController::class, 'redirect'])->name('company.redirect');

Route::get('companies/url/{companyUrl}', [CompanyController::class, 'redirectToSlug'])->name('companies.show.url');
Route::post(
    'companies/{company:slug}/alternatives', [CompanyController::class, 'storeAlternative']
)->name('companies.alternatives.store');

// People
Route::get('people/{person:slug}', [PersonController::class, 'show'])->name('people.show');

// Investors
Route::get('investors/{investor:slug}', [InvestorController::class, 'show'])->name('investors.show');

// Contact
Route::post('contact', [HomeController::class, 'contactPost'])->name('contact.store');
Route::get('contact', [HomeController::class, 'contactGet'])->name('contact.get');

// Pages
Route::get('about', [HomeController::class, 'about'])->name('about');
Route::get('faqs', FaqController::class)->name('faqs');
Route::get('newsletter', [HomeController::class, 'newsletter'])->name('newsletter.get');
Route::get('similar-sites', [HomeController::class, 'similarSites'])->name('similar-sites');

// Webhooks
Route::get('webhooks/mailchimp', MailchimpRedirectionController::class)->name('mailchimp.webhook');
