<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlternativeStoreRequest;
use App\Jobs\AddCompany;
use App\Models\Alternative;
use App\Models\User;
use App\Notification\ReviewCompany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlternativeController extends Controller
{
    public function create(Request $request): View
    {
        return view('alternative.create');
    }

    public function store(AlternativeStoreRequest $request): RedirectResponse
    {
        $alternative = Alternative::create($request->validated());

        AddCompany::dispatch($alternative);

        $request->session()->flash('alternative.name', $alternative->name);

        User::first()->notify(new ReviewCompany($alternative));

        return redirect()->route('company.show', [$alternative->companies]);
    }
}
