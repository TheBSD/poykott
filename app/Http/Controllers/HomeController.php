<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $companies = Company::with([
            'logo:id,imageable_id,path',
            'tagsRelation' => function ($query) {
                $query->select('tags.id', 'name')->limit(3);
            },
        ])->paginate(20, ['companies.id', 'name', 'description']);
        return view('home', compact('companies'));
    }

    public function loadMore(Request $request)
    {
        $companies = Company::with([
            'logo:id,imageable_id,path',
            'tagsRelation' => function ($query) {
                $query->select('tags.id', 'name')->limit(3);
            },
        ])->paginate(20, ['companies.id', 'name', 'description'], 'page', $request->page);
        return response()->json(['companies' => $companies]);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $companies = Company::with([
            'logo:id,imageable_id,path',
            'tagsRelation' => function ($query) {
                $query->select('tags.id', 'name')->limit(3);
            },
        ])
            ->where('name', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%")
            ->paginate(40, ['companies.id', 'name', 'description']);
        return response()->json(['companies' => $companies]);
    }
}
