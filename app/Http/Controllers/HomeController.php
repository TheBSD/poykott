<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $companies = Company::with('tagsRelation')->paginate(20, ['id', 'name', 'description']);
        return view('home', compact('companies'));
    }

    public function loadMoreCompanies(Request $request)
    {
        $companies = Company::with('tagsRelation')->paginate(20, ['id', 'name', 'description'], 'page', $request->page);
        return response()->json(['companies' => $companies]);
    }
}
