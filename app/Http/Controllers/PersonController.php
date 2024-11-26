<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function index()
    {
        $people = Person::query()
        ->with([
            'media' => function ($query) {
                $query->select('id', 'model_id', 'model_type', 'disk', 'file_name', 'generated_conversions','collection_name');
            }])
            ->paginate(20, ['people.id', 'name', 'description', 'avatar', 'slug']);

        return view('people.index', ['people' => $people]);
    }

    public function show(Request $request, Person $person)
    {
        $person->load([
            'resources:id,resourceable_id,url',
            'companies' => function ($query): void {
                $query->with([
                    'media' => function ($query) {
                        $query->select('id', 'model_id', 'model_type', 'disk', 'file_name', 'generated_conversions','collection_name');
                    }])
                    ->select('id', 'name', 'description', 'slug');
            },
        ]);

        return view('people.show', ['person' => $person]);
    }

    public function loadMore(Request $request)
    {
        $people = Person::query()
        ->with([
            'media' => function ($query) {
                $query->select('id', 'model_id', 'model_type', 'disk', 'file_name', 'generated_conversions');
            }])
            ->paginate(20, ['people.id', 'name', 'description', 'avatar', 'slug'], 'page', $request->page);

        return response()->json(['people' => $people]);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $people = Person::query()
        ->with([
            'media' => function ($query) {
                $query->select('id', 'model_id', 'model_type', 'disk', 'file_name', 'generated_conversions');
            }])
            ->where('name', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%")
            ->paginate(40, ['people.id', 'name', 'description', 'avatar', 'slug']);

        return response()->json(['people' => $people]);
    }
}
