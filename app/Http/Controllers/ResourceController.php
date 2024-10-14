<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResourceStoreRequest;
use App\Jobs\AddCompany;
use App\Models\Resource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResourceController extends Controller
{
    public function index(Request $request): View
    {
        $resources = Resource::all();

        return view('resource.index', compact('resources'));
    }

    public function create(Request $request): View
    {
        return view('resource.create');
    }

    public function store(ResourceStoreRequest $request): RedirectResponse
    {
        $resource = Resource::create($request->validated());

        AddCompany::dispatch($resource);

        $request->session()->flash('resource.name', $resource->name);

//        $user->first->notify(new ReviewResource($resource));

        return redirect()->route('resource.index');
    }
}
