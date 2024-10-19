<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvestorStoreRequest;
use App\Jobs\AddInvestor;
use App\Models\Investor;
use App\Models\User;
use App\Notification\ReviewInvestor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvestorController extends Controller
{
    public function index(Request $request): View
    {
        $investors = Investor::all();

        return view('investors.index', compact('investors'));
    }

    public function store(InvestorStoreRequest $request): RedirectResponse
    {
        $investor = Investor::create($request->validated());

        AddInvestor::dispatch($investor);

        $request->session()->flash('investor.name', $investor->name);

        User::isAdmin()->first()->notify(new ReviewInvestor($investor));

        return redirect()->route('investors.index');
    }
}
