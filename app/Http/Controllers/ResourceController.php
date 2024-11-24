<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResourceStoreRequest;
use App\Jobs\AddResource;
use App\Models\Resource;
use App\Models\User;
use App\Notification\ReviewResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResourceController extends Controller
{
    public function index(Request $request): View
    {
        $resources = Resource::all();

        return view('resources.index', ['resources' => $resources]);
    }

    public function create(Request $request): View
    {
        return view('resources.create');
    }

    public function store(ResourceStoreRequest $request): RedirectResponse
    {
        $resource = Resource::create($request->validated());

        AddResource::dispatch($resource);

        $request->session()->flash('resource.title', $resource->title);

        User::isAdmin()->first()->notify(new ReviewResource($resource));

        return redirect()->route('resources.index');
    }
}
