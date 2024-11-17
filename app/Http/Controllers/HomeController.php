<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $companies = Company::with('tagsRelation:id,name')->paginate(20, ['id', 'name', 'description']);
        return view('home', compact('companies'));
    }

    public function loadMore(Request $request)
    {
        $companies = Company::with('tagsRelation:id,name')->paginate(20, ['id', 'name', 'description'], 'page', $request->page);
        return response()->json(['companies' => $companies]);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $companies = Company::with('tagsRelation:id,name')
            ->where('name', 'like', "%{$search}%")
            ->paginate(40, ['id', 'name', 'description']);
        return response()->json(['companies' => $companies]);
    }
}
