<?php

namespace App\Http\Controllers;

use App\Models\Company;

class HomeController extends Controller
{
    public function index()
    {
        $companies = Company::with('tagsRelation')->get(['id', 'name', 'description']);
        return view('home', compact('companies'));
    }
}
