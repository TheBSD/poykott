<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlternativeStoreRequest;
use App\Jobs\AddAlternative;
use App\Models\Alternative;
use App\Models\User;
use App\Notification\ReviewAlternative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlternativeController extends Controller
{
    public function create(Request $request): View
    {
        return view('alternatives.create');
    }

    public function store(AlternativeStoreRequest $request): RedirectResponse
    {
        $alternative = Alternative::create($request->validated());

        AddAlternative::dispatch($alternative);

        $request->session()->flash('alternative.name', $alternative->name);

        User::isAdmin()->first()->notify(new ReviewAlternative($alternative));

        return redirect()->route('home');
    }
}
