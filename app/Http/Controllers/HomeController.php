<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $companies = Company::with([
            'media' => function ($query) {
                $query->select('id', 'model_id', 'model_type', 'disk', 'file_name', 'generated_conversions', 'collection_name');
            },
            'tagsRelation' => function ($query): void {
                $query->select('tags.id', 'name')->limit(3);
            },
        ])
          ->approved()
          ->paginate(20, ['companies.id', 'name', 'description', 'slug']);

        return view('home', ['companies' => $companies]);
    }

    public function loadMore(Request $request)
    {
        $companies = Company::with([
            'media' => function ($query) {
                $query->select('id', 'model_id', 'model_type', 'disk', 'file_name', 'generated_conversions');
            },
            'tagsRelation' => function ($query): void {
                $query->select('tags.id', 'name')->limit(3);
            },
        ])
        ->approved()
        ->paginate(20, ['companies.id', 'name', 'description', 'slug'], 'page', $request->page);

        return response()->json(['companies' => $companies]);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $companies = Company::with([
            'media' => function ($query) {
                $query->select('id', 'model_id', 'model_type', 'disk', 'file_name', 'generated_conversions');
            },
            'tagsRelation' => function ($query): void {
                $query->select('tags.id', 'name')->limit(3);
            },
        ])
        ->approved()
        ->where('name', 'like', "%{$search}%")
        ->orWhere('description', 'like', "%{$search}%")
        ->paginate(40, ['companies.id', 'name', 'description', 'slug']);

        return response()->json(['companies' => $companies]);
    }

    public function about()
    {
        return view('pages.about');
    }

    public function contact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        ContactMessage::query()->create($validated);

        return back()->with('success', 'Message sent successfully');
    }
}
