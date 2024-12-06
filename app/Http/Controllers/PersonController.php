<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function index()
    {
        $people = Person::query()
            ->approved()
            ->paginate(20, ['people.id', 'name', 'description', 'slug']);

        return view('people.index', ['people' => $people]);
    }

    public function show(Request $request, Person $person)
    {

        abort_if(! $person->approved_at, 404);

        $person->load([
            'resources:id,resourceable_id,url',
            'companies' => function ($query): void {
                $query
                    //->with('logo:id,imageable_id,path')
                    ->select('id', 'name', 'description', 'slug');
            },
        ]);

        return view('people.show', ['person' => $person]);
    }

    public function loadMore(Request $request)
    {
        $people = Person::query()->approved()->paginate(20, ['people.id', 'name', 'description', 'slug'], 'page', $request->page);

        return response()->json(['people' => $people]);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $people = Person::query()
            ->approved()
            ->where('name', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%")
            ->paginate(40, ['people.id', 'name', 'description', 'slug']);

        return response()->json(['people' => $people]);
    }
}
