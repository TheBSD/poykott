<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyStoreRequest;
use App\Jobs\AddCompany;
use App\Models\Company;
use App\Models\User;
use App\Notification\ReviewCompany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(Request $request): View
    {
        $companies = Company::all();

        return view('companies.index', compact('companies'));
    }

    public function show(Request $request, Company $company): View
    {
        $company->load('alternatives');

        return view('companies.show', compact('company'));
    }

    public function create(Request $request): View
    {
        return view('companies.create');
    }

    public function store(CompanyStoreRequest $request): RedirectResponse
    {

        $company = Company::create($request->validated());

        AddCompany::dispatch($company);

        $request->session()->flash('company.name', $company->name);

        User::isAdmin()->first()->notify(new ReviewCompany($company));

        return redirect()->route('companies.index');
    }
}
