<?php

namespace App\Http\Controllers;

use App\Actions\FormatResourcesAction;
use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function index()
    {
        $people = Person::query()
            ->with([
                'media',
                'tagsRelation' => function ($query): void {
                    $query->select('tags.id', 'tags.name');
                },
            ])
            ->approved()
            ->paginate(20, ['people.id', 'name', 'description', 'slug', 'image_path']);

        return view('people.index', ['people' => $people]);
    }

    public function show(Request $request, Person $person, FormatResourcesAction $formatResourcesAction)
    {
        abort_if(! $person->approved_at, 404);

        $person->load([
            'resources:id,resourceable_id,url',
            'companies' => function ($query): void {
                $query
                    ->select('id', 'name', 'description', 'slug', 'url');
            },
        ]);

        $resources = $formatResourcesAction->execute($person->resources);

        return view('people.show', ['person' => $person, 'resources' => $resources]);
    }
}
