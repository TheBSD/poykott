<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function index()
    {
        $people = Person::paginate(20, ['people.id', 'name', 'description', 'avatar']);

        return view('people.index', compact('people'));
    }

    public function show(Request $request, Person $person)
    {
        $person->load([
            'resources:id,resourceable_id,url',
            'companies' => function ($query) {
                $query->with('logo:id,imageable_id,path')->select('id', 'name', 'description');
            },
        ]);

        return view('people.show', compact('person'));
    }

    public function loadMore(Request $request)
    {
        $people = Person::paginate(20, ['people.id', 'name', 'description', 'avatar'], 'page', $request->page);

        return response()->json(['people' => $people]);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $people = Person::where('name', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%")
            ->paginate(40, ['people.id', 'name', 'description', 'avatar']);

        return response()->json(['people' => $people]);
    }
}
